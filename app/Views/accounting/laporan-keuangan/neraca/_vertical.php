<?php
// C:\xampp\htdocs\intranet_cdw\app\Views\accounting\laporan-keuangan\neraca\_horizontal.php
?>

<!-- Tabel Neraca Horizontal -->
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th width="40%">ASET</th>
                <th width="10%" class="text-end">Jumlah (Rp)</th>
                <th width="40%">KEWAJIBAN & EKUITAS</th>
                <th width="10%" class="text-end">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Siapkan array untuk baris
            $asetRows = [];
            $pasivaRows = [];
            
            // ===== ASET LANCAR =====
            if (!empty($neraca['aset_lancar'])) {
                $asetRows[] = ['type' => 'header', 'label' => 'ASET LANCAR', 'colspan' => 2];
                
                $subtotalLancar = 0;
                foreach ($neraca['aset_lancar'] as $item) {
                    $isContra = $item['is_contra'] ?? false;
                    
                    // PERBAIKAN: Untuk akun kontra, tampilkan nilai negatif
                    if ($isContra) {
                        $amount = -abs($item['saldo']);
                        $subtotalLancar += $amount;
                        
                        // Tampilkan dengan tanda minus di depan angka
                        $label = '(-) ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = 'text-danger contra-account';
                        $displayAmount = $amount; // Nilai negatif
                    } else {
                        $amount = $item['saldo'];
                        $subtotalLancar += $amount;
                        
                        $label = '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = '';
                        $displayAmount = $amount;
                    }
                    
                    $asetRows[] = [
                        'type' => 'item',
                        'label' => $label,
                        'amount' => $displayAmount, // Langsung pakai nilai (bisa positif/negatif)
                        'class' => $class,
                        'is_contra' => $isContra
                    ];
                }
                
                $asetRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Aset Lancar',
                    'amount' => $subtotalLancar, // Langsung pakai nilai subtotal
                    'class' => $subtotalLancar < 0 ? 'text-danger' : ''
                ];
                $asetRows[] = ['type' => 'spacer'];
            }
            
            // ===== ASET TETAP =====
            if (!empty($neraca['aset_tetap'])) {
                $asetRows[] = ['type' => 'header', 'label' => 'ASET TETAP'];
                
                $subtotalTetap = 0;
                foreach ($neraca['aset_tetap'] as $item) {
                    $isContra = $item['is_contra'] ?? false;
                    
                    if ($isContra) {
                        $amount = -abs($item['saldo']);
                        $subtotalTetap += $amount;
                        
                        $label = '(-) ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = 'text-danger contra-account';
                        $displayAmount = $amount;
                    } else {
                        $amount = $item['saldo'];
                        $subtotalTetap += $amount;
                        
                        $label = '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = '';
                        $displayAmount = $amount;
                    }
                    
                    $asetRows[] = [
                        'type' => 'item',
                        'label' => $label,
                        'amount' => $displayAmount,
                        'class' => $class,
                        'is_contra' => $isContra
                    ];
                }
                
                $asetRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Aset Tetap',
                    'amount' => $subtotalTetap,
                    'class' => $subtotalTetap < 0 ? 'text-danger' : ''
                ];
                $asetRows[] = ['type' => 'spacer'];
            }
            
            // ===== TOTAL ASET =====
            $asetRows[] = [
                'type' => 'total',
                'label' => 'TOTAL ASET',
                'amount' => $total_aset // Dari controller sudah benar
            ];
            
            // ===== KEWAJIBAN JANGKA PENDEK =====
            if (!empty($neraca['kewajiban_jangka_pendek'])) {
                $pasivaRows[] = ['type' => 'header', 'label' => 'KEWAJIBAN JANGKA PENDEK'];
                
                foreach ($neraca['kewajiban_jangka_pendek'] as $item) {
                    $pasivaRows[] = [
                        'type' => 'item',
                        'label' => '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>',
                        'amount' => $item['saldo']
                    ];
                }
                
                $totalKewajibanPendek = 0;
                foreach ($neraca['kewajiban_jangka_pendek'] as $item) {
                    $totalKewajibanPendek += $item['saldo'];
                }
                
                $pasivaRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Kewajiban Jangka Pendek',
                    'amount' => $totalKewajibanPendek
                ];
                $pasivaRows[] = ['type' => 'spacer'];
            }
            
            // ===== KEWAJIBAN JANGKA PANJANG =====
            if (!empty($neraca['kewajiban_jangka_panjang'])) {
                $pasivaRows[] = ['type' => 'header', 'label' => 'KEWAJIBAN JANGKA PANJANG'];
                
                foreach ($neraca['kewajiban_jangka_panjang'] as $item) {
                    $pasivaRows[] = [
                        'type' => 'item',
                        'label' => '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>',
                        'amount' => $item['saldo']
                    ];
                }
                
                $totalKewajibanPanjang = 0;
                foreach ($neraca['kewajiban_jangka_panjang'] as $item) {
                    $totalKewajibanPanjang += $item['saldo'];
                }
                
                $pasivaRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Kewajiban Jangka Panjang',
                    'amount' => $totalKewajibanPanjang
                ];
                $pasivaRows[] = ['type' => 'spacer'];
            }
            
            // ===== EKUITAS =====
            if (!empty($neraca['ekuitas'])) {
                $pasivaRows[] = ['type' => 'header', 'label' => 'EKUITAS'];
                
                $subtotalEkuitas = 0;
                foreach ($neraca['ekuitas'] as $item) {
                    $isContra = $item['is_contra'] ?? false;
                    
                    if ($isContra) {
                        $amount = -abs($item['saldo']);
                        $subtotalEkuitas += $amount;
                        
                        $label = '(-) ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = 'text-danger contra-account';
                        $displayAmount = $amount;
                    } else {
                        $amount = $item['saldo'];
                        $subtotalEkuitas += $amount;
                        
                        $label = '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = '';
                        $displayAmount = $amount;
                    }
                    
                    $pasivaRows[] = [
                        'type' => 'item',
                        'label' => $label,
                        'amount' => $displayAmount,
                        'class' => $class,
                        'is_contra' => $isContra
                    ];
                }
                
                $pasivaRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Ekuitas',
                    'amount' => $subtotalEkuitas,
                    'class' => $subtotalEkuitas < 0 ? 'text-danger' : ''
                ];
                $pasivaRows[] = ['type' => 'spacer'];
            }
            
            // ===== TOTAL PASIVA =====
            $pasivaRows[] = [
                'type' => 'total',
                'label' => 'TOTAL KEWAJIBAN & EKUITAS',
                'amount' => $total_kewajiban_ekuitas
            ];
            
            // Tentukan jumlah baris maksimum
            $maxRows = max(count($asetRows), count($pasivaRows));
            
            // Tampilkan baris
            for ($i = 0; $i < $maxRows; $i++):
                $aset = $asetRows[$i] ?? ['type' => 'empty', 'label' => '', 'amount' => ''];
                $pasiva = $pasivaRows[$i] ?? ['type' => 'empty', 'label' => '', 'amount' => ''];
                
                $rowClass = '';
                if (isset($aset['type'])) {
                    if ($aset['type'] == 'header') $rowClass = 'table-primary fw-bold';
                    elseif ($aset['type'] == 'subtotal') $rowClass = 'table-light fw-bold';
                    elseif ($aset['type'] == 'total') $rowClass = 'table-warning fw-bold';
                }
                if (isset($pasiva['type'])) {
                    if ($pasiva['type'] == 'header') $rowClass = 'table-info fw-bold';
                    elseif ($pasiva['type'] == 'subtotal') $rowClass = 'table-light fw-bold';
                    elseif ($pasiva['type'] == 'total') $rowClass = 'table-warning fw-bold';
                }
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= $aset['label'] ?? '' ?></td>
                <td class="text-end <?= $aset['class'] ?? '' ?>">
                    <?php if (isset($aset['amount']) && $aset['amount'] !== ''): ?>
                        <?php 
                        // Tampilkan dengan tanda minus jika negatif
                        if ($aset['amount'] < 0): 
                            echo '(Rp ' . number_format(abs($aset['amount']), 0, ',', '.') . ')';
                        else:
                            echo 'Rp ' . number_format($aset['amount'], 0, ',', '.');
                        endif;
                        ?>
                    <?php endif; ?>
                </td>
                <td><?= $pasiva['label'] ?? '' ?></td>
                <td class="text-end <?= $pasiva['class'] ?? '' ?>">
                    <?php if (isset($pasiva['amount']) && $pasiva['amount'] !== ''): ?>
                        <?php 
                        if ($pasiva['amount'] < 0): 
                            echo '(Rp ' . number_format(abs($pasiva['amount']), 0, ',', '.') . ')';
                        else:
                            echo 'Rp ' . number_format($pasiva['amount'], 0, ',', '.');
                        endif;
                        ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endfor; ?>
            
            <!-- Balance Check Row -->
            <tr class="table-<?= $is_balanced ? 'success' : 'danger' ?> fw-bold">
                <td colspan="4" class="text-center py-3">
                    <?php if (!$is_balanced): ?>
                        <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
                        <span class="me-3">NERACA TIDAK SEIMBANG!</span>
                        <span class="badge bg-danger me-2 p-2">
                            Selisih: Rp <?= number_format(abs($balance_difference), 0, ',', '.') ?>
                        </span>
                        <span class="text-muted">
                            (Aset <?= $balance_difference > 0 ? '>' : '<' ?> Kewajiban + Ekuitas)
                        </span>
                    <?php else: ?>
                        <i class="fas fa-check-circle me-2 fa-lg text-success"></i>
                        <span class="me-3">NERACA SEIMBANG</span>
                        <span class="text-muted">
                            (Aset = Kewajiban + Ekuitas)
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>