<?php

class SPK_Customer extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[4])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($parse, $parse_2 = 0)
   {
      foreach ($this->dDvs as $dv) {
         if ($dv['id_divisi'] == $parse) {
            $t = $dv['divisi'];
         }
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "SPK_Search - " . $t
      ]);

      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = 0, $parse_2 = 0)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse2" => $parse_2]);
   }

   public function content($parse = 0, $customer = 0)
   {
      $data['parse'] = $parse;
      $data['customer'] = $customer;
      $data['dPelanggan'] = $this->db(0)->get('pelanggan');

      if ($customer <> '') {
         $data['pelanggan'] = $this->db(0)->get_where('pelanggan', "id_pelanggan = " . $customer);
         $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC";
         $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan);

         $dvs = '"D-' . $parse . '"';

         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan = " . $customer . " AND cancel = 0 AND tuntas = 0 AND spk_dvs LIKE '%" . $dvs . "%' ORDER BY id_order_data DESC";
         $data['order'] = $this->db(0)->get_where('order_data', $where);

         $data_ = [];
         foreach ($data['order'] as $key => $do) {
            $data_[$do['ref']][$key] = $do;
         }
         $col = [];
         $actif_col = 1;
         $col[1] = 0;
         $col[2] = 0;

         $data_fix[1] = [];
         $data_fix[2] = [];

         foreach ($data_ as $key => $d) {
            if ($col[1] <= $col[2] + 1) {
               $actif_col = 1;
            } else {
               $actif_col = 2;
            }
            $col[$actif_col] += count($data_[$key]);

            $data_fix[$actif_col][$key] = $d;
         }
         $data['order'] = $data_fix;
      }
      $this->view($this->v_content, $data);
   }

   function done($id_divisi)
   {
      $karyawan = $_POST['id_karyawan'];
      $id = $_POST['id'];
      $tahap = $_POST['mode'];
      $date = date("Y-m-d h:i:s");

      $where = "id_order_data = " . $id;
      $data = unserialize($this->db(0)->get_where_row('order_data', $where)['spk_dvs']);

      //updateFreqPro
      $this->db(0)->update("karyawan", "freq_pro = freq_pro+1", "id_karyawan = " . $karyawan);

      if ($tahap == 1) {
         $data[$id_divisi]["status"] = 1;
         $data[$id_divisi]["user_produksi"] = $karyawan;
         $data[$id_divisi]["update"] = $date;
      } else {
         $data[$id_divisi]["cm_status"] = 1;
         $data[$id_divisi]["user_cm"] = $karyawan;
         $data[$id_divisi]["update_cm"] = $date;
      }

      $set = "spk_dvs = '" . serialize($data) . "'";
      $do = $this->db(0)->update("order_data", $set, $where);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " updateSPK Success!");
         echo $do['errno'];
      } else {
         $this->model('Log')->write($this->userData['user'] . " updateSPK" . $do['error']);
         print_r($do['error']);
         exit();
      }
   }
}
