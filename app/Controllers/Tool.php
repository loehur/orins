<?php

class Tool extends Controller
{
   function mutasi() //davinci aff to davinci rekanan
   {
      $id_rekanan = 1245;
      $pelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $cek = $this->db(0)->get_where("order_data", "id_toko = 4");

      echo "<pre>";
      foreach ($cek as $c) {
         echo $pelanggan[$c['id_pelanggan']]['nama'] . "<br>";
      }
      echo "</pre>";
   }
}
