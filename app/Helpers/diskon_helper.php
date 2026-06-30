<?php

/**
 * Hitung diskon berdasarkan total pembelian
 */
function hitungDiskon($totalHarga): array
{
    if ($totalHarga >= 50000000) {
        $persen = 15;
    } elseif ($totalHarga >= 30000000) {
        $persen = 10;
    } elseif ($totalHarga >= 10000000) {
        $persen = 5;
    } else {
        $persen = 0;
    }

    $nilai = ($persen / 100) * $totalHarga;

    return [
        'persen' => $persen,
        'nilai'  => $nilai,
    ];
}