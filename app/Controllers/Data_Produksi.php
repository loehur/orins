<?php

class Data_Produksi extends Controller
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

   public function index($parse = "", $parse_2 = "")
   {
      $title = "Data Produksi";

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $title
      ]);

      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = "", $parse_2 = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $parse_2]);
   }

   public function content($parse = 0, $parse_2 = 0)
   {
      $data['ea'] = $this->db(0)->get('expedisi_account', 'id');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');

      $where = "tuntas = 0 AND ready_cs = 0";
      $data['data_ref'] = $this->db(0)->get_where('ref', $where, 'ref');
      $refs = array_keys($data['data_ref']);

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $where = "ref IN (" . $ref_list . ") AND id_ambil = 0 AND tuntas = 0 AND (id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND insertTime NOT LIKE '" . date("Y-m-d") . "%' AND cancel = 0 ORDER BY insertTime ASC";
         $data['order'] = $this->db(0)->get_where('order_data', $where, 'ref', 1);

         foreach ($data['order'] as $ref => $list) {
            foreach ($list as $val) {
               if ($val['id_afiliasi'] == $this->userData['id_toko'] && $val['ready_aff_cs'] <> 0) {
                  unset($data['order'][$ref]);
               }
            }
         }

         $where = "ref IN (" . $ref_list . ") AND tuntas = 0 AND (id_toko = " . $this->userData['id_toko'] . ")";
         $data['cs_id'] = $this->db(0)->get_where('order_data', $where, 'id_penerima');

         $where = "ref IN (" . $ref_list . ") AND tuntas = 0 AND (id_afiliasi = " . $this->userData['id_toko'] . ") AND id_user_afiliasi <> 0 AND insertTime NOT LIKE '" . date("Y-m-d") . "%' AND cancel = 0";
         $data['cs_id_aff'] = $this->db(0)->get_where('order_data', $where, 'id_user_afiliasi');
      }


      $data['cs'] = array_keys($data['cs_id']) + array_keys($data['cs_id_aff']);
      $this->view($this->v_content, $data);
   }

   function ready()
   {
      $ref = $_POST['ref'];
      $id_karyawan = $_POST['staf_id'];
      $expedisi = $_POST['expedisi'];
      $up =  $this->data('Operasi')->ready($ref, $id_karyawan, $expedisi);
      echo $up['errno'] <> 0 ? $up['error'] : 0;
   }
}
