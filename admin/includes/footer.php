<?php
/**
 * SCMIPS Gallery Admin Panel - Reusable Admin Footer
 */
?>
</main>

<!-- Sticky Footer -->
<footer class="admin-footer">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div>
                &copy; <?= date('Y') ?> <strong>Shri Cloth Market Institute of Professional Studies, Indore</strong>. All Rights Reserved.
            </div>
            <div class="text-muted">
                SCMIPS Gallery Admin v1.0 &bull; PHP <?= PHP_VERSION ?>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Admin Custom JS -->
<script src="<?= $adminPath ?? '' ?>assets/js/admin.js"></script>

</body>
</html>
