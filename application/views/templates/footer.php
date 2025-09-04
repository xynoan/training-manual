<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script>
    const dropAreaPlaceholder = document.getElementById("drop-area-placeholder");
    const dropArea = document.getElementById("dropArea");
    const fileInput = document.getElementById("fileInput");
    const fileList = document.getElementById("fileList");

    const maxFiles = 5;
    const maxSizeMB = 100;
    const allowedTypes = ["pdf", "ppt", "pptx"];

    dropArea.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", () => handleFiles(fileInput.files));

    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("dragover");
    });

    dropArea.addEventListener("dragleave", () => dropArea.classList.remove("dragover"));

    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragover");

        const files = e.dataTransfer.files;
        handleFiles(files);
        fileInput.files = files;
    });

    // helper functions
    function clearFiles() {
        fileInput.value = "";
        fileList.innerHTML = "";
        dropAreaPlaceholder.classList.remove("d-none");
    }

    function createFileBox(file) {
        const box = document.createElement("div");
        
        // Check if file is a File object or just a filename string
        const isFileObject = file && typeof file === 'object' && file.name;
        const fileName = isFileObject ? file.name : file;
        
        // Set different styling for existing files vs new files
        box.className = isFileObject ? 
            "file-box border rounded-3 p-3 text-center shadow-sm" :
            "file-box border rounded-3 p-3 text-center shadow-sm bg-light";

        const ext = fileName.split('.').pop().toUpperCase();
        const nameOnly = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;

        if (isFileObject) {
            // Handle File object (new uploads)
            let sizeText;
            if (file.size < 1024 * 1024) {
                sizeText = (file.size / 1024).toFixed(1) + " KB";
            } else {
                sizeText = (file.size / (1024 * 1024)).toFixed(2) + " MB";
            }

            const maxLength = 18;
            let displayName = nameOnly.length > maxLength ?
                nameOnly.substring(0, maxLength) + "..." :
                nameOnly;

            const typeEl = document.createElement("div");
            typeEl.className = "fw-bold text-secondary mb-1";
            typeEl.textContent = ext;

            const sizeEl = document.createElement("div");
            sizeEl.className = "fw-bold text-primary mb-1";
            sizeEl.textContent = sizeText;

            const nameEl = document.createElement("div");
            nameEl.className = "small text-muted";
            nameEl.textContent = displayName;

            box.appendChild(typeEl);
            box.appendChild(sizeEl);
            box.appendChild(nameEl);
        } else {
            // Handle filename string (existing files)
            box.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text text-primary mb-2" viewBox="0 0 16 16">
                        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5.5 9a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5.5 11a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                    </svg>
                    <div class="text-truncate w-100" title="${fileName}">
                        <strong>${nameOnly}</strong>
                    </div>
                    <small class="text-muted">${ext}</small>
                    <small class="text-success">Existing File</small>
                </div>
            `;
        }

        return box;
    }


    function handleFiles(files) {
        /* error handlers */
        if (files.length > maxFiles) {
            alert(`You can only upload a maximum of ${maxFiles} files.`);
            clearFiles();
            return;
        }

        for (const file of files) {
            const ext = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(ext)) {
                alert(`Invalid file type: "${file.name}". Only PDF and PPT files are allowed.`);
                clearFiles();
                return;
            }

            if (file.size > maxSizeMB * 1024 * 1024) {
                alert(`File "${file.name}" exceeds ${maxSizeMB} MB.`);
                clearFiles();
                return;
            }
        }
        /* end of error handlers */

        fileList.innerHTML = "";
        Array.from(files).forEach(file => {
            const box = createFileBox(file);
            fileList.appendChild(box);
        });
        
        if (dropArea.classList.contains("error")) {
            dropArea.classList.remove("error");
        }
        
        dropAreaPlaceholder.classList.add("d-none");
    }

    function restoreUploadedFiles() {
        let hasFiles = false;
        fileList.innerHTML = "";
        
        // Display existing files from database (for edit mode)
        if (window.existingFilesData && window.existingFilesData.length > 0) {
            window.existingFilesData.forEach(fileName => {
                if (fileName && fileName.trim()) {
                    const box = createFileBox(fileName.trim());
                    fileList.appendChild(box);
                    hasFiles = true;
                }
            });
        }
        
        // Display newly uploaded files from session
        if (window.uploadedFilesData && window.uploadedFilesData.length > 0) {
            window.uploadedFilesData.forEach(file => {
                const box = createFileBox(file);
                fileList.appendChild(box);
                hasFiles = true;
            });
        }
        
        if (hasFiles) {
            if (dropArea.classList.contains("error")) {
                dropArea.classList.remove("error");
            }
            dropAreaPlaceholder.classList.add("d-none");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        restoreUploadedFiles();
    });

    function showFloatingAlert() {
        const alert = document.getElementById('floatingAlert');
        alert.classList.add('show');

        setTimeout(() => {
            alert.classList.remove('show');
        }, 4000);
    }
</script>


</body>

</html>