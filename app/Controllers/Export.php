<?php

class Export extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[8])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Audit - Data Export"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $this->view($this->v_content);
   }

   public function export()
   {
      $month = $_POST['month'];
      $delimiter = ",";
      $filename = strtoupper($this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $this->userData['id_toko'])) . "-SALES-" . $month . ".csv";
      $f = fopen('php://memory', 'w');

      $where = "insertTime LIKE '" . $month . "%' AND ref <> '' AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("order_data", $where);
      $tanggal = date("Y-m-d");

      $fields = array('TRX ID', 'NO. REFERENSI', 'TANGGAL', 'PELANGGAN', 'KODE HARGA', 'KODE BARANG', 'MAIN ORDER', 'NAMA BARANG', 'QTY', 'HARGA', 'TOTAL', 'CS', 'AFILIASI', 'STATUS', 'NOTE', 'EXPORTED');
      fputcsv($f, $fields, $delimiter);
      foreach ($data as $a) {
         $jumlah = $a['jumlah'];
         $cs = strtoupper($this->model('Arr')->get($this->dKaryawan, "id_karyawan", "nama", $a['id_penerima']));
         $pelanggan = strtoupper($this->model('Arr')->get($this->dPelanggan, "id_pelanggan", "nama", $a['id_pelanggan']));
         $afiliasi = 0;
         $afiliasi = strtoupper($this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $a['id_afiliasi']));
         $note = strtoupper($a['cancel_reason']);
         $tgl_order = substr($a['insertTime'], 0, 10);
         $main_order = strtoupper($a['produk']);

         if ($a['cancel'] == 0) {
            if ($a['tuntas'] == 1) {
               $order_status = "LUNAS";
            } else {
               if ($a['id_ambil'] == 0) {
                  $order_status = "AKTIF";
               }
               if ($a['id_ambil'] <> 0) {
                  $order_status = "PIUTANG";
               }
            }
         } else {
            $order_status = "BATAL";
         }

         $detail_harga = @unserialize($a['detail_harga']);
         if ($detail_harga !== false) {
            $detail_harga = unserialize($a['detail_harga']);
            $harga = 0;
            foreach ($detail_harga as $dh) {
               $cb = $dh['c_b'];
               $ch = $dh['c_h'];
               $nb = strtoupper($dh['n_v']);
               $harga = $dh['h'];
               $total = $harga * $jumlah;
               $lineData = array($a['id_order_data'], "R" . $a['ref'], $tgl_order, $pelanggan, $ch, $cb, $main_order, $nb, $jumlah, $harga, $total, $cs, $afiliasi, $order_status, $note, $tanggal);
               fputcsv($f, $lineData, $delimiter);
            }
         } else {
            $detail_harga = unserialize($a['produk_detail']);
            $cb = $a['produk_code'];
            $harga = $a['harga'];
            $total = $harga * $jumlah;
            $nb = "";
            foreach ($detail_harga as $dh) {
               $nb .= strtoupper($dh['detail_name']) . " ";
            }

            $nb = rtrim($nb);
            $lineData = array($a['id_order_data'], "R" . $a['ref'], $tgl_order, $pelanggan, $cb, $cb, $main_order, $nb, $jumlah, $harga, $total, $cs, $afiliasi, $order_status, $note, $tanggal);
            fputcsv($f, $lineData, $delimiter);
         }
      }

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
   }

   public function export_p()
   {
      $month = $_POST['month'];
      $delimiter = ",";
      $filename = strtoupper($this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $this->userData['id_toko'])) . "-PAYMENT-" . $month . ".csv";
      $f = fopen('php://memory', 'w');

      $where = "insertTime LIKE '" . $month . "%' AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("kas", $where);
      $tanggal = date("Y-m-d");

      $fields = array('TRX ID', 'NO. REFERENSI', 'TANGGAL', 'PELANGGAN', 'JUMLAH', 'METODE', 'NOTE', 'STATUS', 'EXPORTED');
      fputcsv($f, $fields, $delimiter);
      foreach ($data as $a) {
         $jumlah = $a['jumlah'];
         $pelanggan = "";
         $pelanggan = strtoupper($this->model('Arr')->get($this->dPelanggan, "id_pelanggan", "nama", $a['id_client']));
         $note = strtoupper($a['note']);
         $tgl_kas = substr($a['insertTime'], 0, 10);
         $method = "";
         $st = "";
         switch ($a['metode_mutasi']) {
            case 1:
               $method = "TUNAI";
               break;
            case 2:
               $method = "NON TUNAI";
               break;
            case 3:
               $method = "AFILIASI";
               break;
         }

         switch ($a['status_mutasi']) {
            case 0:
               $st = "PENGECEKAN";
               break;
            case 1:
               $st = "SUKSES";
               break;
            case 2:
               $st = "GAGAL";
               break;
         }

         $lineData = array($a['id_kas'], "R" . $a['ref_transaksi'], $tgl_kas, $pelanggan, $jumlah, $method, $note, $st, $tanggal);
         fputcsv($f, $lineData, $delimiter);
      }

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
   }
}
