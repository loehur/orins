<?php

class SPK_Customer extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
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
      $parse = $this->intParam($parse);
      $customer = $this->intParam($customer);

      $data['parse'] = $parse;
      $data['customer'] = $customer;
      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');

      if ($customer > 0) {
         $data['pelanggan'] = $this->db(0)->get_where('pelanggan', "id_pelanggan = " . $customer, 'id_pelanggan');
         if (!isset($data['pelanggan'][$customer])) {
            $customer = 0;
            $data['customer'] = 0;
            $data['pelanggan'] = [];
            $data['pelanggan_init'] = "[]";
         } else {
         $p = $data['pelanggan'][$customer];
         $data['pelanggan_init'] = json_encode([[
            'id' => $p['id_pelanggan'],
            'nama' => strtoupper($p['nama']),
            'no_hp' => $p['no_hp'],
            'inisial' => $this->dToko[$p['id_toko']]['inisial']
         ]]);
         $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC";
         $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan, 'id_karyawan');
         $data['karyawan_all'] = $this->db(0)->get_where('karyawan', 'en = 1', 'id_karyawan');

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
      } else {
         $data['pelanggan'] = [];
         $data['pelanggan_init'] = "[]";
      }
      $this->view($this->v_content, $data);
   }

   function search_pelanggan()
   {
      $q = trim($_GET['q'] ?? '');
      if (strlen($q) < 2) {
         echo json_encode([]);
         exit();
      }

      $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
      $where = "en = 1";
      if ($this->dToko[$this->userData['id_toko']]['produksi'] != 1) {
         $where .= " AND id_toko = " . (int)$this->userData['id_toko'];
      }
      foreach ($parts as $p) {
         $p = addslashes($p);
         $where .= " AND (nama LIKE '%" . $p . "%' OR no_hp LIKE '%" . $p . "%' OR id_pelanggan LIKE '%" . $p . "%')";
      }
      $res = $this->db(0)->get_where('pelanggan', $where);
      $data = [];
      foreach ($res as $p) {
         $data[] = [
            'id' => $p['id_pelanggan'],
            'nama' => strtoupper($p['nama']),
            'no_hp' => $p['no_hp'],
            'inisial' => $this->dToko[$p['id_toko']]['inisial']
         ];
      }
      echo json_encode($data);
   }
}
