<?php
require_once Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'word', 'lib', 'Document.php');

/**
 * Syllabus active record and Word utilities.
 *
 * @author      Charles O'Sullivan (chsoney@sfsu.edu) 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Syllabus extends Bss_ActiveRecord_BaseWithAuthorization implements Bss_AuthZ_IObjectProxy
{
    private $imageUrl;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_syllabus',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:syllabus/Syllabus/',
            
            'id' => 'int',       
            'createdById' => ['int', 'nativeName' => 'created_by_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'templateAuthorizationId' => ['string', 'nativeName' => 'template_authorization_id'],
            'token' => 'string',
           
            'createdBy' => ['1:1', 'to' => 'Bss_AuthN_Account', 'keyMap' => ['created_by_id' => 'id']],
            'versions' => ['1:N', 
                'to' => 'Syllabus_Syllabus_SyllabusVersion', 
                'reverseOf' => 'syllabus', 
                'orderBy' => ['+createdDate', '+id']
            ],
            'roles' => ['1:N', 
                'to' => 'Syllabus_Syllabus_Role', 
                'reverseOf' => 'syllabus', 
                'orderBy' => ['+createdDate', '+id']
            ],
        ];
    }

    public function getAuthorizationId ()
    {
        return 'at:syllabus:syllabus/Syllabus/' . $this->id;
    }

    public function getLatestVersion ()
    {
        $versions = $this->versions->asArray();
        return array_pop($versions);
    }

    public function getObjectProxies ()
    {
        $objectProxyList = [];

        foreach ($this->roles as $role)
        {
            if (!$role->isExpired)
            {
                $objectProxyList[] = $role;
            }
        }
        
        return $objectProxyList;
    }

    public function getCourseSection ()
    {
        $schema = $this->getSchema('Syllabus_ClassData_CourseSection');
        return $schema->findOne($schema->syllabus_id->equals($this->id));
    }

    // $withExt - adds a 'section' property to the section object containing it's extension
    public function getSections ($withExt=true)
    {
        $sections = null;
        if ($this->getLatestVersion())
        {
            $sections = $this->getLatestVersion()->getSectionVersionsWithExt($withExt);
        }
        return $sections;
    }

    public function getPublishedSyllabus ($syllabus)
    {
        $schema = $this->getSchema('Syllabus_Syllabus_PublishedSyllabus');
        $published = $schema->findOne(
            $schema->syllabusId->isNotNull()->andIf(
                $schema->syllabusId->equals($this->_fetch('id'))
            )
        );
        return $published;        
    }

    public function getShareLevel ()
    {
        $published = $this->getPublishedSyllabus($this);
        return ($published ? $published->shareLevel : 'private');
    }

    // Ad hoc roles give users permission to edit & view or clone & view other user's syllabi
    public function getAdHocRoles ()
    {
        $authZ = $this->application->authorizationManager;
        $adHocRoles = [];
        $adHocUsersExist = false;
        foreach ($this->roles as $role)
        {
            $now = new DateTime;
            $role->expiration = $role->expiryDate ? $now->diff($role->expiryDate) : null;
            if ($role->expiration && is_object($role->expiration))
            {
                $intervalString = '';
                $units = ['y' => '%y-year', 'm' => '%m-month', 'd' => '%d-day', 'h' => '%h-hour'];
                foreach ($units as $key => $format)
                {
                	if (($key === 'h' && $role->expiration->$key && $intervalString === '') || 
                		($key !== 'h' && $role->expiration->$key))
                	{
                		$intervalString .= $role->expiration->format($format);
                		$intervalString .= $role->expiration->$key > 1 ? 's ' : ' ';
                	}
                }
                $role->expiration = $intervalString;
            }

            $adHocRoles[$role->id] = ['role' => $role, 'expiration' => $role->expiration];
            $azids = $authZ->getSubjectsWhoCan('syllabus edit', $role);
            array_merge($azids, $authZ->getSubjectsWhoCan('syllabus clone', $role));
            if ($users = $this->getSchema('Bss_AuthN_Account')->getByAzids($azids))
            {
                $adHocRoles[$role->id]['users'] = $users;
                $adHocUsersExist = true;
            }
        }

        return $adHocUsersExist ? $adHocRoles : null;
    }

    public function generateToken () 
    {
        if ($this->_fetch('token') === null)
        {
            $this->_assign('token', $this->getApplication()->generateSecretCode(7));
        }

        return $this->_fetch('token');
    }

    public function getOrganization ()
    {
        $organization = null;
        foreach ($this->versions as $syllabusVersion)
        {
            if ($syllabusVersion->syllabus->templateAuthorizationId)
            {
                list($type, $id) = explode('/', $syllabusVersion->syllabus->templateAuthorizationId);
                switch ($type)
                {
                    case 'departments':
                        $organization = $this->getSchema('Syllabus_AcademicOrganizations_Department')->get($id);
                        break;
                    case 'colleges':
                        $organization = $this->getSchema('Syllabus_AcademicOrganizations_College')->get($id);
                        break;
                    default:
                        break;
                }
                break;
            }            
        }

        return $organization;
    }

    public function fetchImageUrl ($ctrl)
    {
        $screenshotter = new Syllabus_Services_Screenshotter($this->application);
        $sid = $this->id;
        $results = $ctrl->getScreenshotUrl($sid, $screenshotter);
        $this->imageUrl = $results->imageUrls->$sid;

        return $this->imageUrl;
    }

    /**
     * Returns the relative version number for a particular SyllabusVersion
     * belonging to this Syllabus.
     */   
    public function getNormalizedVersion ($trueId=null)
    {
        $trueId = $trueId ?? $this->latestVersion->id;
        $counter = 1;
        foreach ($this->versions as $version)
        {
            if ($trueId === $version->id)
            {
                return $counter;
            }
            $counter++;
        }
        return $trueId;
    }

    // TODO: add param for specific versions
    public function getTitle ()
    {
        return $this->latestVersion->title;
    }

    // TODO: add param for specific versions
    public function getDescription ()
    {
        return $this->latestVersion->description;
    }

    protected function hashPassword ($plainText)
    {
        return md5($plainText);
    }

    public function confirmPassword ($plainText)
    {
        return ($this->hashPassword($plainText) === $this->passwordHash);
    }

    public function setPassword ($plainText, $confirmPlainText)
    {
        $plainText = trim($plainText);
        $confirmPlainText = trim($confirmPlainText);
        $this->_assign('passwordHash', $this->hashPassword($plainText));
        
        if (empty($plainText))
        {
            $this->invalidate('passwordHash', 'You must set a course password when setting your course as password-protected.');
        }
        else
        {
            if (strlen($plainText) < 5)
            {
                $this->invalidate('passwordHash', 'A course password must be at least five characters long.');
            }
            
            if ($plainText !== $confirmPlainText)
            {
                $this->invalidate('passwordHash', 'Please enter the same password twice to change your course\'s password.');
            }
        }
    }

    public function getRelativeUrl ()
    {
        $url = '';
        
        if ($item = $this->resolveItem())
        {
            $url = $item->getUrl();
        }
        
        return $url;
    }

    public function generateCode ()
    {
        $num = $this->getApplication()->generateSecretCode();
        return $num;
    }

    protected function createTemplateInstance ($templateCreator)
    {
        $template = $templateCreator->createTemplateInstance();
        $template->setMasterTemplate(false);
        return $template;
    }
    
    public function getTemplateFile ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', $this->configurationTemplateName);
    }


    private $_listDef;
    private $_standardBorder;
    private $_wordConfiguration;
    
    
    public function getPositions ()
    {
        return $this->getAwards('employment');
    }
    
    public function getMemberships ()
    {
        return $this->getAwards('memberships');
    }
    
    public function getHonors ()
    {
        return $this->getAwards('honors');
    }
    
    public function getGrants ($type)
    {
        $data = $this->data;
        $grantList = array();
        
        if (!empty($data['grants'][$type]))
        {
            $grantSchema = $this->schema->getSchemaManager()->getSchema('Catalog_Grants_Grant');
            
            // $grantMap is used to keep the grants in the order they were
            // specified in the CV configuration.
            $grantMap = array();
            
            foreach ($data['grants'][$type] as $id)
            {
                $grantMap[$id] = null;
            }
            
            foreach ($grantSchema->findById($data['grants'][$type]) as $grant)
            {
                $grantMap[$grant->id] = $grant;
                
            }
            
            // array_filter removes the grants that no longer exist but were
            // referenced in the configuration.
            $grantList = array_values(array_filter($grantMap));
        }
        
        return $grantList;
    }
    
    public function getPublications ()
    {
        $data = $this->data;
        $publicationList = array();
        
        if (!empty($data['publications']))
        {
            $publicationSchema = $this->schema->getSchemaManager()->getSchema('Catalog_Publications_AbstractPublication');
            $publicationList = $publicationSchema->findById($data['publications'], array('orderBy' => array('pubYear', 'pubMonth', '-id')));
        }

        return $publicationList;
    }
    
    public function getDegrees ()
    {
        $data = $this->data;
        $degreeList = array();
        
        if (!empty($data['degrees']))
        {
            $educationSchema = $this->schema->getSchemaManager()->getSchema('Catalog_Faculty_Education');
            $degreeList = $educationSchema->findById($data['degrees'], array('orderBy' => 'years'));
        }

        return $degreeList;
    }
    
    public function generate ($templateCreator)
    {
        $doc = $this->startWordDoc();
        $doc = $this->createTopMatter($doc);
        
        $element = $this->startH2Section($doc, ' use for Modules ');
        
        if (($positions = $this->positions))
        {
            $element = $this->startH3Section($element, 'Employment');
            
            foreach ($positions as $p)
            {
                $element = $this->createPositionHonors($element, $p->year, $p->title . ', ' . $p->institution);
            }
            
            $element = $this->endH3Section($element);
        }
        
        if (($memberships = $this->memberships))
        {
            $element = $this->startH3Section($element, 'Memberships');
            
            foreach ($memberships as $p)
            {
                $element = $this->createPositionHonors($element, $p->year, $p->title . ', ' . $p->institution);
            }
            
            $element = $this->endH3Section($element);
        }
        
        if (($honors = $this->honors))
        {
            $element = $this->startH3Section($element, 'Honors');
            
            foreach ($honors as $p)
            {
                $element = $this->createPositionHonors($element, $p->year, $p->title . ', ' . $p->institution);
            }
            
            $element = $this->endH3Section($element);
        }
        $element = $this->endH2Section($element);
        
        if (($publications = $this->publications))
        {
            $element = $this->startH2Section($element, 'B. Selected peer-reviewed publications (in chronological order).');
            $element = $this->startPublicationList($element);
            
            foreach ($publications as $p)
            {
                $template = $this->createTemplateInstance($templateCreator);
                $template->assign('pub', $p);
                $citation = $template->fetch($p->getRenderTemplate('chicago'));
                $element = $this->createPublication($element, $citation);
            }
            
            $element = $this->endPublicationList($element);
            $element = $this->endH2Section($element);
        }
        
        $ongoing = $this->getGrants('ongoing');
        $completed = $this->getGrants('completed');
        
        if ($ongoing || $completed)
        {
            $element = $this->startH2Section($element, 'C. Research Support.');
            
            if ($ongoing)
            {
                $element = $this->startH3Section($element, 'Ongoing Research Support');
                foreach ($ongoing as $g)
                {
                    $element = $this->createGrant($element, $g);
                }                
                $element = $this->endH3Section($element);
            }
            
            if ($completed)
            {
                $element = $this->startH3Section($element, 'Completed Research Support');
                foreach ($completed as $g)
                {
                    $element = $this->createGrant($element, $g);
                }                
                $element = $this->endH3Section($element);
            }
            
            $element = $this->endH2Section($element);
        }
        
        $file = str_replace(' ', '_', $this->courseNumber) . '-' . str_replace(' ', '_', $this->sectionNumber) . '-' . str_replace(' ', '_', $this->title) . '.doc';
        //$element->sendXML($file);
        $xml = $element->saveXML();
        $templateCreator->getResponse()->sendData(
            $xml, 'application/msword', null, strlen($xml),
            array(
                'noCache' => true,
                'attachmentName' => $file,
            )
        );
    }
    
    protected function getAwards ($type)
    {
        $data = $this->data;
        $awardList = array();
        
        if (!empty($data['positions_honors'][$type]))
        {
            $awards = $this->schema->getSchemaManager()->getSchema('Catalog_Faculty_Award');
            $awardList = $awards->findById($data['positions_honors'][$type], array('orderBy' => 'year'));
        }
        
        return $awardList;
    }
    
    private function startWordDoc ()
    {
        $data = $this->data;

        $tableCellBorder = array('style' => 'single', 'color' => 'cccccc', 'width' => '6');
        $h2_border = array('style' => 'single', 'color' => 'cccccc', 'width' => '6');

        $extraPath = Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'word', 'handlers');
        $className = 'Catalog_Html_Handler_';
        $this->_wordConfiguration = array(
            'html-element-path-extra' => $extraPath,
            'html-element-class-prefix' => $className,
        );
        $this->_standardBorder = array('style' => 'single', 'color' => '000000', 'width' => '5');
        $doc = new WordDocDocument($this->_wordConfiguration);
        $this->_listDef = $doc->createListDefinition()
            ->createLevel()
                ->setStartNumber(1)
                ->setBulletText("%1.")
                ->getParagraphProperty()
                    ->setIndent(array('left' => 0.25, 'hanging' => 0.25))
                    ->setSpacing(0, 0.025)
                ->end()
            ->end();

        return $doc->setView('print')
            ->setPageSize(8.5, 11)
            ->setMargins(0.8, 0.5, 1.5, 0.5, 0.5, 0.5, 0)
            ->addFont('Cambria', true)
            ->addFont('Tahoma', true)
        
        // create styles
            ->createStyle('Normal', 'Normal')
                ->getRunProperty()
                    ->setFontName('Cambria')
                    ->setFontSize(11)
                    ->setFontColor('111111')
                ->end()
                ->applyTo('paragraph')
            ->end()
           /*
           */
            ->createStyle('Paragraph', 'Paragraph')
                ->getParagraphProperty()
                    ->setSpacing(0.1, 0.2)
                ->end()
                ->getRunProperty()
                    ->setFontName('Cambria')
                    ->setFontSize(11)
                    ->setFontColor('111111')
                ->end()
                ->applyTo('paragraph')
            ->end()
            // header and footer
            ->createStyle('Footer', 'Footer')
                ->getParagraphProperty()
                    ->setSpacing(0.1, 0.1)
                    ->addTabStop(7.5, 'right')
                ->end()
                ->getRunProperty()
                    ->setFontSize(8)
                    ->setFontName('Cambria')
                    ->setFontColor('555')
                ->end()
                ->setBasedOn('Normal')
            ->end()
            // page numbering
            ->createStyle('PageNumber', 'Page Number')
                ->setBasedOn('Normal')
                ->getRunProperty()
                    ->setUnderline()
                    ->setFontName('Cambria')
                    ->setFontSize(8)
                ->end()
                ->applyTo('character')
            ->end()
            // headings
            ->createStyle('Heading1', 'Heading 1')
                ->getParagraphProperty()
                    ->setAlignment(WordDocElementStyle::ALIGN_LEFT)
                    ->setSpacing(0, 0)
                ->end()
                ->getRunProperty()
                    ->setFontColor('333333')
                    ->setFontName('Cambria')
                    ->setFontSize(22)
                ->end()
                ->setBasedOn('Normal')
                ->applyTo('paragraph')
            ->end()
            ->createStyle('Heading2', 'Heading 2')
                ->getParagraphProperty()
                    ->setSpacing(0.4, 0.2)
                    ->setBorders(NULL, NULL, $h2_border, NULL)
                ->end()
                ->getRunProperty()
                    ->setFontColor('333333')
                    ->setFontSize(16)
                    ->setFontName('Cambria')
                    ->setItalics()
                ->end()
                ->setBasedOn('Normal')
                ->applyTo('paragraph')
            ->end()
            ->createStyle('Heading3', 'Heading 3')
                ->getRunProperty()
                    ->setFontColor('333333')
                    ->setBold()
                    ->setFontName('Cambria')
                    ->setFontSize(11)
                ->end()
                ->getParagraphProperty()
                    ->setSpacing(0.2, 0.1)
                ->end()
                ->setBasedOn('Normal')
                ->applyTo('paragraph')
            ->end()
            // tables
            ->createStyle('Table', 'Table')
                ->getTableProperty()
                    ->setShading('dddddd')
                ->end()
            ->end()
            ->createStyle('TableCellTopHeader', 'Table Cell - Top Header')
                ->getTableCellProperty()
                    ->setShading('dddddd')
                ->end()
                ->getRunProperty()
                    ->setFontName('Tahoma')
                    ->setFontColor('444444')
                    ->setFontSize(9)
                    ->setBold()
                ->end()
            ->end()
            ->createStyle('TableCellLeftHeader', 'Table Cell - Left Header')
                ->getTableCellProperty()
                    ->setBorders(NULL, NULL, $tableCellBorder, NULL)
                ->end()
                ->getRunProperty()
                    ->setFontName('Tahoma')
                    ->setFontColor('444444')
                    ->setItalics()
                    ->setBold()
                    ->setFontSize(9)
                ->end()
            ->end()
            ->createStyle('TableCellNormal', 'Table Cell - Normal')
                ->getTableCellProperty()     
                    ->setBorders(NULL, NULL, $tableCellBorder, NULL)         
                ->end()
                ->getRunProperty()
                    ->setFontName('Cambria')
                    ->setFontColor('111')
                    ->setFontSize(11)
                ->end()
            ->end()
            // font
            ->createStyle('Strong', 'Strong')
                ->getRunProperty()
                    ->setBold()
                ->end()
            ->end()
            ->createStyle('Underline', 'Underline')
                ->getRunProperty()
                    ->setUnderline()
                ->end()
            ->end()
            ->createStyle('Emphasis', 'Emphasis')
                ->getRunProperty()
                    ->setItalics()
                ->end()
            ->end()
            ->createStyle('Superscript', 'Superscript')
                ->getRunProperty()
                    ->setSuperscript()
                ->end()
            ->end()
            ->createStyle('Subscript', 'Subscript')
                ->getRunProperty()
                    ->setSubscript()
                ->end()
            ->end()
            ->createStyle('HyperLink', 'HyperLink')
                ->setBasedOn('Normal')
                ->applyTo('character')
                ->getRunProperty()
                    ->setFontColor('0000FF')
                    ->setUnderline()
                ->end()
            ->end()
//DOESNT SEEM TO BE WORKING                    
            // create the header / footer
            ->createFooter('odd')
                ->createParagraph('Footer')
                    ->createRun()
                        ->addText('San Francisco State University')       
                        ->addTab()
                        ->addText($data['courseNumber'] . ' - ' . str_pad($data['sectionNumber'], 2, '0', STR_PAD_LEFT))
                        ->addBreak()
                        ->addText('Class Syllabus')
                        ->addTab()
                        ->addText($data['yearSemester'])
                    ->end()
                ->end()
            ->end()
        ->end()


        // output the class header
       ->createSection()
            ->createParagraph('Heading1')
                ->addText('Class Syllabus: ' . $data['courseNumber'] . ' - ' . str_pad($data['sectionNumber'], 2, '0', STR_PAD_LEFT))
            ->end()
       ->end();
    }
    
    private function createTopMatter ($element)
    {
        $data = $this->data;
        $standardBorder = $this->_standardBorder;

        $element = $element->createSection()
            ->createParagraph('Heading2')
                  ->addText($data['fullclass'])
            ->end()
            ->createTable('Table')
                ->setColumns(2, 6)
                ->setCellMargins(0.1, 0.1)
                ->setBorders(0, 0, 0, 0)
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Class Number')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addText($data['courseNumber'] . ' - ' . str_pad($data['sectionNumber'], 2, '0', STR_PAD_LEFT))
                        ->end()
                    ->end()
                ->end()
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Class Title')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addText($data['title'])
                        ->end()
                    ->end()
                ->end()
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Class Description')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addHtml($data['description'])
                        ->end()
                    ->end()
                ->end()
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Class Instructor')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addText($data['instructor'])
                        ->end()
                    ->end()
                ->end()
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Office')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addText($data['office'])
                        ->end()
                    ->end()
                ->end()
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Office Hours')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addHtml($data['officeHours'])
                            /*
                            ;
                            
                            $splits = split("\n", $data['syllabus_office_hours']);
                            foreach($splits as $k => $v) {
                                $element = $element
                                   ->createParagraph()
                                       ->addText($v)
                                   ->end();
                            }
                            $element = $element
                            */
                         ->end()
                    ->end()
                ->end()
                ->createRow()
                    ->createCell('TableCellLeftHeader')
                        ->setWidth(2)
                        ->createParagraph('TableCellLeftHeader')
                            ->addText('Email')
                        ->end()
                    ->end()
                    ->createCell('TableCellNormal')
                        ->setWidth(6)
                        ->createParagraph('TableCellNormal')
                            ->addText($data['email'])
                        ->end()
                    ->end()
                ->end();
                
                if(!empty($data['officeNumber'])) {
                    $element = $element
                    ->createRow()
                        ->createCell('TableCellLeftHeader')
                            ->setWidth(2)
                            ->createParagraph('TableCellLeftHeader')
                                ->addText('Phone Number')
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(6)
                            ->createParagraph('TableCellNormal')
                                ->addText($data['officeNumber'])
                            ->end()
                        ->end()
                    ->end();
                }
                
                if(!empty($data['mobileNumber'])) {
                    $element = $element
                    ->createRow()
                        ->createCell('TableCellLeftHeader')
                            ->setWidth(2)
                            ->createParagraph('TableCellLeftHeader')
                                ->addText('Mobile Phone')
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(6)
                            ->createParagraph('TableCellNormal')
                                ->addText($data['mobileNumber'])
                            ->end()
                        ->end()
                    ->end();
                }
                
                // if(!empty($data['syllabus_fax'])) {
                //     $element = $element
                //     ->createRow()
                //         ->createCell('TableCellLeftHeader')
                //             ->setWidth(2)
                //             ->createParagraph('TableCellLeftHeader')
                //                 ->addText('Fax Number')
                //             ->end()
                //         ->end()
                //         ->createCell('TableCellNormal')
                //             ->setWidth(6)
                //             ->createParagraph('TableCellNormal')
                //                 ->addText(Utility::formatPhoneNumber($data['syllabus_fax'], false))
                //             ->end()
                //         ->end()
                //     ->end();
                // }
                
                if(!empty($data['website'])) {
                    $element = $element
                    ->createRow()
                        ->createCell('TableCellLeftHeader')
                            ->setWidth(2)
                            ->createParagraph('TableCellLeftHeader')
                                ->addText('Website')
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(6)
                            ->createParagraph('TableCellNormal')
                                ->addText($data['website'])
                            ->end()
                        ->end()
                    ->end();
                }
                 
                $element = $element
            ->end()
        ->end();

        foreach ($this->degrees as $degree)
        {
            $element = $this->createDegree($element, $degree);
        }
        
        return $element->end();
    }
    
    private function createDegree ($element, $degree)
    {
        $standardBorder = $this->_standardBorder;
        return $element->createRow()
            ->createCell()
                ->setWidth(3.31)
                ->setBorders(0, $standardBorder, 0, 0)
                ->createParagraph('Normal')
                    ->createRun()
                        ->addText($degree->instituteLocation)
                    ->end()
                ->end()
            ->end()
            ->createCell()
                ->setWidth(1.05)
                ->setBorders(0, $standardBorder, 0, $standardBorder)
                ->setGridSpan(2)
                ->createParagraph('Normal')
                    ->createRun()
                        ->addText($degree->degree)
                    ->end()
                ->end()
            ->end()
            ->createCell()
                ->setWidth(1)
                ->setBorders(0, $standardBorder, 0, $standardBorder)
                ->createParagraph('Normal')
                    ->setAlignment(WordDocElementStyle::ALIGN_RIGHT)
                    ->createRun()
                        ->addText($degree->years)
                    ->end()
                ->end()
            ->end()
            ->createCell()
                ->setWidth(1.05)
                ->setBorders(0, 0, 0, 0)
                ->createParagraph('Normal')
                    ->createRun()
                        ->addText($degree->field)
                    ->end()
                ->end()
            ->end()
        ->end();
    }
    
    private function startH2Section ($element, $title)
    {
        return $element->createSection()
            ->createParagraph('Heading2')
                ->createRun()
                    ->addText($title)
                ->end()
            ->end();
    }
    
    private function endH2Section ($element)
    {
        return $element->end();
    }
    
    private function startH3Section ($element, $title)
    {
        return $element->createSubSection()
        ->createParagraph('Heading3')
            ->createRun()
                ->addText($title)
            ->end()
        ->end();
    }
    
    private function endH3Section ($element)
    {
        return $element->end();
    }
    
    private function createPositionHonors ($element, $years, $description)
    {
        return $element->createParagraph('PositionsandHonors')
            ->createRun()
                ->addText($years)
            ->end()
            ->createRun()
                ->addTab()
                ->addText($description)
            ->end()
        ->end();
    }
    
    private function startPublicationList ($element)
    {
        return $element->createList($this->_listDef);
    }
    
    private function createPublication ($element, $citation)
    {
        return $element
        ->createParagraph()
            ->addHtml($citation)
        ->end();
    }
    
    private function endPublicationList ($element)
    {
        return $element->end();
    }
    
    private function createGrant ($element, $grant)
    {
        return $element->createParagraph('Grant')
            ->createRun()
                ->addText($grant->projectCode)
                ->addTab()
                ->addText(($grant->alternatePI ? $grant->alternatePI : $this->profile->lastName) . ' (PI)')
                ->addTab()
                ->addText($grant->startDate . '-' . $grant->endDate)
                ->addBreak()
                ->addText($grant->institution)
                ->addBreak()
                ->addText($grant->title)
                ->addBreak()
                ->addText($grant->goal)
                ->addBreak()
                ->addText('Role: ' . $grant->role)
            ->end()
        ->end();
    }
}
