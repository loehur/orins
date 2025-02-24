<?php

class Group_Detail extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[1])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($parse, $id_index = "")
   {
      $title = "Produk - Detail Produksi";
      if ($parse == 1) {
         $title = "Produk - Detail Jasa";
      }
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $title
      ]);

      $this->viewer($parse, $id_index);
   }

   public function viewer($parse, $id_index)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $id_index]);
   }

   public function content($parse, $id_index = "")
   {
      $data['pj'] = $parse;
      $data['id_index'] = $id_index;

      if ($parse == 0) {
         $data['main'] = $this->db(0)->get_where('detail_group', 'pj = ' . $parse . " ORDER BY id_index DESC");
      } else {
         $data['main'] = $this->db(0)->get_where('detail_group', 'pj = ' . $this->userData['id_toko'] . " ORDER BY id_index DESC");
      }

      $this->view($this->v_content, $data);
   }

   public function load($parse)
   {
      $data['parse'] = $parse;
      $data['main'] = $this->db(0)->get_where_row('detail_group', "id_index = " . $parse);
      $where = "id_detail_group = " . $data['main']['id_detail_group'];
      $data['item'] = $this->db(0)->get_where('detail_item', $where);

      $this->view(__CLASS__ . "/load", $data);
   }

   function add($link = 0, $pj = 0)
   {
      $group = $_POST['group'];
      $note = $_POST['note'];
      $id_index = $_POST['id_index'];
      $cols = 'id_detail_group, detail_group, pj, note';

      $get = $this->db(0)->get_where_row('detail_group', "id_index = " . $id_index);

      if ($pj == 1) {
         $pj = $this->userData['id_toko'];
      }

      if ($link == 0) {
         $count = $this->db(0)->count('detail_group');
         if ($count == 0) {
            $id_detail_group = 1;
         } else {
            $dataD = $this->db(0)->get('detail_group');
            $ar_id = array_column($dataD, 'id_detail_group');
            $max = max($ar_id);
            $id_detail_group = $max + 1;
         }
      } else {
         $id_detail_group = $get['id_detail_group'];
      }

      $vals = $id_detail_group . ",'" . $group . "'," . $pj . ",'" . $note . "'";
      $do = $this->db(0)->insertCols('detail_group', $cols, $vals);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Add Detail Group Success!");
         die($do['errno']);
      } else {
         die($do['error']);
      }
   }

   function add_varian($parse)
   {
      $id_item = $_POST['id_item'];
      $varian = $_POST['varian'];
      $varian = explode(",", $varian);

      $cols = "id_detail_item, varian";
      foreach ($varian as $v) {
         $whereCount = "id_detail_item = " . $id_item . " AND varian = '" . $v . "'";
         $dataCount = $this->db(0)->count_where('detail_item_varian', $whereCount);
         if ($dataCount == 0) {
            $vals = $id_item . ",'" . $v . "'";
            $do = $this->db(0)->insertCols('detail_item_varian', $cols, $vals);
            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " Add Varian Item Success!");
               echo $do['errno'];
               exit();
            } else {
               print_r($do['error']);
               exit();
            }
         } else {
            echo "Varian sudah ada";
            $this->model('Log')->write($this->userData['user'] . " Add Varian Failed, Double Forbidden!");
            exit();
         }
      }
   }

   public function updateCell()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $set = "detail_item = '" . $value . "'";
      $where = "id_detail_item = " . $id;
      $update = $this->db(0)->update("detail_item", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function updateCell_grup()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $set = "detail_group = '" . $value . "'";
      $where = "id_index = " . $id;
      $update = $this->db(0)->update("detail_group", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function updateCellVarian()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $set = "varian = '" . $value . "'";
      $where = "id_varian = " . $id;
      $update = $this->db(0)->update("detail_item_varian", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function update_add()
   {
      $id = $_POST['id'];
      $val = $_POST['value'];

      $where = "id_index = " . $id;
      $set = "cs = " . $val;
      $update = $this->db(0)->update("detail_group", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function delete_item()
   {
      $id = $_POST['id'];
      $where = "id_detail_item = " . $id;
      $delete = $this->db(0)->delete_where("detail_item", $where);

      $where = "code LIKE '%#" . $id . "-%'";
      $delete = $this->db(0)->delete_where("produk_harga", $where);

      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_varian()
   {
      $id = $_POST['id'];
      $where = "id_varian = " . $id;
      $delete = $this->db(0)->delete_where("detail_item_varian", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_grup()
   {
      $id = $_POST['id'];
      $where = "id_index = " . $id;
      $delete = $this->db(0)->delete_where("detail_group", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }


   function add_item_multi($id_index)
   {
      $item_post = $_POST['item'];

      $grup = $this->db(0)->get_where_row('detail_group', 'id_index = ' . $id_index);
      $id_detail_group = $grup['id_detail_group'];
      $cols = 'id_detail_group, detail_item';

      if (strlen($item_post) > 0) {
         $item = explode(",", $item_post);
         foreach ($item as $i) {
            $vals = "'" . $id_detail_group . "','" . $i . "'";
            $whereCount = "id_detail_group = '" . $id_detail_group . "' AND detail_item = '" . $i . "'";
            $dataCount = $this->db(0)->count_where('detail_item', $whereCount);
            if ($dataCount == 0) {
               $do = $this->db(0)->insertCols('detail_item', $cols, $vals);
               if ($do['errno'] == 0) {
                  $this->model('Log')->write($this->userData['user'] . " Add Detail Item Success!");
                  echo $do['errno'];
                  exit();
               } else {
                  print_r($do['error']);
                  exit();
               }
            } else {
               $this->model('Log')->write($this->userData['user'] . " Add Detail Item Failed, Double Forbidden!");
               echo "Double Entry!";
               exit();
            }
         }
      } else {
         $item = $item_post;
         $vals = "'" . $id_detail_group . "','" . $item . "'";
         $whereCount = "id_detail_group = '" . $id_detail_group . "' AND detail_item = '" . $item . "'";
         $dataCount = $this->db(0)->count_where('detail_item', $whereCount);
         if ($dataCount == 0) {
            $do = $this->db(0)->insertCols('detail_item', $cols, $vals);
            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " Add Detail Item Success!");
               echo $do['errno'];
               exit();
            } else {
               print_r($do['error']);
               exit();
            }
         } else {
            $this->model('Log')->write($this->userData['user'] . " Add Detail Item Failed, Double Forbidden!");
            echo "Double Entry!";
            exit();
         }
      }
   }
}
