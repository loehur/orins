<?php

class Export extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[8]) && !in_array($this->userData['user_tipe'], PV::PRIV[6])) {
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
      $filename = strtoupper($this->dToko[$this->userData['id_toko']]['nama_toko']) . "-PRODUCTION-SALES-" . $month . ".csv";
      $f = fopen('php://memory', 'w');

      $where = "insertTime LIKE '" . $month . "%' AND ref <> '' AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("order_data", $where);
      $tanggal = date("Y-m-d");

      $where = "insertTime LIKE '" . $month . "%'";
      $ref_data = $this->db(0)->get_where('ref', $where, 'ref');

      $dPelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $pj = $this->db(0)->get('pelanggan_jenis', 'id_pelanggan_jenis');

      $fields = array('TRX ID', 'NO. REFERENSI', 'FP', 'TANGGAL', 'JENIS', 'PELANGGAN', 'MARK', 'KODE BARANG', 'PRODUK/PAKET', 'KODE MYOB', 'DETAIL BARANG', 'SERIAL NUMBER', 'QTY', 'HARGA', 'DISKON', 'MARGIN_PAKET', 'TOTAL', 'CS', 'AFF/STORE', 'STATUS', 'NOTE', 'EXPORTED');
      fputcsv($f, $fields, $delimiter);
      foreach ($data as $a) {
         $jumlah = $a['jumlah'];
         $ref = $a['ref'];
         $diskon = $a['diskon'];
         $margin_paket = $a['margin_paket'];
         $jenis = strtoupper($pj[$a['id_pelanggan_jenis']]['pelanggan_jenis']);

         $cs = strtoupper($this->dKaryawanAll[$a['id_penerima']]['nama']);
         $pelanggan = strtoupper($dPelanggan[$a['id_pelanggan']]['nama']);

         if ($a['id_afiliasi'] <> 0) {
            $afiliasi = strtoupper($this->dToko[$a['id_afiliasi']]['nama_toko']);
         } else {
            $afiliasi = "";
         }
         $note = strtoupper($a['cancel_reason']);


         if (!isset($tgl_order[$ref])) {
            $tgl_order[$ref] = substr($a['insertTime'], 0, 10);
         }

         $main_order = strtoupper($a['produk']);

         if ($a['cancel'] == 0) {
            if ($a['tuntas'] == 1) {
               $order_status = "LUNAS " . substr($a['tuntas_date'], 0, 10);
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

         if (isset($ref_data[$a['ref']]['mark'])) {
            $mark = strtoupper($ref_data[$a['ref']]['mark']);
         } else {
            $where = "ref = '" . $a['ref'] . "'";
            $get_ref = $this->db(0)->get_where_row('ref', $where);
            if (isset($get_ref['mark'])) {
               $mark = $get_ref['mark'];
            } else {
               $mark = "";
            }
         }

         $detail_harga = @unserialize($a['detail_harga']);
         if ($detail_harga !== false) {
            $detail_harga = unserialize($a['detail_harga']);
            $harga = 0;
            foreach ($detail_harga as $dh) {
               $cb = $dh['c_b'];
               $cb = str_replace(['-', '&', '#'], '', $cb);
               $ch = $dh['c_h'];
               $ch = str_replace(['-', '&', '#'], '', $ch);
               $nb = strtoupper($dh['n_v']);
               $harga = $dh['h'];
               $total = $harga * $jumlah;
               $lineData = array($a['id_order_data'], "R" . $ref, 'NO', $tgl_order[$ref], $jenis, $pelanggan, $mark, $cb, $main_order, '', $nb, '', $jumlah, $harga, $diskon, $margin_paket, $total, $cs, $afiliasi, $order_status, $note, $tanggal);
               fputcsv($f, $lineData, $delimiter);
            }
         } else {
            $detail_harga = unserialize($a['produk_detail']);
            $cb = $a['produk_code'];
            $harga = $a['harga'];
            $total = ($harga * $jumlah) - $diskon;
            $nb = "";
            foreach ($detail_harga as $dh) {
               $nb .= strtoupper($dh['detail_name']) . " ";
            }

            $nb = rtrim($nb);
            $lineData = array($a['id_order_data'], "R" . $ref, 'NO', $tgl_order[$ref], $jenis, $pelanggan, $mark, $cb, $main_order, '', $nb, '', $jumlah, $harga, $diskon, $margin_paket, $total, $cs, $afiliasi, $order_status, $note, $tanggal);
            fputcsv($f, $lineData, $delimiter);
         }
      }

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
   }

   public function export_pbarang()
   {
      $month = $_POST['month'];
      $delimiter = ",";
      $filename = strtoupper($this->dToko[$this->userData['id_toko']]['nama_toko']) . "-ITEM-SALES-" . $month . ".csv";
      $f = fopen('php://memory', 'w');

      $dPelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $dBarang = $this->db(0)->get_where('master_barang', "en = 1", 'id');

      $where = "insertTime LIKE '" . $month . "%' AND ref <> '' AND id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND stat = 1";
      $data = $this->db(0)->get_where("master_mutasi", $where);

      $where = "insertTime LIKE '" . $month . "%'";
      $ref_data = $this->db(0)->get_where('ref', $where, 'ref');

      $pj = $this->db(0)->get('pelanggan_jenis', 'id_pelanggan_jenis');

      $tanggal = date("Y-m-d");

      $fields = array('TRX ID', 'NO. REFERENSI', 'FP', 'TANGGAL', 'JENIS', 'PELANGGAN', 'MARK', 'KODE BARANG', 'PRODUK/PAKET', 'KODE MYOB', 'DETAIL BARANG', 'SERIAL NUMBER', 'QTY', 'HARGA', 'DISKON', 'MARGIN_PAKET', 'TOTAL', 'CS', 'STORE', 'STATUS', 'NOTE', 'EXPORTED');
      fputcsv($f, $fields, $delimiter);

      foreach ($data as $a) {
         $jumlah = $a['qty'];
         $ref = $a['ref'];
         $diskon = $a['diskon'] * $jumlah;
         $margin_paket = $a['margin_paket'];
         $fp = $a['fp'] == 0 ? "NO" : "YA";
         $jenis = strtoupper($pj[$a['jenis_target']]['pelanggan_jenis']);

         $db = $dBarang[$a['id_barang']];
         $barang = strtoupper($db['product_name'] . $db['brand'] . " " . $db['model']);

         if ($a['stat'] <> 2) {
            if ($a['tuntas'] == 1) {
               $order_status = "LUNAS " . substr($a['tuntas_date'], 0, 10);
            } else {
               $order_status = "PIUTANG";
            }
         } else {
            $order_status = "BATAL";
         }

         $store = $a['sds'] == 1 ? "SDS" : $this->dToko[$this->userData['id_toko']]['inisial'];
         $cs = strtoupper($this->dKaryawanAll[$a['cs_id']]['nama']);
         $pelanggan = strtoupper($dPelanggan[$a['id_target']]['nama']);

         if (!isset($tgl_order[$ref])) {
            $tgl_order[$ref] = substr($a['insertTime'], 0, 10);
         }

         $harga = $a['harga_jual'];
         $total = ($harga * $jumlah) - $diskon;

         if (isset($ref_data[$a['ref']]['mark'])) {
            $mark = strtoupper($ref_data[$a['ref']]['mark']);
         } else {
            $where = "ref = '" . $a['ref'] . "'";
            $get_ref = $this->db(0)->get_where_row('ref', $where);
            if (isset($get_ref['mark'])) {
               $mark = $get_ref['mark'];
            } else {
               $mark = "";
            }
         }

         $lineData = array($a['id'], "R" . $ref, $fp, $tgl_order[$ref], $jenis, $pelanggan, $mark, $db['code'], $a['paket_ref'], $db['code_myob'], $barang, $a['sn'], $jumlah, $harga, $diskon, $margin_paket, $total, $cs, $store, $order_status, '', $tanggal);
         fputcsv($f, $lineData, $delimiter);
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

      $where = "insertTime LIKE '" . $month . "%' AND jenis_transaksi = 1 AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("kas", $where);
      $tanggal = date("Y-m-d");

      $where = "insertTime LIKE '" . $month . "%'";
      $ref_data = $this->db(0)->get_where('ref', $where, 'ref');

      $pacc = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "' ORDER BY freq DESC", 'id');

      $fields = array('TRX ID', 'NO. REFERENSI', 'TANGGAL', 'PELANGGAN', 'MARK', 'JUMLAH', 'METODE', 'PAYMENT_ACCOUNT', 'NOTE', 'STATUS', 'EXPORTED');
      fputcsv($f, $fields, $delimiter);
      foreach ($data as $a) {
         if (isset($pacc[$a['pa']]['payment_account'])) {
            $payment_account = strtoupper($pacc[$a['pa']]['payment_account']) . " ";
         } else {
            $payment_account = "";
         }

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

         if (isset($ref_data[$a['ref_transaksi']]['mark'])) {
            $mark = strtoupper($ref_data[$a['ref_transaksi']]['mark']);
         } else {
            $where = "ref = '" . $a['ref_transaksi'] . "'";
            $get_ref = $this->db(0)->get_where_row('ref', $where);
            if (isset($get_ref['mark'])) {
               $mark = $get_ref['mark'];
            } else {
               $mark = "";
            }
         }

         $lineData = array($a['id_kas'], "R" . $a['ref_transaksi'], $tgl_kas, $pelanggan, $mark, $jumlah, $method, $payment_account, $note, $st, $tanggal);
         fputcsv($f, $lineData, $delimiter);
      }

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
   }
}
