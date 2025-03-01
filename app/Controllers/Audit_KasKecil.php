<?php

class Audit_KasKecil extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[6])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Audit - Kas Kecil"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $whereSplit = "id_target = 1 AND tipe = 0 AND st = 0 AND id_sumber = " . $this->userData['id_toko'] . " AND ref_setoran = ''";
      $data['split'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereSplit = "id_target = 1 AND tipe = 0 AND st <> 0 AND id_sumber = " . $this->userData['id_toko'];
      $data['split_done'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereReim = "tipe = 5 AND ref_setoran = '' AND id_sumber = " . $this->userData['id_toko'];
      $data['reim_done'] = $this->db(0)->get_where('kas_kecil', $whereReim);

      $whereSetor = "id_target = 1 AND tipe = 0 AND st = 1 AND ref_setoran = '' AND id_sumber = " . $this->userData['id_toko'];
      $data['setor'] = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $whereSetor);

      $whereSetor = "tipe = 5 AND st <> 2 AND ref_setoran = '' AND id_sumber = " . $this->userData['id_toko'];
      $data['reimburse'] = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $whereSetor);

      if (!$data['setor']) {
         $data['setor'] = 0;
      }

      if (!$data['reimburse']) {
         $data['reimburse'] = 0;
      }

      $data['setor'] += $data['reimburse'];

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND jenis_transaksi = 3 AND status_setoran = 0 AND status_mutasi = 1 ORDER BY id_kas DESC";
      $data['pengeluaran'] = $this->db(0)->get_where('kas', $where, 'ref_setoran', 1);

      $data['pengeluaran_done'] = [];
      // $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND jenis_transaksi = 3 AND status_setoran <> 0 ORDER BY id_kas DESC";
      // $data['pengeluaran_done'] = $this->db(0)->get_where('kas', $where, 'ref_setoran', 1);
      $this->view(__CLASS__ . '/content', $data);
   }

   function setor_pengeluaran($id, $status)
   {
      $set = "status_setoran = " . $status . ", id_finance_setoran = " . $this->userData['id_user'];
      $where = "id_kas = '" . $id . "'";
      $update = $this->db(0)->update("kas", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   function reimburse($id)
   {
      $where = "id_kas = '" . $id . "'";
      $get = $this->db(0)->get_where_row("kas", $where);

      $ref = $get['id_kas'];
      $cols = 'id_sumber, id_target, tipe, ref, jumlah, st, note';
      $vals =  "'" . $this->userData['id_toko'] . "','" . $get['ref_transaksi'] . "',5,'" . $ref . "'," . $get['jumlah'] . ",0,'" . $get['note'] . "'";

      $cek = $this->db(0)->count_where("kas_kecil", "jumlah = " . $get['jumlah'] . " AND ref = '" . $ref . "' AND tipe = 5");
      if ($cek == 0) {
         $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      } else {
         echo "Data sudah di reimburse";
         exit();
      }

      $set = "status_setoran = 1, id_finance_setoran = " . $this->userData['id_user'];
      $where = "id_kas = '" . $id . "'";
      $update = $this->db(0)->update("kas", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   function verify_kasKecil($id, $status)
   {
      $set = "st = '" . $status . "'";
      $where = "id = '" . $id . "'";
      $update = $this->db(0)->update("kas_kecil", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   function setor()
   {
      $ref = date("ymdhis") . rand(0, 9);
      $set = "ref_setoran = '" . $ref . "'";

      $where = "(tipe = 0) AND st <> 0 AND id_sumber = " . $this->userData['id_toko'] . " AND ref_setoran = ''";
      $update = $this->db(0)->update("kas_kecil", $set, $where);
      if ($update['errno'] <> 0) {
         echo $update['error'];
         exit();
      }

      $where = "(tipe = 5) AND id_sumber = " . $this->userData['id_toko'] . " AND ref_setoran = ''";
      $update = $this->db(0)->update("kas_kecil", $set, $where);
      if ($update['errno'] <> 0) {
         echo $update['error'];
         exit();
      }

      echo 0;
   }
}
