    <a href="<?= base_url('add') ?>" class="btn btn-danger d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 
         0v3h-3a.5.5 0 0 0 0 
         1h3v3a.5.5 0 0 0 1 
         0v-3h3a.5.5 0 0 0 0-1h-3z" />
        </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                        </svg>
                        Filter
                    </button>
                    <?php if (!empty($search) || !empty($dates)): ?>
                        <a href="<?= base_url() ?>" class="btn btn-outline-danger d-flex justify-content-center align-items-center gap-1" title="Clear all filters">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path d="m4.646 4.646.708.708L8 8l2.646-2.646.708-.708L8.707 8l2.647 2.646-.708.708L8 8.707l-2.646 2.647-.708-.708L7.293 8z" />
                            </svg>
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
                                <?php foreach ($training['file_names'] as $index => $file_name): ?>
                                    <a class="link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover file-preview-link"
                                        href="<?= base_url('pages/preview_file/' . $training['id'] . '/' . $index) ?>"
                                        data-training-id="<?= $training['id'] ?>"
                                        data-file-index="<?= $index ?>"
                                        data-file-name="<?= htmlspecialchars($file_name) ?>"
                                        data-file-extension="<?= strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) ?>">
                                        <?= $file_name ?>
                                    </a>
                                    <?= $index < count($training['file_names']) - 1 ? ', ' : '' ?>
                                <?php endforeach; ?>
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="d-flex align-items-center gap-2 dropdown-item text-danger"
                                                href="<?= base_url('pages/delete/' . $training['id']) ?>"
                                                onclick="return confirm('Are you sure you want to delete this training manual?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                                </svg>
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

            // Hover preview functionality
            const hoverPreviewTooltip = document.getElementById('hoverPreviewTooltip');
            const hoverPreviewContent = document.getElementById('hoverPreviewContent');
            const hoverPreviewTitle = document.getElementById('hoverPreviewTitle');
            let hoverTimeout;
            let isHovering = false;

            // Function to position tooltip within viewport
            function positionTooltip(tooltip, mouseX, mouseY) {
                const tooltipRect = tooltip.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                
                let left = mouseX + 15;
                let top = mouseY + 15;
                
                // Adjust if tooltip goes beyond right edge
                if (left + tooltipRect.width > viewportWidth) {
                    left = mouseX - tooltipRect.width - 15;
                }
                
                // Adjust if tooltip goes beyond bottom edge
                if (top + tooltipRect.height > viewportHeight) {
                    top = mouseY - tooltipRect.height - 15;
                }
                
                // Ensure tooltip doesn't go beyond left edge
                if (left < 10) left = 10;
                
                // Ensure tooltip doesn't go beyond top edge
                if (top < 10) top = 10;
                
                tooltip.style.left = left + 'px';
                tooltip.style.top = top + 'px';
            }

            // Function to load preview content
            function loadPreviewContent(fileName, fileExtension, previewUrl) {
                hoverPreviewTitle.textContent = fileName;
                
                // Reset content with loading spinner
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-pdf text-danger me-2" viewBox="0 0 16 16">
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                    <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.135.968z"/>
                                </svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-text me-2" viewBox="0 0 16 16">
                                            <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z"/>
                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                                        </svg>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-ppt text-warning me-2" viewBox="0 0 16 16">
                                    <path d="M7 5.5a1 1 0 0 0-1 1V13a.5.5 0 0 0 1 0v-2h1.188a2.75 2.75 0 0 0 0-5.5zm0 1h1.188a1.75 1.75 0 1 1 0 3.5H7z"/>
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                </svg>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark me-2" viewBox="0 0 16 16">
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                </svg>
                                <strong>${fileExtension.toUpperCase()} File</strong>
                            </div>
                            <p class="text-muted mb-2">Preview not available for this file type</p>
                            <small class="text-secondary">File: ${fileName}</small>
                        </div>
                    `;
                }
            }

            document.querySelectorAll('.file-preview-link').forEach(link => {
                // Hover events for preview tooltip
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
                    }, 300); // 300ms delay before showing tooltip
                });

                link.addEventListener('mouseleave', function() {
                    isHovering = false;
                    clearTimeout(hoverTimeout);
                    hoverTimeout = setTimeout(() => {
                        if (!isHovering) {
                            hoverPreviewTooltip.classList.add('d-none');
                        }
                    }, 100); // Small delay to allow moving to tooltip
                });

                link.addEventListener('mousemove', function(e) {
                    if (!hoverPreviewTooltip.classList.contains('d-none')) {
                        positionTooltip(hoverPreviewTooltip, e.clientX, e.clientY);
                    }
                });

                // Click event for modal (existing functionality)
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Hide hover tooltip when opening modal
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

            // Add hover events to the tooltip itself to prevent it from disappearing
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

            // Hide tooltip when clicking anywhere else
            document.addEventListener('click', function(e) {
                if (!hoverPreviewTooltip.contains(e.target)) {
                    hoverPreviewTooltip.classList.add('d-none');
                    isHovering = false;
                    clearTimeout(hoverTimeout);
                }
            });
        });
    </script>