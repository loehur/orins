<?php

class Group_Detail extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pAdmin)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Set Produksi - Group Detail"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["page" => $this->page]);
   }

   public function content()
   {

      $where = "id_toko = " . $this->userData['id_toko'] . " ORDER BY detail_group ASC";
      $data['main'] = $this->model('M_DB_1')->get_where('detail_group', $where);
      foreach ($data['main'] as $key => $d) {
         $where = "id_detail_group = " . $d['id_detail_group'] . " ORDER BY detail_item ASC";
         $data_item = $this->model('M_DB_1')->get_where('detail_item', $where);
         $data['main'][$key]['item'] = $data_item;

         foreach ($data_item as $di) {
            $where = "id_detail_item = " . $di['id_detail_item'];
            $varian = $this->model('M_DB_1')->get_where('detail_item_varian', $where);
            $data['varian'][$di['id_detail_item']] = $varian;
         }
      }

      $this->view($this->v_content, $data);
   }

   function add($link = 0)
   {
      $group = $_POST['group'];
      $cols = 'id_toko, id_detail_group, detail_group';

      if ($link == 0) {
         $where = "id_toko = " . $this->userData['id_toko'];
         $dataD = $this->model('M_DB_1')->get_where('detail_group', $where);

         $ar_id = array_column($dataD, 'id_detail_group');
         $max = max($ar_id);
         $id_detail_group = $max + 1;
      } else {
         $id_detail_group = $_POST['id_detail_group'];
      }

      $vals = $this->userData['id_toko'] . "," . $id_detail_group . ",'" . $group . "'";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND detail_group = '" . $group . "'";
      $dataCount = $this->model('M_DB_1')->count_where('detail_group', $whereCount);
      if ($dataCount <> 1) {
         $do = $this->model('M_DB_1')->insertCols('detail_group', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Detail Group Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Detail Group Failed, Double Forbidden!");
         echo "Double Entry!";
      }
   }

   function add_varian()
   {
      $id_item = $_POST['id_item'];
      $varian = $_POST['varian'];
      $varian = explode(",", $varian);

      $cols = "id_toko, id_detail_item, varian";
      foreach ($varian as $v) {
         $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_detail_item = " . $id_item . " AND varian = '" . $v . "'";
         $dataCount = $this->model('M_DB_1')->count_where('detail_item_varian', $whereCount);
         if ($dataCount == 0) {
            $vals = $this->userData['id_toko'] . "," . $id_item . ",'" . $v . "'";
            $do = $this->model('M_DB_1')->insertCols('detail_item_varian', $cols, $vals);
            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " Add Varian Item Success!");
               echo $do['errno'];
            } else {
               print_r($do['error']);
            }
         } else {
            $this->model('Log')->write($this->userData['user'] . " Add Varian Failed, Double Forbidden!");
         }
      }
   }

   public function updateCell()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $set = "detail_item = '" . $value . "'";
      $where = "id_detail_item = " . $id;
      $update = $this->model('M_DB_1')->update("detail_item", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function updateCellVarian()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $set = "varian = '" . $value . "'";
      $where = "id_varian = " . $id;
      $update = $this->model('M_DB_1')->update("detail_item_varian", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function update_add()
   {
      $id = $_POST['id'];
      $val = $_POST['value'];

      $where = "id_index = " . $id;
      $set = "cs = " . $val;
      $update = $this->model('M_DB_1')->update("detail_group", $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }

   public function delete_item()
   {
      $id = $_POST['id'];
      $where = "id_detail_item = " . $id;
      $delete = $this->model('M_DB_1')->delete_where("detail_item", $where);

      $where = "code LIKE '%#" . $id . "-%'";
      $delete = $this->model('M_DB_1')->delete_where("produk_harga", $where);

      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_varian()
   {
      $id = $_POST['id'];
      $where = "id_varian = " . $id;
      $delete = $this->model('M_DB_1')->delete_where("detail_item_varian", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_grup()
   {
      $id = $_POST['id'];
      $where = "id_index = " . $id;
      $delete = $this->model('M_DB_1')->delete_where("detail_group", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }


   function add_item_multi($id_detail_group)
   {
      $item_post = $_POST['item'];
      $cols = 'id_toko, id_detail_group, detail_item';

      if (strlen($item_post) > 0) {
         $item = explode(",", $item_post);
         foreach ($item as $i) {
            $vals = "'" . $this->userData['id_toko'] . "','" . $id_detail_group . "','" . $i . "'";
            $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_detail_group = '" . $id_detail_group . "' AND detail_item = '" . $i . "'";
            $dataCount = $this->model('M_DB_1')->count_where('detail_item', $whereCount);
            if ($dataCount == 0) {
               $do = $this->model('M_DB_1')->insertCols('detail_item', $cols, $vals);
               if ($do['errno'] == 0) {
                  $this->model('Log')->write($this->userData['user'] . " Add Detail Item Success!");
                  echo $do['errno'];
               } else {
                  print_r($do['error']);
               }
            } else {
               $this->model('Log')->write($this->userData['user'] . " Add Detail Item Failed, Double Forbidden!");
               echo "Double Entry!";
            }
         }
      } else {
         $item = $item_post;
         $vals = "'" . $this->userData['id_toko'] . "','" . $id_detail_group . "','" . $item . "'";
         $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_detail_group = '" . $id_detail_group . "' AND detail_item = '" . $item . "'";
         $dataCount = $this->model('M_DB_1')->count_where('detail_item', $whereCount);
         if ($dataCount == 0) {
            $do = $this->model('M_DB_1')->insertCols('detail_item', $cols, $vals);
            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " Add Detail Item Success!");
               echo $do['errno'];
            } else {
               print_r($do['error']);
            }
         } else {
            $this->model('Log')->write($this->userData['user'] . " Add Detail Item Failed, Double Forbidden!");
            echo "Double Entry!";
         }
      }
   }
}
