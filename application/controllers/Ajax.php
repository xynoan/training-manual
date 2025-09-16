<?php

class Ajax extends CI_Controller
{
    public function search()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $search = $this->input->post('search');
        $dates = $this->input->post('dates');
        $page = $this->input->post('page', true) ?: 1;

        $date_from = null;
        $date_to = null;

        if (!empty($dates)) {
            if (strpos($dates, ' - ') !== false) {
                $date_parts = explode(' - ', $dates);
                if (count($date_parts) == 2) {
                    $date_from = date('Y-m-d H:i:s', strtotime(trim($date_parts[0])));
                    $date_to = date('Y-m-d H:i:s', strtotime(trim($date_parts[1])));
                }
            } else {
                $date_from = date('Y-m-d H:i:s', strtotime($dates));
                $date_to = $date_from;
            }
        }

        $config['total_rows'] = $this->Training_model->count_all_trainings($search, $date_from, $date_to);
        $config['per_page'] = 10;
        $offset = ($page - 1) * $config['per_page'];

        $trainings = $this->Training_model->get_all_trainings_paginated($config['per_page'], $offset, $search, $date_from, $date_to);

        $total_pages = ceil($config['total_rows'] / $config['per_page']);
        $pagination = generateAjaxPagination($page, $total_pages);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'trainings' => $trainings,
                'pagination' => $pagination,
                'total_rows' => $config['total_rows'],
                'current_page' => $page,
                'total_pages' => $total_pages
            ]));
    }

    public function delete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        if (!$id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Invalid training ID'
                ]));
            return;
        }

        $this->Training_model->delete_training($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Training manual deleted successfully!'
            ]));
    }

    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $errors = [];
        $validateContentLength = $this->form->validate_content_length();
        $validateTitle = $this->form->validate_title();
        $errors = array_merge($validateContentLength, $validateTitle);

        $has_current_files = !empty($_FILES['file']['name'][0]);
        $has_temp_files = !empty($this->session->userdata('temp_files'));

        if (!$has_current_files && !$has_temp_files) {
            $errors['file'] = 'File is required';
        }

        if (!empty($errors)) {
            if ($has_current_files) {
                try {
                    handleFileUploadValidation($this);
                } catch (Exception $e) {
                    log_message('error', 'File upload validation failed: ' . $e->getMessage());
                    $errors['file'] = 'Upload failed due to memory constraints. Please try uploading fewer or smaller files.';
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'errors' => $errors,
                    'uploaded_files' => $this->session->userdata('uploaded_files') ?: []
                ]));
            return;
        }

        try {
            $files_to_save = processFileUploads($has_current_files, $has_temp_files, $this);
        } catch (Exception $e) {
            log_message('error', 'File processing failed in add(): ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Upload failed due to memory or storage issues. Please try uploading fewer or smaller files.'
                ]));
            return;
        }

        $notes = $this->input->post('notes');
        if (empty($notes)) {
            $notes = $this->session->userdata('temp_notes');
        }

        $training_id = $this->Training_model->insert_training([
            'title' => $this->input->post('title'),
            'note' => $notes,
            'name' => $files_to_save
        ]);

        _cleanup_temp_files($this);
        $this->session->unset_userdata('uploaded_files');
        $this->session->unset_userdata('temp_files');
        $this->session->unset_userdata('temp_notes');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Training manual added successfully!',
                'training_id' => $training_id
            ]));
    }

    public function save_notes()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $notes = $this->input->post('notes');

        if (!$id) {
            $this->session->set_userdata('temp_notes', $notes);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Notes saved temporarily'
                ]));
            return;
        }

        $training = $this->Training_model->get_training_by_id($id);
        if (!$training) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Training manual not found'
                ]));
            return;
        }

        $update_data = [
            'note' => $notes
        ];

        $this->Training_model->update_training($id, $update_data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Notes saved successfully!'
            ]));
    }

    public function edit()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        if (!$id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Invalid training ID'
                ]));
            return;
        }

        $errors = [];
        $validateContentLength = $this->form->validate_content_length();
        $validateTitle = $this->form->validate_title();
        $errors = array_merge($validateContentLength, $validateTitle);

        $has_current_files = !empty($_FILES['file']['name'][0]);
        $has_temp_files = !empty($this->session->userdata('temp_files'));

        if (!$has_current_files && !$has_temp_files) {
            $training = $this->Training_model->get_training_by_id($id);
            if (!$training || empty($training['file_names'])) {
                $errors['file'] = 'File is required';
            }
        }

        if (!empty($errors)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'errors' => $errors
                ]));
            return;
        }

        try {
            $files_to_save = processFileUploads($has_current_files, $has_temp_files, $this);
        } catch (Exception $e) {
            log_message('error', 'File processing failed in edit(): ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Upload failed due to memory or storage issues. Please try uploading fewer or smaller files.'
                ]));
            return;
        }

        $current_training = $this->Training_model->get_training_by_id($id);
        $existing_files = $current_training['file_names'] ?: [];

        $removed_files = [];
        if (!empty($this->input->post('removedFiles')) && $this->input->post('removedFiles') !== '[]') {
            $removed_files_data = json_decode(trim($this->input->post('removedFiles')), true);
            if (is_array($removed_files_data)) {
                $removed_files = array_map('trim', $removed_files_data);
            }
        }

        $remaining_existing_files = array_filter($existing_files, function ($file) use ($removed_files) {
            return !in_array(trim($file), $removed_files);
        });

        $final_files = array_merge($remaining_existing_files, $files_to_save);

        if (empty($final_files)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'errors' => ['file' => 'At least one file is required']
                ]));
            return;
        }

        $update_data = [
            'title' => $this->input->post('title'),
            'note' => $this->input->post('notes'),
            'name' => $final_files
        ];

        $this->Training_model->update_training($id, $update_data);
        _cleanup_temp_files($this);
        $this->session->unset_userdata('uploaded_files');
        $this->session->unset_userdata('temp_files');
        $this->session->unset_userdata('temp_notes');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Training manual updated successfully!'
            ]));
    }
}
