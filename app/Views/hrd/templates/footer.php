<?php
// app/Views/hrd/templates/footer.php
?>
    </div> <!-- end container-fluid -->
    
    <footer style="background: rgba(255,255,255,0.6); border-top: 1px solid rgba(0,0,0,0.08); padding: 14px 25px; text-align: center; margin-top: 40px;">
        <small class="text-muted" style="font-size: 0.78rem;">
            &copy; <?= date('Y') ?> CDW Engineering &mdash; Human Resources Department &nbsp;|&nbsp;
            <i class="fas fa-users-cog me-1" style="color: #1e3c72;"></i>CDW Intranet HR System
        </small>
    </footer>
</div> <!-- end .main-content -->
</div> <!-- end .app-container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
setInterval(() => {
    const now = new Date();
    const el = document.getElementById('liveClock');
    if (el) el.textContent = now.toLocaleTimeString('id-ID');
}, 1000);
</script>
</body>
</html>
