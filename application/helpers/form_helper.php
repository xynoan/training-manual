<?php

function handleAddFormSubmission($obj)
{
    $validateContentLength = $obj->form->validate_content_length();
    $validateTitle = $obj->form->validate_title();
    $errors = array_merge($validateContentLength, $validateTitle);

    $has_current_files = !empty($_FILES['file']['name'][0]);
    $has_temp_files = !empty($obj->session->userdata('temp_files'));

    if (!$has_current_files && !$has_temp_files) {
        $errors['file'] = 'File is required';
    }

    if (!empty($errors)) {
        if ($has_current_files) {
            handleFileUploadValidation($obj);
        }
        return $errors;
    }

    $files_to_save = processFileUploads($has_current_files, $has_temp_files, $obj);

    $obj->Training_model->insert_training([
        'title' => $obj->input->post('title'),
        'note' => $obj->input->post('notes'),
        'name' => $files_to_save
    ]);

    _cleanup_session_data($obj);

    redirect(base_url());
}

function handleEditFormSubmission($obj, $id)
{
    $validateContentLength = $obj->form->validate_content_length();
    $validateTitle = $obj->form->validate_title();
    $errors = array_merge($validateContentLength, $validateTitle);

    $has_current_files = !empty($_FILES['file']['name'][0]);
    $has_temp_files = !empty($obj->session->userdata('temp_files'));

    if (!$has_current_files && !$has_temp_files) {
        $training = $obj->Training_model->get_training_by_id($id);
        if (!$training || empty($training['file_names'])) {
            $errors['file'] = 'File is required';
        }
    }

    if (!empty($errors)) {
        if ($has_current_files) {
            handleFileUploadValidation($obj);
        }
        return $errors;
    }

    $files_to_save = processFileUploads($has_current_files, $has_temp_files, $obj);

    $current_training = $obj->Training_model->get_training_by_id($id);
    $existing_files = $current_training['file_names'] ?: [];

    $removed_files = [];
    if (!empty($_POST['removedFiles']) && $_POST['removedFiles'] !== '[]') {
        $removed_files_data = json_decode(trim($_POST['removedFiles']), true);
        if (is_array($removed_files_data)) {
            $removed_files = array_map('trim', $removed_files_data);
        }
    }

    $remaining_existing_files = array_filter($existing_files, function ($file) use ($removed_files) {
        return !in_array(trim($file), $removed_files);
    });

    $final_files = array_merge($remaining_existing_files, $files_to_save);

    if (empty($final_files)) {
        return ['file' => 'At least one file is required', 'removed_files' => $removed_files];
    }

    $update_data = [
        'title' => $obj->input->post('title'),
        'note' => $obj->input->post('notes'),
        'name' => $final_files
    ];

    $obj->Training_model->update_training($id, $update_data);
    _cleanup_session_data($obj);

    redirect(base_url());
}
