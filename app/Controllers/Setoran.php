<?php

class Setoran extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pKasir)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
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
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'];
      $data['pelanggan'] = $this->model('M_DB_1')->get_where('pelanggan', $wherePelanggan);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '' ORDER BY id_kas DESC, id_client ASC";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND status_setoran = 2 ORDER BY id_kas DESC, id_client ASC";
      $data['kas_reject'] = $this->model('M_DB_1')->get_where('kas', $where);

      $cols = "ref_setoran, status_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND status_mutasi = 1 AND status_setoran = 0 AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran <> '' GROUP BY ref_setoran, status_setoran ORDER BY ref_setoran DESC LIMIT 30";
      $data['setor'] = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);

      $this->view($this->v_content, $data);
   }

   function setor()
   {
      $ref = date("Ymdhis") . rand(0, 9);
      $set = "ref_setoran = '" . $ref . "'";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = ''";
      $update = $this->model('M_DB_1')->update("kas", $set, $where);
      echo $update['errno'];
   }

   function setor_masalah()
   {
      $ref = date("Ymdhis") . rand(0, 9);
      $set = "ref_setoran = '" . $ref . "', status_setoran = 0";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND id_client <> 0 AND status_setoran = 2";
      $update = $this->model('M_DB_1')->update("kas", $set, $where);
      echo $update['errno'];
   }

   function cek($ref_setor)
   {
      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'];
      $data['pelanggan'] = $this->model('M_DB_1')->get_where('pelanggan', $wherePelanggan);

      $where = "metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '" . $ref_setor . "' ORDER BY id_kas DESC, id_client ASC";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

      $this->view($this->page . "/cek", $data);
   }

   function cancel()
   {
      $id = $_POST['id_kas'];
      $reason = $_POST['reason'];

      $where = "id_kas = " . $id;
      $set = "status_mutasi = 2, note_batal = '" . $reason . "'";
      $update = $this->model('M_DB_1')->update("kas", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }
}
