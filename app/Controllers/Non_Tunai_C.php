<?php

class Non_Tunai_C extends Controller
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
         "title" => "Cashier - Non Tunai"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['c_'] = __CLASS__;
      $data['pelanggan'] = $this->db(0)->get('pelanggan');

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 2 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 2 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 20";
      $data['kas_done'] = $this->db(0)->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   function cekOrder($ref)
   {
      $data['kas'] = [];
      $data['order'] = [];

      $data['pelanggan'] = $this->db(0)->get('pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan');

      $where = "ref = '" . $ref . "'";
      $data['order'] = $this->db(0)->get_where('order_data', $where);

      $where = "ref_transaksi = '" . $ref . "'";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $this->view(__CLASS__ . "/cek", $data);
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
         $ref = $this->db(0)->get_where_row("kas", $where_kas)['ref_transaksi'];
         $where = "ref = '" . $ref . "'";
         $this->db(0)->update("order_data", $set_, $where);

         $set = "note_batal = '" . $note . "', status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];
      }

      $where = "id_kas = " . $id;
      $update = $this->db(0)->update("kas", $set, $where_kas);

      echo $update['errno'];
   }
}
