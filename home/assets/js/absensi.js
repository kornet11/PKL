// Custom JavaScript for absensi feature

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Function to format date
    function formatDate(date) {
        const d = new Date(date);
        const day = d.getDate().toString().padStart(2, '0');
        const month = (d.getMonth() + 1).toString().padStart(2, '0');
        const year = d.getFullYear();
        return `${day}-${month}-${year}`;
    }
    
    // Function to format time
    function formatTime(time) {
        const t = new Date(`2000-01-01T${time}`);
        const hours = t.getHours().toString().padStart(2, '0');
        const minutes = t.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }
    
    // Function to show notification
    function showNotification(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(alertDiv);
            }, 150);
        }, 5000);
    }
    
    // Function to confirm action
    function confirmAction(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
    
    // Export functions to global scope
    window.formatDate = formatDate;
    window.formatTime = formatTime;
    window.showNotification = showNotification;
    window.confirmAction = confirmAction;
});