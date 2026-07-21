<?php
$title = $title ?? 'Struktur Chart of Accounts';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Struktur Hierarki Akun';
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Struktur Chart of Accounts</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/export') ?>" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-primary" id="expandAllBtn">
                        <i class="fas fa-expand me-1"></i> Buka Semua
                    </button>
                    <button type="button" class="btn btn-warning" id="collapseAllBtn">
                        <i class="fas fa-compress me-1"></i> Tutup Semua
                    </button>
                    <button type="button" class="btn btn-info" id="printTreeBtn">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>
    
    <?php if (session()->getFlashdata('error')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Controls Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="filterType" class="form-label">Filter Tipe Akun</label>
                        <select id="filterType" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="Aset">Aset</option>
                            <option value="Kewajiban">Kewajiban</option>
                            <option value="Ekuitas">Ekuitas</option>
                            <option value="Pendapatan">Pendapatan</option>
                            <option value="Beban">Beban</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterStatus" class="form-label">Filter Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="searchTree" class="form-label">Cari Akun</label>
                        <div class="input-group">
                            <input type="text" id="searchTree" class="form-control" placeholder="Cari kode atau nama akun...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="showInactive" checked>
                            <label class="form-check-label" for="showInactive">
                                Tampilkan akun nonaktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="statsContainer">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="modern-card modern-card-primary shadow-hover">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Akun</h6>
                        <h4 class="mb-0" id="totalAccounts">0</h4>
                        <small class="text-primary">
                            <i class="fas fa-sitemap me-1"></i>
                            Dalam struktur
                        </small>
                    </div>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="modern-card modern-card-success shadow-hover">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Akun Aktif</h6>
                        <h4 class="mb-0" id="activeAccounts">0</h4>
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <span id="activePercentage">0%</span>
                        </small>
                    </div>
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="modern-card modern-card-info shadow-hover">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Header Akun</h6>
                        <h4 class="mb-0" id="headerAccounts">0</h4>
                        <small class="text-info">
                            <i class="fas fa-folder me-1"></i>
                            Grup akun
                        </small>
                    </div>
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-folder"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="modern-card modern-card-warning shadow-hover">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Kedalaman Maks</h6>
                        <h4 class="mb-0" id="maxDepth">0</h4>
                        <small class="text-warning">
                            <i class="fas fa-layer-group me-1"></i>
                            Level hirarki
                        </small>
                    </div>
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-level-down-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tree Visualization -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap me-2"></i> Visualisasi Struktur
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" id="toggleConnections">
                            <i class="fas fa-project-diagram me-1"></i> Koneksi
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info active" id="toggleColors">
                            <i class="fas fa-palette me-1"></i> Warna
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleView" data-view="tree">
                            <i class="fas fa-exchange-alt me-1"></i> Ganti ke List
                        </button>
                    </div>
                </div>
                
                <div id="treeContainer" class="position-relative" style="min-height: 600px; overflow: auto;">
                    <!-- Tree will be rendered here by JavaScript -->
                    <div id="treeLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat struktur...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat visualisasi struktur COA...</p>
                    </div>
                    
                    <div id="noDataMessage" class="text-center py-5 d-none">
                        <i class="fas fa-tree fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data struktur</h5>
                        <p class="text-muted">Belum ada data Chart of Accounts untuk ditampilkan.</p>
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Akun Pertama
                        </a>
                    </div>
                    
                    <div id="treeCanvas" class="tree-canvas"></div>
                </div>
                
                <!-- Legend -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="mb-3"><i class="fas fa-key me-2"></i> Legenda</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="legend-icon bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 20px; height: 20px;">
                                    <i class="fas fa-folder text-white" style="font-size: 10px;"></i>
                                </div>
                                <small>Header Akun</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="legend-icon bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 20px; height: 20px;">
                                    <i class="fas fa-file text-white" style="font-size: 10px;"></i>
                                </div>
                                <small>Detail Akun</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="legend-line border-success me-2" style="width: 20px; height: 2px;"></div>
                                <small>Akun Aktif</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="legend-line border-danger me-2" style="width: 20px; height: 2px;"></div>
                                <small>Akun Nonaktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-mouse-pointer me-1"></i> Klik pada akun untuk melihat detail
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tree List View (Alternative - Hidden by default) -->
    <div class="row mt-4 d-none" id="listViewContainer">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-tree me-2"></i> Daftar Struktur
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleViewList" data-view="list">
                            <i class="fas fa-exchange-alt me-1"></i> Ganti ke Visual
                        </button>
                    </div>
                </div>
                
                <div id="listView" class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Kode Akun</th>
                                <th width="30%">Nama Akun</th>
                                <th width="12%">Tipe Akun</th>
                                <th width="12%">Level</th>
                                <th width="10%">Status</th>
                                <th width="10%">Jenis</th>
                                <th width="11%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="treeListBody">
                            <!-- Data will be loaded via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tree Node Detail Modal -->
<div class="modal fade" id="nodeDetailModal" tabindex="-1" aria-labelledby="nodeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nodeDetailModalLabel">Detail Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="nodeDetailContent">
                    <!-- Content will be loaded via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="nodeEditLink" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="#" id="nodeDetailLink" class="btn btn-info">
                    <i class="fas fa-external-link-alt me-1"></i> Detail Lengkap
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Load D3.js from CDN -->
<script src="https://d3js.org/d3.v7.min.js"></script>

<style>
/* Tree View Styles */
.tree-container {
    min-height: 600px;
    position: relative;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    border: 1px solid #dee2e6;
    overflow: auto;
}

.tree-canvas {
    width: 100%;
    height: 100%;
}

.tree-node {
    cursor: pointer;
    transition: all 0.3s ease;
}

.tree-node:hover {
    transform: scale(1.05);
}

.node-circle {
    transition: r 0.3s;
}

.node-circle:hover {
    r: 15;
}

.node-label {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    user-select: none;
    font-size: 12px;
}

.tree-link {
    transition: stroke 0.3s;
    fill: none;
    stroke: #dee2e6;
    stroke-width: 2;
}

.tree-link:hover {
    stroke: #4e73df;
    stroke-width: 3;
}

/* Search highlight animation */
@keyframes highlight-pulse {
    0% { background-color: #fff3cd; }
    50% { background-color: #ffeaa7; }
    100% { background-color: #fff3cd; }
}

.search-highlight {
    animation: highlight-pulse 1.5s ease-in-out;
}

/* Legend styles */
.legend-icon {
    width: 16px;
    height: 16px;
    display: inline-block;
    margin-right: 5px;
    border-radius: 3px;
}

.legend-line {
    width: 20px;
    height: 3px;
    display: inline-block;
    margin-right: 5px;
}

/* Tree list indentation */
.tree-list-indent-1 { padding-left: 20px !important; }
.tree-list-indent-2 { padding-left: 40px !important; }
.tree-list-indent-3 { padding-left: 60px !important; }
.tree-list-indent-4 { padding-left: 80px !important; }
.tree-list-indent-5 { padding-left: 100px !important; }

/* Tree node colors */
.node-color-aset { background-color: #4e73df; }
.node-color-kewajiban { background-color: #f6c23e; }
.node-color-ekuitas { background-color: #1cc88a; }
.node-color-pendapatan { background-color: #36b9cc; }
.node-color-beban { background-color: #e74a3b; }
.node-color-inactive { background-color: #dc3545; }

/* Modal styles */
.modal-node-detail {
    max-height: 70vh;
    overflow-y: auto;
}
</style>

<script>
// Global variables
let treeData = null;
let treeInstance = null;
let showConnections = true;
let showColors = true;
let currentView = 'tree';

// Utility functions
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingAlerts = document.querySelectorAll('.alert-dismissible:not(.persistent)');
    existingAlerts.forEach(alert => alert.remove());

    // Create new notification
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

function showError(message) {
    const treeLoading = document.getElementById('treeLoading');
    if (treeLoading) {
        treeLoading.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="location.reload()">
                    <i class="fas fa-redo me-1"></i> Refresh
                </button>
            </div>
        `;
    }
}

// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tree
    initTree();
    
    // Setup event listeners
    setupEventListeners();
});

async function initTree() {
    try {
        // Show loading
        const treeLoading = document.getElementById('treeLoading');
        const noDataMessage = document.getElementById('noDataMessage');
        
        if (treeLoading) treeLoading.style.display = 'block';
        if (noDataMessage) noDataMessage.classList.add('d-none');
        
        // Load tree data
        const response = await fetch('<?= site_url("accounting/pembukuan/daftar-akun") ?>?ajax=tree');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            treeData = result.data;
            
            // Update statistics
            updateStatistics(treeData);
            
            // Hide loading
            if (treeLoading) treeLoading.style.display = 'none';
            
            // Render tree visualization
            renderTreeVisualization();
            
            // Render list view
            renderTreeList();
            
        } else {
            throw new Error(result.message || 'Failed to load tree data');
        }
        
    } catch (error) {
        console.error('Error loading tree:', error);
        showError('Gagal memuat data struktur: ' + error.message);
    }
}

function setupEventListeners() {
    // Expand/Collapse buttons
    const expandAllBtn = document.getElementById('expandAllBtn');
    const collapseAllBtn = document.getElementById('collapseAllBtn');
    const printTreeBtn = document.getElementById('printTreeBtn');
    const toggleConnections = document.getElementById('toggleConnections');
    const toggleColors = document.getElementById('toggleColors');
    const toggleView = document.getElementById('toggleView');
    const toggleViewList = document.getElementById('toggleViewList');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const searchTree = document.getElementById('searchTree');
    const clearSearch = document.getElementById('clearSearch');
    const showInactive = document.getElementById('showInactive');
    
    if (expandAllBtn) {
        expandAllBtn.addEventListener('click', expandAllNodes);
    }
    
    if (collapseAllBtn) {
        collapseAllBtn.addEventListener('click', collapseAllNodes);
    }
    
    if (printTreeBtn) {
        printTreeBtn.addEventListener('click', printTree);
    }
    
    if (toggleConnections) {
        toggleConnections.addEventListener('click', function() {
            showConnections = !showConnections;
            this.classList.toggle('active', showConnections);
            if (treeInstance) renderTreeVisualization();
        });
    }
    
    if (toggleColors) {
        toggleColors.addEventListener('click', function() {
            showColors = !showColors;
            this.classList.toggle('active', showColors);
            if (treeInstance) renderTreeVisualization();
        });
    }
    
    if (toggleView) {
        toggleView.addEventListener('click', function() {
            switchView('list');
        });
    }
    
    if (toggleViewList) {
        toggleViewList.addEventListener('click', function() {
            switchView('tree');
        });
    }
    
    if (filterType) {
        filterType.addEventListener('change', filterTree);
    }
    
    if (filterStatus) {
        filterStatus.addEventListener('change', filterTree);
    }
    
    if (searchTree) {
        searchTree.addEventListener('input', debounce(searchInTree, 300));
    }
    
    if (clearSearch) {
        clearSearch.addEventListener('click', function() {
            searchTree.value = '';
            searchInTree();
        });
    }
    
    if (showInactive) {
        showInactive.addEventListener('change', filterTree);
    }
    
    // Window resize handler
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (treeInstance && currentView === 'tree') {
                renderTreeVisualization();
            }
        }, 250);
    });
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function updateStatistics(data) {
    let totalAccounts = 0;
    let activeAccounts = 0;
    let headerAccounts = 0;
    let maxDepth = 0;
    
    function traverse(nodes, depth = 1) {
        nodes.forEach(node => {
            totalAccounts++;
            if (node.is_active == 1) activeAccounts++;
            if (node.is_header == 1) headerAccounts++;
            if (depth > maxDepth) maxDepth = depth;
            
            if (node.children && node.children.length > 0) {
                traverse(node.children, depth + 1);
            }
        });
    }
    
    traverse(data);
    
    // Update DOM
    document.getElementById('totalAccounts').textContent = totalAccounts;
    document.getElementById('activeAccounts').textContent = activeAccounts;
    document.getElementById('headerAccounts').textContent = headerAccounts;
    document.getElementById('maxDepth').textContent = maxDepth;
    
    const percentage = totalAccounts > 0 ? Math.round((activeAccounts / totalAccounts) * 100) : 0;
    document.getElementById('activePercentage').textContent = percentage + '%';
}

function renderTreeVisualization() {
    if (!treeData || treeData.length === 0) {
        document.getElementById('noDataMessage').classList.remove('d-none');
        return;
    }
    
    const treeCanvas = document.getElementById('treeCanvas');
    const container = document.getElementById('treeContainer');
    
    // Clear previous visualization
    treeCanvas.innerHTML = '';
    
    // Set dimensions
    const width = container.clientWidth;
    const height = Math.max(600, container.clientHeight);
    const margin = { top: 40, right: 120, bottom: 40, left: 40 };
    
    const svg = d3.select('#treeCanvas')
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .append('g')
        .attr('transform', `translate(${margin.left},${margin.top})`);
    
    // Create tree layout
    const treeLayout = d3.tree()
        .size([height - margin.top - margin.bottom, width - margin.left - margin.right]);
    
    // Build hierarchy
    const root = d3.hierarchy(buildHierarchy(treeData));
    const treeDataHierarchy = treeLayout(root);
    
    // Draw links
    if (showConnections) {
        svg.selectAll('.link')
            .data(treeDataHierarchy.links())
            .enter()
            .append('path')
            .attr('class', 'tree-link')
            .attr('d', d3.linkHorizontal()
                .x(d => d.y)
                .y(d => d.x))
            .attr('stroke', d => {
                if (!showColors) return '#dee2e6';
                return d.target.data.is_active ? '#adb5bd' : '#dc3545';
            })
            .attr('stroke-dasharray', d => d.target.data.is_active ? 'none' : '5,5');
    }
    
    // Draw nodes
    const nodes = svg.selectAll('.node')
        .data(treeDataHierarchy.descendants())
        .enter()
        .append('g')
        .attr('class', 'tree-node')
        .attr('transform', d => `translate(${d.y},${d.x})`)
        .style('cursor', 'pointer')
        .on('click', function(event, d) {
            showNodeDetail(d.data);
        })
        .on('mouseover', function(event, d) {
            d3.select(this).select('circle').attr('stroke-width', 3);
            d3.select(this).select('text').style('font-weight', 'bold');
        })
        .on('mouseout', function(event, d) {
            d3.select(this).select('circle').attr('stroke-width', 2);
            d3.select(this).select('text').style('font-weight', 'normal');
        });
    
    // Draw circles
    nodes.append('circle')
        .attr('r', 10)
        .attr('fill', d => getNodeColor(d.data))
        .attr('stroke', '#fff')
        .attr('stroke-width', 2);
    
    // Add icon/text inside circle
    nodes.append('text')
        .attr('dy', 4)
        .attr('text-anchor', 'middle')
        .attr('fill', 'white')
        .attr('font-size', '10px')
        .attr('font-weight', 'bold')
        .text(d => d.data.is_header ? 'H' : 'D');
    
    // Add labels
    nodes.append('text')
        .attr('dx', d => d.children ? -15 : 15)
        .attr('dy', 4)
        .attr('text-anchor', d => d.children ? 'end' : 'start')
        .attr('class', 'node-label')
        .text(d => `${d.data.kode_akun}`)
        .attr('fill', d => d.data.is_active ? '#333' : '#6c757d')
        .append('title')
        .text(d => `${d.data.kode_akun} - ${d.data.nama_akun}`);
    
    // Add status indicator
    nodes.append('circle')
        .attr('cx', d => d.children ? -25 : 25)
        .attr('cy', -5)
        .attr('r', 4)
        .attr('fill', d => d.data.is_active ? '#28a745' : '#dc3545');
    
    // Store instance
    treeInstance = { svg, treeDataHierarchy };
}

function buildHierarchy(data) {
    const nodeMap = new Map();
    const rootNodes = [];
    
    function createNode(node) {
        return {
            name: node.nama_akun,
            ...node,
            children: []
        };
    }
    
    // First pass: create all nodes
    function processNodes(nodes) {
        nodes.forEach(node => {
            const d3Node = createNode(node);
            nodeMap.set(node.id, d3Node);
            
            if (node.children && node.children.length > 0) {
                const childNodes = processNodes(node.children);
                d3Node.children = childNodes;
            }
            
            if (!node.parent_id) {
                rootNodes.push(d3Node);
            }
        });
        
        return nodes.map(node => nodeMap.get(node.id));
    }
    
    processNodes(data);
    
    // Second pass: build hierarchy
    function buildTree(nodes) {
        const tree = [];
        
        nodes.forEach(node => {
            const d3Node = nodeMap.get(node.id);
            if (node.parent_id && nodeMap.has(node.parent_id)) {
                const parent = nodeMap.get(node.parent_id);
                parent.children.push(d3Node);
            } else {
                tree.push(d3Node);
            }
        });
        
        return tree;
    }
    
    const tree = buildTree(data);
    return { name: "COA", children: tree.length > 0 ? tree : rootNodes };
}

function getNodeColor(node) {
    if (!showColors) {
        return node.is_active ? '#6c757d' : '#adb5bd';
    }
    
    if (!node.is_active) return '#dc3545';
    
    const colors = {
        'Aset': '#4e73df',
        'Kewajiban': '#f6c23e',
        'Ekuitas': '#1cc88a',
        'Pendapatan': '#36b9cc',
        'Beban': '#e74a3b'
    };
    
    return colors[node.tipe_akun] || '#6c757d';
}

function renderTreeList() {
    const tbody = document.getElementById('treeListBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (!treeData || treeData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-tree fa-2x mb-3"></i>
                        <p>Belum ada data struktur Chart of Accounts</p>
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Akun Pertama
                        </a>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    function renderNodes(nodes, level = 0) {
        let html = '';
        
        nodes.forEach(node => {
            const indentClass = `tree-list-indent-${Math.min(level, 5)}`;
            const typeColor = {
                'Aset': 'primary',
                'Kewajiban': 'warning',
                'Ekuitas': 'success',
                'Pendapatan': 'info',
                'Beban': 'danger'
            }[node.tipe_akun] || 'secondary';
            
            html += `
                <tr data-id="${node.id}" data-level="${level}" class="${indentClass}">
                    <td>
                        <span class="${indentClass.replace('tree-list-indent', 'ms')}"></span>
                        <strong>${node.kode_akun}</strong>
                    </td>
                    <td>
                        ${node.is_header == 1 ? 
                            `<i class="fas fa-folder text-primary me-1"></i><strong>${node.nama_akun}</strong>` : 
                            `<i class="fas fa-file text-muted me-1"></i>${node.nama_akun}`
                        }
                    </td>
                    <td><span class="badge bg-${typeColor}">${node.tipe_akun}</span></td>
                    <td><span class="badge bg-secondary">${node.level}</span></td>
                    <td>
                        ${node.is_active == 1 ? 
                            '<span class="badge bg-success">Aktif</span>' : 
                            '<span class="badge bg-danger">Nonaktif</span>'
                        }
                    </td>
                    <td>
                        ${node.is_header == 1 ? 
                            '<span class="badge bg-info">Header</span>' : 
                            '<span class="badge bg-warning">Detail</span>'
                        }
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/') ?>${node.id}" class="btn btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= site_url('accounting/pembukuan/daftar-akun/edit/') ?>${node.id}" class="btn btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
            
            if (node.children && node.children.length > 0) {
                html += renderNodes(node.children, level + 1);
            }
        });
        
        return html;
    }
    
    tbody.innerHTML = renderNodes(treeData);
    
    // Add click event to rows
    tbody.querySelectorAll('tr[data-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) return;
            
            const accountId = parseInt(this.getAttribute('data-id'));
            const account = findAccountById(treeData, accountId);
            if (account) showNodeDetail(account);
        });
    });
}

function findAccountById(data, id) {
    for (const node of data) {
        if (node.id === id) return node;
        if (node.children && node.children.length > 0) {
            const found = findAccountById(node.children, id);
            if (found) return found;
        }
    }
    return null;
}

function expandAllNodes() {
    const hiddenRows = document.querySelectorAll('#treeListBody tr.d-none');
    hiddenRows.forEach(row => row.classList.remove('d-none'));
    showNotification('Semua node diperlihatkan', 'success');
}

function collapseAllNodes() {
    const childRows = document.querySelectorAll('#treeListBody tr[data-level="1"]');
    childRows.forEach(row => {
        let nextRow = row.nextElementSibling;
        while (nextRow && parseInt(nextRow.getAttribute('data-level')) > 1) {
            nextRow.classList.add('d-none');
            nextRow = nextRow.nextElementSibling;
        }
    });
    showNotification('Semua child node disembunyikan', 'info');
}

function printTree() {
    const printWindow = window.open('', '_blank');
    const companyName = 'PT. Cipta Duta Wacana';
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struktur Chart of Accounts - ${new Date().toLocaleDateString('id-ID')}</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    margin: 20px; 
                    color: #333;
                    font-size: 14px;
                }
                @media print {
                    body { margin: 0; padding: 10px; }
                    .no-print { display: none !important; }
                    @page { margin: 1cm; }
                }
                h1 { 
                    color: #2c3e50; 
                    border-bottom: 2px solid #3498db; 
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                    font-size: 24px;
                }
                .header { 
                    background: #f8f9fa; 
                    padding: 20px; 
                    border-radius: 5px; 
                    margin-bottom: 30px;
                    border-left: 4px solid #3498db;
                }
                .tree-node { 
                    margin-left: 20px; 
                    margin-bottom: 5px; 
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    line-height: 1.5;
                }
                .tree-node:last-child { border-bottom: none; }
                .header-node { 
                    font-weight: bold; 
                    color: #2980b9; 
                    font-size: 1.1em;
                }
                .detail-node { color: #555; }
                .inactive { 
                    color: #e74c3c; 
                    opacity: 0.7;
                    text-decoration: line-through;
                }
                .legend { 
                    background: #f8f9fa; 
                    padding: 15px; 
                    border-radius: 5px; 
                    margin: 20px 0; 
                    border: 1px solid #dee2e6;
                    font-size: 12px;
                }
                .badge {
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-size: 10px;
                    margin-right: 5px;
                    display: inline-block;
                }
                .badge-primary { background: #4e73df; color: white; }
                .badge-success { background: #1cc88a; color: white; }
                .badge-danger { background: #e74a3b; color: white; }
                .badge-warning { background: #f6c23e; color: #333; }
                .badge-info { background: #36b9cc; color: white; }
                .badge-secondary { background: #6c757d; color: white; }
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #dee2e6;
                    color: #6c757d;
                    font-size: 11px;
                }
                .stats {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    flex-wrap: wrap;
                }
                .stat-item {
                    flex: 1;
                    min-width: 150px;
                    margin: 5px;
                    padding: 10px;
                    background: #f8f9fa;
                    border-radius: 5px;
                    text-align: center;
                }
                .stat-value {
                    font-size: 24px;
                    font-weight: bold;
                    color: #2c3e50;
                }
                .stat-label {
                    font-size: 12px;
                    color: #6c757d;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>STRUKTUR CHART OF ACCOUNTS</h1>
                <p><strong>Perusahaan:</strong> ${companyName}</p>
                <p><strong>Tanggal Cetak:</strong> ${new Date().toLocaleString('id-ID')}</p>
                <p><strong>Dicetak oleh:</strong> ${document.querySelector('.user-name')?.textContent || 'System'}</p>
            </div>
            
            <div class="stats" id="printStats">
                <!-- Stats will be populated by JavaScript -->
            </div>
            
            <div class="legend">
                <strong>LEGENDA:</strong><br>
                <span class="badge badge-primary">Aset</span>
                <span class="badge badge-warning">Kewajiban</span>
                <span class="badge badge-success">Ekuitas</span>
                <span class="badge badge-info">Pendapatan</span>
                <span class="badge badge-danger">Beban</span>
                <span style="color:#2980b9; margin-left: 10px;"><i class="fas fa-folder"></i> Header</span>
                <span style="color:#555; margin-left: 10px;"><i class="fas fa-file"></i> Detail</span>
                <span style="color:#e74c3c; margin-left: 10px;"><i class="fas fa-times"></i> Nonaktif</span>
            </div>
            
            <div id="treeContent"></div>
            
            <div class="footer">
                <p>Dokumen ini dicetak dari sistem CDW Intranet - Modul Accounting</p>
                <p>Halaman 1 dari 1</p>
            </div>
            
            <div class="no-print" style="margin-top: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-print"></i> Cetak Dokumen
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                    Tutup Jendela
                </button>
            </div>
            
            <script>
                function generateTreePrint(tree, level = 0) {
                    let html = '';
                    tree.forEach(node => {
                        const indent = '&nbsp;'.repeat(level * 4);
                        const typeClass = {
                            'Aset': 'badge-primary',
                            'Kewajiban': 'badge-warning',
                            'Ekuitas': 'badge-success',
                            'Pendapatan': 'badge-info',
                            'Beban': 'badge-danger'
                        }[node.tipe_akun] || 'badge-secondary';
                        
                        const nodeType = node.is_header ? 'H' : 'D';
                        const nodeClass = node.is_header ? 'header-node' : 'detail-node';
                        const statusClass = !node.is_active ? 'inactive' : '';
                        
                        html += \`<div class="tree-node \${nodeClass} \${statusClass}">
                            \${indent}<strong>\${node.kode_akun}</strong> - \${node.nama_akun}
                            <span class="badge \${typeClass}">\${node.tipe_akun}</span>
                            <span class="badge badge-\${node.is_header ? 'info' : 'warning'}">\${nodeType}</span>
                            \${!node.is_active ? '<span class="badge badge-danger">Nonaktif</span>' : ''}
                        </div>\`;
                        
                        if (node.children && node.children.length > 0) {
                            html += generateTreePrint(node.children, level + 1);
                        }
                    });
                    return html;
                }
                
                // Populate stats
                document.getElementById('printStats').innerHTML = \`
                    <div class="stat-item">
                        <div class="stat-value">\${document.getElementById('totalAccounts').textContent}</div>
                        <div class="stat-label">Total Akun</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">\${document.getElementById('activeAccounts').textContent}</div>
                        <div class="stat-label">Akun Aktif</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">\${document.getElementById('headerAccounts').textContent}</div>
                        <div class="stat-label">Header Akun</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">\${document.getElementById('maxDepth').textContent}</div>
                        <div class="stat-label">Kedalaman Maks</div>
                    </div>
                \`;
                
                // Generate tree content
                const treeData = \${JSON.stringify(treeData || [])};
                document.getElementById('treeContent').innerHTML = generateTreePrint(treeData);
            <\/script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Auto print after load
    setTimeout(() => {
        printWindow.print();
    }, 1000);
}

function filterTree() {
    const type = document.getElementById('filterType').value;
    const status = document.getElementById('filterStatus').value;
    const showAll = document.getElementById('showInactive').checked;
    
    const rows = document.querySelectorAll('#treeListBody tr[data-id]');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let showRow = true;
        
        // Filter by type
        if (type) {
            const typeBadge = row.querySelector('.badge');
            if (typeBadge && !typeBadge.textContent.includes(type)) {
                showRow = false;
            }
        }
        
        // Filter by status
        if (status) {
            const statusBadge = row.querySelector('.badge.bg-success, .badge.bg-danger');
            if (status === 'active' && (!statusBadge || !statusBadge.textContent.includes('Aktif'))) {
                showRow = false;
            }
            if (status === 'inactive' && (!statusBadge || !statusBadge.textContent.includes('Nonaktif'))) {
                showRow = false;
            }
        }
        
        // Show/hide inactive
        const isInactive = row.querySelector('.badge.bg-danger');
        if (!showAll && isInactive) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    showNotification(`Menampilkan ${visibleCount} dari ${rows.length} akun`, 'info');
}

function searchInTree() {
    const query = document.getElementById('searchTree').value.toLowerCase().trim();
    
    if (!query) {
        // Reset all rows
        const rows = document.querySelectorAll('#treeListBody tr[data-id]');
        rows.forEach(row => {
            row.style.display = '';
            row.classList.remove('search-highlight');
            row.style.backgroundColor = '';
        });
        
        // Reset tree visualization
        if (treeInstance) {
            renderTreeVisualization();
        }
        
        return;
    }
    
    // Search in list view
    const rows = document.querySelectorAll('#treeListBody tr[data-id]');
    let matchCount = 0;
    
    rows.forEach(row => {
        const kode = row.cells[0]?.textContent?.toLowerCase() || '';
        const nama = row.cells[1]?.textContent?.toLowerCase() || '';
        
        if (kode.includes(query) || nama.includes(query)) {
            row.style.display = '';
            row.classList.add('search-highlight');
            row.style.backgroundColor = '#fff3cd';
            matchCount++;
        } else {
            row.style.display = 'none';
            row.classList.remove('search-highlight');
            row.style.backgroundColor = '';
        }
    });
    
    // Highlight in tree visualization
    if (treeInstance) {
        treeInstance.svg.selectAll('.tree-node')
            .style('opacity', function(d) {
                const kode = d.data.kode_akun?.toLowerCase() || '';
                const nama = d.data.nama_akun?.toLowerCase() || '';
                return (kode.includes(query) || nama.includes(query)) ? 1 : 0.3;
            });
    }
    
    if (matchCount > 0) {
        showNotification(`Ditemukan ${matchCount} akun yang sesuai`, 'success');
    } else {
        showNotification('Tidak ditemukan akun yang sesuai', 'warning');
    }
}

function showNodeDetail(nodeData) {
    const modal = new bootstrap.Modal(document.getElementById('nodeDetailModal'));
    const modalTitle = document.getElementById('nodeDetailModalLabel');
    const modalContent = document.getElementById('nodeDetailContent');
    const editLink = document.getElementById('nodeEditLink');
    const detailLink = document.getElementById('nodeDetailLink');
    
    // Set modal title
    modalTitle.textContent = `${nodeData.kode_akun} - ${nodeData.nama_akun}`;
    
    // Determine colors
    const typeColor = {
        'Aset': 'primary',
        'Kewajiban': 'warning',
        'Ekuitas': 'success',
        'Pendapatan': 'info',
        'Beban': 'danger'
    }[nodeData.tipe_akun] || 'secondary';
    
    // Create content
    modalContent.innerHTML = `
        <div class="modal-node-detail">
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="card-title border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Informasi Akun
                            </h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%"><i class="fas fa-hashtag me-2"></i>Kode</th>
                                    <td><strong class="text-primary">${nodeData.kode_akun}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-font me-2"></i>Nama</th>
                                    <td>${nodeData.nama_akun}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-tag me-2"></i>Tipe</th>
                                    <td><span class="badge bg-${typeColor}">${nodeData.tipe_akun}</span></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-balance-scale me-2"></i>Saldo Normal</th>
                                    <td>
                                        <span class="badge bg-${nodeData.saldo_normal === 'Debit' ? 'success' : 'warning'}">
                                            ${nodeData.saldo_normal}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="card-title border-bottom pb-2 mb-3">
                                <i class="fas fa-cogs me-2"></i>Properti
                            </h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%"><i class="fas fa-layer-group me-2"></i>Level</th>
                                    <td><span class="badge bg-secondary">${nodeData.level}</span></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-folder me-2"></i>Jenis</th>
                                    <td>
                                        ${nodeData.is_header ? 
                                            '<span class="badge bg-info"><i class="fas fa-folder me-1"></i>Header Akun</span>' : 
                                            '<span class="badge bg-warning"><i class="fas fa-file me-1"></i>Detail Akun</span>'
                                        }
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-power-off me-2"></i>Status</th>
                                    <td>
                                        ${nodeData.is_active ? 
                                            '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>' : 
                                            '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Nonaktif</span>'
                                        }
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-filter me-2"></i>Kategori</th>
                                    <td>${nodeData.kategori || '<span class="text-muted">-</span>'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            ${nodeData.deskripsi ? `
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title border-bottom pb-2 mb-3">
                                <i class="fas fa-align-left me-2"></i>Deskripsi
                            </h6>
                            <div class="p-3 bg-light rounded">
                                ${nodeData.deskripsi}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            ${nodeData.parent_id ? `
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-level-up-alt me-2"></i>
                        <strong>Struktur:</strong> Akun ini merupakan bagian dari struktur hierarki.
                    </div>
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    // Set links
    editLink.href = `<?= site_url('accounting/pembukuan/daftar-akun/edit/') ?>${nodeData.id}`;
    detailLink.href = `<?= site_url('accounting/pembukuan/daftar-akun/detail/') ?>${nodeData.id}`;
    
    modal.show();
}

function switchView(view) {
    currentView = view;
    
    const treeContainer = document.getElementById('treeContainer').parentElement.parentElement;
    const listViewContainer = document.getElementById('listViewContainer');
    const toggleView = document.getElementById('toggleView');
    const toggleViewList = document.getElementById('toggleViewList');
    
    if (view === 'list') {
        treeContainer.classList.add('d-none');
        listViewContainer.classList.remove('d-none');
        
        if (toggleView) {
            toggleView.innerHTML = '<i class="fas fa-exchange-alt me-1"></i> Ganti ke Visual';
            toggleView.setAttribute('data-view', 'list');
        }
    } else {
        treeContainer.classList.remove('d-none');
        listViewContainer.classList.add('d-none');
        
        if (toggleViewList) {
            toggleViewList.innerHTML = '<i class="fas fa-exchange-alt me-1"></i> Ganti ke List';
            toggleViewList.setAttribute('data-view', 'tree');
        }
    }
    
    showNotification(`Berhasil beralih ke tampilan ${view === 'tree' ? 'Visual' : 'List'}`, 'success');
}
</script>

<?= $this->include('accounting/templates/footer') ?>