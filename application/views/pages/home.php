<a href="/add" class="btn btn-danger d-flex align-items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentcolor"
        class="bi bi-plus-circle-fill" viewbox="0 0 16 16">
        <path
            d="m16 8a8 8 0 1 1 0 8a8 8 0 0 1 16 0m8.5 4.5a.5.5 0 0 0-1 
         0v3h-3a.5.5 0 0 0 0 
         1h3v3a.5.5 0 0 0 1 
         0v-3h3a.5.5 0 0 0 0-1h-3z" />
    </svg>
    add
</a>
</div>
<div class="mb-4 d-flex justify-content-end">
    <label for="search">
        <input type="text" class="form-control" id="search" placeholder="search...">
    </label>
</div>
<?php if (!empty($trainings)): ?>
<!-- <div class="table-responsive"> -->
<div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">title</th>
                <th scope="col">files</th>
                <th scope="col">uploaded by</th>
                <th scope="col">uploaded at</th>
                <th scope="col">notes</th>
                <th scope="col">actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trainings as $training): ?>
                <?php $uploaded_at = date_format(date_create($training['created_at']), "m/d/y") ?>
                <tr>
                    <td><?= $training['title'] ?></td>
                    <td>
                        <?php foreach ($training['file_names'] as $index => $file_name): ?>
                            <a class="link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" href="#">
                                <?= $file_name ?>
                            </a>
                            <?= $index < count($training['file_names']) - 1 ? ', ' : '' ?>
                        <?php endforeach; ?>
                    </td>
                    <td>nath</td>
                    <td><?= $uploaded_at ?></td>
                    <td><?= isset($training['note']) ? $training['note'] : '' ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="d-flex align-items-center gap-2 dropdown-item text-primary" href="/edit?id=<?= $training['id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentcolor" class="bi bi-pencil-square" viewbox="0 0 16 16">
                                            <path d="m15.502 1.94a.5.5 0 0 1 0 .706l14.459 3.69l-2-2l13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2l4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd" d="m1 13.5a1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5h9a.5.5 0 0 0 0-1h2.5a1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                        edit
                                    </a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center gap-2 dropdown-item text-danger" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentcolor" class="bi bi-trash3-fill" viewbox="0 0 16 16">
                                            <path d="m11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66a2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84l2.038 3.5h1.5a.5.5 0 0 1 0-1h5v-1a1.5 1.5 0 0 1 6.5 0h3a1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5m4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528m8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0v5a.5.5 0 0 0-.5-.5" />
                                        </svg>
                                        delete
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
    <p class="text-center fs-4">no training manuals found.</p>
<?php endif; ?>
<div>
    <?= $pagination ?>
</div>
