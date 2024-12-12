<?php

class SPK_L extends Controller
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

   public function index($dvs)
   {
      foreach ($this->dDvs as $dv) {
         if ($dv['id_divisi'] == $dvs) {
            $t = $dv['divisi'];
         }
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "SPK - Lanjutan " . $t
      ]);

      $this->viewer($dvs);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['parse'] = $parse;
      $data['pelanggan'] = $this->db(0)->get('pelanggan');

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $dvs = '"D-' . $parse . '"';

      $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND spk_lanjutan LIKE '%D-" . $parse . "#%' AND spk_dvs LIKE '%" . $dvs . "%' ORDER BY id_order_data DESC";
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
      $this->view($this->v_content, $data);
   }
}
