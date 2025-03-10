<?php

class Office_Kas extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Office - Kas"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $whereSplit = "id_target = 0 AND (tipe = 0 OR tipe = 4) AND st = 0";
      $data['split'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereSplit = "id_target = 0 AND (tipe = 0 OR tipe = 4) AND st <> 0";
      $data['split_done'] = $this->db(0)->get_where('kas_kecil', $whereSplit, 'ref');

      $whereSplit = "id_sumber = 0 ORDER BY id DESC";
      $data['keluar_list'] = $this->db(0)->get_where('kas_kecil', $whereSplit);

      $whereSetor = "id_target = 0 AND (tipe = 0 OR tipe = 4) AND st = 1";
      $data['setor'] = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $whereSetor);

      $whereSetor = "id_sumber = 0 AND tipe = 1 AND st <> 2";
      $data['keluar'] = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $whereSetor);

      if (!$data['setor']) {
         $data['setor'] = 0;
      }

      if (!$data['keluar']) {
         $data['keluar'] = 0;
      }

      $data['saldo'] = $data['setor'] - $data['keluar'];
      $this->view(__CLASS__ . '/content', $data);
   }

   function verify_kasKecil($id, $status)
   {
      $set = "st = '" . $status . "'";
      $where = "id = '" . $id . "'";
      $update = $this->db(0)->update("kas_kecil", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   function topupPety()
   {
      $jumlah = $_POST['jumlah'];
      $target = $_POST['toko'];
      $ref = date('ymd');
      $cols = 'id_sumber, id_target, tipe, ref, jumlah, st';
      $vals =  "0," . $target . ",1,'" . $ref . "'," . $jumlah . ",0";
      $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
      if ($do['errno'] == 1062) {
         echo "data sudah di input";
         exit();
      } else {
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      }

      echo 0;
   }
}
