<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="fas fa-running text-warning me-2"></i> Timeline & Sprint Breakdown</h5>
</div>

<div class="card card-custom">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sprint / Milestone</th>
                        <th>Sistem</th>
                        <th>Task Name</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada sprint milestone yang terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $t): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-warning text-dark fw-semibold code-font">
                                        <i class="fas fa-flag me-1"></i> <?= esc($t['milestone_sprint'] ?: 'General Milestone') ?>
                                    </span>
                                </td>
                                <td class="fw-bold"><?= esc($t['nama_sistem'] ?: 'CDW System') ?></td>
                                <td><?= esc($t['task_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc(strtoupper($t['priority'])) ?></span></td>
                                <td>
                                    <?php
                                    $stBadge = 'bg-secondary';
                                    if ($t['status'] === 'in_progress') $stBadge = 'bg-primary';
                                    if ($t['status'] === 'review') $stBadge = 'bg-warning text-dark';
                                    if ($t['status'] === 'done') $stBadge = 'bg-success';
                                    ?>
                                    <span class="badge <?= $stBadge ?>"><?= esc(strtoupper(str_replace('_', ' ', $t['status']))) ?></span>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user me-1"></i> <?= esc($t['assigned_to']) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
