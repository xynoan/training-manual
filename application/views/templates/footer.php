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

        // Handle form submission
        $('#submitBtn').on('click', function(e) {
            e.preventDefault();
            
            const submitBtn = $(this);
            const submitBtnText = $('#submitBtnText');
            const originalText = submitBtnText.text();
            
            // Disable button and show loading state
            submitBtn.prop('disabled', true);
            submitBtnText.text('Saving...');
            
            // Clear any previous errors
            clearFormErrors();
            
            // Get form data
            const formData = new FormData($('#mainForm')[0]);
            
            // Add notes content from Quill editor
            if (typeof quill !== 'undefined') {
                formData.append('notes', quill.root.innerHTML);
            }
            
            // Determine if this is an edit or add operation
            const trainingId = $('input[name="id"]').val();
            const isEdit = trainingId && trainingId.trim() !== '';
            const endpoint = isEdit ? 'ajax/edit' : 'ajax/add';
            
            // Submit form via AJAX
            $.ajax({
                url: window.appConfig.baseUrl + endpoint,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(response.message);
                        
                        // Clear form
                        $('#mainForm')[0].reset();
                        if (typeof quill !== 'undefined') {
                            quill.setContents([]);
                        }
                        
                        // Reset file uploads
                        if (typeof clearUploadedFiles === 'function') {
                            clearUploadedFiles();
                        }
                        
                        // Refresh dashboard content
                        refreshDashboard();
                        
                        // Switch back to dashboard view
                        $('#formSection').hide();
                        $('#dashboardSection').show();
                        
                        // Reset button text for next use
                        $('#submitBtnText').text('Save');
                    } else {
                        // Handle validation errors
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
                    // Re-enable button and restore text
                    submitBtn.prop('disabled', false);
                    submitBtnText.text(originalText);
                }
            });
        });

        // Function to refresh dashboard content
        function refreshDashboard() {
            // Get current search parameters
            const searchParams = {
                search: $('#search').val(),
                dates: $('#dates').val(),
                page: 1 // Reset to first page after adding/editing
            };
            
            // Call the search function to refresh dashboard
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
                    // Fallback: reload the page
                    window.location.reload();
                }
            });
        }
        
        // Function to update dashboard content
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
            
            // Update pagination
            if (response.pagination) {
                $('#paginationContainer').html(response.pagination);
            }
        }
        
        // Function to display form validation errors
        function displayFormErrors(errors) {
            // Clear previous errors first
            clearFormErrors();
            
            for (const field in errors) {
                const errorElement = $('#' + field + 'Error');
                if (errorElement.length) {
                    errorElement.text(errors[field]).show();
                }
                
                // Add error class to form groups
                if (field === 'file') {
                    $('#dropArea').addClass('error');
                } else {
                    $('#' + field + 'Group').addClass('error');
                }
            }
        }

        // File upload event handlers
        $('#dropArea').on('click', function(e) {
            e.preventDefault();
            $('#fileInput').click();
        });

        $('#fileInput').on('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                handleFiles(files);
            }
        });

        // Drag and drop handlers
        $('#dropArea').on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });

        $('#dropArea').on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });

        $('#dropArea').on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                handleFiles(files);
            }
        });

    });
</script>
</div>

</body>

</html>