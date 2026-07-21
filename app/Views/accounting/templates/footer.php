<?php
// app/Views/accounting/templates/footer.php
$scripts = $scripts ?? [];
?>
    </div> <!-- Penutup untuk main-content container -->

    <!-- Quick Journal Modal -->
    <div class="modal fade" id="quickJournalModal" tabindex="-1" aria-labelledby="quickJournalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-accounting text-white">
                    <h5 class="modal-title" id="quickJournalModalLabel">
                        <i class="fas fa-plus me-2"></i>Jurnal Cepat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickJournalForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="journalDate" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="journalDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="journalType" class="form-label">Jenis Jurnal</label>
                                <select class="form-select" id="journalType" required>
                                    <option value="">Pilih Jenis...</option>
                                    <option value="umum">Jurnal Umum</option>
                                    <option value="penyesuaian">Jurnal Penyesuaian</option>
                                    <option value="pembalik">Jurnal Pembalik</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="journalDesc" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="journalDesc" placeholder="Masukkan keterangan jurnal..." required>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Akun</th>
                                                <th>Debit</th>
                                                <th>Kredit</th>
                                                <th>Keterangan</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm">
                                                        <option value="">Pilih akun...</option>
                                                        <!-- Options akan diisi via AJAX -->
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control form-control-sm debit-input" placeholder="0"></td>
                                                <td><input type="number" class="form-control form-control-sm credit-input" placeholder="0"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="Keterangan"></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-row">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    <button type="button" class="btn btn-sm btn-accounting-outline" id="addJournalRow">
                                                        <i class="fas fa-plus"></i> Tambah Baris
                                                    </button>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <strong>Total Debit:</strong> <span id="totalDebit">Rp 0</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Kredit:</strong> <span id="totalCredit">Rp 0</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Status:</strong> <span id="balanceStatus" class="text-success"><i class="fas fa-check-circle"></i> Balance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-accounting" onclick="submitQuickJournal()">
                        <i class="fas fa-save me-1"></i> Simpan Jurnal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript for Accounting -->
    <script>
        // Global accountingApp object untuk menghindari duplikasi
        window.accountingApp = window.accountingApp || {};
        
        // Inisialisasi hanya sekali
        $(document).ready(function() {
            // Cek jika sudah diinisialisasi
            if (window.accountingApp.initialized) {
                return;
            }
            
            window.accountingApp.initialized = true;
            
            // Auto-hide alerts setelah 5 detik
            setTimeout(function() {
                $('.alert').fadeOut('slow');
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
            
            // Toggle sidebar on mobile
            window.toggleSidebar = function() {
                const sidebar = $('.sidebar');
                const mainContent = $('.main-content');
                
                if (window.innerWidth <= 768) {
                    if (sidebar.css('left') === '0px') {
                        sidebar.css('left', '-250px');
                        mainContent.css('marginLeft', '0');
                        sidebar.removeClass('show');
                        mainContent.removeClass('expanded');
                    } else {
                        sidebar.css('left', '0');
                        mainContent.css('marginLeft', '0');
                        sidebar.addClass('show');
                        mainContent.addClass('expanded');
                    }
                }
            };
            
            // Keyboard shortcut untuk toggle menu
            $(document).keydown(function(e) {
                // Toggle sidebar dengan Alt+S
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    window.toggleSidebar();
                }
                
                // Quick journal dengan Alt+J
                if (e.altKey && e.key === 'j') {
                    e.preventDefault();
                    $('#quickJournalModal').modal('show');
                }
            });
            
            // Tambahkan tombol toggle untuk mobile
            if ($('.sidebar-toggle').length === 0) {
                const toggleBtn = $('<button class="btn btn-accounting sidebar-toggle d-md-none" style="position: fixed; bottom: 20px; right: 20px; z-index: 1001; border-radius: 50%; width: 50px; height: 50px; padding: 0;">' +
                                   '<i class="fas fa-bars"></i>' +
                                   '</button>');
                $('body').append(toggleBtn);
            }
            
            // Tutup modal saat escape ditekan
            $(document).keydown(function(e) {
                if (e.key === 'Escape') {
                    $('.modal').modal('hide');
                }
            });
            
            // Quick journal form handling
            $('#addJournalRow').click(function() {
                const newRow = `
                    <tr>
                        <td>
                            <select class="form-select form-select-sm">
                                <option value="">Pilih akun...</option>
                            </select>
                        </td>
                        <td><input type="number" class="form-control form-control-sm debit-input" placeholder="0"></td>
                        <td><input type="number" class="form-control form-control-sm credit-input" placeholder="0"></td>
                        <td><input type="text" class="form-control form-control-sm" placeholder="Keterangan"></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#quickJournalModal tbody').append(newRow);
            });
            
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });
            
            // Calculate totals function
            function calculateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;
                
                $('.debit-input').each(function() {
                    totalDebit += parseFloat($(this).val()) || 0;
                });
                
                $('.credit-input').each(function() {
                    totalCredit += parseFloat($(this).val()) || 0;
                });
                
                // Update total display
                $('#totalDebit').text('Rp ' + formatNumber(totalDebit));
                $('#totalCredit').text('Rp ' + formatNumber(totalCredit));
                
                // Check balance
                if (Math.abs(totalDebit - totalCredit) < 0.01) {
                    $('#balanceStatus').removeClass('text-danger').addClass('text-success')
                        .html('<i class="fas fa-check-circle"></i> Balance');
                } else {
                    $('#balanceStatus').removeClass('text-success').addClass('text-danger')
                        .html('<i class="fas fa-exclamation-triangle"></i> Unbalanced (Rp ' + formatNumber(Math.abs(totalDebit - totalCredit)) + ')');
                }
            }
            
            // Format number function
            function formatNumber(amount) {
                return new Intl.NumberFormat('id-ID').format(amount);
            }
            
            // Auto-calculate journal totals
            $(document).on('input', '.debit-input, .credit-input', function() {
                calculateTotals();
            });
            
            // Initialize charts if present
            if ($('#financialChart').length) {
                initFinancialChart();
            }
            
            // Initial calculation
            calculateTotals();
        });
        
        // Format number function global
        function formatNumber(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        }
        
        // Submit quick journal
        function submitQuickJournal() {
            const formData = {
                date: $('#journalDate').val(),
                type: $('#journalType').val(),
                description: $('#journalDesc').val(),
                entries: []
            };
            
            // Collect journal entries
            $('#quickJournalModal tbody tr').each(function() {
                const account = $(this).find('select').val();
                const debit = parseFloat($(this).find('.debit-input').val()) || 0;
                const credit = parseFloat($(this).find('.credit-input').val()) || 0;
                const note = $(this).find('input[type="text"]').val();
                
                if (account && (debit > 0 || credit > 0)) {
                    formData.entries.push({
                        account: account,
                        debit: debit,
                        credit: credit,
                        note: note
                    });
                }
            });
            
            // Validate
            if (formData.entries.length === 0) {
                Swal.fire('Error', 'Harap tambahkan minimal satu entri jurnal', 'error');
                return;
            }
            
            if (!formData.description) {
                Swal.fire('Error', 'Harap isi keterangan jurnal', 'error');
                return;
            }
            
            // Calculate totals
            const totalDebit = formData.entries.reduce((sum, entry) => sum + entry.debit, 0);
            const totalCredit = formData.entries.reduce((sum, entry) => sum + entry.credit, 0);
            
            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                Swal.fire('Error', 'Jurnal tidak balance! Total debit dan kredit harus sama.', 'error');
                return;
            }
            
            // Submit via AJAX
            $.ajax({
                url: '<?= base_url("accounting/pembukuan/save-quick-journal") ?>',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'Jurnal berhasil disimpan!', 'success');
                        $('#quickJournalModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan jurnal.', 'error');
                }
            });
        }
        
        // Initialize financial chart
        function initFinancialChart() {
            const ctx = document.getElementById('financialChart').getContext('2d');
            window.financialChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: [650, 720, 780, 820, 850, 900],
                            borderColor: 'rgb(40, 167, 69)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Pengeluaran',
                            data: [580, 620, 590, 610, 620, 630],
                            borderColor: 'rgb(220, 53, 69)',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Trend Keuangan 6 Bulan Terakhir'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value + ' Jt';
                                }
                            }
                        }
                    }
                }
            });
        }
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
                scheduleNextUpdate();
            }, delayToNextSecond);
        };
        
        // Mulai schedule
        scheduleNextUpdate();
        
        // Handle tab visibility change
        let lastUpdateTime = Date.now();
        
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                const currentTime = Date.now();
                if (currentTime - lastUpdateTime > 2000) {
                    updateClock();
                    lastUpdateTime = currentTime;
                    scheduleNextUpdate();
                }
            }
        });
        
        // Update saat window focus
        window.addEventListener('focus', () => {
            updateClock();
            scheduleNextUpdate();
        });
        
    })();
    </script>
    
    <!-- Accounting Specific Scripts -->
    <script>
    // Format currency input
    $(document).on('input', '.currency-input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            value = parseInt(value);
            $(this).val(new Intl.NumberFormat('id-ID').format(value));
        }
    });
    
    // Print function for financial reports
    function printReport() {
        window.print();
    }
    
    // Export to Excel function
    function exportToExcel(tableId, filename) {
        const table = document.getElementById(tableId);
        const html = table.outerHTML;
        const url = 'data:application/vnd.ms-excel,' + escape(html);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename + '.xls';
        link.click();
    }
    
    // Calculate balance function
    function calculateBalance(debit, credit) {
        const debitValue = parseFloat(debit) || 0;
        const creditValue = parseFloat(credit) || 0;
        return debitValue - creditValue;
    }
    
    // Show toast notification
    function showToast(message, type = 'success') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Berhasil!' : 'Informasi',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }
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