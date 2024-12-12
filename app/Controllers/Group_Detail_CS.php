<?php

class Group_Detail_CS extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[3])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "CS Fitur - Item Detail"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $where = "cs = 1 ORDER BY detail_group ASC";
      $data = $this->db(0)->get_where('detail_group', $where);

      foreach ($data as $key => $d) {
         $where = "id_detail_group = " . $d['id_detail_group'] . " ORDER BY id_detail_item DESC LIMIT 10";
         $data_item = $this->db(0)->get_where('detail_item', $where);
         $data[$key]['item'] = $data_item;
      }

      $this->view($this->v_content, $data);
   }

   function add_item_multi($id_detail_group)
   {
      $item_post = $_POST['item'];
      $cols = 'id_detail_group, detail_item';

      if (strlen($item_post) > 0) {
         $item = explode(",", $item_post);
         foreach ($item as $i) {
            $vals = $id_detail_group . ",'" . $i . "'";
            $whereCount = "id_detail_group = '" . $id_detail_group . "' AND detail_item = '" . $i . "'";
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
         $vals = "'" . $id_detail_group . "','" . $item . "'";
         $whereCount = "id_detail_group = '" . $id_detail_group . "' AND detail_item = '" . $item . "'";
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
}
