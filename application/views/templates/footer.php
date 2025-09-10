<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
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

        $('#dropArea').removeClass('error');
        $('#drop-area-placeholder').addClass('d-none');
    }

    function restoreUploadedFiles() {
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
            $('#dropArea').removeClass('error');
            $('#drop-area-placeholder').addClass('d-none');
        }
    }

    $(document).ready(function() {
        restoreUploadedFiles();
    });

    function showFloatingAlert() {
        $('#floatingAlert').addClass('show');

        setTimeout(() => {
            $('#floatingAlert').removeClass('show');
            window.location.href = '<?= isset($base_url) ? $base_url : '/' ?>';
        }, 4000);
    }

    $(document).ready(function() {
        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            $('#mainForm')[0].reset();
            $('input[name="id"]').val('');
            $('#submitBtnText').text('Save');
            $('#submitBtn').removeClass().addClass('btn btn-primary d-flex align-items-center gap-1');

            clearFormErrors();

            $('#dashboardSection').hide();
            $('#formSection').show();
        });

        $('#mainMenuBtn').on('click', function(e) {
            e.preventDefault();

            $('#formSection').hide();
            $('#dashboardSection').show();
        });

        $(document).on('click', '.dropdown-item:contains("Edit")', function(e) {
            e.preventDefault();

            $('#submitBtnText').text('Update');
            $('#submitBtn').removeClass().addClass('btn btn-success d-flex align-items-center gap-1');

            clearFormErrors();

            $('#dashboardSection').hide();
            $('#formSection').show();
        });

        function clearFormErrors() {
            $('#titleError, #fileError, #notesError').hide();
            $('#title, #notes').removeClass('is-invalid');
            $('#dropArea').removeClass('error');
        }
    });
</script>
</div>

</body>

</html>