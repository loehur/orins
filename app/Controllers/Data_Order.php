<?php

class Data_Order extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[3]) && !in_array($this->userData['user_tipe'], PV::PRIV[9])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($parse, $parse_2 = 0)
   {
      $title = "";
      switch ($parse_2) {
         case 1:
            $title = "Data Order - Proses (Umum)";
            break;
         case 2:
            $title = "Data Order - Proses (R/D)";
            break;
         case 3:
            $title = "Data Order - Proses (Online)";
            break;
         case 4:
            $title = "Data Order - AFF (IN)";
            break;
         case 5:
            $title = "Data Order - AFF (OUT)";
            break;
         case 100:
            $title = "Data Order - Proses (Stok)";
            break;
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $title
      ]);

      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = 0, $parse_2 = 0)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $parse_2]);
   }

   public function content($parse = 0, $parse_2 = 0)
   {
      $data['parse'] = $parse;
      $data['parse_2'] = $parse_2;
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $where = "tuntas = 0";
      $data['data_ref'] = $this->db(0)->get_where('ref', $where, 'ref');

      switch ($parse) {
         case 0:
            //DALAM PROSES 7 HARI
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND CURDATE() <= (insertTime + INTERVAL 6 DAY) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND CURDATE() <= (insertTime + INTERVAL 6 DAY) AND pid = 0 ORDER BY id DESC";
            break;
         case 1:
            //DALAM PROSES > 7 HARI
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 6 DAY) AND CURDATE() <= (insertTime + INTERVAL 30 DAY)) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 6 DAY) AND CURDATE() <= (insertTime + INTERVAL 30 DAY)) AND pid = 0 ORDER BY id DESC";
            break;
         case 2:
            //DALAM PROSES > 30 HARI
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 30 DAY) AND CURDATE() <= (insertTime + INTERVAL 365 DAY)) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 30 DAY) AND CURDATE() <= (insertTime + INTERVAL 365 DAY)) AND pid = 0 ORDER BY id DESC";
            break;
         case 3:
            //DALAM PROSES > 1 TAHUN
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND CURDATE() > (insertTime + INTERVAL 365 DAY) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND CURDATE() > (insertTime + INTERVAL 365 DAY) AND pid = 0 ORDER BY id DESC";
            break;
      }

      switch ($parse_2) {
         case 4:
            $where = "id_afiliasi = " . $this->userData['id_toko'] . " AND id_pelanggan <> 0 AND tuntas = 0 AND id_ambil_aff = 0 AND id_ambil = 0 ORDER BY id_order_data DESC";
            break;
         case 5:
            $where = "id_afiliasi <> " . $this->userData['id_toko'] . " AND id_afiliasi <> 0 AND id_pelanggan <> 0 AND tuntas = 0 AND id_ambil_aff = 0 AND id_ambil = 0 ORDER BY id_order_data DESC";
            break;
      }

      $data['order'] = $this->db(0)->get_where('order_data', $where, 'ref', 1);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where2, 'ref', 1);

      $ref1 = array_keys($data['order']);
      $ref2 = array_keys($data['mutasi']);
      $refs = array_unique(array_merge($ref1, $ref2));

      $data['kas'] = [];
      $data['diskon'] = [];

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $cols = "ref_transaksi, SUM(jumlah) AS jumlah";
         $where = "ref_transaksi IN (" . $ref_list . ") AND status_mutasi <> 2 GROUP BY ref_transaksi";
         $data['kas'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_transaksi');
         $where = "ref_transaksi IN (" . $ref_list . ") AND cancel = 0 GROUP BY ref_transaksi";
         $data['diskon'] = $this->db(0)->get_cols_where('xtra_diskon', $cols, $where, 1, 'ref_transaksi');
         $data['charge'] = $this->db(0)->get_cols_where('charge', $cols, $where, 1, 'ref_transaksi');

         //UPDATE BELUM TUNTAS
         $set = "tuntas = 0, tuntas_date = ''";
         $where = "ref IN (" . $ref_list . ")";
         $this->db(0)->update("ref", $set, $where);
      }

      $data['refs'] = $refs;
      $this->view($this->v_content, $data);
   }

   function cancel()
   {
      $mode = $_POST['tb'];
      $id = $_POST['cancel_id'];
      $reason = $_POST['reason'];
      $karyawan = $_POST['id_karyawan'];

      if ($mode == 0) {
         $cek = $this->db(0)->get_where_row("order_data", "id_order_data = " . $id);
      } else {
         $cek = $this->db(0)->get_where_row("master_mutasi", "id = " . $id);
      }

      $plock = $cek['price_locker'];
      $pref = $cek['paket_ref'];
      $pgrup = $cek['paket_group'];
      $ref = $cek['ref'];

      $dateNow = date("Y-m-d H:i:s");

      if ($plock == 1) {
         $where = "ref = '" . $ref . "' AND paket_ref = '" . $pref . "' AND paket_group = '" . $pgrup . "'";
      }

      if ($mode == 0) {
         if ($plock == 1) {
            $set = "stat = 2";
            $update = $this->db(0)->update("master_mutasi", $set, $where);
            if ($update['errno'] <> 0) {
               echo $update['error'];
               exit();
            }
         } else {
            $where = "id_order_data = " . $id;
         }

         $set = "id_cancel = " . $karyawan . ", cancel = 1, cancel_reason = '" . $reason . "', tgl_cancel = '" . $dateNow . "'";
         $update = $this->db(0)->update("order_data", $set, $where);
         if ($update['errno'] == 0) {
            //BATALKAN MUTASI PRODUKSI
            $where2 = "pid = " . $id;
            $set2 = "stat = 2";
            $update2 = $this->db(0)->update("master_mutasi", $set2, $where2);
            if ($update2 <> 0) {
               echo $update['error'];
               exit();
            }
         } else {
            echo $update['error'];
            exit();
         }
      } else {
         if ($plock == 1) {
            $set = "id_cancel = " . $karyawan . ", cancel = 1, cancel_reason = '" . $reason . "', tgl_cancel = '" . $dateNow . "'";
            $update = $this->db(0)->update("order_data", $set, $where);
            if ($update['errno'] == 0) {
               //BATALKAN MUTASI PRODUKSI
               $where2 = "pid = " . $id;
               $set2 = "stat = 2";
               $update2 = $this->db(0)->update("master_mutasi", $set2, $where2);
               if ($update2 <> 0) {
                  echo $update['error'];
                  exit();
               }
            } else {
               echo $update['error'];
               exit();
            }
         } else {
            $where = "id = " . $id;
         }

         $set = "stat = 2";
         $update = $this->db(0)->update("master_mutasi", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }

      echo 0;
   }

   function refund()
   {
      $id = $_POST['refund_id'];
      $refund = $_POST['refund'];
      $metod = $_POST['metode'];
      $reason = $_POST['reason'];

      $cek = $this->db(0)->get_where_row("order_data", "id_order_data = " . $id);
      if ($cek['refund'] > 0) {
         echo "Transaksi sudah di refund";
         exit();
      }

      $max = $cek['harga'] * $cek['jumlah'];
      $max += $max + $cek['margin_paket'];
      $max -= $cek['diskon'];

      if ($refund > $max) {
         echo "Refund melebihi jumlah transaksi yang telah disetor!";
         exit();
      }

      $where_kas = "insertTime LIKE '" . date("Y-m-d") . "%' AND metode_mutasi = 1 AND sds = 0 AND status_mutasi <> 2 AND id_toko = '" . $this->userData['id_toko'] . "'";
      $cek_kas = $this->db(0)->sum_col_where('kas', 'jumlah', $where_kas);

      if ($refund > $cek_kas) {
         echo "Kas Toko tidak cukup!";
         exit();
      }


      $dateNow = date("Y-m-d");
      $where = "id_order_data = " . $id;

      $set = "refund = " . $refund . ", refund_metod = " . $metod . ", refund_reason = '" . $reason . "', refund_date = '" . $dateNow . "'";
      $update = $this->db(0)->update("order_data", $set, $where);
      if ($update['errno'] == 0) {
         echo $update['errno'];
      } else {
         echo $update['error'];
      }
   }

   function cancel_diskon()
   {
      if (in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         $id = $_POST['cancel_id_diskon'];
         $reason = $_POST['reason'];
         $karyawan = $this->userData['id_user'];

         $where = "id_diskon = " . $id;
         $dateNow = date("Y-m-d H:i:s");
         $set = "cancel_id = " . $karyawan . ", cancel = 1, cancel_reason = '" . $reason . "', cancel_date = '" . $dateNow . "'";
         $update = $this->db(0)->update("xtra_diskon", $set, $where);
         echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
      } else {
         echo "User Forbidden";
      }
   }

   function cancel_charge()
   {
      if (in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         $id = $_POST['cancel_id_charge'];
         $reason = $_POST['reason'];
         $karyawan = $this->userData['id_user'];

         $where = "id = " . $id;
         $dateNow = date("Y-m-d H:i:s");
         $set = "cancel_id = " . $karyawan . ", cancel = 1, cancel_reason = '" . $reason . "', cancel_date = '" . $dateNow . "'";
         $update = $this->db(0)->update("charge", $set, $where);
         echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
      } else {
         echo "User Forbidden";
         exit();
      }
   }

   function ambil()
   {
      $id = $_POST['ambil_id'];
      $id_karyawan = $_POST['id_karyawan'];
      $id_driver = $_POST['id_driver'];

      $up = $this->data('Operasi')->ambil($id, $id_karyawan, $id_driver);
      echo $up['errno'] <> 0 ? $up['error'] : 0;
   }

   function ambil_semua()
   {
      $ref = $_POST['ambil_ref'];
      $id_karyawan = $_POST['id_karyawan'];
      $id_driver = $_POST['id_driver'];

      if (isset($_POST['id_toko'])) {
         $id_toko = $_POST['id_toko'];
      } else {
         $id_toko = 0;
      }

      if (isset($_POST['mode'])) {
         $mode = $_POST['mode'];
      } else {
         $mode = 0;
      }

      if ($mode == 1) {
         $up1 = $this->db(0)->update("master_input", "id_driver = " . $id_driver, "id = '" . $ref . "'");
         if ($up1['errno'] <> 0) {
            echo $up1['error'];
            exit();
         } else {
            echo 0;
            exit();
         }
      } else {
         $up = $this->data('Operasi')->ambil_semua($ref, $id_karyawan, $id_driver, $id_toko, $mode);
         echo $up['errno'] <> 0 ? $up['error'] : 0;
      }
   }

   public function print($parse = "")
   {
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $data['barang'] = $this->db(0)->get('master_barang', 'id');
      $data['payment_account'] = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "' ORDER BY freq DESC", 'id');

      $data['parse'] = $parse;

      $where = "ref = '" . $parse . "' AND cancel = 0";
      $where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND ref = '" . $parse . "'";

      $data['order'] = [];
      $data['mutasi'] = [];

      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_mutasi);

      $where_ref = "ref = '" . $parse . "'";
      $data['mark'] = $this->db(0)->get_where_row('ref', $where_ref, 'ref')['mark'];

      $where = "ref_transaksi = '" . $parse . "' AND cancel = 0";
      $data['charge'] = $this->db(0)->get_where_row('charge', $where);

      $data['paket'] = [];
      $data['list_paket'] = $this->db(0)->get('paket_main', 'id');
      foreach ($data['order'] as $key => $do) {
         if ($do['paket_ref'] <> "") {
            if (isset($data['paket'][$do['paket_ref']]['harga'])) {
               $data['paket'][$do['paket_ref']]['harga'] += (($do['harga'] * $do['jumlah']) + $do['margin_paket']);
            } else {
               $data['paket'][$do['paket_ref']]['harga'] = (($do['harga'] * $do['jumlah']) + $do['margin_paket']);
            }
            if (!isset($data['paket'][$do['paket_ref']]['order'])) {
               $data['paket'][$do['paket_ref']]['order'] = [];
            }
            if ($do['price_locker'] == 1) {
               if (!isset($data['paket'][$do['paket_ref']]['qty'])) {
                  $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $do['paket_ref'] . "' AND price_locker = 1");
                  if (isset($get['jumlah'])) {
                     $data['paket'][$do['paket_ref']]['qty'] = $do['jumlah'] / $get['jumlah'];
                  }
               }
            }
            array_push($data['paket'][$do['paket_ref']]['order'], $do);
            unset($data['order'][$key]);
         }
      }
      foreach ($data['mutasi'] as $key => $do) {
         if ($do['paket_ref'] <> "") {
            if (isset($data['paket'][$do['paket_ref']]['harga'])) {
               $data['paket'][$do['paket_ref']]['harga'] += (($do['harga_jual'] * $do['qty']) + $do['margin_paket']);
            } else {
               $data['paket'][$do['paket_ref']]['harga'] = (($do['harga_jual'] * $do['qty']) + $do['margin_paket']);
            }
            if (!isset($data['paket'][$do['paket_ref']]['barang'])) {
               $data['paket'][$do['paket_ref']]['barang'] = [];
            }

            if ($do['price_locker'] == 1) {
               if (!isset($data['paket'][$do['paket_ref']]['qty'])) {
                  $get = $this->db(0)->get_where_row('paket_mutasi', "paket_ref = '" . $do['paket_ref'] . "' AND price_locker = 1");
                  if (isset($get['qty'])) {
                     $data['paket'][$do['paket_ref']]['qty'] = $do['qty'] / $get['qty'];
                  }
               }
            }
            array_push($data['paket'][$do['paket_ref']]['barang'], $do);
            unset($data['mutasi'][$key]);
         }
      }

      $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $parse . "'";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi = '" . $parse . "'";
      $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where);

      $this->view(__CLASS__ . "/print", $data);
   }
}
