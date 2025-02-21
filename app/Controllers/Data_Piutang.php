<?php

class Data_Piutang extends Controller
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

   public function index($id_pelanggan_jenis)
   {
      switch ($id_pelanggan_jenis) {
         case 1:
            $title = "Umum";
            break;
         case 2:
            $title = "R/D";
            break;
         case 3:
            $title = "Online";
            break;
         default:
            $title = "Error";
            break;
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Piutang - " . $title
      ]);

      $this->viewer($id_pelanggan_jenis);
   }

   public function viewer($id_pelanggan_jenis)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $id_pelanggan_jenis]);
   }

   public function content($id_pelanggan_jenis)
   {
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', "id_pelanggan_jenis = " . $id_pelanggan_jenis, 'id_pelanggan');

      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_pelanggan_jenis = " . $id_pelanggan_jenis . " AND tuntas = 0 AND cancel = 0 ORDER BY id_order_data DESC";
      $where2 = "id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND jenis_target = " . $id_pelanggan_jenis . " AND tuntas = 0 AND stat = 1 ORDER BY id DESC";

      $data['order'] = $this->db(0)->get_where('order_data', $where, 'ref', 1);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where2, 'ref', 1);

      $ref1 = array_keys($data['order']);
      $ref2 = array_keys($data['mutasi']);
      $refs = array_unique(array_merge($ref1, $ref2));

      $data['kas'] = [];
      $data['diskon'] = [];

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where, 'ref_transaksi', 1);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where, 'ref_transaksi', 1);
      }

      $data['refs'] = $refs;
      $this->view($this->v_content, $data);
   }
}
