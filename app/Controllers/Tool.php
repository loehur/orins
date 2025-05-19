<?php

class Tool extends Controller
{
   function mutasi() //davinci aff to davinci rekanan
   {
      $id_rekanan = 1245;
      $pelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $cek = $this->db(0)->get_where("order_data", "id_toko = 4 AND cancel = 0 AND id_afiliasi <> 0 AND id_pelanggan <> 0",);

      echo "<pre>";
      foreach ($cek as $k => $c) {
         $id[$k] = $c['id_order_data'];
         $pelanggan[$k] = $pelanggan[$c['id_pelanggan']]['nama'];
         $ref[$k] = $c['ref'];
         $cs_id[$k] = $c['id_user_afiliasi'];
         $id_toko[$k] = $c['id_afiliasi'];

         // $up = $this->db(0)->update("ref", "mark = '" . $pelanggan[$k] . "'", "ref = '" . $ref[$k] . "' AND mark = ''");
         // if ($up['errno'] <> 0) {
         //    echo $up['error'] . "<br>";
         // } else {
         //    echo $pelanggan[$k] . " OK<br>";
         // }

         // $up = $this->db(0)->update("order_data", "id_penerima = '" . $cs_id[$k] . "'", "id_order_data = " . $id[$k]);
         // if ($up['errno'] <> 0) {
         //    echo $up['error'] . "<br>";
         // } else {
         //    echo $pelanggan[$k] . " OK<br>";
         // }

         // $up = $this->db(0)->update("order_data", "id_toko = '" . $id_toko[$k] . "'", "id_order_data = " . $id[$k]);
         // if ($up['errno'] <> 0) {
         //    echo $up['error'] . "<br>";
         // } else {
         //    echo $pelanggan[$k] . " ID TOKO OK<br>";
         // }

         $up = $this->db(0)->update("order_data", "id_pelanggan = " . $id_rekanan, "id_order_data = " . $id[$k]);
         if ($up['errno'] <> 0) {
            echo $up['error'] . "<br>";
         } else {
            echo $pelanggan[$k] . " OK<br>";
         }
      }
      echo "</pre>";
   }
}
