/**
 * SCMIPS Gallery Admin Panel - Custom JavaScript Helpers
 */

document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alerts after 4.5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 4500);
    });

    // Universal delete confirmation
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const message = btn.getAttribute('data-confirm-delete') || 'Are you sure you want to delete this item? This action cannot be undone.';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
