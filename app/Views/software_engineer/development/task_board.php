<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="fas fa-tasks text-primary me-2"></i> Task Board Kanban (To Do → In Progress → Review → Done)</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTask">
        <i class="fas fa-plus me-1"></i> Tambah Task Baru
    </button>
</div>

<!-- Kanban Columns -->
<div class="row g-3">
    <?php
    $statuses = [
        'todo'        => ['title' => 'To Do', 'bg' => 'bg-secondary', 'badge' => 'bg-secondary'],
        'in_progress' => ['title' => 'In Progress', 'bg' => 'bg-primary', 'badge' => 'bg-primary'],
        'review'      => ['title' => 'Review', 'bg' => 'bg-warning', 'badge' => 'bg-warning text-dark'],
        'done'        => ['title' => 'Done', 'bg' => 'bg-success', 'badge' => 'bg-success']
    ];
    ?>

    <?php foreach ($statuses as $stKey => $stMeta): ?>
        <div class="col-md-3">
            <div class="card card-custom h-100 bg-light bg-opacity-50">
                <div class="card-header <?= $stMeta['bg'] ?> text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><?= $stMeta['title'] ?></span>
                    <?php
                    $count = 0;
                    foreach ($tasks as $t) {
                        if ($t['status'] === $stKey) $count++;
                    }
                    ?>
                    <span class="badge bg-white text-dark rounded-circle"><?= $count ?></span>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 70vh;">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === $stKey): ?>
                            <div class="card mb-2 border-0 shadow-sm p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-cyan text-dark small"><?= esc($task['nama_sistem'] ?? 'General Dev') ?></span>
                                    <span class="badge bg-light text-dark border"><?= esc(strtoupper($task['priority'])) ?></span>
                                </div>
                                <h6 class="fw-bold mb-1"><?= esc($task['task_name']) ?></h6>
                                <p class="text-muted small mb-2"><?= esc($task['deskripsi']) ?></p>
                                
                                <?php if (!empty($task['milestone_sprint'])): ?>
                                    <div class="mb-2"><small class="text-primary code-font"><i class="fas fa-flag me-1"></i> <?= esc($task['milestone_sprint']) ?></small></div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <small class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-user me-1"></i> <?= esc($task['assigned_to']) ?></small>
                                    
                                    <!-- Status Selector Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-xs btn-outline-secondary dropdown-toggle py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="dropdown">
                                            Pindah Status
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end small">
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $task['id'] ?>, 'todo')">To Do</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $task['id'] ?>, 'in_progress')">In Progress</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $task['id'] ?>, 'review')">Review</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $task['id'] ?>, 'done')">Done</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Tambah Task -->
<div class="modal fade" id="modalTambahTask" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/development/task/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-1"></i> Tambah Task Development</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sistem / Project Target</label>
                    <select name="system_id" class="form-select">
                        <option value="">-- Tanpa Sistem (Umum) --</option>
                        <?php foreach ($systems as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama_sistem']) ?> (<?= esc($s['kode_sistem']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Task / Fitur</label>
                    <input type="text" name="task_name" class="form-control" required placeholder="Contoh: Implementasi JWT Auth API">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Milestone / Sprint</label>
                    <input type="text" name="milestone_sprint" class="form-control" placeholder="Contoh: Sprint 3 - Core Security">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi Task</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Detail requirement dan kriteria pengujian..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status Awal</label>
                        <select name="status" class="form-select">
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Task</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateStatus(id, newStatus) {
        $.ajax({
            url: '<?= site_url("software-engineer/development/task/update-status") ?>',
            type: 'POST',
            data: { task_id: id, status: newStatus },
            success: function() {
                location.reload();
            }
        });
    }
</script>

<?= view('software_engineer/templates/footer') ?>
