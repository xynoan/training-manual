    <a href="<?= base_url('add') ?>" class="btn btn-danger d-flex align-items-center gap-2">
        <i class="fas fa-plus-circle"></i>
        Add
    </a>
    </div>
    <div class="mb-4">
        <form id="searchForm" class="d-flex justify-content-start gap-3 flex-wrap align-items-end">
            <div>
                <label for="dates" class="form-label small text-muted mb-1">Date Range</label>
                <input type="text" class="form-control" id="dates" name="dates"
                    title="Filter from this date"
                    value="<?= isset($dates) ? htmlspecialchars($dates) : '' ?>">
            </div>
            <div class="input-group w-25">
                <input type="text" class="form-control" id="search" name="search"
                    placeholder="Search by title, notes, or filename..."
                    value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
            </div>
            <div>
                <label class="form-label small text-muted mb-1">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit" id="filterBtn">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <button class="btn btn-outline-danger d-flex justify-content-center align-items-center gap-1 d-none" 
                            type="button" id="clearBtn" title="Clear all filters">
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
        <?php if (!empty($trainings)): ?>
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
                            <?php $uploaded_at = date_format(date_create($training['created_at']), "d/m/Y H:i") ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars($training['title']) ?></td>
                                <td class="align-middle">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <?php foreach ($training['file_names'] as $index => $file_name): ?>
                                            <a class="link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover file-preview-link d-inline-flex align-items-center"
                                                href="<?= base_url('file/preview/' . $training['id'] . '/' . $index) ?>"
                                                data-training-id="<?= $training['id'] ?>"
                                                data-file-index="<?= $index ?>"
                                                data-file-name="<?= htmlspecialchars($file_name) ?>"
                                                data-file-extension="<?= strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) ?>"
                                                title="<?= htmlspecialchars($file_name) ?>"
                                                style="font-size: 1.25rem;">
                                                <?= get_file_icon(pathinfo($file_name, PATHINFO_EXTENSION)) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="align-middle">Nath</td>
                                <td class="align-middle"><?= $uploaded_at ?></td>
                                <td class="align-middle"><?= isset($training['note']) ? htmlspecialchars($training['note']) : '' ?></td>
                                <td class="align-middle">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle rounded" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="d-flex align-items-center gap-2 dropdown-item text-primary" href="<?= base_url('edit?id=' . $training['id']) ?>">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="d-flex align-items-center gap-2 dropdown-item text-danger ajax-delete"
                                                    href="#" data-id="<?= $training['id'] ?>">
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
        <?php if (empty($trainings)): ?>
            <div class="text-center">
                <p class="fs-4 text-muted">No training manuals found.</p>
                <p>Try adjusting your search terms or date range.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div id="paginationContainer">
        <?= $pagination ?>
    </div>

    <?php require 'partials/file-preview-modal.php' ?>
    
    <?php require 'partials/hover-preview-tooltip.php' ?>
    
    <?php require 'partials/confirmation-modal.php' ?>

    <script>
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

                fetch('<?= base_url('ajax/search') ?>', {
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
                        const uploadedAt = date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'});
                        
                        html += '<tr>';
                        html += `<td class="align-middle">${escapeHtml(training.title)}</td>`;
                        html += '<td class="align-middle"><div class="d-flex flex-wrap gap-2 align-items-center">';
                        
                        if (training.file_names) {
                            training.file_names.forEach((fileName, index) => {
                                const ext = fileName.split('.').pop().toLowerCase();
                                html += `<a class="file-preview-link" href="<?= base_url('file/preview/') ?>${training.id}/${index}" data-training-id="${training.id}" data-file-index="${index}" data-file-name="${fileName}" data-file-extension="${ext}" title="${fileName}" style="font-size: 1.25rem;">${getFileIcon(ext)}</a>`;
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
                                    <li><a class="dropdown-item text-primary" href="<?= base_url('edit?id=') ?>${training.id}"><i class="fas fa-edit"></i> Edit</a></li>
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
                    case 'pdf': return '<i class="fas fa-file-pdf text-danger"></i>';
                    case 'ppt': case 'pptx': return '<i class="fas fa-file-powerpoint text-warning"></i>';
                    default: return '<i class="fas fa-file text-muted"></i>';
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

                fetch('<?= base_url('ajax/delete') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
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
    </script>