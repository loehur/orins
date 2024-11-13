<?php

class Non_Tunai_C extends Controller
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
         "title" => "Cashier - Non Tunai"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['c_'] = __CLASS__;
      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 2 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 2 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 20";
      $data['kas_done'] = $this->model('M_DB_1')->get_where('kas', $where);
      $this->view($this->v_content, $data);
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
