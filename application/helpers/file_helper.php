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
