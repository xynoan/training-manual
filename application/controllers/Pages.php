<?php
class Pages extends CI_Controller
{

    public function view($page = 'home')
    {
        // foreach($this->Training_model->get_all_trainings() as $training) {
        //     dd($training);
        // }
        // dd($this->Training_model->get_all_trainings());
        $errors = [];

        if (! file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd($_FILES);
            // dd($_POST);

            if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
                $displayMaxSize = ini_get('post_max_size');
                $errors['file'] = "File size exceeds the maximum allowed size of {$displayMaxSize}.";
            }

            if (empty(trim($_POST['title']))) {
                $errors['title'] = 'Please provide a title.';
            }

            $has_current_files = !empty($_FILES['file']['name'][0]);
            $has_temp_files = !empty($this->session->userdata('temp_files'));
            
            if ($page === 'add' && !$has_current_files && !$has_temp_files) {
                $errors['file'] = 'File is required';
            } else if ($page === 'edit' && !$has_current_files && !$has_temp_files) {
                $training = $this->Training_model->get_training_by_id($_GET['id']);
                if (!$training || empty($training['file_names'])) {
                    $errors['file'] = 'File is required';
                }
            }

            if (!empty($errors)) {
                if ($has_current_files) {
                    $uploaded_files = [];
                    $temp_files = [];
                    
                    for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                        if (!empty($_FILES['file']['name'][$i]) && $_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                            $uploaded_files[] = [
                                'name' => $_FILES['file']['name'][$i],
                                'size' => $_FILES['file']['size'][$i],
                                'type' => $_FILES['file']['type'][$i]
                            ];
                            
                            $temp_filename = uniqid() . '_' . $_FILES['file']['name'][$i];
                            $temp_path = sys_get_temp_dir() . '/' . $temp_filename;
                            
                            if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $temp_path)) {
                                $temp_files[] = [
                                    'original_name' => $_FILES['file']['name'][$i],
                                    'temp_path' => $temp_path,
                                    'temp_filename' => $temp_filename,
                                    'size' => $_FILES['file']['size'][$i],
                                    'type' => $_FILES['file']['type'][$i]
                                ];
                            }
                        }
                    }
                    
                    $this->_cleanup_temp_files();
                    $this->session->set_userdata('uploaded_files', $uploaded_files);
                    $this->session->set_userdata('temp_files', $temp_files);
                }
            }

            if (empty($errors)) {
                $files_to_save = [];
                $upload_dir = APPPATH . '../uploads/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if ($has_current_files) {
                    $base_timestamp = time();
                    for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                        if (!empty($_FILES['file']['name'][$i]) && $_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                            // Add microseconds and index to avoid filename collisions
                            $timestamp = $base_timestamp . sprintf('%03d', $i);
                            $filename = $timestamp . '_' . $_FILES['file']['name'][$i];
                            $filepath = $upload_dir . $filename;
                            
                            if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $filepath)) {
                                $files_to_save[] = $_FILES['file']['name'][$i];
                            }
                        }
                    }
                } else if ($has_temp_files) {
                    $temp_files = $this->session->userdata('temp_files');
                    if ($temp_files) {
                        $base_timestamp = time();
                        $index = 0;
                        foreach ($temp_files as $temp_file) {
                            if (file_exists($temp_file['temp_path'])) {
                                // Add index to avoid filename collisions
                                $timestamp = $base_timestamp . sprintf('%03d', $index);
                                $filename = $timestamp . '_' . $temp_file['original_name'];
                                $filepath = $upload_dir . $filename;
                                
                                if (copy($temp_file['temp_path'], $filepath)) {
                                    $files_to_save[] = $temp_file['original_name'];
                                }
                                $index++;
                            }
                        }
                    }
                }
                
                if ($page === 'add') {
                    $this->Training_model->insert_training([
                        'title' => $this->input->post('title'),
                        'note' => $this->input->post('notes'),
                        'name' => $files_to_save
                    ]);
                    $this->_cleanup_temp_files();
                    $this->session->unset_userdata('uploaded_files');
                    $this->session->unset_userdata('temp_files');
                    
                    echo
                    '<script>
                    alert("Training manual added successfully!");
                    window.location.href = "/";
                </script>';
                }

                if ($page === 'edit' && isset($_GET['id'])) {
                    $update_data = [
                        'title' => $this->input->post('title'),
                        'note' => $this->input->post('notes')
                    ];
                    
                    // Only update files if new files were uploaded
                    if (!empty($files_to_save)) {
                        $update_data['name'] = $files_to_save;
                    }
                    
                    $this->Training_model->update_training($_GET['id'], $update_data);
                    $this->_cleanup_temp_files();
                    $this->session->unset_userdata('uploaded_files');
                    $this->session->unset_userdata('temp_files');
                    
                    echo
                    '<script>
                        alert("Training manual updated successfully!");
                        window.location.href = "/";
                    </script>';
                }
            }
        }

        if ($page === 'home') {
            $uri_segment = 1;
            $config['total_rows'] = $this->Training_model->count_all_trainings();
            $config['per_page'] = 10;
            $config['uri_segment'] = $uri_segment;

            $this->pagination->initialize($config);

            $page_num = ($this->uri->segment($uri_segment)) ? $this->uri->segment($uri_segment) : 1;
            // convert page number (1,2,3...) into offset (0,10,20...)
            $offset = ($page_num - 1) * $config['per_page'];

            $data['trainings'] = $this->Training_model->get_all_trainings_paginated($config['per_page'], $offset);
            $data['pagination'] = $this->pagination->create_links();
        }

        if ($page === 'edit') {
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                show_404();
            }

            $training = $this->Training_model->get_training_by_id($_GET['id']);

            if (!$training) {
                show_404();
            }

            $data['training'] = $training;
        }

        $data['title'] = "TRAINING MANUAL";
        $data['uploaded_files'] = $this->session->userdata('uploaded_files') ?: [];

        $this->load->view('templates/header', $data);
        $this->load->view('pages/' . $page, array_merge($data, ['errors' => $errors]));
        $this->load->view('templates/footer', $data);
    }

    public function delete($id = null)
    {
        if (!$id) {
            show_404();
        }

        $this->Training_model->delete_training($id);

        echo
        '<script>
            window.location.href = "/";
        </script>';
    }

    public function preview_file($training_id = null, $file_index = null)
    {
        if (!$training_id || $file_index === null) {
            show_404();
        }

        $training = $this->Training_model->get_training_by_id($training_id);
        
        if (!$training || empty($training['file_names'])) {
            show_404();
        }

        if (!isset($training['file_names'][$file_index])) {
            show_404();
        }

        $file_name = trim($training['file_names'][$file_index]);
        
        $upload_dir = APPPATH . '../uploads/';
        $files = scandir($upload_dir);
        $target_file = null;
        
        // Create a more precise matching pattern
        // Look for files that end with exactly "_" + filename
        // Handle both old format (timestamp_filename) and new format (timestamp###_filename)
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                // Check if the file ends with exactly "_filename" (new format with index)
                if (preg_match('/^\d+\d{3}_' . preg_quote($file_name, '/') . '$/', $file)) {
                    $target_file = $upload_dir . $file;
                    break;
                }
                // Check if the file ends with exactly "_filename" (old format)
                if (preg_match('/^\d+_' . preg_quote($file_name, '/') . '$/', $file)) {
                    $target_file = $upload_dir . $file;
                    break;
                }
            }
        }
        
        // If exact match not found, try the old method as fallback
        if (!$target_file) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && strpos($file, '_' . $file_name) !== false) {
                    $target_file = $upload_dir . $file;
                    break;
                }
            }
        }
        
        if (!$target_file || !file_exists($target_file)) {
            // Debug information for troubleshooting
            if (ENVIRONMENT === 'development') {
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
                echo "Target file found: " . ($target_file ? $target_file : 'None') . "<br>";
                exit;
            }
            show_404();
        }

        $file_info = pathinfo($target_file);
        $mime_type = $this->_get_mime_type($target_file);
        
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($target_file));
        
        if (strtolower($file_info['extension']) === 'pdf') {
            header('Content-Disposition: inline; filename="' . $file_name . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }
        
        readfile($target_file);
        exit;
    }

    private function _get_mime_type($file)
    {
        $file_info = pathinfo($file);
        $extension = strtolower($file_info['extension']);
        
        $mime_types = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
        
        return isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
    }

    private function _cleanup_temp_files()
    {
        $temp_files = $this->session->userdata('temp_files');
        if ($temp_files) {
            foreach ($temp_files as $temp_file) {
                if (file_exists($temp_file['temp_path'])) {
                    unlink($temp_file['temp_path']);
                }
            }
        }
    }
}
