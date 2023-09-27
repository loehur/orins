<?php

class Data_Order extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();

      if (!in_array($this->userData['user_tipe'], $this->pCS)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index($parse)
   {

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Data Order Proses"
      ]);

      $this->viewer($parse);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['parse'] = $parse;
      $data['pelanggan'] = $this->dPelangganAll;
      $data['karyawan'] = $this->model('M_DB_1')->get('karyawan');

      switch ($parse) {
         case 0:
            //DALAM PROSES 7 HARI
            $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND DATE(NOW()) <= (insertTime + INTERVAL 7 DAY) ORDER BY id_order_data DESC";
            break;
         case 1:
            //DALAM PROSES > 7 HARI
            $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND (DATE(NOW()) > (insertTime + INTERVAL 7 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 30 DAY)) ORDER BY id_order_data DESC";
            break;
         case 2:
            //DALAM PROSES > 30 HARI
            $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND (DATE(NOW()) > (insertTime + INTERVAL 30 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 365 DAY)) ORDER BY id_order_data DESC";
            break;
         case 3:
            //DALAM PROSES > 1 TAHUN
            $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 365 DAY) ORDER BY id_order_data DESC";
            break;
      }

      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);

      $refs = array_column($data['order'], 'ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN '" . $min_ref . "' AND '" . $max_ref . "')";
         $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);
      }

      $refs = array_column($data['order'], 'ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);
      }

      $data_ = [];
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $data['order'] = $data_;

      $this->view($this->v_content, $data);
   }

   function bayar()
   {
      $ref = $_POST['ref'];
      $jumlah = $_POST['jumlah'];
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

      $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND metode_mutasi = " . $method . " AND status_mutasi = 0";
      $dataCount = $this->model('M_DB_1')->count_where('kas', $whereCount);

      $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar";
      $vals = $this->userData['id_toko'] . ",1,1,'" . $ref . "'," . $method . "," . $st_mutasi . "," . $jumlah . "," . $this->userData['id_user'] . "," . $client . ",'" . $note . "','" . $ref_bayar . "'";

      if ($dataCount < 1) {
         $do = $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
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

      $where = "id_order_data = " . $id;
      $dateNow = date("Y-m-d H:i:s");
      $set = "id_ambil = " . $karyawan . ", tgl_ambil = '" . $dateNow . "'";
      $update = $this->model('M_DB_1')->update("order_data", $set, $where);
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
      $update = $this->model('M_DB_1')->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function ambil_semua()
   {
      $ref = $_POST['ambil_ref'];
      $karyawan = $_POST['id_karyawan'];

      $where = "ref = '" . $ref . "' AND id_ambil = 0";
      $dateNow = date("Y-m-d H:i:s");
      $set = "id_ambil = " . $karyawan . ", tgl_ambil = '" . $dateNow . "'";
      $update = $this->model('M_DB_1')->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   public function print($parse = "")
   {
      $data['pelanggan'] = $this->dPelangganAll;
      $data['karyawan'] = $this->model('M_DB_1')->get('karyawan');
      $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND ref = '" . $parse . "' AND cancel = 0";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);

      $refs = array_column($data['order'], 'ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND (ref_transaksi BETWEEN '" . $min_ref . "' AND '" . $max_ref . "')";
         $data['diskon'] = $this->model('M_DB_1')->get_where('xtra_diskon', $where);
      }

      $this->view($this->page . "/print", $data);
   }
}
