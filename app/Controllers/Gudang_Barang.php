<?php

class Gudang_Barang extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[7])) {
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
         "title" => "Gudang - Barang"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data['barang'] = $this->db(0)->get_where('master_barang', "en = 1 ORDER BY id DESC");
      $data['grup'] = $this->db(0)->get('master_grup');
      $data['tipe'] = $this->db(0)->get('master_tipe');
      $data['brand'] = $this->db(0)->get('master_brand');
      $data['stok'] = $this->data('Barang')->stok_data_list_all(0);
      $this->view($this->v_content, $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function update_model($kode)
   {
      if (strlen($kode) == 6) {
         $get = $this->db(0)->get_where("master_barang", "code LIKE '" . $kode . "%'");
         foreach ($get as $d) {
            $id = substr($d['code'], -3);
            $code_gtb = $kode;
            $code = $code_gtb . $id;
            $nama = strtoupper($d['model']);

            $cols = "id, code_gtb, code, nama";
            $vals = "'" . $id . "','" . $code_gtb . "','" . $code . "','" . $nama . "'";
            $in = $this->db(0)->insertCols("master_model", $cols, $vals);
            if ($in['errno'] = 1062) {
               $set = "id = '" . $id . "', code_gtb = '" . $code_gtb . "', nama = '" . $nama . "'";
               $up = $this->db(0)->update("master_model", $set, "code = '" . $code . "'");
               if ($up['errno'] <> 0) {
                  print_r($up['error']);
                  exit();
               }
            } else {
               if ($in['errno'] <> 0) {
                  print_r($in['error']);
                  exit();
               }
            }
         }
      }
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
      $model_c = strtoupper($_POST['model_c']);
      $model = strtoupper($_POST['model']);
      $sn = isset($_POST['sn']) ? $_POST['sn'] : 0;
      $pb = isset($_POST['pb']) ? $_POST['pb'] : 0;
      $code_gtb = $grup_c . $tipe_c . $brand_c;
      $code_model = $code_gtb . $model_c;
      $code = $code_model;
      $code_s = "G-" . $grup_c . "#T-" . $tipe_c . "#B-" . $brand_c . "#M-" . $model_c . "#";
      if (strlen($code) < 9) {
         echo "kode barang belum lengkap";
         exit();
      }

      if (strlen($grup) == 0 || strlen($tipe) == 0 || strlen($brand) == 0 || strlen($model) == 0) {
         echo "Data belum lengkap";
         exit();
      }

      $forbid = ['G-', 'T-', 'B-', 'M-', '#'];
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
      $do = $this->db(0)->insertCols($tb, $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(0)->count_where($tb, "id = '" . $grup_c . "' AND nama = '" . $grup . "'");
            if ($cek == 0) {
               echo "Kode Grup: " . $grup_c . " sudah digunakan";
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
      $do = $this->db(0)->insertCols($tb, $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(0)->count_where($tb, "id = '" . $tipe_c . "' AND nama = '" . $tipe . "'");
            if ($cek == 0) {
               echo "Kode Tipe: " . $tipe_c . " sudah digunakan";
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
      $do = $this->db(0)->insertCols('master_brand', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(0)->count_where($tb, "id = '" . $brand_c . "' AND nama = '" . $brand . "'");
            if ($cek == 0) {
               echo "Kode Brand: " . $brand_c . " sudah digunakan";
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
      $vals = "'" . $model_c . "','" . $model . "','" . $code_gtb . "','" . $code_model . "'";
      $do = $this->db(0)->insertCols('master_model', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $cek = $this->db(0)->count_where($tb, "code = '" . $grup_c . $tipe_c . $brand_c . $model_c . "' AND nama = '" . $model . "'");
            if ($cek == 0) {
               echo "Kode Model: " . $grup_c . $tipe_c . $brand_c . $model_c . " sudah digunakan";
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      $code_f = strtoupper($_POST['code_f']);

      //BARANG
      $cols = 'code,code_s,grup,tipe,brand,model,sn,pb,code_f';
      $vals = "'" . $code . "','" . $code_s . "','" . $grup . "','" . $tipe . "','" . $brand . "','" . $model . "','" . $sn . "'," . $pb . ",'" . $code_f . "'";
      $do = $this->db(0)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      echo 0;
   }

   function cek_barang($id)
   {
      $data['stok'] = $this->data('Barang')->stok_data($id, 0);
      $this->view(__CLASS__ . "/data_cek", $data);
   }

   function update_code()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];
      $col = $_POST['col'];
      $parent = $_POST['parent'];
      $value_before = $_POST['value_before'];

      $mode = "NON";
      switch ($col) {
         case 1:
            $where_grup = "id = '" . $value . "'";
            $cek = $this->db(0)->get_where_row('master_grup', $where_grup);
            if (isset($cek['nama'])) {
               $set_m = ", grup = '" . $cek['nama'] . "'";
            } else {
               echo "Not found grup-code " . $value;
               exit();
            }
            $mode = 'G';
            break;
         case 2:
            $where_tipe = "id = '" . $value . "'";
            $cek = $this->db(0)->get_where_row('master_tipe', $where_tipe);
            if (isset($cek['nama'])) {
               $set_m = ", tipe = '" . $cek['nama'] . "'";
            } else {
               echo "Not found tipe-Code " . $value;
               exit();
            }
            $mode = 'T';
            break;
         case 3:
            $where_brand = "id = '" . $value . "'";
            $cek = $this->db(0)->get_where_row('master_brand', $where_brand);
            if (isset($cek['nama'])) {
               $set_m = ", brand = '" . $cek['nama'] . "'";
            } else {
               echo "Not found brand-Code " . $value;
               exit();
            }
            $mode = 'B';
            break;
         case 4:
            $set = "id = '" . $value . "', code = '" . $parent . $value . "'";
            $where_brand = "code = '" . $parent . $value_before . "'";
            $up = $this->db(0)->update('master_model', $set, $where_brand);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            $set_m = "";
            $mode = 'M';
            break;
      }

      $data = $this->db(0)->get_where('master_barang', "id = '" . $id . "'");
      foreach ($data as $d) {
         $new_code_s = str_replace($mode . "-" . $value_before . "#", $mode . "-" . $value . "#", $d['code_s']);
         $new_code = str_replace(['G-', 'T-', 'B-', 'M-', '#'], '', $new_code_s);

         $cek_ada = $this->db(0)->count_where('master_barang', "code = '" . $new_code . "'");
         if ($cek_ada > 0) {
            echo "Kode " . $new_code . " telah digunakan";
            exit();
         } else {
            $where = "id = '" . $d['id'] . "'";
            $set = "code_s = '" . $new_code_s . "', code = '" . $new_code . "'" . $set_m;
            $up = $this->db(0)->update('master_barang', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
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
            $up = $this->db(0)->update('master_model', $set, $where_model);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
      }

      $set = "model = '" . $value . "'";
      $where = "id = '" . $id . "'";
      $up = $this->db(0)->update('master_barang', $set, $where);
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }
      echo 0;
   }

   function update_head()
   {
      //cek dulu
      $mode = strtoupper($_POST['mode']);
      $value = $_POST['value'];
      $code = $_POST['code'];

      switch ($mode) {
         case 'T':
            $set = "nama = '" . $value . "'";
            $where = "id = '" . $code . "'";
            $up = $this->db(0)->update('master_tipe', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }

            $set = "tipe = '" . strtoupper($value) . "'";
            $where = "code_s LIKE '%#" . $mode . "-" . $code . "#%'";
            $up = $this->db(0)->update('master_barang', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
         case 'G':
            $set = "nama = '" . $value . "'";
            $where = "id = '" . $code . "'";
            $up = $this->db(0)->update('master_grup', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }

            $set = "grup = '" . strtoupper($value) . "'";
            $where = "code_s LIKE '%" . $mode . "-" . $code . "#%'";
            $up = $this->db(0)->update('master_barang', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
         case 'B':
            $set = "nama = '" . $value . "'";
            $where = "id = '" . $code . "'";
            $up = $this->db(0)->update('master_brand', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }

            $set = "brand = '" . strtoupper($value) . "'";
            $where = "code_s LIKE '%" . $mode . "-" . $code . "#%'";
            $up = $this->db(0)->update('master_barang', $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
            break;
      }

      echo 0;
   }

   public function update_pbsn()
   {
      $id = $_POST['id'];
      $col = $_POST['col'];
      $val = $_POST['val'];

      $where = "id = " . $id;
      $set = $col . " = " . $val;
      $update = $this->db(0)->update("master_barang", $set, $where);
      echo $update['errno'];
   }
}
