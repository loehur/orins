<?php

class Buka_Order_Aff extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();

      if (!in_array($this->userData['user_tipe'], $this->pCS)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index($ref)
   {

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Afiliasi Order - " . $ref
      ]);

      $this->viewer($ref);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $where = "ref = '" . $parse . "' AND cancel = 0 AND id_user_afiliasi = 0";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);
      $data_harga = $this->model('M_DB_1')->get('produk_harga');

      $data['count'] = count($data['order']);

      foreach ($data['order'] as $key => $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $countDH[$key] = count($detail_harga);
         foreach ($detail_harga as $dh_o) {
            $getHarga[$key][$dh_o['c_h']] = 0;
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $do['id_pelanggan_jenis']] <> 0) {
                  $getHarga[$key][$dh_o['c_h']] = $dh['harga_' . $do['id_pelanggan_jenis']];
                  $countDH[$key] -= 1;
                  break;
               }
            }
         }

         if ($countDH[$key] == 0) {
            $data['order'][$key]['harga'] = array_sum($getHarga[$key]);
         }

         $data['pelanggan'] = $do['id_pelanggan'];
         $data['pelanggan_'] = $this->model('M_DB_1')->get('pelanggan');
         $data['pelanggan_nama'] = "";
         foreach ($data['pelanggan_'] as $pl) {
            if ($pl['id_pelanggan'] == $data['pelanggan']) {
               $data['pelanggan_nama'] = $pl['nama'];
            }
         }
         $data['pelanggan_jenis'] = $do['id_pelanggan_jenis'];
         $data['pengirim'] = $do['id_penerima'];
      }

      $data['parse'] = $parse;
      $data['karyawan'] = $this->model('M_DB_1')->get('karyawan');
      foreach ($data['karyawan'] as $dk) {
         if ($dk['id_karyawan'] == $data['pengirim']) {
            $data['pengirim'] = $dk['nama'];
         }
      }

      $this->view($this->v_content, $data);
   }

   function proses($ref, $id_pelanggan_jenis)
   {
      $id_karyawan = $_POST['id_karyawan'];
      $where = "ref = '" . $ref . "' AND cancel = 0";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);
      $data_harga = $this->model('M_DB_1')->get('produk_harga');

      $c_cart = count($data['order']);
      $error = 0;
      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $harga = 0;
         foreach ($detail_harga as $key => $dh_o) {
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $id_pelanggan_jenis] <> 0) {
                  $harga +=  $dh['harga_' . $id_pelanggan_jenis];
                  $detail_harga[$key]['h'] = $dh['harga_' . $id_pelanggan_jenis];
                  break;
               }
            }
         }
         $where = "id_order_data = " . $do['id_order_data'];
         $set = "detail_harga = '" . serialize($detail_harga) . "', harga = " . $harga . ", id_user_afiliasi = " . $id_karyawan . ", status_order = 0";
         $update = $this->model('M_DB_1')->update("order_data", $set, $where);
         $error = $update['errno'];
      }

      if ($error == 0) {
         echo 1;
      }
   }
}
