<?php

class Buka_Order_Aff extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[3])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
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
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $where = "ref = '" . $parse . "' AND cancel = 0 AND id_penerima <> 0 AND id_user_afiliasi = 0 AND id_afiliasi = " . $this->userData['id_toko'];
      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data_harga = $this->db(0)->get('produk_harga');
      $data['count'] = count($data['order']);

      $getHarga = [];

      foreach ($data['order'] as $key => $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $countDH[$key] = count($detail_harga);
         foreach ($detail_harga as $dh_o) {
            $getHarga[$key][$dh_o['c_h']] = 0;
            foreach ($data_harga as $dh) {
               if (isset($dh['harga_' . $do['id_pelanggan_jenis']])) {
                  if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $do['id_pelanggan_jenis']] <> 0) {
                     $getHarga[$key][$dh_o['c_h']] = $dh['harga_' . $do['id_pelanggan_jenis']];
                     $countDH[$key] -= 1;
                     break;
                  }
               }
            }
         }

         if ($countDH[$key] == 0) {
            $data['order'][$key]['harga'] = array_sum($getHarga[$key]);
         }

         $data['pelanggan'] = $do['id_pelanggan'];
         $data['pelanggan_'] = $this->db(0)->get('pelanggan');
         $data['pelanggan_nama'] = "";
         foreach ($data['pelanggan_'] as $pl) {
            if ($pl['id_pelanggan'] == $data['pelanggan']) {
               $data['pelanggan_nama'] = $pl['nama'];
            }
         }
         $data['pelanggan_jenis'] = $do['id_pelanggan_jenis'];
         $data['pengirim'] = $do['id_penerima'];
      }

      $data['harga'] = $getHarga;
      $data['parse'] = $parse;
      $data['karyawan'] = $this->db(0)->get('karyawan');
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
      $id_pelanggan = $_POST['id_pelanggan'];
      //updateFreqCS
      $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);

      $where = "ref = '" . $ref . "' AND cancel = 0";
      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data_harga = $this->db(0)->get('produk_harga');

      $harga_code = $id_pelanggan_jenis;
      if ($id_pelanggan_jenis == 100) {
         $harga_code = 2;
      }
      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $harga = 0;
         foreach ($detail_harga as $key => $dh_o) {
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $harga_code] <> 0) {
                  $harga +=  $dh['harga_' . $harga_code];
                  $detail_harga[$key]['h'] = $dh['harga_' . $harga_code];
                  break;
               }
            }
         }

         if ($harga == 0) {
            echo "Error getting Harga";
            exit();
         }

         $where = "id_order_data = " . $do['id_order_data'];
         $set = "detail_harga = '" . serialize($detail_harga) . "', harga = " . $harga . ", id_user_afiliasi = " . $id_karyawan . ", status_order = 0";
         $up = $this->db(0)->update("order_data", $set, $where);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }

      echo $id_pelanggan;
   }
}
