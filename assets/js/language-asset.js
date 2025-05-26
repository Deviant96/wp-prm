jQuery(document).ready(function($) {
    // Show create form
    document.getElementById('show-create-form').addEventListener('click', function() {
        document.getElementById('create-language-form').style.display = 'block';
        this.style.display = 'none';
    });

    // Hide create form
    document.getElementById('cancel-create').addEventListener('click', function() {
        const createForm = document.getElementById('create-language-form');
        const showButton = document.getElementById('show-create-form');
        const newForm = document.getElementById('new-language-form');
        
        createForm.style.display = 'none';
        showButton.style.display = 'block';
        newForm.reset();
    });
    
    // Show edit form
    document.querySelectorAll('.edit-language').forEach(button => {
        button.addEventListener('click', async function() {
            const termId = this.dataset.termId;
            
            try {
                const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_asset_language/${termId}`, {
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
                    document.getElementById('edit-language-name').value = data.name || '';
                    document.getElementById('edit-language-id').value = data.term_id || '';
                    
                    // Show/hide sections
                    document.getElementById('language-list').style.display = 'none';
                    document.getElementById('edit-language-form').style.display = 'block';
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
        document.getElementById('edit-language-form').style.display = 'none';
        document.getElementById('languages-list').style.display = 'block';
        document.getElementById('update-language-form').reset();
    });
    
    // CREATE
    document.getElementById('new-language-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const name = formData.get('name').trim();

        if (!name) {
            showError('Language name is required', form);
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Creating...';
        
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_asset_language`, {
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
            document.getElementById('create-language-form').style.display = 'none';
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
    document.getElementById('update-language-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        

        const form = this;
        const formData = new FormData(form);
        const name = formData.get('name').trim();
        const termId = formData.get('term_id'); // Get the term ID from form

        if (!name) {
            showError('Language name is required', form);
            return;
        }
        
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_asset_language/${termId}`, {
                method: 'PUT',
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
                body: formData,
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok) {
                showSuccess('Document type updated successfully!');
                location.reload();
            } else {
                throw new Error(data.message || 'Update failed');
            }
        } catch (error) {
            showError('Error: ' + error.message);
        }
    });
    
    // DELETE
    document.querySelectorAll('.delete-language').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete this document type?')) {
                return;
            }
            
            const termId = this.dataset.termId;
            
            try {
                const response = await fetch('wp-json/prm/v1/tbyte_prm_asset_language/' + termId, {
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
    document.getElementById('language-name')?.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        const name = e.target.value.trim();
        
        if (name.length < 2) return;
        
        debounceTimer = setTimeout(() => {
            checkDocumentTypeExists(name);
        }, 500);
    });

    async function checkDocumentTypeExists(name) {
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_asset_language/check-name?name=${encodeURIComponent(name)}`, {
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