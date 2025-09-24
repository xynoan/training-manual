function clearFormErrors() {
    $('#titleError, #fileError, #notesError').hide().empty();
    $('#title, #notes').removeClass('is-invalid');
    $('#dropArea').removeClass('error');
}

function handleFiles(files) {
    /* error handlers */
    if (typeof window.existingFiles === 'undefined') window.existingFiles = [];
    if (typeof window.currentFiles === 'undefined') window.currentFiles = [];
    
    const maxFiles = 5;
    const maxSizeMB = 100;
    const allowedTypes = ["pdf", "ppt", "pptx"];
    const totalFiles = files.length + window.existingFiles.length;
    
    if (totalFiles > maxFiles) {
        showFileAlert(`You can only upload a maximum of ${maxFiles} files total.`, 'error');
        return;
    }

    let totalSize = 0;
    for (const file of files) {
        totalSize += file.size;
    }
    
    const totalSizeMB = totalSize / (1024 * 1024);
    if (totalSizeMB > 200) {
        showFileAlert(`Total upload size (${totalSizeMB.toFixed(1)} MB) is very large and may cause memory issues. Consider uploading fewer files.`, 'error');
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
        window.currentFiles = Array.from(files);

        const dt = new DataTransfer();
        window.currentFiles.forEach(file => dt.items.add(file));
        document.getElementById('fileInput').files = dt.files;

        hideUploadProgress();

        if (typeof window.renderFileList === 'function') {
            window.renderFileList();
        }

        $('#dropArea').removeClass('error dragover uploading');
        $('#drop-area-placeholder').addClass('d-none');

        const fileCount = files.length;
        const fileText = fileCount === 1 ? 'file' : 'files';
        showFileAlert(`Successfully added ${fileCount} ${fileText}`, 'success');
    }, 800);
}

function restoreUploadedFiles() {
    if (typeof window.existingFiles === 'undefined') window.existingFiles = [];
    if (typeof window.currentFiles === 'undefined') window.currentFiles = [];
    if (typeof window.removedExistingFiles === 'undefined') window.removedExistingFiles = [];

    window.existingFiles = [];
    window.currentFiles = [];
    window.removedExistingFiles = [];

    if (typeof window.removedFilesData !== 'undefined' && window.removedFilesData && window.removedFilesData.length > 0) {
        window.removedExistingFiles = window.removedFilesData.map(fileName => fileName.trim());
    }

    if (typeof window.existingFilesData !== 'undefined' && window.existingFilesData && window.existingFilesData.length > 0) {
        window.existingFiles = window.existingFilesData.filter(fileName => {
            const trimmedName = fileName && fileName.trim();
            return trimmedName && !window.removedExistingFiles.includes(trimmedName);
        });
    }

    if (typeof window.uploadedFilesData !== 'undefined' && window.uploadedFilesData && window.uploadedFilesData.length > 0) {
        window.currentFiles = window.uploadedFilesData || [];
    }

    if (typeof window.updateRemovedFilesInput === 'function') {
        window.updateRemovedFilesInput();
    }

    if (typeof window.renderFileList === 'function') {
        window.renderFileList();
    }

    const hasFiles = window.existingFiles.length > 0 || window.currentFiles.length > 0;
    if (hasFiles) {
        $('#dropArea').removeClass('error').addClass('has-files');
        $('#drop-area-placeholder').addClass('d-none');
    } else {
        $('#dropArea').removeClass('has-files');
        $('#drop-area-placeholder').removeClass('d-none');
    }
}

function clearUploadedFiles() {
    if (typeof window.existingFiles !== 'undefined') window.existingFiles = [];
    if (typeof window.currentFiles !== 'undefined') window.currentFiles = [];
    if (typeof window.removedExistingFiles !== 'undefined') window.removedExistingFiles = [];
    
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

function validateTitleAjax(title) {
    $('#titleError').hide().empty();
    $('#title').removeClass('is-invalid');
    
    if (!title || title.trim() === '') {
        return;
    }
    
    const baseUrl = window.appConfig ? window.appConfig.baseUrl : '';
    const ajaxUrl = baseUrl + (baseUrl.endsWith('/') ? '' : '/') + 'ajax/validate_title';
    
    $.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: { title: title },
        dataType: 'json',
        success: function(response) {
            if (!response.success && response.errors && response.errors.title) {
                $('#titleError').text(response.errors.title).show();
                $('#title').addClass('is-invalid');
            }
        },
        error: function(xhr, status, error) {
            console.log('Title validation error:', error);
        }
    });
}

function initializeTitleValidation() {
    let titleTimeout;
    
    $('#title').on('input', function() {
        const title = $(this).val();
        
        clearTimeout(titleTimeout);
        
        $('#titleError').hide().empty();
        $('#title').removeClass('is-invalid');
        
        titleTimeout = setTimeout(function() {
            validateTitleAjax(title);
        }, 500);
    });
}

function showBeautifulSuccessAlert(message, title = 'Success!') {
    $('#beautifulSuccessAlert').remove();
    
    const alertHtml = `
        <div id="beautifulSuccessAlert" class="beautiful-success-alert">
            <div class="success-icon-container">
                <div class="success-icon">
                    <svg width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                    </svg>
                </div>
            </div>
            <div class="success-content">
                <h5 class="success-title">${title}</h5>
                <p class="success-message">${message}</p>
            </div>
            <button type="button" class="success-close-btn" onclick="hideBeautifulSuccessAlert()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    setTimeout(() => {
        $('#beautifulSuccessAlert').addClass('show');
    }, 100);
    
    setTimeout(() => {
        hideBeautifulSuccessAlert();
    }, 5000);
}

function hideBeautifulSuccessAlert() {
    $('#beautifulSuccessAlert').removeClass('show');
    setTimeout(() => {
        $('#beautifulSuccessAlert').remove();
    }, 500);
}