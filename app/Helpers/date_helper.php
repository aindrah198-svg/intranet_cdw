<?php
if (!function_exists('formatHariCuti')) {
    function formatHariCuti($days)
    {
        if ($days == 1) {
            return '1 hari';
        }
        return $days . ' hari';
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status)
    {
        $badges = [
            'Menunggu' => 'warning',
            'Disetujui HRD' => 'success',
            'Disetujui Atasan' => 'success',
            'Ditolak' => 'danger',
            'Dibatalkan' => 'secondary',
            'Draft' => 'info'
        ];
        
        $badgeClass = $badges[$status] ?? 'secondary';
        return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
    }
}