<?php

class Log extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
   }

   public function sync()
   {
      $this->dataSynchrone();
      $this->data_order();
   }

   function change_toko($id)
   {
      if (!in_array($this->userData['user_tipe'], PV::PRIV[100])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $where = "id_user = " . $this->userData['id_user'];
      $set = "id_toko = " . $id;
      $this->db(0)->update("user", $set, $where);
      $this->dataSynchrone();
   }
}
