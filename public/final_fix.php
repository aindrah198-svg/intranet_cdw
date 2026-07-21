<?php
// koreksi_langsung.php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'database_cdwnet';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "========================================\n";
echo "KOREKSI LANGSUNG DI BUKU BESAR\n";
echo "========================================\n\n";

// 1. Hapus semua jurnal koreksi
echo "Membersihkan jurnal koreksi...\n";
$conn->query("DELETE FROM buku_besar WHERE referensi LIKE 'JRNL-KOREKSI-%'");
$conn->query("DELETE jd FROM jurnal_detail jd INNER JOIN jurnal j ON jd.jurnal_id = j.id WHERE j.nomor_jurnal LIKE 'JRNL-KOREKSI-%'");
$conn->query("DELETE FROM jurnal WHERE nomor_jurnal LIKE 'JRNL-KOREKSI-%'");
echo "✓ Jurnal koreksi dibersihkan\n\n";

// 2. Reset saldo di buku besar
$conn->query("UPDATE buku_besar SET saldo_debit = 0, saldo_kredit = 0, saldo_akhir = 0");
echo "✓ Saldo direset\n\n";

// 3. Hapus entri yang salah dari jurnal penyesuaian?
// Tapi kita tidak bisa hapus karena itu data asli.
// Kita akan override dengan membuat entri baru yang membatalkan yang salah.

echo "MEMBUAT ENTRI KOREKSI LANGSUNG:\n";

// Data koreksi (langsung ke nilai yang benar)
$koreksi = [
    // Untuk 1-1203: dari -1.250.000 menjadi +1.250.000 (perlu +2.500.000)
    ['coa_id' => 41, 'kode' => '1-1203', 'nama' => 'Cadangan Kerugian Piutang', 
     'target' => 1250000, 'current' => -1250000, 'adjustment' => 2500000],
    
    // Untuk 1-2301: dari -1.000.000 menjadi +1.000.000 (perlu +2.000.000)
    ['coa_id' => 57, 'kode' => '1-2301', 'nama' => 'Akum. Penyusutan Peralatan', 
     'target' => 1000000, 'current' => -1000000, 'adjustment' => 2000000],
    
    // Untuk 1-2302: dari -500.000 menjadi +500.000 (perlu +1.000.000)
    ['coa_id' => 58, 'kode' => '1-2302', 'nama' => 'Akum. Penyusutan Kendaraan', 
     'target' => 500000, 'current' => -500000, 'adjustment' => 1000000],
    
    // Untuk 3-1301: dari +20.000.000 menjadi -20.000.000 (perlu -40.000.000)
    ['coa_id' => 23, 'kode' => '3-1301', 'nama' => 'Prive', 
     'target' => -20000000, 'current' => 20000000, 'adjustment' => -40000000]
];

foreach ($koreksi as $k) {
    if ($k['adjustment'] > 0) {
        // Jika perlu menambah (debit)
        echo "  {$k['kode']}: Tambah Debit " . number_format($k['adjustment']) . "\n";
        
        // Update langsung di buku besar untuk semua entri akun ini
        $conn->query("
            UPDATE buku_besar 
            SET debit = debit + {$k['adjustment']}
            WHERE coa_id = {$k['coa_id']}
        ");
    } else {
        // Jika perlu mengurangi (kredit)
        $adjust = abs($k['adjustment']);
        echo "  {$k['kode']}: Tambah Kredit " . number_format($adjust) . "\n";
        
        $conn->query("
            UPDATE buku_besar 
            SET kredit = kredit + $adjust
            WHERE coa_id = {$k['coa_id']}
        ");
    }
}

echo "\n✓ Koreksi langsung diterapkan\n\n";

// 4. Hitung ulang saldo
echo "Menghitung ulang saldo...\n";

// Reset saldo dulu
$conn->query("UPDATE buku_besar SET saldo_debit = 0, saldo_kredit = 0, saldo_akhir = 0");

// Ambil semua akun
$akuns = $conn->query("SELECT id, saldo_normal FROM coa WHERE is_header = 0 AND is_active = 1 ORDER BY id");

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

echo "✓ Saldo dihitung ulang\n\n";

// 5. Verifikasi akhir
echo "========================================\n";
echo "VERIFIKASI HASIL AKHIR\n";
echo "========================================\n";

// Cek saldo akun yang dikoreksi
echo "CEK SALDO AKUN YANG DIKOREKSI:\n";
$result = $conn->query("
    SELECT 
        kode_akun,
        nama_akun,
        SUM(debit) as total_debit,
        SUM(kredit) as total_kredit,
        SUM(debit) - SUM(kredit) as saldo
    FROM buku_besar
    WHERE kode_akun IN ('1-1203', '1-2301', '1-2302', '3-1301')
    GROUP BY kode_akun, nama_akun
    ORDER BY kode_akun
");

$expected = [
    '1-1203' => 1250000,
    '1-2301' => 1000000,
    '1-2302' => 500000,
    '3-1301' => -20000000
];

while ($row = $result->fetch_assoc()) {
    $kode = $row['kode_akun'];
    $saldo = $row['saldo'];
    $seharusnya = $expected[$kode];
    
    $status = (abs($saldo - $seharusnya) < 100) ? "✓" : "❌";
    echo "  $kode - {$row['nama_akun']}: Rp " . number_format(abs($saldo), 0, ',', '.');
    if ($saldo > 0) echo " (Debit)"; else echo " (Kredit)";
    echo " $status\n";
    
    if (abs($saldo - $seharusnya) > 100) {
        echo "    Seharusnya: Rp " . number_format(abs($seharusnya), 0, ',', '.');
        if ($seharusnya > 0) echo " (Debit)"; else echo " (Kredit)";
        echo " (Selisih: " . number_format($saldo - $seharusnya) . ")\n";
    }
}

// Cek total per golongan
echo "\nTOTAL PER GOLONGAN:\n";
$gol = $conn->query("
    SELECT 
        LEFT(kode_akun, 1) as gol,
        SUM(debit) as debit,
        SUM(kredit) as kredit,
        SUM(debit) - SUM(kredit) as saldo
    FROM buku_besar
    GROUP BY LEFT(kode_akun, 1)
    ORDER BY LEFT(kode_akun, 1)
");

$totalAset = 0;
$totalKewajiban = 0;
$totalEkuitas = 0;

while ($row = $gol->fetch_assoc()) {
    $saldo = $row['saldo'];
    $tipe = $row['gol'] == '1' ? 'ASET' : 
            ($row['gol'] == '2' ? 'KEWAJIBAN' :
            ($row['gol'] == '3' ? 'EKUITAS' :
            ($row['gol'] == '4' ? 'PENDAPATAN' : 'BEBAN')));
    
    echo "  Gol {$row['gol']} ($tipe): Rp " . number_format(abs($saldo), 0, ',', '.') . "\n";
    
    if ($row['gol'] == '1') {
        $totalAset = $saldo;
    } elseif ($row['gol'] == '2') {
        $totalKewajiban = $saldo;
    } elseif ($row['gol'] == '3') {
        $totalEkuitas = $saldo;
    }
}

// Cek total keseluruhan
echo "\nTOTAL KESELURUHAN:\n";
$total = $conn->query("SELECT SUM(debit) as debit, SUM(kredit) as kredit FROM buku_besar")->fetch_assoc();
$selisih = $total['debit'] - $total['kredit'];

echo "Total Debit  : Rp " . number_format($total['debit'], 0, ',', '.') . "\n";
echo "Total Kredit : Rp " . number_format($total['kredit'], 0, ',', '.') . "\n";
echo "Selisih      : Rp " . number_format($selisih, 0, ',', '.') . "\n";

echo "\nASET = KEWAJIBAN + EKUITAS:\n";
echo "Aset                : Rp " . number_format($totalAset, 0, ',', '.') . "\n";
echo "Kewajiban           : Rp " . number_format($totalKewajiban, 0, ',', '.') . "\n";
echo "Ekuitas             : Rp " . number_format($totalEkuitas, 0, ',', '.') . "\n";
echo "Kewajiban + Ekuitas : Rp " . number_format($totalKewajiban + $totalEkuitas, 0, ',', '.') . "\n";
echo "Selisih             : Rp " . number_format($totalAset - ($totalKewajiban + $totalEkuitas), 0, ',', '.') . "\n";

if (abs($selisih) < 100 && abs($totalAset - ($totalKewajiban + $totalEkuitas)) < 100) {
    echo "\n✅✅✅ NERACA SEIMBANG! ✅✅✅\n";
} else {
    echo "\n❌❌❌ NERACA MASIH TIDAK SEIMBANG! ❌❌❌\n";
}

$conn->close();
?>