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

   function data($kode, $sn = "")
   {
      $data['barang'] = $this->db(0)->get_where_row('master_barang', "sp = 0 AND id = '" . $kode . "'");
      $data['supplier'] = $this->db(0)->get('master_supplier', "id");
      if ($sn == "") {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "id_barang = '" . $kode . "' AND (id_sumber = 0 OR id_target = 0) AND jenis <> 2");
      } else {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "id_barang = '" . $kode . "' AND sn = '" . $sn . "' AND (id_sumber = 0 OR id_target = 0) AND jenis <> 2");
      }
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', 'id_pelanggan_jenis = 0', 'id_pelanggan');
      $this->view(__CLASS__ . '/data', $data);
   }
}
