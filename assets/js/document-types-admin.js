jQuery(document).ready(function($) {
    // Show create form
    $('#show-create-form').on('click', function() {
        $('#create-document-type-form').show();
        $(this).hide();
    });
    
    // Hide create form
    $('#cancel-create').on('click', function() {
        $('#create-document-type-form').hide();
        $('#show-create-form').show();
        $('#new-document-type-form')[0].reset();
    });
    
    // Show edit form
    $('.edit-document-type').on('click', function() {
        var termId = $(this).data('term-id');
        
        // Get term data via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_document_type_data',
                term_id: termId,
                security: $('#document_type_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    var term = response.data;
                    
                    $('#edit-document-type-name').val(term.name);
                    $('#edit-document-type-slug').val(term.slug);
                    $('#edit-document-type-parent').val(term.parent);
                    $('#edit-document-type-description').val(term.description);
                    $('#edit-document-type-id').val(term.term_id);
                    
                    $('#document-types-list').hide();
                    $('#edit-document-type-form').show();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Hide edit form
    $('#cancel-edit').on('click', function() {
        $('#edit-document-type-form').hide();
        $('#document-types-list').show();
        $('#update-document-type-form')[0].reset();
    });
    
    // Handle create form submission
    $('#new-document-type-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Document type created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Handle update form submission
    $('#update-document-type-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Document type updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Handle delete action
    $('.delete-document-type').on('click', function() {
        if (!confirm('Are you sure you want to delete this document type?')) {
            return;
        }
        
        var termId = $(this).data('term-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_document_type',
                term_id: termId,
                security: $('#update_document_type_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Document type deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});