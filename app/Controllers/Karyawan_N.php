<?php

class Karyawan_N extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pAdmin)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index()
   {

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Karyawan Non Aktif"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $where = "en = 0 AND id_toko = " . $this->userData['id_toko'];
      $data = $this->model('M_DB_1')->get_where('karyawan', $where);
      $this->view($this->v_content, $data);
   }

   public function restore()
   {
      $id = $_POST['id'];
      $set = "en = 1";
      $where = "id_karyawan = " . $id;
      $update = $this->model('M_DB_1')->update("karyawan", $set, $where);
      echo $update['errno'];
      $this->dataSynchrone();
   }
}
