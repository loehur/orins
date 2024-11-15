<?php

class Non_Tunai_Riwayat extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pFinance)) {
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
         "title" => "Finance - Non Tunai Riwayat"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      if ($parse == "") {
         $month = date("Y-m");
      } else {
         $month = $parse;
      }

      $data['m'] = $month;
      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');
      $where = "insertTime LIKE '%" . $month . "%' AND metode_mutasi = 2 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC";
      $data['kas_done'] = $this->model('M_DB_1')->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   function action()
   {
      $id = $_POST['id'];
      $val = $_POST['val'];
      $note = $_POST['note'];
      $where_kas = "id_kas = " . $id;
      $set = "status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];

      if ($val == 2) {
         $set_ = "tuntas = 0";
         $ref = $this->model('M_DB_1')->get_where_row("kas", $where_kas)['ref_transaksi'];
         $where = "ref = '" . $ref . "'";
         $this->model('M_DB_1')->update("order_data", $set_, $where);

         $set = "note_batal = '" . $note . "', status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];
      }

      $where = "id_kas = " . $id;
      $update = $this->model('M_DB_1')->update("kas", $set, $where_kas);

      echo $update['errno'];
   }

   function actionMulti()
   {
      $id = explode("_", $_POST['id']);
      $val = $_POST['val'];

      foreach ($id as $i) {
         $set = "status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];
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
