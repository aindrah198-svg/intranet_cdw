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
                $asetRows[] = ['type' => 'header', 'label' => 'ASET LANCAR'];
                
                $subtotalLancar = 0;
                foreach ($neraca['aset_lancar'] as $item) {
                    $isContra = $item['is_contra'] ?? false;
                    
                    if ($isContra) {
                        // Akun kontra: kurangi dari subtotal dan tampilkan dalam kurung
                        $subtotalLancar -= abs($item['saldo']);
                        $label = '(-) ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = 'text-danger contra-account';
                        $displayAmount = '(Rp ' . number_format(abs($item['saldo']), 0, ',', '.') . ')';
                    } else {
                        // Akun normal: tambah ke subtotal
                        $subtotalLancar += $item['saldo'];
                        $label = '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = '';
                        $displayAmount = 'Rp ' . number_format($item['saldo'], 0, ',', '.');
                    }
                    
                    $asetRows[] = [
                        'type' => 'item',
                        'label' => $label,
                        'display' => $displayAmount,
                        'class' => $class,
                        'is_contra' => $isContra
                    ];
                }
                
                $asetRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Aset Lancar',
                    'display' => 'Rp ' . number_format($subtotalLancar, 0, ',', '.'),
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
                        $subtotalTetap -= abs($item['saldo']);
                        $label = '(-) ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = 'text-danger contra-account';
                        $displayAmount = '(Rp ' . number_format(abs($item['saldo']), 0, ',', '.') . ')';
                    } else {
                        $subtotalTetap += $item['saldo'];
                        $label = '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = '';
                        $displayAmount = 'Rp ' . number_format($item['saldo'], 0, ',', '.');
                    }
                    
                    $asetRows[] = [
                        'type' => 'item',
                        'label' => $label,
                        'display' => $displayAmount,
                        'class' => $class,
                        'is_contra' => $isContra
                    ];
                }
                
                $asetRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Aset Tetap',
                    'display' => 'Rp ' . number_format($subtotalTetap, 0, ',', '.'),
                    'class' => $subtotalTetap < 0 ? 'text-danger' : ''
                ];
                $asetRows[] = ['type' => 'spacer'];
            }
            
            // ===== TOTAL ASET =====
            $asetRows[] = [
                'type' => 'total',
                'label' => 'TOTAL ASET',
                'display' => 'Rp ' . number_format($total_aset, 0, ',', '.')
            ];
            
       // ===== KEWAJIBAN JANGKA PENDEK =====
if (!empty($neraca['kewajiban_jangka_pendek'])) {
    $pasivaRows[] = ['type' => 'header', 'label' => 'KEWAJIBAN JANGKA PENDEK'];
    
    $totalKewajibanPendek = 0;
    foreach ($neraca['kewajiban_jangka_pendek'] as $item) {
        // Ambil nilai absolut untuk kewajiban (selalu positif di neraca)
        $nilaiKewajiban = abs($item['saldo']);
        $totalKewajibanPendek += $nilaiKewajiban;
        
        $pasivaRows[] = [
            'type' => 'item',
            'label' => '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>',
            'display' => 'Rp ' . number_format($nilaiKewajiban, 0, ',', '.')
        ];
    }
    
    $pasivaRows[] = [
        'type' => 'subtotal',
        'label' => '    Total Kewajiban Jangka Pendek',
        'display' => 'Rp ' . number_format($totalKewajibanPendek, 0, ',', '.')
    ];
    $pasivaRows[] = ['type' => 'spacer'];
}

// ===== KEWAJIBAN JANGKA PANJANG ===== (sama)
if (!empty($neraca['kewajiban_jangka_panjang'])) {
    $pasivaRows[] = ['type' => 'header', 'label' => 'KEWAJIBAN JANGKA PANJANG'];
    
    $totalKewajibanPanjang = 0;
    foreach ($neraca['kewajiban_jangka_panjang'] as $item) {
        $nilaiKewajiban = abs($item['saldo']);
        $totalKewajibanPanjang += $nilaiKewajiban;
        
        $pasivaRows[] = [
            'type' => 'item',
            'label' => '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>',
            'display' => 'Rp ' . number_format($nilaiKewajiban, 0, ',', '.')
        ];
    }
    
    $pasivaRows[] = [
        'type' => 'subtotal',
        'label' => '    Total Kewajiban Jangka Panjang',
        'display' => 'Rp ' . number_format($totalKewajibanPanjang, 0, ',', '.')
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
                        $subtotalEkuitas -= abs($item['saldo']);
                        $label = '(-) ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = 'text-danger contra-account';
                        $displayAmount = '(Rp ' . number_format(abs($item['saldo']), 0, ',', '.') . ')';
                    } else {
                        $subtotalEkuitas += $item['saldo'];
                        $label = '    ' . $item['nama_akun'] . ' <small class="text-muted">[' . $item['kode_akun'] . ']</small>';
                        $class = '';
                        $displayAmount = 'Rp ' . number_format($item['saldo'], 0, ',', '.');
                    }
                    
                    $pasivaRows[] = [
                        'type' => 'item',
                        'label' => $label,
                        'display' => $displayAmount,
                        'class' => $class,
                        'is_contra' => $isContra
                    ];
                }
                
                $pasivaRows[] = [
                    'type' => 'subtotal',
                    'label' => '    Total Ekuitas',
                    'display' => 'Rp ' . number_format($subtotalEkuitas, 0, ',', '.'),
                    'class' => $subtotalEkuitas < 0 ? 'text-danger' : ''
                ];
                $pasivaRows[] = ['type' => 'spacer'];
            }
            
            // ===== TOTAL PASIVA =====
            $pasivaRows[] = [
                'type' => 'total',
                'label' => 'TOTAL KEWAJIBAN & EKUITAS',
                'display' => 'Rp ' . number_format($total_kewajiban_ekuitas, 0, ',', '.')
            ];
            
            // Tentukan jumlah baris maksimum
            $maxRows = max(count($asetRows), count($pasivaRows));
            
            // Tampilkan baris
            for ($i = 0; $i < $maxRows; $i++):
                $aset = $asetRows[$i] ?? ['type' => 'empty', 'label' => '', 'display' => ''];
                $pasiva = $pasivaRows[$i] ?? ['type' => 'empty', 'label' => '', 'display' => ''];
                
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
                <td class="<?= $aset['class'] ?? '' ?>"><?= $aset['label'] ?? '' ?></td>
                <td class="text-end <?= $aset['class'] ?? '' ?>"><?= $aset['display'] ?? '' ?></td>
                <td class="<?= $pasiva['class'] ?? '' ?>"><?= $pasiva['label'] ?? '' ?></td>
                <td class="text-end <?= $pasiva['class'] ?? '' ?>"><?= $pasiva['display'] ?? '' ?></td>
            </tr>
            <?php endfor; ?>
            
            <!-- Balance Check Row -->
            <tr class="table-<?= ($is_balanced ?? false) ? 'success' : 'danger' ?> fw-bold">
                <td colspan="4" class="text-center py-3">
                    <?php if (!($is_balanced ?? true)): ?>
                        <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
                        <span class="me-3">NERACA TIDAK SEIMBANG!</span>
                        <span class="badge bg-danger me-2 p-2">
                            Selisih: Rp <?= number_format(abs($balance_difference ?? 0), 0, ',', '.') ?>
                        </span>
                        <span class="text-muted">
                            (Aset <?= ($balance_difference ?? 0) > 0 ? '>' : '<' ?> Kewajiban + Ekuitas)
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