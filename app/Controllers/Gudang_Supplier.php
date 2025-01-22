<?php

class Gudang_Supplier extends Controller
{
   public $main_table = "pelanggan";

   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[7])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Gudang - Supplier"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "1")
   {
      $data['supplier'] = $this->db(0)->get('master_supplier');
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $name = strtoupper($_POST['nama']);
      $id = substr($name, 0, 1) . date("ymd") . rand(0, 9);
      $do = $this->db(0)->insertCols("master_supplier", "id, nama", "'" . $id . "','" . strtoupper($name) . "'");
      if ($do['errno'] <> 0) {
         echo $do['error'];
      } else {
         echo 0;
      }
   }
}
