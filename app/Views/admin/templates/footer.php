<?php
// app/Views/admin/templates/footer.php
?>
    </div> <!-- end .content-wrapper -->

    <footer style="background:rgba(255,255,255,0.6);border-top:1px solid rgba(123,31,162,0.1);padding:14px 25px;text-align:center;margin-top:auto;">
        <small class="text-muted" style="font-size:0.78rem;">
            &copy; <?= date('Y') ?> CDW Engineering &mdash; Admin Panel &nbsp;|&nbsp;
            <i class="fas fa-user-shield me-1" style="color:#7b1fa2;"></i>Sistem Administrasi Internal
        </small>
    </footer>

</div> <!-- end .main-content -->
</div> <!-- end .app-container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.toggleSidebar = function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    if (!sidebar || !mainContent) return;

    if (window.innerWidth < 992) {
        // Mobile layout behavior
        const isVisible = sidebar.classList.contains('show');
        let overlay = document.getElementById('sidebarOverlay');
        
        if (isVisible) {
            sidebar.classList.remove('show');
            if (overlay) overlay.style.display = 'none';
        } else {
            sidebar.classList.add('show');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'sidebarOverlay';
                overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1040;backdrop-filter:blur(2px);-webkit-backdrop-filter:blur(2px);';
                document.body.appendChild(overlay);
                overlay.addEventListener('click', window.toggleSidebar);
            }
            overlay.style.display = 'block';
        }
    } else {
        // Desktop layout behavior
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('expanded');
        
        if (sidebar.classList.contains('closed')) {
            localStorage.setItem('admin_sidebar_state', 'closed');
        } else {
            localStorage.setItem('admin_sidebar_state', 'open');
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth >= 992) {
        const state = localStorage.getItem('admin_sidebar_state');
        if (state === 'closed') {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            if (sidebar) sidebar.classList.add('closed');
            if (mainContent) mainContent.classList.add('expanded');
        }
    }
});

setInterval(() => {
    const now = new Date();
    const el = document.getElementById('liveClock');
    if (el) el.textContent = now.toLocaleTimeString('id-ID');
}, 1000);
</script>
</body>
</html>