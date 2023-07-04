<?php

class Non_Tunai extends Controller
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
         "title" => "Finance - Non Tunai"
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

      $where = "metode_mutasi = 2 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_kas ASC, id_client ASC";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

      $where = "metode_mutasi = 2 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY id_kas DESC, id_client ASC LIMIT 20";
      $data['kas_done'] = $this->model('M_DB_1')->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   function action()
   {
      $id = $_POST['id'];
      $val = $_POST['val'];

      $set = "status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];
      $where = "id_kas = " . $id;
      $update = $this->model('M_DB_1')->update("kas", $set, $where);
      echo $update['errno'];
   }
}
