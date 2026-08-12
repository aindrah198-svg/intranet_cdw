<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-tasks text-primary me-2"></i> Tugas Saya</h5>
        <small class="text-muted">Daftar tugas development & maintenance yang ditugaskan kepada Anda</small>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Task / Fitur</th>
                        <th>Milestone</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tugas)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada tugas spesifik ditugaskan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tugas as $t): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($t['task_name']) ?></div>
                                    <small class="text-muted"><?= esc($t['deskripsi']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border code-font"><?= esc($t['milestone_sprint'] ?: 'Sprint') ?></span></td>
                                <td><span class="badge bg-secondary"><?= esc(strtoupper($t['priority'])) ?></span></td>
                                <td>
                                    <?php
                                    $stBadge = 'bg-secondary';
                                    if ($t['status'] === 'in_progress') $stBadge = 'bg-primary';
                                    if ($t['status'] === 'review') $stBadge = 'bg-warning text-dark';
                                    if ($t['status'] === 'done') $stBadge = 'bg-success';
                                    ?>
                                    <span class="badge <?= $stBadge ?>"><?= esc(strtoupper(str_replace('_', ' ', $t['status']))) ?></span>
                                </td>
                                <td><small class="code-font"><?= $t['due_date'] ? date('d M Y', strtotime($t['due_date'])) : '-' ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
