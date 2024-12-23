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
      $data['barang'] = $this->db(0)->get_where('master_barang', "sp = 0", 'code');
      $this->view(__CLASS__ . '/content', $data);
   }

   function riwayat_data($mode, $val)
   {
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['barang'] = $this->db(0)->get_where('master_barang', "en = 1", "code");
      if ($mode == 0) {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "stat = 1 AND insertTime LIKE '" . $val . "%' AND (id_sumber = " . $this->userData['id_toko'] . " OR id_target = " . $this->userData['id_toko'] . ")");
      } else {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "stat = 1 AND kode_barang = '" . $val . "' ORDER BY id DESC");
      }
      $this->view(__CLASS__ . '/data', $data);
   }
}
