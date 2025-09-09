<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    const dropAreaPlaceholder = document.getElementById("drop-area-placeholder");
    const dropArea = document.getElementById("dropArea");
    const fileInput = document.getElementById("fileInput");
    const fileList = document.getElementById("fileList");

    const maxFiles = 5;
    const maxSizeMB = 100;
    const allowedTypes = ["pdf", "ppt", "pptx"];

    let currentFiles = []; 
    let existingFiles = []; 
    let removedExistingFiles = []; 

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
        currentFiles = [];
        dropAreaPlaceholder.classList.remove("d-none");
    }

    function removeFile(index, isNewFile) {
        if (isNewFile) {
            const actualIndex = index - existingFiles.length;
            if (actualIndex >= 0 && actualIndex < currentFiles.length) {
                currentFiles.splice(actualIndex, 1);
                updateFileInput();
            }
        } else {
            const fileName = existingFiles[index];
            if (fileName && !removedExistingFiles.includes(fileName)) {
                removedExistingFiles.push(fileName);
                updateRemovedFilesInput();
            }
            existingFiles.splice(index, 1);
        }
        
        renderFileList();
        
        if (currentFiles.length === 0 && existingFiles.length === 0) {
            dropAreaPlaceholder.classList.remove("d-none");
        }
    }

    function updateRemovedFilesInput() {
        const removedFilesInput = document.getElementById('removedFiles');
        if (removedFilesInput) {
            removedFilesInput.value = JSON.stringify(removedExistingFiles);
        }
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        currentFiles.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
    }

    function renderFileList() {
        fileList.innerHTML = "";
        let index = 0;
        
        existingFiles.forEach((fileName, i) => {
            if (fileName && fileName.trim()) {
                const box = createFileBox(fileName.trim(), index, false);
                fileList.appendChild(box);
                index++;
            }
        });
        
        currentFiles.forEach((file, i) => {
            const box = createFileBox(file, index, true);
            fileList.appendChild(box);
            index++;
        });
    }

    function createFileBox(file, index, isNewFile = null) {
        const box = document.createElement("div");
        box.className = "position-relative";
        
        const isFileObject = isNewFile !== null ? isNewFile : (file && typeof file === 'object' && file.name);
        const fileName = isFileObject ? file.name : file;
        
        const fileBox = document.createElement("div");
        fileBox.className = isFileObject ? 
            "file-box border rounded-3 p-3 text-center shadow-sm" :
            "file-box border rounded-3 p-3 text-center shadow-sm bg-light";

        const ext = fileName.split('.').pop().toUpperCase();
        const nameOnly = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.className = "btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1";
        removeBtn.style.zIndex = "10";
        removeBtn.innerHTML = `
            <i class="fas fa-times removeFile" style="font-size: 12px;"></i>
        `;
        
        removeBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            removeFile(index, isFileObject);
        });

        if (isFileObject && file.uploading) {
            const progressBar = document.createElement("div");
            progressBar.className = "progress mt-2";
            progressBar.style.height = "4px";
            progressBar.innerHTML = `
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 0%" 
                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            `;
            fileBox.appendChild(progressBar);
        }

        if (isFileObject) {
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

            fileBox.appendChild(typeEl);
            fileBox.appendChild(sizeEl);
            fileBox.appendChild(nameEl);
        } else {
            fileBox.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-file-alt text-primary mb-2" style="font-size: 24px;"></i>
                    <div class="text-truncate w-100" title="${fileName}">
                        <strong>${nameOnly}</strong>
                    </div>
                    <small class="text-muted">${ext}</small>
                    <small class="text-success">Existing File</small>
                </div>
            `;
        }

        box.appendChild(fileBox);
        box.appendChild(removeBtn);
        return box;
    }


    function handleFiles(files) {
        /* error handlers */
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
        
        renderFileList();
        
        if (dropArea.classList.contains("error")) {
            dropArea.classList.remove("error");
        }
        
        dropAreaPlaceholder.classList.add("d-none");
    }

    function restoreUploadedFiles() {
        existingFiles = [];
        currentFiles = [];
        removedExistingFiles = [];
        
        if (window.removedFilesData && window.removedFilesData.length > 0) {
            removedExistingFiles = window.removedFilesData.map(fileName => fileName.trim());
        }
        
        if (window.existingFilesData && window.existingFilesData.length > 0) {
            existingFiles = window.existingFilesData.filter(fileName => {
                const trimmedName = fileName && fileName.trim();
                return trimmedName && !removedExistingFiles.includes(trimmedName);
            });
        }
        
        if (window.uploadedFilesData && window.uploadedFilesData.length > 0) {
            currentFiles = window.uploadedFilesData || [];
        }
        
        updateRemovedFilesInput();
        
        renderFileList();
        
        const hasFiles = existingFiles.length > 0 || currentFiles.length > 0;
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
            window.open(base_url(), '_self');
        }, 4000);
    }
</script>


</body>

</html>