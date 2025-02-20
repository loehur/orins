<?php

class Setoran extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2])) {
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
         "title" => "Cashier - Setoran"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND status_setoran = 2 ORDER BY id_kas DESC, id_client ASC";
      $data['kas_reject'] = $this->db(0)->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '' ORDER BY id_kas DESC, id_client ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_client <> 0 AND ref_setoran = '' ORDER BY id_kas DESC, id_client ASC";
      $data['kas_trx'] = $this->db(0)->get_where('kas', $where, 'ref_transaksi', 1);

      $ref_trx = array_keys($data['kas_trx']);
      $data['nontunai_sds'] = 0;

      if (count($ref_trx) > 0) {
         $reft_list = "";
         foreach ($ref_trx as $r) {
            $reft_list .= $r . ",";
         }

         $reft_list = rtrim($reft_list, ',');
         $where_ref = "ref IN (" . $reft_list . ") AND sds = 1 AND stat = 1 AND jenis = 2 AND id_sumber = '" . $this->userData['id_toko'] . "'";
         $data['sds'] = $this->db(0)->get_where('master_mutasi', $where_ref, 'ref');

         $where_kas_sds = "ref_transaksi IN (" . $reft_list . ") AND sds = 1 AND metode_mutasi = 2 AND status_mutasi <> 2";
         $data['nontunai_sds'] = $this->db(0)->sum_col_where('kas', 'jumlah', $where_kas_sds);
      } else {
         $data['sds'] = [];
      }

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND jenis_mutasi = 2 AND ref_setoran = '' ORDER BY id_kas DESC";
      $data['pengeluaran'] = $this->db(0)->get_where('kas', $where);

      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'];
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan);

      //RIWAYAT
      $cols = "ref_setoran, status_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND status_mutasi = 1 AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran <> '' GROUP BY ref_setoran, status_setoran ORDER BY ref_setoran DESC LIMIT 20";
      $data['setor'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_setoran');
      $refs = array_keys($data['setor']);

      $ref_list = "0";
      if (count($refs) > 0) {
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');
      }

      $whereSplit = "ref IN (" . $ref_list . ") AND tipe = 0 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 1";
      $data['split'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereSplit = "ref IN (" . $ref_list . ") AND tipe = 0 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 0";
      $data['setor_office'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereSplit = "ref IN (" . $ref_list . ") AND tipe = 3 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 1";
      $data['sds_done'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $cols = "ref_setoran, status_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "status_mutasi <> 2 AND jenis_transaksi = 3 AND ref_setoran IN (" . $ref_list . ") GROUP BY ref_setoran";
      $data['keluar'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_setoran');

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');
      $this->view($this->v_content, $data);
   }

   function setor()
   {
      $ref = date("ymdhis") . rand(0, 9);
      $set = "ref_setoran = '" . $ref . "'";

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = ''";
      $data['kas_trx'] = $this->db(0)->get_where('kas', $where, 'ref_transaksi', 1);

      $ref_trx = array_keys($data['kas_trx']);
      $reft_list = "";
      foreach ($ref_trx as $r) {
         $reft_list .= $r . ",";
      }
      $reft_list = rtrim($reft_list, ',');
      $where_ref = "ref IN (" . $reft_list . ") AND sds = 1 AND stat = 1 AND jenis = 2 AND id_sumber = '" . $this->userData['id_toko'] . "'";
      $data['sds'] = $this->db(0)->get_where('master_mutasi', $where_ref, 'ref');

      $nontunai_sds = 0;
      $where_kas_sds = "ref_transaksi IN (" . $reft_list . ") AND sds = 1 AND status_mutasi <> 2";
      $nontunai_sds = $this->db(0)->sum_col_where('kas', 'jumlah', $where_kas_sds);

      $total_sds = 0;
      foreach ($data['sds'] as $ds) {
         $total_sds += (($ds['harga_jual'] - $ds['diskon']) * $ds['qty']);
      }

      if ($total_sds > 0) {
         $total_sds -= $nontunai_sds;
      }

      if ($total_sds > 0) {
         $unic = $ref . "31"; //tipe-target
         $cols = 'id, id_sumber, id_target, tipe, ref, jumlah';
         $vals = "'" . $unic . "'," . $this->userData['id_toko'] . ",1,3,'" . $ref . "','" . $total_sds . "'";
         $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      }

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = ''";
      $update = $this->db(0)->update("kas", $set, $where);
      if ($update['errno'] <> 0) {
         echo $update['error'];
         exit();
      } else {
         $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND jenis_transaksi = 3 AND ref_setoran = ''";
         $update = $this->db(0)->update("kas", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
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
      $jumlah_finance = $_POST['jumlah_finance'];


      if ($jumlah_finance > 0) {
         $unic = $ref . "00"; //tipe-target
         $cols = 'id, id_sumber, id_target, tipe, ref, jumlah, note';
         $vals =  "'" . $unic . "'," . $this->userData['id_toko'] . ",0,0,'" . $ref . "','" . $jumlah_finance . "','" . $note . "'";
         $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
         if ($do['errno'] == 1062) {
            $set = "jumlah = '" . $jumlah_finance . "', note = '" . $note . "'";
            $where = "id = '" . $unic . "'";
            $up = $this->db(0)->update('kas_kecil', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
         } else {
            if ($do['errno'] <> 0) {
               echo $do['error'];
               exit();
            }
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
