<?php
/*
Plugin Name: CF Setter
Plugin URI: http://hypertext.net/projects/cfsetter/
Description: Allows you to define a custom field value from within the body of a post.
Version: 0.1
Author: Justin Blanton
Author URI: http://hypertext.net
*/

/******************************************* 
This plugin is a modification of my Slugger+
plugin, which you can find at 
http://hypertext.net/projects/spluggerplus 
*******************************************/


/* customField_getValue
* Reads in the post content, finds the custom field value you want to use and sets it as a global variable.
* @param STRING
* @return STRING
*/
function customField_getValue($post_content) {
    
    $customFieldValue = customField_findValue($post_content);
    
    if ($customFieldValue) {
        $GLOBALS['customFieldValue'] = $customFieldValue;
    }   
    
    $temp = '/(' . customField_regExEscape('[cf]') . '(.*?)' . customField_regExEscape('[/cf]') . ')/i';
    $post_content = (preg_replace($temp, '', $post_content));
    
    return $post_content;
}

/* customField_setValue
* Sets the custom field value.
* @param STRING
*/
function customField_setValue($post_id) {
    global $customFieldValue;
    // Define the custom field you want this plugin to act on
    $customField = 'linked_list_url';
    
    // Insert the custom field value, if it isn't already inserted
    if ($customFieldValue) {
        add_post_meta($post_id, $customField, $customFieldValue, true);
    }
}

/* customField_findValue
* Sifts through the post content, finds the custom field value and returns it
* @param STRING
* @return STRING
*/
function customField_findValue($text) {
    
    $cfRegEX = '/(' . customField_regExEscape('[cf]') . '(.*?)' . customField_regExEscape('[/cf]') . ')/i';
    
    preg_match_all($cfRegEX, $text, $matches);
    
    if ($matches) {
        foreach ($matches[2] as $match) {
            if ($match) {
                return $match;
            }
        }
    } else {
        // Do nothing
        return false;
    }
}

/* customField_regExEscape
* Escapes for the regular expression.
* @param STRING
* @return STRING
*/
function customField_regExEscape($str) {
    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace('/', '\\/', $str);
    $str = str_replace('[', '\\[', $str);
    $str = str_replace(']', '\\]', $str);
    
    return $str;
}

// Grab the custom field value and save to a global
add_filter('content_save_pre', 'customField_getValue');
// Insert the custom field value into the post's metadata
add_action('save_post', 'customField_setValue');
?>