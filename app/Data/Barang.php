<?php

class Barang extends Controller
{
    function stok_data($kode, $id_toko)
    {
        $cols = "id_barang, CONCAT('U',sn,sds) as unic, sn, sds, 
            (SUM(CASE WHEN id_target = '" . $id_toko . "' AND stat = 1 THEN qty ELSE 0 END) - 
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat <> 2 THEN qty ELSE 0 END)) as qty,
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2 THEN qty ELSE 0 END) as cart";

        $where = "id_barang = '" . $kode . "' AND 
            ((id_target = '" . $id_toko . "' AND stat = 1) OR 
            (id_sumber = '" . $id_toko . "' AND stat <> 2) OR 
            (id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2)) 
            GROUP BY sn, sds";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, "unic");

        $cart_list_cache = null;

        foreach ($masuk as $key => $ms) {
            if ($masuk[$key]['cart'] > 0) {
                if ($cart_list_cache === null) {
                    $cols_cart = "id_barang, user_id, CONCAT('U',sn,sds,user_id) as unic, sn, sds, sum(qty) as qty";
                    $where_cart_list = "id_barang = '" . $kode . "' AND id_sumber = '" . $id_toko . "' AND stat = 0 GROUP BY sn, sds, user_id";
                    $cart_list_cache = $this->db(0)->get_cols_where('master_mutasi', $cols_cart, $where_cart_list);
                }
                $masuk[$key]['cart_list'] = $cart_list_cache;
            } else {
                unset($masuk[$key]['cart']);
            }
        }

        return $masuk;
    }

    function stok_data_all($kode, $id_toko)
    {
        $cols = "id_barang, 
            (SUM(CASE WHEN id_target = '" . $id_toko . "' AND stat = 1 THEN qty ELSE 0 END) - 
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat <> 2 THEN qty ELSE 0 END)) as qty,
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2 THEN qty ELSE 0 END) as cart";

        $where = "id_barang = '" . $kode . "' AND 
            ((id_target = '" . $id_toko . "' AND stat = 1) OR 
            (id_sumber = '" . $id_toko . "' AND stat <> 2) OR 
            (id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2))
            GROUP BY id_barang";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, "id_barang");

        foreach ($masuk as $key => $ms) {
            if ($masuk[$key]['cart'] == 0) {
                unset($masuk[$key]['cart']);
            }
        }

        return $masuk;
    }

    function stok_data_list($id_toko)
    {
        $cols = "id_barang, CONCAT(id_barang,'#',sn,sds) as unic, sn, sds, 
            (SUM(CASE WHEN id_target = '" . $id_toko . "' AND stat = 1 THEN qty ELSE 0 END) - 
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat <> 2 THEN qty ELSE 0 END)) as qty,
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2 THEN qty ELSE 0 END) as cart";

        $where = "((id_target = '" . $id_toko . "' AND stat = 1) OR 
                  (id_sumber = '" . $id_toko . "' AND stat <> 2) OR 
                  (id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2))
                  GROUP BY id_barang, sn, sds";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, "unic");

        foreach ($masuk as $key => $ms) {
            if ($masuk[$key]['cart'] == 0) {
                unset($masuk[$key]['cart']);
            }
        }

        return $masuk;
    }

    function stok_data_list_all($id_toko)
    {
        $cols = "id_barang, 
            (SUM(CASE WHEN id_target = '" . $id_toko . "' AND stat = 1 THEN qty ELSE 0 END) - 
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat <> 2 THEN qty ELSE 0 END)) as qty,
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2 THEN qty ELSE 0 END) as cart";

        $where = "((id_target = '" . $id_toko . "' AND stat = 1) OR 
                  (id_sumber = '" . $id_toko . "' AND stat <> 2) OR 
                  (id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2))
                  GROUP BY id_barang";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, "id_barang");

        foreach ($masuk as $key => $ms) {
            if ($masuk[$key]['cart'] == 0) {
                unset($masuk[$key]['cart']);
            }
        }

        return $masuk;
    }

    function stok_data_web($id_toko = 1)
    {
        $cols = "id_barang as id, 
            (SUM(CASE WHEN id_target = '" . $id_toko . "' AND stat = 1 THEN qty ELSE 0 END) - 
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat <> 2 THEN qty ELSE 0 END)) as stock";

        $where = "((id_target = '" . $id_toko . "' AND stat = 1) OR 
                  (id_sumber = '" . $id_toko . "' AND stat <> 2))
                  GROUP BY id_barang";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, "id");

        return $masuk;
    }

    function stok_data_list_sds($id_toko)
    {
        $cols = "id_barang, CONCAT(id_barang,'#',sds) as unic, sds, 
            (SUM(CASE WHEN id_target = '" . $id_toko . "' AND stat = 1 THEN qty ELSE 0 END) - 
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat <> 2 THEN qty ELSE 0 END)) as qty,
            SUM(CASE WHEN id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2 THEN qty ELSE 0 END) as cart";

        $where = "((id_target = '" . $id_toko . "' AND stat = 1) OR 
                  (id_sumber = '" . $id_toko . "' AND stat <> 2) OR 
                  (id_sumber = '" . $id_toko . "' AND stat = 0 AND jenis = 2))
                  GROUP BY id_barang, sds";

        $masuk = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, "unic");

        foreach ($masuk as $key => $ms) {
            if ($masuk[$key]['cart'] == 0) {
                unset($masuk[$key]['cart']);
            }
        }

        return $masuk;
    }

    function sisa_stok($kode, $id_toko, $sn, $sds)
    {
        $stok = $this->stok_data($kode, $id_toko);
        $unic = "U" . $sn . $sds;
        if (isset($stok[$unic])) {
            return $stok[$unic]['qty'];
        } else {
            return 0;
        }
    }
}
