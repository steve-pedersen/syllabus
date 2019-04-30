<?php

/**
 * Handles main business logic for entity/user syllabi.
 * 
 * @author      Steve Pedersen <pedersen@sfsu.edu>
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Syllabus_Controller extends Syllabus_Master_Controller {

    public static function getRouteMap ()
    {
        return [
            'syllabi'                           => ['callback' => 'mySyllabi'],
            'syllabus/:id'                      => ['callback' => 'edit',  ':id' => '[0-9]+|new'],
            // 'syllabus/:id/:version'             => ['callback' => 'view',  ':id' => '[0-9]+|new'],
        	// 'syllabus/entity/:eid'              => ['callback' => 'list', 	':eid' => '[0-9]+'],
        	// 'syllabus/entity/:eid/view/:sid'    => ['callback' => 'view', 	':eid' => '[0-9]+', ':sid' => '[0-9]+'],
        	// 'syllabus/entity/:eid/edit/:sid'    => ['callback' => 'edit', 	':eid' => '[0-9]+', ':sid' => '[0-9]+|new'],
            // 'syllabus/entity/:eid/render/:sid'  => ['callback' => 'render',':eid' => '[0-9]+', ':sid' => '[0-9]+'],
        ];
    }

    public function mySyllabi ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $mySyllabi = $syllabi->find($syllabi->createdById->equals($viewer->id), ['orderBy' => '-createdDate']);

        // TODO: update header vars based on which view mode we are in
        $title = 'My Syllabi';
        $subtitle = 'Overview';
        $description = 'Temporary layout';
        $this->setPageTitle($title);
        $this->buildHeader('partial:_header.html.tpl', $title, $subtitle, $description);

        $this->template->syllabi = $mySyllabi;
    }

    public function edit ()
    {
        $viewer = $this->requireLogin();
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', ['allowNew' => true]);

        $data = $this->request->getPostParameters();
        if (isset($data['syllabus']['version']))
        {
            $syllabusVersion = $syllabusVersions->get($data['syllabus']['version']['id']);
        }
        else
        {
            $syllabusVersion = $syllabus->latestVersion ?? $syllabusVersions->createInstance();
        }
        $sectionExtensions = $syllabusVersion->getSectionExtensions();

        $title = ($syllabus->inDatasource ? 'Edit' : 'Create') . ' Syllabus';
        $this->setPageTitle($title);
        $this->buildHeader('partial:_header.edit.html.tpl', $title, $syllabusVersion->title, $syllabusVersion->description);
        $this->template->minimized = true;

        if ($this->request->wasPostedByUser())
        {      
            switch ($this->getPostCommand()) {

                case 'addsection':
                    $realSection = $this->schema($data['addSection'])->createInstance();
                    $realSectionExtension = $sectionVersions->createInstance()->getExtensionByName($data['addSection']);

                    $this->template->newSection = true;
                    $this->template->realSection = $realSection;
                    $this->template->realSectionClass = $data['addSection'];
                    $this->template->sectionExtension = $realSectionExtension;
                    break;

                case 'editsection':
                    $sectionVersion = $sectionVersions->get($data['section']['version']['id']);
                    $genericSection = $sectionVersion->section;
                    foreach ($data['section']['properties'] as $key => $prop)
                    {
                        $genericSection->$key = $prop;
                    }

                    $this->template->newSection = false;
                    $this->template->realSection = $sectionVersion->resolveSection();
                    $this->template->realSectionClass = $data['editSection'];
                    $this->template->sectionExtension = $sectionVersion->getExtensionByName($data['editSection']);
                    $this->template->genericSection = $genericSection;
                    $this->template->sectionVersion = $sectionVersion;
                    break;

                case 'editsyllabus':
                    $this->template->editMetadata = true;
                    $this->template->syllabusVersion = $syllabusVersion;
                    break;

                case 'savesection':
                case 'savesyllabus':
                    $this->saveSyllabus($syllabus);
                    break;
            }
        }

        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sections = $syllabus->getSections(true);
        $this->template->sectionMode = 'view';
        $this->template->sectionExtensions = $sectionExtensions;
    }

    /**
     * Bump syllabus and section versions--creating new instances of generic, real, and subsections.
     */  
    protected function saveSyllabus ($syllabus)
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        $subsections = $this->schema('Syllabus_Syllabus_Subsection');

        $data = $this->request->getPostParameters();
        $anyChange = false;
        $sectionChange = false;

        // save any metadata
        if (isset($data['syllabus']))
        {
            $anyChange = true;
            if ($syllabus->createdDate)
            {
                $syllabus->modifiedDate = new DateTime;
            }
            else
            {
                $syllabus->createdDate = new DateTime;
                $syllabus->createdById = $viewer->id;
            }
            $syllabus->save();
        }

        // save section data
        // TODO: add Subsection logic
        if (isset($data['section']))
        {
            $anyChange = true;
            $sectionChange = true;
            $realSectionClass = $data['section']['real']['class'];
            $extKey = $data['section']['extKey'];

            if (isset($data['section']['isNew']) && $data['section']['isNew'])
            {
                $genericSection = $sections->createInstance();
                $genericSection->createdDate = new DateTime;
                $genericSection->createdById = $viewer->id;
            }
            else
            {
                $genericPreviousVer = $sectionVersions->get($data['section']['version']['id']);
                $genericSection = $genericPreviousVer->section;
                $genericSection->modifiedDate = new DateTime;
            }
            $genericSection->save();   

            $realSection = $this->schema($realSectionClass)->createInstance();
            $realSection->absorbData($data['section']['real']);
            $realSection->save();

            // $subsection = $subsections->createInstance(); 

            $genericSectionVer = $sectionVersions->createInstance();
            $genericSectionVer->createdDate = new DateTime;
            $genericSectionVer->sectionId = $genericSection->id;
            $genericSectionVer->$extKey = $realSection->id;
            if (isset($data['section']['generic']))
            {
                $genericSectionVer->absorbData($data['section']['generic']);
            }
            $genericSectionVer->save();
        }

        // save title/description & bump syllabus version
        if (isset($data['syllabus']) || isset($data['syllabusVersion']) || isset($data['section']))
        {
            $anyChange = true;
            $oldSyllabusVersion = $syllabusVersions->get($data['syllabusVersion']['id']) ?? $syllabus->latestVersion;
            $newSyllabusVersion = $syllabusVersions->createInstance();
            $newSyllabusVersion->createdDate = new DateTime;
            $newSyllabusVersion->syllabusId = $syllabus->id;
            if (isset($data['syllabus']) && isset($data['syllabus']['title']))
            {
                $newSyllabusVersion->absorbData($data['syllabus']);
            }
            else
            {
                $newSyllabusVersion->title = $oldSyllabusVersion->title;
                $newSyllabusVersion->description = $oldSyllabusVersion->description;
            }
            $newSyllabusVersion->save();
        }

        // map any bumped section versions to this new syllabus version
        // TODO: write a sorting function based on the 'sort_order' property ******
        $savedSectionVersions = false;
        if ($sectionChange)
        {   
            $anyChange = true;
            $savedSectionVersions = true;
            $version = array_pop($oldSyllabusVersion->sectionVersions->asArray());
            $bottomPosition = $version ? $oldSyllabusVersion->sectionVersions->getProperty($version, 'sort_order') : 0;
            $newPosition = $data['section']['sortOrder'] ?? ($bottomPosition + 1);
            $newSyllabusVersion->sectionVersions->add($genericSectionVer);
            $newSyllabusVersion->sectionVersions->setProperty($genericSectionVer, 'sort_order', $newPosition);
            $newSyllabusVersion->sectionVersions->setProperty($genericSectionVer, 'read_only', (isset($data['section']['readOnly'])&&$data['section']['readOnly'] ? true : false));
            $newSyllabusVersion->sectionVersions->setProperty($genericSectionVer, 'is_anchored', (isset($data['section']['isAnchored']) ? true : false));
            $newSyllabusVersion->sectionVersions->setProperty($genericSectionVer, 'log', ($data['section']['log'] ?? ''));
            $newSyllabusVersion->save();
            $newSyllabusVersion->sectionVersions->save();
        }

        // reassign section versions to this new syllabus version when no changes made to any section
        if (!$savedSectionVersions && $oldSyllabusVersion && $newSyllabusVersion)
        {
            foreach ($oldSyllabusVersion->sectionVersions as $sv)
            {
                $newSyllabusVersion->sectionVersions->add($sv);
                foreach ($oldSyllabusVersion->sectionVersions->getProperties($sv) as $key => $val)
                {
                    $newSyllabusVersion->sectionVersions->setProperty($sv, $key, $val);
                }
            }
            $newSyllabusVersion->save();
            $newSyllabusVersion->sectionVersions->save();
        }

        if (!$anyChange) 
        { 
            $this->flash('No changes were made', 'warning'); 
        }
        else
        {
            $syllabus->modifiedDate = new DateTime;
            $syllabus->save();
        }

        $this->response->redirect('syllabus/' . $syllabus->id);
    }

    /**
     * Viewing in thumbnail mode will make requests to Screenshotter service
     * to fetch images of all syllabi associated with this entity.
     */
    public function list ()
    {
        // $account = $this->requireLogin();
        $app = $this->getApplication();
        $eid = $this->getRouteVariable('eid');
        $eid = 999;
        $urls = [];
        $messages = [];
        $cachedVersions = true;
        // $keyPrefix = "{$eid}-";

        // get all syllabus ids for this entity based on offset and limit=10
        // $sids = ['1', '6', '4', '7', '3', '8', '5', '9', '2', '10'];
        $sids = ['1', '6', '4'];

        // generate some key/value access tokens for each render page
        $keyPrefix = "{$eid}-";
        $screenshotter = new Syllabus_Services_Screenshotter($app);
        $screenshotter->saveUids($eid, $sids);

        foreach ($sids as $id)
        {
            $urls[$id] = $this->baseUrl("syllabus/entity/{$eid}/render/{$id}");
        }

        $results = $screenshotter->concurrentRequests($urls, $cachedVersions, $keyPrefix);
        $results = json_decode($results);

        $this->template->messages = $results->messages;
        $this->template->urls = $results->imageUrls;
    }

    /**
     * Accessible only with proper Access-Token
     */    
    public function render ()
    {
        // $entities = $this->schema('Syllabus_Academia_Entity');
        // $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $tokenHeader = $this->requireExists($this->request->getHeader('Access-Token'));
        $eid = $this->getRouteVariable('eid');
        $sid = $this->getRouteVariable('sid');
        $key = "{$eid}-{$sid}";
        $uid = Syllabus_Services_Screenshotter::CutUid($key);

        if ($tokenHeader && $uid && ($tokenHeader == $uid))
        {
            // NOTE: Temporary debug ***************
            // if (!$this->requireExists($syllabi->get($sid)) || !$this->requireExists($entities->get($eid)))
            if (intval($sid) > 5) {
                // $this->notFound();
                http_response_code (404);
                die;
            } else {
                $this->template->syllabusId = $sid;
            }
        }
        elseif (!$tokenHeader && !$uid)
        {

        }
        else
        {
            http_response_code (401);
            die;            
        }
    }


    public function view ()
    {
        // $account = $this->requireLogin();
        $eid = $this->getRouteVariable('eid');
        $sid = $this->getRouteVariable('sid');
        $sidInt = intval($sid);

        // NOTE: Temporary debug ***************
        // do a requireExists() here
        if ($sidInt > 5) {
            // $this->notFound();
            $response_code = 404;
            http_response_code (404);
            die;
        } else {
            $this->template->syllabusId = $sid;
        }
    }


}