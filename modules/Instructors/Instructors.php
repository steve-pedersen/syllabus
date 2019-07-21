<?php

/**
 * Instructor section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Instructors_Instructors extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_instructors',
            '__pk' => ['id'],
            
            'id' => 'int',

            'instructors' => ['1:N', 'to' => 'Syllabus_Instructors_Instructor', 'reverseOf' => 'instructorsSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getInstructors ()
    {
        $instructors = [];
        foreach ($this->_fetch('instructors') as $obj)
        {
            $instructors[] = $obj;
        }    
        return !empty($instructors) ? $instructors : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        // echo "<pre>"; var_dump($data['section']['real']); die;
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->save();
            $schema = $this->getSchema('Syllabus_Instructors_Instructor');
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            foreach ($data['section']['real'] as $id => $instructor)
            {
                if ($this->isNotWhiteSpaceOnly($instructor, 'name'))
                {
                    $obj = (!is_numeric($id)) ? $schema->createInstance() : $schema->get($id);
                    $save = true;
                    if ($obj->inDatasource)
                    {
                        if ($obj->id != $id)
                        {
                            $save = false;
                        }
                    }
                    if ($save)
                    {
                        $obj->absorbData($instructor);
                        $obj->name = isset($instructor['name']) ? strip_tags(trim($instructor['name'])) : '';
                        $obj->title = isset($instructor['title']) ? strip_tags(trim($instructor['title'])) : '';
                        $obj->email = isset($instructor['email']) ? strip_tags(trim($instructor['email'])) : '';
                        $obj->credentials = isset($instructor['credentials']) ? strip_tags(trim($instructor['credentials'])) : '';
                        $obj->office = isset($instructor['office']) ? strip_tags(trim($instructor['office'])) : '';
                        $obj->website = isset($instructor['website']) ? strip_tags(trim($instructor['website'])) : '';
                        $obj->phone = isset($instructor['phone']) ? strip_tags(trim($instructor['phone'])) : '';
                        $obj->officeHours = $htmlSanitizer->sanitize(trim($instructor['officeHours']));
                        $obj->about = $htmlSanitizer->sanitize(trim($instructor['about']));
                        $obj->instructors_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any instructors with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}
