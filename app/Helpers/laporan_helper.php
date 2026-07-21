laporan_helper.php<?php
// app/Helpers/laporan_helper.php

use CodeIgniter\HTTP\URI;

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('getLabaRugiBadge')) {
    function getLabaRugiBadge($amount) {
        if ($amount > 0) {
            return '<span class="badge bg-success">Laba</span>';
        } elseif ($amount < 0) {
            return '<span class="badge bg-danger">Rugi</span>';
        } else {
            return '<span class="badge bg-secondary">Break Even</span>';
        }
    }
}

if (!function_exists('formatSaldoByNormal')) {
    function formatSaldoByNormal($amount, $saldoNormal) {
        $formatted = formatCurrency(abs($amount));
        if (($saldoNormal == 'Debit' && $amount < 0) || ($saldoNormal == 'Kredit' && $amount < 0)) {
            return '(' . $formatted . ')';
        }
        return $formatted;
    }
}

if (!function_exists('getSaldoColor')) {
    function getSaldoColor($amount) {
        if ($amount > 0) {
            return 'text-success';
        } elseif ($amount < 0) {
            return 'text-danger';
        } else {
            return 'text-muted';
        }
    }
}