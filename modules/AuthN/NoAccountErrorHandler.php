<?php

/**
 * Handles the case when someone attempts to login with a nonexistent account.
 * There are three different cases of interest here:
 * 
 * 1. The user is authenticated by Shibboleth (or another remote identity
 *    provider) and has a Syllabus account, but their Syllabus account's username
 *    equals their e-mail address rather than the username returned by 
 *    Shibboleth. We fix the acocunt's username and log them in seamlessly.
 * 
 * 2. The user is authenticated but they do not have a Syllabus account with a
 *    username that matches either their Shibboleth username or e-mail
 *    address. It's still possible that the person has a Syllabus account under a
 *    different e-mail address, but we deal with these mismatches through 
 *    manual intervention. If the user is of the appropriate role, they should
 *    be allowed to create a new account. If the user is enrolled as an instructor
 *    via ClassData, give them Faculty Role and add them as members to any
 *    departments and colleges that they should be associated with.
 * 
 * 3. The user is unauthenticated and no account could be found. This generally
 *    occurs because the user entered the wrong username (e-mail address) for
 *    the internal password authentication scheme. We treat this the same as
 *    Bss_AuthN_ExLoginRequired.
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_AuthN_NoAccountErrorHandler extends Syllabus_Master_ErrorHandler
{
    public static function getErrorClassList () { return array('Bss_AuthN_ExNoAccount'); }
    
    private $pageTitle;
    
    protected function getStatusCode () { return 403; }
    protected function getStatusMessage () { return 'Forbidden'; }
    protected function getTemplateFile () { return 'error-login.html.tpl'; }
    
    protected function handleError ($error)
    {
        parent::handleError($error);
        
        $identity = $error->getExtraInfo();
        $provider = $identity->getIdentityProvider();
        
        // TODO: Remove this temporary hack to spoof authentication for dev
        // NOTE: choose dev idp, enter in whatever for username/pass, 
        // it will generate 400+ faculty accounts to be used. Don't repeat.
        if (get_class($provider) === 'Syllabus_AuthN_DevIdentityProvider')
        {
            foreach ($this->dummydata as $user)
            {
                $identity->setAuthenticated(true);
                $identity->setProperty('username', $user);
                $identity->setProperty('allowCreateAccount', true);
                if ($identity->getAuthenticated())
                {
                    if (($username = $identity->getProperty('username')))
                    {
                        $accounts = $this->schema('Bss_AuthN_Account');
                        $account = $accounts->findOne($accounts->username->lower()->equals(strtolower($username)));
                        
                        if ($account)
                        {
                            $account->username = $identity->getProperty('username');
                            // $this->getUserContext()->login($account);
                        }
                    }
                    if (($allowCreateAccount = $identity->getProperty('allowCreateAccount')))
                    {
                        $accountManager = new Syllabus_ClassData_AccountManager($this->getApplication());
                        $account = $accountManager->createUserAccount($identity);
                        
                        // $this->getUserContext()->login($account);
                        // $this->response->redirect('home');
                    }
                    else
                    {
                        $this->template->setPageTitle('Account creation disallowed');
                    }
                    
                    $this->template->identity = $identity;
                    $this->template->allowCreateAccount = $allowCreateAccount;
                }           
            }

        }
        elseif ($identity->getAuthenticated())
        // if ($identity->getAuthenticated())
        {
            // We know the user's identity for sure, but we couldn't find an
            // account for them. This only happens for Shibboleth, LDAP, and
            // similar remote authentication systems, where we can verify
            // the person's identity independent of knowing that they own a
            // particular Syllabus account.
            
            // This situation can occur if the person doesn't have a Syllabus
            // account at all, or if they have an older account where the
            // username is their e-mail address.
            
            if (($username = $identity->getProperty('username')))
            {
                $accounts = $this->schema('Bss_AuthN_Account');
                $account = $accounts->findOne($accounts->username->lower()->equals(strtolower($username)));
                
                if ($account)
                {
                    // The user has an account with username equal to their
                    // e-mail address. We trust that the identity provider
                    // requires the person to prove they own whatever e-mail
                    // address that is being reported to us. (In general,
                    // this is a dangerous assumption, but we can vet that
                    // with our identity providers.)
                    
                    // If we didn't trust them, we'd have to send an e-mail
                    // out to the user with a link to initiate the migration.
                    // Let's go with this for right now.
                    
                    // NOTE: login() is guaranteed to save the account.
                    
                    $account->username = $identity->getProperty('username');
                    $this->getUserContext()->login($account);
                }
            }
            if (($allowCreateAccount = $identity->getProperty('allowCreateAccount')))
            {
                $accountManager = new Syllabus_ClassData_AccountManager($this->getApplication());
                $account = $accountManager->createUserAccount($identity);
                
                $this->getUserContext()->login($account);
                // $this->response->redirect('home');
            }
            else
            {
                $this->template->setPageTitle('Account creation disallowed');
            }
            
            $this->template->identity = $identity;
            $this->template->allowCreateAccount = $allowCreateAccount;
        }
        else
        {
            // Treat it the same as if they had an account but failed to
            // authenticate, which is probably due to them entering the
            // wrong username for the internal password authentication
            // scheme.
            
            $this->forwardError('Bss_AuthN_ExLoginRequired', $error);
        }
    }
    
    private function getAffiliations ($identity)
    {
        if (($affiliationList = $identity->getProperty('affiliation', [])))
        {
            if (is_string($affiliationList))
            {
                $affiliationList = explode(';', $affiliationList);
            }
            
            $affiliationList = array_map(array($this, 'normalizeAffiliation'), $affiliationList);
        }
        
        return $affiliationList;
    }
    
    protected function normalizeAffiliation ($affiliation)
    {
        return strtolower(trim($affiliation));
    }
    
    private function guessUniversity ($identity, $provider)
    {
        $universities = $this->schema('Bss_Academia_University');
        $condList = [];
        
        // If we have an organization from Shibboleth, see if it matches the
        // university abbreviation.
        if (($org = $identity->getProperty('organization')))
        {
            $condList[] = $universities->abbreviation->lower()->equals(strtolower($org));
        }
        
        $providerName = strtolower(preg_replace('/^([^-]+)-shib$/', '\\1', $provider->getName()));
        $condList[] = $universities->abbreviation->lower()->equals($providerName);
        
        $providerTitle = strtolower($provider->getDisplayName());
        $condList[] = $universities->name->lower()->equals($providerTitle);
        
        return $universities->findOne($universities->anyTrue($condList));
    }

private $dummydata = [
    '907899023',
    '907597514',
    '913694033',
    '900012547',
    '902112255',
    '900001276',
    '900000483',
    '913725805',
    '907608499',
    '913766352',
    '918826134',
    '900019489',
    '917661165',
    '912159565',
    '900008387',
    '909889921',
    '900036350',
    '900031202',
    '900006697',
    '900616982',
    '900002121',
    '907603858',
    '900019788',
    '900047699',
    '913822577',
    '911966333',
    '903918488',
    '909142798',
    '903472029',
    '913477349',
    '900027159',
    '909163299',
    '913827400',
    '900045853',
    '903407003',
    '908491901',
    '911357738',
    '900035531',
    '901713116',
    '900031514',
    '907874453',
    '917175082',
    '918825757',
    '900096826',
    '918104608',
    '909802600',
    '909918170',
    '900022453',
    '905323229',
    '900032567',
    '901695020',
    '909743619',
    '900047348',
    '900033958',
    '903162642',
    '901073074',
    '909198113',
    '900015160',
    '907303662',
    '900008556',
    '907896059',
    '906090853',
    '917172261',
    '906232501',
    '900011065',
    '920008216',
    '900044163',
    '901218128',
    '910627866',
    '900014627',
    '917215200',
    '918121144',
    '913751948',
    '913669190',
    '900004513',
    '916500733',
    '916482637',
    '907057377',
    '900046659',
    '913529752',
    '900011403',
    '916402856',
    '900003421',
    '910609588',
    '900010987',
    '900008322',
    '911349236',
    '915385970',
    '918115775',
    '900048206',
    '909809243',
    '918825159',
    '908557499',
    '907608980',
    '900015602',
    '900063871',
    '904731404',
    '904534389',
    '916401595',
    '908484751',
    '900013041',
    '900034998',
    '918112434',
    '902603226',
    '909942597',
    '900016265',
    '900008452',
    '917097888',
    '901748593',
    '916472718',
    '900013457',
    '900001770',
    '908496477',
    '907608902',
    '900023064',
    '900663886',
    '913869390',
    '901518077',
    '918830229',
    '907597033',
    '901148396',
    '920005837',
    '910965645',
    '900038989',
    '900013522',
    '916432314',
    '907744427',
    '900008712',
    '900052236',
    '900014419',
    '905019120',
    '912953670',
    '909861620',
    '900011598',
    '913836591',
    '917920229',
    '904083354',
    '918103997',
    '918827733',
    '917242188',
    '900017448',
    '917254239',
    '900011715',
    '900026730',
    '909014189',
    '913579022',
    '904119858',
    '917169388',
    '908389123',
    '900045762',
    '912141768',
    '900018046',
    '916449344',
    '917922556',
    '900006996',
    '918825783',
    '915572169',
    '910626033',
    '912960625',
    '900016031',
    '916483989',
    '909863934',
    '900044579',
    '918823274',
    '900006034',
    '913765338',
    '900007074',
    '913764116',
    '900029408',
    '906280055',
    '900010142',
    '911414756',
    '900042850',
    '900004955',
    '900006710',
    '900045658',
    '909133386',
    '913812333',
    '901228476',
    '907490173',
    '901316044',
    '900019281',
    '900050377',
    '901502477',
    '900039860',
    '917240251',
    '900036311',
    '917237092',
    '917227316',
    '907620147',
    '908492018',
    '916530412',
    '913839360',
    '909851649',
    '916852123',
    '908491836',
    '900010220',
    '916479426',
    '909169097',
    '900024000',
    '916402466',
    '907475860',
    '913784162',
    '905639480',
    '904778581',
    '903907048',
    '905086460',
    '900010441',
    '909907575',
    '917966587',
    '918825094',
    '918108781',
    '900030032',
    '902773799',
    '917075346',
    '920021697',
    '918103087',
    '917248311',
    '909912164',
    '918110224',
    '912132486',
    '916498458',
    '903345955',
    '900004318',
    '916478035',
    '908494020',
    '900030656',
    '910631181',
    '917956928',
    '910631259',
    '901647791',
    '916502202',
    '900006463',
    '902967798',
    '917950753',
    '907585580',
    '900000106',
    '909169357',
    '902110786',
    '900001432',
    '913010324',
    '908497608',
    '908338332',
    '907563337',
    '918826888',
    '916491685',
    '916483521',
    '912893961',
    '900038209',
    '916477996',
    '911333103',
    '907570981',
    '900007568',
    '900014055',
    '900043110',
    '900004669',
    '917241681',
    '900007633',
    '900002654',
    '908362070',
    '900367278',
    '918825250',
    '900025508',
    '900008491',
    '900006411',
    '918831048',
    '909905144',
    '913828674',
    '905608228',
    '910598694',
    '907469061',
    '910598434',
    '912159396',
    '918828929',
    '912953605',
    '900006190',
    '910837751',
    '917242929',
    '900043123',
    '907897775',
    '900045983',
    '900011897',
    '915607867',
    '910576568',
    '913769173',
    '913727729',
    '900050442',
    '909070076',
    '913765351',
    '917324387',
    '900027042',
    '909893392',
    '918825471',
    '908507748',
    '909168824',
    '907384821',
    '900017110',
    '913868129',
    '900046269',
    '900028329',
    '903783977',
    '900047049',
    '900041264',
    '900076520',
    '900046139',
    '900052093',
    '900050832',
    '911136075',
    '901063220',
    '918825445',
    '917953236',
    '918825341',
    '904787460',
    '918825510',
    '916481571',
    '903185119',
    '907609006',
    '918825172',
    '908989697',
    '916502384',
    '900005176',
    '900010168',
    '909803445',
    '902366275',
    '913717680',
    '916481506',
    '900017019',
    '900033022',
    '900021790',
    '909169188',
    '900064287',
    '912993814',
    '917243150',
    '918544723',
    '903548820',
    '910936460',
    '900048401',
    '905017170',
    '900013184',
    '904506257',
    '918672851',
    '900047140',
    '900013379',
    '900710738',
    '900016967',
    '900025404',
    '900026574',
    '907938556',
    '900006099',
    '900000821',
    '900049727',
    '916401556',
    '901386166',
    '911377251',
    '900012612',
    '916406925',
    '913295024',
    '917197663',
    '907556512',
    '910630635',
    '907874466',
    '916449409',
    '908778356',
    '900032008',
    '913020984',
    '907599984',
    '912153715',
    '913753794',
    '900041173',
    '904208830',
    '912925837',
    '900021088',
    '900012690',
    '910626670',
    '907608863',
    '908388759',
    '913869507',
    '900915527',
    '900021465',
    '911383868',
    '900014445',
    '909906548',
    '910598681',
    '918103971',
    '900015823',
    '918634085',
    '900004760',
    '901848901',
    '900040705',
    '912032659',
    '918830814',
    '900680604',
    '907551312',
    '900031384',
    '917922478',
    '918074565',
    '910188986',
    '900041836',
    '900003590',
    '908509685',
    '918826706',
    '907085106',
    '900003460',
    '913002225',
    '904218463',
    '912159942',
    '918827811',
    '900036714',
    '900008127',
    '910497424',
    '910675303',
    '900021959',
    '917248207',
    '912214256',
    '908492083',
    '906487509',
    '918809052',
    '909895264',
    '912207392',
    '900010532',
    '900055148',
    '908491758',
    '901333659',
    '900005410',
    '902788619',
    '905826745',
    '908345196',
    '900068746',
    '912188217',
    '900020737',
    '901107147',
    '917172053',
    '900000951',
    '900006047',
    '917197650',
    '906269434',
    '900007113',
    '904959762',
    '900036597',
    '917240238',
    '900055707',
    '910624564',
    '900520457',
    '900012001',
    '900030214',
    '900394877',
    '900030539',
    '900789895',
    '903524029',
    '918544710',
    '913828596',
    '900032099',
    '902221312',
    '902476632',
    '917951715',
    '900036779',
    '900051209',
    '900004552',
    '918064061',
    '913841297',
    '900039483',
    '900035856',
    '918106441',
    '916467726',
    '905185260',
    '910606832',
    '907608850',
    '909162155',
    '913764428',
    '917215135',
    '900001913',
    '900008868',
    '916479452',
    '908481137',
    '906310449',
    '900006450',
    '900018215',
    '900003343',
    '909169071',
    '900011585',
    '918825432',
    '900009323',
    '900016551',
    '916480063',
];
}
