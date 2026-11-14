/**
 * SCMIPS Gallery Admin Panel - Custom JavaScript Helpers & Layout Controls
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Sidebar Toggle & Mobile Overlay
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
        });
    }

    if (sidebarOverlay && adminSidebar) {
        sidebarOverlay.addEventListener('click', function () {
            adminSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // 2. Auto-dismiss alerts after 4.5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 4500);
    });

    // 3. Universal delete confirmation
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
