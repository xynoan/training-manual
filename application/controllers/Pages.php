<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller
{
    private $upload_config = [
        'upload_path' => './uploads/',
        'allowed_types' => 'pdf|ppt|pptx',
        'max_size' => 102400, // 100MB
        'encrypt_name' => FALSE
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main view handler for different pages
     */
    public function view($page = 'home')
    {
        if (!$this->_page_exists($page)) {
            show_404();
        }

        $data = $this->_prepare_base_data();
        $errors = [];

        if ($this->input->method() === 'post') {
            $errors = $this->_handle_form_submission($page);
            if (empty($errors)) {
                $this->_redirect_with_success($page);
                return;
            }
        }

        // Load page-specific data
        $data = array_merge($data, $this->_load_page_data($page));
        $data['errors'] = $errors;

        $this->_render_page($page, $data);
    }

    /**
     * Delete a training manual
     */
    public function delete($id = null)
    {
        if (!$id || !$this->Training_model->get_training_by_id($id)) {
            show_404();
        }

        $this->Training_model->delete_training($id);
        redirect('/');
    }

    /**
     * Preview/download a training file
     */
    public function preview_file($training_id = null, $file_index = null)
    {
        if (!$training_id || $file_index === null) {
            show_404();
        }

        $training = $this->Training_model->get_training_by_id($training_id);
        if (!$training || empty($training['file_names']) || !isset($training['file_names'][$file_index])) {
            show_404();
        }

        $file_name = trim($training['file_names'][$file_index]);
        $file_path = $this->_find_uploaded_file($file_name);

        if (!$file_path) {
            $this->_debug_file_not_found($training_id, $file_index, $file_name);
            show_404();
        }

        $this->_serve_file($file_path, $file_name);
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Check if page template exists
     */
    private function _page_exists($page)
    {
        return file_exists(APPPATH . 'views/pages/' . $page . '.php');
    }

    /**
     * Prepare base data for all pages
     */
    private function _prepare_base_data()
    {
        return [
            'title' => 'TRAINING MANUAL',
            'uploaded_files' => $this->session->userdata('uploaded_files') ?: []
        ];
    }

    /**
     * Handle form submissions for add/edit pages
     */
    private function _handle_form_submission($page)
    {
        $errors = $this->_validate_form_data($page);

        if (!empty($errors)) {
            $this->_handle_validation_errors();
            return $errors;
        }

        // Process successful submission
        $files_to_save = $this->_process_file_uploads();
        $this->_save_training_data($page, $files_to_save);
        $this->_cleanup_session_data();

        return [];
    }

    /**
     * Validate form data
     */
    private function _validate_form_data($page)
    {
        $errors = [];

        // Check for POST size limit exceeded
        if ($this->_is_post_size_exceeded()) {
            $max_size = ini_get('post_max_size');
            $errors['file'] = "File size exceeds the maximum allowed size of {$max_size}.";
        }

        // Validate title
        if (empty(trim($this->input->post('title')))) {
            $errors['title'] = 'Please provide a title.';
        }

        // Validate file requirements
        $file_errors = $this->_validate_file_requirements($page);
        if ($file_errors) {
            $errors['file'] = $file_errors;
        }

        return $errors;
    }

    /**
     * Check if POST size limit was exceeded
     */
    private function _is_post_size_exceeded()
    {
        return empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0;
    }

    /**
     * Validate file upload requirements based on page type
     */
    private function _validate_file_requirements($page)
    {
        $has_current_files = !empty($_FILES['file']['name'][0]);
        $has_temp_files = !empty($this->session->userdata('temp_files'));

        if ($page === 'add' && !$has_current_files && !$has_temp_files) {
            return 'File is required';
        }

        if ($page === 'edit' && !$has_current_files && !$has_temp_files) {
            $training_id = $this->input->get('id');
            $training = $this->Training_model->get_training_by_id($training_id);
            if (!$training || empty($training['file_names'])) {
                return 'File is required';
            }
        }

        return null;
    }

    /**
     * Handle validation errors by storing files temporarily
     */
    private function _handle_validation_errors()
    {
        if (!empty($_FILES['file']['name'][0])) {
            $this->_store_files_temporarily();
        }
    }

    /**
     * Store uploaded files temporarily for form re-display
     */
    private function _store_files_temporarily()
    {
        $uploaded_files = [];
        $temp_files = [];

        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if ($this->_is_valid_upload($i)) {
                $file_data = $this->_get_file_data($i);
                $uploaded_files[] = $file_data;

                $temp_file = $this->_move_to_temp_storage($i, $file_data['name']);
                if ($temp_file) {
                    $temp_files[] = $temp_file;
                }
            }
        }

        _cleanup_temp_files($this);
        $this->session->set_userdata([
            'uploaded_files' => $uploaded_files,
            'temp_files' => $temp_files
        ]);
    }

    /**
     * Check if upload at index is valid
     */
    private function _is_valid_upload($index)
    {
        return !empty($_FILES['file']['name'][$index]) && 
               $_FILES['file']['error'][$index] === UPLOAD_ERR_OK;
    }

    /**
     * Get file data for uploaded file
     */
    private function _get_file_data($index)
    {
        return [
            'name' => $_FILES['file']['name'][$index],
            'size' => $_FILES['file']['size'][$index],
            'type' => $_FILES['file']['type'][$index]
        ];
    }

    /**
     * Move uploaded file to temporary storage
     */
    private function _move_to_temp_storage($index, $original_name)
    {
        $temp_filename = uniqid() . '_' . $original_name;
        $temp_path = sys_get_temp_dir() . '/' . $temp_filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'][$index], $temp_path)) {
            return [
                'original_name' => $original_name,
                'temp_path' => $temp_path,
                'temp_filename' => $temp_filename,
                'size' => $_FILES['file']['size'][$index],
                'type' => $_FILES['file']['type'][$index]
            ];
        }

        return null;
    }

    /**
     * Process file uploads and return files to save
     */
    private function _process_file_uploads()
    {
        $upload_dir = $this->_ensure_upload_directory();
        $has_current_files = !empty($_FILES['file']['name'][0]);
        $has_temp_files = !empty($this->session->userdata('temp_files'));

        if ($has_current_files) {
            return $this->_process_current_uploads($upload_dir);
        } elseif ($has_temp_files) {
            return $this->_process_temp_files($upload_dir);
        }

        return [];
    }

    /**
     * Ensure upload directory exists
     */
    private function _ensure_upload_directory()
    {
        $upload_dir = APPPATH . '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        return $upload_dir;
    }

    /**
     * Process current file uploads
     */
    private function _process_current_uploads($upload_dir)
    {
        $files_to_save = [];
        $base_timestamp = time();

        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if ($this->_is_valid_upload($i)) {
                $filename = $this->_generate_filename($base_timestamp, $i, $_FILES['file']['name'][$i]);
                $filepath = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $filepath)) {
                    $files_to_save[] = $_FILES['file']['name'][$i];
                }
            }
        }

        return $files_to_save;
    }

    /**
     * Process temporary files
     */
    private function _process_temp_files($upload_dir)
    {
        $temp_files = $this->session->userdata('temp_files');
        if (!$temp_files) {
            return [];
        }

        $files_to_save = [];
        $base_timestamp = time();
        $index = 0;

        foreach ($temp_files as $temp_file) {
            if (file_exists($temp_file['temp_path'])) {
                $filename = $this->_generate_filename($base_timestamp, $index, $temp_file['original_name']);
                $filepath = $upload_dir . $filename;

                if (copy($temp_file['temp_path'], $filepath)) {
                    $files_to_save[] = $temp_file['original_name'];
                }
                $index++;
            }
        }

        return $files_to_save;
    }

    /**
     * Generate unique filename with timestamp
     */
    private function _generate_filename($base_timestamp, $index, $original_name)
    {
        $timestamp = $base_timestamp . sprintf('%03d', $index);
        return $timestamp . '_' . $original_name;
    }

    /**
     * Save training data to database
     */
    private function _save_training_data($page, $files_to_save)
    {
        $data = [
            'title' => $this->input->post('title'),
            'note' => $this->input->post('notes')
        ];

        if ($page === 'add') {
            $data['name'] = $files_to_save;
            $this->Training_model->insert_training($data);
        } elseif ($page === 'edit') {
            $training_id = $this->input->get('id');
            if (!empty($files_to_save)) {
                $data['name'] = $files_to_save;
            }
            $this->Training_model->update_training($training_id, $data);
        }
    }

    /**
     * Clean up session data
     */
    private function _cleanup_session_data()
    {
        _cleanup_temp_files($this);
        $this->session->unset_userdata(['uploaded_files', 'temp_files']);
    }

    /**
     * Redirect with success message
     */
    private function _redirect_with_success($page)
    {
        $message = ($page === 'add') ? 'Training manual added successfully!' : 'Training manual updated successfully!';
        $this->session->set_flashdata('success', $message);
        redirect('/');
    }

    /**
     * Load page-specific data
     */
    private function _load_page_data($page)
    {
        switch ($page) {
            case 'home':
                return $this->_load_home_data();
            case 'edit':
                return $this->_load_edit_data();
            default:
                return [];
        }
    }

    /**
     * Load data for home page (pagination)
     */
    private function _load_home_data()
    {
        $config = [
            'total_rows' => $this->Training_model->count_all_trainings(),
            'per_page' => 10,
            'uri_segment' => 1
        ];

        $this->pagination->initialize($config);

        $page_num = $this->uri->segment(1) ?: 1;
        $offset = ($page_num - 1) * $config['per_page'];

        return [
            'trainings' => $this->Training_model->get_all_trainings_paginated($config['per_page'], $offset),
            'pagination' => $this->pagination->create_links()
        ];
    }

    /**
     * Load data for edit page
     */
    private function _load_edit_data()
    {
        $training_id = $this->input->get('id');
        if (!$training_id) {
            show_404();
        }

        $training = $this->Training_model->get_training_by_id($training_id);
        if (!$training) {
            show_404();
        }

        return ['training' => $training];
    }

    /**
     * Render the page with header and footer
     */
    private function _render_page($page, $data)
    {
        $this->load->view('templates/header', $data);
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Find uploaded file by name
     */
    private function _find_uploaded_file($file_name)
    {
        $upload_dir = APPPATH . '../uploads/';
        $files = scandir($upload_dir);

        // Try different filename patterns
        $patterns = [
            '/^\d+\d{3}_' . preg_quote($file_name, '/') . '$/',
            '/^\d+_' . preg_quote($file_name, '/') . '$/'
        ];

        foreach ($patterns as $pattern) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && preg_match($pattern, $file)) {
                    return $upload_dir . $file;
                }
            }
        }

        // Fallback: partial match
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && strpos($file, '_' . $file_name) !== false) {
                return $upload_dir . $file;
            }
        }

        return null;
    }

    /**
     * Debug information when file is not found
     */
    private function _debug_file_not_found($training_id, $file_index, $file_name)
    {
        if (ENVIRONMENT !== 'development') {
            return;
        }

        $upload_dir = APPPATH . '../uploads/';
        $files = scandir($upload_dir);

        echo "Debug Info:<br>";
        echo "Training ID: " . $training_id . "<br>";
        echo "File Index: " . $file_index . "<br>";
        echo "Requested File Name: " . $file_name . "<br>";
        echo "Available files in uploads:<br>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "- " . $file . "<br>";
            }
        }
        exit;
    }

    /**
     * Serve file to browser
     */
    private function _serve_file($file_path, $file_name)
    {
        if (!file_exists($file_path)) {
            show_404();
        }

        $file_info = pathinfo($file_path);
        $mime_type = _get_mime_type($file_path);

        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($file_path));

        if (strtolower($file_info['extension']) === 'pdf') {
            header('Content-Disposition: inline; filename="' . $file_name . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }

        readfile($file_path);
        exit;
    }
}
