<?php

function _get_memory_limit_bytes()
{
    $memory_limit = ini_get('memory_limit');
    if ($memory_limit == -1) {
        return -1; // No limit
    }
    
    $unit = strtolower(substr($memory_limit, -1));
    $value = (int) $memory_limit;
    
    switch ($unit) {
        case 'g':
            $value *= 1024 * 1024 * 1024;
            break;
        case 'm':
            $value *= 1024 * 1024;
            break;
        case 'k':
            $value *= 1024;
            break;
    }
    
    return $value;
}

function _handle_upload_error($error_code, $filename)
{
    $error_messages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds the upload_max_filesize directive',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds the MAX_FILE_SIZE directive',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    
    $message = isset($error_messages[$error_code]) 
        ? $error_messages[$error_code] 
        : 'Unknown upload error';
        
    log_message('error', "Upload error for file '{$filename}': {$message} (Code: {$error_code})");
}

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
    // Check memory usage before processing
    $memory_limit = _get_memory_limit_bytes();
    $current_memory = memory_get_usage(true);
    
    if ($memory_limit > 0 && $current_memory > ($memory_limit * 0.8)) {
        log_message('warning', 'Memory usage high before file upload: ' . $current_memory . ' bytes');
    }
    
    $uploaded_files = [];
    $temp_files = [];
    $total_size = 0;

    for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
        // Check for upload errors first
        if (!empty($_FILES['file']['name'][$i])) {
            $upload_error = $_FILES['file']['error'][$i];
            
            if ($upload_error !== UPLOAD_ERR_OK) {
                _handle_upload_error($upload_error, $_FILES['file']['name'][$i]);
                continue;
            }
            
            $file_size = $_FILES['file']['size'][$i];
            $total_size += $file_size;
            
            // Check if total size would exceed memory limit
            if ($memory_limit > 0 && ($current_memory + $total_size) > ($memory_limit * 0.9)) {
                log_message('error', 'Upload would exceed memory limit. Current: ' . $current_memory . ', File size: ' . $file_size);
                throw new Exception('Upload size too large for available memory');
            }
            
            $uploaded_files[] = [
                'name' => $_FILES['file']['name'][$i],
                'size' => $file_size,
                'type' => $_FILES['file']['type'][$i]
            ];

            $temp_filename = uniqid() . '_' . $_FILES['file']['name'][$i];
            $temp_path = sys_get_temp_dir() . '/' . $temp_filename;

            if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $temp_path)) {
                $temp_files[] = [
                    'original_name' => $_FILES['file']['name'][$i],
                    'temp_path' => $temp_path,
                    'temp_filename' => $temp_filename,
                    'size' => $file_size,
                    'type' => $_FILES['file']['type'][$i]
                ];
            }
            
            // Force garbage collection after each file
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
    }

    _cleanup_temp_files($obj);
    $obj->session->set_userdata('uploaded_files', $uploaded_files);
    $obj->session->set_userdata('temp_files', $temp_files);
    
    log_message('info', 'File upload validation completed. Memory usage: ' . memory_get_usage(true) . ' bytes');
}

function processFileUploads($has_current_files, $has_temp_files, $obj)
{
    // Monitor memory usage during processing
    $initial_memory = memory_get_usage(true);
    log_message('info', 'Starting file upload processing. Initial memory: ' . $initial_memory . ' bytes');
    
    $files_to_save = [];
    $upload_dir = APPPATH . '../uploads/';

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            log_message('error', 'Failed to create upload directory: ' . $upload_dir);
            throw new Exception('Failed to create upload directory');
        }
    }

    if ($has_current_files) {
        $base_timestamp = time();
        $file_count = count($_FILES['file']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if (!empty($_FILES['file']['name'][$i]) && $_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                // Check memory before processing each file
                $current_memory = memory_get_usage(true);
                $memory_limit = _get_memory_limit_bytes();
                
                if ($memory_limit > 0 && $current_memory > ($memory_limit * 0.85)) {
                    log_message('warning', "Memory usage high during file processing: {$current_memory} bytes");
                }
                
                $timestamp = $base_timestamp . sprintf('%03d', $i);
                $filename = $timestamp . '_' . $_FILES['file']['name'][$i];
                $filepath = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $filepath)) {
                    $files_to_save[] = $_FILES['file']['name'][$i];
                    log_message('info', "Successfully moved file: {$_FILES['file']['name'][$i]} to {$filepath}");
                } else {
                    log_message('error', "Failed to move uploaded file: {$_FILES['file']['name'][$i]}");
                }
                
                // Force garbage collection after each file
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
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
