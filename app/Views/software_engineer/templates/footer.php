</div> <!-- End main-content -->

    <!-- jQuery & Bootstrap 5 Bundle JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Kredensial Reveal AJAX + Audit Log trigger
        function revealCredential(id) {
            if (confirm('Akses kredensial sensitif ini akan dicatat ke dalam Audit Trail Security Log. Lanjutkan?')) {
                $.ajax({
                    url: '<?= base_url("software-engineer/manajemen-sistem/kredensial-akses/reveal") ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#password_text_' + id).text(res.password).removeClass('text-muted code-font').addClass('fw-bold text-danger');
                            $('#btn_reveal_' + id).html('<i class="fas fa-eye-slash me-1"></i> Terbuka').addClass('disabled');
                            alert('Password berhasil terurai dan aksi Anda telah berhasil dicatat pada Audit Trail Security Log.');
                        } else {
                            alert(res.message || 'Gagal mengurai password');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan jaringan/autentikasi.');
                    }
                });
            }
        }
    </script>
</body>
</html>
