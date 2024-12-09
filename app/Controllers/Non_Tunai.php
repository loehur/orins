<?php

class Non_Tunai extends Controller
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
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['pelanggan'] = $this->db(0)->get('pelanggan');

      $where = "metode_mutasi = 2 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $where = "metode_mutasi = 2 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 10";
      $data['kas_done'] = $this->db(0)->get_where('kas', $where);

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
         $ref = $this->db(0)->get_where_row("kas", $where_kas)['ref_transaksi'];
         $where = "ref = '" . $ref . "'";
         $this->db(0)->update("order_data", $set_, $where);

         $set = "note_batal = '" . $note . "', status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];
      }

      $where = "id_kas = " . $id;
      $update = $this->db(0)->update("kas", $set, $where_kas);

      echo $update['errno'];
   }

   function actionMulti()
   {
      $id = explode("_", $_POST['id']);
      $val = $_POST['val'];

      foreach ($id as $i) {
         $set = "status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];
         $where = "id_kas = " . $i;
         $update = $this->db(0)->update("kas", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }
   }
}
