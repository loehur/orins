<?php

class Master_Grup extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], $this->pMaster)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = $this->page . "/load";
      $this->v_content = $this->page . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Master Data - Grup"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => $this->page, "parse" => ""]);
   }

   public function content()
   {

      $data = $this->db(0)->get('master_grup');
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $nama = $_POST['nama'];
      $cols = 'nama';
      $vals = "'" . $nama . "'";
      $do = $this->db(0)->insertCols('master_grup', $cols, $vals);
      if ($do['errno'] == 0) {
         echo $do['errno'];
      } else {
         print_r($do['error']);
      }
   }
}
