<?php

class Saldo extends Controller
{
    public function __construct()
    {
        $this->session_cek();
        $this->data_order();
    }

    function deposit($id_pelanggan)
    {
        $topup = $this->db(0)->sum_col_where("kas", "jumlah", "jenis_transaksi = 2 AND jenis_mutasi = 1 AND id_client = " . $id_pelanggan . " AND status_mutasi = 1");
        $pakai = $this->db(0)->sum_col_where("kas", "jumlah", "jenis_transaksi = 1 AND metode_mutasi = 4 AND id_client = " . $id_pelanggan . " AND status_mutasi <> 2");
        return $topup - $pakai;
    }

    function list_saldo()
    {
        $cols = "id_client, SUM(jumlah) as jumlah";
        $topup = $this->db(0)->get_cols_where("kas", $cols, "jenis_transaksi = 2 AND jenis_mutasi = 1 AND status_mutasi = 1 AND id_toko = " . $this->userData['id_toko'], 1, "id_client");
        $pakai = $this->db(0)->get_cols_where("kas", $cols, "jenis_transaksi = 1 AND metode_mutasi = 4 AND status_mutasi <> 2 AND id_toko = " . $this->userData['id_toko'], 1, "id_client");

        $return = [];
        foreach ($topup as $key => $t) {
            if (isset($pakai[$key]['jumlah'])) {
                $return[$key] = $topup[$key]['jumlah'] - $pakai[$key]['jumlah'];
            } else {
                $return[$key] = $topup[$key]['jumlah'];
            }
        }

        return $return;
    }
}
