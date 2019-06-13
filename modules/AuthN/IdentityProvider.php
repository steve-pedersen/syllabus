<?php

/**
 * 	TODO: Update this to check for account on shibboleth auth, then check ClassData table if exists
 */
class Syllabus_AuthN_IdentityProvider extends At_Shibboleth_IdentityProvider
{
    private $allowedAffiliationList;
    
    protected function getDefaultAttributeHeaders ()
    {
        return array(
            'username' => 'UID',
            'organization' => 'calstateEduPersonOrg',
            'emailAddress' => 'mail',
            'displayName' => 'displayName',
            'firstName' => 'givenName',
            'lastName' => 'surname',
            'affiliation' => 'calstateEduPersonAffiliation',
        );
    }
    
    protected function getDefaultAllowedAffiliations ()
    {
        return array('Employee Faculty', 'Employee Staff');
    }
    
    protected function configureProvider ($attributeMap)
    {
        parent::configureProvider($attributeMap);
        
        $this->allowedAffiliationsList = array_map(
            array($this, 'normalizeAffiliation'),
            (!empty($attributeMap['allowedAffiliations'])
                ? $attributeMap['allowedAffiliations']
                : $this->getDefaultAllowedAffiliations()
            )
        );
    }
    
    protected function initializeIdentityProperties (Bss_Core_IRequest $request, Bss_AuthN_Identity $identity)
    {
        parent::initializeIdentityProperties($request, $identity);
        
        $identity->setProperty('allowCreateAccount', $this->getAllowCreateAccount($identity));
    }
    

    /**
     * 	TODO: Update this to check for account on shibboleth auth, then check ClassData table if exists
     *  Allow faculty, staff, and students who teach courses to create accounts.
     */
    protected function getAllowCreateAccount (Bss_AuthN_Identity $identity)
    {
        if (($affiliationList = $identity->getProperty('affiliation')))
        {
            if (is_string($affiliationList))
            {
                $affiliationList = explode(';', $affiliationList);
            }
            
            $affiliationList = array_map(array($this, 'normalizeAffiliation'), $affiliationList);
            
            foreach ($affiliationList as $affiliation)
            {
                if (in_array($affiliation, $this->allowedAffiliationsList))
                {
                    return true;
                }
            }
        }

        $users = $this->schema('Syllabus_ClassData_User');
        $teacherSym = new Bss_ActiveRecord_RawSymbol('enrollments', 'user_id', 'string');
        $cond = $teacherSym->equals($identity->getUsername());
        $roleSym = new Bss_ActiveRecord_RawSymbol('enrollments', 'role', 'string');
        $cond = $cond->andIf($roleSym->equals('instructor'));

        $instructors = $users->find(
            $cond,
            array(
                'arrayKey' => 'id',
                'orderBy' => array('+lastName', '+firstName'),
                'extraJoins' => array(
                    'enrollments' => array(
                        'to' => 'syllabus_classdata_enrollments',
                        'on' => array('id' => 'user_id'),
                        'type' => Bss_DataSource_SelectQuery::INNER_JOIN,
                    ),
                )
            )
        );
        
        return (count($instructors) > 0);
    }
    
    protected function normalizeAffiliation ($affiliation)
    {
        return strtolower(trim($affiliation));
    }

    protected function schema ($recordClass)
    {
        return $this->getApplication()->schemaManager->getSchema($recordClass);
    }
}
