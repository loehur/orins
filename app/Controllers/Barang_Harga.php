<?php

class Barang_Harga extends Controller
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
         "title" => "Harga Barang"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data['barang'] = $this->db(0)->get_order('master_barang', 'id DESC');
      $data['grup'] = $this->db(0)->get('master_grup');
      $data['tipe'] = $this->db(0)->get('master_tipe');
      $data['brand'] = $this->db(0)->get('master_brand');
      $this->view($this->v_content, $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
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
      $model_c = strtoupper($_POST['model_c']);
      $model = strtoupper($_POST['model']);
      $varian1_c = strlen($_POST['varian1_c']) == 0 && strlen($_POST['varian1']) == 0 ? "00" : strtoupper($_POST['varian1_c']);
      $varian1 = strtoupper($_POST['varian1']);
      $varian2_c = strlen($_POST['varian2_c']) == 0 && strlen($_POST['varian2']) == 0 ? "00" : strtoupper($_POST['varian2_c']);
      $varian2 = strtoupper($_POST['varian2']);
      $sn = isset($_POST['sn']) ? $_POST['sn'] : 0;
      $pb = isset($_POST['pb']) ? $_POST['pb'] : 0;
      $code_gtb = $grup_c . $tipe_c . $brand_c;
      $code_model = $code_gtb . $model_c;
      $code_varian1 = $code_model . $varian1_c;
      $code_varian2 = $code_varian1 . $varian2_c;
      $code = $code_varian2;
      $code_s = "G-" . $grup_c . "T-" . $tipe_c . "B-" . $brand_c . "M-" . $model_c . "V1-" . $varian1_c . "V2-" . $varian2_c;
      if (strlen($code) < 12) {
         echo "Data belum lengkap";
         exit();
      }

      if (strlen($grup) == 0 || strlen($tipe) == 0 || strlen($brand) == 0 || strlen($model) == 0) {
         echo "Data belum lengkap";
         exit();
      }

      $error = 0;

      //GRUP
      $cols = 'id,nama';
      $vals = "'" . $grup_c . "','" . $grup . "'";
      $do = $this->db(0)->insertCols('master_grup', $cols, $vals);
      if ($do['errno'] == 1062) {
         $set = "nama = '" . $grup . "'";
         $where_grup = "id = '" . $grup_c . "'";
         $up = $this->db(0)->update('master_grup', $set, $where_grup);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
         //BARANG
         $set = "grup = '" . $grup . "'";
         $where = "code_s LIKE 'G-" . $grup_c . "'";
         $up = $this->db(0)->update('master_barang', $set, $where);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
      } else if ($do['errno'] <> 0) {
         $error .= $do['error'];
         echo $error;
         exit();
      }

      //TIPE
      $cols = 'id,nama';
      $vals = "'" . $tipe_c . "','" . $tipe . "'";
      $do = $this->db(0)->insertCols('master_tipe', $cols, $vals);
      if ($do['errno'] == 1062) {
         $set = "nama = '" . $tipe . "'";
         $where_tipe = "id = '" . $tipe_c . "'";
         $up = $this->db(0)->update('master_tipe', $set, $where_tipe);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
         //BARANG
         $set = "tipe = '" . $tipe . "'";
         $where = "code_s LIKE 'T-" . $tipe_c . "'";
         $up = $this->db(0)->update('master_barang', $set, $where);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
      } else if ($do['errno'] <> 0) {
         $error .= $do['error'];
         echo $error;
         exit();
      }

      //BRAND
      $cols = 'id,nama';
      $vals = "'" . $brand_c . "','" . $brand . "'";
      $do = $this->db(0)->insertCols('master_brand', $cols, $vals);
      if ($do['errno'] == 1062) {
         $set = "nama = '" . $brand . "'";
         $where_brand = "id = '" . $brand_c . "'";
         $up = $this->db(0)->update('master_brand', $set, $where_brand);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
         //BARANG
         $set = "brand = '" . $brand . "'";
         $where = "code_s LIKE 'B-" . $brand_c . "'";
         $up = $this->db(0)->update('master_barang', $set, $where);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
      } else if ($do['errno'] <> 0) {
         $error .= $do['error'];
         echo $error;
         exit();
      }

      //MODEL
      $cols = 'id,nama,code_gtb,code';
      $vals = "'" . $model_c . "','" . $model . "','" . $code_gtb . "','" . $code_model . "'";
      $do = $this->db(0)->insertCols('master_model', $cols, $vals);
      if ($do['errno'] == 1062) {
         $set = "nama = '" . $model . "'";
         $where_brand = "code = '" . $code_model . "'";
         $up = $this->db(0)->update('master_model', $set, $where_brand);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
         //BARANG
         $set = "model = '" . $model . "'";
         $where = "code_s LIKE 'M-" . $model_c . "'";
         $up = $this->db(0)->update('master_barang', $set, $where);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
      } else if ($do['errno'] <> 0) {
         $error .= $do['error'];
         echo $error;
         exit();
      }

      //VARIAN 1
      if (strlen($varian1) > 0 && strlen($varian1_c) > 0) {
         $cols = 'id,nama,code_model,code';
         $vals = "'" . $varian1_c . "','" . $varian1 . "','" . $code_model . "','" . $code_varian1 . "'";
         $do = $this->db(0)->insertCols('master_varian1', $cols, $vals);
         if ($do['errno'] == 1062) {
            $set = "nama = '" . $varian1 . "'";
            $where_varian1 = "code = '" . $code_varian1 . "'";
            $up = $this->db(0)->update('master_varian1', $set, $where_varian1);
            if ($up['errno'] <> 0) {
               $error .= $up['error'];
               echo $error;
               exit();
            }
            //BARANG
            $set = "varian1 = '" . $varian1 . "'";
            $where = "code_s LIKE 'V1-" . $varian1_c . "'";
            $up = $this->db(0)->update('master_barang', $set, $where);
            if ($up['errno'] <> 0) {
               $error .= $up['error'];
               echo $error;
               exit();
            }
         } else if ($do['errno'] <> 0) {
            $error .= $do['error'];
            echo $error;
            exit();
         }
      }

      //VARIAN 2
      if (strlen($varian1) > 0 && strlen($varian2) > 0 && strlen($varian1_c) > 0 && strlen($varian2_c) > 0) {
         $cols = 'id,nama,code_varian1,code';
         $vals = "'" . $varian2_c . "','" . $varian2 . "','" . $code_varian1 . "','" . $code_varian2 . "'";
         $do = $this->db(0)->insertCols('master_varian2', $cols, $vals);
         if ($do['errno'] == 1062) {
            $set = "nama = '" . $varian2 . "'";
            $where_varian2 = "code = '" . $code_varian2 . "'";
            $up = $this->db(0)->update('master_varian2', $set, $where_varian2);
            if ($up['errno'] <> 0) {
               $error .= $up['error'];
               echo $error;
               exit();
            }
            //BARANG
            $set = "varian2 = '" . $varian2 . "'";
            $where = "code_s LIKE 'V2-" . $varian2_c . "'";
            $up = $this->db(0)->update('master_barang', $set, $where);
            if ($up['errno'] <> 0) {
               $error .= $up['error'];
               echo $error;
               exit();
            }
         } else if ($do['errno'] <> 0) {
            $error .= $do['error'];
            echo $error;
            exit();
         }
      }

      //BARANG
      $cols = 'code,code_s,grup,tipe,brand,model,varian1,varian2,sn,pb';
      $vals = "'" . $code . "','" . $code_s . "','" . $grup . "','" . $tipe . "','" . $brand . "','" . $model . "','" . $varian1 . "','" . $varian2 . "'," . $sn . "," . $pb;
      $do = $this->db(0)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] == 1062) {
         $set = "sn = " . $sn . ", pb = " . $pb;
         $where = "code = '" . $code . "'";
         $up = $this->db(0)->update('master_barang', $set, $where);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
      } else if ($do['errno'] <> 0) {
         $error .= $do['error'];
         echo $error;
         exit();
      }

      echo $error;
   }
}
