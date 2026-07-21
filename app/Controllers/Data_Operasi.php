<?php

class Data_Operasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[3]) && !in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($parse, $parse_2 = 0)
   {
      if ($parse_2 == 0) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Data Order - Customer"
         ]);
      } else if ($parse_2 == 1) {
         $parse_2 = date('Y');
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Data Order - Tuntas"
         ]);
      } else {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Data Order - Tuntas"
         ]);
      }
      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = 0, $parse_2 = 0)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $parse_2]);
   }

   public function content($parse = 0, $parse_2 = 0)
   {
      $parse = $this->intParam($parse);
      $parse_2 = $this->intParam($parse_2);

      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');
      $data['ea'] = $this->db(0)->get('expedisi_account', 'id');

      $data['parse'] = $parse;
      $data['parse_2'] = $parse_2;
      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');

      if ($parse > 0) {
         $data['pelanggan'] = $this->db(0)->get_where('pelanggan', 'id_pelanggan = ' . $parse, 'id_pelanggan');
         if (!isset($data['pelanggan'][$parse])) {
            $parse = 0;
            $data['parse'] = 0;
            $data['pelanggan'] = [];
            $data['pelanggan_init'] = "[]";
            $data['pelanggan_ubah_init'] = "[]";
         } else {
            $p = $data['pelanggan'][$parse];
            $data['pelanggan_init'] = json_encode([[
               'id' => $p['id_pelanggan'],
               'nama' => strtoupper($p['nama']),
               'no_hp' => $p['no_hp'],
               'inisial' => $this->dToko[$p['id_toko']]['inisial']
            ]]);
            $data['pelanggan_ubah_init'] = $data['pelanggan_init'];
         }
      } else {
         $data['pelanggan'] = [];
         $data['pelanggan_init'] = "[]";
         $data['pelanggan_ubah_init'] = "[]";
      }

      $data['saldo'] = $this->data("Saldo")->deposit($parse);
      $data['paket'] = $this->db(0)->get('paket_main', "id");
      $data['barang'] = $this->db(0)->get('master_barang', 'id');
      $data['payment_account'] = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "' ORDER BY freq DESC", 'id');

      if ($parse_2 < 2023) {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $parse . " AND tuntas = 0";
         $where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND id_target = " . $parse . " AND tuntas = 0";
      } else {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $parse . " AND tuntas = 1 AND insertTime LIKE '%" . $parse_2 . "%'";
         $where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND id_target = " . $parse . " AND tuntas = 1 AND insertTime LIKE '%" . $parse_2 . "%'";
      }

      if ($parse == 0) {
         $data['order'] = [];
         $data['mutasi'] = [];
      } else {
         $pelanggan = $data['pelanggan'][$parse];
         $data['cust_wa'] = $this->data('Validasi')->valid_wa_direct($pelanggan['no_hp']);
         $data['order'] = $this->db(0)->get_where('order_data', $where, 'ref', 1);
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_mutasi, 'ref', 1);
         
         // AUTO-FIX: Paksa stat = 1 jika masih 0 dengan kondisi ref ada
         // Ini mengatasi masalah orphan items dari edit session yang tidak selesai
         if ($parse_2 < 2023) { // Only for non-tuntas (active) data
            $fix_where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND id_target = " . $parse . " AND tuntas = 0 AND ref <> '' AND stat = 0";
            $this->db(0)->update("master_mutasi", "stat = 1", $fix_where_mutasi);
         }
      }

      $ref1 = array_keys($data['order']);
      $ref2 = array_keys($data['mutasi']);
      $refs = array_unique(array_merge($ref1, $ref2));

      $new_refs = [];
      foreach ($refs as $ts) {
         $new_refs[substr($ts, 0, 7) . substr($ts, -4)] = $ts;
      }

      krsort($new_refs);
      $refs = $new_refs;

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $where_kas = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where_kas, 'ref_transaksi', 1);

         $where_ref = "ref IN (" . $ref_list . ")";
         $data['ref'] = $this->db(0)->get_where('ref', $where_ref, 'ref');

         $cols = "ref_bayar, metode_mutasi, sum(jumlah) as total, sum(bayar) as bayar, sum(kembali) as kembali, status_mutasi, insertTime";
         $where_2 = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ") GROUP BY ref_bayar";
         $data['r_kas'] = $this->db(0)->get_cols_where('kas', $cols, $where_2, 1);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where, 'ref_transaksi', 1);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['charge'] = $this->db(0)->get_where('charge', $where, 'ref_transaksi', 1);

         //PASTIKAN BELUM TUNTAS INDUK REF
         if ($parse_2 == 0) {
            $set = "tuntas = 0";
            $where = "ref IN (" . $ref_list . ")";
            $up = $this->db(0)->update("ref", $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
         }
      }

      $data['refs'] = $refs;
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');

      foreach ($refs as $r) {
         $data['head'][$r]['cs_to'] = 0;
         $data['head'][$r]['id_afiliasi'] = 0;
      }

      foreach ($data['order'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['id_penerima'];
            if ($dd['id_afiliasi'] <> 0) {
               $data['head'][$ref]['id_afiliasi'] = $dd['id_afiliasi'];
            }
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            $data['head'][$ref]['user_id'] = $dd['id_user'];
            if ($dd['id_user_afiliasi'] <> 0) {
               $data['head'][$ref]['cs_to'] = $dd['id_user_afiliasi'];
               break;
            }
         }
      }

      foreach ($data['mutasi'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['cs_id'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            $data['head'][$ref]['user_id'] = $dd['user_id'];
            break;
         }
      }

      $this->view($this->v_content, $data);
   }

   function faktur_pajak($id, $status)
   {
      $set = "fp = " . $status;
      $where = "id = " . $id;
      $update = $this->db(0)->update("master_mutasi", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   function jadikan($id)
   {
      $set = "stat = 1";
      $where = "id = " . $id;
      $update = $this->db(0)->update("master_mutasi", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   private function msgBayarGagal($jenis, $ctx = [])
   {
      switch ($jenis) {
         case 'duplikat':
            $ref = $ctx['ref'] ?? '';
            $jumlah = isset($ctx['jumlah']) ? number_format((int) $ctx['jumlah']) : '0';
            $metode = (int) ($ctx['metode'] ?? 0);
            if (in_array($metode, [2, 3], true)) {
               $solusi = 'Ubah isian <b>Catatan Transaksi</b> agar berbeda dari pembayaran sebelumnya (mis. nama pengirim, jam, atau no. referensi transfer).';
            } else {
               $solusi = 'Pastikan pembayaran belum tercatat (refresh halaman). Jika ini pembayaran baru, gunakan metode <b>Non Tunai/Afiliasi</b> lalu isi <b>Catatan Transaksi</b> yang unik.';
            }
            return 'Pembayaran gagal: sistem mendeteksi <b>input ganda</b> (ref ' . $ref . ', jumlah Rp' . $jumlah . ', metode & catatan sama dengan data yang sudah ada).<br><br><b>Solusi:</b> ' . $solusi;
         case 'pilih_tagihan':
            return 'Pembayaran gagal: tidak ada tagihan yang dipilih.<br><br><b>Solusi:</b> centang minimal satu tagihan di daftar <b>Pembayaran Multi</b>.';
         case 'akun_pembayaran':
            return 'Pembayaran gagal: akun pembayaran belum dipilih.<br><br><b>Solusi:</b> pilih <b>Akun Pembayaran</b> pada metode Non Tunai.';
         case 'akun_sds':
            $mode = $ctx['mode'] ?? 'TOKO';
            if ($mode === 'SDS') {
               $solusi = 'Tagihan terpilih hanya item <b>SDS</b>. Pilih akun pembayaran SDS.';
            } elseif ($mode === 'TOKO') {
               $solusi = 'Tagihan terpilih hanya item <b>TOKO</b>. Pilih akun pembayaran selain SDS.';
            } else {
               $solusi = 'Periksa kembali akun pembayaran yang dipilih.';
            }
            return 'Pembayaran gagal: akun pembayaran tidak sesuai lokasi item (' . $mode . ').<br><br><b>Solusi:</b> ' . $solusi;
         case 'db':
            return 'Pembayaran gagal: ' . ($ctx['msg'] ?? 'terjadi kesalahan database.') . '<br><br><b>Solusi:</b> coba lagi. Jika berulang, hubungi admin.';
         default:
            return $ctx['msg'] ?? 'Pembayaran gagal.';
      }
   }

   private function refSdsProfile($ref)
   {
      $refEsc = addslashes($ref);
      $hasSds = false;
      $hasToko = false;

      $charges = $this->db(0)->get_where('charge', "ref_transaksi = '" . $refEsc . "'");
      foreach ($charges as $ds) {
         if ($ds['cancel'] == 0) {
            $hasToko = true;
         }
      }

      $orders = $this->db(0)->get_where('order_data', "ref = '" . $refEsc . "'");
      foreach ($orders as $do) {
         if ($do['cancel'] == 0 && $do['stok'] == 0) {
            $hasToko = true;
         }
      }

      $mutasi = $this->db(0)->get_where('master_mutasi', "ref = '" . $refEsc . "'");
      foreach ($mutasi as $do) {
         if ($do['stat'] <> 2) {
            if ((int)($do['sds'] ?? 0) === 1) {
               $hasSds = true;
            } else {
               $hasToko = true;
            }
         }
      }

      if ($hasSds && $hasToko) {
         return 'MIX';
      }
      if ($hasSds) {
         return 'SDS';
      }
      return 'TOKO';
   }

   private function multiPayLokasiMode(array $ref_multi)
   {
      $profiles = [];
      foreach ($ref_multi as $value) {
         $parts = explode('_', $value);
         if (!isset($parts[1]) || $parts[1] === '') {
            continue;
         }
         $profiles[] = $this->refSdsProfile($parts[1]);
      }

      if (count($profiles) === 0) {
         return 'TOKO';
      }

      if (in_array('MIX', $profiles, true)) {
         return 'MIX';
      }

      $unique = array_unique($profiles);
      if (count($unique) === 1) {
         return $profiles[0];
      }

      return 'MIX';
   }

   public function bayar_multi()
   {
      if (isset($_POST['ref_multi'])) {
         $ref_multi = $_POST['ref_multi'];
      } else {
         echo $this->msgBayarGagal('pilih_tagihan');
         exit();
      }

      $dibayar = $_POST['dibayar_multi'];

      $count_ref = count($ref_multi);
      if ($count_ref == 0) {
         echo $this->msgBayarGagal('pilih_tagihan');
         exit();
      }

      $note =  $_POST['note_multi'];
      $metode =  $_POST['metode_multi'];
      $charge = isset($_POST['charge']) ? $_POST['charge'] : 0;

      if ($charge == "") {
         $charge = 0;
      }

      $ref_bayar = date("ymdhis") . rand(0, 9);
      $sds = 0;

      if ($metode == 2) {
         if ($_POST['payment_account'] == "") {
            echo $this->msgBayarGagal('akun_pembayaran');
            exit();
         } else {
            $payment_account = $_POST['payment_account'];
            $dPa = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "'", 'id');
            if (!isset($dPa[$payment_account])) {
               echo $this->msgBayarGagal('akun_pembayaran');
               exit();
            }

            $lokasiMode = $this->multiPayLokasiMode($ref_multi);
            $paSds = (int)($dPa[$payment_account]['sds'] ?? 0);
            if ($lokasiMode === 'SDS' && $paSds !== 1) {
               echo $this->msgBayarGagal('akun_sds', ['mode' => 'SDS']);
               exit();
            }
            if ($lokasiMode === 'TOKO' && $paSds === 1) {
               echo $this->msgBayarGagal('akun_sds', ['mode' => 'TOKO']);
               exit();
            }

            $sds = $paSds;
            //updateFreq
            $this->db(0)->update("payment_account", "freq = freq+1", "id = " . $payment_account);
         }
      } else {
         $payment_account = "";
      }

      if (strlen($note) == 0 && $metode == 2) {
         $note = "Non_Tunai";
      } elseif (strlen($note) == 0 && $metode == 3) {
         $note = "Afiliasi";
      }

      $inserted = 0;
      arsort($ref_multi);
      foreach ($ref_multi as $value) {
         $count_ref -= 1;

         if ($dibayar == 0) {
            echo 0;
            exit();
         }

         $value_ = explode("_", $value);

         $client = $value_[0];
         $ref = $value_[1];
         $jumlah = $value_[2];

         if ($dibayar < $jumlah) {
            $jumlah = $dibayar;
         }
         $finance_id = 0;

         switch ($metode) {
            case 1:
               $status_mutasi = 1;
               break;
            case 4:
               $status_mutasi = 1;
               $saldo = $this->data('Saldo')->deposit($client);
               if ($jumlah > $saldo) {
                  $jumlah = $saldo;
               }
               break;
            default:
               $status_mutasi = 0;
               break;
         }
         if ($count_ref == 0) {
            $bayarnya = $dibayar;
            $kembalian = $dibayar - $jumlah;
         } else {
            $bayarnya = $jumlah;
            $kembalian = 0;
         }

         $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND metode_mutasi = " . $metode . " AND status_mutasi = " . $status_mutasi . " AND note = '" . $note . "'";
         $dataCount = $this->db(0)->count_where('kas', $whereCount);

         $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar, bayar, kembali, id_finance_nontunai, pa, sds, charge";
         $vals = $this->userData['id_toko'] . ",1,1,'" . $ref . "'," . $metode . "," . $status_mutasi . "," . $jumlah . "," . $this->userData['id_user'] . "," . $client . ",'" . $note . "','" . $ref_bayar . "'," . $bayarnya . "," . $kembalian . "," . $finance_id . ",'" . $payment_account . "'," . $sds . "," . $charge;

         if ($dataCount > 0) {
            echo $this->msgBayarGagal('duplikat', [
               'ref' => $ref,
               'jumlah' => $jumlah,
               'metode' => $metode,
            ]);
            exit();
         }

         $do = $this->db(0)->insertCols('kas', $cols, $vals);
         if ($do['errno'] == 0) {
            $dibayar -= $jumlah;
            $inserted++;
         } else {
            echo $this->msgBayarGagal('db', ['msg' => $do['error']]);
            exit();
         }
      }

      if ($inserted === 0) {
         echo $this->msgBayarGagal('duplikat', [
            'ref' => '',
            'jumlah' => 0,
            'metode' => $metode,
         ]);
         exit();
      }

      echo 0;
   }

   public function transfer_item()
   {
      $item_type = isset($_POST['item_type']) ? $_POST['item_type'] : '';
      $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : 0;
      $dest_ref = isset($_POST['dest_ref']) ? $_POST['dest_ref'] : '';
      if ($item_type == '' || $item_id == 0 || $dest_ref == '') {
         echo "Data tidak lengkap";
         exit();
      }

      $pelanggan = 0;
      $source_ref = '';
      if ($item_type == 'order') {
         $row = $this->db(0)->get_where_row('order_data', "id_order_data = " . $item_id);
         if (!isset($row['id_order_data'])) {
            echo "Item tidak ditemukan";
            exit();
         }
         $pelanggan = $row['id_pelanggan'];
         $source_ref = $row['ref'];
         $pg_group = isset($row['paket_group']) ? $row['paket_group'] : '';
      } else if ($item_type == 'mutasi') {
         $row = $this->db(0)->get_where_row('master_mutasi', "id = " . $item_id);
         if (!isset($row['id'])) {
            echo "Item tidak ditemukan";
            exit();
         }
         $pelanggan = $row['id_target'];
         $source_ref = $row['ref'];
         $pg_group = isset($row['paket_group']) ? $row['paket_group'] : '';
      } else {
         echo "Tipe item tidak valid";
         exit();
      }

      $count1 = $this->db(0)->count_where("order_data", "ref = '" . $dest_ref . "' AND id_pelanggan = " . $pelanggan . " AND tuntas = 0");
      $count2 = $this->db(0)->count_where("master_mutasi", "ref = '" . $dest_ref . "' AND id_target = " . $pelanggan . " AND tuntas = 0");
      if ($count1 == 0 && $count2 == 0) {
         echo "Ref tujuan tidak valid";
         exit();
      }

      $set = "ref = '" . $dest_ref . "'";
      if (strlen($pg_group) > 0) {
         $where_od = "paket_group = '" . $pg_group . "' AND ref = '" . $source_ref . "' AND tuntas = 0";
         $update1 = $this->db(0)->update("order_data", $set, $where_od);
         if ($update1['errno'] <> 0) {
            echo $update1['error'];
            exit();
         }
         $where_mm = "paket_group = '" . $pg_group . "' AND ref = '" . $source_ref . "' AND tuntas = 0";
         $update2 = $this->db(0)->update("master_mutasi", $set, $where_mm);
         if ($update2['errno'] <> 0) {
            echo $update2['error'];
            exit();
         }
         $update = ['errno' => 0];
      } else {
         if ($item_type == 'order') {
            $where = "id_order_data = " . $item_id;
            $update = $this->db(0)->update("order_data", $set, $where);
         } else {
            $where = "id = " . $item_id;
            $update = $this->db(0)->update("master_mutasi", $set, $where);
         }
      }

      if ($update['errno'] <> 0) {
         echo $update['error'];
         exit();
      }

      if ($source_ref <> '') {
         $remain_order = $this->db(0)->count_where('order_data', "ref = '" . $source_ref . "'");
         $remain_mutasi = $this->db(0)->count_where('master_mutasi', "ref = '" . $source_ref . "'");
         if ($remain_order == 0 && $remain_mutasi == 0) {
            $this->db(0)->delete_where('ref', "ref = '" . $source_ref . "'");
         }
      }

      echo 0;
   }

   public function refundCash()
   {
      $ref = $_POST['ref_refund'];
      $client = $_POST['id_client'];
      $refund = $_POST['refund'];
      $metode = $_POST['metode'];

      if ($metode == 1) {
         $st_mutasi = 1;
      } else {
         $st_mutasi = 0;
      }

      $dibayar = $this->db(0)->sum_col_where("kas", "jumlah", "jenis_transaksi = 1 AND ref_transaksi ='" . $ref . "' AND status_mutasi = 1 AND ref_setoran <> ''");
      $sudah_refund = $this->db(0)->sum_col_where("kas", "jumlah", "jenis_transaksi = 4 AND ref_transaksi = '" . $ref . "' AND status_mutasi <> 2");

      $max_refund = $dibayar - $sudah_refund;

      if ($refund > $max_refund) {
         echo "Jumlah refund melebihi batas maksimal!";
         exit();
      }

      $note =  $_POST['note'];
      $sds = $_POST['sds'];
      $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, sds";
      $vals = $this->userData['id_toko'] . ",4,2,'" . $ref . "',1," . $st_mutasi . "," . $refund . "," . $this->userData['id_user'] . "," . $client . ",'" . $note . "'," . $sds;

      $do = $this->db(0)->insertCols('kas', $cols, $vals);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }
   }

   function xtraDiskon()
   {
      $ref = $_POST['ref_diskon'];
      $jumlah = $_POST['diskon'];
      $note = $_POST['note'];
      $sds = $_POST['sds'];
      $max = $_POST['max_diskon'];

      if ($jumlah > $max || $jumlah == 0) {
         echo "Jumlah Diskon tidak di izinkan!";
         exit();
      }

      //data sds
      $where_ref = "ref = '" . $ref . "' AND sds = 1 AND stat = 1";
      $data_sds = $this->db(0)->get_where('master_mutasi', $where_ref);

      //maximal transaksi sds
      $max_sds = 0;
      foreach ($data_sds as $ds) {
         $max_sds += (($ds['harga_jual'] - $ds['diskon']) * $ds['qty']);
      }

      if ($sds == 1) {
         //jika sds
         if ($jumlah > $max_sds) {
            echo "Diskon melebihi batas transaksi SDS!";
            exit();
         }
      } else {
         //jika toko
         $max_toko = $max - $max_sds;
         if ($jumlah > $max_toko) {
            echo "Diskon melebihi batas transaksi Toko!";
            exit();
         }
      }

      $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND cancel = 0";
      $dataCount = $this->db(0)->count_where('xtra_diskon', $whereCount);

      $cols = "id_toko, ref_transaksi, jumlah, id_user, note, sds";
      $vals = $this->userData['id_toko'] . ",'" . $ref . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "'," . $sds;

      if ($dataCount < 1) {
         $do = $this->db(0)->insertCols('xtra_diskon', $cols, $vals);
         if ($do['errno'] == 0) {
            echo $do['errno'];
            $this->model('Log')->write($this->userData['user'] . " Extra Diskon " . $jumlah . " Success!");
         } else {
            echo $do['error'];
         }
      }
   }

   function charge()
   {
      $ref = $_POST['ref_charge'];
      $jumlah = $_POST['charge'];
      $note = $_POST['note'];

      $whereCount = "ref_transaksi = '" . $ref . "' AND cancel = 0";
      $dataCount = $this->db(0)->count_where('charge', $whereCount);

      $cols = "id_toko, ref_transaksi, jumlah, id_user, note";
      $vals = $this->userData['id_toko'] . ",'" . $ref . "'," . $jumlah . "," . $this->userData['id_user'] . ", '" . $note . "'";

      if ($dataCount < 1) {
         $do = $this->db(0)->insertCols('charge', $cols, $vals);
         if ($do['errno'] == 0) {
            echo $do['errno'];
            exit();
         } else {
            echo $do['error'];
         }
      }
   }

   function mark()
   {
      $ref = $_POST['ref_mark'];
      $mark = $_POST['mark'];

      $up = $this->db(0)->update("ref", "mark = '" . $mark . "'", "ref = '" . $ref . "'");
      if ($up['errno'] <> 0) {
         echo $up['error'];
      } else {
         echo 0;
      }
   }

   function ubahPelanggan()
   {
      $ref = $_POST['ubah_ref'];
      $id_pelanggan_baru = $_POST['id_pelanggan_baru'];
      $pelanggan_lama = $_POST['pelanggan_lama'];

      // Validate new pelanggan exists
      $pelanggan = $this->db(0)->get_where_row('pelanggan', "id_pelanggan = " . $id_pelanggan_baru);
      if (!isset($pelanggan['id_pelanggan'])) {
         echo "Pelanggan tidak ditemukan";
         exit();
      }

      // Update order_data
      $set = "id_pelanggan = " . intval($id_pelanggan_baru);
      $where = "ref = '" . addslashes($ref) . "' AND tuntas = 0";
      $up1 = $this->db(0)->update("order_data", $set, $where);
      if ($up1['errno'] <> 0) {
         echo $up1['error'];
         exit();
      }

      // Update master_mutasi
      $set = "id_target = " . $id_pelanggan_baru;
      $where = "ref = '" . addslashes($ref) . "' AND tuntas = 0";
      $up2 = $this->db(0)->update("master_mutasi", $set, $where);
      if ($up2['errno'] <> 0) {
         echo $up2['error'];
         exit();
      }

      $this->model('Log')->write($this->userData['user'] . " Ubah Pelanggan " . $pelanggan_lama . " -> " . $id_pelanggan_baru . " untuk ref " . $ref);
      
      // Redirect back
      header("Location: " . PV::BASE_URL . "Data_Operasi/index/" . $id_pelanggan_baru);
      exit();
   }
   function search_pelanggan()
   {
      $q = trim($_GET['q'] ?? '');
      if (strlen($q) < 2) {
         echo json_encode([]);
         exit();
      }

      // Pisah kata agar "nn 91" bisa match nama + id_pelanggan
      $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
      $where = "en = 1";
      foreach ($parts as $p) {
         $p = addslashes($p);
         $where .= " AND (nama LIKE '%" . $p . "%' OR no_hp LIKE '%" . $p . "%' OR id_pelanggan LIKE '%" . $p . "%')";
      }
      $res = $this->db(0)->get_where('pelanggan', $where);
      $data = [];
      foreach ($res as $p) {
         $data[] = [
            'id' => $p['id_pelanggan'],
            'nama' => strtoupper($p['nama']),
            'no_hp' => $p['no_hp'],
            'inisial' => $this->dToko[$p['id_toko']]['inisial']
         ];
      }
      echo json_encode($data);
   }

   function search_pelanggan_ubah()
   {
      $q = trim($_GET['q'] ?? '');
      if (strlen($q) < 2) {
         echo json_encode([]);
         exit();
      }

      $id_toko = (int)$this->userData['id_toko'];
      $id_pelanggan_jenis = isset($_GET['id_pelanggan_jenis']) ? (int)$_GET['id_pelanggan_jenis'] : 0;

      $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
      $where = "en = 1 AND id_toko = " . $id_toko;
      if ($id_pelanggan_jenis > 0) {
         $where .= " AND id_pelanggan_jenis = " . $id_pelanggan_jenis;
      }
      foreach ($parts as $p) {
         $p = addslashes($p);
         $where .= " AND (nama LIKE '%" . $p . "%' OR no_hp LIKE '%" . $p . "%' OR id_pelanggan LIKE '%" . $p . "%')";
      }
      $res = $this->db(0)->get_where('pelanggan', $where);
      $data = [];
      foreach ($res as $p) {
         $data[] = [
            'id' => $p['id_pelanggan'],
            'nama' => strtoupper($p['nama']),
            'no_hp' => $p['no_hp'],
            'inisial' => $this->dToko[$p['id_toko']]['inisial']
         ];
      }
      echo json_encode($data);
   }

   public function mark_print()
   {
      $ref = trim($_POST['ref'] ?? '');
      $reason = trim($_POST['reprint_reason'] ?? '');

      if ($ref === '') {
         echo 'Ref tidak valid';
         exit();
      }

      $refEsc = addslashes($ref);
      $row = $this->db(0)->get_where_row('ref', "ref = '" . $refEsc . "'");
      if (!isset($row['ref'])) {
         echo 'Ref tidak ditemukan';
         exit();
      }

      $printed = (int)($row['printed'] ?? 0);
      $userLabel = $this->userData['user'] ?? ('#' . $this->userData['id_user']);

      if ($printed === 0) {
         $up = $this->db(0)->update('ref', 'printed = 1', "ref = '" . $refEsc . "'");
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
         $this->model('Log')->write($this->userData['user'] . " Cetak order pertama ref " . $ref);
         echo 0;
         exit();
      }

      $line = '[' . date('Y-m-d H:i') . '] ' . $userLabel;
      if ($reason !== '') {
         $line .= ': ' . $reason;
      }
      $existing = trim($row['reprint_reason'] ?? '');
      $newReason = $existing === '' ? $line : $existing . "\n" . $line;
      $newCount = $printed + 1;

      $set = "printed = " . $newCount . ", reprint_reason = '" . addslashes($newReason) . "'";
      $up = $this->db(0)->update('ref', $set, "ref = '" . $refEsc . "'");
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }

      $logMsg = $this->userData['user'] . " Cetak ulang ref " . $ref . " (#" . $newCount . ")";
      if ($reason !== '') {
         $logMsg .= ": " . $reason;
      }
      $this->model('Log')->write($logMsg);
      echo 0;
   }

   public function stok_sn($id_barang, $id_sumber)
   {
      $stok = $this->data('Barang')->stok_data($id_barang, $id_sumber);
      $list = [];
      foreach ($stok as $s) {
         if ($s['qty'] > 0 && $s['sn'] !== "") {
            $list[] = [
               'sn' => $s['sn'],
               'sds' => (int) $s['sds'],
               'qty' => (int) $s['qty'],
            ];
         }
      }
      header('Content-Type: application/json');
      echo json_encode($list);
   }

   public function cek_barang_tukar()
   {
      header('Content-Type: application/json');

      $id = (int) ($_POST['tukarBarang_id'] ?? 0);
      $id_baru = trim($_POST['id_baru'] ?? '');
      $sn_baru = trim($_POST['sn_baru'] ?? '');
      $sds_baru = isset($_POST['sds_baru']) ? (int) $_POST['sds_baru'] : -1;

      if ($id <= 0) {
         echo json_encode(['ok' => false, 'message' => 'Data baris order tidak valid']);
         exit();
      }
      if ($id_baru === '') {
         echo json_encode(['ok' => false, 'message' => 'ID Barang baru wajib diisi']);
         exit();
      }
      if ($sds_baru < 0 || $sds_baru > 1) {
         echo json_encode(['ok' => false, 'message' => 'SDS harus dipilih (Ya/Tidak)']);
         exit();
      }

      $cek = $this->db(0)->get_where_row('master_mutasi', 'id = ' . $id);
      if (!$cek) {
         echo json_encode(['ok' => false, 'message' => 'Data order tidak ditemukan']);
         exit();
      }

      $barang = $this->db(0)->get_where_row('master_barang', "id = '" . addslashes($id_baru) . "'");
      if (!$barang) {
         echo json_encode(['ok' => false, 'message' => 'ID Barang tidak ditemukan']);
         exit();
      }

      $has_sn = (int) ($barang['sn'] ?? 0) === 1;
      $sn_cek = $sn_baru;
      if (!$has_sn) {
         $sn_cek = '';
      } elseif ($sn_cek === '') {
         echo json_encode(['ok' => false, 'message' => 'SN wajib diisi untuk barang ber-SN']);
         exit();
      }

      $id_sumber = (int) ($cek['id_sumber'] ?? 0);
      $id_toko = (int) $this->userData['id_toko'];
      $sisa_stok = $this->data('Barang')->sisa_stok($id_baru, $id_sumber, $sn_cek, $sds_baru);
      if ($sisa_stok <= 0 && $id_sumber != $id_toko) {
         $sisa_stok = $this->data('Barang')->sisa_stok($id_baru, $id_toko, $sn_cek, $sds_baru);
      }

      if ($sisa_stok <= 0) {
         echo json_encode(['ok' => false, 'message' => 'Stok barang tidak tersedia untuk ID, SN, dan SDS tersebut']);
         exit();
      }

      $nama = trim(($barang['brand'] ?? '') . ' ' . ($barang['model'] ?? '') . ($barang['product_name'] ?? ''));
      echo json_encode([
         'ok' => true,
         'nama' => $nama !== '' ? $nama : $id_baru,
         'sn' => ($has_sn && $sn_baru !== '') ? $sn_baru : '-',
         'lokasi' => $sds_baru === 1 ? 'SDS' : 'TOKO',
         'qty' => (int) $sisa_stok,
      ]);
   }

   private function assertKasirPriv()
   {
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         echo 'Akses ditolak';
         exit();
      }
   }

   private function getKasRow($id_kas)
   {
      $row = $this->db(0)->get_where_row('kas', "id_kas = " . (int)$id_kas);
      if (!isset($row['id_kas']) || (int)$row['id_toko'] !== (int)$this->userData['id_toko']) {
         echo 'Data pembayaran tidak ditemukan';
         exit();
      }
      return $row;
   }

   private function refFinance($ref)
   {
      $refEsc = addslashes($ref);
      $bill = 0;

      $charges = $this->db(0)->get_where('charge', "ref_transaksi = '" . $refEsc . "'");
      foreach ($charges as $ds) {
         if ($ds['cancel'] == 0) {
            $bill += (int)$ds['jumlah'];
         }
      }

      $orders = $this->db(0)->get_where('order_data', "ref = '" . $refEsc . "'");
      foreach ($orders as $do) {
         if ($do['cancel'] == 0 && $do['stok'] == 0) {
            $paket_qty_val = isset($do['paket_qty']) && $do['paket_qty'] > 0 ? (int)$do['paket_qty'] : 1;
            $bill += (int)(($do['harga'] * $do['jumlah']) + ($do['harga_paket'] * $paket_qty_val));
            $listDetail = @unserialize($do['detail_harga']);
            $akum_diskon_unit = 0;
            if (is_array($listDetail)) {
               foreach ($listDetail as $ld_o) {
                  $akum_diskon_unit += isset($ld_o['d']) ? (int)$ld_o['d'] : 0;
               }
            }
            $bill -= $akum_diskon_unit * (int)$do['jumlah'];
         }
      }

      $mutasi = $this->db(0)->get_where('master_mutasi', "ref = '" . $refEsc . "'");
      foreach ($mutasi as $do) {
         if ($do['stat'] <> 2) {
            $paket_qty_val = isset($do['paket_qty']) && $do['paket_qty'] > 0 ? (int)$do['paket_qty'] : 1;
            $bill += (int)((($do['harga_jual'] * $do['qty']) + ($do['harga_paket'] * $paket_qty_val)) - ($do['diskon'] * $do['qty']));
         }
      }

      $dibayar = 0;
      $kasRows = $this->db(0)->get_where('kas', "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $refEsc . "'");
      foreach ($kasRows as $dk) {
         if ($dk['status_mutasi'] == 0 || $dk['status_mutasi'] == 1) {
            $dibayar += (int)$dk['jumlah'];
         }
      }

      $diskonRows = $this->db(0)->get_where('xtra_diskon', "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi = '" . $refEsc . "'");
      foreach ($diskonRows as $ds) {
         if ($ds['cancel'] == 0) {
            $dibayar += (int)$ds['jumlah'];
         }
      }

      return [
         'bill' => $bill,
         'dibayar' => $dibayar,
         'sisa' => $bill - $dibayar,
      ];
   }

   private function refBelongsToClient($ref, $id_client)
   {
      $refEsc = addslashes($ref);
      $c1 = $this->db(0)->count_where('order_data', "ref = '" . $refEsc . "' AND id_pelanggan = " . (int)$id_client);
      $c2 = $this->db(0)->count_where('master_mutasi', "ref = '" . $refEsc . "' AND id_target = " . (int)$id_client);
      return ($c1 + $c2) > 0;
   }

   private function insertKasFromTemplate(array $kas, $target_ref, $jumlah)
   {
      $note = addslashes($kas['note'] ?? '');
      $ref_bayar = addslashes($kas['ref_bayar'] ?? '');
      $pa = addslashes($kas['pa'] ?? '');
      $targetEsc = addslashes($target_ref);
      $jumlah = (int)$jumlah;

      $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar, bayar, kembali, id_finance_nontunai, pa, sds, charge";
      $vals = (int)$kas['id_toko'] . ",1,1,'" . $targetEsc . "'," . (int)$kas['metode_mutasi'] . "," . (int)$kas['status_mutasi'] . "," . $jumlah . "," . $this->userData['id_user'] . "," . (int)$kas['id_client'] . ",'" . $note . "','" . $ref_bayar . "'," . $jumlah . ",0," . (int)($kas['id_finance_nontunai'] ?? 0) . ",'" . $pa . "'," . (int)($kas['sds'] ?? 0) . "," . (int)($kas['charge'] ?? 0);
      return $this->db(0)->insertCols('kas', $cols, $vals);
   }

   private function updateKasJumlah(array $kas, $new_jumlah)
   {
      $new_jumlah = (int)$new_jumlah;
      $new_bayar = (int)$kas['bayar'];
      if ($new_bayar > $new_jumlah) {
         $new_bayar = $new_jumlah;
      }
      $new_kembali = max(0, $new_bayar - $new_jumlah);
      $set = "jumlah = " . $new_jumlah . ", bayar = " . $new_bayar . ", kembali = " . $new_kembali;
      return $this->db(0)->update('kas', $set, "id_kas = " . (int)$kas['id_kas']);
   }

   public function fix_bayar_adjust()
   {
      $this->assertKasirPriv();

      $id_kas = (int)($_POST['id_kas'] ?? 0);
      $source_ref = trim($_POST['source_ref'] ?? '');
      if ($id_kas <= 0 || $source_ref === '') {
         echo 'Data tidak lengkap';
         exit();
      }

      $kas = $this->getKasRow($id_kas);
      if ($kas['ref_transaksi'] !== $source_ref) {
         echo 'Ref pembayaran tidak sesuai';
         exit();
      }

      $fin = $this->refFinance($source_ref);
      if ($fin['sisa'] >= 0) {
         echo 'Tidak ada pembayaran berlebih pada ref ini';
         exit();
      }

      $excess = abs($fin['sisa']);
      if ((int)$kas['jumlah'] < $excess) {
         echo 'Kelebihan bayar melebihi jumlah pada pembayaran terpilih';
         exit();
      }

      $new_jumlah = (int)$kas['jumlah'] - $excess;
      $up = $this->updateKasJumlah($kas, $new_jumlah);
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }

      $this->model('Log')->write($this->userData['user'] . " Fix bayar adjust ref " . $source_ref . " kas#" . $id_kas . " -" . $excess);
      echo 0;
   }

   public function fix_bayar_split()
   {
      $this->assertKasirPriv();

      $id_kas = (int)($_POST['id_kas'] ?? 0);
      $source_ref = trim($_POST['source_ref'] ?? '');
      $target_refs = $_POST['target_refs'] ?? [];
      if ($id_kas <= 0 || $source_ref === '' || !is_array($target_refs) || count($target_refs) === 0) {
         echo 'Data tidak lengkap';
         exit();
      }

      $kas = $this->getKasRow($id_kas);
      if ($kas['ref_transaksi'] !== $source_ref) {
         echo 'Ref pembayaran tidak sesuai';
         exit();
      }

      $fin = $this->refFinance($source_ref);
      if ($fin['sisa'] >= 0) {
         echo 'Tidak ada pembayaran berlebih pada ref ini';
         exit();
      }

      $excess = abs($fin['sisa']);
      if ((int)$kas['jumlah'] < $excess) {
         echo 'Kelebihan bayar melebihi jumlah pada pembayaran terpilih';
         exit();
      }

      $allocated = 0;
      $id_client = (int)$kas['id_client'];
      foreach ($target_refs as $target_ref) {
         $target_ref = trim($target_ref);
         if ($target_ref === '' || $target_ref === $source_ref) {
            continue;
         }

         if (!$this->refBelongsToClient($target_ref, $id_client)) {
            echo 'Ref tujuan bukan milik pelanggan yang sama';
            exit();
         }

         $refRow = $this->db(0)->get_where_row('ref', "ref = '" . addslashes($target_ref) . "'");
         if (isset($refRow['tuntas']) && (int)$refRow['tuntas'] === 1) {
            continue;
         }

         $tfin = $this->refFinance($target_ref);
         if ($tfin['sisa'] <= 0) {
            continue;
         }

         $alloc = min($tfin['sisa'], $excess - $allocated);
         if ($alloc <= 0) {
            break;
         }

         $ins = $this->insertKasFromTemplate($kas, $target_ref, $alloc);
         if ($ins['errno'] <> 0) {
            echo $ins['error'];
            exit();
         }
         $allocated += $alloc;
      }

      if ($allocated <= 0) {
         echo 'Tidak ada tagihan tujuan yang bisa dibayar';
         exit();
      }

      if ($allocated < $excess) {
         echo 'Total tagihan tujuan kurang dari kelebihan bayar. Pilih ref lain atau tambahkan ref tujuan.';
         exit();
      }

      $new_jumlah = (int)$kas['jumlah'] - $allocated;
      $up = $this->updateKasJumlah($kas, $new_jumlah);
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }

      $this->model('Log')->write($this->userData['user'] . " Fix bayar split ref " . $source_ref . " kas#" . $id_kas . " alokasi " . $allocated . " ke " . implode(',', $target_refs));
      echo 0;
   }

   /**
    * Analisa lengkap 1 nota (ref) — untuk diagnose kenapa belum tuntas, dll.
    */
   public function analisa($ref = '')
   {
      $ref = trim(urldecode((string) $ref));
      if ($ref === '') {
         echo '<div class="alert alert-danger mb-0">Ref tidak valid.</div>';
         exit();
      }

      $refEsc = addslashes($ref);
      $idToko = (int) $this->userData['id_toko'];

      $dRef = $this->db(0)->get_where_row('ref', "ref = '" . $refEsc . "'");
      $orders = $this->db(0)->get_where('order_data', "ref = '" . $refEsc . "' ORDER BY id_order_data ASC");
      if (!is_array($orders) || isset($orders['errno'])) {
         $orders = [];
      }
      $mutasi = $this->db(0)->get_where('master_mutasi', "ref = '" . $refEsc . "' ORDER BY id ASC");
      if (!is_array($mutasi) || isset($mutasi['errno'])) {
         $mutasi = [];
      }

      // Scope: toko sendiri atau afiliasi
      $allowed = false;
      foreach ($orders as $o) {
         if ((int)($o['id_toko'] ?? 0) === $idToko || (int)($o['id_afiliasi'] ?? 0) === $idToko) {
            $allowed = true;
            break;
         }
      }
      foreach ($mutasi as $m) {
         if ((int)($m['id_sumber'] ?? 0) === $idToko) {
            $allowed = true;
            break;
         }
      }
      if (!$allowed && is_array($dRef) && !empty($dRef['ref'])) {
         // ref exists but no matching rows for this toko
         echo '<div class="alert alert-warning mb-0">Nota tidak ditemukan untuk toko ini.</div>';
         exit();
      }
      if (!$allowed && (count($orders) + count($mutasi)) === 0) {
         echo '<div class="alert alert-warning mb-0">Data nota tidak ditemukan.</div>';
         exit();
      }

      $kas = $this->db(0)->get_where('kas', "ref_transaksi = '" . $refEsc . "' AND (jenis_transaksi = 1 OR jenis_transaksi = 4) ORDER BY id_kas ASC");
      if (!is_array($kas) || isset($kas['errno'])) {
         $kas = [];
      }
      $diskon = $this->db(0)->get_where('xtra_diskon', "ref_transaksi = '" . $refEsc . "'");
      if (!is_array($diskon) || isset($diskon['errno'])) {
         $diskon = [];
      }
      $charge = $this->db(0)->get_where('charge', "ref_transaksi = '" . $refEsc . "'");
      if (!is_array($charge) || isset($charge['errno'])) {
         $charge = [];
      }
      $kasKecil = $this->db(0)->get_where('kas_kecil', "ref = '" . $refEsc . "' AND tipe = 0");
      if (!is_array($kasKecil) || isset($kasKecil['errno'])) {
         $kasKecil = [];
      }

      $bill = 0;
      $dibayarUi = 0;
      $verifyPayment = 0;
      $refundTotal = 0;
      $ambilAll = true;
      $spkPending = [];
      $cancelCount = 0;
      $itemCount = 0;
      $hasStok = false;
      $hasDiskonItem = false;
      $insertTime = '';
      $idPelanggan = 0;
      $idPenerima = 0;
      $idUser = 0;
      $idAfiliasi = 0;
      $idUserAfiliasi = 0;
      $idTokoNota = 0;
      $orderLines = [];
      $mutasiLines = [];

      foreach ($charge as $c) {
         if ((int)($c['cancel'] ?? 0) === 0) {
            $bill += (int) $c['jumlah'];
         }
      }

      foreach ($orders as $do) {
         $itemCount++;
         if ((int)$do['tuntas'] === 1) {
            // tracked in dRef mainly
         }
         if ((int)$do['stok'] === 1) {
            $hasStok = true;
         }
         if ((int)$do['diskon'] > 0) {
            $hasDiskonItem = true;
         }
         if ($insertTime === '' && !empty($do['insertTime'])) {
            $insertTime = $do['insertTime'];
         }
         if ($idPelanggan === 0) {
            $idPelanggan = (int) $do['id_pelanggan'];
         }
         if ($idPenerima === 0) {
            $idPenerima = (int) $do['id_penerima'];
         }
         if ($idUser === 0) {
            $idUser = (int) $do['id_user'];
         }
         if ((int)$do['id_afiliasi'] !== 0) {
            $idAfiliasi = (int) $do['id_afiliasi'];
         }
         if ((int)$do['id_user_afiliasi'] !== 0) {
            $idUserAfiliasi = (int) $do['id_user_afiliasi'];
         }
         if ($idTokoNota === 0) {
            $idTokoNota = (int) $do['id_toko'];
         }

         $cancel = (int) $do['cancel'];
         if ($cancel === 1) {
            $cancelCount++;
         }

         $spkRaw = $do['spk_dvs'] ?? '';
         $spkArr = (strlen($spkRaw) > 1) ? @unserialize($spkRaw) : [];
         if (!is_array($spkArr)) {
            $spkArr = [];
         }
         $spkCount = count($spkArr);
         if ((int)$do['id_ambil'] === 0 && $cancel === 0 && $spkCount > 0) {
            $ambilAll = false;
         }

         $spkStatusLines = [];
         foreach ($spkArr as $idDiv => $dv) {
            $status = (int)($dv['status'] ?? 0);
            $cm = (int)($dv['cm'] ?? 0);
            $cmStatus = (int)($dv['cm_status'] ?? 0);
            $done = ($status === 1 && ($cm !== 1 || $cmStatus === 1));
            $divName = isset($this->dDvs_all[$idDiv]['divisi']) ? $this->dDvs_all[$idDiv]['divisi'] : (isset($this->dDvs[$idDiv]['divisi']) ? $this->dDvs[$idDiv]['divisi'] : ('D-' . $idDiv));
            $spkStatusLines[] = [
               'divisi' => $divName,
               'done' => $done,
               'status' => $status,
               'cm' => $cm,
               'cm_status' => $cmStatus,
            ];
            if ($cancel === 0 && !$done) {
               $spkPending[$divName] = true;
            }
         }

         $lineBill = 0;
         if ($cancel === 0 && (int)$do['stok'] === 0) {
            $paketQty = isset($do['paket_qty']) && $do['paket_qty'] > 0 ? (int)$do['paket_qty'] : 1;
            $lineBill = (int)(($do['harga'] * $do['jumlah']) + ($do['harga_paket'] * $paketQty));
            $listDetail = @unserialize($do['detail_harga']);
            $akumDiskon = 0;
            if (is_array($listDetail)) {
               foreach ($listDetail as $ld) {
                  $akumDiskon += isset($ld['d']) ? (int)$ld['d'] : 0;
               }
            }
            $lineBill -= $akumDiskon * (int)$do['jumlah'];
            $bill += $lineBill;
         }

         $orderLines[] = [
            'id' => (int)$do['id_order_data'],
            'produk' => $do['produk'] ?? '',
            'jumlah' => (int)$do['jumlah'],
            'harga' => (int)$do['harga'],
            'cancel' => $cancel,
            'tuntas' => (int)$do['tuntas'],
            'id_ambil' => (int)$do['id_ambil'],
            'stok' => (int)$do['stok'],
            'line_bill' => $lineBill,
            'spk' => $spkStatusLines,
            'insertTime' => $do['insertTime'] ?? '',
            'note' => $do['note'] ?? '',
         ];
      }

      foreach ($mutasi as $dm) {
         $itemCount++;
         if ($insertTime === '' && !empty($dm['insertTime'])) {
            $insertTime = $dm['insertTime'];
         }
         if ($idPelanggan === 0) {
            $idPelanggan = (int) $dm['id_target'];
         }
         if ($idPenerima === 0 && isset($dm['cs_id'])) {
            $idPenerima = (int) $dm['cs_id'];
         }
         if ($idUser === 0) {
            $idUser = (int) $dm['user_id'];
         }
         if ($idTokoNota === 0) {
            $idTokoNota = (int) $dm['id_sumber'];
         }
         if ((int)$dm['diskon'] > 0) {
            $hasDiskonItem = true;
         }

         $stat = (int)$dm['stat'];
         $lineBill = 0;
         if ($stat !== 2) {
            $paketQty = isset($dm['paket_qty']) && $dm['paket_qty'] > 0 ? (int)$dm['paket_qty'] : 1;
            $lineBill = (int)((($dm['harga_jual'] * $dm['qty']) + ($dm['harga_paket'] * $paketQty)) - ($dm['diskon'] * $dm['qty']));
            $bill += $lineBill;
         } else {
            $cancelCount++;
         }

         $barangName = '#' . $dm['id_barang'];
         $mb = $this->db(0)->get_where_row('master_barang', 'id = ' . (int)$dm['id_barang']);
         if (is_array($mb) && !empty($mb['id'])) {
            $barangName = strtoupper(trim(($mb['brand'] ?? '') . ' ' . ($mb['model'] ?? '') . ($mb['product_name'] ?? '')));
         }

         $mutasiLines[] = [
            'id' => (int)$dm['id'],
            'barang' => $barangName,
            'qty' => (int)$dm['qty'],
            'sn' => $dm['sn'] ?? '',
            'stat' => $stat,
            'tuntas' => (int)$dm['tuntas'],
            'line_bill' => $lineBill,
            'insertTime' => $dm['insertTime'] ?? '',
         ];
      }

      $kasLines = [];
      foreach ($kas as $dk) {
         $jenis = (int)$dk['jenis_transaksi'];
         if ($jenis === 4) {
            if ((int)$dk['status_mutasi'] !== 2) {
               $refundTotal += (int)$dk['jumlah'];
            }
         } else {
            if ((int)$dk['status_mutasi'] === 0 || (int)$dk['status_mutasi'] === 1) {
               $dibayarUi += (int)$dk['jumlah'];
            }
            if ((int)$dk['metode_mutasi'] === 1 && (int)$dk['status_mutasi'] === 1 && (int)$dk['status_setoran'] === 1) {
               $verifyPayment += (int)$dk['jumlah'];
            }
            if (in_array((int)$dk['metode_mutasi'], [2, 3, 4], true) && (int)$dk['status_mutasi'] === 1) {
               $verifyPayment += (int)$dk['jumlah'];
            }
         }

         $metod = 'Lain';
         switch ((int)$dk['metode_mutasi']) {
            case 1: $metod = 'Tunai'; break;
            case 2: $metod = 'NonTunai'; break;
            case 3: $metod = 'Afiliasi'; break;
            case 4: $metod = 'Saldo'; break;
         }
         $stLabel = 'Batal';
         switch ((int)$dk['status_mutasi']) {
            case 0: $stLabel = 'Office Checking'; break;
            case 1: $stLabel = 'OK'; break;
         }

         $kasLines[] = [
            'id' => (int)$dk['id_kas'],
            'jenis' => $jenis === 4 ? 'Refund' : 'Bayar',
            'metode' => $metod,
            'jumlah' => (int)$dk['jumlah'],
            'status' => $stLabel,
            'status_mutasi' => (int)$dk['status_mutasi'],
            'status_setoran' => (int)($dk['status_setoran'] ?? 0),
            'note' => $dk['note'] ?? '',
            'insertTime' => $dk['insertTime'] ?? '',
            'counts_verify' => ($jenis === 1) && (
               ((int)$dk['metode_mutasi'] === 1 && (int)$dk['status_mutasi'] === 1 && (int)$dk['status_setoran'] === 1)
               || (in_array((int)$dk['metode_mutasi'], [2, 3, 4], true) && (int)$dk['status_mutasi'] === 1)
            ),
         ];
      }

      $diskonTotal = 0;
      $diskonLines = [];
      foreach ($diskon as $ds) {
         if ((int)$ds['cancel'] === 0) {
            $diskonTotal += (int)$ds['jumlah'];
            $verifyPayment += (int)$ds['jumlah'];
            $dibayarUi += (int)$ds['jumlah'];
         }
         $diskonLines[] = $ds;
      }

      $chargeLines = $charge;
      $verifyKasKecil = true;
      foreach ($kasKecil as $kk) {
         if ((int)$kk['st'] !== 1) {
            $verifyKasKecil = false;
         }
      }

      $paymentOk = ($verifyPayment == $bill);
      $readyToTuntas = false;
      $flags = []; // ['level' => ok|warn|error|info, 'text' => ...]

      if ($paymentOk && $ambilAll && $verifyKasKecil) {
         if ($bill > 0 && $verifyPayment > 0) {
            $readyToTuntas = true;
            $flags[] = ['level' => 'ok', 'text' => 'Kriteria bisnis OK: pembayaran match + sudah ambil + kas kecil OK'];
         } elseif ($hasStok || $hasDiskonItem) {
            $readyToTuntas = true;
            $flags[] = ['level' => 'ok', 'text' => 'Kriteria bisnis OK: bill 0 (stok/diskon)'];
         } elseif ($itemCount > 0 && $itemCount === $cancelCount) {
            $readyToTuntas = true;
            $flags[] = ['level' => 'ok', 'text' => 'Kriteria bisnis OK: semua item cancel'];
         } else {
            $flags[] = ['level' => 'warn', 'text' => 'Bill 0 tapi belum memenuhi syarat stok/diskon/all-cancel'];
         }
      } else {
         if (!$paymentOk) {
            $diff = $bill - $verifyPayment;
            $flags[] = ['level' => 'error', 'text' => 'Pembayaran verify belum match (selisih Rp' . number_format($diff) . '). Tunai hanya dihitung jika sudah setor (status_setoran=1); NonTunai/Afiliasi/Saldo harus status OK.'];
         }
         if (!$ambilAll) {
            $flags[] = ['level' => 'error', 'text' => 'Masih ada order produksi yang belum diambil (id_ambil = 0)'];
         }
         if (!$verifyKasKecil) {
            $flags[] = ['level' => 'error', 'text' => 'Ada kas kecil yang belum valid (st <> 1)'];
         }
      }

      if (count($spkPending) > 0) {
         $flags[] = ['level' => 'warn', 'text' => 'SPK belum selesai di divisi: ' . implode(', ', array_keys($spkPending)) . ' (tidak menghalangi tuntas cron, info saja)'];
      }

      $tuntasInduk = is_array($dRef) ? (int)($dRef['tuntas'] ?? 0) : 0;

      // Diagnosa sistem cron (collation / antrian batch)
      $systemChecks = [];
      $baseUrl = PV::BASE_URL;
      $collationProbe = $this->db(0)->count_where(
         'master_mutasi',
         "`ref` <> '' AND CONVERT(`ref` USING utf8mb4) COLLATE utf8mb4_unicode_ci IN (SELECT CONVERT(`ref` USING utf8mb4) COLLATE utf8mb4_unicode_ci FROM `ref` WHERE tuntas = 1)"
      );
      $collationRaw = $this->db(0)->count_where(
         'master_mutasi',
         "`ref` <> '' AND `ref` IN (SELECT `ref` FROM `ref` WHERE tuntas = 1)"
      );

      // Ambil definisi kolom ref untuk SQL ALTER yang akurat
      $colDefs = [];
      foreach (['ref', 'order_data', 'master_mutasi'] as $tbl) {
         $row = $this->db(0)->get_where_row('INFORMATION_SCHEMA.COLUMNS', "TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $tbl . "' AND COLUMN_NAME = 'ref'");
         if (is_array($row) && !empty($row['COLUMN_TYPE'])) {
            $colDefs[$tbl] = [
               'type' => $row['COLUMN_TYPE'],
               'collation' => $row['COLLATION_NAME'] ?? '',
               'nullable' => strtoupper($row['IS_NULLABLE'] ?? '') === 'YES',
            ];
         }
      }

      $alterSql = [];
      foreach (['ref' => 'ref', 'order_data' => 'ref', 'master_mutasi' => 'ref'] as $tbl => $col) {
         $type = $colDefs[$tbl]['type'] ?? 'VARCHAR(20)';
         $nullSql = !empty($colDefs[$tbl]['nullable']) ? 'NULL' : 'NOT NULL';
         $alterSql[] = "ALTER TABLE `{$tbl}` MODIFY `{$col}` {$type} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci {$nullSql};";
      }
      $checkCollationSql = "SELECT TABLE_NAME, COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME\nFROM INFORMATION_SCHEMA.COLUMNS\nWHERE TABLE_SCHEMA = DATABASE()\n  AND COLUMN_NAME = 'ref'\n  AND TABLE_NAME IN ('ref','order_data','master_mutasi');";

      if (is_array($collationRaw)) {
         $errInfo = (string)($collationRaw['info'] ?? 'unknown');
         $systemChecks[] = [
            'level' => 'error',
            'code' => 'COLLATION',
            'title' => '1) Error collation kolom `ref`',
            'text' => $errInfo,
            'fix' => 'Samakan collation kolom `ref` di 3 tabel ke utf8mb4_unicode_ci.',
            'steps' => [
               'Cek collation saat ini (jalankan di MySQL):',
               $checkCollationSql,
               'Samakan collation (jalankan satu per satu, backup dulu):',
               implode("\n", $alterSql),
               'Deploy patch Cron yang memakai CONVERT/COLLATE (sudah ada di kode).',
               'Ulangi cron: ' . $baseUrl . 'Cron/run_cek_tuntas',
               'Buka ulang Analisa nota ini — error collation harus hilang.',
            ],
         ];
         $flags[] = ['level' => 'error', 'text' => 'Sistem: cron sync detail gagal karena collation — perbaiki dulu error #1 di section Sistem/Cron'];
      } elseif (is_array($collationProbe)) {
         $systemChecks[] = [
            'level' => 'warn',
            'code' => 'COLLATION_PATCH',
            'title' => '1) Probe collation (patched) masih gagal',
            'text' => (string)($collationProbe['info'] ?? 'unknown'),
            'fix' => 'Query dengan COLLATE masih error — cek hak akses / versi MySQL.',
            'steps' => [
               'Pastikan user DB punya SELECT ke tabel ref, order_data, master_mutasi.',
               'Jalankan SQL cek collation di atas.',
               'Jika perlu, tetap jalankan ALTER TABLE samakan collation.',
            ],
         ];
      } else {
         $systemChecks[] = [
            'level' => 'ok',
            'code' => 'COLLATION',
            'title' => '1) Collation join `ref`',
            'text' => 'Perbandingan antar-tabel OK' . (is_array($collationRaw) ? '' : ' (raw join juga OK atau sudah di-patch).'),
            'fix' => '',
            'steps' => [],
         ];
         // Tetap tampilkan info collation berbeda jika ada, meski query patched OK
         $collations = [];
         foreach ($colDefs as $tbl => $def) {
            if ($def['collation'] !== '') {
               $collations[$def['collation']][] = $tbl;
            }
         }
         if (count($collations) > 1) {
            $detail = [];
            foreach ($colDefs as $tbl => $def) {
               $detail[] = $tbl . '=' . $def['collation'];
            }
            $systemChecks[] = [
               'level' => 'warn',
               'code' => 'COLLATION_DIFF',
               'title' => '1b) Collation masih beda antar tabel',
               'text' => implode(', ', $detail),
               'fix' => 'Cron patched bisa jalan, tapi samakan collation agar query lain tidak rawan error.',
               'steps' => [
                  'Backup database.',
                  implode("\n", $alterSql),
                  'Verifikasi ulang dengan SQL cek collation.',
               ],
            ];
         }
      }

      if ($tuntasInduk === 1) {
         array_unshift($flags, ['level' => 'ok', 'text' => 'Ref induk sudah tuntas di DB']);
      } elseif ($readyToTuntas) {
         array_unshift($flags, [
            'level' => 'warn',
            'text' => 'Nota siap tuntas menurut kriteria bisnis, tapi status induk masih BELUM',
         ]);

         $batchLimit = 200;
         $antrian = $this->db(0)->count_where('ref', "tuntas = 0 AND CONVERT(`ref` USING utf8mb4) COLLATE utf8mb4_unicode_ci < CONVERT('" . $refEsc . "' USING utf8mb4) COLLATE utf8mb4_unicode_ci");
         if (is_array($antrian)) {
            $antrian = $this->db(0)->count_where('ref', "tuntas = 0 AND `ref` < '" . $refEsc . "'");
         }
         if (!is_array($antrian)) {
            $pos = (int)$antrian + 1;
            $cekUrl = $baseUrl . 'Cron/cek_tuntas/' . rawurlencode($ref);
            $cekPrintUrl = $baseUrl . 'Cron/cek_tuntas/' . rawurlencode($ref) . '/1';
            $runUrl = $baseUrl . 'Cron/run_cek_tuntas';

            $systemChecks[] = [
               'level' => $pos > $batchLimit ? 'warn' : 'warn',
               'code' => 'QUEUE',
               'title' => '2) Antrian cron cek_tuntas',
               'text' => 'Posisi kira-kira #' . number_format($pos) . ' dari ref yang belum tuntas (cron max ' . $batchLimit . ' per eksekusi, urut ASC).',
               'fix' => $pos > $batchLimit
                  ? 'Nota ini di luar batch 200 pertama — tuntaskan manual dulu, atau naikkan batch / kurangi backlog.'
                  : 'Sudah di rentang batch. Jika cron tetap 0 ORDER TUNTAS, paksa cek per-ref.',
               'steps' => [
                  'LANGKAH A — Tuntaskan nota ini saja sekarang (paling cepat): buka URL ' . $cekUrl,
                  'LANGKAH A2 — Debug detail hitung cron: buka ' . $cekPrintUrl,
                  'LANGKAH B — Setelah collation beres, jalankan batch: ' . $runUrl,
                  'LANGKAH C (opsional) — Naikkan batch di Cron.php: const CEK_TUNTAS_BATCH = 200 → 1000 (hati-hati beban server).',
                  'LANGKAH D — Kurangi backlog: cari/refaktor kenapa ada ~' . number_format($pos) . '+ ref belum tuntas (banyak order lama?).',
               ],
            ];
            $flags[] = [
               'level' => 'warn',
               'text' => 'Antri cron ~#' . number_format($pos) . ' — solusi cepat: jalankan Cron/cek_tuntas/' . $ref,
            ];
         }

         $systemChecks[] = [
            'level' => 'info',
            'code' => 'FORCE_TUNTAS',
            'title' => '3) Paksa analisa+tuntas nota ini',
            'text' => 'Karena kriteria bisnis sudah OK, nota ini bisa diproses langsung tanpa menunggu antrian.',
            'fix' => 'Jalankan cek_tuntas untuk ref ini.',
            'steps' => [
               'Buka: ' . $baseUrl . 'Cron/cek_tuntas/' . rawurlencode($ref),
               'Atau debug: ' . $baseUrl . 'Cron/cek_tuntas/' . rawurlencode($ref) . '/1',
               'Refresh Data Operasi / Analisa — Tuntas Induk harus jadi Ya.',
            ],
         ];
      } else {
         array_unshift($flags, ['level' => 'error', 'text' => 'Belum siap tuntas']);

         if (!$paymentOk) {
            $systemChecks[] = [
               'level' => 'error',
               'code' => 'PAYMENT',
               'title' => 'Pembayaran belum verify-match',
               'text' => 'Bill Rp' . number_format($bill) . ' vs Verify Rp' . number_format($verifyPayment),
               'fix' => 'Samakan pembayaran yang dihitung cron.',
               'steps' => [
                  'Cek baris pembayaran di section Keuangan: status harus OK (bukan Office Checking).',
                  'Jika Tunai: pastikan status_setoran = 1 (sudah disetor).',
                  'Jika NonTunai/Afiliasi: pastikan status_mutasi = 1 (bukan checking/batal).',
                  'Jika ada kelebihan/kekurangan bayar: perbaiki via Fix Bayar / bayar ulang / refund.',
               ],
            ];
         }
         if (!$ambilAll) {
            $systemChecks[] = [
               'level' => 'error',
               'code' => 'AMBIL',
               'title' => 'Belum ambil semua',
               'text' => 'Ada item produksi id_ambil = 0.',
               'fix' => 'Ambil barang/jasa di Data Operasi.',
               'steps' => [
                  'Di kartu nota, klik Ambil (Ambil Semua) setelah order ready.',
                  'Pastikan item produksi tidak cancel dan punya SPK.',
                  'Refresh Analisa — Ambil Semua harus OK.',
               ],
            ];
         }
         if (!$verifyKasKecil) {
            $systemChecks[] = [
               'level' => 'error',
               'code' => 'KAS_KECIL',
               'title' => 'Kas kecil belum valid',
               'text' => 'Ada baris kas_kecil dengan st <> 1.',
               'fix' => 'Selesaikan/validasi kas kecil terkait ref ini.',
               'steps' => [
                  'Cek section Kas Kecil di Analisa.',
                  'Validasi di modul Kas Kecil hingga st = 1.',
                  'Refresh Analisa.',
               ],
            ];
         }
      }

      $pelangganNama = '#' . $idPelanggan;
      if ($idPelanggan > 0) {
         $p = $this->db(0)->get_where_row('pelanggan', 'id_pelanggan = ' . $idPelanggan);
         if (is_array($p) && !empty($p['nama'])) {
            $pelangganNama = strtoupper($p['nama']) . ' (#' . $idPelanggan . ')';
         }
      }

      $csNama = $idPenerima > 0 && isset($this->dKaryawanAll[$idPenerima])
         ? ucwords($this->dKaryawanAll[$idPenerima]['nama']) . ' (#' . $idPenerima . ')'
         : ($idPenerima > 0 ? '#' . $idPenerima : '-');
      $csAffNama = $idUserAfiliasi > 0 && isset($this->dKaryawanAll[$idUserAfiliasi])
         ? ucwords($this->dKaryawanAll[$idUserAfiliasi]['nama']) . ' (#' . $idUserAfiliasi . ')'
         : ($idUserAfiliasi > 0 ? '#' . $idUserAfiliasi : '-');
      $creatorNama = '-';
      if ($idUser > 0) {
         $u = $this->db(0)->get_where_row('user', 'id_user = ' . $idUser);
         if (is_array($u) && !empty($u['nama'])) {
            $creatorNama = $u['nama'] . ' (#' . $idUser . ')';
         } else {
            $creatorNama = '#' . $idUser;
         }
      }

      $tokoNama = isset($this->dToko[$idTokoNota]) ? $this->dToko[$idTokoNota]['nama_toko'] : ('#' . $idTokoNota);
      $affNama = $idAfiliasi > 0
         ? (isset($this->dToko[$idAfiliasi]) ? $this->dToko[$idAfiliasi]['nama_toko'] : ('#' . $idAfiliasi))
         : '-';

      $data = [
         'ref' => $ref,
         'dRef' => is_array($dRef) ? $dRef : [],
         'insertTime' => $insertTime,
         'pelanggan' => $pelangganNama,
         'cs' => $csNama,
         'cs_aff' => $csAffNama,
         'creator' => $creatorNama,
         'toko' => $tokoNama,
         'afiliasi' => $affNama,
         'bill' => $bill,
         'dibayar_ui' => $dibayarUi,
         'verify_payment' => $verifyPayment,
         'sisa_ui' => $bill - $dibayarUi,
         'sisa_verify' => $bill - $verifyPayment,
         'refund_total' => $refundTotal,
         'diskon_total' => $diskonTotal,
         'ambil_all' => $ambilAll,
         'verify_kas_kecil' => $verifyKasKecil,
         'payment_ok' => $paymentOk,
         'ready_to_tuntas' => $readyToTuntas,
         'tuntas_induk' => $tuntasInduk,
         'flags' => $flags,
         'system_checks' => $systemChecks,
         'spk_pending' => array_keys($spkPending),
         'order_lines' => $orderLines,
         'mutasi_lines' => $mutasiLines,
         'kas_lines' => $kasLines,
         'diskon_lines' => $diskonLines,
         'charge_lines' => $chargeLines,
         'kas_kecil' => $kasKecil,
      ];

      $this->view(__CLASS__ . '/analisa', $data);
   }
}
