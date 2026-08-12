<?php
$data = ['title' => 'Slip Gaji Saya', 'subtitle' => 'Daftar Slip Gaji Bulanan Administrator', 'active' => 'slip-gaji', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <h5 style="color:#4a148c;font-weight:700;" class="mb-4"><i class="fas fa-money-bill-wave me-2"></i>Slip Gaji Saya</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Periode Gaji</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Potongan</th><th>Gaji Bersih</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Juli 2025</td><td>Rp 6.500.000</td><td>Rp 1.200.000</td><td>Rp 250.000</td><td>Rp 7.450.000</td>
                    <td><button class="btn btn-sm btn-outline-purple"><i class="fas fa-file-pdf me-1"></i>Unduh PDF</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
