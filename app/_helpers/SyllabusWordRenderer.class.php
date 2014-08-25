<?php

/**
 * This class renders the selected syllabus as a Microsoft Word Document
 */

class SyllabusWordRenderer {
    
    /**
     * @var string The syllabus id
     */
    private $syllabus_id;
    
    /**
     * @var array Modules for the syllabus
     */
    private $modules;
    
    /**
     * @var string Custom name of the current module
     */
    private $current_module_header;
    
    static $OldSemesterCodes = array(
        1 => 'Winter',
        2 => 'Spring',
        3 => 'Summer',
        4 => 'Fall',
    );

    static $NewSemesterCodes = array(
        1 => 'Winter',
        3 => 'Spring',
        5 => 'Summer',
        7 => 'Fall',
    );
    
    /**
     * Constructor
     */
    public function __construct($syllabus_id) {
		
        $this->syllabus_id = $syllabus_id;
        $this->Syllabus = new SyllabusModel();
        $this->syllabus = $this->Syllabus->getSyllabusById($syllabus_id);
		
		if(preg_match('!^draft-!', $this->syllabus['syllabus_id'])) {
			$this->syllabus['class_name'] = 'DRAFT';
			$this->syllabus['syllabus_class_year'] = 'DRAFT';
		}
//        $this->syllabus = $result_array[0];
        $this->modules = $this->Syllabus->getModulesForSyllabus($syllabus_id);
		
        
        $this->init();
        
        $this->render_general();
		
        foreach($this->modules as $k => $v) {
            $this->current_module_header = $v['module_custom_name'];
            $method_name = 'render' . ucwords(strtolower($v['module_type']));
            if(method_exists($this, $method_name)) {
                $this->{$method_name}();
            }
        }
        
        $this->output();
    }
	
    

    /**
     * Output the Word Document
     */
    private function output() {
        $class_name = $this->syllabus['syllabus_class_number'] . '-' . str_pad($this->syllabus['syllabus_class_section'], 2, '0', STR_PAD_LEFT);
        $file_name = str_replace(' ', '_', $class_name) . '_Syllabus.doc';
		
        $this->cursor->getDocument()->sendXML($file_name);
    }
    
    
    
    /**
     * Initialize the MS Word API and create necessary styles, etc.
     */
    private function init() {
        set_time_limit(30);
        require_once('_helpers/msword/Document.php');
        $h2_border = array('style' => 'single', 'color' => 'cccccc', 'width' => '6');
        $tableHeaderBorder = array('style' => 'single', 'color' => 'cccccc', 'width' => '6');
        $tableCellBorder = array('style' => 'single', 'color' => 'cccccc', 'width' => '6');
        $footerBorder = array('style' => 'single', 'color' => 'dddddd', 'width' => '6');
        
        $semesterId = trim($this->syllabus['syllabus_sem_id']);
        $length = strlen("$semesterId");
        $sem = '';

        switch ($length) {
              case 5:
                      $sem = self::$OldSemesterCodes[$this->syllabus['syllabus_class_semester']];
                      break;
              case 4:
                      $sem = self::$NewSemesterCodes[$this->syllabus['syllabus_class_semester']];
                      break;
        }
        
        $this->cursor = new WordDocDocument();
        
        $this->cursor = $this->cursor
       ->setView('print')
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
        
        
        // create the header / footer
       ->createFooter('odd')
            ->createParagraph('Footer')
               ->createRun()
                   ->addText('San Francisco State University')		
                   ->addTab()
                   ->addText($this->syllabus['syllabus_class_number'] . ' - ' . str_pad($this->syllabus['syllabus_class_section'], 2, '0', STR_PAD_LEFT))
                   ->addBreak()
                   ->addText('Class Syllabus')
                   ->addTab()
                   ->addText($sem . ' '. $this->syllabus['syllabus_class_year'])
               ->end()
            ->end()
       ->end()->end()
        
        
        // output the class header
       ->createSection()
            ->createParagraph('Heading1')
                 ->addText('Class Syllabus: ' . $this->syllabus['syllabus_class_number'] . ' - ' . str_pad($this->syllabus['syllabus_class_section'], 2, '0', STR_PAD_LEFT))
            ->end()
       ->end()
        
        // end the chaining
        ;
    }



    /**
     * Render the general information section
     */
    protected function render_general() {
        $this->cursor = $this->cursor
        // create the first page
       ->createSection()
           ->createParagraph('Heading2')
                 ->addText($this->current_module_header)
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
                           ->addText($this->syllabus['syllabus_class_number'] . ' - ' . str_pad($this->syllabus['syllabus_class_section'], 2, '0', STR_PAD_LEFT))
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
                           ->addText($this->syllabus['syllabus_class_title'])
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
                           ->addHtml($this->syllabus['syllabus_class_description'])
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
                           ->addText($this->syllabus['syllabus_instructor'])
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
                           ->addText($this->syllabus['syllabus_office'])
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
                            ->addHtml($this->syllabus['syllabus_office_hours'])
                            /*
                            ;
                            
                            $splits = split("\n", $this->syllabus['syllabus_office_hours']);
                            foreach($splits as $k => $v) {
                                $this->cursor = $this->cursor
                                   ->createParagraph()
                                       ->addText($v)
                                   ->end();
                            }
                            $this->cursor = $this->cursor
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
                           ->addText($this->syllabus['syllabus_email'])
                       ->end()
                   ->end()
               ->end();
               
                if(!empty($this->syllabus['syllabus_phone'])) {
                    $this->cursor = $this->cursor
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
                                ->addText(Utility::formatPhoneNumber($this->syllabus['syllabus_phone'], false))
                            ->end()
                        ->end()
                    ->end();
                }
               
                if(!empty($this->syllabus['syllabus_mobile'])) {
                    $this->cursor = $this->cursor
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
                                ->addText(Utility::formatPhoneNumber($this->syllabus['syllabus_mobile'], false))
                            ->end()
                        ->end()
                    ->end();
                }
               
                if(!empty($this->syllabus['syllabus_fax'])) {
                    $this->cursor = $this->cursor
                    ->createRow()
                        ->createCell('TableCellLeftHeader')
                            ->setWidth(2)
                            ->createParagraph('TableCellLeftHeader')
                                ->addText('Fax Number')
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(6)
                            ->createParagraph('TableCellNormal')
                                ->addText(Utility::formatPhoneNumber($this->syllabus['syllabus_fax'], false))
                            ->end()
                        ->end()
                    ->end();
                }
               
                if(!empty($this->syllabus['syllabus_website'])) {
                    $this->cursor = $this->cursor
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
                                ->addText($this->syllabus['syllabus_website'])
                            ->end()
                        ->end()
                    ->end();
                }
                
               $this->cursor = $this->cursor
           ->end()
       ->end()
        // end the chaining
        ;
    }


    /**
     * Render the Teaching Assistants
     */
    private function renderTas() {
        $this->cursor = $this->cursor
        ->createSection()
        ->createParagraph('Heading2')
            ->addText($this->current_module_header)
        ->end();
        
        $tas = $this->Syllabus->getTasForSyllabus($this->syllabus_id);
        if(count($tas) > 0) {
            $this->cursor = $this->cursor
                ->createTable()
                    ->setColumns(4, 4)
                    ->setCellMargins(0.1, 0.1)
                    ->setBorders(0, 0, 0, 0)
                    ->createRow()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(3)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Name')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(5)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Email Address')
                            ->end()
                        ->end()
                    ->end()
                    // end chaining
                    ;
            // loop all tas
            foreach($tas as $k => $v) {
                $this->cursor = $this->cursor
                    ->createRow()
                        ->createCell('TableCellNormal')
                            ->setWidth(3)
                            ->createParagraph('TableCellNormal')
                                ->addText($v['ta_name'])
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(5)
                            ->createParagraph('TableCellNormal')
                                ->addText($v['ta_email'])
                            ->end()
                        ->end()
                    ->end()
                ;
            } 
            // end the table 
            $this->cursor = $this->cursor
            ->end();
        }
        // end the section
        $this->cursor = $this->cursor
        ->end();
    }


    /**
     * Render the objectives
     */
    private function renderObjectives() {
        $this->cursor = $this->cursor
        ->createSection()
            ->createParagraph('Heading2')
                  ->addText($this->current_module_header)
            ->end();
        
        $objectives = $this->Syllabus->getObjectivesForSyllabus($this->syllabus_id);
        if(count($objectives) > 0) {
            // loop all objectives
            foreach($objectives as $k => $v) {
                $this->cursor = $this->cursor
                    ->createParagraph('Heading3')
                        ->addText($v['objective_title'])
                    ->end()		
                    ->addHtml($v['objective_text']);
            }
        }
        
        // end the section
        $this->cursor = $this->cursor
        ->end();
    }

    
    /**
     * Render the materials
     */
    private function renderMaterials() {
        $this->cursor = $this->cursor
        ->createSection()
        ->createParagraph('Heading2')
              ->addText($this->current_module_header)
        ->end();
        
        $materials = $this->Syllabus -> getMaterialsForSyllabus($this->syllabus_id);
        if(count($materials) > 0) {
            $this->cursor = $this->cursor
                ->createTable()
                    ->setColumns(4, 4)
                    ->setCellMargins(0.1, 0.1)
                    ->setBorders(0, 0, 0, 0)
                    ->createRow()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(2.5)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Title')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(1.5)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Required')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(4)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Notes')
                            ->end()
                        ->end()
                    ->end()
                    // end chaining
                    ;
            // loop all materials
            foreach($materials as $k => $v) {
                $material_required = ($v['material_required'] == 1) ? 'Required' : 'Optional';
                $this->cursor = $this->cursor
                    ->createRow()
                        ->createCell('TableCellNormal')
                            ->setWidth(2.5)
                            ->createParagraph('TableCellNormal')
                                ->addText($v['material_title'])
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(1.5)
                            ->createParagraph('TableCellNormal')
                                ->addText($material_required)
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(4)
                            ->createParagraph('TableCellNormal')
                                ->addHtml($v['material_info'])
                            ->end()
                        ->end()
                    ->end()
                ;
            } 
            // end the table 
            $this->cursor = $this->cursor
            ->end();
            
        }
        // end the section
        $this->cursor = $this->cursor
        ->end();
    }


    /**
     * Render the Schedules
     */
    private function renderSchedules() {
        $this->cursor = $this->cursor
        ->createSection()
        ->createParagraph('Heading2')
              ->addText($this->current_module_header)
        ->end();
        
        $schedules = $this->Syllabus->getSchedulesForSyllabus($this->syllabus_id);
        if(count($schedules) > 0) {
            $this->cursor = $this->cursor
                ->createTable()
                    ->setColumns(4, 4)
                    ->setCellMargins(0.1, 0.1)
                    ->setBorders(0, 0, 0, 0)
                    ->createRow()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(2)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Date')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(3)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Notes')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(3)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Due')
                            ->end()
                        ->end()
                    ->end()
                    // end chaining
                    ;
            // loop all schedule items
            foreach($schedules as $k => $v) {
                // set the week string
                if($v['schedule_period'] == 'w') $schedule_date = 'Week of ' . date('m/d/y', strtotime($v['schedule_date']));
                else $schedule_date = date('l', strtotime($v['schedule_date'])) . ', ' . date('m/d/y', strtotime($v['schedule_date']));
                
                $this->cursor = $this->cursor
                    ->createRow()
                        ->createCell('TableCellNormal')
                            ->setWidth(2)
                            ->createParagraph('TableCellNormal')
                                ->addText($schedule_date)
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(3)
                            ->createParagraph('TableCellNormal')
                                ->addHtml($v['schedule_desc'])
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(3)
                            ->createParagraph()
                                ->addHtml($v['schedule_due'])
                            ->end()
                        ->end()
                    ->end()
                ;
            } 
            // end the table 
            $this->cursor = $this->cursor
            ->end();
        }
        // end the section
        $this->cursor = $this->cursor
        ->end();
    }


    /**
     * Render the Assignments
     */
    private function renderAssignments() {
        $this->cursor = $this->cursor
        ->createSection()
        ->createParagraph('Heading2')
              ->addText($this->current_module_header)
        ->end();
        
        $assignments = $this->Syllabus -> getAssignmentsForSyllabus($this->syllabus_id);
        if(count($assignments) > 0) {
            $this->cursor = $this->cursor
                ->createTable()
                    ->setColumns(4, 4)
                    ->setCellMargins(0.1, 0.1)
                    ->setBorders(0, 0, 0, 0)
                    ->createRow()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(2.5)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Assignment')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(1)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Value')
                            ->end()
                        ->end()
                        ->createCell('TableCellTopHeader')
                            ->setWidth(5)
                            ->createParagraph('TableCellTopHeader')
                                ->addText('Notes')
                            ->end()
                        ->end()
                    ->end()
                    // end chaining
                    ;
                    
            // loop all assignments
            foreach($assignments as $k => $v) {
                $this->cursor = $this->cursor
                    ->createRow()
                        ->createCell('TableCellNormal')
                            ->setWidth(2.5)
                            ->createParagraph('TableCellNormal')
                                ->addText($v['assignment_title'])
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(1)
                            ->createParagraph('TableCellNormal')
                                ->addText($v['assignment_value'])
                            ->end()
                        ->end()
                        ->createCell('TableCellNormal')
                            ->setWidth(5)
                            ->createParagraph('TableCellNormal')
                                ->addHtml($v['assignment_desc'])
                            ->end()
                        ->end()
                    ->end()
                ;
            }
            // end the table 
            $this->cursor = $this->cursor
            ->end();
            
        }        
        // end the section
        $this->cursor = $this->cursor
        ->end();
    }
    

    /**
     * Render Policies
     */
    private function renderPolicies() {
        $this->cursor = $this->cursor
        ->createSection()
            ->createParagraph('Heading2')
                  ->addText($this->current_module_header)
            ->end();
        
        $policies = $this->Syllabus -> getPoliciesForSyllabus($this->syllabus_id);
        if(count($policies) > 0) {
            foreach($policies as $k => $v) {
                $this->cursor = $this->cursor
                    ->createParagraph('Heading3')
                        ->addText($v['policy_title'])
                    ->end()
                    ->addHtml($v['policy_text'])
                ->end();
            } 
        }        
        // end the section
        $this->cursor = $this->cursor
        ->end();
    }


    /**
     * Render Methods
     */
    private function renderMethods() {
	$this->cursor = $this->cursor
	->createSection()
		->createParagraph('Heading2')
			  ->addText($this->current_module_header)
		->end();

	$methods = $this->Syllabus -> getMethodsForSyllabus($this->syllabus_id);
	if(count($methods) > 0) {
		foreach($methods as $k => $v) {
			$this->cursor = $this->cursor
				->createParagraph('Heading3')
					->addText($v['method_title'])
				->end()		
    			->addHtml($v['method_text']);
		} 
	}
	// end the section
	$this->cursor = $this->cursor
	->end();
}

}