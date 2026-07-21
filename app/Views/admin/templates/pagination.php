<?php
// Custom pagination template untuk Bootstrap 5
if (isset($pager) && !empty($pager)): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- First -->
        <?php if (isset($pager['first']) && $pager['first']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['first'] ?>" aria-label="First">
                <span aria-hidden="true">&laquo;&laquo;</span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link">&laquo;&laquo;</span>
        </li>
        <?php endif; ?>
        
        <!-- Previous -->
        <?php if (isset($pager['previous']) && $pager['previous']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['previous'] ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link">&laquo;</span>
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
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link">&raquo;</span>
        </li>
        <?php endif; ?>
        
        <!-- Last -->
        <?php if (isset($pager['last']) && $pager['last']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager['last'] ?>" aria-label="Last">
                <span aria-hidden="true">&raquo;&raquo;</span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link">&raquo;&raquo;</span>
        </li>
        <?php endif; ?>
    </ul>
    
    <!-- Page info -->
    <?php if (isset($pager['current']) && isset($pager['total'])): ?>
    <div class="text-center text-muted mt-2">
        Halaman <?= $pager['current'] ?> dari <?= $pager['total_pages'] ?? ceil($pager['total'] / ($pager['per_page'] ?? 10)) ?> 
        (Total: <?= $pager['total'] ?> data)
    </div>
    <?php endif; ?>
</nav>
<?php endif; ?>

<?php
// Alternatif: Jika pager adalah string (dari makeLinks())
if (isset($pager) && is_string($pager)): ?>
    <?= $pager ?>
<?php endif; ?>