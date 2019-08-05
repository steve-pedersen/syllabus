<?php

class Syllabus_Syllabus_AdminController extends Syllabus_Master_AdminController
{
    public static function getRouteMap ()
    {
        return [
            'admin/templates/university' => ['callback' => 'universityTemplates'],
            'admin/syllabus/resources' => ['callback' => 'campusResources'],
            'admin/syllabus/guidedocs' => ['callback' => 'guideDocs'],
        ];
    }

	public function beforeCallback ($callback)
	{
		parent::beforeCallback($callback);
		$this->requirePermission('admin');
	}

    public function universityTemplates ()
    {
        $this->template->addBreadcrumb('admin/templates/university', 'University Templates');
        $viewer = $this->requireLogin();
        if (!$this->hasPermission('admin'))
        {
            $this->sendError(403, 'Forbidden', 'Non-Admin', 'You must be a site Administrator to manage university templates.');
        }
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $siteSettings = $this->getApplication()->siteSettings;
        
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');

        if ($newUserId = $this->request->getQueryParameter('userId'))
        {
            $siteSettings->setProperty('university-template-user-id', $newUserId);
            $userId = $newUserId;
        }

        if ($userId)
        {
            $this->template->universityTemplates = $syllabi->find(
                $syllabi->createdById->equals($userId),
                ['orderBy' => ['-modifiedDate', '-createdDate']]
            );
        }

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand())
            {
                case 'select':
                    $templateId = $this->request->getPostParameter('template');
                    if ($template = $syllabi->get($templateId))
                    {
                        $template->templateAuthorizationId = 'university/' . $templateId;
                        $template->save();
                        $siteSettings->setProperty('university-template-id', $templateId);
                        $this->flash('The University Template has been set.');
                    }
                    break;
                case 'unset':
                    $siteSettings->setProperty('university-template-id', null);
                    $this->flash('Template has been unset');
            }
            $this->response->redirect('admin/templates/university');
        }

        $this->template->templateId = $templateId;
        $this->template->userId = $userId;
    }

    public function guideDocs ()
    {
        $this->template->addBreadcrumb('admin/syllabus/guidedocs', 'Guidelines & Documents');
        $files = $this->schema('Syllabus_Files_File');
        $guideDocs = $this->schema('Syllabus_Syllabus_SharedResource');
        $allGuideDocs = $guideDocs->getAll(['orderBy' => ['sortOrder', 'title']]);
        $bottommostPosition = -1;
        if ($allGuideDocs)
        {
            $bottommostPosition = array_values(array_slice($allGuideDocs, -1))[0]->sortOrder;    
        }
        
        if ($this->request->wasPostedByUser())
        {
            $data = $this->request->getPostParameters();
            switch ($this->getPostCommand())
            {
                case 'upload':
                    $file = $files->createInstance();
                    $file->createFromRequest($this->request, 'file', false);
                    
                    if ($file->isValid())
                    {
                        $file->uploadedBy = $this->getAccount();
                        $file->moveToPermanentStorage();
                        $file->save();
                    }
                    
                    $this->template->errors = $file->getValidationMessages();
                    break;
                
                case 'save': 
                    $resource = (isset($data['resourceId'])&&$data['resourceId']!=='') ? 
                        $guideDocs->get($data['resourceId']) : $guideDocs->createInstance();
                    $resource->sortOrder = (isset($data['resource']) && isset($data['resource']['sortOrder']) ? 
                        $data['resource']['sortOrder'] : ($bottommostPosition+1));
                    $resource->fileId = isset($data['resource']['fileId']) ? (int)$data['resource']['fileId'] : null;
                    $resource->title = $data['resource']['title'];
                    $resource->iconClass = $data['resource']['iconClass'];
                    $resource->description = $data['resource']['description'];
                    $resource->url = isset($data['resource']['url']) ? $data['resource']['url'] : null;
                    $resource->createdDate = new DateTime;
                    $resource->active = isset($data['resource']['active']);
                    $resource->save();
                    
                    $this->flash('Resource saved.');
                    break;

                case 'delete':
                    $resource = $this->requireExists($guideDocs->get(key($this->getPostCommandData())));

                    $resource->delete();
                    $this->flash('Resource has been flagged as deleted.', 'secondary');
                    break;

                case 'sort':
                    foreach ($data['sortOrder'] as $id => $sortOrder)
                    {
                        $resource = $this->requireExists($guideDocs->get($id));
                        $resource->sortOrder = $sortOrder;
                        $resource->save();
                    }
                    $this->flash('Order of campus resources updated.', 'success');
                    break;
            }
            $this->response->redirect('admin/syllabus/guidedocs');
        }

        if (!$this->request->wasPostedByUser() && ($resourceId = $this->request->getQueryParameter('edit')))
        {
            $this->template->resource = $guideDocs->get($resourceId);
        }

        $this->template->bottommostPosition = $bottommostPosition;
        $this->template->guideDocs = $allGuideDocs;
        $this->template->files = $files->getAll();
    }

    public function campusResources ()
    {
        $this->template->addBreadcrumb('admin/syllabus/resources', 'Campus Resources');
        $files = $this->schema('Syllabus_Files_File');
        $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
        $allResources = $campusResources->find(
            $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
            ['orderBy' => ['sortOrder', 'title']]
        );
        $bottommostPosition = -1;
        if ($allResources)
        {
            $bottommostPosition = array_values(array_slice($allResources, -1))[0]->sortOrder;    
        }
        
        if ($this->request->wasPostedByUser())
        {
            $data = $this->request->getPostParameters();
            switch ($this->getPostCommand())
            {
                case 'upload':
                    $file = $files->createInstance();
                    $file->createFromRequest($this->request, 'file', false);
                    
                    if ($file->isValid())
                    {
                        $file->uploadedBy = $this->getAccount();
                        $file->moveToPermanentStorage();
                        $file->save();
                    }
                    
                    $this->template->errors = $file->getValidationMessages();
                    break;
                
                case 'save': 
                    $resource = (isset($data['resourceId'])&&$data['resourceId']!=='') ? 
                        $campusResources->get($data['resourceId']) : $campusResources->createInstance();
                    $resource->sortOrder = (isset($data['resource']) && isset($data['resource']['sortOrder']) ? 
                        $data['resource']['sortOrder'] : ($bottommostPosition+1));
                    $resource->imageId = $data['resource']['imageId'];
                    $resource->title = $data['resource']['title'];
                    $resource->abbreviation = $data['resource']['abbreviation'];
                    $resource->description = $data['resource']['description'];
                    $resource->url = $data['resource']['url'];
                    $resource->save();
                    
                    $this->flash('Resource saved.');
                    break;

                case 'delete':
                    $resource = $this->requireExists($campusResources->get(key($this->getPostCommandData())));
                    $resource->deleted = true;
                    $resource->save();
                    $this->flash('Resource has been flagged as deleted. 
                        <form action="'.$this->baseUrl('/admin/syllabus/resources').'" method="post">
                            <input type="submit" name="command[undelete]['.$resource->id.']" 
                            value="Undo Delete" class="btn btn-warning" />
                            '. $this->template->generateFormPostKey([], null) .'
                        </form>
                        ', 
                        'secondary'
                    );
                    break;

                case 'undelete':
                    $resource = $this->requireExists($campusResources->get(key($this->getPostCommandData())));
                    $resource->deleted = false;
                    $resource->save();
                    $this->flash('Resource has been restored.', 'success');
                    break;

                case 'sort':
                    foreach ($data['sortOrder'] as $id => $sortOrder)
                    {
                        $resource = $this->requireExists($campusResources->get($id));
                        $resource->sortOrder = $sortOrder;
                        $resource->save();
                    }
                    $this->flash('Order of campus resources updated.', 'success');
                    break;
            }
            $this->response->redirect('admin/syllabus/resources');
        }

        if (!$this->request->wasPostedByUser() && ($resourceId = $this->request->getQueryParameter('edit')))
        {
            $this->template->resource = $campusResources->get($resourceId);
        }

        $this->template->bottommostPosition = $bottommostPosition;
        $this->template->campusResources = $allResources;
        $this->template->files = $files->getAll();
    }
}















