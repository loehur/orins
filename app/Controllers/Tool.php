<?php

class Tool extends Controller
{
   // function mutasi() //davinci aff to davinci rekanan
   // {
   //    $id_rekanan = 1245;
   //    $dPelanggan = $this->db(0)->get_where('pelanggan', 'id_toko = 2', 'id_pelanggan');
   //    $dPelangganDVC = $this->db(0)->get_where('pelanggan', 'id_toko = 4', 'id_pelanggan');
   //    $cek = $this->db(0)->get_where("order_data", "id_toko = 2 AND cancel = 0 AND id_afiliasi <> 0 AND id_pelanggan <> 0 AND insertTime LIKE '2025-05%'",);

   //    echo "<pre>";
   //    foreach ($cek as $k => $c) {
   //       $id[$k] = $c['id_order_data'];

   //       if (isset($dPelanggan[$c['id_pelanggan']])) {
   //          $pelanggan[$k] = $dPelanggan[$c['id_pelanggan']]['nama'];
   //       } else {

   //          if (isset($dPelangganDVC[$c['id_pelanggan']])) {
   //             $pelanggan[$k] = $dPelangganDVC[$c['id_pelanggan']]['nama'];

   //             $up = $this->db(0)->update("order_data", "id_pelanggan = " . $id_rekanan . ", id_afiliasi = 0, id_user_afiliasi = 0", "id_order_data = " . $id[$k]);
   //             if ($up['errno'] <> 0) {
   //                echo $up['error'] . "<br>";
   //             } else {
   //                echo $pelanggan[$k] . " OK pindah sudah<br>";
   //             }
   //          } else {
   //             echo $c['id_pelanggan'] . " REJECTED<br>";
   //             continue;
   //          }
   //       }

   //       $ref[$k] = $c['ref'];
   //       $cs_id[$k] = $c['id_user_afiliasi'];
   //       $id_toko[$k] = $c['id_afiliasi'];

   //       // $up = $this->db(0)->update("ref", "mark = '" . $pelanggan[$k] . "'", "ref = '" . $ref[$k] . "' AND mark = ''");
   //       // if ($up['errno'] <> 0) {
   //       //    echo $up['error'] . "<br>";
   //       // } else {
   //       //    echo $pelanggan[$k] . " OK<br>";
   //       // }

   //       // $up = $this->db(0)->update("order_data", "id_penerima = '" . $cs_id[$k] . "'", "id_order_data = " . $id[$k]);
   //       // if ($up['errno'] <> 0) {
   //       //    echo $up['error'] . "<br>";
   //       // } else {
   //       //    echo $pelanggan[$k] . " OK<br>";
   //       // }

   //       // $up = $this->db(0)->update("order_data", "id_toko = '" . $id_toko[$k] . "'", "id_order_data = " . $id[$k]);
   //       // if ($up['errno'] <> 0) {
   //       //    echo $up['error'] . "<br>";
   //       // } else {
   //       //    echo $pelanggan[$k] . " ID TOKO OK<br>";
   //       // }
   //    }
   //    echo "</pre>";
   // }

   function fix_jual_gudang()
   {
      $input = $this->db(0)->get_where('master_input', "tipe = 3 AND id_sumber = 0", 'id');
      $refs = array_keys($input);
      $mutasi = array();
      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $mutasi = $this->db(0)->get_where('master_mutasi', "ref IN (" . $ref_list . ")");
      }

      echo "<pre>";
      print_r($mutasi);
      echo "</pre>";
   }
}
