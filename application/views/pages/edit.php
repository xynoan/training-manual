<style>
    .upload-box {
        border: 2px dashed #d3d3d3;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .upload-box.dragover {
        border-color: #0d6efd;
        background: #f0f8ff;
    }

    .upload-box svg {
        width: 50px;
        height: 50px;
        fill: #adb5bd;
    }

    .upload-box p {
        margin-top: 10px;
        color: #adb5bd;
        font-weight: 500;
    }

    #fileInput {
        display: none;
    }
</style>


<div class="d-flex gap-2">
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy2" viewBox="0 0 16 16">
            <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v3.5A1.5 1.5 0 0 1 11.5 6h-7A1.5 1.5 0 0 1 3 4.5V1H1.5a.5.5 0 0 0-.5.5m9.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5z" />
        </svg>
        Submit
    </button>
    <a href="/" class="btn btn-danger d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-skip-backward-fill" viewBox="0 0 16 16">
            <path d="M.5 3.5A.5.5 0 0 0 0 4v8a.5.5 0 0 0 1 0V8.753l6.267 3.636c.54.313 1.233-.066 1.233-.697v-2.94l6.267 3.636c.54.314 1.233-.065 1.233-.696V4.308c0-.63-.693-1.01-1.233-.696L8.5 7.248v-2.94c0-.63-.692-1.01-1.233-.696L1 7.248V4a.5.5 0 0 0-.5-.5" />
        </svg>
        Main Menu
    </a>
</div>
</div>
<label for="title">
    <p class="fs-4 text-danger">Title</p>
</label>
<div class="w-50">
    <input type="text" class="form-control" id="title">
</div>
<div class="w-50 my-3">
    <label class="form-label fs-4 text-danger mb-3">Upload File</label>
    <div class="upload-box" id="dropArea">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-up" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708z" />
            <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z" />
        </svg>
        <p>Drag and Drop files here</p>
        <p class="text-muted">or click to select a file</p>
    </div>
    <input type="file" id="fileInput" />
    <p id="fileName" class="mt-2 text-secondary"></p>
</div>
<label for="notes">
    <p class="fs-4 text-danger">Notes</p>
</label>
<div class="w-50">
    <textarea class="form-control" id="notes"></textarea>
</div>