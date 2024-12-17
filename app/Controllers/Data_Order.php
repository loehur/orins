<?php

class Data_Order extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[3])) {
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
      $data['pelanggan'] = $this->db(0)->get('pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan');

      switch ($parse) {
         case 0:
            //DALAM PROSES 7 HARI
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND CURDATE() <= (insertTime + INTERVAL 7 DAY)  ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND CURDATE() <= (insertTime + INTERVAL 7 DAY)  ORDER BY id DESC";
            break;
         case 1:
            //DALAM PROSES > 7 HARI
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 7 DAY) AND CURDATE() <= (insertTime + INTERVAL 30 DAY)) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 7 DAY) AND CURDATE() <= (insertTime + INTERVAL 30 DAY)) ORDER BY id DESC";
            break;
         case 2:
            //DALAM PROSES > 30 HARI
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 30 DAY) AND CURDATE() <= (insertTime + INTERVAL 365 DAY)) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND (CURDATE() > (insertTime + INTERVAL 30 DAY) AND CURDATE() <= (insertTime + INTERVAL 365 DAY)) ORDER BY id DESC";
            break;
         case 3:
            //DALAM PROSES > 1 TAHUN
            $where = "id_pelanggan_jenis = " . $parse_2 . " AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND CURDATE() > (insertTime + INTERVAL 365 DAY) ORDER BY id_order_data DESC";
            $where2 = "jenis_target = " . $parse_2 . " AND (id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND tuntas = 0 AND CURDATE() > (insertTime + INTERVAL 365 DAY) ORDER BY id DESC";
            break;
      }

      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where2);

      $refs = array_unique(array_column($data['order'], 'ref'));

      foreach ($data['mutasi'] as $key => $dm) {
         foreach ($refs as $r) {
            if ($dm['ref'] == $r) {
               unset($data['mutasi'][$key]);
            }
         }
      }


      $data['kas'] = [];
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN '" . $min_ref . "' AND '" . $max_ref . "')";
         $data['kas'] = $this->db(0)->get_where('kas', $where);
      }

      $refs = array_column($data['order'], 'ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where);
      }

      $data_ = [];
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $datam_ = [];
      foreach ($data['mutasi'] as $key => $do) {
         $datam_[$do['ref']][$key] = $do;
      }

      $data['order'] = $data_;
      $data['mutasi'] = $datam_;

      $this->view($this->v_content, $data);
   }

   function bayar()
   {
      $ref = $_POST['ref'];

      $jumlah = $_POST['jumlah'];

      $dibayar = $jumlah;
      $kembalian = $_POST['kembalian'];

      $bill = $_POST['bill'];
      $method = $_POST['method'];
      $client = $_POST['client'];
      $note = $_POST['note'];
      $st_mutasi = 1;

      $ref_bayar = date("Ymdhis") . rand(0, 9);

      if ($jumlah > $bill) {
         $jumlah = $bill;
      }

      if ($method == 2) {
         if (strlen($note) == 0) {
            $note = "Non Tunai";
         }
         $st_mutasi = 0;
      }

      if ($method == 3) {
         if (strlen($note) == 0) {
            $note = "Afiliasi";
         }
         $st_mutasi = 0;
      }

      if ($method == 4) {
         $saldo = $this->data('Saldo')->deposit($client);
         if ($jumlah > $saldo) {
            $jumlah = $saldo;
         }
      }


      $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND metode_mutasi = " . $method . " AND status_mutasi = 0";
      $dataCount = $this->db(0)->count_where('kas', $whereCount);

      $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar, bayar, kembali";
      $vals = $this->userData['id_toko'] . ",1,1,'" . $ref . "'," . $method . "," . $st_mutasi . "," . $jumlah . "," . $this->userData['id_user'] . "," . $client . ",'" . $note . "','" . $ref_bayar . "'," . $dibayar . "," . $kembalian;

      if ($dataCount < 1) {
         $do = $this->db(0)->insertCols('kas', $cols, $vals);
         if ($do['errno'] == 0) {
            echo $do['errno'];
            $this->model('Log')->write($this->userData['user'] . " Bayar Success!");
         } else {
            echo $do['error'];
         }
      }
   }

   function ambil()
   {
      $id = $_POST['ambil_id'];
      $karyawan = $_POST['id_karyawan'];
      //updateFreqCS
      $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $karyawan);

      $where = "id_order_data = " . $id;
      $dateNow = date("Y-m-d H:i:s");
      $set = "id_ambil = " . $karyawan . ", tgl_ambil = '" . $dateNow . "'";
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function cancel()
   {
      $id = $_POST['cancel_id'];
      $reason = $_POST['reason'];
      $karyawan = $_POST['id_karyawan'];

      $where = "id_order_data = " . $id;
      $dateNow = date("Y-m-d H:i:s");
      $set = "id_cancel = " . $karyawan . ", cancel = 1, cancel_reason = '" . $reason . "', tgl_cancel = '" . $dateNow . "'";
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
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

   function ambil_semua()
   {
      $ref = $_POST['ambil_ref'];
      $karyawan = $_POST['id_karyawan'];
      //updateFreqCS
      $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $karyawan);

      $where = "ref = '" . $ref . "' AND id_ambil = 0";
      $dateNow = date("Y-m-d H:i:s");
      $set = "id_ambil = " . $karyawan . ", tgl_ambil = '" . $dateNow . "'";
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   public function print($parse = "")
   {
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $data['barang'] = $this->db(0)->get('master_barang', 'code');
      $data['parse'] = $parse;

      $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND ref = '" . $parse . "' AND cancel = 0";
      $where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND ref = '" . $parse . "'";

      $data['order'] = [];
      $data['mutasi'] = [];
      if ($parse <> "" && $parse <> 0) {
         $data['order'] = $this->db(0)->get_where('order_data', $where);
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_mutasi);
      }

      $data['paket'] = [];
      $data['list_paket'] = $this->db(0)->get_where('paket_main', 'id_toko = ' . $this->userData['id_toko'], 'id');
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
            array_push($data['paket'][$do['paket_ref']]['barang'], $do);
            unset($data['mutasi'][$key]);
         }
      }


      $ref1 = array_unique(array_column($data['order'], 'ref'));
      $ref2 = array_unique(array_column($data['mutasi'], 'ref'));
      $refs = array_unique(array_merge($ref1, $ref2));

      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND (ref_transaksi BETWEEN '" . $min_ref . "' AND '" . $max_ref . "')";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where);
      }

      $this->view(__CLASS__ . "/print", $data);
   }
}
