<html>

<head>
    <title>Training Manual</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<style>
    .dropdown-menu a:active {
        background-color: white;
    }

    /* Common styles for forms */
    textarea::placeholder {
        opacity: 0.7 !important;
    }

    /* Floating Success Alert */
    .success-alert-floating {
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #00b09b, #96c93d);
        color: white;
        padding: 20px 25px;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 176, 155, 0.3);
        transform: translateX(400px);
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 1000;
        max-width: 350px;
    }

    .success-alert-floating.show {
        transform: translateX(0);
    }

    .success-alert-floating .success-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        animation: pulse 2s infinite;
    }

    /* Upload box styles */
    .upload-box {
        border: 2px dashed #d3d3d3;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .upload-box.error {
        border: 2px dashed #e74c3c;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        background: #fff5f5;
        cursor: pointer;
        transition: border-color 0.3s ease, background 0.3s ease;
    }

    .upload-box.error::after {
        content: "⚠ No file uploaded!";
        display: block;
        color: #e74c3c;
        font-size: 14px;
        margin-top: 10px;
        font-weight: bold;
    }

    .upload-box.dragover {
        border-color: #0d6efd;
        background: #f0f8ff;
    }

    .upload-box svg {
        width: 50px;
        height: 50px;
        fill: #adb5bd;
    }

    .upload-box svg.removeFile {
        width: 25px;
        height: 25px;
        fill: white;
    }

    .upload-box p {
        margin-top: 10px;
        color: #adb5bd;
        font-weight: 500;
    }

    #fileInput {
        display: none;
    }

    #fileList .file-box {
        width: 150px;
        word-break: break-word;
        background: #f8f9fa;
    }

    /* File icon styles */
    .file-preview-link {
        text-decoration: none !important;
        display: inline-block;
        margin-right: 8px;
        font-size: 18px;
        transition: transform 0.2s ease;
    }

    .file-preview-link:hover {
        transform: scale(1.1);
    }

    .file-preview-link i {
        font-size: 20px;
    }
</style>

<body>
    <div class="container mt-5">
        <div id="formSection" style="display: none;">
            <form id="mainForm" enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" name="id" value="">
                <input type="hidden" id="removedFiles" name="removedFiles" value="[]">

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" id="submitBtn" disabled>
                        <i class="fas fa-save"></i>
                        <span id="submitBtnText">Save</span>
                    </button>
                    <button type="button" class="btn btn-danger d-flex align-items-center gap-1" id="mainMenuBtn">
                        <i class="fas fa-arrow-left"></i>
                        Main Menu
                    </button>
                </div>

                <label for="title">
                    <p class="fs-4">Title</p>
                </label>
                <div class="w-50">
                    <input type="text" class="form-control" id="title" name="title" value="<?= isset($_POST['title']) ? $_POST['title'] : '' ?>" readonly>
                </div>
                <div id="titleError" class="text-danger mt-2" style="display: none;"></div>
                <?php if (isset($errors['title'])) : ?>
                    <p class="text-danger mt-2"><?= $errors['title'] ?></p>
                <?php endif; ?>

                <div class="w-50 my-3">
                    <label class="form-label fs-4 mb-3">Upload File</label>
                    <div class="<?= isset($errors['file']) ? 'upload-box error' : 'upload-box' ?>" id="dropArea" style="pointer-events: none; opacity: 0.7;">
                        <div id="fileList" class="d-flex flex-wrap gap-3"></div>
                        <div id="drop-area-placeholder">
                            <i class="fas fa-cloud-upload-alt fs-1"></i>
                            <p>Drag and Drop files here</p>
                            <p class="text-muted">or click to select a file</p>
                        </div>
                    </div>
                    <input type="file" id="fileInput" name="file[]" accept=".pdf, .pptx, .ppt" multiple disabled />
                </div>
                <div id="fileError" class="text-danger mt-2" style="display: none;"></div>
                <?php if (isset($errors['file'])) : ?>
                    <p class="text-danger mt-2"><?= $errors['file'] ?></p>
                <?php endif; ?>

                <label for="notes">
                    <p class="fs-4">Notes</p>
                </label>
                <div class="w-50">
                    <textarea class="form-control" id="notes" name="notes" placeholder="Optional" readonly><?= isset($_POST['notes']) ? $_POST['notes'] : '' ?></textarea>
                </div>
                <div id="notesError" class="text-danger mt-2" style="display: none;"></div>
            </form>
        </div>

        <div id="dashboardSection">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="fw-bold"><?php echo isset($title) ? $title : 'Training Manual'; ?></h1>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2" id="addBtn">
                    <i class="fas fa-plus-circle"></i>
                    Add
                </button>
            </div>
            <div class="mb-4">
                <form id="searchForm" class="d-flex justify-content-start gap-3 flex-wrap align-items-end" onsubmit="return false;">
                    <div>
                        <label for="dates" class="form-label small text-muted mb-1">Date Range</label>
                        <input type="text" class="form-control" id="dates" name="dates"
                            title="Filter from this date"
                            placeholder="Select date range"
                            value="<?= isset($dates) ? htmlspecialchars($dates) : '' ?>" readonly>
                    </div>
                    <div>
                        <label for="search" class="form-label small text-muted mb-1">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Search by title, notes, or filename..."
                            value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" readonly>
                    </div>
                    <div>
                        <label class="form-label small text-muted mb-1">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="button" id="filterBtn" disabled>
                                <i class="fas fa-filter"></i>
                                Filter
                            </button>
                            <button class="btn btn-outline-danger d-flex justify-content-center align-items-center gap-1 d-none"
                                type="button" id="clearBtn" title="Clear all filters" disabled>
                                <i class="fas fa-times-circle"></i>
                                Clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php require 'partials/loading-indicator.php' ?>

            <?php require 'partials/filter-info.php' ?>

            <div id="mainContent">
                <?php if (isset($trainings) && !empty($trainings)): ?>
                    <div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 15%;">Title</th>
                                    <th scope="col">File(s)</th>
                                    <th scope="col">Uploaded by</th>
                                    <th scope="col">Uploaded at</th>
                                    <th scope="col">Notes</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trainings as $training): ?>
                                    <?php $uploaded_at = isset($training['created_at']) ? date_format(date_create($training['created_at']), "d/m/Y H:i") : '' ?>
                                    <tr>
                                        <td class="align-middle"><?= isset($training['title']) ? htmlspecialchars($training['title']) : '' ?></td>
                                        <td class="align-middle">
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <?php if (isset($training['file_names']) && is_array($training['file_names'])): ?>
                                                    <?php foreach ($training['file_names'] as $index => $file_name): ?>
                                                        <a class="link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover file-preview-link d-inline-flex align-items-center"
                                                            href="#"
                                                            data-training-id="<?= isset($training['id']) ? $training['id'] : '' ?>"
                                                            data-file-index="<?= $index ?>"
                                                            data-file-name="<?= htmlspecialchars($file_name) ?>"
                                                            data-file-extension="<?= strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) ?>"
                                                            title="<?= htmlspecialchars($file_name) ?>"
                                                            style="font-size: 1.25rem;">
                                                            <?php if (function_exists('get_file_icon')): ?><?= get_file_icon(pathinfo($file_name, PATHINFO_EXTENSION)) ?><?php else: ?><i class="fas fa-file"></i><?php endif; ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="align-middle">Nath</td>
                                        <td class="align-middle"><?= isset($uploaded_at) ? $uploaded_at : '' ?></td>
                                        <td class="align-middle"><?= isset($training['note']) ? htmlspecialchars($training['note']) : '' ?></td>
                                        <td class="align-middle">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-danger btn-sm dropdown-toggle rounded" disabled>
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="d-flex align-items-center gap-2 dropdown-item text-primary" href="#">
                                                            <i class="fas fa-edit"></i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="d-flex align-items-center gap-2 dropdown-item text-danger ajax-delete"
                                                            href="#" data-id="<?= isset($training['id']) ? $training['id'] : '' ?>">
                                                            <i class="fas fa-trash"></i>
                                                            Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <?php if (!isset($trainings) || empty($trainings)): ?>
                    <div class="text-center">
                        <p class="fs-4 text-muted">No training manuals found.</p>
                        <p>Try adjusting your search terms or date range.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div id="paginationContainer">
                <?= isset($pagination) ? $pagination : '' ?>
            </div>
        </div>

        <?php require 'partials/file-preview-modal.php' ?>

        <?php require 'partials/hover-preview-tooltip.php' ?>

        <?php require 'partials/confirmation-modal.php' ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script>
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

                const dropArea = document.getElementById("dropArea");
                const dropAreaPlaceholder = document.getElementById("drop-area-placeholder");

                if (dropArea && dropArea.classList.contains("error")) {
                    dropArea.classList.remove("error");
                }

                if (dropAreaPlaceholder) {
                    dropAreaPlaceholder.classList.add("d-none");
                }
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
                    if (dropArea.classList.contains("error")) {
                        dropArea.classList.remove("error");
                    }
                    dropAreaPlaceholder.classList.add("d-none");
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                restoreUploadedFiles();
            });

            function showFloatingAlert() {
                const alert = document.getElementById('floatingAlert');
                alert.classList.add('show');

                setTimeout(() => {
                    alert.classList.remove('show');
                    window.location.href = '<?= isset($base_url) ? $base_url : '/' ?>';
                }, 4000);
            }

            document.addEventListener('DOMContentLoaded', function() {
                const dashboardSection = document.getElementById('dashboardSection');
                const formSection = document.getElementById('formSection');
                const addBtn = document.getElementById('addBtn');
                const mainMenuBtn = document.getElementById('mainMenuBtn');
                const submitBtn = document.getElementById('submitBtn');
                const submitBtnText = document.getElementById('submitBtnText');
                const mainForm = document.getElementById('mainForm');

                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    mainForm.reset();
                    document.querySelector('input[name="id"]').value = '';
                    submitBtnText.textContent = 'Save';
                    submitBtn.className = 'btn btn-primary d-flex align-items-center gap-1';

                    clearFormErrors();

                    dashboardSection.style.display = 'none';
                    formSection.style.display = 'block';
                });

                mainMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    formSection.style.display = 'none';
                    dashboardSection.style.display = 'block';
                });

                document.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown-item') && e.target.closest('.dropdown-item').textContent.includes('Edit')) {
                        e.preventDefault();

                        submitBtnText.textContent = 'Update';
                        submitBtn.className = 'btn btn-success d-flex align-items-center gap-1';

                        clearFormErrors();

                        dashboardSection.style.display = 'none';
                        formSection.style.display = 'block';
                    }
                });

                function clearFormErrors() {
                    const errorElements = document.querySelectorAll('#titleError, #fileError, #notesError');
                    errorElements.forEach(el => el.style.display = 'none');

                    const inputElements = document.querySelectorAll('#title, #notes');
                    inputElements.forEach(el => el.classList.remove('is-invalid'));

                    const dropArea = document.getElementById('dropArea');
                    if (dropArea) {
                        dropArea.classList.remove('error');
                    }
                }
            });
        </script>
    </div>

</body>

</html>