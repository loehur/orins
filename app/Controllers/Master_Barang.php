<?php

class Master_Barang extends Controller
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
         "title" => "Master Data - Barang"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => $this->page, "parse" => ""]);
   }

   public function content()
   {
      $data['barang'] = $this->db(0)->get('master_barang');
      $data['grup'] = $this->db(0)->get('master_grup');
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $kode = $_POST['kode'];
      $nama = $_POST['nama'];
      $harga_1 = $_POST['harga_1'];
      $harga_2 = $_POST['harga_2'];
      $id_grup = $_POST['id_grup'];
      $sn = isset($_POST['sn']) ? $_POST['sn'] : 0;

      $cols = 'kode,nama,harga_1,harga_2,id_grup,sn';
      $vals = "'" . $kode . "','" . $nama . "'," . $harga_1 . "," . $harga_2 . "," . $id_grup . "," . $sn;
      $do = $this->db(0)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] == 0) {
         echo 0;
      } else {
         echo $do['error'];
      }
   }
}
