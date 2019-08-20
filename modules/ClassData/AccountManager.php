<?php

/**
 * Manager to integrate ClassData with Syllabus Account data.
 *
 * @author Steve Pedersen (pedersen@sfsu.edu)
 */
class Syllabus_ClassData_AccountManager
{
    private $application;

    public function __construct($app)
    {
        $this->application = $app;
    }

    public function hasEnrollment ($identity)
    {
        $schema =  $this->getSchema('Syllabus_ClassData_Enrollment');
        return $schema->findOne($schema->userId->equals($identity->getProperty('username')));
    }

    public function hasClassData ($identity)
    {
        return $this->getSchema('Syllabus_ClassData_User')->get($identity->getProperty('username'));
    }

    public function createUserAccount ($identity)
    {
        $accounts = $this->getSchema('Bss_AuthN_Account');
        $account = $accounts->createInstance();
        
        if ($classDataUser = $this->getSchema('Syllabus_ClassData_User')->get($identity->getProperty('username')))
        {
            $account->username = $classDataUser->id;
            $account->emailAddress = $identity->getProperty('emailAddress') ?? $classDataUser->emailAddress ?? '';
            $account->firstName = $classDataUser->firstName;
            $account->lastName = $classDataUser->lastName;
            $account->createdDate = new DateTime;
            $account->save();

            $account = $this->grantRoleAndMembership($account, $classDataUser);
        }
        else
        {
            $account->username = $identity->getProperty('username');
            $account->firstName = $identity->getProperty('firstName');
            $account->lastName = $identity->getProperty('lastName');
            $account->emailAddress = $identity->getProperty('emailAddress');
            $account->createdDate = new DateTime;
            $account->save();
        }

        return $account;
    }

    /**
     * Gives any instructors the 'Faculty' role and give membership to their
     * enrolled course departments and colleges.
     * Gives students the 'Student' role.
     */
    public function grantRoleAndMembership ($account, $classDataUser)
    {
        $roles = $this->getSchema('Syllabus_AuthN_Role');
        $departments = $this->getSchema('Syllabus_AcademicOrganizations_Department');
        $colleges = $this->getSchema('Syllabus_AcademicOrganizations_College');
        $userDepartments = [];
        $userColleges = [];
        $isStudent = false;

        foreach ($classDataUser->enrollments as $enrollment)
        {
            if ($classDataUser->enrollments->getProperty($enrollment, 'role') === 'instructor')
            {
                if (empty($userDepartments))
                {
                    $facultyRole = $roles->findOne($roles->name->equals('Faculty'));
                    $account->roles->add($facultyRole);
                    $account->roles->save();
                }
                if (!isset($userDepartments[$enrollment->department->id]))
                {
                    $userDepartments[$enrollment->department->id] = $enrollment->department;
                }
                if (!isset($userColleges[$enrollment->department->parent->id]))
                {
                    $userColleges[$enrollment->department->parent->id] = $enrollment->department->parent;
                }
            }
            elseif ($classDataUser->enrollments->getProperty($enrollment, 'role') === 'student')
            {
                $isStudent = true;        
            }
        }

        if ($isStudent)
        {
            $studentRole = $roles->findOne($roles->name->equals('Student'));
            $account->roles->add($studentRole);
            $account->roles->save();   
        }

        // auto grant instructors membership into their course departments
        foreach ($userDepartments as $department)
        {
            $department->grantUsersRole($account, 'member');
            if (!$userColleges[$department->parent->id])
            {
                $userColleges[$department->parent->id] = $department->parent;
            }
        }
        // auto grant instructors membership into their department colleges
        foreach ($userColleges as $college)
        {
            $college->grantUsersRole($account, 'member');
        }

        return $account;
    }

    public function getSchema ($name)
    {
        return $this->application->schemaManager->getSchema($name);
    }
}