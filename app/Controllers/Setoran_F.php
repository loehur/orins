<?php

class Setoran_F extends Controller
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
         "title" => "Finance - Setoran"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $cols = "id_toko, ref_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "status_mutasi = 1 AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran <> '' AND status_setoran = 0 GROUP BY id_toko, ref_setoran, status_setoran";
      $data['setor'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1);

      $cols = "id_toko, ref_setoran, status_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "status_mutasi = 1 AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran <> '' AND status_setoran <> 0 GROUP BY id_toko, ref_setoran, status_setoran ORDER BY ref_setoran DESC LIMIT 20";
      $data['setor_done'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1);

      $this->view($this->v_content, $data);
   }

   function setor($status)
   {
      $ref = $_POST['ref'];
      $set = "status_setoran = " . $status . ", id_finance_setoran = " . $this->userData['id_user'];
      $where = "ref_setoran = '" . $ref . "'";
      $update = $this->db(0)->update("kas", $set, $where);
      echo $update['errno'];
   }

   function cek($ref_setor)
   {
      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'];
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan);

      $where = "metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '" . $ref_setor . "' ORDER BY id_kas DESC, id_client ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $this->view(__CLASS__ . "/cek", $data);
   }
}
