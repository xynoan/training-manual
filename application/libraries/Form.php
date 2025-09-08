<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form
{
    public function validate_content_length()
    {
        $errors = [];
        
        if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $displayMaxSize = ini_get('post_max_size');
            $errors['file'] = "File size exceeds the maximum allowed size of {$displayMaxSize}.";
        }
        
        return $errors;
    }
    
    public function validate_title($title = null)
    {
        $errors = [];
        
        if ($title === null) {
            $title = isset($_POST['title']) ? $_POST['title'] : '';
        }
        
        if (empty(trim($title))) {
            $errors['title'] = 'Please provide a title.';
        }
        
        return $errors;
    }
}
