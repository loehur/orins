<?php

class Office_Kas extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Office - Kas"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $whereSplit = "id_target = 0 AND tipe = 0 AND st = 0";
      $data['split'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereSetor = "id_target = 0 AND tipe = 0 AND st = 1 AND ref_setoran = ''";
      $data['setor'] = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $whereSetor);

      if (!$data['setor']) {
         $data['setor'] = 0;
      }
      $this->view(__CLASS__ . '/content', $data);
   }

   function verify_kasKecil($id, $status)
   {
      $set = "st = '" . $status . "'";
      $where = "id = '" . $id . "'";
      $update = $this->db(0)->update("kas_kecil", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }
}
