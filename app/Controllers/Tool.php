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

   function update_harga_paket_by_group($month = null)
   {
      // sanitize/normalize month input (expected format: YYYY-MM). If empty or invalid, use current month.
      if ($month === null) {
         $month = date('Y-m');
      } else {
         $month = preg_replace('/[^0-9\-]/', '', $month);
         if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
         }
      }

      $timeFilter = " AND insertTime LIKE '" . $month . "%'";

      $cols_od = "paket_group, SUM((harga*jumlah)+harga_paket) as total";
      $where_od = "paket_group <> ''" . $timeFilter . " GROUP BY paket_group";
      $sum_od = $this->db(0)->get_cols_where('order_data', $cols_od, $where_od);

      $cols_mm = "paket_group, SUM((harga_jual*qty)+harga_paket) as total";
      $where_mm = "paket_group <> ''" . $timeFilter . " GROUP BY paket_group";
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
         $where = "paket_group = '" . $pg . "' AND price_locker = 1" . $timeFilter;
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
         // only reset harga for rows within the selected month to avoid touching historical data
         $r1 = $this->db(0)->update('order_data', "harga = 0", "paket_group <> ''" . $timeFilter);
         $r2 = $this->db(0)->update('master_mutasi', "harga_jual = 0", "paket_group <> ''" . $timeFilter);
         $result['_reset'] = [
            'order_errno' => isset($r1['errno']) ? $r1['errno'] : null,
            'mutasi_errno' => isset($r2['errno']) ? $r2['errno'] : null
         ];
      }

      echo json_encode($result);
   }

   /**
    * Clean orphaned ref records based on month
    * Deletes ref records that don't exist in order_data or master_mutasi
    *
    * @param string $month Format: YYYY-MM (default: current month)
    * @return void Outputs JSON result
    */
   function clean_ref($month = null)
   {
      // Sanitize/normalize month input (expected format: YYYY-MM)
      if ($month === null) {
         $month = date('Y-m');
      } else {
         $month = preg_replace('/[^0-9\-]/', '', $month);
         if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            echo json_encode([
               'success' => false,
               'error' => 'Invalid month format. Use YYYY-MM format (e.g., 2025-01)'
            ]);
            return;
         }
      }

      $timeFilter = "insertTime LIKE '" . $month . "%'";
      $result = [
         'month' => $month,
         'total_checked' => 0,
         'total_deleted' => 0,
         'orphaned_refs' => [],
         'errors' => []
      ];

      // Get all refs from ref table for the specified month
      $refs_in_table = $this->db(0)->get_where('ref', $timeFilter, 'ref');

      if (!is_array($refs_in_table) || count($refs_in_table) == 0) {
         echo json_encode([
            'success' => true,
            'message' => 'No ref records found for month ' . $month,
            'data' => $result
         ]);
         return;
      }

      $result['total_checked'] = count($refs_in_table);

      // Check each ref if it exists in order_data or master_mutasi
      foreach ($refs_in_table as $ref_key => $ref_data) {
         // Check in order_data
         $exists_in_order = $this->db(0)->count_where('order_data', "ref = '" . $ref_key . "'");

         // Check in master_mutasi
         $exists_in_mutasi = $this->db(0)->count_where('master_mutasi', "ref = '" . $ref_key . "'");

         // If ref doesn't exist in both tables, it's orphaned
         if ($exists_in_order == 0 && $exists_in_mutasi == 0) {
            $result['orphaned_refs'][] = [
               'ref' => $ref_key,
               'id_toko' => $ref_data['id_toko'],
               'insertTime' => $ref_data['insertTime']
            ];

            // Delete orphaned ref
            $delete_result = $this->db(0)->delete_where('ref', "ref = '" . $ref_key . "'");

            if ($delete_result['errno'] == 0) {
               $result['total_deleted']++;
            } else {
               $result['errors'][] = [
                  'ref' => $ref_key,
                  'error' => $delete_result['error']
               ];
            }
         }
      }

      echo json_encode([
         'success' => true,
         'message' => 'Cleanup completed for month ' . $month . '. Deleted ' . $result['total_deleted'] . ' out of ' . $result['total_checked'] . ' ref records.',
         'data' => $result
      ], JSON_PRETTY_PRINT);
   }

   /**
    * Preview orphaned ref records without deleting
    *
    * @param string $month Format: YYYY-MM (default: current month)
    * @return void Outputs JSON result
    */
   function preview_orphaned_ref($month = null)
   {
      // Sanitize/normalize month input
      if ($month === null) {
         $month = date('Y-m');
      } else {
         $month = preg_replace('/[^0-9\-]/', '', $month);
         if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            echo json_encode([
               'success' => false,
               'error' => 'Invalid month format. Use YYYY-MM format (e.g., 2025-01)'
            ]);
            return;
         }
      }

      $timeFilter = "insertTime LIKE '" . $month . "%'";
      $result = [
         'month' => $month,
         'total_checked' => 0,
         'total_orphaned' => 0,
         'orphaned_refs' => []
      ];

      // Get all refs from ref table for the specified month
      $refs_in_table = $this->db(0)->get_where('ref', $timeFilter, 'ref');

      if (!is_array($refs_in_table) || count($refs_in_table) == 0) {
         echo json_encode([
            'success' => true,
            'message' => 'No ref records found for month ' . $month,
            'data' => $result
         ]);
         return;
      }

      $result['total_checked'] = count($refs_in_table);

      // Check each ref if it exists in order_data or master_mutasi
      foreach ($refs_in_table as $ref_key => $ref_data) {
         // Check in order_data
         $exists_in_order = $this->db(0)->count_where('order_data', "ref = '" . $ref_key . "'");

         // Check in master_mutasi
         $exists_in_mutasi = $this->db(0)->count_where('master_mutasi', "ref = '" . $ref_key . "'");

         // If ref doesn't exist in both tables, it's orphaned
         if ($exists_in_order == 0 && $exists_in_mutasi == 0) {
            $result['orphaned_refs'][] = [
               'ref' => $ref_key,
               'id_toko' => $ref_data['id_toko'],
               'insertTime' => $ref_data['insertTime']
            ];
            $result['total_orphaned']++;
         }
      }

      echo json_encode([
         'success' => true,
         'message' => 'Preview completed for month ' . $month . '. Found ' . $result['total_orphaned'] . ' orphaned ref records out of ' . $result['total_checked'] . ' total.',
         'data' => $result
      ], JSON_PRETTY_PRINT);
   }
}
