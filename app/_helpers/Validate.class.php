<?php

/**
 * Validation class.
 * Custom, per-variable validation methods.  All methods are listed alphabetically by variable name
 * The value for the variable is passed in by reference, so methods may handle data sanitization as well if appropriate.
 * All methods should return boolean true if validation passes, boolean false if validation fails.
 */
class Validate {
    
    /**
     * @var array Array of fields that are allowed to have HTML.  All other variables will have HTML tags stripped
     */
    private static $allow_html = array(
        'body',
        'assignment_desc',
        'post_text',
        'material_info',
        'method_text',
        'objective_text',
        'policy_text',
        'schedule_desc',
        'syllabus_class_description',
        'syllabus_class_prereqs',
        'syllabus_office_hours',
        'invite_message'
    );
    
    /**
     * @var string Allowed HTML tags for fields that allow HTML
     */
    private static $allowable_tags = '<b><strong><i><em><ul><ol><li><hr><br><p><a>';
    
    
    /**
     * Strip HTML tags from a string.  If the field is in the $allow_html array, only tags not on the $allowable_tags list are stripped
     * @param string $field The name of the field
     * @param string $val The value to be stripped (passed by reference)
     * @return NULL There is no return value, but the value is passed into the method by reference and changed as necessary
     */
    public function stripTags($field, &$val) {
        if(!in_array($field, self::$allow_html)) {
            $val = strip_tags($val);
        } else {
            $val = strip_tags($val, self::$allowable_tags);
        }
    }
    
    
    
    
    
// ==========================================
// Custom field validation methods   

    
    /**
     * Validate syllabus_id
     */
    public function syllabus_id(&$v) {
        return (preg_match('! [\w\d_-]+ !x', $v))
            ? true
            : false;
    }
    
    
    /**
     * Validate user_id
     */
    public function user_id(&$v) {
        if(!preg_match('! [\w\d_-]{3,20} !x', $v)) {
            Messages::addMessage('Invalid <strong>User ID</strong>.  User IDs must be 3-20 characters and must use only letters, numbers, underscores (_) and dashes (-)', 'error');
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Validate user email
     */
    public function user_email(&$v) {
        if(!preg_match('! .+@.+ !x', $v)) {
            Messages::addMessage('Invalid <strong>email address</strong>', 'error');
            return false;
        } else {
            return true;
        }
    }
    
    
    /**
     * Validate email addresses for invites
     */
    public function invite_addresses(&$v) {
        if(!preg_match('! (.+@.+,?)+ !x', $v)) {
            Messages::addMessage('You must provide at least one email address to send the invitation to.', 'error');
            return false;
        } else {
            return true;
        }
    }
    
}
