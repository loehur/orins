<?php

class Data_Operasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

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
      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');
      $data['ea'] = $this->db(0)->get('expedisi_account', 'id');

      $data['parse'] = $parse;
      $data['parse_2'] = $parse_2;
      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');

      if ($this->dToko[$this->userData['id_toko']]['produksi'] == 1) {
         $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      } else {
         $data['pelanggan'] = $this->db(0)->get_where('pelanggan', 'id_toko = ' . $this->userData['id_toko'], 'id_pelanggan');
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

   public function bayar_multi()
   {
      if (isset($_POST['ref_multi'])) {
         $ref_multi = $_POST['ref_multi'];
      } else {
         echo "Tidak pembayaran yang di pilih";
         exit();
      }

      $dibayar = $_POST['dibayar_multi'];

      $count_ref = count($ref_multi);
      if ($count_ref == 0) {
         echo "Tidak pembayaran yang di pilih";
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
            echo "Silahkan pilih akun pembayaran";
            exit();
         } else {
            $payment_account = $_POST['payment_account'];
            $dPa = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "'", 'id');
            $sds = $dPa[$payment_account]['sds'];
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

      $error = 0;
      asort($ref_multi);
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

         if ($dataCount == 0) {
            $do = $this->db(0)->insertCols('kas', $cols, $vals);
            if ($do['errno'] == 0) {
               $dibayar -= $jumlah;
               $error = $do['errno'];
            } else {
               echo $do['error'];
               exit();
            }
         }
      }

      echo $error;
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
}
