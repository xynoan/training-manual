<?php require 'partials/form.php' ?>

<div id="dashboardSection">
    <div class="d-flex flex-sm-row flex-column justify-content-between align-items-center mb-3">
        <h1 class="fw-bold text-center mb-3"><?php echo isset($title) ? $title : 'Training Manual'; ?></h1>
        <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center gap-2" id="addBtn">
            <i class="fas fa-plus-circle"></i>
            Add
        </button>
    </div>
    <form id="searchForm" class="d-flex flex-column flex-sm-row justify-content-center justify-content-sm-start gap-3 flex-wrap align-items-center align-items-sm-end mb-0" onsubmit="return false;">
        <div class="responsiveFormGroup">
            <label for="datetimes" class="form-label small text-muted mb-1">Date Range</label>
            <input type="text" class="form-control" id="datetimes" name="datetimes"
                title="Filter from this date"
                placeholder="Select date range"
                value="<?= isset($datetimes) ? htmlspecialchars($datetimes) : '' ?>">
        </div>
        <div class="responsiveFormGroup">
            <label for="search" class="form-label small text-muted mb-1">Search</label>
            <input type="text" class="form-control" id="search" name="search"
                placeholder="Search by title, notes"
                value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
        </div>
        <div class="d-block responsiveFormGroup mb-3">
            <label class="form-label small text-muted mb-1 d-none">&nbsp;</label>
            <div class="d-flex gap-2 flex-column flex-sm-row">
                <button class="btn btn-outline-danger d-flex justify-content-center align-items-center gap-1 d-none"
                    type="button" id="clearBtn" title="Clear all filters">
                    <i class="fas fa-times-circle"></i>
                    Clear
                </button>
            </div>
        </div>
    </form>

    <?php require 'partials/loading-indicator.php' ?>

    <?php require 'partials/filter-info.php' ?>

    <div id="mainContent">
        <?php if (isset($trainings) && !empty($trainings)): ?>
            <div class="table-responsive-sm">
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
                                <td class="align-middle"><?= isset($training['note']) ? $training['note'] : '' ?></td>
                                <td class="align-middle">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle rounded" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="d-flex align-items-center gap-2 dropdown-item text-primary edit-training" href="#" 
                                                   data-id="<?= isset($training['id']) ? $training['id'] : '' ?>"
                                                   data-title="<?= isset($training['title']) ? htmlspecialchars($training['title']) : '' ?>"
                                                   data-note="<?= isset($training['note']) ? htmlspecialchars($training['note']) : '' ?>">
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