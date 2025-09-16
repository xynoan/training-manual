let notesAutoSave = {
    saveTimeout: null,
    isEditMode: false,
    trainingId: null,
    saveDelay: 2000, // 2 seconds
    isSaving: false,

    init: function() {
        this.setupQuillListener();
        this.setupFormListener();
        this.createSaveIndicator();
    },

    setupQuillListener: function() {
        if (typeof quill !== 'undefined') {
            quill.on('text-change', (delta, oldDelta, source) => {
                if (source === 'user') {
                    this.scheduleAutoSave();
                }
            });
        }
    },

    setupFormListener: function() {
        const idField = document.querySelector('input[name="id"]');
        if (idField && idField.value) {
            this.isEditMode = true;
            this.trainingId = idField.value;
        }

        if (idField) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        this.trainingId = idField.value;
                        this.isEditMode = !!idField.value;
                    }
                });
            });
            observer.observe(idField, { attributes: true });
        }
    },

    createSaveIndicator: function() {
        const notesGroup = document.getElementById('notesGroup');
        if (notesGroup) {
            const indicator = document.createElement('div');
            indicator.id = 'notesSaveIndicator';
            indicator.className = 'text-muted small mt-1';
            indicator.style.display = 'none';
            indicator.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Saving notes...';
            notesGroup.appendChild(indicator);
        }
    },


    scheduleAutoSave: function() {
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }

        this.saveTimeout = setTimeout(() => {
            this.saveNotes();
        }, this.saveDelay);
    },

    saveNotes: function() {
        if (this.isSaving || typeof quill === 'undefined') {
            return;
        }

        const notesContent = quill.root.innerHTML;
        
        // Check if content is empty or contains only empty paragraphs
        if (!notesContent || notesContent.trim() === '') {
            return;
        }
        
        // Remove all HTML tags and check if there's actual text content
        const textContent = notesContent.replace(/<[^>]*>/g, '').trim();
        if (textContent === '') {
            return;
        }
        
        // Also check for content that's only empty paragraphs (multiple <p><br></p> tags)
        const cleanContent = notesContent.replace(/<p><br><\/p>/g, '').trim();
        if (cleanContent === '' || cleanContent === '<p></p>') {
            return;
        }

        this.isSaving = true;
        this.showSaveIndicator();

        const formData = new FormData();
        formData.append('notes', notesContent);
        
        if (this.isEditMode && this.trainingId) {
            formData.append('id', this.trainingId);
        }

        fetch(window.appConfig ? window.appConfig.baseUrl + 'ajax/save_notes' : '/ajax/save_notes', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.isSaving = false;
            this.hideSaveIndicator();
            
            if (data.success) {
                this.showSaveSuccess();
                console.log('Notes saved:', data.message);
            } else {
                this.showSaveError(data.message || 'Failed to save notes');
                console.error('Save failed:', data.message);
            }
        })
        .catch(error => {
            this.isSaving = false;
            this.hideSaveIndicator();
            this.showSaveError('Network error occurred while saving notes');
            console.error('Save error:', error);
        });
    },

    showSaveIndicator: function() {
        const indicator = document.getElementById('notesSaveIndicator');
        if (indicator) {
            indicator.style.display = 'block';
            indicator.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Saving notes...';
        }
    },

    hideSaveIndicator: function() {
        const indicator = document.getElementById('notesSaveIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    },

    showSaveSuccess: function() {
        const indicator = document.getElementById('notesSaveIndicator');
        if (indicator) {
            indicator.innerHTML = '<i class="fas fa-check text-success"></i> Notes saved';
            indicator.style.display = 'block';
            
            setTimeout(() => {
                this.hideSaveIndicator();
            }, 2000);
        }
    },

    showSaveError: function(message) {
        const indicator = document.getElementById('notesSaveIndicator');
        if (indicator) {
            indicator.innerHTML = `<i class="fas fa-exclamation-triangle text-danger"></i> ${message}`;
            indicator.style.display = 'block';
            
            setTimeout(() => {
                this.hideSaveIndicator();
            }, 5000);
        }
    },

    loadTempNotes: function() {
        if (typeof quill !== 'undefined') {
            if (window.existingNotes) {
                quill.root.innerHTML = window.existingNotes;
            }
            else if (window.tempNotes) {
                quill.root.innerHTML = window.tempNotes;
            }
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        notesAutoSave.init();
        notesAutoSave.loadTempNotes();
    }, 500);
});

window.notesAutoSave = notesAutoSave;