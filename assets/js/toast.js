class Toast {
    static show(message, type = 'success', duration = 5000) {
        const container = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠'
        };
        
        toast.innerHTML = `
            <span class="toast-icon">${icons[type] || ''}</span>
            <span class="toast-message">${message}</span>
            <span class="toast-close">&times;</span>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 10);
        
        let timeoutId = setTimeout(() => {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, duration);
        
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timeoutId);
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        });
    }
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

function showSuccess(message, duration = 5000) {
    Toast.show(message, 'success', duration);
}

function showError(message, duration = 8000) {
    Toast.show(message, 'error', duration);
}

function showWarning(message, duration = 6000) {
    Toast.show(message, 'warning', duration);
}