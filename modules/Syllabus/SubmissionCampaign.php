<?php

/**
 * SubmissionCampaign object.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_SubmissionCampaign extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_submission_campaigns',
            '__pk' => ['id'],
            
            'id' => 'int',   
            'description' => 'string',
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'dueDate' => ['datetime', 'nativeName' => 'due_date'],
            'required' => 'bool',
            'log' => 'string',
            // 'organizationAuthorizationId' => ['string', 'nativeName' => 'organization_authorization_id'],

            'semester' => ['1:1', 'to' => 'Syllabus_Admin_Semester', 'keyMap' => ['semester_id' => 'id']],
            // 'organization' => ['1:1', 'keyMap' => ['organization_authorization_id' => 'id']],
            'submissions' => ['1:N', 
                'to' => 'Syllabus_Syllabus_Submission', 
                'reverseOf' => 'campaign', 
                'orderBy' => ['campaign_id', 'submittedDate', 'status']
            ],
        ];
    }

    public function getSubmissions ()
    {
        $submissions = [];
        foreach ($this->_fetch('submissions') as $submission)
        {
            if (!$submission->deleted)
            {
                $courseKey = $submission->courseSection->classNumber.'.'.$submission->courseSection->sectionNumber;
                $submissions[$courseKey] = $submission;
            }
        }
        ksort($submissions);

        return $submissions;
    }

    public function getStatistics ()
    {
        $statistics = ['open' => 0, 'pending' => 0, 'approved' => 0, 'denied' => 0, 'total' => 0];
        foreach ($this->submissions as $submission)
        {
            $statistics[$submission->status]++;
            $statistics['total']++;
        }

        return $statistics;
    }

    public function getOrganization ()
    {
        $organization = null;
        if ($organizationAuthorizationId = $this->_fetch('organization_authorization_id'))
        {
            list($type, $id) = explode('/', $organizationAuthorizationId);
            switch (lcfirst($type))
            {
                case 'departments':
                    $organization = $this->getSchema('Syllabus_AcademicOrganizations_Department')->get($id);
                    break;
                case 'colleges':
                    $organization = $this->getSchema('Syllabus_AcademicOrganizations_College')->get($id);
                    break;
            }
        }

        return $organization;
    }
}
