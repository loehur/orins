<?php

class Akun extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      $this->v_load = $this->page . "/load";
      $this->v_content = $this->page . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $this->page
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => $this->page, "parse" => ""]);
   }

   public function content()
   {
      $this->view($this->v_content);
   }

   public function updatePass()
   {
      $pass = $_POST['pass'];
      $pass_ = $_POST['pass_'];
      $pass__ = $_POST['pass__'];

      if ($pass_ <> $pass__) {
         echo "Password Baru tidak Cocok";
         exit();
      }

      if ($this->model('Enc')->enc($pass) <> $this->userData['password']) {
         echo "Password Lama Salah!";
         exit();
      }

      $new = $this->model('Enc')->enc($pass_);

      $where = "id_user = '" . $this->userData['id_user'] . "'";
      $set = "password = '" . $new . "'";
      $update = $this->db(0)->update("user", $set, $where);
      echo $update['errno'];
   }
}
