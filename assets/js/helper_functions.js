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
        showFileAlert(`You can only upload a maximum of ${maxFiles} files total.`, 'error');
        return;
    }

    for (const file of files) {
        const ext = file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(ext)) {
            showFileAlert(`Invalid file type: "${file.name}". Only PDF and PPT files are allowed.`, 'error');
            return;
        }

        if (file.size > maxSizeMB * 1024 * 1024) {
            showFileAlert(`File "${file.name}" exceeds ${maxSizeMB} MB.`, 'error');
            return;
        }
    }
    /* end of error handlers */

    showUploadProgress();

    setTimeout(() => {
        currentFiles = Array.from(files);

        const dt = new DataTransfer();
        currentFiles.forEach(file => dt.items.add(file));
        document.getElementById('fileInput').files = dt.files;

        hideUploadProgress();

        if (typeof renderFileList === 'function') {
            renderFileList();
        }

        $('#dropArea').removeClass('error dragover uploading');
        $('#drop-area-placeholder').addClass('d-none');

        const fileCount = files.length;
        const fileText = fileCount === 1 ? 'file' : 'files';
        showFileAlert(`Successfully added ${fileCount} ${fileText}`, 'success');
    }, 800);
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
        $('#dropArea').removeClass('error').addClass('has-files');
        $('#drop-area-placeholder').addClass('d-none');
    } else {
        $('#dropArea').removeClass('has-files');
        $('#drop-area-placeholder').removeClass('d-none');
    }
}

function clearUploadedFiles() {
    if (typeof existingFiles !== 'undefined') existingFiles = [];
    if (typeof currentFiles !== 'undefined') currentFiles = [];
    if (typeof removedExistingFiles !== 'undefined') removedExistingFiles = [];
    
    $('#fileInput').val('');
    
    $('#removedFiles').val('[]');
    
    $('.file-card').each(function(index) {
        $(this).css('animation', `slideOutRight 0.3s ease-in ${index * 0.05}s forwards`);
    });
    
    setTimeout(() => {
        $('#fileList').empty();
        
        $('#drop-area-placeholder').removeClass('d-none');
        
        $('#dropArea').removeClass('error uploading dragover has-files');
        $('#fileError').hide();
        $('.file-alert').remove();
        $('#uploadProgressIndicator').removeClass('show');
    }, 500);
}

function showFloatingAlert() {
    $('#floatingAlert').addClass('show');

    setTimeout(() => {
        $('#floatingAlert').removeClass('show');
        window.location.href = window.appConfig ? window.appConfig.baseUrl : '/';
    }, 4000);
}

function showUploadProgress() {
    console.log('Showing upload progress indicator...');
    $('#dropArea').addClass('uploading');
    $('#uploadProgressIndicator').addClass('show');
    $('#drop-area-placeholder').addClass('d-none');
}

function hideUploadProgress() {
    console.log('Hiding upload progress indicator...');
    $('#dropArea').removeClass('uploading');
    $('#uploadProgressIndicator').removeClass('show');
}

function showFileAlert(message, type = 'info') {
    console.log('Showing file alert:', message, type);
    $('.file-alert').remove();
    
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                      type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} file-alert d-flex align-items-center gap-2 mt-2" style="animation: slideInDown 0.3s ease-out;">
            <i class="fas ${iconClass}"></i>
            <span>${message}</span>
        </div>
    `;
    
    $('#fileGroup').append(alertHtml);
    
    setTimeout(() => {
        $('.file-alert').fadeOut(300, function() {
            $(this).remove();
        });
    }, 4000);
}