<?php
// app/Views/direktur/templates/footer.php
$scripts = $scripts ?? [];
?>
    </div> <!-- Penutup untuk container-fluid yang dibuka di navbar.php -->
</div> <!-- Penutup untuk main-content yang dibuka di navbar.php -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Global direkturApp object untuk menghindari duplikasi
        window.direkturApp = window.direkturApp || {};
        
        // Inisialisasi hanya sekali
        $(document).ready(function() {
            // Cek jika sudah diinisialisasi
            if (window.direkturApp.initialized) {
                return;
            }
            
            window.direkturApp.initialized = true;
            
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
            
            // Mobile sidebar toggle
            $(document).on('click', '.sidebar-toggle', function() {
                $('.sidebar').toggleClass('show');
                $('.main-content').toggleClass('expanded');
            });
            
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
                                const chevron = toggleButton.find('.fa-chevron-down');
                                if (chevron.length) {
                                    chevron.css('transform', 'rotate(180deg)');
                                }
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
                
                if (window.innerWidth < 992) {
                    sidebar.css('left', '-260px');
                    mainContent.css('marginLeft', '0');
                    sidebar.removeClass('show');
                    mainContent.removeClass('expanded');
                    $('#sidebarOverlay').fadeOut(150, function() { $(this).remove(); });
                } else {
                    sidebar.css('left', '0');
                    mainContent.css('marginLeft', 'var(--sidebar-width)');
                    sidebar.addClass('show');
                    mainContent.addClass('expanded');
                    $('#sidebarOverlay').remove();
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
            
            // Toggle sidebar drawer on mobile and desktop
            window.toggleSidebar = function() {
                const sidebar = $('.sidebar');
                const mainContent = $('.main-content');
                
                if (window.innerWidth < 992) {
                    const isVisible = sidebar.hasClass('show') || sidebar.css('left') === '0px';
                    if (isVisible) {
                        sidebar.css('left', '-260px').removeClass('show');
                        mainContent.removeClass('expanded');
                        $('#sidebarOverlay').fadeOut(200, function() { $(this).remove(); });
                    } else {
                        sidebar.css('left', '0').addClass('show');
                        mainContent.addClass('expanded');
                        if (!$('#sidebarOverlay').length) {
                            $('body').append('<div id="sidebarOverlay" style="position:fixed !important;top:0 !important;left:0 !important;right:0 !important;bottom:0 !important;width:100% !important;height:100% !important;min-height:100dvh !important;background:rgba(0,0,0,0.65) !important;backdrop-filter:blur(3px) !important;-webkit-backdrop-filter:blur(3px) !important;z-index:1040 !important;margin:0 !important;padding:0 !important;display:none;"></div>');
                            $('#sidebarOverlay').fadeIn(200);
                            $('#sidebarOverlay').off('click').on('click', function() {
                                window.toggleSidebar();
                            });
                        }
                    }
                } else {
                    // Desktop behavior
                    const isVisible = sidebar.css('left') === '0px' || !sidebar.hasClass('closed');
                    if (isVisible) {
                        sidebar.css('left', '-260px').addClass('closed').removeClass('show');
                        mainContent.css('marginLeft', '0');
                    } else {
                        sidebar.css('left', '0').removeClass('closed').addClass('show');
                        mainContent.css('marginLeft', 'var(--sidebar-width)');
                    }
                }
            };
            
            // Keyboard shortcut untuk toggle menu
            $(document).keydown(function(e) {
                if (e.altKey && e.key === 'r') {
                    e.preventDefault();
                    const laporanMenu = $('#laporanMenu');
                    const button = $('[href="#laporanMenu"]');
                    if (laporanMenu.length && button.length) {
                        button.click();
                    }
                }
                
                // Toggle sidebar dengan Alt+S
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    window.toggleSidebar();
                }
            });
            

            
            // Tutup modal saat escape ditekan
            $(document).keydown(function(e) {
                if (e.key === 'Escape') {
                    $('.modal').modal('hide');
                }
            });
        });
    </script>

    <!-- Live Clock Script -->
    <script>
    // Live Clock dengan akurasi tinggi
    (function() {
        const clockElement = document.getElementById('liveClock');
        if (!clockElement) return;
        
        // Format waktu
        const formatTime = (date) => {
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const seconds = date.getSeconds().toString().padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        };
        
        // Update clock
        const updateClock = () => {
            if (clockElement) {
                clockElement.textContent = formatTime(new Date());
            }
        };
        
        // Update pertama
        updateClock();
        
        // Function untuk schedule update berikutnya tepat di detik berikutnya
        const scheduleNextUpdate = () => {
            const now = new Date();
            const currentMilliseconds = now.getMilliseconds();
            const delayToNextSecond = 1000 - currentMilliseconds;
            
            // Set timeout dengan delay yang tepat
            setTimeout(() => {
                updateClock();
                scheduleNextUpdate(); // Schedule berikutnya
            }, delayToNextSecond);
        };
        
        // Mulai schedule
        scheduleNextUpdate();
        
        // Handle tab visibility change untuk sync ulang saat kembali ke tab
        let lastUpdateTime = Date.now();
        
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                const currentTime = Date.now();
                // Jika lebih dari 2 detik tidak update, update sekarang
                if (currentTime - lastUpdateTime > 2000) {
                    updateClock();
                    lastUpdateTime = currentTime;
                    // Restart schedule
                    scheduleNextUpdate();
                }
            }
        });
        
        // Update saat window focus
        window.addEventListener('focus', () => {
            updateClock();
            scheduleNextUpdate();
        });
        
        // Optional: Update setiap 30 detik sebagai fallback
        setInterval(() => {
            const currentTime = Date.now();
            if (currentTime - lastUpdateTime > 35000) {
                updateClock();
                lastUpdateTime = currentTime;
            }
        }, 30000);
        
    })();
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