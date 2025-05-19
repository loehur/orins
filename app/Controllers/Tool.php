<?php

class Tool extends Controller
{
   function mutasi() //davinci aff to davinci rekanan
   {
      $id_rekanan = 1245;
      $cek = $this->db(0)->get_where("order_data", "id_toko = 4");
      echo "<pre>";
      print_r($cek);
      echo "</pre>";
   }
}
