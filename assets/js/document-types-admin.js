jQuery(document).ready(function($) {
    // Show create form
    document.getElementById('show-create-form').addEventListener('click', function() {
        document.getElementById('create-document-type-form').style.display = 'block';
        this.style.display = 'none';
    });

    // Hide create form
    document.getElementById('cancel-create').addEventListener('click', function() {
        const createForm = document.getElementById('create-document-type-form');
        const showButton = document.getElementById('show-create-form');
        const newForm = document.getElementById('new-document-type-form');
        
        createForm.style.display = 'none';
        showButton.style.display = 'block';
        newForm.reset();
    });
    
    // Show edit form
    document.querySelectorAll('.edit-document-type').forEach(button => {
        button.addEventListener('click', async function() {
            const termId = this.dataset.termId;
            
            try {
                const response = await fetch(`${wpApiSettings.root}prm/v1/document_type/${termId}`, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': wpApiSettings.nonce,
                        'Content-Type': 'application/json',
                    },
                    credentials: 'include'
                });

                const data = await response.json();

                if (response.ok) {
                    // Populate form fields
                    document.getElementById('edit-document-type-name').value = data.name || '';
                    document.getElementById('edit-document-type-slug').value = data.slug || '';
                    document.getElementById('edit-document-type-parent').value = data.parent || '';
                    document.getElementById('edit-document-type-description').value = data.description || '';
                    document.getElementById('edit-document-type-id').value = data.term_id || '';
                    
                    // Show/hide sections
                    document.getElementById('document-types-list').style.display = 'none';
                    document.getElementById('edit-document-type-form').style.display = 'block';
                } else {
                    throw new Error(data.message || 'Failed to fetch document type');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });
    });
    
    // Hide edit form
    document.getElementById('cancel-edit').addEventListener('click', function() {
        document.getElementById('edit-document-type-form').style.display = 'none';
        document.getElementById('document-types-list').style.display = 'block';
        document.getElementById('update-document-type-form').reset();
    });
    
    // CREATE
    document.getElementById('new-document-type-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const name = formData.get('name').trim();

        if (!name) {
            showError('Document type name is required', form);
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Creating...';
        
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/document_type`, {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
                body: formData,
                credentials: 'include'
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to create document type');
            }

            showSuccess('Document type created successfully!');
            form.reset();

            // Optional: Hide form after creation
            document.getElementById('create-document-type-form').style.display = 'none';
            document.getElementById('show-create-form').style.display = 'block';
            
            // Refresh list after 1 second
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            showError(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
    
    // UPDATE
    document.getElementById('update-document-type-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const termId = formData.get('term_id'); // Get the term ID from form
        
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/document_type/${termId}`, {
                method: 'POST', // or 'PUT' if you prefer
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
                body: formData,
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok) {
                alert('Document type updated successfully!');
                location.reload();
            } else {
                throw new Error(data.message || 'Update failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
    
    // DELETE
    document.querySelectorAll('.delete-document-type').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete this document type?')) {
                return;
            }
            
            const termId = this.dataset.termId;
            
            try {
                const response = await fetch('wp-json/prm/v1/document_type/' + termId, {
                    method: 'DELETE',
                    headers: {
                        'X-WP-Nonce': wpApiSettings.nonce,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Document type deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.data);
                }
            } catch (error) {
                alert('Error: ' + error);
            }
        });
    });







    // Add duplicate name checking (debounced)
    let debounceTimer;
    document.getElementById('document-type-name')?.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        const name = e.target.value.trim();
        
        if (name.length < 2) return;
        
        debounceTimer = setTimeout(() => {
            checkDocumentTypeExists(name);
        }, 500);
    });

    async function checkDocumentTypeExists(name) {
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/document_type/check-name?name=${encodeURIComponent(name)}`, {
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce
                },
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.exists) {
                showWarning('A document type with this name already exists', 6000);
            }
        } catch (error) {
            showError('Failed to check document type name');
        }
    }
});