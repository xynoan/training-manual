                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
                <script>
                    const dropArea = document.getElementById("dropArea");
                    const fileInput = document.getElementById("fileInput");
                    const fileName = document.getElementById("fileName");

                    dropArea.addEventListener("click", () => fileInput.click());

                    dropArea.addEventListener("dragover", (e) => {
                        e.preventDefault();
                        dropArea.classList.add("dragover");
                    });

                    dropArea.addEventListener("dragleave", () => {
                        dropArea.classList.remove("dragover");
                    });

                    dropArea.addEventListener("drop", (e) => {
                        e.preventDefault();
                        dropArea.classList.remove("dragover");
                        if (e.dataTransfer.files.length) {
                            fileInput.files = e.dataTransfer.files;
                            showFileName(fileInput.files[0].name);
                        }
                    });

                    fileInput.addEventListener("change", () => {
                        if (fileInput.files.length) {
                            showFileName(fileInput.files[0].name);
                        }
                    });

                    function showFileName(name) {
                        fileName.textContent = "Selected file: " + name;
                    }
                </script>
                </body>

                </html>