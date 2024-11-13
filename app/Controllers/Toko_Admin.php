<?php

class Toko_Admin extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pMaster)) {
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
         "title" => "Managment - Admin Toko"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["page" => $this->page]);
   }

   public function content()
   {

      $where = "user_tipe = 1 AND id_toko = " . $this->userData['id_toko'];
      $data = $this->model('M_DB_1')->get_where('user', $where);
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $no = $_POST['hp'];
      $pass = $this->model('Enc')->enc("123");
      $cols = 'id_toko, nama, user, password, user_tipe';
      $vals = "'" . $this->userData['id_toko'] . "','Admin','" . $no . "','" . $pass . "',1";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND user_tipe = 1";
      $dataCount = $this->model('M_DB_1')->count_where('user', $whereCount);
      if ($dataCount <> 1) {
         $do = $this->model('M_DB_1')->insertCols('user', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Admin Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Admin Failed, Double Admin Forbidden!");
         echo "Double Entry!";
      }
   }
}
