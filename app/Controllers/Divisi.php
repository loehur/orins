<?php

class Divisi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[1])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Managment - Divisi Produksi"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data = $this->db(0)->get('divisi');
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $dvs = $_POST['dvs'];
      $cols = 'id_toko, divisi';
      $vals = "'" . $this->userData['id_toko'] . "','" . $dvs . "'";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND divisi = '" . $dvs . "'";
      $dataCount = $this->db(0)->count_where('divisi', $whereCount);
      if ($dataCount <> 1) {
         $do = $this->db(0)->insertCols('divisi', $cols, $vals);
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
