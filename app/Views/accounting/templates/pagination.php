<?php
// Custom pagination template untuk Accounting module
if (isset($pager) && !empty($pager)): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <!-- First -->
        <?php if (isset($pager['first']) && $pager['first']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['first'] ?>" aria-label="First">
                <span aria-hidden="true"><i class="fas fa-angle-double-left"></i></span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link"><i class="fas fa-angle-double-left"></i></span>
        </li>
        <?php endif; ?>
        
        <!-- Previous -->
        <?php if (isset($pager['previous']) && $pager['previous']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['previous'] ?>" aria-label="Previous">
                <span aria-hidden="true"><i class="fas fa-angle-left"></i></span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link"><i class="fas fa-angle-left"></i></span>
        </li>
        <?php endif; ?>
        
        <!-- Pages -->
        <?php if (isset($pager['links']) && is_array($pager['links'])): ?>
            <?php foreach ($pager['links'] as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Next -->
        <?php if (isset($pager['next']) && $pager['next']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['next'] ?>" aria-label="Next">
                <span aria-hidden="true"><i class="fas fa-angle-right"></i></span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link"><i class="fas fa-angle-right"></i></span>
        </li>
        <?php endif; ?>
        
        <!-- Last -->
        <?php if (isset($pager['last']) && $pager['last']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['last'] ?>" aria-label="Last">
                <span aria-hidden="true"><i class="fas fa-angle-double-right"></i></span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link"><i class="fas fa-angle-double-right"></i></span>
        </li>
        <?php endif; ?>
    </ul>
    
    <!-- Page info -->
    <?php if (isset($pager['current']) && isset($pager['total'])): ?>
    <div class="text-center text-muted mt-2">
        <small>
            Menampilkan halaman <?= $pager['current'] ?> dari <?= $pager['total_pages'] ?? ceil($pager['total'] / ($pager['per_page'] ?? 10)) ?> 
            (Total: <?= number_format($pager['total'], 0, ',', '.') ?> data)
        </small>
    </div>
    <?php endif; ?>
</nav>

<!-- Alternative pagination style for financial reports -->
<div class="row mt-3 d-none d-print-block">
    <div class="col-12 text-center">
        <small class="text-muted">
            Halaman <?= $pager['current'] ?? 1 ?> dari <?= $pager['total_pages'] ?? 1 ?>
        </small>
    </div>
</div>

<?php endif; ?>

<?php
// Alternatif: Jika pager adalah string (dari makeLinks())
if (isset($pager) && is_string($pager)): ?>
    <div class="mt-4">
        <?= $pager ?>
    </div>
<?php endif; ?>

<!-- No data message -->
<?php if (isset($pager) && isset($pager['total']) && $pager['total'] == 0): ?>
<div class="alert alert-info mt-4 text-center">
    <i class="fas fa-info-circle me-2"></i>
    Tidak ada data yang ditemukan.
</div>
<?php endif; ?>