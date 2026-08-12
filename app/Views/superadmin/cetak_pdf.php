<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            font-size: 13px;
        }

        .pdf-container {
            max-width: 900px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .header-logo-title {
            border-bottom: 3px double #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .doc-badge {
            background: #0f172a;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .section-header {
            background: #f1f5f9;
            border-left: 4px solid #0d6efd;
            padding: 8px 14px;
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            margin-top: 24px;
            margin-bottom: 12px;
            border-radius: 0 8px 8px 0;
        }

        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 10px;
            background: #ffffff;
        }

        .wa-box {
            background: #f0fdf4;
            border: 1px dashed #22c55e;
            border-radius: 12px;
            padding: 16px;
            font-family: monospace;
            font-size: 12px;
            color: #14532d;
            white-space: pre-wrap;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .pdf-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
            .wa-box {
                border: 1px solid #cbd5e1 !important;
                background: #f8fafc !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body>

<!-- Floating Action Bar for Print & Copy WA -->
<div class="no-print position-fixed top-0 start-50 translate-middle-x mt-3 z-3 bg-white p-2.5 rounded-pill shadow-lg border d-flex gap-2 align-items-center">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">
        <i class="fas fa-print me-1.5"></i> Cetak / Simpan PDF
    </button>
    <button onclick="copyWaFormat()" class="btn btn-success rounded-pill px-4 btn-sm fw-bold">
        <i class="fab fa-whatsapp me-1.5"></i> Salin Ringkasan WA
    </button>
    <a href="<?= base_url('superadmin') ?>" class="btn btn-outline-secondary rounded-pill px-3 btn-sm fw-semibold">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="pdf-container">
    <!-- Official Header Document -->
    <div class="header-logo-title d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="company-title"><i class="fas fa-building text-primary me-2"></i> CDW ENGINEERING INTRANET</div>
            <small class="text-muted">Laporan Rekapitulasi Pembaruan Fitur & File Sistem (Direktur & Admin)</small>
        </div>
        <div class="text-end">
            <span class="doc-badge">DOCUMENT REF: REKAP-SYS-<?= date('Ymd') ?></span>
            <div class="text-muted text-xs mt-1"><i class="far fa-clock me-1"></i> Dicetak: <?= $printDate ?> WIB</div>
            <div class="text-muted text-xs"><i class="fas fa-user-shield me-1"></i> Oleh: <?= esc($user['name']) ?></div>
        </div>
    </div>

    <!-- Ringkasan Eksekutif -->
    <div class="alert alert-primary border-0 rounded-3 p-3 mb-4 text-xs">
        <h6 class="fw-bold mb-1"><i class="fas fa-info-circle me-1.5"></i> Ringkasan Eksekutif Pembaruan Sistem</h6>
        Dokumen ini menyajikan rekapitulasi ringkas mengenai seluruh fitur, alur kerja, dan berkas sistem yang telah berhasil dibuat dan diperbarui pada modul <strong>Direktur</strong> dan <strong>Admin</strong> di Intranet CDW Engineering.
    </div>

    <!-- SECTION 1: MODUL & FITUR DIREKTUR -->
    <div class="section-header">
        <i class="fas fa-user-tie text-primary me-2"></i> 1. REKAPITULASI FITUR SISI DIREKTUR
    </div>

    <div class="row g-2">
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-calculator text-primary me-1.5"></i> Cuti Karyawan & Recalculate Kuota</div>
                <small class="text-muted d-block mt-0.5">Penghitungan dinamis kuota cuti karyawan (`recalculateKuotaCuti`). Otomatis memotong kuota saat disetujui & mengembalikan kuota jika ditolak/dihapus.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-tasks text-success me-1.5"></i> Penugasan Harian (Dedicated Page)</div>
                <small class="text-muted d-block mt-0.5">Pembaruan halaman penugasan tanpa modal (`/direktur/penugasan/tambah`, `/detail`, `/edit`). Dilengkapi multi-item task checklist dinamis.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-user-check text-info me-1.5"></i> Approval Permohonan & Cuti</div>
                <small class="text-muted d-block mt-0.5">Sistem verifikasi persetujuan permohonan cuti, izin, kasbon, BAST, dan pembelian barang dengan bukti lampiran foto terkompresi.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-clock text-warning me-1.5"></i> Monitoring Absensi Real-Time</div>
                <small class="text-muted d-block mt-0.5">Pemantauan riwayat log absensi masuk & pulang karyawan/admin secara langsung (*real-time*) terhubung dari sistem mandiri.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-stream text-danger me-1.5"></i> Timeline Kerja & Milestone Proyek</div>
                <small class="text-muted d-block mt-0.5">Pengelolaan eksekusi jadwal harian/mingguan proyek, pengaktifan proyek inisiasi, status %, serta pencarian barang proyek.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-boxes text-purple me-1.5"></i> Pengadaan, Stok ATK & Gudang</div>
                <small class="text-muted d-block mt-0.5">Manajemen stok barang inventaris, aset perusahaan, serta laporan keuangan, kasbon, dan ringkasan penggajian.</small>
            </div>
        </div>
    </div>

    <!-- SECTION 2: MODUL & FITUR ADMIN -->
    <div class="section-header">
        <i class="fas fa-user-gear text-success me-2"></i> 2. REKAPITULASI FITUR SISI ADMIN
    </div>

    <div class="row g-2">
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-sitemap text-primary me-1.5"></i> Restrukturisasi Sidebar Menu</div>
                <small class="text-muted d-block mt-0.5">Pengelompokan kategori **Laporan & Keluhan** (`Laporan Kerja Harian`, `Keluhan`, `Slip Gaji`) dan **MENU PRIBADI** (`Absensi`, `Tugas Hari Ini`, `Timeline Kerja`, `Profil`).</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-fingerprint text-success me-1.5"></i> Absensi Saya (Terhubung Direktur)</div>
                <small class="text-muted d-block mt-0.5">Routing `/admin/absensi-saya` terhubung ke controller `Pribadi::absensi`, `checkin`, dan `checkout` secara real-time ke monitoring Direktur.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-list-check text-info me-1.5"></i> Tugas Saya (Sub-Item Checklist)</div>
                <small class="text-muted d-block mt-0.5">Penghilangan tombol "Kerjakan", fasilitas update status sub-item bertahap / sekaligus ("Tandai Semua Selesai"), dan kunci pembuatan laporan sebelum 100% selesai.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-file-export text-warning me-1.5"></i> Laporan Kerja Harian (Auto Pre-Fill)</div>
                <small class="text-muted d-block mt-0.5">Full CRUD (`/admin/laporan/kerja-harian`). Tugas selesai otomatis dikonversi ke form pratinjau & edit laporan sebelum dikirim ke Direktur.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-project-diagram text-danger me-1.5"></i> Timeline Kerja (Sync Direktur 100%)</div>
                <small class="text-muted d-block mt-0.5">Routing & tampilan `admin/timeline-kerja` diselaraskan 100% dengan data Direktur, termasuk fitur update task, progres %, dan status proyek.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="fw-bold text-dark"><i class="fas fa-user-circle text-purple me-1.5"></i> Profil Saya & Glassmorphism UI</div>
                <small class="text-muted d-block mt-0.5">Tampilan profil modern glassmorphism, pengukur kekuatan password (*password strength meter*), toggle show/hide pass, dan hak akses.</small>
            </div>
        </div>
    </div>

    <!-- SECTION 3: DAFTAR BERKAS UTAMA -->
    <div class="section-header">
        <i class="fas fa-folder-tree text-warning me-2"></i> 3. DAFTAR BERKAS & SCRIPT UTAMA YANG DIBUAT/DIPERBARUI
    </div>

    <table class="table table-bordered table-sm text-xs align-middle">
        <thead class="table-dark">
            <tr>
                <th style="width: 150px;">Kategori Berkas</th>
                <th>Nama File & Lokasi Absolute</th>
                <th>Fungsi Utama</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold">Controller Files</td>
                <td>`App\Controllers\Admin\Pribadi.php`<br>`App\Controllers\Admin\Laporan.php`<br>`App\Controllers\Direktur\PenugasanController.php`<br>`App\Controllers\Auth.php`<br>`App\Controllers\Superadmin.php`</td>
                <td>Logika bisnis tugas, laporan harian, timeline sync, absensi mandiri, auth seeding, dan superadmin portal.</td>
            </tr>
            <tr>
                <td class="fw-bold">Route Files</td>
                <td>`app/Config/Routes/AdminRoutes.php`<br>`app/Config/Routes/DirekturRoutes.php`<br>`app/Config/Routes/SuperadminRoutes.php`<br>`app/Config/Routes.php`</td>
                <td>Pendaftaran endpoint URL `/admin/*`, `/direktur/*`, `/superadmin/*`, dan pemuatan otomatis.</td>
            </tr>
            <tr>
                <td class="fw-bold">Admin View Files</td>
                <td>`admin/pribadi/tugas.php`, `tugas_detail.php`<br>`admin/pribadi/timeline_kerja.php`, `timeline_kerja_detail.php`<br>`admin/pribadi/profil.php`, `absensi.php`<br>`admin/laporan/kerja_harian*.php`</td>
                <td>Tampilan antarmuka staf admin untuk penugasan, laporan kerja harian, timeline proyek, profil, dan absensi.</td>
            </tr>
            <tr>
                <td class="fw-bold">Direktur View Files</td>
                <td>`direktur/penugasan/index.php`, `tambah.php`, `detail.php`, `edit.php`<br>`direktur/proyek/timeline.php`, `detail_timeline.php`</td>
                <td>Tampilan manajemen penugasan dedicated tanpa modal dan pengawasan timeline proyek Direktur.</td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION 4: RINGKASAN FORMAT WHATSAPP -->
    <div class="section-header">
        <i class="fab fa-whatsapp text-success me-2"></i> 4. RINGKASAN SINGKAT (FORMAT SIAP KIRIM WHATSAPP)
    </div>

    <div class="wa-box mb-4" id="waTextContent">REKAPITULASI DOKUMENTASI FITUR & FUNGSI SISTEM INTRANET CDW

Tanggal Update: <?= $printDate ?> WIB
Oleh: Superadmin System Access

==================================================
1. FITUR & FUNGSI SISI DIREKTUR (EXECUTIVE DASHBOARD)
==================================================

[ Dashboard ]
- Dashboard: Overview KPI eksekutif, statistik absensi, proyek aktif, & approval pending.

[ Karyawan & SDM ]
- Kelola Karyawan: Manajemen data induk SDM, NIK, jabatan, & profil staf.
- Kelola Akun Karyawan: Pembuatan & pengaturan akun login pengguna intranet.
- Surat (Kontrak/SP): Penerbitan & arsip surat kontrak kerja serta Surat Peringatan.
- Permohonan & Izin: Persetujuan permohonan izin operasional & kedinasan karyawan.
- Cuti Karyawan: Kelola cuti dengan sistem kalkulasi kuota otomatis & pengembalian kuota dinamis.
- Keluhan Karyawan: Pemantauan & penanganan tanggapan aspirasi/keluhan staf.
- Monitoring Absensi: Rekapitulasi log jam masuk/pulang & kehadiran karyawan real-time.

[ Penugasan & Proyek ]
- Penugasan Harian: Pendelegasian tugas ke staf via form dedicated tanpa modal + multi-item checklist.
- Laporan Kerja Harian: Pengawasan & review laporan hasil kerja harian karyawan.
- Monitoring Laporan: Pemantauan rekapitulasi status laporan harian staf secara menyeluruh.
- Project Baru: Inisiasi & pendaftaran proyek baru perusahaan.
- Timeline Kerja: Pengelolaan jadwal milestone harian/mingguan proyek & progres %.
- Project Selesai: Arsip & rekapitulasi proyek yang telah diselesaikan.
- Penugasan Pencarian: Pendelegasian tugas pencarian barang/material proyek.

[ Keuangan ]
- Penggajian Karyawan: Pengawasan payroll, rincian komponen gaji, & cetak slip gaji.
- Kasbon: Approval & pencatatan pengajuan pinjaman/kasbon karyawan.
- Laporan Keuangan: Rekapitulasi transaksi keuangan & arus kas operasional.

[ Pengadaan & Aset ]
- Pengajuan ATK: Persetujuan & pengawasan permintaan Alat Tulis Kantor.
- Monitoring Stok ATK: Kontrol sisa stok inventaris ATK secara real-time.
- Pengadaan Aset: Persetujuan pembelian & pencatatan aset inventaris perusahaan.
- Pencatatan & Tracking Pembelian (PR): Tracking status Purchase Request & barang masuk.
- Kerusakan Alat: Laporan & penanganan kerusakan alat/fasilitas operasional.
- Monitoring Gudang: Pengawasan keluar-masuk stok barang & material gudang.

[ Dokumen ]
- Dokumen Penting: Penyimpanan & pengelolaan arsip dokumen legalitas perusahaan.
- Dokumen Sertifikat: Pengarsipan sertifikat perusahaan, lisensi, & keahlian.
- Kontak Project: Direktori data kontak klien, vendor, & stakeholder proyek.

==================================================
2. FITUR & FUNGSI SISI ADMIN (ADMIN PANEL)
==================================================

[ Dashboard ]
- Dashboard: Monitoring aktivitas harian admin, tugas pending, & ringkasan operasional.

[ Administrasi ]
- Surat Menyurat: Pengelolaan arsip surat masuk, surat keluar, & surat jalan operasional.

[ Pengadaan & Aset ]
- Pengajuan ATK: Pembuatan & pengajuan kebutuhan Alat Tulis Kantor.
- Monitoring Stok ATK: Pengecekan sisa stok ATK gudang operasional.
- Pengadaan Aset: Penginputan & pencatatan barang aset kantor baru.
- Pencatatan & Tracking Pembelian (PR): Input pengajuan Purchase Request & tracking barang.
- Kerusakan Alat: Pelaporan unit peralatan operasional yang mengalami kerusakan.
- Monitoring Gudang: Pencatatan inventarisasi keluar-masuk fisik barang di gudang.

[ Dokumen ]
- Dokumen Penting: Akses & pengarsipan berkas dokumen penting kantor.
- Dokumen Sertifikat: Pengarsipan berkas sertifikat & dokumen perizinan.
- Kontak Project: Pencatatan nomor kontak klien, supplier, & mitra proyek.

[ Fasilitas & Tamu ]
- Buku Tamu: Pencatatan identitas & tujuan kunjungan tamu perusahaan.
- Booking Ruang Meeting: Reservasi jadwal pemakaian ruang rapat/meeting kantor.
- Koordinasi Kendaraan: Pengaturan jadwal & pemakaian kendaraan operasional kantor.

[ Pengajuan ]
- Pengajuan: Penginputan form pengajuan operasional umum staf.
- Cuti: Pengajuan permohonan cuti mandiri admin ke pimpinan.
- Kasbon: Pengajuan pinjaman/kasbon staf & karyawan yang terhubung real-time ke Direktur (http://localhost:8080/direktur/keuangan/kasbon).
- Keluhan: Pelaporan kendala atau keluhan operasional kerja yang terhubung real-time ke Direktur (http://localhost:8080/direktur/karyawan/keluhan).

[ Laporan & Keluhan ]
- Laporan Kerja Harian: Full CRUD laporan kerja harian + konversi otomatis tugas selesai ke form pratinjau/edit.
- Keluhan: Pelaporan & pemantauan keluhan karyawan terhubung real-time dengan Direktur (http://localhost:8080/direktur/karyawan/keluhan).
- Slip Gaji: Pemeriksaan & pencetakan slip gaji bulanan mandiri admin.

[ Menu Pribadi ]
- Absensi: Log absensi masuk & pulang mandiri (fix route) yang terhubung real-time ke Direktur.
- Tugas Hari Ini: Pengerjaan tugas dari Direktur dengan update sub-item checklist (1 per 1 / sekaligus) & validasi 100% selesai untuk kunci laporan.
- Timeline Kerja: Pemantauan & update milestone proyek yang sinkron 100% dengan Direktur.
- Project Saat Ini: Monitoring status & rincian proyek berjalan yang sedang ditangani.
- Profil: Pengaturan data diri, tampilan glassmorphism, & password strength meter.

==================================================
3. MODUL SELANJUTNYA (UPCOMING MODULES)
==================================================
- Modul HRD (Dalam tahap pengembangan selanjutnya)
- Modul Accounting / Akuntansi (Dalam tahap pengembangan selanjutnya)
- Modul Staff / Karyawan General (Dalam tahap pengembangan selanjutnya)
- Modul Teknisi (Dalam tahap pengembangan selanjutnya)

==================================================
4. KREDENSIAL LOGIN SUPERADMIN
==================================================
- URL Login: http://localhost:8080/login
- Username: superadmin
- Password: superadminpw123

==================================================
STATUS SISTEM: 100% Aktif, Berfungsi Normal & Terintegrasi Sempurna.
==================================================</div>

    <div class="text-end text-muted text-xs pt-3 border-top">
        Intranet Management System &bull; CDW Engineering &copy; <?= date('Y') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function copyWaFormat() {
    const text = document.getElementById('waTextContent').innerText;
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Disalin!',
            text: 'Teks ringkasan format WhatsApp berhasil disalin ke clipboard. Tinggal paste di WhatsApp!',
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4 shadow-lg' }
        });
    });
}
</script>

</body>
</html>
