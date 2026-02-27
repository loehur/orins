<?php

class Export extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[107])) {
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
      list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
      $filename = strtoupper($this->dToko[$this->userData['id_toko']]['nama_toko']) . "-PRODUCTION-SALES-" . $periodLabel . ".xlsx";

      $where = "paket_group = '' AND insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND ref <> '' AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("order_data", $where);
      $tanggal = date("Y-m-d");

      $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
      $ref_data = $this->db(0)->get_where('ref', $where, 'ref');
      $ref_data = $this->ensureRefDataComplete($ref_data, array_column($data, 'ref'));

      $dPelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $dKaryawan = $this->db(0)->get('karyawan', 'id_karyawan');
      $pj = $this->db(0)->get('pelanggan_jenis', 'id_pelanggan_jenis');
      $rows = [];
      $rows[] = array('TRX_ID', 'NO_REFERENSI', 'FP', 'TANGGAL', 'JENIS', 'PELANGGAN', 'MARK', 'KODE_BARANG', 'PRODUK', 'KODE_MYOB', 'DETAIL_BARANG', 'SERIAL_NUMBER', 'QTY', 'HARGA', 'DISKON', 'TOTAL', 'CS', 'AFF/STORE', 'STATUS', 'NOTE', 'EXPORTED');
      foreach ($data as $a) {
         $jumlah = $a['jumlah'];
         $ref = $a['ref'];
         $diskon = $a['diskon'];
         $jenis = strtoupper($pj[$a['id_pelanggan_jenis']]['pelanggan_jenis']);

         if (isset($dKaryawan[$a['id_penerima']]['nama'])) {
            $cs = strtoupper($dKaryawan[$a['id_penerima']]['nama']);
         } else {
            $cs = $a['id_penerima'];
         }

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

         $mark = $this->getMark($a['ref'], $ref_data);

         $detail_harga = @unserialize($a['detail_harga']);
         if ($detail_harga !== false) {
            $harga = 0;
            foreach ($detail_harga as $dh) {
               $cb = $dh['c_b'];
               $cb = str_replace(['-', '&', '#'], '', $cb);
               $ch = $dh['c_h'];
               $ch = str_replace(['-', '&', '#'], '', $ch);
               $nb = strtoupper($dh['n_v']);
               $harga = $dh['h'];
               $total = ($harga * $jumlah) - $diskon;
               $rows[] = array($a['id_order_data'], "R" . $ref, 'NO', $tgl_order[$ref], $jenis, $pelanggan, $mark, $cb, $main_order, '', $nb, '', $jumlah, $harga, $diskon, $total, $cs, $afiliasi, $order_status, $note, $tanggal);
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
            $rows[] = array($a['id_order_data'], "R" . $ref, 'NO', $tgl_order[$ref], $jenis, $pelanggan, $mark, $cb, $main_order, '', $nb, '', $jumlah, $harga, $diskon, $total, $cs, $afiliasi, $order_status, $note, $tanggal);
         }
      }
      $this->output_xlsx($filename, $rows);
   }

   public function export_pbarang()
   {
      list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
      $filename = strtoupper($this->dToko[$this->userData['id_toko']]['nama_toko']) . "-ITEM-SALES-" . $periodLabel . ".xlsx";

      $dPelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $dBarang = $this->db(0)->get('master_barang', 'id');

      $where = "paket_group = '' AND insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND ref <> '' AND id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND stat = 1";
      $data = $this->db(0)->get_where("master_mutasi", $where);

      $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
      $ref_data = $this->db(0)->get_where('ref', $where, 'ref');
      $ref_data = $this->ensureRefDataComplete($ref_data, array_column($data, 'ref'));

      $pj = $this->db(0)->get('pelanggan_jenis', 'id_pelanggan_jenis');
      $dKaryawan = $this->db(0)->get('karyawan', 'id_karyawan');

      $tanggal = date("Y-m-d");

      $rows = [];
      $rows[] = array('TRX_ID', 'NO_REFERENSI', 'FP', 'TANGGAL', 'JENIS', 'PELANGGAN', 'MARK', 'KODE_BARANG', 'PRODUK', 'KODE_MYOB', 'DETAIL_BARANG', 'SERIAL_NUMBER', 'QTY', 'HARGA', 'DISKON', 'TOTAL', 'CS', 'AFF/STORE', 'STATUS', 'NOTE', 'EXPORTED');

      foreach ($data as $a) {
         $jumlah = $a['qty'];
         $ref = $a['ref'];
         $diskon = $a['diskon'] * $jumlah;
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


         if (isset($dKaryawan[$a['cs_id']]['nama'])) {
            $cs = strtoupper($dKaryawan[$a['cs_id']]['nama']);
         } else {
            $cs = $a['cs_id'];
         }

         $store = $a['sds'] == 1 ? "SDS" : $this->dToko[$this->userData['id_toko']]['inisial'];

         $pelanggan = strtoupper($dPelanggan[$a['id_target']]['nama']);

         if (!isset($tgl_order[$ref])) {
            $tgl_order[$ref] = substr($a['insertTime'], 0, 10);
         }

         $harga = $a['harga_jual'];
         $total = ($harga * $jumlah) - $diskon;
         $mark = $this->getMark($a['ref'], $ref_data);

         $rows[] = array($a['id'], "R" . $ref, $fp, $tgl_order[$ref], $jenis, $pelanggan, $mark, $db['code'], '', $db['code_myob'], $barang, $a['sn'], $jumlah, $harga, $diskon, $total, $cs, $store, $order_status, '', $tanggal);
      }
      $this->output_xlsx($filename, $rows);
   }

   public function export_paket()
   {
      // Clear any existing output buffer
      while (ob_get_level() > 0) {
         ob_end_clean();
      }
      ob_start();

      try {
         $this->model('Log')->write("export_paket started", "Export");

         list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
         $lineData = [];
         $filename = strtoupper($this->dToko[$this->userData['id_toko']]['nama_toko']) . "-BUNDLE-SALES-" . $periodLabel . ".xlsx";

         $tanggal = date("Y-m-d");

         $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
         $ref_data = $this->db(0)->get_where('ref', $where, 'ref');

         $dPelanggan = $this->db(0)->get('pelanggan', 'id_pelanggan');
         $dKaryawan = $this->db(0)->get('karyawan', 'id_karyawan');
         $pj = $this->db(0)->get('pelanggan_jenis', 'id_pelanggan_jenis');
         $paket = $this->db(0)->get('paket_main', "id");

         $rows = [];
         $sumPaket = [];
         $tgl_order = [];
         $rows[] = array('TRX_ID', 'NO_REFERENSI', 'FP', 'TANGGAL', 'JENIS', 'PELANGGAN', 'MARK', 'KODE_BARANG', 'PRODUK', 'KODE_MYOB', 'DETAIL_BARANG', 'SERIAL_NUMBER', 'QTY', 'HARGA', 'DISKON', 'TOTAL', 'CS', 'AFF/STORE', 'STATUS', 'NOTE', 'EXPORTED');

         $where = "paket_group <> '' AND price_locker = 1 AND insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND ref <> '' AND id_toko = " . $this->userData['id_toko'];
         $data = $this->db(0)->get_where("order_data", $where);
         $ref_data = $this->ensureRefDataComplete($ref_data, array_column($data, 'ref'));

         foreach ($data as $a) {
            $jumlah = $a['jumlah'];
            $ref = $a['ref'];
            $diskon = $a['diskon'];
            $harga_paket = $a['harga_paket'];
            $paket_ref = $a['paket_ref'];
            $paket_group = $a['paket_group'];

            if (!isset($sumPaket[$paket_group])) {
               $sumPaket[$paket_group] = 0;
            }

            $nama_paket = isset($paket[$paket_ref]['nama']) ? $paket[$paket_ref]['nama'] : '';
            $jenis = isset($pj[$a['id_pelanggan_jenis']]['pelanggan_jenis']) ? strtoupper($pj[$a['id_pelanggan_jenis']]['pelanggan_jenis']) : '';

            if (isset($dKaryawan[$a['id_penerima']]['nama'])) {
               $cs = strtoupper($dKaryawan[$a['id_penerima']]['nama']);
            } else {
               $cs = $a['id_penerima'];
            }

            $pelanggan = isset($dPelanggan[$a['id_pelanggan']]['nama']) ? strtoupper($dPelanggan[$a['id_pelanggan']]['nama']) : '';

            if ($a['id_afiliasi'] <> 0 && isset($this->dToko[$a['id_afiliasi']]['nama_toko'])) {
               $afiliasi = strtoupper($this->dToko[$a['id_afiliasi']]['nama_toko']);
            } else {
               $afiliasi = "";
            }
            $note = strtoupper($a['cancel_reason'] ?? '');


            if (!isset($tgl_order[$ref])) {
               $tgl_order[$ref] = substr($a['insertTime'], 0, 10);
            }

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

            $mark = $this->getMark($a['ref'], $ref_data);

            $lineData["1" . $a['id_order_data']] = array($a['id_order_data'], "R" . $ref, 0, $tgl_order[$ref], $jenis, $pelanggan, $mark, $paket_ref, 'PAKET', '', $nama_paket, $paket_group, $a['paket_qty'], $harga_paket, 0, $harga_paket * $a['paket_qty'], $cs, $afiliasi, $order_status, $note, $tanggal);
         }

         $dBarang = $this->db(0)->get('master_barang', 'id');
         $where = "paket_group <> '' AND price_locker = 1 AND insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND ref <> '' AND id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND stat = 1";
         $data2 = $this->db(0)->get_where("master_mutasi", $where);
         $ref_data = $this->ensureRefDataComplete($ref_data, array_column($data2, 'ref'));

         foreach ($data2 as $a) {
            $jumlah = $a['paket_qty'];
            $ref = $a['ref'];
            $diskon = $a['diskon'] * $jumlah;
            $jenis = isset($pj[$a['jenis_target']]['pelanggan_jenis']) ? strtoupper($pj[$a['jenis_target']]['pelanggan_jenis']) : '';
            $db = isset($dBarang[$a['id_barang']]) ? $dBarang[$a['id_barang']] : [];
            $barang = isset($db['product_name']) ? strtoupper(($db['product_name'] ?? '') . ($db['brand'] ?? '') . " " . ($db['model'] ?? '')) : '';

            $paket_group = $a['paket_group'];
            $paket_ref = $a['paket_ref'];
            $nama_paket = isset($paket[$paket_ref]['nama']) ? $paket[$paket_ref]['nama'] : '';

            if (!isset($sumPaket[$paket_group])) {
               $sumPaket[$paket_group] = 0;
            }

            if ($a['stat'] <> 2) {
               if ($a['tuntas'] == 1) {
                  $order_status = "LUNAS " . substr($a['tuntas_date'] ?? '', 0, 10);
               } else {
                  $order_status = "PIUTANG";
               }
            } else {
               $order_status = "BATAL";
            }

            if (isset($dKaryawan[$a['cs_id']]['nama'])) {
               $cs = strtoupper($dKaryawan[$a['cs_id']]['nama']);
            } else {
               $cs = $a['cs_id'];
            }

            $store = $a['sds'] == 1 ? "SDS" : (isset($this->dToko[$this->userData['id_toko']]['inisial']) ? $this->dToko[$this->userData['id_toko']]['inisial'] : '');

            $pelanggan = isset($dPelanggan[$a['id_target']]['nama']) ? strtoupper($dPelanggan[$a['id_target']]['nama']) : '';

            if (!isset($tgl_order[$ref])) {
               $tgl_order[$ref] = substr($a['insertTime'], 0, 10);
            }

            $harga = $a['harga_paket'];
            $total = ($harga * $jumlah) - $diskon;
            $harga_paket = $a['harga_paket'];
            $mark = $this->getMark($a['ref'], $ref_data);

            $sumPaket[$paket_group] += ($total + $harga_paket);
            //'TRX_ID', 'NO_REFERENSI', 'TANGGAL', 'JENIS', 'PELANGGAN', 'MARK', 'KODE_BARANG', 'PRODUK', 'PAKET', 'PAKET_REF', 'DETAIL_BARANG', 'SERIAL_NUMBER', 'QTY', 'SUBTOTAL', 'TOTAL', 'CS', 'AFF/STORE', 'STATUS', 'NOTE', 'EXPORTED'
            $lineData["2" . $a['id']] = array($a['id'], "R" . $ref, 0, $tgl_order[$ref], $jenis, $pelanggan, $mark, $db['code'] ?? '', 'PAKET', $db['code_myob'] ?? '', $nama_paket, $paket_group, $jumlah, $harga_paket, $diskon, $total, $cs, $store, $order_status, $a['note'] ?? '', $tanggal);
         }

         foreach ($lineData as $key => $ld) {
            $paket_group = $ld[9];
            $ld[14] = isset($sumPaket[$paket_group]) ? $sumPaket[$paket_group] : 0;
            $rows[] = $ld;
            if (isset($sumPaket[$paket_group])) {
               $sumPaket[$paket_group] = 0;
            }
         }

         // Check for any unexpected output before writing Excel
         $bufferedOutput = ob_get_contents();
         if (!empty($bufferedOutput)) {
            $this->model('Log')->write("UNEXPECTED OUTPUT BEFORE EXCEL: " . substr($bufferedOutput, 0, 500), "Export");
         }
         ob_end_clean();

         $this->model('Log')->write("export_paket finished, rows: " . count($rows), "Export");
         $this->output_xlsx($filename, $rows);

      } catch (Exception $e) {
         $this->model('Log')->write("export_paket ERROR: " . $e->getMessage() . " at line " . $e->getLine(), "Export");
         echo "Export Error: " . $e->getMessage();
         exit;
      }
   }

   private static $METODE_MUTASI = [1 => 'TUNAI', 2 => 'NON TUNAI', 3 => 'AFILIASI'];
   private static $STATUS_MUTASI = [0 => 'PENGECEKAN', 1 => 'SUKSES', 2 => 'GAGAL'];
   private static $CANCEL_STATUS = [0 => 'SUKSES', 1 => 'CANCEL'];
   private static $PETYCASH_STATUS = [0 => 'CHECKING', 1 => 'CONFIRMED', 2 => 'REJECTED'];

   public function export_p()
   {
      list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
      $filename = strtoupper($this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $this->userData['id_toko'])) . "-PAYMENT-" . $periodLabel . ".xlsx";

      $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND jenis_transaksi = 1 AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("kas", $where);
      $tanggal = date("Y-m-d");

      $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
      $ref_data = $this->db(0)->get_where('ref', $where, 'ref');
      $ref_data = $this->ensureRefDataComplete($ref_data, array_column($data, 'ref_transaksi'));

      $pacc = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "' ORDER BY freq DESC", 'id');

      $rows = [];
      $rows[] = array('TRX_ID', 'NO_REFERENSI', 'TANGGAL', 'PELANGGAN', 'MARK', 'JUMLAH', 'METODE', 'PAYMENT_ACCOUNT', 'NOTE', 'STATUS', 'EXPORTED');
      foreach ($data as $a) {
         $payment_account = isset($pacc[$a['pa']]['payment_account']) ? strtoupper($pacc[$a['pa']]['payment_account']) . " " : "";
         $pelanggan = strtoupper($this->model('Arr')->get($this->dPelanggan, "id_pelanggan", "nama", $a['id_client']));
         $method = self::$METODE_MUTASI[$a['metode_mutasi']] ?? "";
         $st = self::$STATUS_MUTASI[$a['status_mutasi']] ?? "";
         $mark = $this->getMark($a['ref_transaksi'], $ref_data);

         $rows[] = array($a['id_kas'], "R" . $a['ref_transaksi'], substr($a['insertTime'], 0, 10), $pelanggan, $mark, $a['jumlah'], $method, $payment_account, strtoupper($a['note']), $st, $tanggal);
      }
      $this->output_xlsx($filename, $rows);
   }

   public function export_ed() //EXTRA DISKON
   {
      list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
      $filename = strtoupper($this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $this->userData['id_toko'])) . "-EXTRADISKON-" . $periodLabel . ".xlsx";

      $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("xtra_diskon", $where);
      $tanggal = date("Y-m-d");

      $rows = [];
      $rows[] = array('TRX_ID', 'NO_REFERENSI', 'TANGGAL', 'JUMLAH', 'DISKON_NOTE', 'STATUS', 'STATUS_NOTE', 'EXPORTED');
      foreach ($data as $a) {
         $st = self::$CANCEL_STATUS[$a['cancel']] ?? 'UNDEFINED';
         $rows[] = array($a['id_diskon'], "R" . $a['ref_transaksi'], substr($a['insertTime'], 0, 10), $a['jumlah'], strtoupper($a['note']), $st, strtoupper($a['cancel_reason']), $tanggal);
      }
      $this->output_xlsx($filename, $rows);
   }

   public function export_sc() //surcharge
   {
      list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
      $filename = strtoupper($this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $this->userData['id_toko'])) . "-SURCHARGE-" . $periodLabel . ".xlsx";

      $where = "insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' AND id_toko = " . $this->userData['id_toko'];
      $data = $this->db(0)->get_where("charge", $where);
      $tanggal = date("Y-m-d");

      $rows = [];
      $rows[] = array('TRX_ID', 'NO_REFERENSI', 'TANGGAL', 'JUMLAH', 'CHARGE_NOTE', 'STATUS', 'STATUS_NOTE', 'EXPORTED');
      foreach ($data as $a) {
         $st = self::$CANCEL_STATUS[$a['cancel']] ?? 'UNDEFINED';
         $rows[] = array($a['id_diskon'], "R" . $a['ref_transaksi'], substr($a['insertTime'], 0, 10), $a['jumlah'], strtoupper($a['note']), $st, strtoupper($a['cancel_reason']), $tanggal);
      }
      $this->output_xlsx($filename, $rows);
   }

   public function export_pc() //PETYCASH
   {
      list($date_from, $date_to, $periodLabel, $startTime, $endTime) = $this->getPeriodParams();
      $filename = strtoupper($this->dToko[$this->userData['id_toko']]["nama_toko"]) . "-PETTYCASH-" . $periodLabel . ".xlsx";

      $pj = $this->db(0)->get('pengeluaran_jenis', 'id');
      $where = "id_sumber = " . $this->userData['id_toko'] . " AND tipe = 2 AND insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
      $data = $this->db(0)->get_where("kas_kecil", $where);
      $tanggal = date("Y-m-d");

      $rows = [];
      $rows[] = array('TRX_ID', 'INSERT_DATE', 'TRX_DATE', 'JENIS', 'KETERANGAN', 'JUMLAH', 'STATUS', 'EXPORTED');
      foreach ($data as $a) {
         $jenis = $pj[$a['id_target']]['nama'] ?? $a['id_target'];
         $st = self::$PETYCASH_STATUS[$a['st']] ?? 'UNDEFINED';
         $rows[] = array($a['id'], substr($a['insertTime'], 0, 10), $a['tanggal'], strtoupper($jenis), strtoupper($a['note']), $a['jumlah'], $st, $tanggal);
      }
      $this->output_xlsx($filename, $rows);
   }

   private function getPeriod()
   {
      $df = isset($_POST['date_from']) ? $_POST['date_from'] : '';
      $dt = isset($_POST['date_to']) ? $_POST['date_to'] : '';
      if ($df == '' || $dt == '') {
         echo "Periode tidak lengkap";
         exit();
      }
      $tsf = strtotime($df);
      $tst = strtotime($dt);
      if ($tsf === false || $tst === false) {
         echo "Format tanggal tidak valid";
         exit();
      }
      if ($tsf > $tst) {
         echo "Tanggal From melewati Date To";
         exit();
      }
      $days = ($tst - $tsf) / 86400;
      if ($days > 92) {
         echo "Maksimal periode 3 bulan (92 hari)";
         exit();
      }
      return [$df, $dt];
   }

   /** Returns [date_from, date_to, periodLabel, startTime, endTime] */
   private function getPeriodParams()
   {
      list($date_from, $date_to) = $this->getPeriod();
      return [
         $date_from,
         $date_to,
         $date_from . "_to_" . $date_to,
         $date_from . " 00:00:00",
         $date_to . " 23:59:59"
      ];
   }

   /** Batch-fetch missing refs to avoid N+1 queries. Returns merged ref_data. */
   private function ensureRefDataComplete(array $ref_data, array $refsFromData)
   {
      $missingRefs = array_diff(array_unique(array_filter($refsFromData)), array_keys($ref_data));
      if (empty($missingRefs)) {
         return $ref_data;
      }
      $escaped = array_map(function ($r) {
         return "'" . addslashes((string) $r) . "'";
      }, $missingRefs);
      $where = "ref IN (" . implode(",", $escaped) . ")";
      $extra = $this->db(0)->get_where('ref', $where, 'ref');
      return $ref_data + (is_array($extra) ? $extra : []);
   }

   private function getMark($ref, array $ref_data)
   {
      return isset($ref_data[$ref]['mark']) ? strtoupper($ref_data[$ref]['mark']) : '';
   }

   private function output_xlsx($filename, $rows, $sheetName = 'Sheet1')
   {
      $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setTitle($sheetName);

      // Identify which columns should be stored as text based on header names
      $codeColumns = [];  // Will get 'C' prefix
      $idColumns = [];    // Will get 'T' prefix
      $snColumns = [];    // Will get 'SN' prefix
      $detailColumns = []; // Will get 'G' prefix if starts with number
      if (!empty($rows[0])) {
         $colNum = 1;
         foreach ($rows[0] as $header) {
            $headerUpper = strtoupper((string) $header);
            // Code columns get 'C' prefix
            if (
               strpos($headerUpper, 'KODE') !== false ||
               strpos($headerUpper, 'CODE') !== false
            ) {
               $codeColumns[$colNum] = true;
            }
            // Serial columns get 'SN' prefix
            if (strpos($headerUpper, 'SERIAL') !== false) {
               $snColumns[$colNum] = true;
            }
            // ID columns get 'T' prefix
            if (
               strpos($headerUpper, 'TRX_ID') !== false ||
               strpos($headerUpper, 'NO_REF') !== false
            ) {
               $idColumns[$colNum] = true;
            }
            // Detail columns get 'G' prefix if starts with number
            if (strpos($headerUpper, 'DETAIL_BARANG') !== false) {
               $detailColumns[$colNum] = true;
            }
            $colNum++;
         }
      }

      $rowNum = 1;
      foreach ($rows as $row) {
         $colNum = 1;
         foreach ($row as $val) {
            // Code columns get 'C' prefix
            if (isset($codeColumns[$colNum]) && $rowNum > 1 && is_numeric($val) && $val !== '') {
               $sheet->setCellValue([$colNum, $rowNum], 'C' . $val);
            }
            // Serial columns get 'SN' prefix
            elseif (isset($snColumns[$colNum]) && $rowNum > 1 && $val !== '' && $val !== null) {
               $sheet->setCellValue([$colNum, $rowNum], 'SN' . $val);
            }
            // ID columns get 'T' prefix
            elseif (isset($idColumns[$colNum]) && $rowNum > 1 && is_numeric($val) && $val !== '') {
               $sheet->setCellValue([$colNum, $rowNum], 'T' . $val);
            }
            // Detail columns get 'G' prefix if value starts with a digit
            elseif (isset($detailColumns[$colNum]) && $rowNum > 1 && $val !== '' && $val !== null && preg_match('/^\d/', (string) $val)) {
               $sheet->setCellValue([$colNum, $rowNum], 'G' . $val);
            } else {
               // For non-text columns, convert numeric strings to actual numbers
               if ($rowNum > 1 && is_numeric($val) && $val !== '') {
                  // Convert to number (float or int)
                  $val = strpos($val, '.') !== false ? (float) $val : (int) $val;
               }
               $sheet->setCellValue([$colNum, $rowNum], $val);
            }
            $colNum++;
         }
         $rowNum++;
      }

      // Auto-size columns for header row
      foreach (range('A', $sheet->getHighestColumn()) as $col) {
         $sheet->getColumnDimension($col)->setAutoSize(true);
      }

      // Style header row (bold)
      $headerRange = 'A1:' . $sheet->getHighestColumn() . '1';
      $sheet->getStyle($headerRange)->getFont()->setBold(true);

      // Create xlsx file
      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment; filename="' . $filename . '"');
      header('Cache-Control: max-age=0');

      $writer->save('php://output');
      exit();
   }
}


