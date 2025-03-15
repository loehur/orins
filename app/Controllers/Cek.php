<?php

class Cek extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[103])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }
   }

   function order($ref = 0, $id_pelanggan = 0)
   {
      if ($ref == 0) {
         $ref = $_POST['ref'];
      }

      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");
      $data['barang'] = $this->db(0)->get('master_barang', 'id');
      $data['payment_account'] = $this->db(0)->get_where('payment_account', "id_toko = '" . $this->userData['id_toko'] . "' ORDER BY freq DESC", 'id');

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

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['charge'] = $this->db(0)->get_where('charge', $where, 'ref_transaksi', 1);
      } else {
         echo "<div class='row'><div class='col text-center'>Tidak ada data</div></div>";
         exit();
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
            $id_pelanggan = $dd['id_pelanggan'];
            break;
         }
      }

      foreach ($data['mutasi'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['cs_id'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            $id_pelanggan = $dd['id_target'];
            break;
         }
      }
      $data['id_pelanggan'] = $id_pelanggan;

      $this->view(__CLASS__ . "/order", $data);
   }
}
