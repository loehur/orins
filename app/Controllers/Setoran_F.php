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
      $where = "id_toko = " . $this->userData['id_toko'] . " AND status_mutasi = 1 AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran <> '' AND status_setoran = 0 GROUP BY id_toko, ref_setoran, status_setoran";
      $data['setor'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_setoran');
      $refs = array_keys($data['setor']);

      $ref_list = "";
      if (count($refs) > 0) {
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');
      }

      $cols = "id_toko, ref_setoran, status_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND status_mutasi = 1 AND metode_mutasi = 1 AND id_client <> 0 AND ref_setoran <> '' AND status_setoran <> 0 GROUP BY ref_setoran ORDER BY ref_setoran DESC LIMIT 20";
      $data['setor_done'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_setoran');

      $refs_done = array_keys($data['setor_done']);
      if (count($refs_done) > 0) {
         $ref_list_done = "";
         foreach ($refs_done as $r) {
            $ref_list_done .= $r . ",";
         }
         $ref_list_done = rtrim($ref_list_done, ',');
      }

      if ($ref_list == "") {
         $ref_list = "0";
      }

      $cols = "ref_setoran, sum(refund) as jumlah";
      $where = "id_toko = " . $this->userData['id_toko'] . " AND refund_metod = 1 AND ref_setoran IN (" . $ref_list . "," . $ref_list_done . ") GROUP BY ref_setoran";
      $data['refund'] = $this->db(0)->get_cols_where('order_data', $cols, $where, 1, 'ref_setoran');

      $whereSplit = "ref IN (" . $ref_list . "," . $ref_list_done . ") AND tipe = 0 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 1";
      $data['split'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');
      $whereSplit = "ref IN (" . $ref_list . "," . $ref_list_done . ") AND tipe = 0 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 0";
      $data['setor_office'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');
      $whereSplit = "ref IN (" . $ref_list . "," . $ref_list_done . ") AND tipe = 3 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 1";
      $data['sds_done'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');
      $whereSplit = "ref IN (" . $ref_list . "," . $ref_list_done . ") AND tipe = 4 AND id_sumber = " . $this->userData['id_toko'] . " AND id_target = 0";
      $data['sds_tarik'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $cols = "ref_setoran, status_setoran, sum(jumlah) as jumlah, count(jumlah) as count";
      $where = "status_mutasi <> 2 AND jenis_transaksi = 3 AND ref_setoran IN (" . $ref_list . "," . $ref_list_done . ") GROUP BY ref_setoran";
      $data['keluar'] = $this->db(0)->get_cols_where('kas', $cols, $where, 1, 'ref_setoran');

      $this->view($this->v_content, $data);
   }

   function setor($status)
   {
      $ref = $_POST['ref'];
      $set = "status_setoran = " . $status . ", id_finance_setoran = " . $this->userData['id_user'];
      $where = "ref_setoran = '" . $ref . "' AND jenis_transaksi <> 3";
      $update = $this->db(0)->update("kas", $set, $where);
      if ($update['errno'] == 0) {
         $set = "st = " . $status . ", id_finance = " . $this->userData['id_user'];
         $where = "ref = '" . $ref . "' AND tipe = 3 AND id_target = 1";
         $update = $this->db(0)->update("kas_kecil", $set, $where);
         echo $update['errno'];
      };
   }

   function cek($ref_setor)
   {
      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'];
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan, 'id_pelanggan');

      $where = "metode_mutasi = 1 AND id_client <> 0 AND ref_setoran = '" . $ref_setor . "' ORDER BY id_kas DESC, id_client ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');
      $data['pengeluaran'] = [];
      $where = "id_toko = " . $this->userData['id_toko'] . " AND metode_mutasi = 1 AND jenis_mutasi = 2 AND ref_setoran = '" . $ref_setor . "' ORDER BY id_kas DESC";
      $data['pengeluaran'] = $this->db(0)->get_where('kas', $where);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_setoran = '" . $ref_setor . "'";
      $data['refund'] = $this->db(0)->get_where('order_data', $where);

      $this->view(__CLASS__ . "/cek", $data);
   }
}
