<div id="formSection" style="display: none;">
    <form id="mainForm" enctype="multipart/form-data" onsubmit="return false;">
        <input type="hidden" name="id" value="">
        <input type="hidden" id="removedFiles" name="removedFiles" value="[]">
        <div class="row mb-3">
            <h1 class="fw-bold text-center text-sm-start col-12 col-sm-8 col-lg-6"><?php echo isset($title) ? $title : 'Training Manual'; ?></h1>
            <div class="gap-2 col-12 col-sm-4 col-lg-6 d-sm-flex justify-content-sm-end align-items-sm-initial align-items-md-center">
                <button type="button" class="btn btn-primary gap-1 responsiveFormGroup w-md-auto" id="submitBtn">
                    <i class="fas fa-save"></i>
                    <span id="submitBtnText">Save</span>
                </button>
                <button type="button" class="btn btn-danger gap-1 responsiveFormGroup" id="mainMenuBtn">
                    <i class="fas fa-arrow-left"></i>
                    Main Menu
                </button>
            </div>
        </div>

        <label for="title">
            <p class="fs-4">Title</p>
        </label>
        <div class="responsiveFormGroup" id="titleGroup">
            <input type="text" class="form-control" id="title" name="title" value="<?= isset($_POST['title']) ? $_POST['title'] : '' ?>" placeholder="Enter training manual title" required>
        </div>
        <div id="titleError" class="text-danger mt-2" style="display: none;"></div>
        <?php if (isset($errors['title'])) : ?>
            <p class="text-danger mt-2"><?= $errors['title'] ?></p>
        <?php endif; ?>

        <div class="my-3 responsiveFormGroup" id="fileGroup">
            <label class="form-label fs-4 mb-3">Upload File</label>
            <div class="<?= isset($errors['file']) ? 'upload-box error' : 'upload-box' ?>" id="dropArea">
                <div id="fileList" class="d-flex flex-wrap gap-3"></div>
                <div id="drop-area-placeholder">
                    <i class="fas fa-cloud-upload-alt fs-1"></i>
                    <p>Drag and Drop files here</p>
                    <p class="text-muted">or click to select a file</p>
                </div>
            </div>
            <input type="file" id="fileInput" name="file[]" accept=".pdf, .pptx, .ppt" multiple/>
        </div>
        <div id="fileError" class="text-danger mt-2" style="display: none;"></div>
        <?php if (isset($errors['file'])) : ?>
            <p class="text-danger mt-2"><?= $errors['file'] ?></p>
        <?php endif; ?>

        <label for="notes">
            <p class="fs-4">Notes</p>
        </label>
        <div class="responsiveFormGroup h-25" id="notesGroup">
            <div id="editor">
                <p><br /></p>
            </div>
            <!-- <textarea class="form-control" id="notes" name="notes" placeholder="Optional" readonly><?= isset($_POST['notes']) ? $_POST['notes'] : '' ?></textarea> -->
        </div>
        <div id="notesError" class="text-danger mt-2" style="display: none;"></div>
    </form>
</div>