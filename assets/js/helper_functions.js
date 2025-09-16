function clearFormErrors() {
    $('#titleError, #fileError, #notesError').hide();
    $('#title, #notes').removeClass('is-invalid');
    $('#dropArea').removeClass('error');
}

function handleFiles(files) {
    /* error handlers */
    if (typeof existingFiles === 'undefined') existingFiles = [];
    const maxFiles = 5;
    const maxSizeMB = 100;
    const allowedTypes = ["pdf", "ppt", "pptx"];
    const totalFiles = files.length + existingFiles.length;
    if (totalFiles > maxFiles) {
        alert(`You can only upload a maximum of ${maxFiles} files total.`);
        return;
    }

    for (const file of files) {
        const ext = file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(ext)) {
            alert(`Invalid file type: "${file.name}". Only PDF and PPT files are allowed.`);
            return;
        }

        if (file.size > maxSizeMB * 1024 * 1024) {
            alert(`File "${file.name}" exceeds ${maxSizeMB} MB.`);
            return;
        }
    }
    /* end of error handlers */

    currentFiles = Array.from(files);

    if (typeof renderFileList === 'function') {
        renderFileList();
    }

    $('#dropArea').removeClass('error');
    $('#drop-area-placeholder').addClass('d-none');
}

function restoreUploadedFiles() {
    if (typeof existingFiles === 'undefined') existingFiles = [];
    if (typeof currentFiles === 'undefined') currentFiles = [];
    if (typeof removedExistingFiles === 'undefined') removedExistingFiles = [];

    existingFiles = [];
    currentFiles = [];
    removedExistingFiles = [];

    if (typeof window.removedFilesData !== 'undefined' && window.removedFilesData && window.removedFilesData.length > 0) {
        removedExistingFiles = window.removedFilesData.map(fileName => fileName.trim());
    }

    if (typeof window.existingFilesData !== 'undefined' && window.existingFilesData && window.existingFilesData.length > 0) {
        existingFiles = window.existingFilesData.filter(fileName => {
            const trimmedName = fileName && fileName.trim();
            return trimmedName && !removedExistingFiles.includes(trimmedName);
        });
    }

    if (typeof window.uploadedFilesData !== 'undefined' && window.uploadedFilesData && window.uploadedFilesData.length > 0) {
        currentFiles = window.uploadedFilesData || [];
    }

    if (typeof updateRemovedFilesInput === 'function') {
        updateRemovedFilesInput();
    }

    if (typeof renderFileList === 'function') {
        renderFileList();
    }

    const hasFiles = existingFiles.length > 0 || currentFiles.length > 0;
    if (hasFiles) {
        $('#dropArea').removeClass('error');
        $('#drop-area-placeholder').addClass('d-none');
    }
}

function clearUploadedFiles() {
    // Clear all file-related variables
    if (typeof existingFiles !== 'undefined') existingFiles = [];
    if (typeof currentFiles !== 'undefined') currentFiles = [];
    if (typeof removedExistingFiles !== 'undefined') removedExistingFiles = [];
    
    // Clear file input
    $('#fileInput').val('');
    
    // Reset removed files input
    $('#removedFiles').val('[]');
    
    // Clear file list display
    $('#fileList').empty();
    
    // Show the drop area placeholder again
    $('#drop-area-placeholder').removeClass('d-none');
    
    // Remove any error states
    $('#dropArea').removeClass('error');
    $('#fileError').hide();
}

function showFloatingAlert() {
    $('#floatingAlert').addClass('show');

    setTimeout(() => {
        $('#floatingAlert').removeClass('show');
        window.location.href = window.appConfig ? window.appConfig.baseUrl : '/';
    }, 4000);
}