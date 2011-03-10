<?php

/**
 * Repository Model
 */
class RepositoryModel extends BaseModel {	

    /**
     * Get all the repository items
     * @param string $module Optional name of module
     * @return array Returns an array of all repository items, or repository items for a specific module if $module is specified
     */
    public function getRepositoryItems($module = NULL) {
        $items = array();
        foreach($this->all_modules as $k => $v) {
            if(is_null($module) || ($k == $module && array_key_exists($module, $this->all_modules))) {
                switch($k) {
                    case 'assignments': $this->query = "SELECT * FROM assignments WHERE syllabus_id='repository' ORDER BY assignment_title ASC;"; break;
                    case 'materials': $this->query = "SELECT * FROM materials WHERE syllabus_id='repository' ORDER BY material_title ASC;"; break;
                    case 'methods': $this->query = "SELECT * FROM methods WHERE syllabus_id='repository' ORDER BY method_title ASC;"; break;
                    case 'objectives': $this->query = "SELECT * FROM objectives WHERE syllabus_id='repository' ORDER BY objective_title ASC;"; break;
                    case 'policies': $this->query = "SELECT * FROM policies WHERE syllabus_id='repository' ORDER BY policy_title ASC;"; break;
                    case 'schedules': $this->query = "SELECT * FROM schedules WHERE syllabus_id='repository' ORDER BY schedule_date ASC;"; break;
                    case 'tas': $this->query = "SELECT * FROM teaching_assistants WHERE syllabus_id='repository' ORDER BY ta_name ASC;"; break;
                    default: break;
                }
                $result = $this->executeQuery();
                $items[$k] = $this->all_modules[$k];
                $items[$k]['items'] = ($result['count'] > 0) ? $result['data'] : array();
            }
        }
        
        return $items;
    }


    /**
     * Create a new repository item
     * @return bool Returns true on success, false otherwise
     */
    public function createRepositoryItem() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            switch($this->module_type) {
                
                case 'assignments':
                    $this->query = sprintf(
                        "INSERT INTO assignments (syllabus_id, assignment_title, assignment_desc, assignment_value) VALUES ('%s','%s','%s', '%s');",
                        'repository',
                        $this->assignment_title,
                        $this->assignment_desc,
                        $this->assignment_value
                        );
                break;
                
                case 'materials':
                    $this->query = sprintf(
                        "INSERT INTO materials (syllabus_id, material_title, material_info, material_required) VALUES ('%s','%s','%s', %d);",
                        'repository',
                        $this->material_title,
                        $this->material_info,
                        $this->material_required
                        );
                break;
                
                case 'methods':
                    $this->query = sprintf(
                        "INSERT INTO methods (syllabus_id, method_title, method_text) VALUES ('%s', '%s', '%s');",
                        'repository',
                        $this->method_title,
                        $this->method_text
                        );
                break;
                
                case 'objectives':
                    $this->query = sprintf(
                        "INSERT INTO objectives (syllabus_id, objective_title, objective_text) VALUES ('%s', '%s', '%s');",
                        'repository',
                        $this->objective_title,
                        $this->objective_text
                        );
                break;
                
                case 'policies':
                    $this->query = sprintf(
                        "INSERT INTO policies (syllabus_id, policy_title, policy_text) VALUES ('%s', '%s', '%s');",
                        'repository',
                        $this->policy_title,
                        $this->policy_text
                        );
                break;
                
                case 'schedules':
                    $this->query = sprintf(
                        "INSERT INTO schedules (syllabus_id, schedule_date, schedule_period, schedule_desc, schedule_due) VALUES ('%s', '%s', '%s', '%s', '%s');",
                        'repository',
                        $this->schedule_date,
                        $this->schedule_period,
                        $this->schedule_desc,
                        $this->schedule_due
                        );
                break;
                
                case 'tas':
                    $this->query = sprintf(
                        "INSERT INTO teaching_assistants (syllabus_id, ta_name, ta_email) VALUES ('%s', '%s', '%s');",
                        'repository',
                        $this->ta_name,
                        $this->ta_email
                        );                   
                break;
                
                default: break;		
            }
            
            if(isset($this->query)) {
                $this->executeQuery();
                $this->redirect = 'repository';
                Messages::addMessage('Repository item created.', 'success');
                $return = true;
            } else {
                Messages::addMessage('Invalid module.', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to create repository items', 'error');
            $return = false;
        }
        
        return $return;
    }


    /**
     * Edit a repository item
     * @return bool Returns true on success, false otherwise
     */
    public function editRepositoryItem() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            $error = false;
            switch($this->module_type) {
                
                case 'assignments': 
                    $this->query = sprintf(
                        "UPDATE assignments SET assignment_title='%s', assignment_desc='%s', assignment_value='%s' WHERE assignment_id=%d;",
                        $this->assignment_title,
                        $this->assignment_desc,
                        $this->assignment_value,
                        $this->item_id
                    );
                    $this->executeQuery();
                break;
                
                case 'materials':
                    $this->query = sprintf(
                        "UPDATE materials SET material_title='%s', material_info='%s', material_required=%d WHERE material_id=%d;",
                        $this->material_title,
                        $this->material_info,
                        ($this->material_required == 1) ? 1 : 0,
                        $this->item_id
                    );
                    $this->executeQuery();
                break;
                
                case 'methods':
                    $this->query = sprintf(
                        "UPDATE methods SET method_title='%s', method_text='%s' WHERE method_id=%d;",
                        $this->method_title,
                        $this->method_text,
                        $this->item_id
                    );
                    $this->executeQuery();                    
                break;
             
                case 'objectives':
                    $this->query = sprintf(
                        "UPDATE objectives SET objective_title='%s', objective_text='%s' WHERE objective_id=%d;",
                        $this->objective_title, 
                        $this->objective_text,
                        $this->item_id
                    );
                    $this->executeQuery();
                break;
             
                case 'policies':
                    $this->query = sprintf(
                        "UPDATE policies SET policy_title='%s', policy_text='%s' WHERE policy_id=%d;",
                        $this->policy_title,
                        $this->policy_text,
                        $this->item_id
                    );
                    $this->executeQuery();
                break;
                
                case 'schedules':
                    $this->query = sprintf(
                        "UPDATE schedules SET schedule_date='%s', schedule_period='%s', schedule_desc='%s', schedule_due='%s' WHERE schedule_id=%d",
                        $this->schedule_date,
                        $this->schedule_period,
                        $this->schedule_desc,
                        $this->schedule_due,
                        $this->item_id
                    );
                    $this->executeQuery();
                break;
                
                case 'tas':
                    $this->query = sprintf(
                        "UPDATE teaching_assistants SET ta_name='%s', ta_email='%s' WHERE ta_id=%d",
                        $this->ta_name,
                        $this->ta_email,
                        $this->item_id
                    );
                    $this->executeQuery();
                break;
                
                default: break;
            }
            
            if(isset($this->query)) {
                $this->executeQuery();
                $this->redirect = 'repository';
                Messages::addMessage('Repository item edited.', 'success');
                $return = true;
            } else {
                Messages::addMessage('Invalid module.', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to edit repository items', 'error');
            $return = false;
        }
        
        return $return;
    }


    /**
     * Remove an item from a syllabus
     * @return bool returns true on success, false otherwise
     */
    public function deleteRepositoryItem() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            $this->query = sprintf(
                "DELETE FROM %s WHERE %s=%d;",
                $this->all_modules[$this->module_type]['db_name'],
                $this->all_modules[$this->module_type]['db_prefix'] . '_id',
                $this->item_id
                );
            $this->executeQuery();
            $this->redirect = 'repository';
            Messages::addMessage('Repository item deleted.', 'success');
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to delete this item.', 'error');
            $return = false;
        }
        return $return;
    }



}