<?php

class SPK_C extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pProduksi)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index($dvs)
   {
      foreach ($this->dDvs as $dv) {
         if ($dv['id_divisi'] == $dvs) {
            $t = $dv['divisi'];
         }
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "SPK_C - " . $t
      ]);

      $this->viewer($dvs);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "", $date = "")
   {
      $data['parse'] = $parse;
      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC";
      $data['karyawan'] = $this->model('M_DB_1')->get_where('karyawan', $whereKaryawan);

      $dvs = '"D-' . $parse . '"';

      $TD = date("Y-m-d");
      if ($date <> "") {
         $TD = $date;
      }

      $data['date'] = $TD;

      $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND insertTime LIKE '%" . $TD . "%' AND spk_dvs LIKE '%" . $dvs . "%' ORDER BY id_order_data DESC";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);

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
      $this->view($this->v_content, $data);
   }

   function done($id_divisi)
   {
      $karyawan = $_POST['id_karyawan'];
      //updateFreqPro
      $this->model('M_DB_1')->update("karyawan", "freq_pro = freq_pro+1", "id_karyawan = " . $karyawan);

      $id = $_POST['id'];
      $tahap = $_POST['mode'];
      $date = date("Y-m-d h:i:s");

      $where = "id_order_data = " . $id;
      $data = unserialize($this->model('M_DB_1')->get_where_row('order_data', $where)['spk_dvs']);

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
      $do = $this->model('M_DB_1')->update("order_data", $set, $where);
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
