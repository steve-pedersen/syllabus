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
        $cachedVersions = true;

        $messages = array();
        $dummyIds = array('1', '6', '4', '7', '3', '8', '5', '9', '2', '10');

        foreach ($dummyIds as $id)
        {
            $urls[$id] = $this->baseUrl("syllabus/entity/{$eid}/view/{$id}");
        }

        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        $results = $screenshotter->concurrentRequests($urls, $cachedVersions);
        $results = json_decode($results);

        $this->template->messages = $results->messages;
        $this->template->urls = $results->imageUrls;
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