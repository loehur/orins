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

         $update_mutasi = $this->db(0)->update('master_mutasi', "jenis = 2", "ref IN (" . $ref_list . ")");
         print_r($update_mutasi);
      }
   }

   function update_harga_paket_by_group()
   {
      $cols_od = "paket_group, SUM((harga*jumlah)+harga_paket) as total";
      $where_od = "paket_group <> '' GROUP BY paket_group";
      $sum_od = $this->db(0)->get_cols_where('order_data', $cols_od, $where_od);

      $cols_mm = "paket_group, SUM((harga_jual*qty)+harga_paket) as total";
      $where_mm = "paket_group <> '' GROUP BY paket_group";
      $sum_mm = $this->db(0)->get_cols_where('master_mutasi', $cols_mm, $where_mm);

      $od_total = [];
      $mm_total = [];
      $groups = [];

      if (is_array($sum_od)) {
         foreach ($sum_od as $row) {
            $pg = $row['paket_group'];
            $od_total[$pg] = (float)$row['total'];
            $groups[$pg] = 1;
         }
      }

      if (is_array($sum_mm)) {
         foreach ($sum_mm as $row) {
            $pg = $row['paket_group'];
            $mm_total[$pg] = (float)$row['total'];
            $groups[$pg] = 1;
         }
      }

      $result = [];
      $all_ok = true;
      foreach (array_keys($groups) as $pg) {
         $total = (isset($od_total[$pg]) ? $od_total[$pg] : 0) + (isset($mm_total[$pg]) ? $mm_total[$pg] : 0);
         $set = "harga_paket = " . $total;
         $where = "paket_group = '" . $pg . "' AND price_locker = 1";
         $u1 = $this->db(0)->update('order_data', $set, $where);
         $u2 = $this->db(0)->update('master_mutasi', $set, $where);
         $e1 = isset($u1['errno']) ? $u1['errno'] : 0;
         $e2 = isset($u2['errno']) ? $u2['errno'] : 0;
         if ($e1 <> 0 || $e2 <> 0) {
            $all_ok = false;
         }
         $result[$pg] = [
            'total' => $total,
            'order_errno' => isset($u1['errno']) ? $u1['errno'] : null,
            'mutasi_errno' => isset($u2['errno']) ? $u2['errno'] : null
         ];
      }

      if ($all_ok) {
         $r1 = $this->db(0)->update('order_data', "harga = 0", "paket_group <> ''");
         $r2 = $this->db(0)->update('master_mutasi', "harga_jual = 0", "paket_group <> ''");
         $result['_reset'] = [
            'order_errno' => isset($r1['errno']) ? $r1['errno'] : null,
            'mutasi_errno' => isset($r2['errno']) ? $r2['errno'] : null
         ];
      }

      echo json_encode($result);
   }
}
