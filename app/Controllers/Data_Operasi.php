<?php

class Data_Operasi extends Controller
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
      $data['parse'] = $parse;
      $data['parse_2'] = $parse_2;
      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['saldo'] = $this->data("Saldo")->deposit($parse);
      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");

      $data['barang'] = $this->db(0)->get('master_barang', 'code');

      if ($parse_2 < 2023) {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $parse . " AND tuntas = 0";
         $where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND id_target = " . $parse . " AND tuntas = 0";
      } else {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $parse . " AND tuntas = 1 AND insertTime LIKE '%" . $parse_2 . "%'";
         $where_mutasi = "id_sumber = " . $this->userData['id_toko'] . " AND id_target = " . $parse . " AND tuntas = 0 AND insertTime LIKE '%" . $parse_2 . "%'";
      }

      $data['order'] = [];
      $data['mutasi'] = [];
      if ($parse <> "" && $parse <> 0) {
         $data['order'] = $this->db(0)->get_where('order_data', $where);
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_mutasi);
      }

      $ref1 = array_unique(array_column($data['order'], 'ref'));
      $ref2 = array_unique(array_column($data['mutasi'], 'ref'));
      $refs = array_unique(array_merge($ref1, $ref2));

      $ref_list = "";
      foreach ($refs as $r) {
         $ref_list .= $r . ",";
      }
      $ref_list = rtrim($ref_list, ',');

      if (count($refs) > 0) {
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where);

         $cols = "ref_bayar, metode_mutasi, sum(jumlah) as total, sum(bayar) as bayar, sum(kembali) as kembali, status_mutasi";
         $where_2 = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ") GROUP BY ref_bayar";
         $data['r_kas'] = $this->db(0)->get_cols_where('kas', $cols, $where_2, 1);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where);
      }

      $data_ = [];
      $data['mode'] = 0;
      foreach ($data['order'] as $key => $do) {
         if ($do['id_afiliasi'] <> 0) {
            $data['mode'] = 1;
         }
         $data_[$do['ref']][$key] = $do;
      }

      $data_m = [];
      foreach ($data['mutasi'] as $key => $do) {
         $data_m[$do['ref']][$key] = $do;
      }

      rsort($refs);
      $data['refs'] = $refs;
      $data['order'] = $data_;
      $data['mutasi'] = $data_m;
      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan);

      foreach ($refs as $r) {
         $data['head'][$r]['cs_to'] = 0;
         $data['head'][$r]['id_afiliasi'] = 0;
      }

      foreach ($data['order'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['id_penerima'];
            $data['head'][$ref]['cs_to'] = $dd['id_user_afiliasi'];
            $data['head'][$ref]['id_afiliasi'] = $dd['id_afiliasi'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            break;
         }
      }

      foreach ($data['mutasi'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['cs_id'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            break;
         }
      }

      $this->view($this->v_content, $data);
   }

   public function clearTuntas()
   {
      $today = date("Y-m-d");
      if (isset($_POST['data'])) {
         $data = unserialize($_POST['data']);
         foreach ($data as $a) {
            $set = "tuntas = 1, tuntas_date = '" . $today . "'";
            $where = "ref = '" . $a . "'";
            $this->db(0)->update("order_data", $set, $where);
            $this->db(0)->update("master_mutasi", $set, $where);
         }
      }
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
      $ref_bayar = date("Ymdhis") . rand(0, 9);

      if (strlen($note) == 0 && $metode == 2) {
         $note = "Non_Tunai";
      } elseif (strlen($note) == 0 && $metode == 3) {
         $note = "Afiliasi";
      }

      $error = 0;
      ksort($ref_multi);
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

         $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND metode_mutasi = " . $metode . " AND status_mutasi = " . $status_mutasi;
         $dataCount = $this->db(0)->count_where('kas', $whereCount);

         $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar, bayar, kembali";
         $vals = $this->userData['id_toko'] . ",1,1,'" . $ref . "'," . $metode . "," . $status_mutasi . "," . $jumlah . "," . $this->userData['id_user'] . "," . $client . ",'" . $note . "','" . $ref_bayar . "'," . $bayarnya . "," . $kembalian;

         if ($dataCount < 1) {
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

   function xtraDiskon()
   {
      $ref = $_POST['ref_diskon'];
      $jumlah = $_POST['diskon'];
      $max = $_POST['max_diskon'];

      if ($jumlah > $max || $jumlah == 0) {
         echo "Jumlah Diskon tidak di izinkan!";
         exit();
      }

      $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah;
      $dataCount = $this->db(0)->count_where('xtra_diskon', $whereCount);

      $cols = "id_toko, ref_transaksi, jumlah, id_user";
      $vals = $this->userData['id_toko'] . ",'" . $ref . "'," . $jumlah . "," . $this->userData['id_user'];

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
}