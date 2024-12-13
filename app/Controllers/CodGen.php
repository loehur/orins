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
      $data['grup'] = $this->db(1)->get('master_grup');
      $data['tipe'] = $this->db(1)->get('master_tipe');
      $data['brand'] = $this->db(1)->get('master_brand');
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
      $grup_c = strtoupper($_POST['grup_c']);
      $grup = strtoupper($_POST['grup']);
      $tipe_c = strtoupper($_POST['tipe_c']);
      $tipe = strtoupper($_POST['tipe']);
      $brand_c = strtoupper($_POST['brand_c']);
      $brand = strtoupper($_POST['brand']);
      $c4_c = strtoupper($_POST['c4_c']);
      $c4 = strtoupper($_POST['c4']);
      $model_c = strtoupper($_POST['model_c']);
      $model = strtoupper($_POST['model']);
      $code_gtbc = $grup_c . $tipe_c . $brand_c . $c4_c;
      $code_model = $code_gtbc . $model_c;
      $code = $code_model;
      $code_s = "G-" . $grup_c . "#T-" . $tipe_c . "#B-" . $brand_c . "#C4-" . $c4_c . "#M-" . $model_c . "#";
      if (strlen($code) < 12) {
         echo "kode barang belum lengkap";
         exit();
      }

      if (strlen($grup) == 0 || strlen($tipe) == 0 || strlen($brand) == 0 || strlen($c4) == 0 || strlen($model) == 0) {
         echo "Data belum lengkap";
         exit();
      }

      $forbid = ['G-', 'T-', 'B-', 'M-', 'C4-', '#'];
      foreach ($forbid as $f) {
         if (str_contains($code, $f)) {
            echo "Character dilarang!";
            exit();
         }
      }

      //GRUP
      $tb = "master_grup";
      $cols = 'id,nama';
      $vals = "'" . $grup_c . "','" . $grup . "'";
      $do = $this->db(1)->insertCols($tb, $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $grup_c . "' AND nama = '" . $grup . "'");
            if ($cek == 0) {
               echo "Kode C1: " . $grup_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //TIPE
      $tb = "master_tipe";
      $cols = 'id,nama';
      $vals = "'" . $tipe_c . "','" . $tipe . "'";
      $do = $this->db(1)->insertCols($tb, $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $tipe_c . "' AND nama = '" . $tipe . "'");
            if ($cek == 0) {
               echo "Kode C2: " . $tipe_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //BRAND
      $tb = "master_brand";
      $cols = 'id,nama';
      $vals = "'" . $brand_c . "','" . $brand . "'";
      $do = $this->db(1)->insertCols('master_brand', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "id = '" . $brand_c . "' AND nama = '" . $brand . "'");
            if ($cek == 0) {
               echo "Kode C3: " . $brand_c . " sudah digunakan";
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
      $tb = "master_model";
      $cols = 'id,nama,code_gtb,code';
      $vals = "'" . $model_c . "','" . $model . "','" . $code_gtbc . "','" . $code_model . "'";
      $do = $this->db(1)->insertCols('master_model', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(1)->count_where($tb, "code = '" . $grup_c . $tipe_c . $brand_c . $c4_c . $model_c . "' AND nama = '" . $brand . "'");
            if ($cek == 0) {
               echo "Kode C5: " . $grup_c . $tipe_c . $brand_c . $model_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      //BARANG
      $cols = 'code,code_s,grup,tipe,brand,c4,model';
      $vals = "'" . $code . "','" . $code_s . "','" . $grup . "','" . $tipe . "','" . $brand . "','" . $c4 . "','" . $model . "'";
      $do = $this->db(1)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      echo 0;
   }

   function update_code()
   {
      //cek dulu
      $id = $_POST['id'];

      $value = $_POST['value'];
      $col = $_POST['col'];
      $parent = $_POST['parent'];
      $value_before = $_POST['value_before'];

      $mode = "NON";
      switch ($col) {
         case 1:
            $set = "id = '" . $value . "'";
            $where_grup = "id = '" . $value_before . "'";
            $up = $this->db(1)->update('master_grup', $set, $where_grup);
            if ($up['errno'] <> 0) {
               echo $col . $up['error'];
               exit();
            }

            $mode = 'G';
            break;
         case 2:
            $set = "id = '" . $value . "'";
            $where_tipe = "id = '" . $value_before . "'";
            $up = $this->db(1)->update('master_tipe', $set, $where_tipe);
            if ($up['errno'] <> 0) {
               echo $col . $up['error'];
               exit();
            }

            $mode = 'T';
            break;
         case 3:
            $set = "id = '" . $value . "'";
            $where_brand = "id = '" . $value_before . "'";
            $up = $this->db(1)->update('master_brand', $set, $where_brand);
            if ($up['errno'] <> 0) {
               echo $col . $up['error'];
               exit();
            }
            $mode = 'B';
            break;
         case 4:
            $set = "id = '" . $value . "'";
            $where_c4 = "id = '" . $value_before . "'";
            $up = $this->db(1)->update('master_c4', $set, $where_c4);
            if ($up['errno'] <> 0) {
               echo $col . $up['error'];
               exit();
            }
            $mode = 'C4';
            break;
         case 5:
            $set = "id = '" . $value . "', code = '" . $parent . $value . "'";
            $where_brand = "code = '" . $parent . $value_before . "'";
            $up = $this->db(1)->update('master_model', $set, $where_brand);
            if ($up['errno'] <> 0) {
               echo $col . $up['error'];
               exit();
            }
            $mode = 'M';
            break;
      }

      $data = $this->db(1)->get_where('master_barang', "code LIKE '" . $parent . $value_before . "%' AND code_s LIKE '%" . $mode . "-" . $value_before . "#%'");
      foreach ($data as $d) {
         $new_code_s = str_replace($mode . "-" . $value_before . "#", $mode . "-" . $value . "#", $d['code_s']);
         $new_code = str_replace(['G-', 'T-', 'B-', 'C4-', 'M-', '#'], '', $new_code_s);

         $set = "code_s = '" . $new_code_s . "', code = '" . $new_code . "'";
         $where = "code_s = '" . $d['code_s'] . "'";
         $up = $this->db(1)->update('master_barang', $set, $where);
         if ($up['errno'] <> 0) {
            echo "Barang" . $up['error'];
            exit();
         }
      }

      echo 0;
   }

   function update_name()
   {
      //cek dulu
      $id = $_POST['id'];
      $mode = $_POST['mode'];
      $value = $_POST['value'];
      $code = $_POST['code'];

      switch ($mode) {
         case 'M':
            $set = "nama = '" . $value . "'";
            $where_model = "code = '" . $code . "'";
            $up = $this->db(1)->update('master_model', $set, $where_model);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
      }

      $set = "model = '" . $value . "'";
      $where = "id = '" . $id . "'";
      $up = $this->db(1)->update('master_barang', $set, $where);
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }
      echo 0;
   }
}
