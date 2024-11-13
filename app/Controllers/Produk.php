<?php

class Produk extends Controller
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
         "title" => "Set Produksi - Produk"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["page" => $this->page]);
   }

   public function content()
   {

      $where = "id_toko = " . $this->userData['id_toko'];
      $data['produk'] = $this->model('M_DB_1')->get_where('produk', $where . " ORDER BY produk ASC");
      $data['detail'] = $this->model('M_DB_1')->get_where('detail_group', $where . " ORDER BY sort ASC");
      $data['divisi'] = $this->dDvs;

      foreach ($data['produk'] as $key => $d) {
         $where = "id_produk = " . $d['id_produk'];
         $data_item = $this->model('M_DB_1')->get_where('spk_dvs', $where);
         $data['produk'][$key]['spk_dvs'] = $data_item;

         $data_detail = $this->model('M_DB_1')->get_where('produk_detail', $where);
         $data['produk'][$key]['detail'] = $data_detail;
      }

      $this->view($this->v_content, $data);
   }

   function add()
   {
      $produk = $_POST['produk'];
      $detail = serialize($_POST['detail']);

      $cols = 'id_toko, produk, produk_detail';
      $vals = "'" . $this->userData['id_toko'] . "','" . $produk . "','" . $detail . "'";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND UPPER(produk) = '" . strtoupper($produk) . "' AND produk_detail = '" . $detail . "'";
      $dataCount = $this->model('M_DB_1')->count_where('produk', $whereCount);
      if ($dataCount == 0) {
         $do = $this->model('M_DB_1')->insertCols('produk', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Produk Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Produk Failed, Double Forbidden!");
         echo "Double Entry!";
      }

      $this->dataSynchrone();
   }

   function add_componen_harga()
   {
      $id_produk = $_POST['id_produk_harga'];
      $detail = serialize($_POST['detail_group']);

      $cols = 'id_toko, id_produk, detail';
      $vals = "'" . $this->userData['id_toko'] . "','" . $id_produk . "','" . $detail . "'";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_produk = " . $id_produk . " AND detail = '" . $detail . "'";
      $dataCount = $this->model('M_DB_1')->count_where('produk_detail', $whereCount);
      if ($dataCount == 0) {
         $do = $this->model('M_DB_1')->insertCols('produk_detail', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Produk Detail Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Produk Detail Failed, Double Forbidden!");
         echo "Double Entry!";
      }

      $this->dataSynchrone();
   }

   function edit($id_produk)
   {
      $produk = $_POST['produk'];
      $detail = serialize($_POST['detail']);

      $set = "produk = '" . $produk . "', produk_detail = '" . $detail . "'";
      $where = "id_produk = " . $id_produk;
      $update = $this->model('M_DB_1')->update("produk", $set, $where);
      $this->dataSynchrone();

      echo $update['errno'];
   }

   function add_spk($id_produk)
   {
      $cols = 'id_toko, id_produk, id_divisi, detail_groups, cm';
      $divisi = $_POST['divisi'];
      $cm = (isset($_POST['cm'])) ? $_POST['cm'] : 0;
      $detail_groups = serialize($_POST['detail_group']);

      $result = 0;

      if (count($_POST['detail_group']) > 0) {
         $vals = "'" . $this->userData['id_toko'] . "','" . $id_produk . "','" . $divisi . "','" . $detail_groups . "'," . $cm;
         $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_produk = '" . $id_produk . "' AND id_divisi = '" . $divisi . "'";
         $dataCount = $this->model('M_DB_1')->count_where('spk_dvs', $whereCount);
         if ($dataCount == 0) {
            $do = $this->model('M_DB_1')->insertCols('spk_dvs', $cols, $vals);
            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " Add spk_dvs Success!");
               $result = $do['errno'];
            } else {
               $result = $do['error'];
            }
         } else {
            $set = "detail_groups = '" . $detail_groups . "', cm = " . $cm;
            $where = "id_toko = '" . $this->userData['id_toko'] . "' AND id_produk = '" . $id_produk . "' AND id_divisi = '" . $divisi . "'";
            $update = $this->model('M_DB_1')->update("spk_dvs", $set, $where);
            $result = $update['errno'];
         }
      }
      $this->dataSynchrone();
      echo $result;
   }

   public function delete_item()
   {
      $id = $_POST['id'];
      $where = "id_spk_dvs = " . $id;
      $delete = $this->model('M_DB_1')->delete_where("spk_dvs", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_detail()
   {
      $id = $_POST['id'];
      $where = "id_produk_detail = " . $id;
      $delete = $this->model('M_DB_1')->delete_where("produk_detail", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_produk()
   {
      $id = $_POST['id'];

      $where = "code LIKE '" . $id . "#%'";
      $delete = $this->model('M_DB_1')->delete_where("produk_harga", $where);
      $where = "id_produk = " . $id;
      $delete = $this->model('M_DB_1')->delete_where("produk", $where);

      $this->dataSynchrone();
      echo $delete['errno'];
   }

   function add_item_multi($id_detail_group)
   {
      $item_post = $_POST['item'];
      $cols = 'id_toko, id_detail_group, item_name';

      if (strlen($item_post) > 0) {
         $item = explode(",", $item_post);
         foreach ($item as $i) {
            $vals = "'" . $this->userData['id_toko'] . "','" . $id_detail_group . "','" . $i . "'";
            $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_detail_group = '" . $id_detail_group . "' AND item_name = '" . $i . "'";
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
         $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND id_detail_group = '" . $id_detail_group . "' AND item_name = '" . $item . "'";
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

   function load_detail($id_produk)
   {
      $data['produk'] = $this->model('M_DB_1')->get_where_row('produk', "id_produk =" . $id_produk);
      $dp = $data['produk'];
      $detail = unserialize($dp['produk_detail']);
      $dg = [];

      foreach ($this->dDetailGroup as $ddg) {
         foreach ($detail as $dt) {
            if ($dt == $ddg['id_index']) {
               array_push($dg, ["id" => $dt, "detail" => $ddg['detail_group']]);
            }
         }
      }

      $this->view($this->page . "/detail", $dg);
   }
}
