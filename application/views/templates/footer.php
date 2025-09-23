<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/initialize_quill.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/notes_auto_save.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/helper_functions.js"></script>
<script>
    $(function() {
        $('input[name="datetimes"]').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            autoUpdateInput: false, 
            locale: {
                format: 'MM/DD/YYYY HH:mm',
                cancelLabel: 'Clear'
            }
        });
        
        $('input[name="datetimes"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY HH:mm') + ' - ' + picker.endDate.format('MM/DD/YYYY HH:mm'));
            console.log('Date range applied:', $(this).val());
            if (typeof window.performAjaxFilter === 'function') {
                window.performAjaxFilter();
            } else {
                console.error('performAjaxFilter function not found');
            }
        });
        
        $('input[name="datetimes"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            console.log('Date range cleared');
            if (typeof window.performAjaxFilter === 'function') {
                window.performAjaxFilter();
            } else {
                console.error('performAjaxFilter function not found');
            }
        });
    });
    window.appConfig = {
        baseUrl: '<?php echo base_url(); ?>'
    };

    <?php if (isset($training) && !empty($training['note'])): ?>
        window.existingNotes = <?php echo json_encode($training['note']); ?>;
    <?php endif; ?>

    <?php if ($this->session->userdata('temp_notes')): ?>
        window.tempNotes = <?php echo json_encode($this->session->userdata('temp_notes')); ?>;
    <?php endif; ?>

    $(document).ready(function() {
        restoreUploadedFiles();

        if (typeof initializeTitleValidation === 'function') {
            initializeTitleValidation();
        }

        $('#addBtn').on('click', function(e) {
            // Clear form for new training
            $('#mainForm')[0].reset();
            $('input[name="id"]').val('');
            $('#submitBtnText').text('Save');
            
            if (typeof quill !== 'undefined') {
                quill.setContents([]);
            }
            
            if (typeof clearUploadedFiles === 'function') {
                clearUploadedFiles();
            }
            
            clearFormErrors();
            
            $('#dashboardSection').hide();
            $('#formSection').show();

            // Reinitialize drag and drop events when form becomes visible
            initializeDragDropEvents();
        });

        $('#mainMenuBtn').on('click', function(e) {
            // Clear form when going back to dashboard
            $('#mainForm')[0].reset();
            $('input[name="id"]').val('');
            $('#submitBtnText').text('Save');
            $('#removedFiles').val('[]');
            
            if (typeof quill !== 'undefined') {
                quill.setContents([]);
            }
            
            if (typeof clearUploadedFiles === 'function') {
                clearUploadedFiles();
            }
            
            $('#fileList').empty();
            if (typeof updateDropAreaVisibility === 'function') {
                updateDropAreaVisibility();
            }
            clearFormErrors();
            
            $('#formSection').hide();
            $('#dashboardSection').show();
        });

        $(document).on('click', '.edit-training', function(e) {
            e.preventDefault();
            
            const trainingId = $(this).data('id');
            if (!trainingId) {
                alert('Training ID not found');
                return;
            }
            
            // Switch to form view first
            $('#dashboardSection').hide();
            $('#formSection').show();
            
            // Load training data via AJAX
            $.ajax({
                url: window.appConfig.baseUrl + 'ajax/load_training',
                type: 'POST',
                data: { id: trainingId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.training) {
                        populateFormWithTrainingData(response.training);
                    } else {
                        alert(response.message || 'Failed to load training data');
                        $('#formSection').hide();
                        $('#dashboardSection').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading training data:', error);
                    alert('Failed to load training data. Please try again.');
                    $('#formSection').hide();
                    $('#dashboardSection').show();
                }
            });
        });

        // Function to populate form with training data
        function populateFormWithTrainingData(training) {
            // Clear form first
            clearFormErrors();
            
            // Set form to edit mode
            $('#submitBtnText').text('Update');
            $('input[name="id"]').val(training.id);
            
            // Populate title
            $('#title').val(training.title || '');
            
            // Populate notes using Quill editor
            if (typeof quill !== 'undefined' && training.note) {
                quill.root.innerHTML = training.note;
            }
            
            // Handle existing files - display them in the file list
            if (training.file_names && training.file_names.length > 0) {
                displayExistingFiles(training.file_names, training.id);
            }
            
            // Reinitialize drag and drop events
            if (typeof initializeDragDropEvents === 'function') {
                initializeDragDropEvents();
            }
        }
        
        // Function to display existing files in edit mode
        function displayExistingFiles(fileNames, trainingId) {
            const fileList = $('#fileList');
            fileList.empty();
            
            fileNames.forEach(function(fileName, index) {
                const fileExtension = fileName.split('.').pop().toLowerCase();
                const fileIcon = getFileIcon(fileExtension);
                
                const fileItem = $(`
                    <div class="file-card existing-file" data-file-name="${fileName}" data-training-id="${trainingId}" data-file-index="${index}" style="opacity: 0; animation: slideInUp 0.4s ease-out ${index * 0.1}s forwards;">
                        <div class="file-icon">${fileIcon}</div>
                        <div class="file-info">
                            <div class="file-name" title="${fileName}">${fileName}</div>
                            <div class="file-size text-muted">Existing file</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-existing-file" title="Remove file">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                
                fileList.append(fileItem);
            });
            
            // Update drop area visibility
            updateDropAreaVisibility();
        }
        
        // Function to get file icon based on extension
        function getFileIcon(extension) {
            switch(extension.toLowerCase()) {
                case 'pdf':
                    return '<i class="fas fa-file-pdf text-danger"></i>';
                case 'ppt':
                case 'pptx':
                    return '<i class="fas fa-file-powerpoint text-warning"></i>';
                default:
                    return '<i class="fas fa-file"></i>';
            }
        }
        
        // Function to update drop area visibility
        function updateDropAreaVisibility() {
            const fileList = $('#fileList');
            const placeholder = $('#drop-area-placeholder');
            const dropArea = $('#dropArea');
            
            if (fileList.children().length > 0) {
                placeholder.addClass('d-none');
                dropArea.addClass('has-files');
            } else {
                placeholder.removeClass('d-none');
                dropArea.removeClass('has-files');
            }
        }
        
        // Handle removal of existing files
        $(document).on('click', '.remove-existing-file', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const fileItem = $(this).closest('.file-card');
            const fileName = fileItem.data('file-name');
            
            // Add to removed files list
            const removedFiles = JSON.parse($('#removedFiles').val() || '[]');
            removedFiles.push(fileName);
            $('#removedFiles').val(JSON.stringify(removedFiles));
            
            // Remove from UI
            fileItem.remove();
            updateDropAreaVisibility();
        });

        $('#submitBtn').on('click', function(e) {
            e.preventDefault();

            const submitBtn = $(this);
            const submitBtnText = $('#submitBtnText');
            const originalText = submitBtnText.text();

            submitBtn.prop('disabled', true);
            submitBtnText.text('Saving...');

            clearFormErrors();

            const formData = new FormData($('#mainForm')[0]);

            if (typeof quill !== 'undefined') {
                formData.append('notes', quill.root.innerHTML);
            }

            const trainingId = $('input[name="id"]').val();
            const isEdit = trainingId && trainingId.trim() !== '';
            const endpoint = isEdit ? 'ajax/edit' : 'ajax/add';

            $.ajax({
                url: window.appConfig.baseUrl + endpoint,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showBeautifulSuccessAlert(response.message, 'Training Saved Successfully!');

                        $('#mainForm')[0].reset();
                        if (typeof quill !== 'undefined') {
                            quill.setContents([]);
                        }

                        if (typeof clearUploadedFiles === 'function') {
                            clearUploadedFiles();
                        }

                        refreshDashboard();

                        $('#formSection').hide();
                        $('#dashboardSection').show();

                        $('#submitBtnText').text('Save');
                    } else {
                        if (response.errors) {
                            displayFormErrors(response.errors);
                        } else {
                            alert(response.message || 'An error occurred while saving.');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Form submission error:', error);
                    alert('An error occurred while saving. Please try again.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitBtnText.text(originalText);
                }
            });
        });

        function refreshDashboard() {
            const searchParams = {
                search: $('#search').val(),
                datetimes: $('#datetimes').val(),
                page: 1
            };

            $.ajax({
                url: window.appConfig.baseUrl + 'ajax/search',
                type: 'POST',
                data: searchParams,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateDashboardContent(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Dashboard refresh error:', error);
                    window.location.reload();
                }
            });
        }

        function updateDashboardContent(response) {
            const mainContent = $('#mainContent');

            if (response.trainings && response.trainings.length > 0) {
                let tableHtml = `
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
                            <tbody>`;

                response.trainings.forEach(function(training) {
                    const uploadedAt = training.created_at ?
                        new Date(training.created_at).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '';

                    let filesHtml = '';
                    if (training.file_names && training.file_names.length > 0) {
                        training.file_names.forEach(function(fileName, index) {
                            const extension = fileName.split('.').pop().toLowerCase();
                            let icon = '<i class="fas fa-file"></i>';
                            if (extension === 'pdf') {
                                icon = '<i class="fas fa-file-pdf text-danger"></i>';
                            } else if (extension === 'ppt' || extension === 'pptx') {
                                icon = '<i class="fas fa-file-powerpoint text-warning"></i>';
                            }

                            filesHtml += `
                                <a class="link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover file-preview-link d-inline-flex align-items-center"
                                   href="#"
                                   data-training-id="${training.id}"
                                   data-file-index="${index}"
                                   data-file-name="${fileName}"
                                   data-file-extension="${extension}"
                                   title="${fileName}"
                                   style="font-size: 1.25rem;">
                                    ${icon}
                                </a>`;
                        });
                    }

                    tableHtml += `
                        <tr>
                            <td class="align-middle">${training.title || ''}</td>
                            <td class="align-middle">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    ${filesHtml}
                                </div>
                            </td>
                            <td class="align-middle">Nath</td>
                            <td class="align-middle">${uploadedAt}</td>
                            <td class="align-middle">${training.note || ''}</td>
                            <td class="align-middle">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle rounded" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="d-flex align-items-center gap-2 dropdown-item text-primary edit-training" href="#" 
                                               data-id="${training.id}"
                                               data-title="${training.title || ''}"
                                               data-note="${training.note || ''}">
                                                <i class="fas fa-edit"></i>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="d-flex align-items-center gap-2 dropdown-item text-danger ajax-delete"
                                               href="#" data-id="${training.id}">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>`;
                });

                tableHtml += `
                            </tbody>
                        </table>
                    </div>`;

                mainContent.html(tableHtml);
            } else {
                mainContent.html(`
                    <div class="text-center">
                        <p class="fs-4 text-muted">No training manuals found.</p>
                        <p>Try adjusting your search terms or date range.</p>
                    </div>
                `);
            }

            if (response.pagination) {
                $('#paginationContainer').html(response.pagination);
            }
        }

        function displayFormErrors(errors) {
            clearFormErrors();

            for (const field in errors) {
                const errorElement = $('#' + field + 'Error');
                if (errorElement.length) {
                    errorElement.text(errors[field]).show();
                }

                if (field === 'file') {
                    $('#dropArea').addClass('error');
                } else {
                    $('#' + field + 'Group').addClass('error');
                }
            }
        }

        // Initialize drag and drop events
        initializeDragDropEvents();

        function initializeDragDropEvents() {
            console.log('Initializing drag and drop events...');

            // Remove any existing event handlers to prevent duplicates
            $('#dropArea').off('click dragenter dragover dragleave drop');
            $('#fileInput').off('change');

            $('#dropArea').on('click', function(e) {
                console.log('Drop area clicked');
                // Only trigger file input if clicking on the drop area itself or placeholder, not on file cards or buttons
                if ($(e.target).closest('.file-card').length === 0 && 
                    !$(e.target).hasClass('remove-existing-file') && 
                    !$(e.target).closest('.remove-existing-file').length) {
                    e.preventDefault();
                    $('#fileInput').click();
                }
            });

            $('#fileInput').on('change', function(e) {
                console.log('File input changed');
                const files = e.target.files;
                if (files.length > 0) {
                    const fileCount = files.length;
                    const fileText = fileCount === 1 ? 'file' : 'files';
                    showFileAlert(`Processing ${fileCount} ${fileText}...`, 'info');
                    handleFiles(files);
                }
            });

            let dragCounter = 0;
            let isDragActive = false;

            $('#dropArea').on('dragenter', function(e) {
                console.log('Drag enter - counter:', dragCounter);
                e.preventDefault();
                e.stopPropagation();

                dragCounter++;
                if (!isDragActive) {
                    isDragActive = true;
                    $(this).addClass('dragover');
                    $('#drop-area-placeholder p').first().text('Drop files here to upload');
                    $('#drop-area-placeholder .text-muted').text('Release to add files');
                }
            });

            $('#dropArea').on('dragover', function(e) {
                console.log('Drag over');
                e.preventDefault();
                e.stopPropagation();
                // Ensure dataTransfer is set to allow drop
                if (e.originalEvent.dataTransfer) {
                    e.originalEvent.dataTransfer.dropEffect = 'copy';
                }
            });

            $('#dropArea').on('dragleave', function(e) {
                console.log('Drag leave - counter before:', dragCounter, 'target:', e.target, 'currentTarget:', e.currentTarget);
                e.preventDefault();
                e.stopPropagation();

                // Only decrement if the mouse actually leaves the drop area, not child elements
                if (!$(this).is(e.relatedTarget) && !$(this).has(e.relatedTarget).length) {
                    dragCounter--;
                    console.log('Drag leave - counter after:', dragCounter);

                    if (dragCounter <= 0) {
                        dragCounter = 0;
                        isDragActive = false;
                        $(this).removeClass('dragover');
                        $('#drop-area-placeholder p').first().text('Drag and Drop files here');
                        $('#drop-area-placeholder .text-muted').text('or click to select a file');
                    }
                }
            });

            $('#dropArea').on('drop', function(e) {
                console.log('Drop event triggered');
                e.preventDefault();
                e.stopPropagation();

                // Reset drag state
                dragCounter = 0;
                isDragActive = false;
                $(this).removeClass('dragover');
                $('#drop-area-placeholder p').first().text('Drag and Drop files here');
                $('#drop-area-placeholder .text-muted').text('or click to select a file');

                const files = e.originalEvent.dataTransfer.files;
                console.log('Files dropped:', files.length);
                if (files.length > 0) {
                    const fileCount = files.length;
                    const fileText = fileCount === 1 ? 'file' : 'files';
                    showFileAlert(`Processing ${fileCount} ${fileText}...`, 'info');
                    handleFiles(files);
                }
            });

            // Add global drag events to handle edge cases
            $(document).on('dragenter dragover', function(e) {
                e.preventDefault();
            });

            $(document).on('drop', function(e) {
                // Prevent file from being opened in browser if dropped outside drop area
                if (!$(e.target).closest('#dropArea').length) {
                    e.preventDefault();
                }
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function getFileIcon(extension) {
            switch (extension.toLowerCase()) {
                case 'pdf':
                    return '<i class="fas fa-file-pdf text-danger"></i>';
                case 'ppt':
                case 'pptx':
                    return '<i class="fas fa-file-powerpoint text-warning"></i>';
                default:
                    return '<i class="fas fa-file"></i>';
            }
        }

        function renderFileList() {
            const fileListContainer = $('#fileList');
            fileListContainer.empty();

            const totalFiles = (existingFiles ? existingFiles.length : 0) + (currentFiles ? currentFiles.length : 0);
            if (totalFiles > 0) {
                $('#dropArea').addClass('has-files');
            } else {
                $('#dropArea').removeClass('has-files');
            }

            if (typeof existingFiles !== 'undefined' && existingFiles.length > 0) {
                existingFiles.forEach((fileName, index) => {
                    const extension = fileName.split('.').pop();
                    const fileCard = `
                        <div class="file-card existing-file" data-file-name="${fileName}" style="opacity: 0; animation: slideInUp 0.4s ease-out ${index * 0.1}s forwards;">
                            <div class="file-icon">
                                ${getFileIcon(extension)}
                            </div>
                            <div class="file-info">
                                <div class="file-name" title="${fileName}">${fileName}</div>
                                <div class="file-size text-muted">Existing file</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-type="existing" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    fileListContainer.append(fileCard);
                });
            }

            if (typeof currentFiles !== 'undefined' && currentFiles.length > 0) {
                currentFiles.forEach((file, index) => {
                    const extension = file.name.split('.').pop();
                    const delay = (existingFiles ? existingFiles.length : 0) * 0.1 + index * 0.1;
                    const fileCard = `
                        <div class="file-card new-file file-upload-success" data-file-name="${file.name}" style="opacity: 0; animation: slideInUp 0.4s ease-out ${delay}s forwards;">
                            <div class="file-icon">
                                ${getFileIcon(extension)}
                            </div>
                            <div class="file-info">
                                <div class="file-name" title="${file.name}">${file.name}</div>
                                <div class="file-size text-muted">${formatFileSize(file.size)}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-type="current" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    fileListContainer.append(fileCard);
                });
            }

            $('.remove-file').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const $fileCard = $(this).closest('.file-card');
                const fileName = $fileCard.data('file-name');
                const type = $(this).data('type');
                const index = $(this).data('index');

                $fileCard.css('animation', 'slideOutRight 0.3s ease-in forwards');

                setTimeout(() => {
                    if (type === 'existing') {
                        if (typeof removedExistingFiles === 'undefined') removedExistingFiles = [];
                        removedExistingFiles.push(existingFiles[index]);
                        existingFiles.splice(index, 1);

                        if (typeof updateRemovedFilesInput === 'function') {
                            updateRemovedFilesInput();
                        }

                        showFileAlert(`Removed "${fileName}" from existing files`, 'info');
                    } else if (type === 'current') {
                        currentFiles.splice(index, 1);

                        const dt = new DataTransfer();
                        currentFiles.forEach(file => dt.items.add(file));
                        document.getElementById('fileInput').files = dt.files;

                        showFileAlert(`Removed "${fileName}" from upload queue`, 'info');
                    }

                    renderFileList();

                    const totalFiles = (existingFiles ? existingFiles.length : 0) + (currentFiles ? currentFiles.length : 0);
                    if (totalFiles === 0) {
                        $('#drop-area-placeholder').removeClass('d-none');
                    }
                }, 300);
            });
        }

        function updateRemovedFilesInput() {
            if (typeof removedExistingFiles !== 'undefined') {
                $('#removedFiles').val(JSON.stringify(removedExistingFiles));
            }
        }

        // Delete training handler
        $(document).on('click', '.ajax-delete', function(e) {
            e.preventDefault();
            
            const trainingId = $(this).data('id');
            if (!trainingId) {
                alert('Training ID not found');
                return;
            }
            
            // Show confirmation modal
            $('#confirmationMessage').text('Are you sure you want to delete this training manual? This action cannot be undone.');
            $('#confirmationModal').modal('show');
            
            // Store the training ID for confirmation
            $('#confirmActionBtn').data('training-id', trainingId);
        });
        
        // Handle confirmation modal confirm button
        $('#confirmActionBtn').on('click', function() {
            const trainingId = $(this).data('training-id');
            if (!trainingId) return;
            
            const confirmBtn = $(this);
            const originalText = confirmBtn.text();
            
            confirmBtn.prop('disabled', true).text('Deleting...');
            
            $.ajax({
                url: window.appConfig.baseUrl + 'ajax/delete',
                type: 'POST',
                data: { id: trainingId },
                dataType: 'json',
                success: function(response) {
                    $('#confirmationModal').modal('hide');
                    
                    if (response.success) {
                        // Show success message
                        showBeautifulSuccessAlert(response.message || 'Training manual deleted successfully!', 'Deleted Successfully!');
                        
                        // Refresh the training list
                        refreshDashboard();
                    } else {
                        alert(response.message || 'Failed to delete training manual');
                    }
                },
                error: function(xhr, status, error) {
                    $('#confirmationModal').modal('hide');
                    console.error('Delete error:', error);
                    alert('An error occurred while deleting the training manual. Please try again.');
                },
                complete: function() {
                    confirmBtn.prop('disabled', false).text(originalText);
                    $('#confirmActionBtn').removeData('training-id');
                }
            });
        });

        // Pagination click handler for AJAX pagination
        $(document).on('click', '.ajax-page', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            const searchParams = {
                search: $('#search').val(),
                datetimes: $('#datetimes').val(),
                page: page
            };

            // Show loading indicator
            $('#loadingIndicator').show();
            $('#mainContent').hide();

            $.ajax({
                url: window.appConfig.baseUrl + 'ajax/search',
                type: 'POST',
                data: searchParams,
                dataType: 'json',
                success: function(response) {
                    $('#loadingIndicator').hide();
                    $('#mainContent').show();

                    if (response.success) {
                        updateDashboardContent(response);
                    } else {
                        console.error('Pagination error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingIndicator').hide();
                    $('#mainContent').show();
                    console.error('Pagination AJAX error:', error);
                }
            });
        });

        // Pagination click handler for standard CodeIgniter pagination links
        $(document).on('click', '#paginationContainer .page-link:not(.ajax-page)', function(e) {
            e.preventDefault();

            const url = $(this).attr('href');
            if (!url || url === '#') return;

            // Extract page number from URL
            const urlParams = new URLSearchParams(url.split('?')[1]);
            const page = urlParams.get('page') || 1;

            const searchParams = {
                search: $('#search').val(),
                datetimes: $('#datetimes').val(),
                page: page
            };

            // Show loading indicator
            $('#loadingIndicator').show();
            $('#mainContent').hide();

            $.ajax({
                url: window.appConfig.baseUrl + 'ajax/search',
                type: 'POST',
                data: searchParams,
                dataType: 'json',
                success: function(response) {
                    $('#loadingIndicator').hide();
                    $('#mainContent').show();

                    if (response.success) {
                        updateDashboardContent(response);
                    } else {
                        console.error('Pagination error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingIndicator').hide();
                    $('#mainContent').show();
                    console.error('Pagination AJAX error:', error);
                }
            });
        });

        // Centralized AJAX filter function
        window.performAjaxFilter = function() {
            console.log('performAjaxFilter called');
            const searchParams = {
                search: $('#search').val(),
                datetimes: $('#datetimes').val(),
                page: 1
            };
            
            console.log('Search params:', searchParams);

            // Show loading indicator
            $('#loadingIndicator').show();
            $('#mainContent').hide();

            $.ajax({
                url: window.appConfig.baseUrl + 'ajax/search',
                type: 'POST',
                data: searchParams,
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX response:', response);
                    $('#loadingIndicator').hide();
                    $('#mainContent').show();

                    if (response.success) {
                        updateDashboardContent(response);
                        
                        if (searchParams.search || searchParams.datetimes) {
                            $('#clearBtn').removeClass('d-none');
                        } else {
                            $('#clearBtn').addClass('d-none');
                        }
                    } else {
                        console.error('Filter error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingIndicator').hide();
                    $('#mainContent').show();
                    console.error('Filter AJAX error:', error);
                    console.error('XHR response:', xhr.responseText);
                }
            });
        }

        $('#filterBtn').on('click', function(e) {
            e.preventDefault();
            window.performAjaxFilter();
        });

        $('#clearBtn').on('click', function(e) {
            e.preventDefault();
            
            $('#search').val('');
            $('#datetimes').val('');
            
            clearTimeout(searchTimeout);
            
            $(this).addClass('d-none');
            
            window.performAjaxFilter();
        });

        let searchTimeout;
        $('#search').on('input', function(e) {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(function() {
                window.performAjaxFilter();
            }, 300);
        });

        $('#search').on('keypress', function(e) {
            if (e.which === 13) {
                clearTimeout(searchTimeout); // Cancel any pending search
                window.performAjaxFilter();
            }
        });

        function checkInitialFilters() {
            const searchValue = $('#search').val();
            const datetimesValue = $('#datetimes').val();
            
            if (searchValue || datetimesValue) {
                $('#clearBtn').removeClass('d-none');
            }
        }
        
        checkInitialFilters();

        window.renderFileList = renderFileList;
        window.updateRemovedFilesInput = updateRemovedFilesInput;

    });
</script>
</div>

</body>

</html>