<?php
// posting_jurnal.php - Simpan di C:\xampp\htdocs\cdwnet\public\

// Koneksi database manual
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'database_cdwnet';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "========================================\n";
echo "POSTING JURNAL PENYESUAIAN KE BUKU BESAR\n";
echo "========================================\n\n";

// Cek jurnal penyesuaian yang statusnya 'posted'
$result = $conn->query("
    SELECT * FROM jurnal_penyesuaian 
    WHERE status = 'posted'
    ORDER BY tanggal_penyesuaian ASC
");

if ($result->num_rows == 0) {
    die("Tidak ada jurnal penyesuaian dengan status 'posted'.\n");
}

$totalDiposting = 0;
$totalRef = [];

while ($jurnal = $result->fetch_assoc()) {
    echo "Memproses: {$jurnal['nomor_penyesuaian']}... ";
    
    // Cek apakah sudah ada di buku besar
    $check = $conn->query("
        SELECT COUNT(*) as total 
        FROM buku_besar 
        WHERE referensi = '{$jurnal['nomor_penyesuaian']}' 
        AND referensi_tipe = 'jurnal_penyesuaian'
    ");
    $exists = $check->fetch_assoc()['total'];
    
    if ($exists > 0) {
        // Hapus yang lama dulu
        $conn->query("
            DELETE FROM buku_besar 
            WHERE referensi = '{$jurnal['nomor_penyesuaian']}' 
            AND referensi_tipe = 'jurnal_penyesuaian'
        ");
        echo "hapus data lama... ";
    }
    
    // Ambil detail jurnal penyesuaian
    $detailResult = $conn->query("
        SELECT jpd.*, c.kode_akun, c.nama_akun
        FROM jurnal_penyesuaian_detail jpd
        JOIN coa c ON jpd.coa_id = c.id
        WHERE jpd.jurnal_penyesuaian_id = {$jurnal['id']}
    ");
    
    $countDetail = 0;
    while ($detail = $detailResult->fetch_assoc()) {
        $insert = $conn->query("
            INSERT INTO buku_besar 
            (coa_id, kode_akun, nama_akun, tanggal, keterangan, 
             referensi, referensi_id, referensi_tipe, debit, kredit, 
             created_at, updated_at)
            VALUES (
                {$detail['coa_id']},
                '{$detail['kode_akun']}',
                '{$detail['nama_akun']}',
                '{$jurnal['tanggal_penyesuaian']}',
                '{$jurnal['keterangan']} - {$detail['keterangan']}',
                '{$jurnal['nomor_penyesuaian']}',
                {$jurnal['id']},
                'jurnal_penyesuaian',
                {$detail['debit']},
                {$detail['kredit']},
                NOW(),
                NOW()
            )
        ");
        
        if ($insert) {
            $countDetail++;
        }
    }
    
    echo "OK ($countDetail entri)\n";
    $totalDiposting++;
    $totalRef[] = $jurnal['nomor_penyesuaian'];
}

// Sekarang posting jurnal umum yang mungkin belum
echo "\nCEK JURNAL UMUM...\n";
$jurnalUmum = $conn->query("
    SELECT j.* FROM jurnal j
    LEFT JOIN (
        SELECT DISTINCT referensi_id 
        FROM buku_besar 
        WHERE referensi_tipe = 'jurnal'
    ) bb ON j.id = bb.referensi_id
    WHERE j.status = 'posted' AND bb.referensi_id IS NULL
");

if ($jurnalUmum->num_rows > 0) {
    while ($jurnal = $jurnalUmum->fetch_assoc()) {
        echo "Memproses: {$jurnal['nomor_jurnal']}... ";
        
        $detailResult = $conn->query("
            SELECT jd.*, c.kode_akun, c.nama_akun
            FROM jurnal_detail jd
            JOIN coa c ON jd.coa_id = c.id
            WHERE jd.jurnal_id = {$jurnal['id']}
        ");
        
        $countDetail = 0;
        while ($detail = $detailResult->fetch_assoc()) {
            $insert = $conn->query("
                INSERT INTO buku_besar 
                (coa_id, kode_akun, nama_akun, tanggal, keterangan, 
                 referensi, referensi_id, referensi_tipe, debit, kredit, 
                 created_at, updated_at)
                VALUES (
                    {$detail['coa_id']},
                    '{$detail['kode_akun']}',
                    '{$detail['nama_akun']}',
                    '{$jurnal['tanggal']}',
                    '{$jurnal['keterangan']} - {$detail['keterangan']}',
                    '{$jurnal['nomor_jurnal']}',
                    {$jurnal['id']},
                    'jurnal',
                    {$detail['debit']},
                    {$detail['kredit']},
                    NOW(),
                    NOW()
                )
            ");
            
            if ($insert) {
                $countDetail++;
            }
        }
        
        echo "OK ($countDetail entri)\n";
    }
} else {
    echo "Semua jurnal umum sudah diposting.\n";
}

// HITUNG ULANG SALDO
echo "\nMENGHITUNG ULANG SALDO... ";

// Reset saldo dulu
$conn->query("UPDATE buku_besar SET saldo_debit = 0, saldo_kredit = 0, saldo_akhir = 0");

// Ambil semua akun
$akuns = $conn->query("SELECT id, saldo_normal FROM coa WHERE is_header = 0 AND is_active = 1");

while ($akun = $akuns->fetch_assoc()) {
    // Ambil semua transaksi untuk akun ini urut berdasarkan tanggal
    $transaksi = $conn->query("
        SELECT id, debit, kredit 
        FROM buku_besar 
        WHERE coa_id = {$akun['id']}
        ORDER BY tanggal ASC, id ASC
    ");
    
    $saldoDebit = 0;
    $saldoKredit = 0;
    
    while ($trx = $transaksi->fetch_assoc()) {
        $saldoDebit += $trx['debit'];
        $saldoKredit += $trx['kredit'];
        
        if ($akun['saldo_normal'] == 'Debit') {
            $saldoAkhir = $saldoDebit - $saldoKredit;
        } else {
            $saldoAkhir = $saldoKredit - $saldoDebit;
        }
        
        $conn->query("
            UPDATE buku_besar 
            SET saldo_debit = $saldoDebit,
                saldo_kredit = $saldoKredit,
                saldo_akhir = $saldoAkhir
            WHERE id = {$trx['id']}
        ");
    }
}

echo "OK\n\n";

// VERIFIKASI
echo "========================================\n";
echo "VERIFIKASI HASIL\n";
echo "========================================\n";

$total = $conn->query("SELECT SUM(debit) as total_debit, SUM(kredit) as total_kredit FROM buku_besar")->fetch_assoc();
$totalDebit = $total['total_debit'];
$totalKredit = $total['total_kredit'];
$selisih = $totalDebit - $totalKredit;

echo "Total Debit  : Rp " . number_format($totalDebit, 0, ',', '.') . "\n";
echo "Total Kredit : Rp " . number_format($totalKredit, 0, ',', '.') . "\n";
echo "Selisih      : Rp " . number_format($selisih, 0, ',', '.') . "\n\n";

if (abs($selisih) < 1) {
    echo "✅ NERACA SEIMBANG!\n";
} else {
    echo "❌ NERACA TIDAK SEIMBANG! Selisih: Rp " . number_format($selisih, 0, ',', '.') . "\n";
}

// Tampilkan jurnal penyesuaian yang sudah diposting
echo "\nJURNAL PENYESUAIAN YANG DIPOSTING:\n";
foreach ($totalRef as $ref) {
    $count = $conn->query("SELECT COUNT(*) as total FROM buku_besar WHERE referensi = '$ref'")->fetch_assoc()['total'];
    echo "  - $ref: $count entri\n";
}

$conn->close();
echo "\nSelesai!\n";
?>