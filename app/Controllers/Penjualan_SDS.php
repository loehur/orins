<?php

class Penjualan_SDS extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($date = "")
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Penjualan SDS"
      ]);
      $this->viewer($date);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      if ($parse == "") {
         $parse = date("Y-m-d");
      }

      $where_ref = "sds = 1 AND insertTime LIKE '" . $parse . "%' AND stat = 1 AND jenis = 2 AND id_sumber = '" . $this->userData['id_toko'] . "'";
      $data['sds'] = $this->db(0)->get_where('master_mutasi', $where_ref);
      $refs = $this->db(0)->get_where('master_mutasi', $where_ref, 'ref', 1);

      $ref_trx = array_keys($refs);
      $reft_list = "";
      foreach ($ref_trx as $r) {
         if ($r == "") {
            $r = 0;
         }
         $reft_list .= $r . ",";
      }
      $reft_list = rtrim($reft_list, ',');
      $where = "ref_transaksi IN (" . $reft_list . ")";

      $data['nTunai'] = $this->db(0)->sum_col_where('kas', 'jumlah', $where);
      $data['xtra_diskon'] = $this->db(0)->sum_col_where('xtra_diskon', 'jumlah', $where);

      $tunai = 0;
      foreach ($data['sds'] as $ds) {
         $tunai += (($ds['harga_jual'] - $ds['diskon']) * $ds['qty']);
      }

      echo "<pre>";
      echo $data['nTunai'];
      echo "</pre>";

      $tunai -= $data['nTunai'];
      $data['tunai'] = $tunai;

      $data['date'] = $parse;
      $this->view($this->v_content, $data);
   }

   function setor($parse)
   {
      $ref = date("ymdhis") . rand(0, 9);
      $set = "ref_setoran = '" . $ref . "'";

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '' AND insertTime LIKE '" . $parse . "%'";
      $data['kas_trx'] = $this->db(0)->get_where('kas', $where, 'ref_transaksi', 1);

      $ref_trx = array_keys($data['kas_trx']);
      $reft_list = "";
      foreach ($ref_trx as $r) {
         if ($r == "") {
            $r = 0;
         }
         $reft_list .= $r . ",";
      }
      $reft_list = rtrim($reft_list, ',');

      $where_ref = "ref IN (" . $reft_list . ") AND sds = 1 AND stat = 1 AND jenis = 2 AND id_sumber = '" . $this->userData['id_toko'] . "'";
      $data['sds'] = $this->db(0)->get_where('master_mutasi', $where_ref);

      $nontunai_sds = 0;
      $where_kas_sds = "ref_transaksi IN (" . $reft_list . ") AND metode_mutasi <> 1 AND sds = 1 AND status_mutasi <> 2 AND id_toko = '" . $this->userData['id_toko'] . "'";
      $nontunai_sds = $this->db(0)->sum_col_where('kas', 'jumlah', $where_kas_sds);

      $total_sds = 0;
      foreach ($data['sds'] as $ds) {
         $total_sds += (($ds['harga_jual'] - $ds['diskon']) * $ds['qty']);
      }

      if ($total_sds > 0) {
         $total_sds -= $nontunai_sds;
      }

      if ($total_sds > 0) {
         $cols = 'id_sumber, id_target, tipe, ref, jumlah';
         $vals = $this->userData['id_toko'] . ",1,3,'" . $ref . "','" . $total_sds . "'";
         $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      }

      $where = "(jenis_transaksi = 1 OR jenis_transaksi = 2 OR jenis_transaksi = 3) AND id_toko = " . $this->userData['id_toko'] . " AND ref_setoran = '' AND insertTime LIKE '" . $parse . "%'";
      $update = $this->db(0)->update("kas", $set, $where);
      if ($update['errno'] <> 0) {
         echo $update['error'];
         exit();
      }

      echo 0;
   }

   function setor_masalah()
   {
      $ref = date("ymdhis") . rand(0, 9);
      $set = "ref_setoran = '" . $ref . "', status_setoran = 0";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND status_setoran = 2";
      $update = $this->db(0)->update("kas", $set, $where);
      echo $update['errno'];
   }

   function cek($ref_setor)
   {
      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'];
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan);

      $where = "metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '" . $ref_setor . "' ORDER BY id_kas DESC, id_client ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');
      $data['pengeluaran'] = [];
      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND jenis_mutasi = 2 AND ref_setoran = '" . $ref_setor . "' ORDER BY id_kas DESC";
      $data['pengeluaran'] = $this->db(0)->get_where('kas', $where);

      $this->view(__CLASS__ . "/cek", $data);
   }

   function split()
   {
      $ref = $_POST['ref'];
      $note = $_POST['note'];
      $tipe = $_POST['tipe'];
      $jumlah_finance = $_POST['jumlah_finance'];

      if ($jumlah_finance > 0) {

         $check = $this->db(0)->count_where("kas_kecil", "ref = '" . $ref . "' AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 0");
         if ($check == 0) {
            $cols = 'id_sumber, id_target, tipe, ref, jumlah, note';
            $vals =  $this->userData['id_toko'] . ",0," . $tipe . ",'" . $ref . "','" . $jumlah_finance . "','" . $note . "'";
            $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
            if ($do['errno'] <> 0) {
               echo $do['error'];
               exit();
            }
         } else {
            echo "Data sudah ada";
            exit();
         }
      }
      echo 0;
   }

   function cancel()
   {
      $id = $_POST['id_kas'];
      $reason = $_POST['reason'];

      $where = "id_kas = " . $id;
      $set = "status_mutasi = 2, note_batal = '" . $reason . "'";
      $update = $this->db(0)->update("kas", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function tambah_pengeluaran()
   {
      $jumlah = $_POST['jumlah'];
      $jenis = $_POST['jenis'];
      $note = $_POST['note'];

      $cols = "id_toko, jenis_transaksi, jenis_mutasi, ref_transaksi, metode_mutasi, status_mutasi, jumlah, id_user, id_client, note, ref_bayar, bayar, kembali";
      $vals = $this->userData['id_toko'] . ",3,2,'" . $jenis . "',1,1," . $jumlah . "," . $this->userData['id_user'] . ",0,'" . $note . "','',0,0";

      $do = $this->db(0)->insertCols('kas', $cols, $vals);
      if ($do['errno'] <> 0) {
         echo $do['error'];
      } else {
         echo 0;
      }
   }
}
