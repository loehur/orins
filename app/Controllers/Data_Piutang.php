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

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Data Order - Piutang"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_ambil <> 0 AND tuntas = 0 AND cancel = 0 ORDER BY id_order_data DESC";
      $data['order'] = $this->db(0)->get_where('order_data', $where);

      $where = "id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND tuntas = 0 AND stat = 1 ORDER BY id DESC";
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where);

      $this->view($this->v_content, $data);
   }
}
