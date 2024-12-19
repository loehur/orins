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

   function cekOrder($ref, $id_pelanggan)
   {
      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");
      $data['barang'] = $this->db(0)->get('master_barang', 'code');

      $where = "ref = '" . $ref . "'";
      $data['order'] = [];
      $data['mutasi'] = [];
      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where);
      $ref1 = array_unique(array_column($data['order'], 'ref'));
      $ref2 = array_unique(array_column($data['mutasi'], 'ref'));
      $refs = array_unique(array_merge($ref1, $ref2));

      $ref_list = "";
      foreach ($refs as $r) {
         $ref_list .= $r . ",";
      }
      $ref_list = rtrim($ref_list, ',');

      if (count($refs) > 0) {
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where);

         $cols = "ref_bayar, metode_mutasi, sum(jumlah) as total, sum(bayar) as bayar, sum(kembali) as kembali, status_mutasi";
         $where_2 = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ") GROUP BY ref_bayar";
         $data['r_kas'] = $this->db(0)->get_cols_where('kas', $cols, $where_2, 1);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where);
      }

      $data_ = [];
      $data['mode'] = 0;
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $data_m = [];
      foreach ($data['mutasi'] as $key => $do) {
         $data_m[$do['ref']][$key] = $do;
      }

      rsort($refs);
      $data['refs'] = $refs;
      $data['order'] = $data_;
      $data['mutasi'] = $data_m;
      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan);

      foreach ($refs as $r) {
         $data['head'][$r]['cs_to'] = 0;
         $data['head'][$r]['id_afiliasi'] = 0;
      }

      foreach ($data['order'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['id_penerima'];
            $data['head'][$ref]['cs_to'] = $dd['id_user_afiliasi'];
            $data['head'][$ref]['id_afiliasi'] = $dd['id_afiliasi'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            break;
         }
      }

      foreach ($data['mutasi'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['cs_id'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            break;
         }
      }

      $data['id_pelanggan'] = $id_pelanggan;

      $this->view(__CLASS__ . "/cek", $data);
   }
}
