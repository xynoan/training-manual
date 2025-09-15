<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>initialize_quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>helper_functions.js"></script>
<script>
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


    });
</script>
</div>

</body>

</html>