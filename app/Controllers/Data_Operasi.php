<?php

class Data_Operasi extends Controller
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

   public function index($parse, $parse_2 = 0)
   {
      if ($parse_2 == 0) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Data Order Customer"
         ]);
      } else {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Data Order Tuntas"
         ]);
      }
      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = "", $parse_2)
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse, "parse_2" => $parse_2]);
   }

   public function content($parse = "", $parse_2 = 0)
   {
      $data['parse'] = $parse;
      $data['parse_2'] = $parse_2;
      $data['kas'] = [];
      $data['order'] = [];

      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');

      if ($parse_2 < 2023) {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $parse . " AND tuntas = 0 ORDER BY id_order_data DESC";
      } else {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $parse . " AND tuntas = 1 AND insertTime LIKE '%" . $parse_2 . "%' ORDER BY id_order_data DESC";
      }
      if ($parse <> "" && $parse <> 0) {
         $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);
      }

      $refs = array_column($data['order'], 'ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN '" . $min_ref . "' AND '" . $max_ref . "')";
         $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND (ref_transaksi BETWEEN '" . $min_ref . "' AND '" . $max_ref . "')";
         $data['diskon'] = $this->model('M_DB_1')->get_where('xtra_diskon', $where);
      }

      $data_ = [];
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $col = [];
      $actif_col = 1;
      $col[1] = 0;
      $col[2] = 0;

      $data_fix[1] = [];
      $data_fix[2] = [];

      foreach ($data_ as $key => $d) {
         if ($col[1] <= $col[2] + 1) {
            $actif_col = 1;
         } else {
            $actif_col = 2;
         }
         $col[$actif_col] += count($data_[$key]);

         $data_fix[$actif_col][$key] = $d;
      }
      $data['order'] = $data_fix;

      $this->view($this->v_content, $data);
   }

   public function clearTuntas()
   {
      if (isset($_POST['data'])) {
         $data = unserialize($_POST['data']);
         foreach ($data as $a) {
            $set = "tuntas = 1";
            $where = "ref = '" . $a . "'";
            $this->model('M_DB_1')->update("order_data", $set, $where);
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
      if (count($ref_multi) == 0) {
         echo "Tidak pembayaran yang di pilih";
         exit();
      }

      $note =  $_POST['note_multi'];
      $metode =  $_POST['metode_multi'];
      $ref_bayar = date("Ymdhis") . rand(0, 9);

      if (strlen($note) == 0 && $metode == 2) {
         $note = "Non_Tunai";
      }

      $error = 0;
      ksort($ref_multi);
      foreach ($ref_multi as $value) {

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
            case "1":
               $status_mutasi = 1;
               break;
            default:
               $status_mutasi = 0;
               break;
         }

         $whereCount = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND metode_mutasi = " . $metode . " AND status_mutasi = " . $status_mutasi;
         $dataCount = $this->model('M_DB_1')->count_where('kas', $whereCount);

         $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar";
         $vals = $this->userData['id_toko'] . ",1,1,'" . $ref . "'," . $metode . "," . $status_mutasi . "," . $jumlah . "," . $this->userData['id_user'] . "," . $client . ",'" . $note . "','" . $ref_bayar . "'";

         if ($dataCount < 1) {
            $do = $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
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
      $dataCount = $this->model('M_DB_1')->count_where('xtra_diskon', $whereCount);

      $cols = "id_toko, ref_transaksi, jumlah, id_user";
      $vals = $this->userData['id_toko'] . ",'" . $ref . "'," . $jumlah . "," . $this->userData['id_user'];

      if ($dataCount < 1) {
         $do = $this->model('M_DB_1')->insertCols('xtra_diskon', $cols, $vals);
         if ($do['errno'] == 0) {
            echo $do['errno'];
            $this->model('Log')->write($this->userData['user'] . " Extra Diskon " . $jumlah . " Success!");
         } else {
            echo $do['error'];
         }
      }
   }
}
