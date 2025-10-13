<?php

class CodGen extends Controller
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
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "CodGen"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data['barang'] = $this->db(1)->get_where('master_barang', "code <> '' ORDER BY id DESC");
      $data['c1'] = $this->db(1)->get('master_c1');
      $data['c2'] = $this->db(1)->get('master_c2');
      $data['c3'] = $this->db(1)->get('master_c3');
      $data['c4'] = $this->db(1)->get('master_c4');
      $this->view($this->v_content, $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(1)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function add()
   {
      $code = "";
      $c1_c = strtoupper($_POST['c1_c']);
      $c1 = strtoupper($_POST['c1']);
      $c2_c = strtoupper($_POST['c2_c']);
      $c2 = strtoupper($_POST['c2']);
      $c3_c = strtoupper($_POST['c3_c']);
      $c3 = strtoupper($_POST['c3']);
      $c4_c = strtoupper($_POST['c4_c']);
      $c4 = strtoupper($_POST['c4']);
      $c5_c = strtoupper($_POST['c5_c']);
      $c5 = strtoupper($_POST['c5']);
      $code_1234 = $c1_c . $c2_c . $c3_c . $c4_c;
      $code_full = $code_1234 . $c5_c;
      $code = $code_full;
      $code_s = "C1-" . $c1_c . "#C2-" . $c2_c . "#C3-" . $c3_c . "#C4-" . $c4_c . "#C5-" . $c5_c . "#";
      if (strlen($code) < 12) {
         echo "kode barang belum lengkap";
         exit();
      }

      if (strlen($c1) == 0 || strlen($c2) == 0 || strlen($c3) == 0 || strlen($c4) == 0 || strlen($c5) == 0) {
         echo "Data belum lengkap";
         exit();
      }

      $forbid = ['C1-', 'C2-', 'C3-', 'C4-', 'C5-', '#'];
      foreach ($forbid as $f) {
         if (str_contains($code, $f)) {
            echo "Character dilarang!";
            exit();
         }
      }

      //GRUP
      $tb = "master_c1";
      $cols = 'id,nama';
      $vals = "'" . $c1_c . "','" . $c1 . "'";
      $do = $this->db(1)->insertCols($tb, $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $c1_c . "' AND nama = '" . $c1 . "'");
            if ($cek == 0) {
               echo "Kode C1: " . $c1_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //TIPE
      $tb = "master_c2";
      $cols = 'id,nama';
      $vals = "'" . $c2_c . "','" . $c2 . "'";
      $do = $this->db(1)->insertCols($tb, $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $c2_c . "' AND nama = '" . $c2 . "'");
            if ($cek == 0) {
               echo "Kode C2: " . $c2_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //BRAND
      $tb = "master_c3";
      $cols = 'id,nama';
      $vals = "'" . $c3_c . "','" . $c3 . "'";
      $do = $this->db(1)->insertCols('master_c3', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $c3_c . "' AND nama = '" . $c3 . "'");
            if ($cek == 0) {
               echo "Kode C3: " . $c3_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //c4
      $tb = "master_c4";
      $cols = 'id,nama';
      $vals = "'" . $c4_c . "','" . $c4 . "'";
      $do = $this->db(1)->insertCols('master_c4', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $c4_c . "' AND nama = '" . $c4 . "'");
            if ($cek == 0) {
               echo "Kode C4: " . $c4_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //MODEL
      $tb = "master_c5";
      $cols = 'id,nama,code_1234,code';
      $vals = "'" . $c5_c . "','" . $c5 . "','" . $code_1234 . "','" . $code_full . "'";
      $do = $this->db(1)->insertCols('master_c5', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "code = '" . $c1_c . $c2_c . $c3_c . $c4_c . $c5_c . "' AND nama = '" . $c3 . "'");
            if ($cek == 0) {
               echo "Kode C5: " . $c1_c . $c2_c . $c3_c . $c5_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //BARANG
      $cols = 'code,code_s,c1,c2,c3,c4,c5';
      $vals = "'" . $code . "','" . $code_s . "','" . $c1 . "','" . $c2 . "','" . $c3 . "','" . $c4 . "','" . $c5 . "'";
      $do = $this->db(1)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      echo 0;
   }

   function update_name()
   {
      //cek dulu
      $mode = $_POST['mode'];
      $value = strtoupper($_POST['value']);
      $code_s = $_POST['code_s'];

      switch ($mode) {
         case 'c1':
            $set = "nama = '" . $value . "'";
            $id = $this->getStringBetween($code_s, "C1-", "#");
            $where_b = "code_s LIKE '%" . strtoupper($mode) . "-" . $id . "#%'";
            $where = "id = '" . $id . "'";
            $up = $this->db(1)->update('master_c1', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
         case 'c2':
            $set = "nama = '" . $value . "'";
            $id = $this->getStringBetween($code_s, "C2-", "#");
            $where_b = "code_s LIKE '%" . strtoupper($mode) . "-" . $id . "#%'";
            $where = "id = '" . $id . "'";
            $up = $this->db(1)->update('master_c2', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
         case 'c3':
            $set = "nama = '" . $value . "'";
            $id = $this->getStringBetween($code_s, "C3-", "#");
            $where_b = "code_s LIKE '%" . strtoupper($mode) . "-" . $id . "#%'";
            $where = "id = '" . $id . "'";
            $up = $this->db(1)->update('master_c3', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
         case 'c4':
            $set = "nama = '" . $value . "'";
            $id = $this->getStringBetween($code_s, "C4-", "#");
            $where_b = "code_s LIKE '%" . strtoupper($mode) . "-" . $id . "#%'";
            $where = "id = '" . $id . "'";
            $up = $this->db(1)->update('master_c4', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
         case 'c5':
            $set = "nama = '" . $value . "'";
            $where_b = "code = '" . $code_s . "'";
            $up = $this->db(1)->update('master_c5', $set, $where_b);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
      }

      $set = $mode . " = '" . $value . "'";
      $up = $this->db(1)->update('master_barang', $set, $where_b);
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }

      echo 0;
   }

   function delete()
   {
      $id = $_POST['id'];
      if (!is_numeric($id)) {
         echo "Data tidak valid";
         exit();
      }

      $where = "id = '" . $id . "'";
      $do = $this->db(1)->delete_where('master_barang', $where);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      } else {
         echo 0;
      }
   }
}
