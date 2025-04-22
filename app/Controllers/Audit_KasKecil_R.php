<?php

class Audit_KasKecil_R extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[6])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Audit - Riwayat Setor"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $cols = "ref_setoran, SUM(jumlah) AS jumlah";
      $where = "(tipe = 0 OR tipe = 5) AND id_sumber = " . $this->userData['id_toko'] . " AND ref_setoran <> '' GROUP BY ref_setoran";
      $data['kas_kecil'] = $this->db(0)->get_cols_where("kas_kecil", $cols, $where, 1, "ref_setoran");
      $this->view(__CLASS__ . '/content', $data);
   }
}
