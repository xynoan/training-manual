<?php require 'partials/floating-alert.php' ?>
<form action="<?= base_url('add') ?>" method="post" enctype="multipart/form-data">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" onclick="showFloatingAlert()">
            <i class="fas fa-save"></i>
            Submit
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
        <input type="text" class="form-control" id="title" name="title" value="<?= isset($_POST['title']) ? $_POST['title'] : '' ?>">
    </div>
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
    <label for="notes">
        <p class="fs-4 ">Notes</p>
    </label>
    <div class="w-50">
        <textarea class="form-control" id="notes" name="notes" placeholder="Optional"><?= isset($_POST['notes']) ? $_POST['notes'] : '' ?></textarea>
    </div>
    
    <input type="hidden" id="removedFiles" name="removed_files" value="">
</form>

<script>
    window.uploadedFilesData = <?= json_encode($uploaded_files) ?>;
</script>