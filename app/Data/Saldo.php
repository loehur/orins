<?php

class Saldo extends Controller
{
    function deposit($id_pelanggan)
    {
        $topup = $this->db(0)->sum_col_where("kas", "jumlah", "jenis_transaksi = 2 AND jenis_mutasi = 1 AND id_client = " . $id_pelanggan . " AND status_mutasi = 1");
        $pakai = $this->db(0)->sum_col_where("kas", "jumlah", "jenis_transaksi = 1 AND metode_mutasi = 4 AND id_client = " . $id_pelanggan . " AND status_mutasi <> 2");
        return $topup - $pakai;
    }
}
