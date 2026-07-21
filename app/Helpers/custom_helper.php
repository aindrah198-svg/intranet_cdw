<?php
if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status)
    {
        $classes = [
            'Draft' => 'secondary',
            'Menunggu' => 'warning',
            'Disetujui HRD' => 'success',
            'Disetujui Atasan' => 'success',
            'Ditolak' => 'danger',
            'Dibatalkan' => 'secondary'
        ];
        return $classes[$status] ?? 'secondary';
    }
}

if (!function_exists('formatDateIndo')) {
    function formatDateIndo($date, $includeTime = false)
    {
        if (!$date) return '-';
        
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $dateTime = new DateTime($date);
        $day = $dateTime->format('d');
        $month = $months[(int)$dateTime->format('m') - 1];
        $year = $dateTime->format('Y');
        
        if ($includeTime) {
            $time = $dateTime->format('H:i');
            return "$day $month $year $time";
        }
        
        return "$day $month $year";
    }
}