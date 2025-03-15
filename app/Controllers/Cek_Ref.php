<?php

class Cek_Ref extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[1])) {
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
         "title" => "Data Order - Referensi"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $this->view($this->v_content);
   }

   public function cek_data()
   {
      $from = $_POST['from'];
      $to = $_POST['to'];
      $data['order'] = [];
      $data['mutasi'] = [];

      $from_t = strtotime($from);
      $to_t = strtotime($to);
      $datediff = $to_t - $from_t;
      $jumlahHari = round($datediff / (60 * 60 * 24));

      if ($jumlahHari < 32) {
         $data['barang'] = $this->db(0)->get('master_barang', 'id');
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND (insertTime BETWEEN '" . $from . "' AND '" . $to . "') ORDER BY id_order_data DESC";
         $where2 = "(id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND (insertTime BETWEEN '" . $from . "' AND '" . $to . "') ORDER BY id DESC";
         $data['order'] = $this->db(0)->get_where('order_data', $where, 'ref', 1);
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where2, 'ref', 1);
      }

      $this->view(__CLASS__ . "/data", $data);
   }

   public function cek_rekap_1()
   {
      $from = $_POST['from'];
      $to = $_POST['to'];
      $data['order'] = [];

      $from_t = strtotime($from);
      $to_t = strtotime($to);

      $from = date("Y-m-d", $from_t);
      $to = date("Y-m-d", $to_t);

      $datediff = $to_t - $from_t;
      $jumlahHari = round($datediff / (60 * 60 * 24));

      if ($jumlahHari < 32) {
         $data['barang'] = $this->db(0)->get('master_barang', 'id');
         $cols = "id_produk, SUM(jumlah) as qty, SUM(jumlah*harga) as jumlah";
         $where = "cancel = 0 AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND (SUBSTR(insertTime, 1, 10) BETWEEN '" . $from . "' AND '" . $to . "') GROUP BY id_produk ORDER BY id_order_data DESC";
         $data['order'] = $this->db(0)->get_cols_where('order_data', $cols, $where);
      }

      $data['produk'] = $this->db(0)->get('produk', 'id_produk');
      $data['range'] = $_POST;
      $this->view(__CLASS__ . "/rekap", $data);
   }

   public function cek_rekap_2()
   {
      $from = $_POST['from'];
      $to = $_POST['to'];
      $data['mutasi'] = [];

      $from_t = strtotime($from);
      $to_t = strtotime($to);

      $from = date("Y-m-d", $from_t);
      $to = date("Y-m-d", $to_t);

      $datediff = $to_t - $from_t;
      $jumlahHari = round($datediff / (60 * 60 * 24));
      $data['barang'] = [];
      if ($jumlahHari < 32) {
         $data['barang'] = $this->db(0)->get('master_barang', 'id');
         $cols2 = "id_barang, SUM(qty) as qty, SUM(qty*harga_jual) as jumlah";
         $where2 = "(id_sumber = " . $this->userData['id_toko'] . ") AND id_target <> 0 AND jenis = 2 AND stat = 1 AND (SUBSTR(insertTime, 1, 10) BETWEEN '" . $from . "' AND '" . $to . "') GROUP BY id_barang ORDER BY id DESC";
         $data['mutasi'] = $this->db(0)->get_cols_where('master_mutasi', $cols2, $where2);
      }

      $data['range'] = $_POST;
      $this->view(__CLASS__ . "/rekap2", $data);
   }
}
