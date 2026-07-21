<?php
// app/Views/teknisi/templates/footer.php
$scripts = $scripts ?? [];
?>
    </div> <!-- Penutup untuk main-content yang dibuka di navbar.php -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Global teknisiApp object untuk menghindari duplikasi
        window.teknisiApp = window.teknisiApp || {};
        
        // Inisialisasi hanya sekali
        $(document).ready(function() {
            // Cek jika sudah diinisialisasi
            if (window.teknisiApp.initialized) {
                return;
            }
            
            window.teknisiApp.initialized = true;
            
            // Initialize DataTable jika belum ada
            if ($('.data-table-wrapper table').length && !$.fn.DataTable.isDataTable('.data-table-wrapper table')) {
                $('.data-table-wrapper table').DataTable({
                    "pageLength": 10,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "responsive": true,
                    "language": {
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "paginate": {
                            "first": "Pertama",
                            "last": "Terakhir",
                            "next": "Berikutnya",
                            "previous": "Sebelumnya"
                        }
                    }
                });
            }
            
            // Auto-hide alerts setelah 5 detik
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            // Sub-menu arrow rotation
            $(document).on('click', '[data-bs-toggle="collapse"]', function() {
                $(this).find('.fa-chevron-down').toggleClass('rotated');
            });
            
            // Active menu highlighting dari URL
            function highlightActiveMenu() {
                const currentPath = window.location.pathname;
                const menuLinks = $('.sidebar .nav-link');
                
                menuLinks.removeClass('active');
                
                menuLinks.each(function() {
                    const href = $(this).attr('href');
                    if (href && href !== '#' && currentPath.includes(href.replace(/\/$/, ''))) {
                        $(this).addClass('active');
                        
                        // Expand parent menu jika submenu
                        const parentMenu = $(this).closest('.collapse');
                        if (parentMenu.length) {
                            const toggleButton = $('[href="#' + parentMenu.attr('id') + '"]');
                            if (toggleButton.length) {
                                toggleButton.attr('aria-expanded', 'true');
                                parentMenu.addClass('show');
                            }
                        }
                    }
                });
            }
            
            // Jalankan highlight menu
            highlightActiveMenu();
            
            // Responsive sidebar handling
            function handleSidebar() {
                const sidebar = $('.sidebar');
                const mainContent = $('.main-content');
                
                if (window.innerWidth <= 768) {
                    sidebar.css('left', '-250px');
                    mainContent.css('marginLeft', '0');
                    sidebar.removeClass('show');
                    mainContent.removeClass('expanded');
                } else {
                    sidebar.css('left', '0');
                    mainContent.css('marginLeft', 'var(--sidebar-width)');
                }
            }
            
            // Initial check
            handleSidebar();
            
            // Handle window resize
            let resizeTimer;
            $(window).resize(function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    handleSidebar();
                }, 250);
            });
            
            // SweetAlert Toast notification
            window.showToast = function(icon, title, text, timer = 3000) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: timer,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                
                Toast.fire({
                    icon: icon,
                    title: title,
                    text: text
                });
            };
            
            // Confirm dialog
            window.confirmDialog = function(title, text, confirmButtonText = 'Ya', cancelButtonText = 'Tidak') {
                return Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#858796',
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: cancelButtonText
                });
            };
            
            // Handle session messages
            <?php if (session()->getFlashdata('success')): ?>
                showToast('success', 'Success', '<?= session()->getFlashdata('success') ?>');
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                showToast('error', 'Error', '<?= session()->getFlashdata('error') ?>');
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('warning')): ?>
                showToast('warning', 'Warning', '<?= session()->getFlashdata('warning') ?>');
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('info')): ?>
                showToast('info', 'Info', '<?= session()->getFlashdata('info') ?>');
            <?php endif; ?>
            
            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // Alt+S untuk toggle sidebar
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    window.toggleSidebar();
                }
                
                // Alt+D untuk Dashboard
                if (e.altKey && e.key === 'd') {
                    e.preventDefault();
                    window.location.href = '<?= base_url("teknisi/dashboard") ?>';
                }
                
                // Alt+A untuk Absensi
                if (e.altKey && e.key === 'a') {
                    e.preventDefault();
                    window.location.href = '<?= base_url("teknisi/absensi") ?>';
                }
                
                // Alt+T untuk Tugas & Proyek
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    window.location.href = '<?= base_url("teknisi/tugas-proyek") ?>';
                }
            });
            
            // Tutup modal saat escape ditekan
            $(document).keydown(function(e) {
                if (e.key === 'Escape') {
                    $('.modal').modal('hide');
                }
            });
        });
        
        // Toggle sidebar on mobile
        window.toggleSidebar = function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                mainContent.classList.toggle('expanded');
            }
        };
    </script>
    
    <?php
    // Additional JavaScript files
    if (!empty($scripts)) {
        foreach ($scripts as $script) {
            echo '<script src="' . $script . '"></script>' . "\n";
        }
    }
    ?>
</body>
</html>