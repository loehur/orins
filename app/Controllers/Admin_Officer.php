<?php

class Admin_Officer extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[0])) {
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
         "title" => "Managment - Office User"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $where = "user_tipe IN(5,6,7,8)";
      $data = $this->db(0)->get_where('user', $where);
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $user = $_POST['user'];
      $nama = $_POST['nama'];
      $office = $_POST['office'];

      $pass = $this->model('Enc')->enc("123");
      $cols = 'id_toko, nama, user, password, user_tipe';
      $vals = "'" . $this->userData['id_toko'] . "','" . $nama . "','" . $user . "','" . $pass . "'," . $office;

      $do = $this->db(0)->insertCols('user', $cols, $vals);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Add Admin Officer Success!");
         echo $do['errno'];
      } else {
         print_r($do['error']);
      }
   }
}
