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
        return array(
        	'syllabus/entity/:eid'				=> array('callback' => 'list', 	':eid' => '[0-9]+'),
        	'syllabus/entity/:eid/view/:sid'	=> array('callback' => 'view', 	':eid' => '[0-9]+', ':sid' => '[0-9]+'),
        	'syllabus/entity/:eid/edit/:sid'	=> array('callback' => 'edit', 	':eid' => '[0-9]+', ':sid' => '[0-9]+|new'),
            'syllabus/entity/:eid/render/:sid'  => array('callback' => 'render',':eid' => '[0-9]+', ':sid' => '[0-9]+'),
        );
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
        $urls = array();
        $messages = array();
        $cachedVersions = false;
        // $keyPrefix = "{$eid}-";


        // get viewer account
        // $viewer = $this->getAccount();

        // get entity route var and schema object
        //  - does viewer belong to entity and/or have permission to e.g. 'view syllabi' on entity object?
        // $eid = $this->getRouteVariable('eid');
        // $entity = $this->helper('activeRecord')->fromRoute('Syllabus_Academia_Entity', array('eid' => 'id'));
        $authZ = $this->getAuthorizationManager();
        $azids = $authZ->getObjectsForWhich($viewer, 'academic entity belong');
        $azids = $authZ->getObjectsForWhich($viewer, 'academic entity embody');
        $entities = $this->schema('Syllabus_Academia_Entity')->getByAzids($azids);
        // get page offset, default 0

        // get all syllabus ids for this entity based on offset and limit=10
        $sids = array('1', '6', '4', '7', '3', '8', '5', '9', '2', '10');

        // generate some key/value access tokens for each render page
        $keyPrefix = "{$eid}-";
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
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

    public function render ()
    {
        // $entities = $this->schema('Syllabus_Academia_Entity');   // need this?
        // $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');  // need this?
        $tokenHeader = $this->request->getHeader('Access-Token');
        $eid = $this->getRouteVariable('eid');
        $sid = $this->getRouteVariable('sid');
        $key = "{$eid}-{$sid}";
        $uid = Syllabus_Services_Screenshotter::CutUid($key);
        if ($tokenHeader === $uid)
        {
            // NOTE: Temporary debug ***************
            // if (!$this->requireExists($syllabi->get($sid)))
            if (intval($sid) > 5) {
                // $this->notFound();
                $response_code = 404;
                http_response_code (404);
                die;
            } else {
                $this->template->syllabusId = $sid;
            }
        }
        else
        {
            http_response_code (404);
            die;            
        }

    }

    // /**      ---- DEV VERSION OF THIS FUNCTION ----
    //  * Viewing in thumbnail mode will make requests to Screenshotter service
    //  * to fetch images of all syllabi associated with this entity.
    //  */
    // public function list ()
    // {
    //     // $account = $this->requireLogin();
    //     $app = $this->getApplication();
    //     $eid = $this->getRouteVariable('eid');
    //     $eid = 999;
    //     $urls = array();
    //     $cachedVersions = true;

    //     $messages = array();
    //     $sids = array('1', '6', '4', '7', '3', '8', '5', '9', '2', '10');

    //     foreach ($sids as $id)
    //     {
    //         $urls[$id] = $this->baseUrl("syllabus/entity/{$eid}/view/{$id}");
    //     }

    //     $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
    //     $results = $screenshotter->concurrentRequests($urls, $cachedVersions);
    //     $results = json_decode($results);

    //     $this->template->messages = $results->messages;
    //     $this->template->urls = $results->imageUrls;
    // }

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