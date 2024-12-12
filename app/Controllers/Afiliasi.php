<?php

class Afiliasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[8])) {
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
         "title" => "Audit - Afiliasi"
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
      $data['_c'] = __CLASS__;
      $where = "metode_mutasi = 3 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $where = "metode_mutasi = 3 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 20";
      $data['kas_done'] = $this->db(0)->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   function action()
   {
      $id = $_POST['id'];
      $val = $_POST['val'];
      $note = $_POST['note'];

      $where_kas = "id_kas = " . $id;

      if ($val == 1) {
         $set = "note_office = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
      } else {
         $set = "note_batal = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
      }

      $ref = $this->db(0)->get_where_row("kas", $where_kas)['ref_transaksi'];
      $where = "ref = '" . $ref . "'";
      $set_ = "tuntas = 0";
      $this->db(0)->update("order_data", $set_, $where);

      $update = $this->db(0)->update("kas", $set, $where_kas);
      echo $update['errno'];
   }

   function actionMulti()
   {
      $id = explode("_", $_POST['id']);
      $val = $_POST['val'];
      $note = $_POST['note'];

      foreach ($id as $i) {
         if ($val == 1) {
            $set = "note_office = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
         } else {
            $set = "note_batal = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
         }

         $where_kas = "id_kas = " . $i;
         $ref = $this->db(0)->get_where_row("kas", $where_kas)['ref_transaksi'];

         $where_ref = "ref = '" . $ref . "'";
         $set_ = "tuntas = 0";
         $this->db(0)->update("order_data", $set_, $where_ref);

         $update = $this->db(0)->update("kas", $set, $where_kas);
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

      $data['pelanggan'] = $this->db(0)->get('pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan');


      $where = "ref = '" . $ref . "'";
      $data['order'] = $this->db(0)->get_where('order_data', $where);

      $where = "ref_transaksi = '" . $ref . "'";
      $data['kas'] = $this->db(0)->get_where('kas', $where);


      $this->view(__CLASS__ . "/cek", $data);
   }
}
