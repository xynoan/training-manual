<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="filePreviewContent" style="height: 600px;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="downloadFileBtn" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download"></i>
                    Download File
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>