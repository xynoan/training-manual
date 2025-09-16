<?php require 'partials/floating-alert.php' ?>

<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<style>
    #quill-editor.is-invalid .ql-container {
        border-color: #dc3545;
    }
    
    #quill-editor.is-invalid .ql-toolbar {
        border-color: #dc3545;
    }
    
    .ql-editor {
        min-height: 150px;
    }
</style>

<!-- Loading overlay -->
<div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center" style="z-index: 9999;">
    <div class="bg-white p-4 rounded text-center">
        <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mb-0">Updating training manual...</p>
    </div>
</div>

<form id="editForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $training['id'] ?>">
    <input type="hidden" id="removedFiles" name="removedFiles" value="[]">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success d-flex align-items-center gap-1" id="submitBtn">
            <i class="fas fa-save"></i>
            Update
        </button>
        <a href="<?= base_url() ?>" class="btn btn-danger d-flex align-items-center gap-1">
            <i class="fas fa-arrow-left"></i>
            Main Menu
        </a>
    </div>
    </div>
    <label for="title">
        <p class="fs-4 ">Title</p>
    </label>
    <div class="w-50">
        <input type="text" class="form-control" id="title" name="title"
            value="<?= isset($_POST['title']) ? $_POST['title'] : $training['title'] ?>">
    </div>
    <div id="titleError" class="text-danger mt-2" style="display: none;"></div>
    <?php if (isset($errors['title'])) : ?>
        <p class="text-danger mt-2"><?= $errors['title'] ?></p>
    <?php endif; ?>
    <div class="w-50 my-3">
        <label class="form-label fs-4  mb-3">Upload File</label>
        <div class="<?= isset($errors['file']) ? 'upload-box error' : 'upload-box' ?>" id="dropArea">
            <div id="fileList" class="d-flex flex-wrap gap-3"></div>
            <div id="drop-area-placeholder">
                <i class="fas fa-cloud-upload-alt fs-1"></i>
                <p>Drag and Drop files here</p>
                <p class="text-muted">or click to select a file</p>
            </div>
        </div>
        <input type="file" id="fileInput" name="file[]" accept=".pdf, .pptx, .ppt" multiple />
    </div>
    <div id="fileError" class="text-danger mt-2" style="display: none;"></div>
    <?php if (isset($errors['file'])) : ?>
        <p class="text-danger mt-2"><?= $errors['file'] ?></p>
    <?php endif; ?>
    
    <label for="notes">
        <p class="fs-4 ">Notes</p>
    </label>
    <div class="w-50">
        <div id="quill-editor" style="height: 200px;"></div>
        <textarea id="notes" name="notes" style="display: none;"><?= isset($_POST['notes']) ? $_POST['notes'] : $training['note'] ?></textarea>
    </div>
    <div id="notesError" class="text-danger mt-2" style="display: none;"></div>
</form>

<!-- Quill.js JavaScript -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
    window.uploadedFilesData = <?= json_encode($uploaded_files) ?>;
    window.existingFilesData = <?= json_encode($training['file_names']) ?>;
    window.removedFilesData = <?= json_encode(isset($removed_files) ? $removed_files : []) ?>;

    // Initialize Quill editor
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Enter notes (optional)...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                ['link'],
                ['clean']
            ]
        }
    });

    // Set initial content from the hidden textarea
    var initialContent = document.getElementById('notes').value;
    if (initialContent) {
        quill.root.innerHTML = initialContent;
    }

    // Update hidden textarea when Quill content changes
    quill.on('text-change', function() {
        document.getElementById('notes').value = quill.root.innerHTML;
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Sync Quill content to hidden textarea before submitting
        document.getElementById('notes').value = quill.root.innerHTML;
        
        clearErrors();
        
        document.getElementById('loadingOverlay').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = true;
        
        const formData = new FormData();
        formData.append('id', document.querySelector('input[name="id"]').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('notes', document.getElementById('notes').value);
        formData.append('removedFiles', document.getElementById('removedFiles').value);
        
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
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    alert('An error occurred while processing the response.');
                }
            } else {
                alert('An error occurred while updating. Please try again.');
            }
            
            document.getElementById('loadingOverlay').classList.add('d-none');
            document.getElementById('submitBtn').disabled = false;
        });
        
        xhr.addEventListener('error', function() {
            console.error('Update failed');
            alert('An error occurred while updating. Please try again.');
            
            document.getElementById('loadingOverlay').classList.add('d-none');
            document.getElementById('submitBtn').disabled = false;
        });
        
        xhr.open('POST', '<?= base_url('ajax/edit') ?>');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });
    
    function clearErrors() {
        document.getElementById('titleError').style.display = 'none';
        document.getElementById('fileError').style.display = 'none';
        document.getElementById('notesError').style.display = 'none';
        
        document.getElementById('title').classList.remove('is-invalid');
        document.getElementById('dropArea').classList.remove('error');
        document.getElementById('quill-editor').classList.remove('is-invalid');
    }
    
    function showErrors(errors) {
        if (errors.title) {
            document.getElementById('titleError').textContent = errors.title;
            document.getElementById('titleError').style.display = 'block';
            document.getElementById('title').classList.add('is-invalid');
        }
        
        if (errors.file) {
            document.getElementById('fileError').textContent = errors.file;
            document.getElementById('fileError').style.display = 'block';
            document.getElementById('dropArea').classList.add('error');
        }
        
        if (errors.notes) {
            document.getElementById('notesError').textContent = errors.notes;
            document.getElementById('notesError').style.display = 'block';
            document.getElementById('quill-editor').classList.add('is-invalid');
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
</script>