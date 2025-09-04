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
            const box = document.createElement("div");
            box.className = "file-box border rounded-3 p-3 text-center shadow-sm";

            const ext = file.name.split('.').pop().toUpperCase();

            let sizeText;
            if (file.size < 1024 * 1024) {
                sizeText = (file.size / 1024).toFixed(1) + " KB";
            } else {
                sizeText = (file.size / (1024 * 1024)).toFixed(2) + " MB";
            }

            const nameOnly = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
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
            fileList.appendChild(box);

            if (dropArea.classList.contains("error")) {
                dropArea.classList.remove("error");
            }

            dropAreaPlaceholder.classList.add("d-none");
        });
    }

    function restoreUploadedFiles() {
        if (window.uploadedFilesData && window.uploadedFilesData.length > 0) {
            fileList.innerHTML = "";
            
            window.uploadedFilesData.forEach(file => {
                const box = document.createElement("div");
                box.className = "file-box border rounded-3 p-3 text-center shadow-sm";

                const ext = file.name.split('.').pop().toUpperCase();

                let sizeText;
                if (file.size < 1024 * 1024) {
                    sizeText = (file.size / 1024).toFixed(1) + " KB";
                } else {
                    sizeText = (file.size / (1024 * 1024)).toFixed(2) + " MB";
                }

                const nameOnly = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
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
                fileList.appendChild(box);
            });
            
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