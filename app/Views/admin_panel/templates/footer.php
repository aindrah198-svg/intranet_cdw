<?php
// app/Views/admin_panel/templates/footer.php
?>
    </div> <!-- end .content-wrapper -->

    <!-- Footer -->
    <footer style="background:rgba(255,255,255,0.6);border-top:1px solid rgba(123,31,162,0.1);padding:14px 25px;text-align:center;">
        <small class="text-muted" style="font-size:0.78rem;">
            &copy; <?= date('Y') ?> CDW Engineering &mdash; Admin Panel &nbsp;|&nbsp;
            <i class="fas fa-user-shield me-1" style="color:#7b1fa2;"></i>Sistem Administrasi Internal
        </small>
    </footer>

</div> <!-- end .main-content -->
</div> <!-- end .app-container -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert Flash
<?php if (session()->getFlashdata('sweetalert')): ?>
const _sa = <?= json_encode(session()->getFlashdata('sweetalert')) ?>;
Swal.fire({ icon: _sa.type, title: _sa.title, text: _sa.message, timer: 3000, showConfirmButton: false });
<?php endif; ?>
</script>

</body>
</html>
