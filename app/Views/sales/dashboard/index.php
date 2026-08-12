<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard Sales & Marketing</h4>
            <p class="text-muted mb-0">Overview performa leads, pipeline, quotation, dan target penjualan</p>
        </div>
        <div>
            <a href="<?= site_url('sales/leads/create') ?>" class="btn btn-primary shadow-sm mr-2">
                <i class="fas fa-user-plus mr-1"></i> Tambah Lead Baru
            </a>
            <a href="<?= site_url('sales/deal/create') ?>" class="btn btn-success shadow-sm">
                <i class="fas fa-handshake mr-1"></i> Catat Closing Deal
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Leads Aktif</small>
                    <h3 class="mb-0 mt-2 font-weight-bold"><?= $leadsAktif ?></h3>
                    <small class="text-white-50">Dari total <?= $totalLeads ?> leads</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Closing Bulan Ini</small>
                    <h3 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format($nilaiClosingBulanIni, 0, ',', '.') ?></h3>
                    <small class="text-white-50"><?= $jumlahDealBulanIni ?> deal terkonfirmasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Target vs Realisasi</small>
                    <h3 class="mb-0 mt-2 font-weight-bold"><?= $persenRealisasi ?>%</h3>
                    <small class="text-white-50">Target: Rp <?= number_format($targetBulanIni, 0, ',', '.') ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Quotation Pending</small>
                    <h3 class="mb-0 mt-2 font-weight-bold"><?= $quotationPending ?></h3>
                    <small class="text-dark-50">Penawaran menunggu respon</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Funnel Stats -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-funnel-dollar mr-2"></i>Status Leads & Pipeline Funnel</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <?php 
                $badgeColors = ['Baru' => 'secondary', 'Follow Up' => 'info', 'Negosiasi' => 'warning', 'Closing' => 'success', 'Hilang' => 'danger'];
                foreach ($pipelineStats as $st => $count): 
                ?>
                    <div class="col">
                        <div class="p-3 rounded border bg-light">
                            <span class="badge badge-<?= $badgeColors[$st] ?? 'secondary' ?> mb-2"><?= $st ?></span>
                            <h4 class="font-weight-bold mb-0"><?= $count ?></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-clock mr-2"></i>Leads Terbaru</h6>
            <a href="<?= site_url('sales/leads') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua Leads</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Lead</th>
                            <th>Perusahaan</th>
                            <th>Sumber</th>
                            <th class="text-right">Nilai Potensi</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentLeads)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data leads.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentLeads as $lead): ?>
                                <tr>
                                    <td><code><?= esc($lead['kode_lead']) ?></code></td>
                                    <td><strong><?= esc($lead['nama_lead']) ?></strong></td>
                                    <td><?= esc($lead['perusahaan'] ?? '-') ?></td>
                                    <td><span class="badge badge-light border"><?= esc($lead['sumber_lead']) ?></span></td>
                                    <td class="text-right">Rp <?= number_format($lead['nilai_potensi'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $badgeColors[$lead['status']] ?? 'secondary' ?>"><?= esc($lead['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>