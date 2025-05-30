<?php

class Home extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();


      if ($this->userData['user_tipe'] == 9) {
         header('Location: ' . PV::BASE_URL . "Driver_JL");
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => __CLASS__
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC LIMIT 5";
      $cs = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC LIMIT 5";
      $pro = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $whereKaryawan =  "en = 1 ORDER BY freq_driver DESC LIMIT 5";
      $dr = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $data['cs'] = [];
      $data['cs_data'] = [];
      $data['pro'] = [];
      $data['pro_data'] = [];
      $data['dr'] = [];
      $data['dr_data'] = [];

      foreach ($cs as $c) {
         array_push($data['cs'], $c['nama']);
         array_push($data['cs_data'], $c['freq_cs']);
      }

      foreach ($pro as $c) {
         array_push($data['pro'], $c['nama']);
         array_push($data['pro_data'], $c['freq_pro']);
      }

      foreach ($dr as $c) {
         array_push($data['dr'], $c['nama']);
         array_push($data['dr_data'], $c['freq_driver']);
      }

      $this->view($this->v_content, $data);
   }
}
