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
        <!-- Form Section (Hidden by default) -->
        <div id="formSection" style="display: none;">
            <form id="mainForm" enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" name="id" value="">
                <input type="hidden" id="removedFiles" name="removedFiles" value="[]">
                
                <div class="d-flex gap-2 mb-4">
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" id="submitBtn" disabled>
                        <i class="fas fa-save"></i>
                        <span id="submitBtnText">Submit</span>
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

        <!-- Dashboard Section (Shown by default) -->
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

    <!-- Loading indicator -->
    <div id="loadingIndicator" class="text-center d-none mb-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading training manuals...</p>
    </div>

    <!-- Filter info -->
    <div id="filterInfo" class="alert alert-info mb-3" style="display: none;">
        <strong>Filters Applied:</strong>
        <span id="filterDetails"></span>
        <br><small id="resultCount"></small>
    </div>

    <!-- Main content container -->
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

    <script>
        /* COMMENTED OUT FOR DESIGN-ONLY VERSION
        document.addEventListener('DOMContentLoaded', function() {
            const dateFormat = 'MM/DD/YYYY HH:mm';

            $('input[name="dates"]').daterangepicker({
                autoUpdateInput: false,
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: dateFormat
                }
            });

            $('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format(dateFormat) + ' - ' + picker.endDate.format(dateFormat));
                toggleClearButton();
                performSearch();
            });

            $('input[name="dates"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                toggleClearButton();
                performSearch();
            });

            let searchTimeout;
            const searchInput = document.getElementById('search');
            const searchForm = document.getElementById('searchForm');
            const clearBtn = document.getElementById('clearBtn');

            function toggleClearButton() {
                const hasSearchContent = searchInput.value.trim().length > 0;
                const datesInput = document.getElementById('dates');
                const hasDatesContent = datesInput.value.trim().length > 0;

                if (hasSearchContent || hasDatesContent) {
                    clearBtn.classList.remove('d-none');
                    clearBtn.classList.add('d-flex');
                } else {
                    clearBtn.classList.add('d-none');
                    clearBtn.classList.remove('d-flex');
                }
            }

            // Initial check on page load
            toggleClearButton();

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                toggleClearButton();
                searchTimeout = setTimeout(performSearch, 500);
            });

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                document.getElementById('dates').value = '';
                toggleClearButton();
                performSearch();
            });

            function performSearch(page = 1) {
                const searchValue = searchInput.value.trim();
                const datesValue = document.getElementById('dates').value.trim();

                document.getElementById('loadingIndicator').classList.remove('d-none');
                document.getElementById('mainContent').style.opacity = '0.5';

                const formData = new FormData();
                formData.append('search', searchValue);
                formData.append('dates', datesValue);
                formData.append('page', page);

                fetch('<?= isset($base_url) ? $base_url : '' ?>ajax/search', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateContent(data, searchValue, datesValue);
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('loadingIndicator').classList.add('d-none');
                        document.getElementById('mainContent').style.opacity = '1';
                    });
            }

            function updateContent(data, searchValue, datesValue) {
                const filterInfo = document.getElementById('filterInfo');
                const filterDetails = document.getElementById('filterDetails');
                const resultCount = document.getElementById('resultCount');

                if (searchValue || datesValue) {
                    let details = '';
                    if (searchValue) details += `Search: "${searchValue}"`;
                    if (datesValue) {
                        if (searchValue) details += ' | ';
                        details += `Date Range: ${datesValue}`;
                    }
                    filterDetails.textContent = details;
                    resultCount.textContent = `Found ${data.total_rows} result(s)`;
                    filterInfo.style.display = 'block';
                } else {
                    filterInfo.style.display = 'none';
                }

                const mainContent = document.getElementById('mainContent');
                if (data.trainings && data.trainings.length > 0) {
                    let html = '<div><table class="table table-bordered"><thead><tr>';
                    html += '<th scope="col" style="width: 15%;">Title</th>';
                    html += '<th scope="col">Files</th>';
                    html += '<th scope="col">Uploaded by</th>';
                    html += '<th scope="col">Uploaded at</th>';
                    html += '<th scope="col">Notes</th>';
                    html += '<th scope="col">Actions</th>';
                    html += '</tr></thead><tbody>';

                    data.trainings.forEach(training => {
                        const date = new Date(training.created_at);
                        const uploadedAt = date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString('en-GB', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        html += '<tr>';
                        html += `<td class="align-middle">${escapeHtml(training.title)}</td>`;
                        html += '<td class="align-middle"><div class="d-flex flex-wrap gap-2 align-items-center">';

                        if (training.file_names) {
                            training.file_names.forEach((fileName, index) => {
                                const ext = fileName.split('.').pop().toLowerCase();
                                html += `<a class="file-preview-link" href="#" data-training-id="${training.id}" data-file-index="${index}" data-file-name="${fileName}" data-file-extension="${ext}" title="${fileName}" style="font-size: 1.25rem;">${getFileIcon(ext)}</a>`;
                            });
                        }

                        html += '</div></td>';
                        html += '<td class="align-middle">Nath</td>';
                        html += `<td class="align-middle">${uploadedAt}</td>`;
                        html += `<td class="align-middle">${training.note ? escapeHtml(training.note) : ''}</td>`;
                        html += `<td class="align-middle">
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger btn-sm dropdown-toggle rounded" data-bs-toggle="dropdown">Actions</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item text-primary" href="#"><i class="fas fa-edit"></i> Edit</a></li>
                                    <li><a class="dropdown-item text-danger ajax-delete" href="#" data-id="${training.id}"><i class="fas fa-trash"></i> Delete</a></li>
                                </ul>
                            </div>
                        </td>`;
                        html += '</tr>';
                    });

                    html += '</tbody></table></div>';
                    mainContent.innerHTML = html;
                } else {
                    mainContent.innerHTML = '<div class="text-center"><p class="fs-4 text-muted">No training manuals found.</p></div>';
                }

                document.getElementById('paginationContainer').innerHTML = data.pagination;

                initializeFilePreview();
                initializeDeleteFunctionality();
            }

            function getFileIcon(ext) {
                switch (ext) {
                    case 'pdf':
                        return '<i class="fas fa-file-pdf text-danger"></i>';
                    case 'ppt':
                    case 'pptx':
                        return '<i class="fas fa-file-powerpoint text-warning"></i>';
                    default:
                        return '<i class="fas fa-file text-muted"></i>';
                }
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            const confirmationMessage = document.getElementById('confirmationMessage');
            const confirmActionBtn = document.getElementById('confirmActionBtn');
            let pendingDeleteId = null;

            function initializeDeleteFunctionality() {
                document.querySelectorAll('.ajax-delete').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        pendingDeleteId = this.dataset.id;
                        confirmationMessage.textContent = 'Are you sure you want to delete this training manual? This action cannot be undone.';
                        confirmationModal.show();
                    });
                });
            }

            confirmActionBtn.addEventListener('click', function() {
                if (pendingDeleteId) {
                    deleteTraining(pendingDeleteId);
                    confirmationModal.hide();
                    pendingDeleteId = null;
                }
            });

            function deleteTraining(id) {
                document.getElementById('loadingIndicator').classList.remove('d-none');

                const formData = new FormData();
                formData.append('id', id);

                fetch('<?= isset($base_url) ? $base_url : '' ?>ajax/delete', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                            alert.style.zIndex = '9999';
                            alert.innerHTML = `
                            <strong>Success!</strong> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                            document.body.appendChild(alert);

                            setTimeout(() => {
                                if (alert.parentNode) {
                                    alert.parentNode.removeChild(alert);
                                }
                            }, 3000);

                            performSearch();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting.');
                    })
                    .finally(() => {
                        document.getElementById('loadingIndicator').classList.add('d-none');
                    });
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('ajax-page')) {
                    e.preventDefault();
                    performSearch(e.target.dataset.page);
                }
            });

            initializeDeleteFunctionality();

            const filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
            const filePreviewContent = document.getElementById('filePreviewContent');
            const downloadBtn = document.getElementById('downloadFileBtn');
            const modalTitle = document.getElementById('filePreviewModalLabel');

            function initializeFilePreview() {
                document.querySelectorAll('.file-preview-link').forEach(link => {
                    link.replaceWith(link.cloneNode(true));
                });

                document.querySelectorAll('.file-preview-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();

                        const fileName = this.dataset.fileName;
                        const fileExtension = this.dataset.fileExtension;
                        const previewUrl = this.href;

                        modalTitle.textContent = `Preview: ${fileName}`;
                        downloadBtn.href = previewUrl;
                        downloadBtn.download = fileName;

                        filePreviewContent.innerHTML = `
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        `;

                        filePreviewModal.show();

                        if (fileExtension === 'pdf') {
                            filePreviewContent.innerHTML = `
                                <iframe src="${previewUrl}" width="100%" height="100%" style="border: none;">
                                    <p>Your browser does not support PDFs. <a href="${previewUrl}" target="_blank">Click here to download the PDF</a></p>
                                </iframe>
                            `;
                        } else {
                            filePreviewContent.innerHTML = `
                                <div class="alert alert-info">
                                    <h5>Preview not available</h5>
                                    <p>This file type (${fileExtension.toUpperCase()}) cannot be previewed in the browser. You can download it using the button below.</p>
                                    <div class="mt-3">
                                        <strong>File:</strong> ${fileName}<br>
                                        <strong>Type:</strong> ${fileExtension.toUpperCase()}
                                    </div>
                                </div>
                            `;
                        }
                    });
                });
            }

            initializeFilePreview();

            const hoverPreviewTooltip = document.getElementById('hoverPreviewTooltip');
            const hoverPreviewContent = document.getElementById('hoverPreviewContent');
            const hoverPreviewTitle = document.getElementById('hoverPreviewTitle');
            let hoverTimeout;
            let isHovering = false;

            function positionTooltip(tooltip, mouseX, mouseY) {
                const tooltipRect = tooltip.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                let left = mouseX + 15;
                let top = mouseY + 15;

                if (left + tooltipRect.width > viewportWidth) {
                    left = mouseX - tooltipRect.width - 15;
                }

                if (top + tooltipRect.height > viewportHeight) {
                    top = mouseY - tooltipRect.height - 15;
                }

                if (left < 10) left = 10;

                if (top < 10) top = 10;

                tooltip.style.left = left + 'px';
                tooltip.style.top = top + 'px';
            }

            function loadPreviewContent(fileName, fileExtension, previewUrl) {
                hoverPreviewTitle.textContent = fileName;

                hoverPreviewContent.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center p-4">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                if (fileExtension === 'pdf') {
                    hoverPreviewContent.innerHTML = `
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-pdf text-danger me-2" style="font-size: 24px;"></i>
                                <strong>PDF Document</strong>
                            </div>
                            <p class="text-muted mb-2">Click to view full PDF in modal</p>
                            <small class="text-secondary">File: ${fileName}</small>
                        </div>
                    `;
                } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                    hoverPreviewContent.innerHTML = `
                        <div class="p-2">
                            <img src="${previewUrl}" 
                                 class="img-fluid rounded" 
                                 alt="${fileName}"
                                 style="max-height: 250px; width: 100%; object-fit: contain;"
                                 onerror="this.parentElement.innerHTML='<div class=\\"p-3 text-center\\"><div class=\\"text-muted\\">Image preview not available</div><small>${fileName}</small></div>'">
                        </div>
                    `;
                } else if (fileExtension === 'txt') {
                    fetch(previewUrl)
                        .then(response => response.text())
                        .then(text => {
                            const truncatedText = text.length > 500 ? text.substring(0, 500) + '...' : text;
                            hoverPreviewContent.innerHTML = `
                                <div class="p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-file-alt me-2" style="font-size: 20px;"></i>
                                        <strong>Text File</strong>
                                    </div>
                                    <pre style="white-space: pre-wrap; word-wrap: break-word; font-size: 0.875rem; margin: 0; max-height: 200px; overflow-y: auto; background-color: #f8f9fa; padding: 0.75rem; border-radius: 0.375rem;">${truncatedText}</pre>
                                </div>
                            `;
                        })
                        .catch(error => {
                            hoverPreviewContent.innerHTML = `
                                <div class="p-3 text-center">
                                    <div class="text-muted">Text preview not available</div>
                                    <small>${fileName}</small>
                                </div>
                            `;
                        });
                } else if (['ppt', 'pptx'].includes(fileExtension)) {
                    hoverPreviewContent.innerHTML = `
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-powerpoint text-warning me-2" style="font-size: 24px;"></i>
                                <strong>PowerPoint Presentation</strong>
                            </div>
                            <p class="text-muted mb-2">Click to download and view presentation</p>
                            <small class="text-secondary">File: ${fileName}</small>
                        </div>
                    `;
                } else {
                    hoverPreviewContent.innerHTML = `
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file me-2" style="font-size: 20px;"></i>
                                <strong>${fileExtension.toUpperCase()} File</strong>
                            </div>
                            <p class="text-muted mb-2">Preview not available for this file type</p>
                            <small class="text-secondary">File: ${fileName}</small>
                        </div>
                    `;
                }
            }

            document.querySelectorAll('.file-preview-link').forEach(link => {
                link.addEventListener('mouseenter', function(e) {
                    isHovering = true;
                    const fileName = this.dataset.fileName;
                    const fileExtension = this.dataset.fileExtension;
                    const previewUrl = this.href;

                    clearTimeout(hoverTimeout);
                    hoverTimeout = setTimeout(() => {
                        if (isHovering) {
                            loadPreviewContent(fileName, fileExtension, previewUrl);
                            hoverPreviewTooltip.classList.remove('d-none');
                            positionTooltip(hoverPreviewTooltip, e.clientX, e.clientY);
                        }
                    }, 300);
                });

                link.addEventListener('mouseleave', function() {
                    isHovering = false;
                    clearTimeout(hoverTimeout);
                    hoverTimeout = setTimeout(() => {
                        if (!isHovering) {
                            hoverPreviewTooltip.classList.add('d-none');
                        }
                    }, 100);
                });

                link.addEventListener('mousemove', function(e) {
                    if (!hoverPreviewTooltip.classList.contains('d-none')) {
                        positionTooltip(hoverPreviewTooltip, e.clientX, e.clientY);
                    }
                });

                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    hoverPreviewTooltip.classList.add('d-none');
                    isHovering = false;
                    clearTimeout(hoverTimeout);

                    const fileName = this.dataset.fileName;
                    const fileExtension = this.dataset.fileExtension;
                    const previewUrl = this.href;

                    modalTitle.textContent = `Preview: ${fileName}`;
                    downloadBtn.href = previewUrl;
                    downloadBtn.download = fileName;

                    filePreviewContent.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

                    filePreviewModal.show();

                    if (fileExtension === 'pdf') {
                        filePreviewContent.innerHTML = `
                    <iframe src="${previewUrl}" 
                            width="100%" 
                            height="100%" 
                            style="border: none;">
                        <p>Your browser does not support PDFs. 
                           <a href="${previewUrl}" target="_blank">Click here to download the PDF</a>
                        </p>
                    </iframe>
                `;
                    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                        filePreviewContent.innerHTML = `
                    <div class="text-center">
                        <img src="${previewUrl}" 
                             class="img-fluid" 
                             alt="${fileName}"
                             style="max-height: 100%; max-width: 100%;">
                    </div>
                `;
                    } else if (fileExtension === 'txt') {
                        fetch(previewUrl)
                            .then(response => response.text())
                            .then(text => {
                                filePreviewContent.innerHTML = `
                            <pre style="white-space: pre-wrap; word-wrap: break-word; height: 100%; overflow-y: auto; padding: 1rem; background-color: #f8f9fa; border-radius: 0.375rem;">${text}</pre>
                        `;
                            })
                            .catch(error => {
                                filePreviewContent.innerHTML = `
                            <div class="alert alert-warning">
                                <h5>Preview not available</h5>
                                <p>Cannot preview this file type. You can download it using the button below.</p>
                            </div>
                        `;
                            });
                    } else {
                        filePreviewContent.innerHTML = `
                    <div class="alert alert-info">
                        <h5>Preview not available</h5>
                        <p>This file type (${fileExtension.toUpperCase()}) cannot be previewed in the browser. You can download it using the button below.</p>
                        <div class="mt-3">
                            <strong>File:</strong> ${fileName}<br>
                            <strong>Type:</strong> ${fileExtension.toUpperCase()}
                        </div>
                    </div>
                `;
                    }
                });
            });

            hoverPreviewTooltip.addEventListener('mouseenter', function() {
                isHovering = true;
                clearTimeout(hoverTimeout);
            });

            hoverPreviewTooltip.addEventListener('mouseleave', function() {
                isHovering = false;
                hoverTimeout = setTimeout(() => {
                    if (!isHovering) {
                        hoverPreviewTooltip.classList.add('d-none');
                    }
                }, 100);
            });

            document.addEventListener('click', function(e) {
                if (!hoverPreviewTooltip.contains(e.target)) {
                    hoverPreviewTooltip.classList.add('d-none');
                    isHovering = false;
                    clearTimeout(hoverTimeout);
                }
            });
        });
        */ // END COMMENTED OUT FOR DESIGN-ONLY VERSION
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        /* COMMENTED OUT FOR DESIGN-ONLY VERSION
        const dropAreaPlaceholder = document.getElementById("drop-area-placeholder");
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("fileInput");
        const fileList = document.getElementById("fileList");

        let currentFiles = [];
        let existingFiles = [];
        let removedExistingFiles = [];
        const maxFiles = 5;
        const maxSizeMB = 100;
        const allowedTypes = ["pdf", "ppt", "pptx"];

        dropArea.addEventListener("click", () => fileInput.click());

        fileInput.addEventListener("change", () => handleFiles(fileInput.files));

        dropArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropArea.classList.add("dragover");
        });

        dropArea.addEventListener("dragleave", () => dropArea.classList.remove("dragover"));

        dropArea.addEventListener("drop", (e) => {
            e.preventDefault();
            dropArea.classList.remove("dragover");

            const files = e.dataTransfer.files;
            handleFiles(files);
            fileInput.files = files;
        });

        // helper functions
        function clearFiles() {
            fileInput.value = "";
            fileList.innerHTML = "";
            currentFiles = [];
            dropAreaPlaceholder.classList.remove("d-none");
        }

        function removeFile(index, isNewFile) {
            if (isNewFile) {
                const actualIndex = index - existingFiles.length;
                if (actualIndex >= 0 && actualIndex < currentFiles.length) {
                    currentFiles.splice(actualIndex, 1);
                    updateFileInput();
                }
            } else {
                const fileName = existingFiles[index];
                if (fileName && !removedExistingFiles.includes(fileName)) {
                    removedExistingFiles.push(fileName);
                    updateRemovedFilesInput();
                }
                existingFiles.splice(index, 1);
            }

            renderFileList();

            if (currentFiles.length === 0 && existingFiles.length === 0) {
                dropAreaPlaceholder.classList.remove("d-none");
            }
        }

        function updateRemovedFilesInput() {
            const removedFilesInput = document.getElementById('removedFiles');
            if (removedFilesInput) {
                removedFilesInput.value = JSON.stringify(removedExistingFiles);
            }
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            currentFiles.forEach(file => {
                dt.items.add(file);
            });
            fileInput.files = dt.files;
        }

        function renderFileList() {
            fileList.innerHTML = "";
            let index = 0;

            existingFiles.forEach((fileName, i) => {
                if (fileName && fileName.trim()) {
                    const box = createFileBox(fileName.trim(), index, false);
                    fileList.appendChild(box);
                    index++;
                }
            });

            currentFiles.forEach((file, i) => {
                const box = createFileBox(file, index, true);
                fileList.appendChild(box);
                index++;
            });
        }

        function createFileBox(file, index, isNewFile = null) {
            const box = document.createElement("div");
            box.className = "position-relative";

            const isFileObject = isNewFile !== null ? isNewFile : (file && typeof file === 'object' && file.name);
            const fileName = isFileObject ? file.name : file;

            const fileBox = document.createElement("div");
            fileBox.className = isFileObject ?
                "file-box border rounded-3 p-3 text-center shadow-sm" :
                "file-box border rounded-3 p-3 text-center shadow-sm bg-light";

            const ext = fileName.split('.').pop().toUpperCase();
            const nameOnly = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;

            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.className = "btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1";
            removeBtn.style.zIndex = "10";
            removeBtn.innerHTML = `
            <i class="fas fa-times removeFile" style="font-size: 12px;"></i>
        `;

            removeBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                removeFile(index, isFileObject);
            });

            if (isFileObject && file.uploading) {
                const progressBar = document.createElement("div");
                progressBar.className = "progress mt-2";
                progressBar.style.height = "4px";
                progressBar.innerHTML = `
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 0%" 
                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            `;
                fileBox.appendChild(progressBar);
            }

            if (isFileObject) {
                let sizeText;
                if (file.size < 1024 * 1024) {
                    sizeText = (file.size / 1024).toFixed(1) + " KB";
                } else {
                    sizeText = (file.size / (1024 * 1024)).toFixed(2) + " MB";
                }

                const maxLength = 18;
                let displayName = nameOnly.length > maxLength ?
                    nameOnly.substring(0, maxLength) + "..." :
                    nameOnly;

                const typeEl = document.createElement("div");
                typeEl.className = "fw-bold text-secondary mb-1";
                typeEl.textContent = ext;

                const sizeEl = document.createElement("div");
                sizeEl.className = "fw-bold text-primary mb-1";
                sizeEl.textContent = sizeText;

                const nameEl = document.createElement("div");
                nameEl.className = "small text-muted";
                nameEl.textContent = displayName;

                fileBox.appendChild(typeEl);
                fileBox.appendChild(sizeEl);
                fileBox.appendChild(nameEl);
            } else {
                fileBox.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-file-alt text-primary mb-2" style="font-size: 24px;"></i>
                    <div class="text-truncate w-100" title="${fileName}">
                        <strong>${nameOnly}</strong>
                    </div>
                    <small class="text-muted">${ext}</small>
                    <small class="text-success">Existing File</small>
                </div>
            `;
            }

            box.appendChild(fileBox);
            box.appendChild(removeBtn);
            return box;
        }

        */

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
            // Initialize arrays safely
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

        // Toggle functionality for dashboard and form
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardSection = document.getElementById('dashboardSection');
            const formSection = document.getElementById('formSection');
            const addBtn = document.getElementById('addBtn');
            const mainMenuBtn = document.getElementById('mainMenuBtn');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const mainForm = document.getElementById('mainForm');

            // Show form when Add button is clicked
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Reset form for add mode
                mainForm.reset();
                document.querySelector('input[name="id"]').value = '';
                submitBtnText.textContent = 'Submit';
                submitBtn.className = 'btn btn-primary d-flex align-items-center gap-1';
                
                // Clear any existing errors
                clearFormErrors();
                
                // Toggle sections
                dashboardSection.style.display = 'none';
                formSection.style.display = 'block';
            });

            // Show dashboard when Main Menu button is clicked
            mainMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle sections
                formSection.style.display = 'none';
                dashboardSection.style.display = 'block';
            });

            // Handle edit button clicks (for existing training items)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.dropdown-item') && e.target.closest('.dropdown-item').textContent.includes('Edit')) {
                    e.preventDefault();
                    
                    // Set form to edit mode
                    submitBtnText.textContent = 'Update';
                    submitBtn.className = 'btn btn-success d-flex align-items-center gap-1';
                    
                    // You would populate form fields here with existing data
                    // This is just the UI toggle for now
                    
                    // Clear any existing errors
                    clearFormErrors();
                    
                    // Toggle sections
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