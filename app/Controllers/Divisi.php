<?php

class Divisi extends Controller
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

      $this->v_load = $this->page . "/load";
      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Set Produksi - Divisi"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["page" => $this->page]);
   }

   public function content()
   {

      $where = "id_toko = " . $this->userData['id_toko'];
      $data = $this->model('M_DB_1')->get_where('divisi', $where);
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $dvs = $_POST['dvs'];
      $cols = 'id_toko, divisi';
      $vals = "'" . $this->userData['id_toko'] . "','" . $dvs . "'";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND divisi = '" . $dvs . "'";
      $dataCount = $this->model('M_DB_1')->count_where('divisi', $whereCount);
      if ($dataCount <> 1) {
         $do = $this->model('M_DB_1')->insertCols('divisi', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Divisi Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Divisi Failed, Double Forbidden!");
         echo "Double Entry!";
      }
   }
}
