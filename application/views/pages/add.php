<?php require 'partials/floating-alert.php' ?>

<!-- Loading overlay -->
<div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center" style="z-index: 9999;">
    <div class="bg-white p-4 rounded text-center">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mb-0">Saving training manual...</p>
    </div>
</div>



<script>
    /* COMMENTED OUT FOR DESIGN-ONLY VERSION
    window.uploadedFilesData = <?= json_encode($uploaded_files) ?>;

    document.getElementById('addForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        clearErrors();
        
        document.getElementById('loadingOverlay').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = true;
        
        const formData = new FormData();
        formData.append('title', document.getElementById('title').value);
        formData.append('notes', document.getElementById('notes').value);
        
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('file[]', fileInput.files[i]);
            }
        }
        
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                updateUploadProgress(percentComplete);
            }
        });
        
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        alert(data.message);
                        window.location.href = '<?= base_url() ?>';
                    } else {
                        if (data.errors) {
                            showErrors(data.errors);
                        }
                        if (data.uploaded_files) {
                            window.uploadedFilesData = data.uploaded_files;
                            restoreUploadedFiles();
                        }
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    alert('An error occurred while processing the response.');
                }
            } else {
                alert('An error occurred while saving. Please try again.');
            }
            
            document.getElementById('loadingOverlay').classList.add('d-none');
            document.getElementById('submitBtn').disabled = false;
        });
        
        xhr.addEventListener('error', function() {
            console.error('Upload failed');
            alert('An error occurred while saving. Please try again.');
            
            document.getElementById('loadingOverlay').classList.add('d-none');
            document.getElementById('submitBtn').disabled = false;
        });
        
        xhr.open('POST', '<?= base_url('pages/ajax_add') ?>');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });
    
    function clearErrors() {
        document.getElementById('titleError').style.display = 'none';
        document.getElementById('notesError').style.display = 'none';
        
        document.getElementById('title').classList.remove('is-invalid');
        document.getElementById('dropArea').classList.remove('error');
        document.getElementById('notes').classList.remove('is-invalid');
    }
    
    function showErrors(errors) {
        if (errors.title) {
            document.getElementById('titleError').textContent = errors.title;
            document.getElementById('titleError').style.display = 'block';
            document.getElementById('title').classList.add('is-invalid');
        }
        
        if (errors.file) {
            document.getElementById('dropArea').classList.add('error');
        }
        
        if (errors.notes) {
            document.getElementById('notesError').textContent = errors.notes;
            document.getElementById('notesError').style.display = 'block';
            document.getElementById('notes').classList.add('is-invalid');
        }
    }
    
    function updateUploadProgress(percent) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        let progressBar = loadingOverlay.querySelector('.progress');
        
        if (!progressBar) {
            const progressContainer = document.createElement('div');
            progressContainer.className = 'mt-3 w-100';
            progressContainer.innerHTML = `
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%" 
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">Uploading files... <span class="progress-text">0%</span></small>
            `;
            
            loadingOverlay.querySelector('.bg-white').appendChild(progressContainer);
            progressBar = progressContainer.querySelector('.progress-bar');
        } else {
            progressBar = progressBar.querySelector('.progress-bar');
        }
        
        const progressText = loadingOverlay.querySelector('.progress-text');
        const roundedPercent = Math.round(percent);
        
        progressBar.style.width = roundedPercent + '%';
        progressBar.setAttribute('aria-valuenow', roundedPercent);
        
        if (progressText) {
            progressText.textContent = roundedPercent + '%';
        }
    }
    */ // END COMMENTED OUT FOR DESIGN-ONLY VERSION
</script>