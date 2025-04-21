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
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['toko'] = $this->db(0)->get('toko', 'id_toko');
      $data['payment_account'] = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "' ORDER BY freq DESC", 'id');

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 2 AND id_client <> 0 AND status_mutasi = 0";
      $data['kas'] = $this->db(0)->get_where('kas', $where, 'ref_bayar', 1);

      $data['kas_group'] = [];
      $data['charge'] = [];
      $data['ref'] = [];

      $refs = array_keys($data['kas']);
      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $cols = "ref_bayar, note, SUM(jumlah) as jumlah";
         $where = "ref_bayar IN (" . $ref_list . ") GROUP BY ref_bayar";
         $data['kas_group'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_bayar');
      }

      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 2 AND id_client <> 0 AND status_mutasi <> 0 ORDER BY updateTime DESC LIMIT 20";
      $data['kas_trx'] = $this->db(0)->get_where('kas', $where, 'ref_transaksi', 1);
      $ref_trx = array_keys($data['kas_trx']);
      $reft_list = "";
      foreach ($ref_trx as $r) {
         $reft_list .= $r . ",";
      }
      $reft_list = rtrim($reft_list, ',');
      $where_ref = "ref IN (" . $reft_list . ")";
      $data['ref'] = $this->db(0)->get_where('ref', $where_ref, 'ref');

      $data['kas_done'] = $this->db(0)->get_where('kas', $where, 'ref_bayar', 1);
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
      $ref_bayar = $_POST['id'];
      $val = $_POST['val'];
      $note = $_POST['note'];

      $set = "note_batal = '" . $note . "', status_mutasi = " . $val . ", id_finance_nontunai = " . $this->userData['id_user'];

      $where = "ref_bayar = '" . $ref_bayar . "'";
      $update = $this->db(0)->update("kas", $set, $where);
      if ($update['errno'] <> 0) {
         echo $update['error'];
         exit();
      } else {
         echo 0;
      }
   }
}
