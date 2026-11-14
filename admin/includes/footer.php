<?php
/**
 * SCMIPS Gallery Admin Panel - Reusable Modern Admin Footer
 */
?>
        </main> <!-- /.admin-content-body -->

        <!-- Sticky Footer -->
        <footer class="admin-footer">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 small">
                <div>
                    &copy; <?= date('Y') ?> <strong>Shri Cloth Market Institute of Professional Studies, Indore</strong>. All Rights Reserved.
                </div>
                <div class="text-muted">
                    SCMIPS Gallery Admin v1.0 &bull; PHP <?= PHP_VERSION ?>
                </div>
            </div>
        </footer>
    </div> <!-- /.admin-main -->

    <!-- Floating Action Button (FAB) (Nexa Style Bottom-Right) -->
    <a href="<?= $adminPath ?? '' ?>images/upload.php" class="admin-fab" title="Quick Upload Photos">
        <i class="fa-solid fa-cloud-arrow-up"></i>
    </a>
</div> <!-- /.admin-wrapper -->

<!-- Bootstrap 5 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Admin Custom JS -->
<script src="<?= $adminPath ?? '' ?>assets/js/admin.js"></script>

</body>
</html>
