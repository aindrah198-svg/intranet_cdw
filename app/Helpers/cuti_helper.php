<?php
// File: app/Helpers/cuti_helper.php

if (!function_exists('getStatusBadgeClass')) {
    /**
     * Get Bootstrap badge class based on status
     * 
     * @param string $status
     * @return string
     */
    function getStatusBadgeClass($status)
    {
        switch ($status) {
            case 'Disetujui HRD':
            case 'Disetujui Atasan':
                return 'success';
            
            case 'Menunggu':
                return 'warning';
            
            case 'Ditolak':
                return 'danger';
            
            case 'Dibatalkan':
                return 'secondary';
            
            case 'Draft':
                return 'secondary';
            
            default:
                return 'info';
        }
    }
}

if (!function_exists('formatTanggalIndonesia')) {
    /**
     * Format tanggal ke format Indonesia
     * 
     * @param string $tanggal
     * @param bool $withDay
     * @return string
     */
    function formatTanggalIndonesia($tanggal, $withDay = true)
    {
        if (empty($tanggal)) {
            return '-';
        }
        
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        $timestamp = strtotime($tanggal);
        $dayName = $hari[date('l', $timestamp)];
        $day = date('d', $timestamp);
        $month = $bulan[(int)date('m', $timestamp)];
        $year = date('Y', $timestamp);
        
        if ($withDay) {
            return "$dayName, $day $month $year";
        } else {
            return "$day $month $year";
        }
    }
}

if (!function_exists('hitungHariKerja')) {
    /**
     * Hitung jumlah hari kerja (Senin-Jumat) antara dua tanggal
     * 
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    function hitungHariKerja($startDate, $endDate)
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1;
        
        $workDays = 0;
        for ($i = 0; $i < $days; $i++) {
            $currentDate = clone $start;
            $currentDate->add(new DateInterval("P{$i}D"));
            $dayOfWeek = $currentDate->format('N');
            
            // Monday (1) to Friday (5)
            if ($dayOfWeek < 6) {
                $workDays++;
            }
        }
        
        return $workDays;
    }
}

if (!function_exists('getJenisCutiColor')) {
    /**
     * Get color for leave type
     * 
     * @param string $jenisCuti
     * @return string
     */
    function getJenisCutiColor($jenisCuti)
    {
        switch ($jenisCuti) {
            case 'Tahunan':
                return '#3498db';
            case 'Hamil':
                return '#e74c3c';
            case 'Sakit':
                return '#f39c12';
            case 'Khusus':
                return '#9b59b6';
            case 'Lainnya':
                return '#95a5a6';
            default:
                return '#2ecc71';
        }
    }
}