<?php

class Barang_Riwayat extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[7])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Barang - Riwayat"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['barang'] = $this->db(0)->get_where('master_barang', "sp = 0", 'id');
      $this->view(__CLASS__ . '/content', $data);
   }

   function data($kode, $sn = "", $filter = "")
   {
      $data['barang'] = $this->db(0)->get_where_row('master_barang', "sp = 0 AND id = '" . $kode . "'");
      $data['supplier'] = $this->db(0)->get('master_supplier', "id");
      $data['akun_pakai'] = $this->db(0)->get('akun_pakai', "id");

      $where = "id_barang = '" . $kode . "' AND stat <> 0";
      if ($sn <> "") {
         $where .= " AND sn = '" . $sn . "'";
      }

      switch ($filter) {
         case 'Masuk':
            $where .= " AND jenis = 0";
            break;
         case 'Toko Jual':
            $where .= " AND jenis = 2 AND id_sumber <> 0";
            break;
         case 'Gudang Jual':
            $where .= " AND jenis = 2 AND id_sumber = 0";
            break;
         case 'Transfer':
            $where .= " AND jenis = 1";
            break;
         case 'Retur':
            $where .= " AND jenis = 3";
            break;
         case 'Pakai':
            $where .= " AND jenis = 4";
            break;
      }

      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where);
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $this->view(__CLASS__ . '/data', $data);
   }

   function update_sn()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];

      $data = $this->db(0)->get_where_row('master_mutasi', "id = '" . $id . "'");

      if (isset($data['sn'])) {
         $cek_sn = $this->db(0)->count_where('master_mutasi', "sn = '" . $value . "' AND id_barang = '" . $data['id_barang'] . "' AND jenis = 0");
         if ($cek_sn == 0) {
            $where = "id_barang = '" . $data['id_barang'] . "' AND sn = '" . $data['sn'] . "'";
            $up = $this->db(0)->update("master_mutasi", "sn = '" . strtoupper($value) . "'", $where);
            echo $up['errno'] == 0 ? 0 : $up['error'];
         } else {
            echo "Duplicate SN " . $value;
            exit();
         }
      } else {
         echo "No Data";
         exit();
      }
      echo 0;
   }

   function update_sds()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];

      $data = $this->db(0)->get_where_row('master_mutasi', "id = '" . $id . "'");

      if (isset($data['sn'])) {
         $new_sds = $value == 0 ? 1 : 0;
         $where = "id_barang = '" . $data['id_barang'] . "' AND sn = '" . $data['sn'] . "'";
         $up = $this->db(0)->update("master_mutasi", "sds = " . $new_sds, $where);
         echo $up['errno'] == 0 ? $new_sds : $up['error'];
      } else {
         echo $data['sds'];
      }
   }
}
