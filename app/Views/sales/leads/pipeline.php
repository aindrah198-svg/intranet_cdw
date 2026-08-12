<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-columns mr-2"></i>Pipeline Sales (Kanban Board)</h4>
            <p class="text-muted mb-0">Tampilan visual funnel prospek sales dari awal hingga closing</p>
        </div>
        <div>
            <a href="<?= site_url('sales/leads') ?>" class="btn btn-outline-secondary mr-2">
                <i class="fas fa-list mr-1"></i> Tampilan List
            </a>
            <a href="<?= site_url('sales/leads/create') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Lead
            </a>
        </div>
    </div>

    <!-- Kanban Board Grid -->
    <div class="row flex-nowrap overflow-auto pb-4">
        <?php 
        $badgeColors = ['Baru' => 'secondary', 'Follow Up' => 'info', 'Negosiasi' => 'warning', 'Closing' => 'success', 'Hilang' => 'danger'];
        foreach ($statuses as $st): 
            $items = $pipeline[$st] ?? [];
        ?>
            <div class="col-md-3" style="min-width: 280px;">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center py-3">
                        <span><span class="badge badge-<?= $badgeColors[$st] ?? 'secondary' ?> mr-2"><?= $st ?></span></span>
                        <span class="badge badge-pill badge-light border"><?= count($items) ?></span>
                    </div>
                    <div class="card-body p-2" style="min-height: 450px;">
                        <?php if (empty($items)): ?>
                            <div class="text-center py-4 text-muted small">Tidak ada lead di status ini.</div>
                        <?php else: ?>
                            <?php foreach ($items as $lead): ?>
                                <div class="card border-0 shadow-sm mb-2 p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="font-weight-bold mb-0 text-dark"><?= esc($lead['nama_lead']) ?></h6>
                                        <code><?= esc($lead['kode_lead']) ?></code>
                                    </div>
                                    <p class="text-muted small mb-2"><?= esc($lead['perusahaan'] ?? '-') ?></p>
                                    <div class="font-weight-bold text-success mb-2">Rp <?= number_format($lead['nilai_potensi'], 0, ',', '.') ?></div>
                                    
                                    <!-- Dropdown Ganti Status -->
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <small class="text-muted"><?= esc($lead['sumber_lead']) ?></small>
                                        <select class="form-control form-control-sm w-auto change-status-select" data-id="<?= $lead['id'] ?>">
                                            <?php foreach ($statuses as $sOption): ?>
                                                <option value="<?= $sOption ?>" <?= ($lead['status'] == $sOption) ? 'selected' : '' ?>><?= $sOption ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.change-status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var leadId = this.getAttribute('data-id');
            var newStatus = this.value;
            
            var formData = new FormData();
            formData.append('id', leadId);
            formData.append('status', newStatus);
            
            fetch('<?= site_url('sales/leads/update-status') ?>', {
                method: 'POST',
                body: formData
            }).then(function(response) {
                if (response.ok) {
                    window.location.reload();
                }
            });
        });
    });
});
</script>
