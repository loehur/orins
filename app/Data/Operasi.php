<?php

class Operasi extends Controller
{
    public function __construct()
    {
        $this->session_cek();
        $this->data_order();
    }

    function ambil($id, $id_karyawan, $id_driver = 0)
    {
        $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);
        if ($id_driver <> 0) {
            $update = $this->db(0)->update("karyawan", "freq_driver = freq_driver+1", "id_karyawan = " . $id_driver);
        }
        $dateNow = date("Y-m-d H:i:s");

        $set = "id_ambil = " . $id_karyawan . ", tgl_ambil = '" . $dateNow . "'";
        $cek_toko = $this->db(0)->get_where_row('order_data', "id_order_data = '" . $id . "'");

        if ($cek_toko['id_toko'] == $this->userData['id_toko']) {
            $where = "id_order_data = " . $id . " AND id_ambil = 0";
            $set = "id_ambil = " . $id_karyawan . ", id_ambil_driver = " . $id_driver . ", tgl_ambil = '" . $dateNow . "'";
        } else {
            $where = "id_order_data = " . $id . " AND id_ambil_aff = 0 AND id_afiliasi = " . $this->userData['id_toko'];
            $set = "id_ambil_aff = " . $id_karyawan . ", id_ambil_driver = " . $id_driver . ", tgl_ambil_aff = '" . $dateNow . "'";
        }

        if ($cek_toko['stok'] == 1) {
            $up_stok = $this->terima_stok_satuan($id, $cek_toko['ref']);
            if ($up_stok['errno'] <> 0) {
                return $up_stok;
            }
        }

        $update = $this->db(0)->update("order_data", $set, $where);
        return $update;
    }

    function ambil_semua($ref, $id_karyawan, $id_driver = 0, $id_toko = 0, $mode = 0)
    {
        $dateNow = date("Y-m-d H:i:s");
        if ($id_driver <> 0) {
            $update = $this->db(0)->update("karyawan", "freq_driver = freq_driver+1", "id_karyawan = " . $id_driver);
        }

        if ($mode == 0) {
            if ($id_toko == 0) {
                $id_toko = $this->userData['id_toko'];
            }

            $update = $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);
            $cek_toko_asal = $this->db(0)->get_where('order_data', "ref = '" . $ref . "' AND (id_toko = " . $id_toko . " OR id_afiliasi = " . $id_toko . ")", 'id_toko');
            if (count($cek_toko_asal) > 0) {
                if (isset($cek_toko_asal[$id_toko])) {
                    $where = "ref = '" . $ref . "' AND id_ambil = 0";
                    $set = "id_ambil = " . $id_karyawan . ", id_ambil_driver = " . $id_driver . ", tgl_ambil = '" . $dateNow . "'";
                    $update = $this->db(0)->update("order_data", $set, $where);
                    if ($update['errno'] == 0) {
                        $up_stok = $this->terima_stok_semua($ref);
                        if ($up_stok['errno'] <> 0) {
                            return $up_stok;
                        }

                        $cek = $this->db(0)->get_where_row('ref', "ref = '" . $ref . "'");
                        if ($cek['ready_cs'] == 0) {
                            return $this->ready($ref, $id_karyawan, 0, 0);
                        } else {
                            return $update;
                        }
                    } else {
                        return $update;
                    }
                } else {
                    $cek_toko = $this->db(0)->get_where('order_data', "ref = '" . $ref . "' AND (id_toko = " . $id_toko . " OR id_afiliasi = " . $id_toko . ")", 'id_afiliasi');
                    if (isset($cek_toko[$id_toko])) {
                        $where = "ref = '" . $ref . "' AND id_ambil_aff = 0 AND id_afiliasi = " . $id_toko;
                        $set = "id_ambil_aff = " . $id_karyawan . ", id_ambil_driver = " . $id_driver . ", tgl_ambil_aff = '" . $dateNow . "'";
                        $update = $this->db(0)->update("order_data", $set, $where);
                        if ($update['errno'] == 0) {
                            $cek = $this->db(0)->get_where_row('order_data', "ref = '" . $ref . "' AND id_afiliasi = " . $id_toko);
                            if ($cek['ready_aff_cs'] == 0) {
                                return $this->ready($ref, $id_karyawan, 0, 0);
                            } else {
                                return $update;
                            }
                        } else {
                            return $update;
                        }
                    }
                }
            } else {
                return $update;
            }
        }
    }

    function ready($ref, $id_karyawan, $expedisi = 0, $notif = 1)
    {
        $dateNow = date("Y-m-d H:i:s");
        $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);
        $where = "ref = '" . $ref . "' AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ")";
        $cek_toko_asal = $this->db(0)->get_where('order_data', $where, 'id_toko');
        if (isset($cek_toko_asal[$this->userData['id_toko']])) {
            $set = "ready_cs = " . $id_karyawan . ", ready_date = '" . $dateNow . "', expedisi = " . $expedisi;
            $where = "ref = '" . $ref . "' AND ready_cs = 0";
            $update = $this->db(0)->update("ref", $set, $where);
            if ($update['errno'] <> 0) {
                return $update;
            }
        } else {
            $cek_toko = $this->db(0)->get_where('order_data', $where, 'id_afiliasi');
            if (isset($cek_toko[$this->userData['id_toko']])) {
                $set = "ready_aff_cs = " . $id_karyawan . ", ready_aff_date = '" . $dateNow . "'";
                $where = "ref = '" . $ref . "' AND ready_aff_cs = 0 AND id_afiliasi = " . $this->userData['id_toko'];
                $update = $this->db(0)->update("order_data", $set, $where);
                if ($update['errno'] <> 0) {
                    return $update;
                } else {
                    $set = "status_order = 0, id_user_afiliasi = " . $id_karyawan;
                    $where = "ref = '" . $ref . "' AND id_user_afiliasi = 0 AND id_afiliasi = " . $this->userData['id_toko'];
                    $update = $this->db(0)->update("order_data", $set, $where);
                    if ($update['errno'] == 0) {
                        if (PV::PRO == 1 && $notif == 1) {
                            $get = $cek_toko[$this->userData['id_toko']];
                            $nama_sumber = strtoupper($this->dToko[$get['id_afiliasi']]['nama_toko']);
                            $nama_target = strtoupper($this->dToko[$get['id_toko']]['inisial']);
                            $pelanggan = strtoupper($this->dPelangganAll[$get['id_pelanggan']]['nama']);
                            $cs_name = $this->dKaryawanAll[$get['id_user_afiliasi']]['nama'];
                            $cs = strtoupper(substr($cs_name, 0, 2) . "-" . $get['id_user_afiliasi']);
                            $sort_ref = substr($get['ref'], -4);
                            $text = "*" . $nama_sumber . "* _#" . $sort_ref . "_ \n" . $nama_target . " " . $pelanggan . " SIAP JEMPUT \n_" . $cs . "_";

                            $target = $this->dToko[$get['id_toko']]['hp'];
                            $kirim = $this->data("WA")->send_wa(PV::API_KEY['fonnte'], $target, $text, 1);
                            if ($kirim['status'] <> true) {
                                print_r($kirim);
                            }
                        }
                    }
                }
            }
        }

        $cek_stok_produksi = $this->db(0)->get_where_row('order_data', "ref = '" . $ref . "'");
        $id = $cek_stok_produksi['id_order_data'];
        $up_stok = $this->terima_stok_satuan($id, $ref);
        if ($up_stok['errno'] <> 0) {
            return $up_stok;
        }

        return $update;
    }

    function terima_stok_satuan($id, $ref)
    {
        $update = $this->db(0)->update("master_mutasi", "stat = 1", "id_target = '" . $this->userData['id_toko'] . "' AND pid = " . $id);
        if ($update['errno'] == 0) {
            $count_mutasi = $this->db(0)->count_where("master_mutasi", "ref = '" . $ref . "' AND stat = 0");
            if ($count_mutasi == 0) {
                $update = $this->db(0)->update("master_input", "cek = 1", "id = '" . $ref . "'");
                if ($update['errno'] <> 0) {
                    return $update;
                }
            }
        }
        return $update;
    }

    function terima_stok_semua($ref)
    {
        $update = $this->db(0)->update("master_mutasi", "stat = 1", "id_target = '" . $this->userData['id_toko'] . "' AND ref = '" . $ref . "'");
        if ($update['errno'] <> 0) {
            return $update;
        }
        $update = $this->db(0)->update("master_input", "cek = 1", "id_target = '" . $this->userData['id_toko'] . "' AND id = '" . $ref . "'");
        return $update;
    }
}
