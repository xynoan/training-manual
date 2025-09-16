<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/initialize_quill.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/notes_auto_save.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/helper_functions.js"></script>
<script>
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
            $('#dashboardSection').hide();
            $('#formSection').show();
        });

        $('#mainMenuBtn').on('click', function(e) {
            $('#formSection').hide();
            $('#dashboardSection').show();
        });

        $(document).on('click', '.dropdown-item:contains("Edit")', function(e) {
            $('#submitBtnText').text('Update');
            $('#dashboardSection').hide();
            $('#formSection').show();
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
                        // Show beautiful success alert
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
                dates: $('#dates').val(),
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

        $('#dropArea').on('click', function(e) {
            e.preventDefault();
            $('#fileInput').click();
        });

        $('#fileInput').on('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                const fileCount = files.length;
                const fileText = fileCount === 1 ? 'file' : 'files';
                showFileAlert(`Processing ${fileCount} ${fileText}...`, 'info');
                handleFiles(files);
            }
        });

        let dragCounter = 0;
        
        $('#dropArea').on('dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dragCounter++;
            $(this).addClass('dragover');
            $('#drop-area-placeholder p').first().text('Drop files here to upload');
            $('#drop-area-placeholder .text-muted').text('Release to add files');
        });

        $('#dropArea').on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $('#dropArea').on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dragCounter--;
            if (dragCounter === 0) {
                $(this).removeClass('dragover');
                $('#drop-area-placeholder p').first().text('Drag and Drop files here');
                $('#drop-area-placeholder .text-muted').text('or click to select a file');
            }
        });

        $('#dropArea').on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dragCounter = 0;
            $(this).removeClass('dragover');
            $('#drop-area-placeholder p').first().text('Drag and Drop files here');
            $('#drop-area-placeholder .text-muted').text('or click to select a file');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                const fileCount = files.length;
                const fileText = fileCount === 1 ? 'file' : 'files';
                showFileAlert(`Processing ${fileCount} ${fileText}...`, 'info');
                handleFiles(files);
            }
        });

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

        window.renderFileList = renderFileList;
        window.updateRemovedFilesInput = updateRemovedFilesInput;

    });
</script>
</div>

</body>

</html>