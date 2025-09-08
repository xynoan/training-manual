<a href="/add" class="btn btn-danger d-flex align-items-center gap-2">
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
        <!-- Date Range Filters -->
        <div>
            <label for="date_from" class="form-label small text-muted mb-1">From Date</label>
            <input type="date" class="form-control" id="date_from" name="date_from"
                title="Filter from this date"
                value="<?= isset($date_from) ? htmlspecialchars($date_from) : '' ?>">
        </div>
        <div>
            <label for="date_to" class="form-label small text-muted mb-1">To Date</label>
            <input type="date" class="form-control" id="date_to" name="date_to"
                title="Filter up to this date"
                value="<?= isset($date_to) ? htmlspecialchars($date_to) : '' ?>">
        </div>
        <!-- Search Input -->
        <div class="input-group w-25">
            <input type="text" class="form-control" id="search" name="search"
                placeholder="Search by title, notes, or filename..."
                value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
        </div>
        <!-- Action Buttons -->
        <div>
            <label class="form-label small text-muted mb-1">&nbsp;</label>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                        <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                    </svg>
                    Filter
                </button>
                <?php if (!empty($search) || !empty($date_from) || !empty($date_to)): ?>
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

<?php if (!empty($search) || !empty($date_from) || !empty($date_to)): ?>
    <div class="alert alert-info mb-3">
        <strong>Filters Applied:</strong>
        <?php if (!empty($search)): ?>
            Search: "<?= htmlspecialchars($search) ?>"
        <?php endif; ?>
        <?php if (!empty($date_from) || !empty($date_to)): ?>
            <?php if (!empty($search)): ?> | <?php endif; ?>
            Date Range:
            <?php if (!empty($date_from)): ?>
                From <?= date('M j, Y', strtotime($date_from)) ?>
            <?php endif; ?>
            <?php if (!empty($date_from) && !empty($date_to)): ?> to <?php endif; ?>
            <?php if (!empty($date_to)): ?>
                <?php if (empty($date_from)): ?>Up to <?php endif; ?><?= date('M j, Y', strtotime($date_to)) ?>
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
                    <th scope="col">Title</th>
                    <th scope="col">Files</th>
                    <th scope="col">Uploaded by</th>
                    <th scope="col">Uploaded at</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainings as $training): ?>
                    <?php $uploaded_at = date_format(date_create($training['created_at']), "m/d/Y") ?>
                    <tr>
                        <td><?= htmlspecialchars($training['title']) ?></td>
                        <td>
                            <?php foreach ($training['file_names'] as $index => $file_name): ?>
                                <a class="link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover file-preview-link"
                                    href="/pages/preview_file/<?= $training['id'] ?>/<?= $index ?>"
                                    data-training-id="<?= $training['id'] ?>"
                                    data-file-index="<?= $index ?>"
                                    data-file-name="<?= htmlspecialchars($file_name) ?>"
                                    data-file-extension="<?= strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) ?>">
                                    <?= $file_name ?>
                                </a>
                                <?= $index < count($training['file_names']) - 1 ? ', ' : '' ?>
                            <?php endforeach; ?>
                        </td>
                        <td>nath</td>
                        <td><?= $uploaded_at ?></td>
                        <td><?= isset($training['note']) ? htmlspecialchars($training['note']) : '' ?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="d-flex align-items-center gap-2 dropdown-item text-primary" href="/edit?id=<?= $training['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="d-flex align-items-center gap-2 dropdown-item text-danger"
                                            href="/pages/delete/<?= $training['id'] ?>"
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
    <?php if (!empty($search) || !empty($date_from) || !empty($date_to)): ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
        const filePreviewContent = document.getElementById('filePreviewContent');
        const downloadBtn = document.getElementById('downloadFileBtn');
        const modalTitle = document.getElementById('filePreviewModalLabel');

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
    });
</script>