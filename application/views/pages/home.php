    <a href="<?= base_url('add') ?>" class="btn btn-danger d-flex align-items-center gap-2">
        <i class="fas fa-plus-circle"></i>
        Add
    </a>
    </div>
    <div class="mb-4">
        <form method="GET" action="<?= base_url() ?>" class="d-flex justify-content-start gap-3 flex-wrap align-items-end">
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
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <?php if (!empty($search) || !empty($dates)): ?>
                        <a href="<?= base_url() ?>" class="btn btn-outline-danger d-flex justify-content-center align-items-center gap-1" title="Clear all filters">
                            <i class="fas fa-times-circle"></i>
                            Clear
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($search) || !empty($dates)): ?>
        <div class="alert alert-info mb-3">
            <strong>Filters Applied:</strong>
            <?php if (!empty($search)): ?>
                Search: "<?= htmlspecialchars($search) ?>"
            <?php endif; ?>
            <?php if (!empty($dates)): ?>
                <?php if (!empty($search)): ?> | <?php endif; ?>
                Date Range:
                <?php if (!empty($date_from) && !empty($date_to)): ?>
                    <?php if ($date_from === $date_to): ?>
                        <?= date('M j, Y H:i', strtotime($date_from)) ?>
                    <?php else: ?>
                        <?= date('M j, Y H:i', strtotime($date_from)) ?> to <?= date('M j, Y H:i', strtotime($date_to)) ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($trainings)): ?>
                <br><small>Found <?= count($trainings) ?> result(s)</small>
            <?php else: ?>
                <br><small>No results found</small>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($trainings)): ?>
        <!-- <div class="table-responsive"> -->
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" style="width: 15%;">Title</th>
                        <th scope="col">Files</th>
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
                                            href="<?= base_url('pages/preview_file/' . $training['id'] . '/' . $index) ?>"
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
                                            <a class="d-flex align-items-center gap-2 dropdown-item text-danger"
                                                href="<?= base_url('pages/delete/' . $training['id']) ?>"
                                                onclick="return confirm('Are you sure you want to delete this training manual?');">
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
        <?php if (!empty($search) || !empty($dates)): ?>
            <div class="text-center">
                <p class="fs-4 text-muted">No training manuals match your filters.</p>
                <p>Try adjusting your search terms or date range, or <a href="<?= base_url() ?>">view all training manuals</a>.</p>
            </div>
        <?php else: ?>
            <p class="text-center fs-4">No training manuals found.</p>
        <?php endif; ?>
    <?php endif; ?>
    <div>
        <?= $pagination ?>
    </div>

    <?php require 'partials/file-preview-modal.php' ?>
    
    <?php require 'partials/hover-preview-tooltip.php' ?>

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
                $(this).closest('form').submit();
            });

            $('input[name="dates"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
            const filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
            const filePreviewContent = document.getElementById('filePreviewContent');
            const downloadBtn = document.getElementById('downloadFileBtn');
            const modalTitle = document.getElementById('filePreviewModalLabel');

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