<?php

class Afiliasi extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pAudit)) {
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
         "title" => "Audit - Afiliasi"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');
      $data['_c'] = __CLASS__;
      $where = "metode_mutasi = 3 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

      $where = "metode_mutasi = 3 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 20";
      $data['kas_done'] = $this->model('M_DB_1')->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   function action()
   {
      $id = $_POST['id'];
      $val = $_POST['val'];
      $note = $_POST['note'];

      $where_kas = "id_kas = " . $id;

      if ($val == 1) {
         $set = "tuntas = 0, note_office = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
      } else {
         $set = "tuntas = 0, note_batal = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];

         $set_ = "tuntas = 0";
         $ref = $this->model('M_DB_1')->get_where_row("kas", $where_kas)['ref_transaksi'];
         $where = "ref = '" . $ref . "'";
         $this->model('M_DB_1')->update("order_data", $set_, $where);
      }

      $update = $this->model('M_DB_1')->update("kas", $set, $where_kas);
      echo $update['errno'];
   }

   function actionMulti()
   {
      $id = explode("_", $_POST['id']);
      $val = $_POST['val'];
      $note = $_POST['note'];

      foreach ($id as $i) {
         if ($val == 1) {
            $set = "tuntas = 0, note_office = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
         } else {
            $set = "tuntas = 0, note_batal = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
         }
         $where = "id_kas = " . $i;
         $update = $this->model('M_DB_1')->update("kas", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }
   }

   function cekOrder($ref)
   {
      $data['kas'] = [];
      $data['order'] = [];

      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');
      $data['karyawan'] = $this->model('M_DB_1')->get('karyawan');


      $where = "ref = '" . $ref . "'";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);

      $where = "ref_transaksi = '" . $ref . "'";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);


      $this->view($this->page . "/cek", $data);
   }
}
