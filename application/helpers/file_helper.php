<?php

function _get_mime_type($file)
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

function _cleanup_temp_files($obj)
{
    $temp_files = $obj->session->userdata('temp_files');
    if ($temp_files) {
        foreach ($temp_files as $temp_file) {
            if (file_exists($temp_file['temp_path'])) {
                unlink($temp_file['temp_path']);
            }
        }
    }
}

function handleFileUploadValidation($obj)
{
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

    _cleanup_temp_files($obj);
    $obj->session->set_userdata('uploaded_files', $uploaded_files);
    $obj->session->set_userdata('temp_files', $temp_files);
}

function processFileUploads($has_current_files, $has_temp_files, $obj)
{
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
        $temp_files = $obj->session->userdata('temp_files');
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

    return $files_to_save;
}
