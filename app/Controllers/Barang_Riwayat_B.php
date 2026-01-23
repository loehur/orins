<?php

class Barang_Riwayat_B extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Barang - Riwayat Bulanan"
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
      $data['akun_pakai'] = $this->db(0)->get('akun_pakai', "id");
      if ($sn == "") {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "id_barang = '" . $kode . "' AND stat <> 0");
      } else {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "id_barang = '" . $kode . "' AND sn = '" . $sn . "' AND stat <> 0");
      }
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $this->view(__CLASS__ . '/data', $data);
   }
}
