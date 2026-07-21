// public/assets/js/modal_handlers.js

class ModalHandler {
    constructor() {
        this.initialized = false;
        this.modalState = {
            foto: false,
            cv: false,
            ktp: false
        };
        this.setupComplete = false;
    }

    init() {
        if (this.initialized) {
            console.log('[ModalHandler] Already initialized');
            return;
        }

        console.log('[ModalHandler] Initializing...');
        
        // Setup hanya sekali
        this.setupEventListeners();
        this.setupModalCloseHandlers();
        this.setupFormSubmitHandlers();
        
        this.initialized = true;
        console.log('[ModalHandler] Initialization complete');
    }

    setupEventListeners() {
        if (this.setupComplete) return;
        
        // Foto Upload
        this.setupUploadHandler('foto', ['image/jpeg', 'image/png'], 2);
        
        // CV Upload
        this.setupUploadHandler('cv', [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ], 5);
        
        // KTP Upload
        this.setupUploadHandler('ktp', [
            'image/jpeg', 
            'image/png', 
            'application/pdf'
        ], 2);
        
        this.setupComplete = true;
    }

    setupUploadHandler(type, allowedTypes, maxSizeMB) {
        const input = document.getElementById(`${type}Input`);
        const preview = document.getElementById(`${type}Preview`);
        
        if (!input || !preview) {
            console.warn(`[ModalHandler] ${type} elements not found`);
            return;
        }

        // Remove existing listeners
        const newInput = input.cloneNode(true);
        const newPreview = preview.cloneNode(true);
        
        input.parentNode.replaceChild(newInput, input);
        preview.parentNode.replaceChild(newPreview, preview);

        // Get new elements
        const freshInput = document.getElementById(`${type}Input`);
        const freshPreview = document.getElementById(`${type}Preview`);

        // Add fresh listeners
        freshInput.addEventListener('change', (e) => {
            this.handleFileSelect(e, type, allowedTypes, maxSizeMB);
        });

        freshPreview.addEventListener('click', (e) => {
            e.preventDefault();
            freshInput.click();
        });

        this.setupDragAndDrop(freshPreview, freshInput, type);
        
        console.log(`[ModalHandler] ${type} upload handler setup`);
    }

    handleFileSelect(e, type, allowedTypes, maxSizeMB) {
        const file = e.target.files[0];
        if (!file) return;

        // Validation
        if (file.size > maxSizeMB * 1024 * 1024) {
            this.showAlert(`Ukuran file maksimal ${maxSizeMB}MB`, 'danger');
            e.target.value = '';
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            this.showAlert(`Format file tidak didukung. Gunakan: ${allowedTypes.join(', ')}`, 'danger');
            e.target.value = '';
            return;
        }

        this.showPreview(file, type);
    }

    showPreview(file, type) {
        const preview = document.getElementById(`${type}Preview`);
        const info = document.getElementById(`${type}Info`);
        const container = document.getElementById(`${type}PreviewContainer`);

        if (!preview || !info || !container) return;

        // Hide default preview, show info
        preview.classList.add('d-none');
        info.classList.remove('d-none');
        container.innerHTML = '';

        // Create preview based on file type
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '5px';
                img.className = 'mb-2';
                container.appendChild(img);
                
                this.addFileInfo(file, container);
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            const icon = document.createElement('i');
            icon.className = 'fas fa-file-pdf fa-4x text-danger mb-3';
            container.appendChild(icon);
            
            this.addFileInfo(file, container);
        } else if (file.type.includes('word')) {
            const icon = document.createElement('i');
            icon.className = 'fas fa-file-word fa-4x text-primary mb-3';
            container.appendChild(icon);
            
            this.addFileInfo(file, container);
        }
    }

    addFileInfo(file, container) {
        const info = document.createElement('div');
        info.className = 'text-center';
        info.innerHTML = `
            <p class="mb-1"><strong>${file.name}</strong></p>
            <p class="text-muted small mb-0">
                ${(file.size / 1024).toFixed(2)} KB | 
                ${file.type.split('/')[1].toUpperCase()}
            </p>
        `;
        container.appendChild(info);
    }

    setupDragAndDrop(dropZone, fileInput, type) {
        if (!dropZone || !fileInput) return;

        ['dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.removeEventListener(eventName, this.handleDragEvents);
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.backgroundColor = '#e9ecef';
            dropZone.style.borderColor = '#0d6efd';
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.backgroundColor = '#f8f9fa';
            dropZone.style.borderColor = '#dee2e6';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.backgroundColor = '#f8f9fa';
            dropZone.style.borderColor = '#dee2e6';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
                
                console.log(`[ModalHandler] ${type} file dropped:`, files[0].name);
            }
        });
    }

    setupModalCloseHandlers() {
        // Use Bootstrap modal events
        const modals = ['uploadFotoModal', 'uploadCvModal', 'uploadKtpModal'];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                // Remove existing listeners
                modal.removeEventListener('hidden.bs.modal', this.handleModalClose);
                modal.removeEventListener('shown.bs.modal', this.handleModalShow);
                
                // Add new listeners
                modal.addEventListener('hidden.bs.modal', (e) => {
                    this.handleModalClose(e.target.id);
                });
                
                modal.addEventListener('shown.bs.modal', (e) => {
                    this.handleModalShow(e.target.id);
                });
            }
        });
    }

    handleModalClose(modalId) {
        console.log(`[ModalHandler] Modal ${modalId} closed`);
        
        // Reset based on modal type
        if (modalId.includes('Foto')) this.reset('foto');
        if (modalId.includes('Cv')) this.reset('cv');
        if (modalId.includes('Ktp')) this.reset('ktp');
    }

    handleModalShow(modalId) {
        console.log(`[ModalHandler] Modal ${modalId} shown`);
        
        // Re-initialize handlers for this modal
        if (modalId.includes('Foto')) this.modalState.foto = true;
        if (modalId.includes('Cv')) this.modalState.cv = true;
        if (modalId.includes('Ktp')) this.modalState.ktp = true;
    }

    reset(type) {
        const input = document.getElementById(`${type}Input`);
        const preview = document.getElementById(`${type}Preview`);
        const info = document.getElementById(`${type}Info`);

        if (input) {
            input.value = '';
            // Create a new input element to truly reset
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
        }
        if (preview) preview.classList.remove('d-none');
        if (info) info.classList.add('d-none');
        
        this.modalState[type] = false;
    }

    setupFormSubmitHandlers() {
        const forms = ['fotoUploadForm', 'cvUploadForm', 'ktpUploadForm'];
        
        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.removeEventListener('submit', this.handleFormSubmit);
                form.addEventListener('submit', (e) => {
                    this.handleFormSubmit(e);
                });
            }
        });
    }

    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
            
            // Auto re-enable after 10 seconds if still disabled
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    console.warn('[ModalHandler] Form submission timeout');
                }
            }, 10000);
        }
    }

    showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlert = document.querySelector('.modal-alert');
        if (existingAlert) existingAlert.remove();

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show modal-alert`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert at the beginning of modal body
        const modalBody = document.querySelector('.modal-body');
        if (modalBody) {
            modalBody.insertBefore(alertDiv, modalBody.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
}

// Global instance
const modalHandler = new ModalHandler();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        modalHandler.init();
    });
} else {
    modalHandler.init();
}

// Global functions
window.resetFoto = () => modalHandler.reset('foto');
window.resetCV = () => modalHandler.reset('cv');
window.resetKtpFile = () => modalHandler.reset('ktp');

window.hapusFotoProfil = (karyawanId) => {
    if (confirm('Apakah Anda yakin ingin menghapus foto profil ini?')) {
        window.location.href = `${BASE_URL || ''}/admin/karyawan/dokumen/delete-foto/${karyawanId}`;
    }
};

window.hapusCV = (karyawanId) => {
    if (confirm('Apakah Anda yakin ingin menghapus CV ini?')) {
        window.location.href = `${BASE_URL || ''}/admin/karyawan/dokumen/delete-cv/${karyawanId}`;
    }
};

// Debug function
window.debugModalHandler = () => {
    console.log('Modal Handler Status:');
    console.log('- Initialized:', modalHandler.initialized);
    console.log('- Modal State:', modalHandler.modalState);
    console.log('- Setup Complete:', modalHandler.setupComplete);
};