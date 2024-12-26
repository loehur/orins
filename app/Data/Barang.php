<?php

class Barang extends Controller
{
    function stok_data($kode, $id_toko)
    {
        $cols = "id_barang, CONCAT('U',sn,sds) as unic, sn, sds, sum(qty) as qty";
        $where_masuk = "id_barang = '" . $kode . "' AND id_target = '" . $id_toko . "' AND stat = 1 GROUP BY sn, sds";
        $where_keluar = "id_barang = '" . $kode . "' AND id_sumber = '" . $id_toko . "' AND stat <> 2 GROUP BY sn, sds";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_masuk, 1, "unic");
        $keluar = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_keluar, 1, "unic");

        foreach ($masuk as $key => $ms) {
            if (isset($keluar[$key])) {
                $masuk[$key]['qty'] -= $keluar[$key]['qty'];
            }
        }

        return $masuk;
    }

    function stok_data_all($kode, $id_toko)
    {
        $cols = "id_barang, sum(qty) as qty";
        $where_masuk = "id_barang = '" . $kode . "' AND id_target = '" . $id_toko . "' AND stat = 1 GROUP BY id_barang";
        $where_keluar = "id_barang = '" . $kode . "' AND id_sumber = '" . $id_toko . "' AND stat <> 2 GROUP BY id_barang";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_masuk, 1, "id_barang");
        $keluar = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_keluar, 1, "id_barang");

        foreach ($masuk as $key => $ms) {
            if (isset($keluar[$key])) {
                $masuk[$key]['qty'] -= $keluar[$key]['qty'];
            }
        }

        return $masuk;
    }

    function stok_data_list($id_toko)
    {
        $cols = "id_barang, CONCAT(id_barang,'#',sn,sds) as unic, sn, sds, sum(qty) as qty";
        $where_masuk = "id_target = '" . $id_toko . "' AND stat = 1 GROUP BY id_barang, sn, sds";
        $where_keluar = "id_sumber = '" . $id_toko . "' AND stat <> 2 GROUP BY id_barang, sn, sds";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_masuk, 1, "unic");
        $keluar = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_keluar, 1, "unic");

        foreach ($masuk as $key => $ms) {
            if (isset($keluar[$key])) {
                $masuk[$key]['qty'] -= $keluar[$key]['qty'];
            }
        }

        return $masuk;
    }

    function stok_data_list_all($id_toko)
    {
        $cols = "id_barang, sum(qty) as qty";
        $where_masuk = "id_target = '" . $id_toko . "' AND stat = 1 GROUP BY id_barang";
        $where_keluar = "id_sumber = '" . $id_toko . "' AND stat <> 2 GROUP BY id_barang";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_masuk, 1, "id_barang");
        $keluar = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_keluar, 1, "id_barang");

        foreach ($masuk as $key => $ms) {
            if (isset($keluar[$key])) {
                $masuk[$key]['qty'] -= $keluar[$key]['qty'];
            }
        }

        return $masuk;
    }

    function cek($kode, $id_toko, $sn, $sds, $qty)
    {
        $stok = $this->stok_data($kode, $id_toko);
        $unic = "U" . $sn . $sds;
        if (isset($stok[$unic])) {
            if ($stok[$unic]['qty'] < $qty) {
                return $stok;
            }
        } else {
            return $stok;
        }

        return 1;
    }


    function stok_data_proses($kode, $id_toko)
    {
        $cols = "id_barang, CONCAT('U',sn,sds) as unic, sn, sds, sum(qty) as qty";
        $where_masuk = "id_barang = '" . $kode . "' AND id_target = '" . $id_toko . "' AND stat = 1 GROUP BY sn, sds";
        $where_keluar = "id_barang = '" . $kode . "' AND id_sumber = '" . $id_toko . "' AND stat = 1 GROUP BY sn, sds";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_masuk, 1, "unic");
        $keluar = $this->db(0)->get_cols_where('master_mutasi', $cols, $where_keluar, 1, "unic");

        foreach ($masuk as $key => $ms) {
            if (isset($keluar[$key])) {
                $masuk[$key]['qty'] -= $keluar[$key]['qty'];
            }
        }

        return $masuk;
    }

    function cek_proses($kode, $id_toko, $sn, $sds, $qty)
    {
        $stok = $this->stok_data_proses($kode, $id_toko);
        $unic = "U" . $sn . $sds;
        if (isset($stok[$unic])) {
            if ($stok[$unic]['qty'] < $qty) {
                return 0;
            }
        } else {
            return 0;
        }

        return 1;
    }
}
