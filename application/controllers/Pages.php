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

            if (empty($_FILES['file']['name'][0])) {
                $errors['file'] = 'File is required';
            }

            if (empty($errors)) {
                $this->Training_model->insert_training([
                    'title' => $this->input->post('title'),
                    'note' => $this->input->post('notes'),
                    'name' => $_FILES['file']['name']
                ]);

                echo
                '<script>
                    alert("Training manual added successfully!");
                    window.location.href = "/";
                </script>';
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

        $data['title'] = "TRAINING MANUAL";

        $this->load->view('templates/header', $data);
        $this->load->view('pages/' . $page, array_merge($data, ['errors' => $errors]));
        $this->load->view('templates/footer', $data);
    }
}
