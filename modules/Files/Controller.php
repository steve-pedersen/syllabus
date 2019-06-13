<?php

class Syllabus_Files_Controller extends Syllabus_Master_Controller
{
    public static function GetRouteMap ()
    {
        return array(
            '/files/:fid/download' => array( 'callback' => 'download', 'fid' => '[0-9]+'),
            '/files/:fid/imagesrc' => array( 'callback' => 'imageSrc', 'fid' => '[0-9]+'),
            '/files/check'         => array( 'callback' => 'check'),
        );
    }

	public function check ()
	{
		$this->requirePermission('file download');
		$missing = array();
		$files = $this->schema('Syllabus_Files_File')->getAll();

		foreach ($files as $file)
		{
			if (!file_exists($file->localName))
			{
				$missing[$file->id] = $file;
			}
		}

		$this->template->missing = $missing;
	}

    public function imageSrc ()
    {
        $this->requireLogin();
        $fid = $this->getRouteVariable('fid');
        $file = $this->requireExists($this->schema('Syllabus_Files_File')->get($fid));
        $file->sendFile($this->response);
    }
  
    public function download ()
    {
        $account = $this->requireLogin();
        
        $fid = $this->getRouteVariable('fid');
        $file = $this->requireExists($this->schema('Syllabus_Files_File')->get($fid));
        
        if ($file->uploadedBy && ($account->id != $file->uploadedBy->id))
        {
            
            if ($item = $this->getRouteVariable('item'))
            {
                $authZ = $this->getAuthorizationManager();
                $extension = $item->extension;
                
                if ($authZ->hasPermission($account, $extension->getItemViewTask(), $item))
                {
                    $file->sendFile($this->response);
                }
            }
            
            $this->requirePermission('file download');
        }
        
        $file->sendFile($this->response);
    }

}