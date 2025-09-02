<?php
class Pages extends CI_Controller
{

    public function view($page = 'home')
    {
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

                echo '<script>alert("Training manual added successfully!"); window.location.href = "/";</script>';
            }
        }

        $data['title'] = "TRAINING MANUAL";

        $this->load->view('templates/header', $data);
        $this->load->view('pages/' . $page, array_merge($data, ['errors' => $errors]));
        $this->load->view('templates/footer', $data);
    }
}
