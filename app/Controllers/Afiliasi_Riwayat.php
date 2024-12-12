<?php

class Afiliasi_Riwayat extends Controller
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
         "title" => "Audit - Afiliasi Riwayat"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      if ($parse == "") {
         $month = date("Y-m-d");
      } else {
         $month = $parse;
      }

      $data['m'] = $month;
      $data['pelanggan'] = $this->db(0)->get('pelanggan');
      $data['_c'] = __CLASS__;

      $where = "insertTime LIKE '%" . $month . "%' AND metode_mutasi = 3 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC";
      $data['kas_done'] = $this->db(0)->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   function action()
   {
      $id = $_POST['id'];
      $val = $_POST['val'];
      $note = $_POST['note'];

      if ($val == 1) {
         $set = "tuntas = 0, note_office = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
      } else {
         $set = "tuntas = 0, note_batal = '" . $note . "', status_mutasi = " . $val . ", id_audit_afiliasi = " . $this->userData['id_user'];
      }
      $where = "id_kas = " . $id;
      $update = $this->db(0)->update("kas", $set, $where);
      echo $update['errno'];
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
