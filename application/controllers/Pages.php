<?php
// TODO: refactor
class Pages extends CI_Controller
{

    public function view($page = 'home')
    {
        $errors = [];

        if (! file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        // for every form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $validateContentLength = $this->form->validate_content_length();
            $validateTitle = $this->form->validate_title();

            // merge all errors
            $errors = array_merge($validateContentLength, $validateTitle);

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
                    
                    _cleanup_temp_files($this);
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
                    _cleanup_temp_files($this);
                    $this->session->unset_userdata('uploaded_files');
                    $this->session->unset_userdata('temp_files');
                }

                if ($page === 'edit' && isset($_GET['id'])) {
                    $current_training = $this->Training_model->get_training_by_id($_GET['id']);
                    $existing_files = $current_training['file_names'] ?: [];
                    
                    $removed_files = [];
                    if (!empty($_POST['removedFiles']) && $_POST['removedFiles'] !== '[]') {
                        $removed_files_data = json_decode(trim($_POST['removedFiles']), true);
                        if (is_array($removed_files_data)) {
                            $removed_files = array_map('trim', $removed_files_data);
                        }
                    }
                    
                    $remaining_existing_files = array_filter($existing_files, function($file) use ($removed_files) {
                        return !in_array(trim($file), $removed_files);
                    });
                    
                    $final_files = array_merge($remaining_existing_files, $files_to_save);
                    
                    if (empty($final_files)) {
                        $errors['file'] = 'At least one file is required';
                    }
                    
                    if (empty($errors)) {
                        $update_data = [
                            'title' => $this->input->post('title'),
                            'note' => $this->input->post('notes'),
                            'name' => $final_files
                        ];
                        
                        $this->Training_model->update_training($_GET['id'], $update_data);
                        _cleanup_temp_files($this);
                        $this->session->unset_userdata('uploaded_files');
                        $this->session->unset_userdata('temp_files');
                    } else {
                        $data['removed_files'] = $removed_files;
                    }
                }
            }
        }

        if ($page === 'home') {
            _cleanup_temp_files($this);
            $this->session->unset_userdata('uploaded_files');
            $this->session->unset_userdata('temp_files');
            
            $search = $this->input->get('search') ?: $this->input->post('search');
            $dates = $this->input->get('dates') ?: $this->input->post('dates');
            
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
            
            $uri_segment = 1;
            $config['total_rows'] = $this->Training_model->count_all_trainings($search, $date_from, $date_to);
            $config['per_page'] = 10;
            $config['uri_segment'] = $uri_segment;
            
            $query_params = [];
            if (!empty($search)) {
                $query_params['search'] = $search;
            }
            if (!empty($dates)) {
                $query_params['dates'] = $dates;
            }
            
            if (!empty($query_params)) {
                $query_string = http_build_query($query_params);
                $config['base_url'] = base_url() . '?' . $query_string . '&page=';
                $config['page_query_string'] = TRUE;
                $config['query_string_segment'] = 'page';
            }

            $this->pagination->initialize($config);

            $page_num = ($this->uri->segment($uri_segment)) ? $this->uri->segment($uri_segment) : 1;
            // convert page number (1,2,3...) into offset (0,10,20...)
            $offset = ($page_num - 1) * $config['per_page'];

            $data['trainings'] = $this->Training_model->get_all_trainings_paginated($config['per_page'], $offset, $search, $date_from, $date_to);
            $data['pagination'] = $this->pagination->create_links();
            $data['search'] = $search;
            $data['dates'] = $dates;
            $data['date_from'] = $date_from;
            $data['date_to'] = $date_to;
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
            window.location.href = "' . base_url() . '";
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
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                if (preg_match('/^\d+\d{3}_' . preg_quote($file_name, '/') . '$/', $file)) {
                    $target_file = $upload_dir . $file;
                    break;
                }
                if (preg_match('/^\d+_' . preg_quote($file_name, '/') . '$/', $file)) {
                    $target_file = $upload_dir . $file;
                    break;
                }
            }
        }
        
        if (!$target_file) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && strpos($file, '_' . $file_name) !== false) {
                    $target_file = $upload_dir . $file;
                    break;
                }
            }
        }
        
        if (!$target_file || !file_exists($target_file)) {
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
        $mime_type = _get_mime_type($target_file);
        
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
    
    public function ajax_search()
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
        $pagination = $this->generate_ajax_pagination($page, $total_pages);

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

    public function ajax_delete()
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

    private function generate_ajax_pagination($current_page, $total_pages)
    {
        if ($total_pages <= 1) return '';

        $pagination = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        if ($current_page > 1) {
            $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="' . ($current_page - 1) . '">&laquo; Previous</a></li>';
        }
        
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        
        if ($start > 1) {
            $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="1">1</a></li>';
            if ($start > 2) {
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $current_page) ? ' active' : '';
            $pagination .= '<li class="page-item' . $active . '"><a class="page-link ajax-page" href="#" data-page="' . $i . '">' . $i . '</a></li>';
        }
        
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
        }
        
        if ($current_page < $total_pages) {
            $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="' . ($current_page + 1) . '">Next &raquo;</a></li>';
        }
        
        $pagination .= '</ul></nav>';
        
        return $pagination;
    }

    public function ajax_add()
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
                
                _cleanup_temp_files($this);
                $this->session->set_userdata('uploaded_files', $uploaded_files);
                $this->session->set_userdata('temp_files', $temp_files);
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

        $files_to_save = [];
        $upload_dir = APPPATH . '../uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if ($has_current_files) {
            $base_timestamp = time();
            for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                if (!empty($_FILES['file']['name'][$i]) && $_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
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
        
        $training_id = $this->Training_model->insert_training([
            'title' => $this->input->post('title'),
            'note' => $this->input->post('notes'),
            'name' => $files_to_save
        ]);
        
        _cleanup_temp_files($this);
        $this->session->unset_userdata('uploaded_files');
        $this->session->unset_userdata('temp_files');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Training manual added successfully!',
                'training_id' => $training_id
            ]));
    }

    public function ajax_edit()
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

        $files_to_save = [];
        $upload_dir = APPPATH . '../uploads/';
        
        if ($has_current_files) {
            $base_timestamp = time();
            for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                if (!empty($_FILES['file']['name'][$i]) && $_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
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

        $current_training = $this->Training_model->get_training_by_id($id);
        $existing_files = $current_training['file_names'] ?: [];
        
        $removed_files = [];
        if (!empty($this->input->post('removedFiles')) && $this->input->post('removedFiles') !== '[]') {
            $removed_files_data = json_decode(trim($this->input->post('removedFiles')), true);
            if (is_array($removed_files_data)) {
                $removed_files = array_map('trim', $removed_files_data);
            }
        }
        
        $remaining_existing_files = array_filter($existing_files, function($file) use ($removed_files) {
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

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Training manual updated successfully!'
            ]));
    }

}
