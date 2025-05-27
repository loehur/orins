<?php

class Riwayat_Jual extends Controller
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
         "title" => "Barang - Riwayat Jual"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $this->view(__CLASS__ . '/content', []);
   }

   function riwayat_data($date_f = "", $date_t = "")
   {
      $data['barang'] = $this->db(0)->get_where('master_barang', "en = 1", "id");

      if ($date_f <> "") {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "stat = 1 AND (insertTime BETWEEN '" . $date_f . " 00:00:00' AND  '" . $date_t . " 00:00:00') AND id_sumber = " . $this->userData['id_toko'] . "  AND jenis = 2");
      } else {
         $data['mutasi'] = [];
      }

      $this->view(__CLASS__ . '/data', $data);
   }
}
