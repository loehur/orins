<?php

class Log extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function sync()
   {
      $this->dataSynchrone();
      $this->data();
   }

   function change_toko($id)
   {
      if (!in_array($this->userData['user_tipe'], $this->pFinance)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $where = "id_user = " . $this->userData['id_user'];
      $set = "id_toko = " . $id;
      $this->model('M_DB_1')->update("user", $set, $where);
      $this->dataSynchrone();
   }
}
