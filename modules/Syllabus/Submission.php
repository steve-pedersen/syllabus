<?php

/**
 * Submission object. Maps a CourseSection to either a syllabus or uploaded file.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Submission extends Bss_ActiveRecord_Base
{
    private $_fileSrc;

    public static $StatusCodes = ['open', 'pending', 'approved', 'denied'];

    public static $StatusCodesHelpText = [
        'open' => 'This syllabus is available to be submitted.',
        'pending' => 'Your submission has been received and is pending approval.',
        'approved' => 'Your submission has been approved.',
        'denied' => 'Your submission has been denied. Please make any necessary corrections and re-submit.'
    ];

    public static $SyllabusFileTypes = [
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
        'application/vnd.ms-powerpoint', 
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text',
        'text/html',
        'image/jpeg',
        'image/gif',
        'image/png',
        'application/pdf',
        'application/rtf',
        'text/plain'
    ];

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_submissions',
            '__pk' => ['id'],
            
            'id' => 'int',   
            'status' => 'string',
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'submittedDate' => ['datetime', 'nativeName' => 'submitted_date'],
            'approvedDate' => ['datetime', 'nativeName' => 'approved_date'],
            'deleted' => 'bool',
            'log' => 'string',
            'feedback' => 'string',

            'campaign' => ['1:1', 'to' => 'Syllabus_Syllabus_SubmissionCampaign', 'keyMap' => ['campaign_id' => 'id']],
            'submittedBy' => ['1:1', 'to' => 'Bss_AuthN_Account', 'keyMap' => ['submitted_by_id' => 'id']],
            'file' => ['1:1', 'to' => 'Syllabus_Files_File', 'keyMap' => ['file_id' => 'id']],
            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
            'courseSection' => [
                '1:1', 'to' => 'Syllabus_ClassData_CourseSection', 'keyMap' => ['course_section_id' => 'id']
            ],
        ];
    }

    public function getStatusHelpText ($status)
    {
        return self::$StatusCodesHelpText[$status] ?? '';
    }

    public function getSyllabus ()
    {
        return $this->_fetch('syllabus') ?? $this->_fetch('file');
    }

    public function getOrganization ()
    {
        return $this->campaign->organization;
    }

    public function getFileSrc ($reload=false)
    {
        if (!$this->_fileSrc || $reload)
        {
            $this->_fileSrc = $this->file->getDownloadUrl();
        }
        return $this->_fileSrc;
    }

    public function getDueDateInterval ()
    {
        return $this->campaign->getDueDateInterval();
    }
}
