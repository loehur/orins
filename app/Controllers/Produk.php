<?php

class Produk extends Controller
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

   public function index($parse)
   {
      $title = "Produk - Produksi";
      if ($parse == 1) {
         $title = "Produk - Jasa";
      }
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $title
      ]);

      $this->viewer($parse);
   }

   public function viewer($parse)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse)
   {
      if ($parse == 0) {
         $pj = $parse;
      } else {
         $pj = $this->userData['id_toko'];
      }

      $data['parse'] = $pj;
      $data['produk'] = $this->db(0)->get_where('produk', 'pj = ' . $pj . ' ORDER BY freq DESC, id_produk');

      $data['detail'] = $this->db(0)->get_where('detail_group', 'pj = ' . $pj);
      $data['divisi'] = $this->db(0)->get('divisi');

      foreach ($data['produk'] as $key => $d) {
         $where = "id_produk = " . $d['id_produk'];
         $data_item = $this->db(0)->get_where('spk_dvs', $where);
         $data['produk'][$key]['spk_dvs'] = $data_item;
         $data_detail = $this->db(0)->get_where('produk_detail', $where);
         $data['produk'][$key]['detail'] = $data_detail;
      }

      $this->view($this->v_content, $data);
   }

   function add($parse)
   {
      $produk = $_POST['produk'];
      $detail = serialize($_POST['detail']);

      $cols = 'produk, produk_detail, pj';
      $vals = "'" . $produk . "','" . $detail . "', " . $parse;

      $whereCount = "UPPER(produk) = '" . strtoupper($produk) . "' AND produk_detail = '" . $detail . "'";
      $dataCount = $this->db(0)->count_where('produk', $whereCount);
      if ($dataCount == 0) {
         $do = $this->db(0)->insertCols('produk', $cols, $vals);
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

      $cols = 'id_produk, detail';
      $vals = "'" . $id_produk . "','" . $detail . "'";

      $whereCount = "id_produk = " . $id_produk . " AND detail = '" . $detail . "'";
      $dataCount = $this->db(0)->count_where('produk_detail', $whereCount);
      if ($dataCount == 0) {
         $do = $this->db(0)->insertCols('produk_detail', $cols, $vals);
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
      $update = $this->db(0)->update("produk", $set, $where);
      $this->dataSynchrone();

      echo $update['errno'];
   }

   function add_spk($id_produk)
   {
      $cols = 'id_produk, id_divisi, detail_groups, cm';
      $divisi = $_POST['divisi'];
      $cm = (isset($_POST['cm'])) ? $_POST['cm'] : 0;
      $detail_groups = serialize($_POST['detail_group']);

      $result = 0;

      if (count($_POST['detail_group']) > 0) {
         $vals = $id_produk . ",'" . $divisi . "','" . $detail_groups . "'," . $cm;
         $whereCount = "id_produk = '" . $id_produk . "' AND id_divisi = '" . $divisi . "'";
         $dataCount = $this->db(0)->count_where('spk_dvs', $whereCount);
         if ($dataCount == 0) {
            $do = $this->db(0)->insertCols('spk_dvs', $cols, $vals);
            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " Add spk_dvs Success!");
               $result = $do['errno'];
            } else {
               $result = $do['error'];
            }
         } else {
            $set = "detail_groups = '" . $detail_groups . "', cm = " . $cm;
            $where = "id_produk = '" . $id_produk . "' AND id_divisi = '" . $divisi . "'";
            $update = $this->db(0)->update("spk_dvs", $set, $where);
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
      $delete = $this->db(0)->delete_where("spk_dvs", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_detail()
   {
      $id = $_POST['id'];
      $where = "id_produk_detail = " . $id;
      $delete = $this->db(0)->delete_where("produk_detail", $where);
      $this->dataSynchrone();
      echo $delete['errno'];
   }

   public function delete_produk()
   {
      $id = $_POST['id'];

      $where = "code LIKE '" . $id . "#%'";
      $delete = $this->db(0)->delete_where("produk_harga", $where);
      $where = "id_produk = " . $id;
      $delete = $this->db(0)->delete_where("produk", $where);

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
            $dataCount = $this->db(0)->count_where('detail_item', $whereCount);
            if ($dataCount == 0) {
               $do = $this->db(0)->insertCols('detail_item', $cols, $vals);
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
         $dataCount = $this->db(0)->count_where('detail_item', $whereCount);
         if ($dataCount == 0) {
            $do = $this->db(0)->insertCols('detail_item', $cols, $vals);
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
      $dp = $this->db(0)->get_where_row('produk', 'id_produk = ' . $id_produk);
      $detail = unserialize($dp['produk_detail']);
      $dg = [];

      foreach ($this->dDetailGroup as $ddg) {
         foreach ($detail as $dt) {
            if ($dt == $ddg['id_index']) {
               array_push($dg, ["id" => $dt, "detail" => $ddg['detail_group'], "note" => $ddg['note']]);
            }
         }
      }

      $this->view(__CLASS__ . "/detail", $dg);
   }
}
