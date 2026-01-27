<?php

class Cron extends Controller
{
   public function unser()
   {
      $data = unserialize('a:5:{i:0;a:7:{s:3:"c_h";s:10:"#500-#163-";s:3:"c_b";s:12:"#500&-#163&-";s:3:"n_b";s:17:"100CM X 60CM LPS ";s:3:"n_v";s:17:"100CM X 60CM LPS ";s:1:"g";s:7:"#1-#27-";s:1:"h";i:0;s:1:"d";i:0;}i:1;a:7:{s:3:"c_h";s:9:"#500-#51-";s:3:"c_b";s:11:"#500&-#51&-";s:3:"n_b";s:20:"100CM X 60CM LIQUID ";s:3:"n_v";s:20:"100CM X 60CM LIQUID ";s:1:"g";s:6:"#1-#7-";s:1:"h";i:0;s:1:"d";i:0;}i:2;a:7:{s:3:"c_h";s:9:"#500-#63-";s:3:"c_b";s:13:"#500&-#63&34-";s:3:"n_b";s:20:"100CM X 60CM 101050 ";s:3:"n_v";s:22:"100CM X 60CM 101050-c ";s:1:"g";s:6:"#1-#5-";s:1:"h";i:0;s:1:"d";i:0;}i:3;a:7:{s:3:"c_h";s:10:"#500-#377-";s:3:"c_b";s:15:"#500&-#377&150-";s:3:"n_b";s:20:"100CM X 60CM 101060 ";s:3:"n_v";s:22:"100CM X 60CM 101060-w ";s:1:"g";s:6:"#1-#6-";s:1:"h";i:0;s:1:"d";i:0;}i:4;a:7:{s:3:"c_h";s:9:"#500-#56-";s:3:"c_b";s:11:"#500&-#56&-";s:3:"n_b";s:17:"100CM X 60CM 6MM ";s:3:"n_v";s:17:"100CM X 60CM 6MM ";s:1:"g";s:7:"#1-#10-";s:1:"h";i:0;s:1:"d";i:0;}}');
      echo "<pre>";
      print_r($data);
      echo "</pre>";
   }

   function insertRef($year)
   {
      $data['order'] = $this->db(0)->get_where('order_data', "insertTime LIKE '" . $year . "%'", 'ref', 1);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "insertTime LIKE '" . $year . "%'", 'ref', 1);

      $ref1 = array_keys($data['order']);
      $ref2 = array_keys($data['mutasi']);
      $refs = array_unique(array_merge($ref1, $ref2));
      $cols = 'ref';

      foreach ($refs as $r) {
         $vals = $r;
         $do = $this->db(0)->insertCols('ref', $cols, $vals);
         if ($do['errno'] <> 0) {
            continue;
         }
      }
   }

   function update_idbarang()
   {
      $barang = $this->db(0)->get('master_barang', "code");
      $mutasi = $this->db(0)->get_where('master_mutasi', 'id_barang = 0');
      foreach ($mutasi as $r) {
         $up = $this->db(0)->update('master_mutasi', "id_barang = '" . $barang[$r['kode_barang']]['id'] . "'", "id = " . $r['id']);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }
   }



   function update_idproduk($year)
   {
      $data_harga = $this->db(0)->get('produk_harga');
      $where = "insertTime LIKE '" . $year . "%' AND id_pelanggan <> 0";
      $data['order'] = $this->db(0)->get_where('order_data', $where);
      foreach ($data['order'] as $key => $do) {
         $parse_harga = $do['id_pelanggan_jenis'];
         if ($parse_harga == 100) {
            $parse_harga = 2;
         }
         $detail_harga = unserialize($do['detail_harga']);
         if (is_array($detail_harga)) {
            $countDH[$key] = count($detail_harga);
            foreach ($detail_harga as $dh_o) {
               $getHarga[$key][$dh_o['c_h']] = 0;
               foreach ($data_harga as $dh) {
                  if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $parse_harga] <> 0 && $dh['id_produk'] == 0) {
                     $getHarga[$key][$dh_o['c_h']] = $dh['harga_' . $parse_harga];
                     $where = "code = '" . $dh_o['c_h'] . "' AND id_produk = 0";
                     $set = "harga_" . $parse_harga . " = " .  $getHarga[$key][$dh_o['c_h']] . ", id_produk = " . $do['id_produk'];
                     $up = $this->db(0)->update("produk_harga", $set, $where);
                     if ($up['errno'] <> 0) {
                        echo $up['error'];
                        exit();
                     }
                     break;
                  }
               }
            }
         }
      }
   }

   function cek_input_ref()
   {
      $cek = $this->db(0)->get_where("master_input", "cek = 0");
      foreach ($cek as $c) {
         $hitung = $this->db(0)->count_where("master_mutasi", "ref = '" . $c['id'] . "'");
         if ($hitung == 0) {
            $this->db(0)->delete_where("master_input", "id = '" . $c['id'] . "' AND cek = 0");
         }
      }
   }

   function run_cek_tuntas()
   {
      $where_ref = "tuntas = 0";
      $cek = $this->db(0)->get_where('ref', $where_ref, "ref");
      $ref_tuntas = [];

      $refs = array_keys($cek);
      $tuntas_date = date("Y-m-d");

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $where = "ref IN (" . $ref_list . ")";

         $dOrder = $this->db(0)->get_where('order_data', $where, 'ref', 1);
         $dMutasi = $this->db(0)->get_where('master_mutasi', $where, 'ref', 1);

         $where_kas = "jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ") AND status_mutasi = 1";
         $dKas = $this->db(0)->get_where('kas', $where_kas, 'ref_transaksi', 1);

         $where_kasKecil = "ref IN (" . $ref_list . ") AND tipe = 0";
         $dKasKecil = $this->db(0)->get_where('kas_kecil', $where_kasKecil, 'ref', 1);

         $cols = "ref_transaksi, cancel, SUM(jumlah) as jumlah";
         $where = "ref_transaksi IN (" . $ref_list . ") AND cancel = 0 GROUP BY ref_transaksi";
         $dDiskon = $this->db(0)->get_cols_where('xtra_diskon', $cols, $where, 1, 'ref_transaksi');
         $dCharge = $this->db(0)->get_cols_where('charge', $cols, $where, 1, 'ref_transaksi');

         foreach ($refs as $r) {

            $charge[$r] = 0;
            if (isset($dCharge[$r]['jumlah'])) {
               $charge[$r] = $dCharge[$r]['jumlah'];
            }

            $stok[$r] = false;
            $ada_diskon[$r] = false;
            $verify_kas_kecil[$r] = true;

            if (isset($dKasKecil[$r]) && count($dKasKecil[$r]) > 0) {
               foreach ($dKasKecil[$r] as $kk) {
                  if ($kk['kas_kecil']['st'] <> 1) {
                     $verify_kas_kecil[$r] = false;
                     break;
                  }
               }
            }

            $bill[$r] = $charge[$r];
            $ambil_all[$r] = true;
            $verify_payment[$r] = 0;
            $cancel_count[$r] = 0;
            $stok[$r] = false;

            if (isset($dKas[$r]) && count($dKas[$r]) > 0) {
               foreach ($dKas[$r] as $dk) {
                  if ($dk['metode_mutasi'] == 1 && $dk['status_mutasi'] == 1 && $dk['status_setoran'] == 1) {
                     $verify_payment[$r] += $dk['jumlah'];
                  }

                  if (($dk['metode_mutasi'] == 2 || $dk['metode_mutasi'] == 3 || $dk['metode_mutasi'] == 4) && $dk['status_mutasi'] == 1) {
                     $verify_payment[$r] += $dk['jumlah'];
                  }
               }
            }

            if (isset($dDiskon[$r]) && count($dDiskon[$r]) > 0) {
               $ds = $dDiskon[$r];
               $verify_payment[$r] += $ds['jumlah'];
            }

            if (isset($dOrder[$r]) && count($dOrder[$r]) > 0) {
               foreach ($dOrder[$r] as $do) {
                  if ($do['stok'] == 1) {
                     $stok[$r] = true;
                  }

                  $jumlah = $do['harga'] * $do['jumlah'];
                  $cancel = $do['cancel'];

                  if ($cancel == 0 && $do['stok'] == 0) {
                     $bill[$r] += ($jumlah + $do['harga_paket']);
                     $bill[$r] -= $do['diskon'];

                     if ($do['diskon'] > 0) {
                        $ada_diskon[$r] = true;
                     }
                  }

                  if ($cancel == 1) {
                     $cancel_count[$r] += 1;
                  }

                  $id_ambil = $do['id_ambil'];
                  $divisi_arr = unserialize($do['spk_dvs']);
                  $countSPK = count($divisi_arr);
                  if ($id_ambil == 0 && $cancel == 0) {
                     if ($countSPK > 0 && $cancel == 0) {
                        $ambil_all[$r] = false;
                     }
                  }
               }
            } else {
               $dOrder[$r] = [];
            }

            if (isset($dMutasi[$r]) && count($dMutasi[$r]) > 0) {
               foreach ($dMutasi[$r] as $do) {

                  $cancel_barang = $do['stat'];
                  $jumlah = $do['qty'];
                  if ($cancel_barang <> 2) {
                     if ($do['diskon'] > 0) {
                        $ada_diskon[$r] = true;
                     }

                     $bill[$r] += (($jumlah * $do['harga_jual']) + $do['harga_paket']);
                     $bill[$r] -= ($do['diskon'] * $jumlah);
                  } else {
                     $cancel_count[$r] += 1;
                  }
               }
            } else {
               $dMutasi[$r] = [];
            }

            $order_count[$r] = count($dMutasi[$r]) + count($dOrder[$r]);
            if ($verify_payment[$r] == $bill[$r] && $ambil_all[$r] == true && $verify_kas_kecil[$r] == true) {
               if ($bill[$r] > 0 && $verify_payment[$r] > 0) {
                  array_push($ref_tuntas, $r);
               } else {
                  if ($stok[$r] == true || $ada_diskon[$r] == true) {
                     array_push($ref_tuntas, $r);
                  } else {
                     if ($order_count[$r] == $cancel_count[$r]) {
                        array_push($ref_tuntas, $r);
                     }
                  }
               }
            }
         }

         $total_tuntas = count($ref_tuntas);
         if ($total_tuntas > 0) {
            $rt_list = "";
            foreach ($ref_tuntas as $r) {
               $rt_list .= $r . ",";
            }
            $rt_list = rtrim($rt_list, ',');

            $where = "ref IN (" . $rt_list . ")";
            $set = "tuntas = 1, tuntas_date = '" . $tuntas_date . "'";
            $up = $this->db(0)->update("ref", $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'] . "\n";
            } else {
               $set = "tuntas = 1, tuntas_date = '" . $tuntas_date . "'";
               $up = $this->db(0)->update("order_data", $set, $where);
               $up = $this->db(0)->update("master_mutasi", $set, $where);
               echo $total_tuntas . " ORDER TUNTAS \n";
            }
         }
      }
   }

   public function cek_tuntas($ref = "", $print = false)
   {
      $where_ref = "ref = '" . $ref . "'";
      $cek = $this->db(0)->get_where_row('ref', $where_ref, 'ref');

      if (!isset($cek['ref'])) {
         if ($print) echo "Ref not found in DB.<br>";
         exit();
      }

      $ref = $cek['ref'];
      $tuntas_date = date("Y-m-d");
      
      // Data Gathering
      $where = "ref = '" . $ref . "'";
      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where);
      
      $where_kas = "jenis_transaksi = 1 AND ref_transaksi = '" . $ref . "' AND status_mutasi = 1";
      $data['kas'] = $this->db(0)->get_where('kas', $where_kas);

      $where_kasKecil = "ref = '" . $ref . "' AND tipe = 0";
      $data['kas_kecil'] = $this->db(0)->get_where('kas_kecil', $where_kasKecil);
      
      $where_diskon = "ref_transaksi = '" . $ref . "' AND cancel = 0";
      $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where_diskon);

      $where_charge = "ref_transaksi = '" . $ref . "' AND cancel = 0";
      $data['charge'] = $this->db(0)->get_where_row('charge', $where_charge);

      // Calculations
      $charge = isset($data['charge']['jumlah']) ? $data['charge']['jumlah'] : 0;
      $bill = $charge;
      $verify_payment = 0;
      
      $tuntas_status_db = false;
      $db_tuntas_date = "";

      $stok = false;
      $ada_diskon = false;
      $ambil_all = true;
      $verify_kas_kecil = true;
      $cancel_count = 0;

      // Verify Kas Kecil
      if (count($data['kas_kecil']) > 0) {
         foreach ($data['kas_kecil'] as $kk) {
            if ($kk['st'] <> 1) {
               $verify_kas_kecil = false;
            }
         }
      }

      // Verify Payment (Kas)
      if (count($data['kas']) > 0) {
         foreach ($data['kas'] as $dk) {
            if ($dk['metode_mutasi'] == 1 && $dk['status_mutasi'] == 1 && $dk['status_setoran'] == 1) {
               $verify_payment += $dk['jumlah'];
            }
            if (($dk['metode_mutasi'] == 2 || $dk['metode_mutasi'] == 3 || $dk['metode_mutasi'] == 4) && $dk['status_mutasi'] == 1) {
               $verify_payment += $dk['jumlah'];
            }
         }
      }

      // Verify Payment (Diskon)
      if (count($data['diskon']) > 0) {
         foreach ($data['diskon'] as $ds) {
            if ($ds['cancel'] == 0) {
               $verify_payment += $ds['jumlah'];
            }
         }
      }

      // Process Orders
      if (count($data['order']) > 0) {
         foreach ($data['order'] as $do) {
            if ($do['tuntas'] == 1) {
               $tuntas_status_db = true;
               $db_tuntas_date = $do['tuntas_date'];
            }

            if ($do['stok'] == 1) $stok = true;
            if ($do['diskon'] > 0) $ada_diskon = true;

            $jumlah = $do['harga'] * $do['jumlah'];
            $cancel = $do['cancel'];

            if ($cancel == 0 && $do['stok'] == 0) {
               $bill += ($jumlah + $do['harga_paket']);
               $bill -= $do['diskon'];
            }

            if ($cancel == 1) {
               $cancel_count += 1;
            }

            $id_ambil = $do['id_ambil'];
            $divisi_arr = unserialize($do['spk_dvs']);
            $countSPK = count($divisi_arr);
            if ($id_ambil == 0 && $cancel == 0 && $countSPK > 0) {
               $ambil_all = false;
            }
         }
      }

      // Process Mutasi
      if (count($data['mutasi']) > 0) {
         foreach ($data['mutasi'] as $dm) {
            if ($dm['tuntas'] == 1) {
               $tuntas_status_db = true;
               $db_tuntas_date = $dm['tuntas_date'];
            }

            if ($dm['diskon'] > 0) $ada_diskon = true;

            $cancel_barang = $dm['stat'];
            $jumlah = $dm['qty'];

            if ($cancel_barang <> 2) {
               $bill += (($jumlah * $dm['harga_jual']) + $dm['harga_paket']);
               $bill -= ($dm['diskon'] * $jumlah);
            } else {
               $cancel_count += 1;
            }
         }
      }

      $order_count = count($data['mutasi']) + count($data['order']);
      
      // Determine Output Status
      $ready_to_tuntas = false;
      $reason = "";

      if ($verify_payment == $bill && $ambil_all == true && $verify_kas_kecil == true) {
         if ($bill > 0 && $verify_payment > 0) {
            $ready_to_tuntas = true;
            $reason = "Normal Payment Matched";
         } else {
            if ($stok == true || $ada_diskon == true) {
               $ready_to_tuntas = true;
               $reason = "Zero Bill (Stok/Diskon)";
            } elseif ($order_count == $cancel_count) {
               $ready_to_tuntas = true;
               $reason = "All Cancelled";
            } else {
               $reason = "Zero Bill but not qualified";
            }
         }
      } else {
         $reason = "Criteria Not Met (Payment: " . ($verify_payment == $bill ? 'OK' : 'Diff') . ", Ambil: " . ($ambil_all ? 'OK' : 'No') . ", KasKecil: " . ($verify_kas_kecil ? 'OK' : 'No') . ")";
      }

      // Actions
      if (!$print) {
         if ($tuntas_status_db) {
            $this->update_ref($ref, $db_tuntas_date);
            exit();
         }
         if ($ready_to_tuntas) {
            $this->clearTuntas($ref);
         }
      } else {
         // Echo Robust Info
         echo "<h3>Cek Tuntas Debug Info</h3>";
         echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 50%;'>";
         echo "<tr><th colspan='2'>Main Status</th></tr>";
         echo "<tr><td>REF ID</td><td>" . $ref . "</td></tr>";
         echo "<tr><td>Tuntas Induk DB</td><td>" . $cek['tuntas'] . "</td></tr>";
         echo "<tr><td>Tuntas Items DB</td><td>" . ($tuntas_status_db ? 'Yes (' . $db_tuntas_date . ')' : 'No') . "</td></tr>";
         echo "<tr><td><strong>Decision</strong></td><td><strong>" . ($ready_to_tuntas ? "READY TO TUNTAS" : "NOT READY") . "</strong></td></tr>";
         echo "<tr><td>Reason</td><td>" . $reason . "</td></tr>";

         echo "<tr><th colspan='2'>Financials</th></tr>";
         // format as currency/number
         echo "<tr><td>Bill Amount</td><td>" . number_format($bill) . "</td></tr>";
         echo "<tr><td>Verify Payment</td><td>" . number_format($verify_payment) . "</td></tr>";
         echo "<tr><td>Charge</td><td>" . number_format($charge) . "</td></tr>";
         echo "<tr><td>Has Discount</td><td>" . ($ada_diskon ? 'Yes' : 'No') . "</td></tr>";

         echo "<tr><th colspan='2'>Logistics & Validation</th></tr>";
         echo "<tr><td>Ambil All Status</td><td>" . ($ambil_all ? 'Yes' : 'No (Pending)') . "</td></tr>";
         echo "<tr><td>Verify Kas Kecil</td><td>" . ($verify_kas_kecil ? 'Yes' : 'No (Invalid)') . "</td></tr>";
         echo "<tr><td>Stok Mode</td><td>" . ($stok ? 'Yes' : 'No') . "</td></tr>";
         
         echo "<tr><th colspan='2'>Counters</th></tr>";
         echo "<tr><td>Total Orders</td><td>" . $order_count . "</td></tr>";
         echo "<tr><td>Cancelled Count</td><td>" . $cancel_count . "</td></tr>";
         echo "</table>";

         if ($ready_to_tuntas) {
             echo "<br><strong>Executing ClearTuntas...</strong>";
             $this->clearTuntas($ref);
         }
      }
   }

   public function un_tuntas($ref = "")
   {
      if ($ref == "") {
         echo "No Ref Found";
         return;
      }

      $undo = $this->data('Operasi')->un_tuntas($ref);
      if ($undo['status'] == 'failed') {
         echo $undo['error'] . "\n";
      } else {
         echo "Success\n";
      }
   }

   public function clearTuntas($ref)
   {
      $today = date("Y-m-d");
      $set = "tuntas = 1, tuntas_date = '" . $today . "'";
      $where = "ref = '" . $ref . "'";
      $up = $this->db(0)->update("order_data", $set, $where);
      if ($up['errno'] == 0) {
         $up = $this->db(0)->update("master_mutasi", $set, $where);
         if ($up['errno'] == 0) {
            $this->update_ref($ref, $today);
         }
      }
   }

   function update_ref($ref, $date)
   {
      $set = "tuntas = 1, tuntas_date = '" . $date . "'";
      $where = "ref = '" . $ref . "'";
      $this->db(0)->update("ref", $set, $where);
   }

   function delete_ref($ref)
   {
      echo "<h3>Deleting Ref: " . $ref . "</h3>";
      $this->db(0)->query("START TRANSACTION");

      $where = "ref = '" . $ref . "'";

      // 1. Delete from REF
      $del1 = $this->db(0)->delete_where("ref", $where);
      if ($del1['errno'] <> 0) {
         $this->db(0)->query("ROLLBACK");
         echo "[FAILED] Delete 'ref' Error: " . $del1['error'] . "<br>";
         return;
      }
      echo "[OK] Deleted from 'ref'<br>";

      // 2. Delete from ORDER_DATA
      $del2 = $this->db(0)->delete_where("order_data", $where);
      if ($del2['errno'] <> 0) {
         $this->db(0)->query("ROLLBACK");
         echo "[FAILED] Delete 'order_data' Error: " . $del2['error'] . "<br>";
         return;
      }
      echo "[OK] Deleted from 'order_data'<br>";

      // 3. Delete from MASTER_MUTASI
      $del3 = $this->db(0)->delete_where("master_mutasi", $where);
      if ($del3['errno'] <> 0) {
         $this->db(0)->query("ROLLBACK");
         echo "[FAILED] Delete 'master_mutasi' Error: " . $del3['error'] . "<br>";
         return;
      }
      echo "[OK] Deleted from 'master_mutasi'<br>";

      $this->db(0)->query("COMMIT");
      echo "<strong>Transaction Completed Successfully.</strong><br>";
   }
}
