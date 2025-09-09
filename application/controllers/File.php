<?php

class File extends CI_Controller
{
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
}
