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
                
                // Create uploads directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if ($has_current_files) {
                    // Handle direct file uploads
                    for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                        if (!empty($_FILES['file']['name'][$i]) && $_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                            $filename = time() . '_' . $_FILES['file']['name'][$i];
                            $filepath = $upload_dir . $filename;
                            
                            if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $filepath)) {
                                $files_to_save[] = $_FILES['file']['name'][$i];
                            }
                        }
                    }
                } else if ($has_temp_files) {
                    // Handle files from temp storage
                    $temp_files = $this->session->userdata('temp_files');
                    if ($temp_files) {
                        foreach ($temp_files as $temp_file) {
                            if (file_exists($temp_file['temp_path'])) {
                                $filename = time() . '_' . $temp_file['original_name'];
                                $filepath = $upload_dir . $filename;
                                
                                if (copy($temp_file['temp_path'], $filepath)) {
                                    $files_to_save[] = $temp_file['original_name'];
                                }
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
                    // Clean up temp files after successful save
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
                    $this->Training_model->update_training($_GET['id'], [
                        'title' => $this->input->post('title'),
                        'note' => $this->input->post('notes'),
                        'name' => $files_to_save
                    ]);
                    // Clean up temp files after successful save
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
