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
        $cachedVersions = true;
        // $keyPrefix = "{$eid}-";

        // get all syllabus ids for this entity based on offset and limit=10
        // $sids = array('1', '6', '4', '7', '3', '8', '5', '9', '2', '10');
        $sids = array('1', '6', '4');

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