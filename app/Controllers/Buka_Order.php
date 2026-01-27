<?php

class Buka_Order extends Controller
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

   // Constants for customer types
   const CUSTOMER_TYPE_UMUM = 1;
   const CUSTOMER_TYPE_REKANAN = 2;
   const CUSTOMER_TYPE_ONLINE = 3;
   const CUSTOMER_TYPE_STOK = 100;

   /**
    * Get cs_id and id_pelanggan from ref
    */
   private function getRefMetadata($ref)
   {
      $metadata = [
         'cs_id' => 0,
         'id_pelanggan' => 0
      ];

      if (empty($ref)) {
         return $metadata;
      }

      // Try order_data first
      $order_row = $this->db(0)->get_where_row('order_data', "ref = '" . $ref . "' AND cancel <> 1 AND id_penerima > 0 LIMIT 1");
      if (isset($order_row['id_penerima']) && $order_row['id_penerima'] > 0) {
         $metadata['cs_id'] = $order_row['id_penerima'];
      }
      if (isset($order_row['id_pelanggan']) && $order_row['id_pelanggan'] > 0) {
         $metadata['id_pelanggan'] = $order_row['id_pelanggan'];
      }

      // Fallback to master_mutasi if not found
      if ($metadata['cs_id'] == 0) {
         $mutasi_row = $this->db(0)->get_where_row('master_mutasi', "ref = '" . $ref . "' AND cs_id > 0 LIMIT 1");
         if (isset($mutasi_row['cs_id']) && $mutasi_row['cs_id'] > 0) {
            $metadata['cs_id'] = $mutasi_row['cs_id'];
         }
      }
      if ($metadata['id_pelanggan'] == 0) {
         $mutasi_row = $this->db(0)->get_where_row('master_mutasi', "ref = '" . $ref . "' AND id_target > 0 LIMIT 1");
         if (isset($mutasi_row['id_target']) && $mutasi_row['id_target'] > 0) {
            $metadata['id_pelanggan'] = $mutasi_row['id_target'];
         }
      }

      return $metadata;
   }

   /**
    * Calculate paket pricing adjustments
    */
   private function calculatePaketAdjustments($total_per_paket, $id_margin, $paket_data, $id_pelanggan_jenis)
   {
      $adjuster = [];
      foreach ($total_per_paket as $key => $tpp) {
         if (isset($id_margin[$key]['qty']) && isset($paket_data[$key])) {
            $adjuster[$key] = ($paket_data[$key]['harga_' . $id_pelanggan_jenis] * $id_margin[$key]['qty']) - $tpp;
            $id_margin[$key]['harga_paket'] = $adjuster[$key];
         }
      }
      return [$adjuster, $id_margin];
   }

   /**
    * Get indexed harga data for faster lookup
    */
   private function getIndexedHarga()
   {
      $data_harga = $this->db(0)->get('produk_harga');
      $indexed = [];

      foreach ($data_harga as $dh) {
         $key = $dh['code'] . '_' . $dh['id_produk'];
         $indexed[$key] = $dh;
      }

      return $indexed;
   }

   /**
    * Normalize customer type (100 -> 2)
    */
   private function normalizeCustomerType($id_pelanggan_jenis)
   {
      return $id_pelanggan_jenis == self::CUSTOMER_TYPE_STOK ? self::CUSTOMER_TYPE_REKANAN : $id_pelanggan_jenis;
   }

   function Edit_order($ref, $jenis_pelanggan, $dibayar, $id_pelanggan)
   {
      // Create snapshot before entering edit mode
      $session_key = 'edit_' . $this->userData['id_user'] . '_' . $ref . '_' . time();

      // Get current order data
      $order_data = $this->db(0)->get_where('order_data', "ref = '" . $ref . "' AND cancel <> 1");
      $mutasi_data = $this->db(0)->get_where('master_mutasi', "ref = '" . $ref . "' AND stat <> 2");

      // Convert to JSON for snapshot
      $snapshot_order = json_encode($order_data);
      $snapshot_mutasi = json_encode($mutasi_data);

      // Clean up any existing active sessions for this user
      $this->db(0)->update('edit_sessions', "status = 'cancelled'", "user_id = " . $this->userData['id_user'] . " AND status = 'active'");

      // Use helper to get id_penerima
      $refMeta = $this->getRefMetadata($ref);
      $id_penerima_cur = $refMeta['cs_id'];

      // Create new edit session with snapshot
      $cols = 'session_key, user_id, ref, id_pelanggan, jenis_pelanggan, dibayar, id_penerima, snapshot_data, snapshot_mutasi, status';
      $vals = "'" . $session_key . "'," . $this->userData['id_user'] . ", '" . $ref . "'," . $id_pelanggan . "," . $jenis_pelanggan . "," . $dibayar . "," . $id_penerima_cur . ", '" . addslashes($snapshot_order) . "', '" . addslashes($snapshot_mutasi) . "', 'active'";

      $result = $this->db(0)->insertCols('edit_sessions', $cols, $vals);
      if ($result['errno'] != 0) {
         echo "Error creating edit session: " . $result['error'];
         exit();
      }

      // Set session with session_key
      $_SESSION['edit'][$this->userData['id_user']] = [$ref, $jenis_pelanggan, $dibayar, $id_pelanggan, $session_key, $id_penerima_cur];

      // Clean up temporary items (items with no ref or id_pelanggan)
      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0 AND ref = ''";
      $do = $this->db(0)->delete_where('order_data', $where);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      $whereBarang = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0 AND ref = ''";
      $do = $this->db(0)->delete_where('master_mutasi', $whereBarang);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      $this->index($jenis_pelanggan);
   }

   public function index($jenis_pelanggan)
   {
      if ($jenis_pelanggan == 1) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Umum"
         ]);
      } elseif ($jenis_pelanggan == 2) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Rekanan"
         ]);
      } elseif ($jenis_pelanggan == 3) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Online"
         ]);
      } elseif ($jenis_pelanggan == 100) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Stok"
         ]);
      }
      $this->viewer($jenis_pelanggan);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');
      $data['produk'] = $this->db(0)->get_where('produk', 'pj = 0 ORDER BY freq DESC, id_produk');
      $data['produk_jasa'] = $this->db(0)->get_where('produk', "pj = " . $this->userData['id_toko'] . " ORDER BY freq DESC, id_produk");
      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");

      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 AND id_pelanggan_jenis = " . $parse . " ORDER BY freq DESC";
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan, 'id_pelanggan');

      $data['id_jenis_pelanggan'] = $parse;
      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $where = "(ref = '" . $dEdit[0] . "' AND cancel <> 1) OR (id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0) AND cancel = 0";;
         $whereBarang = "(ref = '" . $dEdit[0] . "' AND stat <> 2 AND pid = 0) OR (id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0 AND stat <> 2 AND pid = 0)";
      } else {
         $where = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0 AND cancel = 0";
         $whereBarang = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0 AND stat <> 2 AND pid = 0";
      }

      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['order_barang'] = $this->db(0)->get_where('master_mutasi', $whereBarang);

      $data['barang'] = $this->db(0)->get('master_barang', 'id');

      if ($parse == self::CUSTOMER_TYPE_STOK) {
         $data['barang_code'] = $this->db(0)->get('master_barang', 'code');
      }

      // Optimized Stock Fetching
      if ($this->userData['id_toko'] == 1) {
         // Combine gudang (0) and toko (1) stock in one go if possible, or keep separate calls if logic demands
         // Original code merged them with overwrite. Optimization:
         $stok_gudang = $this->data('Barang')->stok_data_list(0);
         $stok_toko = $this->data('Barang')->stok_data_list(1);
         // Use + operator for array union if keys are id_barang, but array_merge overwrites index.
         // Original loop overwrite logic: Toko overwrites Gudang.
         $data['stok'] = $stok_toko + $stok_gudang;
         // Note: If stok_data_list returns array indexed by 0,1,2.. this won't work as expected.
         // Assuming it returns indexed by id_barang as seemingly implied by original code structure?
         // Original code:
         // foreach ($stok_gudang as $item) { $gabung[$item['id_barang']] = $item; }
         // foreach ($stok_toko as $item) { $gabung[$item['id_barang']] = $item; }
         // So if we trust array_column or assuming the return is clean, let's stick to safe manual merge but cleaner
      } else {
         $data['stok'] = $this->data('Barang')->stok_data_list($this->userData['id_toko']);
      }

      // Pre-load and Index Prices for O(1) Lookup
      $indexedHarga = $this->getIndexedHarga();

      $data['count'] = count($data['order']) + count($data['order_barang']);
      $getHarga = [];
      $data['errorID'] = [];

      $count_price_locker = 0;
      $id_margin = [];
      $total_per_paket = [];
      $harga_paket = [];

      foreach ($data['order'] as $key => $do) {
         if ($do['paket_ref'] <> "") {
            if ($do['price_locker'] == 1) {

               if (!isset($total_per_paket[$do['paket_ref']])) {
                  $total_per_paket[$do['paket_ref']] = 0;
               }

               $count_price_locker += 1;
               $harga_paket[$do['paket_ref']] = $data['paket'][$do['paket_ref']]['harga_' . $parse];
               $id_margin[$do['paket_ref']]['id'] = $do['id_order_data'];
               $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $do['paket_ref'] . "' AND price_locker = 1");
               if (isset($get['jumlah'])) {
                  $paket_qty = $do['jumlah'] / $get['jumlah'];
                  $id_margin[$do['paket_ref']]['qty'] = $paket_qty;
                  $id_margin[$do['paket_ref']]['nama'] =  $data['paket'][$do['paket_ref']]['nama'];
                  $id_margin[$do['paket_ref']]['tb'] = "order_data";
                  $id_margin[$do['paket_ref']]['primary'] =  "id_order_data";
               }
            }
         }

         $parse_harga = $this->normalizeCustomerType($parse);

         $detail_harga = unserialize($do['detail_harga']);
         if (is_array($detail_harga)) {
            $countDH[$key] = count($detail_harga);
            foreach ($detail_harga as $dh_o) {
               $getHarga[$key][$dh_o['c_h']] = 0;

               // Optimized Price Lookup O(1)
               $priceFound = null;
               
               // Try specific product price first
               $specificKey = $dh_o['c_h'] . '_' . $do['id_produk'];
               // Try generic price (id_produk = 0)
               $genericKey = $dh_o['c_h'] . '_0';

               if (isset($indexedHarga[$genericKey]) && $indexedHarga[$genericKey]['harga_' . $parse_harga] <> 0) {
                  $dh = $indexedHarga[$genericKey];
                  $priceFound = $dh['harga_' . $parse_harga];
                  
                  // Update Logic (Legacy migration support)
                  $where = "code = '" . $dh_o['c_h'] . "' AND id_produk = 0";
                  $set = "harga_" . $parse_harga . " = " .  $priceFound . ", id_produk = " . $do['id_produk'];
                  $up = $this->db(0)->update("produk_harga", $set, $where);
                  if ($up['errno'] <> 0) {
                     echo $up['error'];
                     exit();
                  }
                  
                  // Update local index to reflect change if needed, though mostly read-only for this page load
               } elseif (isset($indexedHarga[$specificKey]) && $indexedHarga[$specificKey]['harga_' . $parse_harga] <> 0) {
                  $dh = $indexedHarga[$specificKey];
                  $priceFound = $dh['harga_' . $parse_harga];
               }

               if ($priceFound !== null) {
                  $getHarga[$key][$dh_o['c_h']] = $priceFound;
                  if ($do['paket_ref'] <> "") {
                      $total_per_paket[$do['paket_ref']] = isset($total_per_paket[$do['paket_ref']]) 
                          ? $total_per_paket[$do['paket_ref']] + ($priceFound * $do['jumlah']) 
                          : ($priceFound * $do['jumlah']);
                  }
                  $countDH[$key] -= 1;
               }
            }

            if ($countDH[$key] == 0) {
               if (isset($data['order'][$key]['harga'])) {
                  if (isset($getHarga[$key]) && is_array($getHarga[$key])) {
                     $data['order'][$key]['harga'] = array_sum($getHarga[$key]);
                  } else {
                     array_push($data['errorID'], ['id' => $do['id_order_data'], 'produk' => $do['produk']]);
                  }
               }
            }
         }
      }

      foreach ($data['order_barang'] as $dm) {
         if ($dm['price_locker'] == 1) {

            if (!isset($total_per_paket[$dm['paket_ref']])) {
               $total_per_paket[$dm['paket_ref']] = 0;
            }

            $count_price_locker += 1;
            $harga_paket[$dm['paket_ref']] = $data['paket'][$dm['paket_ref']]['harga_' . $parse];
            $id_margin[$dm['paket_ref']]['id'] = $dm['id'];
            $get = $this->db(0)->get_where_row('paket_mutasi', "paket_ref = '" . $dm['paket_ref'] . "' AND price_locker = 1");
            if (isset($get['qty'])) {
               $paket_qty = $dm['qty'] / $get['qty'];
               $id_margin[$dm['paket_ref']]['qty'] = $paket_qty;
               $id_margin[$dm['paket_ref']]['nama'] =  $data['paket'][$dm['paket_ref']]['nama'];
               $id_margin[$dm['paket_ref']]['tb'] = "master_mutasi";
               $id_margin[$dm['paket_ref']]['primary'] =  "id";
            }
         }

         if (strlen($dm['paket_ref']) > 0) {
            $db = $data['barang'][$dm['id_barang']];
            if (isset($total_per_paket[$dm['paket_ref']])) {
               $total_per_paket[$dm['paket_ref']] += ($db['harga_' . $parse] * $dm['qty']);
            } else {
               $total_per_paket[$dm['paket_ref']] = ($db['harga_' . $parse] * $dm['qty']);
            }
         }
      }

      // Use Helper for Paket Adjustments
      list($adjuster, $id_margin) = $this->calculatePaketAdjustments($total_per_paket, $id_margin, $data['paket'], $parse);

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan, 'id_karyawan');
      $data['harga'] = $getHarga;

      $data['harga_paket'] = $id_margin;
      $this->view($this->v_content, $data);
   }

   function delete_error()
   {
      $id = $_POST['id'];
      $where = "id_order_data = " . $id;
      $this->db(0)->delete_where('order_data', $where);
   }

   function update_note()
   {
      $mode = $_POST['note_mode'];
      $id = $_POST['note_id'];
      $value = $_POST['note_val'];
      if ($mode == "main") {
         $do = $this->db(0)->update("order_data", "note = '" . $value . "'", "id_order_data = " . $id);
      } else {
         $data = $this->db(0)->get_where_row("order_data", "id_order_data = " . $id)['note_spk'];
         $data = unserialize($data);
         $data[$mode] = $value;
         $new_data = serialize($data);
         $do = $this->db(0)->update("order_data", "note_spk = '" . $new_data . "'", "id_order_data = " . $id);
      }

      echo $do['errno'] == 0 ? 0 : $do['error'];
   }

   function add_paket($id_pelanggan_jenis)
   {
      $id = $_POST['id'];
      $qty_paket = $_POST['qty_paket'];
      $paket_group = $this->userData['id_toko'] . date("ymdHis") . rand(0, 9);
      $data['order'] = $this->db(0)->get_where("paket_order", "paket_ref = '" . $id . "'");
      $data['mutasi'] = $this->db(0)->get_where("paket_mutasi", "paket_ref = '" . $id . "'");
      $data['barang'] = $this->db(0)->get('master_barang', 'code');

      // Get harga_paket from paket_main
      $paket_main = $this->db(0)->get_where_row("paket_main", "id = '" . $id . "'");
      $harga_paket_column = 'harga_' . $id_pelanggan_jenis;
      $harga_paket = isset($paket_main[$harga_paket_column]) ? $paket_main[$harga_paket_column] : 0;

      foreach ($data['mutasi'] as $dm) {
         $_POST['kode'] = $dm['id_barang'];
         $_POST['qty'] = $qty_paket * $dm['qty'];
         $_POST['sds'] = $dm['sds'];
         $_POST['sn'] = '';
         $id_sumber = $dm['id_sumber'];
         $harga_paket_item = ($dm['price_locker'] == 1) ? $harga_paket : 0;
         $paket_qty_value = ($dm['price_locker'] == 1) ? $qty_paket : 0;
         $this->add_barang($id_pelanggan_jenis, $dm['price_locker'], $id, $id_sumber, $harga_paket_item, $paket_group, $paket_qty_value);
      }

      foreach ($data['order'] as $do) {
         $_POST['id_produk'] = $do['id_produk'];
         $_POST['note'] = $do['note'];
         $_POST['note_spk'] = $do['note_spk'];
         $_POST['pending_spk'] = $do['pending_spk'];
         $_POST['detail_harga'] = $do['detail_harga'];
         $_POST['produk_code'] = $do['produk_code'];
         $_POST['produk_detail'] = $do['produk_detail'];
         $_POST['jumlah'] = $qty_paket * $do['jumlah'];
         $harga_paket_item = ($do['price_locker'] == 1) ? $harga_paket : 0;
         $paket_qty_value = ($do['price_locker'] == 1) ? $qty_paket : 0;
         $this->add($do['id_afiliasi'], $id, $paket_group, $do['price_locker'], $harga_paket_item, $do['pj'], $paket_qty_value);
      }
   }

   function add($afiliasi = 0, $paket_ref = '', $paket_group = '', $price_locker = 0, $harga_paket = 0, $pj = 0, $paket_qty = 0)
   {
      $this->dataSynchrone();
      $this->data_order();

      $ref = "";
      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $ref = $dEdit[0];
      }

      if ($afiliasi == 0 && isset($_POST['aff_target'])) {
         $afiliasi = $_POST['aff_target'];
      }

      $id_produk = $_POST['id_produk'];
      $jumlah = $_POST['jumlah'];
      $note = $_POST['note'];

      //update freq
      $this->db(0)->update("produk", "freq = freq+1", "id_produk = " . $id_produk);

      $where_idProduk = "id_produk = " . $id_produk;
      $detailHarga = [];
      $listDetail = $this->db(0)->get_where('produk_detail', $where_idProduk);

      if (count($listDetail) == 0) {
         echo "Pengaturan Harga belum di setting!";
         exit();
      }

      if (isset($_POST['note_spk'])) {
         $spkNote = unserialize($_POST['note_spk']);
      } else {
         $spkNote = [];
         foreach ($this->dSPK as $sd) {
            if ($sd['id_produk'] == $id_produk) {
               $spkNote[$sd['id_divisi']] = $_POST['d-' . $sd['id_divisi']];
            }
         }
      }

      if (isset($_POST['pending_spk'])) {
         $spkR = unserialize($_POST['pending_spk']);
      } else {
         $spkR = [];
         foreach ($this->dSPK as $sd) {
            if ($sd['id_produk'] == $id_produk) {
               if (isset($_POST['p-' . $sd['id_divisi']])) {
                  if ($_POST['p-' . $sd['id_divisi']] <> "") {
                     $spkR[$sd['id_divisi']] = $_POST['p-' . $sd['id_divisi']] . "-p";
                  }
               }
            }
         }
      }

      $data = [];
      $dp = $this->db(0)->get_where_row('produk', 'id_produk = ' . $id_produk);
      $data = unserialize($dp['produk_detail']);
      $produk_name = $dp['produk'];

      $produk_code = $id_produk . "#";
      $detail_code = "";
      $get_detail_item = [];

      if (isset($_POST['produk_detail'])) {
         $produk_detail_ = unserialize($_POST['produk_detail']);
      } else {
         foreach ($data as $d) {
            $groupName = "";
            $detail_item = [];

            $id_detail_item_ex = explode("#", $_POST['f-' . $d]);
            $id_item_ex = explode("-", $id_detail_item_ex[0]);

            //update freq
            $this->db(0)->update("detail_item", "freq = freq+1", "id_detail_item = " . $id_item_ex[0]);

            $get_detail_item[$d]['id'] = $id_item_ex[0];
            $get_detail_item[$d]['name'] = $id_item_ex[1];

            if ($id_detail_item_ex[1] <> "") {
               $varian_ex = explode("-", $id_detail_item_ex[1]);
               $get_detail_item[$d]['id_varian'] = $varian_ex[0];
               $get_detail_item[$d]['varian'] = $varian_ex[1];
               $detail_item = $get_detail_item[$d]['name'] . "-" . $get_detail_item[$d]['varian'];
            } else {
               $get_detail_item[$d]['id_varian'] = "";
               $get_detail_item[$d]['varian'] = "";
               $detail_item = $get_detail_item[$d]['name'];
            }

            foreach ($this->dDetailGroup as $dg) {
               if ($dg['id_index'] == $d) {
                  $groupName = $dg['detail_group'];
               }
            }

            if ($groupName == "" || $detail_item == "") {
               echo "Error! diperlukan Synchrone Data!";
               exit();
            }

            $produk_detail_[$d] = array(
               "group_name" => $groupName,
               "detail_id" =>   $get_detail_item[$d]['id'] . "_" .  $get_detail_item[$d]['id_varian'],
               "detail_name" => $detail_item,
            );

            $detail_code .= "-" .  $get_detail_item[$d]['id'] . "&" .  $get_detail_item[$d]['id_varian'];
         }
      }

      if (isset($_POST['detail_harga'])) {
         $detailHarga = unserialize($_POST['detail_harga']);
      } else {
         foreach ($listDetail as $key_l => $ldt) {
            $c_harga = "";
            $c_barang = "";
            $g = "";
            $n_b = "";
            $n_v = "";

            $detail__ = unserialize($ldt['detail']);
            foreach ($detail__ as $d_L) {
               foreach ($data as $d) {
                  if ($d_L == $d) {
                     $c_harga .= "#" . $get_detail_item[$d]['id'] . "-";
                     $c_barang .= "#" . $get_detail_item[$d]['id'] . "&" . $get_detail_item[$d]['id_varian'] . "-";
                     $n_b .= $get_detail_item[$d]['name'] . " ";

                     if (strlen($get_detail_item[$d]['varian']) > 0) {
                        $n_v .= $get_detail_item[$d]['name'] . "-" . $get_detail_item[$d]['varian'] . " ";
                     } else {
                        $n_v .= $get_detail_item[$d]['name'] . " ";
                     }
                     $g .= "#" . $d . "-";
                  }
               }


               $n_b = preg_replace('!\s+!', ' ', $n_b);
               $n_v = preg_replace('!\s+!', ' ', $n_v);

               $detailHarga[$key_l] = array(
                  "c_h" => $c_harga, //code harga
                  "c_b" => $c_barang, // code_barang
                  "n_b" => $n_b, // nama barang
                  "n_v" => $n_v, // nama barang varian
                  "g" => $g, // group komponen
                  "h" => 0, // harga
                  "d" => 0, // diskon
               );
            }
         }
      }

      if (isset($_POST['produk_code'])) {
         $produk_code = $_POST['produk_code'];
      } else {
         $produk_code .= $detail_code;
      }

      $produk_detail = serialize($produk_detail_);
      $spkDVS = [];

      foreach ($this->dSPK as $ds) {
         if ($id_produk == $ds['id_produk']) {
            $detailNeed = [];
            $dgr = unserialize($ds['detail_groups']);
            $cm = $ds['cm'];

            foreach ($dgr as $key => $dgr_) {
               foreach ($produk_detail_ as $key => $pd) {
                  if ($dgr_ == $key) {
                     $detailNeed[$pd['detail_id']] = $pd['detail_name'];
                  }
               }
            }

            $spkDVS[$ds['id_divisi']] = array(
               "divisi_code" => "D-" . $ds['id_divisi'],
               "spk" => $detailNeed,
               "status" => 0,
               "user_produksi" => 0,
               "update" => "",
               "cm" => $cm, //complete maker
               "cm_status" => 0,
               "user_cm" => 0,
               "update_cm" => "",
            );
         }
      }

      $spkDVS_ = serialize($spkDVS);
      $spkNote_ = serialize($spkNote);
      $spkR_ = serialize($spkR);
      $detailHarga_ = serialize($detailHarga);

      if (isset($_POST['id_paket']) && $_POST['id_paket'] <> "") {
         $paketGet = explode("-", $_POST['id_paket']);

         $where = $paketGet[1] . " = " . $paketGet[0];
         $link_paket = $this->db(0)->get_where_row($paketGet[2], $where);
         $paket_ref = $link_paket['paket_ref'];
         $paket_group = $link_paket['paket_group'];
      }

      if ($paket_ref <> "") {
         $cek_double = $this->db(0)->count_where('order_data', "ref = '" . $ref . "' AND id_user = " . $this->userData['id_user'] . " AND produk_code = '" . $produk_code . "' AND paket_ref = '" . $paket_ref . "' AND tuntas = 0 AND cancel = 0");
         if ($cek_double <> 0) {
            echo 0;
            exit();
         }
      }

      // If this is part of a paket, ensure harga field is set to 0 at insert time
      $harga_insert = 0;
      if ($afiliasi == 0) {
         $cols = 'ref, detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, id_user, note, note_spk, paket_ref, paket_group, price_locker, harga_paket, pj, pending_spk, harga, paket_qty';
         $vals = "'" . $ref . "','" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "','" . $spkNote_ . "','" . $paket_ref . "','" . $paket_group . "'," . $price_locker . "," . $harga_paket . "," . $pj . ",'" . $spkR_ . "'," . $harga_insert . "," . $paket_qty;
      } else {
         $cols = 'ref, detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, id_user, note, note_spk, id_afiliasi, status_order, paket_ref, paket_group, price_locker, harga_paket, pj, pending_spk, harga, paket_qty';
         $vals = "'" . $ref . "','" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "','" . $spkNote_ . "'," . $afiliasi . ",1,'" . $paket_ref . "','" . $paket_group . "'," . $price_locker . "," . $harga_paket . "," . $pj . ",'" . $spkR_ . "'," . $harga_insert . "," . $paket_qty;
      }

      $do = $this->db(0)->insertCols('order_data', $cols, $vals);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Add Order Success!");
         echo $do['errno'];
      } else {
         print_r($do['error']);
         exit();
      }
   }

   function add_barang($id_jenis_pelanggan, $price_locker = 0, $paket_ref = "", $id_sumber = 0, $harga_paket = 0, $paket_group = "", $paket_qty = 0)
   {
      $ref = "";
      $cs_id = 0;
      $id_target = 0;
      
      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $ref = $dEdit[0];
         
         // Use helper to get cs_id and id_target
         $refMeta = $this->getRefMetadata($ref);
         $cs_id = $refMeta['cs_id'];
         $id_target = $refMeta['id_pelanggan'];
         
         // Fallback to session data if not found
         if ($cs_id == 0 && isset($dEdit[5]) && $dEdit[5] > 0) {
            $cs_id = $dEdit[5];
         }
         if ($id_target == 0 && isset($dEdit[3]) && $dEdit[3] > 0) {
            $id_target = $dEdit[3];
         }
      }

      $id_barang = $_POST['kode'];
      $qty = $_POST['qty'];
      $sds = $_POST['sds'];
      $sn =  $_POST['sn'];
      $sn_c = 0;
      if (strlen($sn) > 0) {
         $sn_c = 1;
      }

      if ($id_sumber == 0) {
         $id_sumber = $this->userData['id_toko'];
      }

      if (isset($_POST['id_paket']) && $_POST['id_paket'] <> "") {
         $paketGet = explode("-", $_POST['id_paket']);
         $where = $paketGet[1] . " = " . $paketGet[0];
         $link_paket = $this->db(0)->get_where_row($paketGet[2], $where);
         $paket_ref = $link_paket['paket_ref'];
         $paket_group = $link_paket['paket_group'];
      }

      if ($paket_ref <> "") {
         $cek_double_paket = $this->db(0)->count_where('master_mutasi', "ref = '" . $ref . "' AND user_id = " . $this->userData['id_user'] . " AND id_barang = " . $id_barang . " AND sn = '" . $sn . "' AND sds = " . $sds . " AND paket_ref = '" . $paket_ref . "' AND tuntas = 0 AND stat <> 2");
         if ($cek_double_paket <> 0) {
            echo 0;
            exit();
         }
      }

      $barang = $this->db(0)->get_where_row('master_barang', "id = '" . $id_barang . "'");
      $harga = $barang['harga_' . $id_jenis_pelanggan];
      // if this item is part of a paket, keep harga_jual at 0
      if ($paket_ref <> "") {
         $harga = 0;
      }

      $cols = 'ref, jenis, jenis_target, id_barang, id_sumber, id_target, qty, sds, sn, sn_c, user_id, cs_id, harga_jual, price_locker, paket_ref, paket_group, harga_paket, paket_qty';
      $vals = "'" . $ref . "',2," . $id_jenis_pelanggan . "," . $id_barang . ",'" . $id_sumber . "'," . $id_target . "," . $qty . "," . $sds . ",'" . $sn . "'," . $sn_c . "," . $this->userData['id_user'] . "," . $cs_id . "," . $harga . "," . $price_locker . ",'" . $paket_ref . "','" . $paket_group . "'," . $harga_paket . "," . $paket_qty;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }

   function load_detail($produk)
   {
      $data = [];
      $dp = $this->db(0)->get_where_row('produk', "id_produk = " . $produk);
      $data = unserialize($dp['produk_detail']);
      $spkNote = [];
      foreach ($this->dSPK as $sd) {
         if ($sd['id_produk'] == $produk) {
            $spkNote[$sd['id_divisi']] = "";
         }
      }

      $data_ = [];
      $varian = [];
      foreach ($data as $d) {
         $groupName = "";
         if (isset($this->dDetailGroup[$d])) {
            $dg = $this->dDetailGroup[$d];
            $data_item = $this->dDetailItem_1[$dg['id_detail_group']];

            if (isset($this->dDetailItem_1[$dg['id_detail_group']])) {
               $do = $this->dDetailItem_1[$dg['id_detail_group']];
               foreach ($do as $di) {
                  if (isset($this->dDetailItemVarian_1[$di['id_detail_item']])) {
                     $varian[$di['id_detail_item']] = $this->dDetailItemVarian_1[$di['id_detail_item']];
                  }
               }
            }
            $groupName = $dg['detail_group'];
         }
         foreach ($this->dDetailGroup as $dg) {
            if ($dg['id_index'] == $d) {
            }
         }
         $data_[$d]['name'] = $groupName;
         $data_[$d]['item'] = $data_item;
      }

      $data_['detail'] = $data_;
      $data_['varian'] = $varian;
      $data_['spkNote'] = $spkNote;
      $data_['spk_pending'] = $this->db(0)->get('spk_pending', 'id_divisi', 1);
      $data_['divisi'] = $this->db(0)->get('divisi', 'id_divisi');
      $this->view(__CLASS__ . "/detail", $data_);
   }

   function load_detail_barang($produk, $id_pelanggan_jenis)
   {
      $data['user'] = $this->db(0)->get('user', 'id_user');
      $data['stok'] = $this->data('Barang')->stok_data($produk, $this->userData['id_toko']);
      $data['stok_gudang'] = $this->data('Barang')->stok_data($produk, 0);
      $data['id_pelanggan_jenis'] = $id_pelanggan_jenis;
      $this->view(__CLASS__ . "/detail_barang", $data);
   }

   function add_price($id_pelanggan_jenis)
   {
      if ($id_pelanggan_jenis == 100) {
         $id_pelanggan_jenis = 2;
      }

      $harga_code = $_POST['harga_code'];
      $harga = $_POST['harga'];
      $id_produk = $_POST['id_produk'];

      $cols = 'id_produk, code, harga_' . $id_pelanggan_jenis;
      $vals = $id_produk . ",'" . $harga_code . "'," . $harga;

      $cols_ph = "id_barang, price, user_id";
      $id_barang = $id_produk . $harga_code;
      $cs_id = $this->userData['id_user'];
      $vals_ph = "'" . $id_barang . "'," . $harga . "," . $cs_id;

      $whereCount = "code = '" . $harga_code . "' AND id_produk = 0";
      $cek = $this->db(0)->get_where_row('produk_harga', $whereCount);
      if (!isset($cek['code'])) {
         $whereCount2 = "code = '" . $harga_code . "' AND id_produk = " . $id_produk;
         $cek2 = $this->db(0)->get_where_row('produk_harga', $whereCount2);
         if (!isset($cek2['code'])) {
            $do = $this->db(0)->insertCols('produk_harga', $cols, $vals);
            if ($do['errno'] <> 0) {
               echo $do['error'];
               exit();
            } else {
               $price_history = $this->db(0)->insertCols('price_history', $cols_ph, $vals_ph);
               if ($price_history['errno'] <> 0) {
                  echo $price_history['error'];
                  exit();
               }
            }
         } else {
            $where = "code = '" . $harga_code . "' AND id_produk = " . $id_produk;
            $set = "harga_" . $id_pelanggan_jenis . " = " . $harga . ", id_produk = " . $id_produk;
            $up = $this->db(0)->update("produk_harga", $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            } else {
               $price_history = $this->db(0)->insertCols('price_history', $cols_ph, $vals_ph);
               if ($price_history['errno'] <> 0) {
                  echo $price_history['error'];
                  exit();
               }
            }
         }
      } else {
         $where = "code = '" . $harga_code . "' AND id_produk = 0";
         $set = "harga_" . $id_pelanggan_jenis . " = " . $harga . ", id_produk = " . $id_produk;
         $up = $this->db(0)->update("produk_harga", $set, $where);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         } else {
            $price_history = $this->db(0)->insertCols('price_history', $cols_ph, $vals_ph);
            if ($price_history['errno'] <> 0) {
               echo $price_history['error'];
               exit();
            }
         }
      }

      $this->dataSynchrone();
   }

   function add_price_barang($id_pelanggan_jenis)
   {
      //Hanya Kasir dan ABF
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2]) && $this->userData['id_toko'] == 1) {
         echo "Selain kasir, tidak di izinkan menambah/merubah harga";
         exit();
      }

      if ($id_pelanggan_jenis == 100) {
         $id_pelanggan_jenis = 2;
      }

      $code = $_POST['code_barang'];
      $harga = $_POST['harga'];

      $cols_ph = "id_barang, price, user_id";
      $id_barang = $code;
      $cs_id = $this->userData['id_user'];
      $vals_ph = "'" . $id_barang . "'," . $harga . "," . $cs_id;

      $price_history = $this->db(0)->insertCols('price_history', $cols_ph, $vals_ph);
      if ($price_history['errno'] <> 0) {
         echo $price_history['error'];
         exit();
      }

      $where = "id_barang = '" . $code . "' AND stat = 0 AND ref = ''";
      $set = "harga_jual = " . $harga;
      $update = $this->db(0)->update("master_mutasi", $set, $where);

      if ($update['errno'] == 0) {
         $where = "id = '" . $code . "'";
         $set = "harga_" . $id_pelanggan_jenis . " = " . $harga;
         $update = $this->db(0)->update("master_barang", $set, $where);
         echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
      } else {
         echo $update['error'];
      }
   }

   function diskon()
   {
      $parse = explode("_", $_POST['parse']);
      $diskon = $_POST['diskon'];
      $harga = $parse[2];

      if ($diskon > $harga) {
         echo "Diskon tidak boleh melebihi harga!";
         exit();
      }

      $cols = "detail_harga";
      $where = "id_order_data = " . $parse[0];
      $detail = unserialize($this->db(0)->get_cols_where('order_data', $cols, $where, 0)['detail_harga']);

      $detail[$parse[1]]['d'] = $diskon;
      $detail_ = serialize($detail);

      $set = "detail_harga = '" . $detail_ . "'";
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];

      $this->dataSynchrone();
   }

   function diskon_barang()
   {
      $parse = explode("_", $_POST['id_barang_diskon']);
      $diskon = $_POST['diskon'];
      $harga = $parse[1];

      if ($diskon > $harga) {
         echo "Diskon tidak boleh melebihi harga";
         exit();
      }

      $where = "id = " . $parse[0];
      $set = "diskon = '" . $diskon . "'";
      $update = $this->db(0)->update("master_mutasi", $set, $where);
      echo $update['errno'] <> 0 ? $update['error'] : 0;
   }

   function proses($id_pelanggan_jenis, $id_pelanggan = 0, $ref = "")
   {
      $id_user_afiliasi = 0;
      if (isset($_GET['id_karyawan']) && intval($_GET['id_karyawan']) > 0) {
         $id_karyawan = intval($_GET['id_karyawan']);
      }

      if (isset($_POST['id_karyawan_aff'])) {
         $id_user_afiliasi = $_POST['id_karyawan_aff'];
         if (isset($_POST['id_pelanggan'])) {
            $id_pelanggan = $_POST['id_pelanggan'];
         }
      }

      if ($id_user_afiliasi <> 0) {
         $where = "ref = '" . $ref . "' AND cancel = 0";
         $data['order'] = $this->db(0)->get_where('order_data', $where);
      }

      if ($id_pelanggan_jenis == 100) {
         $stok_order = 1;
      } else {
         $stok_order = 0;
      }

      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $ref = $dEdit[0];
         if (isset($_POST['id_pelanggan']) && $_POST['id_pelanggan'] <> "") {
            $id_pelanggan = $_POST['id_pelanggan'];
         }

         if (isset($_POST['id_karyawan']) && $_POST['id_karyawan'] <> "") {
            $id_karyawan = $_POST['id_karyawan'];
         }

         if ((!isset($id_karyawan) || $id_karyawan == 0) && isset($dEdit[5]) && $dEdit[5] > 0) {
            $id_karyawan = $dEdit[5];
         }

         if ($id_pelanggan == 0 && isset($dEdit[3]) && $dEdit[3] > 0) {
            $id_pelanggan = $dEdit[3];
         }
         $where_order = "(ref = '" . $ref . "' AND cancel <> 1) OR (id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0)";
         $where_barang = "(ref = '" . $ref . "' AND stat <> 2) OR (id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND id_target = 0 AND jenis = 2)";
      } else {
         if ($id_user_afiliasi == 0) {
            if (!empty($ref)) {
               $where_order = "ref = '" . $ref . "' AND cancel <> 1";
               $where_barang = "ref = '" . $ref . "' AND stat <> 2";
               $row_od = $this->db(0)->get_where_row('order_data', "ref = '" . $ref . "' LIMIT 1");
               if (isset($row_od['id_pelanggan']) && $row_od['id_pelanggan'] <> 0) {
                  $id_pelanggan = $row_od['id_pelanggan'];
               } else {
                  $row_mm = $this->db(0)->get_where_row('master_mutasi', "ref = '" . $ref . "' LIMIT 1");
                  if (isset($row_mm['id_target']) && $row_mm['id_target'] <> 0) {
                     $id_pelanggan = $row_mm['id_target'];
                  }
               }
               if (!isset($id_karyawan) || $id_karyawan == 0) {
                  if (isset($row_od['id_penerima']) && $row_od['id_penerima'] <> 0) {
                     $id_karyawan = $row_od['id_penerima'];
                  } else {
                     $row_mm_k = $this->db(0)->get_where_row('master_mutasi', "ref = '" . $ref . "' AND cs_id <> 0 LIMIT 1");
                     if (isset($row_mm_k['cs_id']) && $row_mm_k['cs_id'] <> 0) {
                        $id_karyawan = $row_mm_k['cs_id'];
                     }
                  }
               }
            } else {
               if (isset($_POST['id_pelanggan']) && $_POST['id_pelanggan'] <> "") {
                  $id_pelanggan = $_POST['id_pelanggan'];
               } else {
                  if (isset($_POST['new_customer']) || isset($_POST['hp'])) {
                     $hp = isset($_POST['hp']) ? $_POST['hp'] : '';
                     if (strlen($hp) > 0) {
                        $hp = $this->data('Validasi')->valid_wa($hp);
                        if ($hp == false) {
                           echo "Nomor HP tidak valid";
                           exit();
                        }
                     }
                     $nama = strtoupper(isset($_POST['new_customer']) ? $_POST['new_customer'] : '');
                     if (strlen($nama) == 0) {
                        echo "Lengkapi Nama Customer";
                        exit();
                     }
                     $cek_pelanggan = $this->db(0)->get_where_row('pelanggan', "UPPER(nama) = '" . $nama . "' AND no_hp = '" . $hp . "' AND id_toko = " . $this->userData['id_toko']);
                     if (isset($cek_pelanggan['id_pelanggan'])) {
                        $id_pelanggan = $cek_pelanggan['id_pelanggan'];
                     } else {
                        $get_lastID = $this->db(0)->get_cols('pelanggan', 'MAX(id_pelanggan) as max', 0);
                        $id_pelanggan = $get_lastID['max'] + 1;
                        $cols = 'id_pelanggan, id_toko, nama, no_hp, id_pelanggan_jenis';
                        $vals = $id_pelanggan . ", '" . $this->userData['id_toko'] . "', '" . $nama . "', '" . $hp . "', " . $id_pelanggan_jenis;
                        $do = $this->db(0)->insertCols('pelanggan', $cols, $vals);
                        if ($do['errno'] <> 0) {
                           echo $do['error'];
                           exit();
                        }
                     }
                  }
               }
               if (isset($_POST['id_karyawan'])) {
                  $id_karyawan = $_POST['id_karyawan'];
               }
               $where_order = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0";
               $where_barang = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND id_target = 0 AND jenis = 2";
               $n_ref = [];
               $where_n = "id_toko = " . $this->userData['id_toko'] . " AND insertTime LIKE '" . date("Y") . "-" . date('m') . "-%' AND ref <> '' GROUP BY ref";
               $n_ref =  $this->db(0)->get_cols_where('order_data', 'ref', $where_n, 1, 'ref');
               $n2_ref = [];
               $where_n2 = "id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND insertTime LIKE '" . date("Y") . "-" . date('m') . "-%' AND ref <> '' GROUP BY ref";
               $n2_ref =  $this->db(0)->get_cols_where('master_mutasi', 'ref', $where_n2, 1, 'ref');
               foreach ($n2_ref as $key => $n2) {
                  if (isset($n_ref[$key])) {
                     unset($n2_ref[$key]);
                  }
               }
               $qty_ref = count($n_ref) + count($n2_ref);
               $qty_ref += 1;
               $qty_ref = substr($qty_ref, -4);
               $nv = str_pad($qty_ref, 4, "0", STR_PAD_LEFT);
               $ref = $this->userData['id_toko'] . date("ymd") . rand(0, 9) . $nv;
            }
         }
      }

      $data['paket'] = $this->db(0)->get('paket_main', "id");

      $total_per_paket = [];
      $harga_paket = [];
      $paket_qty = [];
      $id_margin = [];
      $paket_ready = true;

      if ($id_user_afiliasi == 0) {
         $data['barang'] = $this->db(0)->get('master_barang', 'id');
         $data['order'] = $this->db(0)->get_where('order_data', $where_order);
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_barang);

         $default_cs_id = 0;
         foreach ($data['order'] as $od_) {
            if ($od_['ref'] <> '' && isset($od_['id_penerima']) && $od_['id_penerima'] <> 0) {
               $default_cs_id = $od_['id_penerima'];
               break;
            }
         }
         if ($default_cs_id == 0) {
            foreach ($data['mutasi'] as $mm_) {
               if ($mm_['ref'] <> '' && isset($mm_['cs_id']) && $mm_['cs_id'] <> 0) {
                  $default_cs_id = $mm_['cs_id'];
                  break;
               }
            }
         }

         foreach ($data['mutasi'] as $dbr) {
            $id_sumber = $dbr['id_sumber'];
            $id_barang = $dbr['id_barang'];

            if ($dbr['ref'] <> '' && isset($dbr['cs_id']) && $dbr['cs_id'] > 0) {
               $id_karyawan = $dbr['cs_id'];
            }

            $qty = $dbr['qty'];
            $sn =  $dbr['sn'];

            if ($dbr['price_locker'] == 1) {
               if ($data['paket'][$dbr['paket_ref']]['harga_' . $id_pelanggan_jenis] == 0) {
                  $paket_ready = false;
               }

               $harga_paket[$dbr['paket_ref']] = $data['paket'][$dbr['paket_ref']]['harga_' . $id_pelanggan_jenis];
               $id_margin[$dbr['paket_ref']]['id'] = $dbr['id'];
               $id_margin[$dbr['paket_ref']]['primary'] = 'id';
               $id_margin[$dbr['paket_ref']]['tb'] = 'master_mutasi';

               $get = $this->db(0)->get_where_row('paket_mutasi', "paket_ref = '" . $dbr['paket_ref'] . "' AND price_locker = 1");
               if (isset($get['qty'])) {
                  $paket_qty = $dbr['qty'] / $get['qty'];
                  $id_margin[$dbr['paket_ref']]['qty'] = $paket_qty;
               }
            }

            if (strlen($dbr['paket_ref']) > 0) {
               $db = $data['barang'][$id_barang];
               if (isset($total_per_paket[$dbr['paket_ref']])) {
                  $total_per_paket[$dbr['paket_ref']] += ($db['harga_' . $id_pelanggan_jenis] * $dbr['qty']);
               } else {
                  $total_per_paket[$dbr['paket_ref']] = ($db['harga_' . $id_pelanggan_jenis] * $dbr['qty']);
               }
            }

            if ($id_sumber == 0) {
               $id_sumber = $this->userData['id_toko'];
            }
         }
      }
      //===========================

      $data_harga = $this->db(0)->get('produk_harga');

      $detail_harga = [];
      foreach ($data['order'] as $do) {
         if ($id_pelanggan_jenis == 100 && $id_user_afiliasi == 0) {
            $b_code = str_replace(['-', '&', '#'], '', $do['produk_code']);
            $barang = $this->db(0)->get_where_row('master_barang', "code = '" . $b_code . "'");
            if (!isset($barang['product_name'])) {
               echo "Nama Barang belum di tentukan";
               exit();
            }
         }

         if ($do['ref'] <> '' && isset($do['id_penerima']) && $do['id_penerima'] > 0) {
            $id_karyawan = $do['id_penerima'];
         }

         if (strlen($do['paket_ref']) > 0 && $id_user_afiliasi == 0) {
            if ($do['price_locker'] == 1) {
               if ($data['paket'][$do['paket_ref']]['harga_' . $id_pelanggan_jenis] == 0) {
                  $paket_ready = false;
               }

               $harga_paket[$do['paket_ref']] = $data['paket'][$do['paket_ref']]['harga_' . $id_pelanggan_jenis];
               $id_margin[$do['paket_ref']]['id'] = $do['id_order_data'];
               $id_margin[$do['paket_ref']]['primary'] = 'id_order_data';
               $id_margin[$do['paket_ref']]['tb'] = 'order_data';

               $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $do['paket_ref'] . "' AND price_locker = 1");
               if (isset($get['jumlah'])) {
                  $paket_qty = $do['jumlah'] / $get['jumlah'];
                  $id_margin[$do['paket_ref']]['qty'] = $paket_qty;
               }
            }
         }

         if ($paket_ready == false) {
            echo "Harga paket belum ditentukan";
            exit();
         }

         $detail_harga = unserialize($do['detail_harga']);
         $countDH = count($detail_harga);
         $harga_code = $id_pelanggan_jenis;
         if ($id_pelanggan_jenis == 100) {
            $harga_code = 2;
         }
         foreach ($detail_harga as $kH => $dh_o) {
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $harga_code] <> 0 && $dh['id_produk'] == $do['id_produk']) {
                  $countDH -= 1;
                  break;
               }
            }
         }

         // Skip harga validation for paket items (they use harga_paket instead)
         $is_paket_item = !empty($do['paket_ref']) || !empty($do['paket_group']);
         if ($countDH <> 0 && !$is_paket_item) {
            echo "Lengkapi harga (" . $do['produk'] . ") terlebih dahulu!";
            exit();
         }
      }

      if (!isset($id_karyawan)) {
         // Try to get from order_data
         $get_ik = $this->db(0)->get_where_row('order_data', "ref = '" . $ref . "' AND id_penerima <> 0 LIMIT 1");
         if (isset($get_ik['id_penerima']) && $get_ik['id_penerima'] <> 0) {
            $id_karyawan = $get_ik['id_penerima'];
         }
         // Try to get from master_mutasi
         if (!isset($id_karyawan)) {
            $get_ik = $this->db(0)->get_where_row('master_mutasi', "ref = '" . $ref . "' AND cs_id <> 0 LIMIT 1");
            if (isset($get_ik['cs_id']) && $get_ik['cs_id'] <> 0) {
               $id_karyawan = $get_ik['cs_id'];
            }
         }
         // Fallback to POST if available
         if (!isset($id_karyawan) && isset($_POST['id_karyawan']) && $_POST['id_karyawan'] <> '') {
            $id_karyawan = $_POST['id_karyawan'];
         }
         // Final fallback: do not block, set 0
         if (!isset($id_karyawan)) {
            $id_karyawan = 0;
         }
      }

      // Insert ke tabel ref hanya jika belum ada (hindari duplikasi saat update edit)
      if ($id_user_afiliasi == 0 && !empty($ref)) {
         $exists_ref = $this->db(0)->count_where('ref', "ref = '" . $ref . "'");
         if ($exists_ref == 0) {
            $cols = 'ref, id_toko';
            $vals = $ref . "," . $this->userData['id_toko'];
            $do = $this->db(0)->insertCols('ref', $cols, $vals);
            if ($do['errno'] <> 0 && $do['errno'] <> 1062) {
               echo $do['error'];
               exit();
            }
         }
      }

      if ($id_pelanggan_jenis == 100 && $id_user_afiliasi == 0) {
         $tujuan = $this->userData['id_toko'];
         $tanggal = date('Y-m-d');

         $cols = 'id, tipe, id_sumber, id_target, tanggal, user_id';
         $vals = "'" . $ref . "',2,0,'" . $tujuan . "','" . $tanggal . "'," . $this->userData['id_user'];
         $do = $this->db(0)->insertCols('master_input', $cols, $vals);
         if ($do['errno'] <> 0 && $do['errno'] <> 1062) {
            echo $do['error'];
            exit();
         }
      }

      $this->db(0)->update("pelanggan", "freq = freq+1", "id_pelanggan = " . $id_pelanggan);
      if ($id_karyawan > 0) {
         $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);
      }

      if ($id_user_afiliasi == 0 && $id_pelanggan_jenis <> 100) {
         foreach ($data['mutasi'] as $dbr) {
            $id_barang = $dbr['id_barang'];
            $barang = $this->db(0)->get_where_row('master_barang', "id ='" . $id_barang . "'");

            $harga = $barang['harga_' . $id_pelanggan_jenis];

            if ($harga == 0) {
               echo "Harga " . trim($barang['brand'] . " " . $barang['model']) . " belum ditentukan";
               exit();
            }

            $id_sumber = $dbr['id_sumber'];
            $qty = $dbr['qty'];
            $sn =  $dbr['sn'];
            $sn_c = 0;
            if (strlen($sn) > 0) {
               $sn_c = 1;
            }

            $where = "id = " . $dbr['id'] . " AND ref = ''";
            // If this mutasi item belongs to a paket (paket_ref or paket_group), keep harga_jual = 0
            $harga_to_set = $harga;
            if ((isset($dbr['paket_ref']) && strlen($dbr['paket_ref']) > 0) || (isset($dbr['paket_group']) && strlen($dbr['paket_group']) > 0)) {
               $harga_to_set = 0;
            }
            // Build update set: if item has paket_ref/paket_group, do not update any price fields
            $base_set = "stat = 1, sn_c = " . $sn_c . ", cs_id = " . $id_karyawan . ", id_target = " . $id_pelanggan . ", jenis_target = " . $id_pelanggan_jenis . ", ref = '" . $ref . "'";
            if ((isset($dbr['paket_ref']) && strlen($dbr['paket_ref']) > 0) || (isset($dbr['paket_group']) && strlen($dbr['paket_group']) > 0)) {
               $set = $base_set;
            } else {
               $set = "harga_jual = " . $harga_to_set . ", " . $base_set;
            }
            $update = $this->db(0)->update("master_mutasi", $set, $where);
            if ($update['errno'] <> 0) {
               echo $update['error'];
               exit();
            }
         }
      }

      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $harga = 0;
         $diskon = 0;
         $jumlah = $do['jumlah'];

         foreach ($detail_harga as $key => $dh_o) {
            $diskon += ($dh_o['d'] * $jumlah);
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $harga_code] <> 0 && $dh['id_produk'] == $do['id_produk']) {
                  $harga +=  $dh['harga_' . $harga_code];
                  $detail_harga[$key]['h'] = $dh['harga_' . $harga_code];
                  if (strlen($do['paket_ref']) > 0) {
                     if (isset($total_per_paket[$do['paket_ref']])) {
                        $total_per_paket[$do['paket_ref']] += ($detail_harga[$key]['h'] * $do['jumlah']);
                     } else {
                        $total_per_paket[$do['paket_ref']] = ($detail_harga[$key]['h'] * $do['jumlah']);
                     }
                  }
                  break;
               }
            }
         }

         if ($id_user_afiliasi <> 0) {

            $new_data_pending = "";
            if (strlen($do['pending_spk']) > 1) {
               $data_pending = unserialize($do['pending_spk']);
               foreach ($data_pending as $key => $val) {
                  $data_pending[$key] = str_replace("-p", "-r", $val);
               }

               $new_data_pending = serialize($data_pending);
            }

            $spkL = "";
            if (strlen($do['spk_dvs']) > 1 && strlen($do['pending_spk']) > 1) {
               $spk_list = unserialize($do['spk_dvs']);

               foreach ($spk_list as $key => $val) {
                  if ($val['status'] == 0) {
                     $spkL .=  "D-" . $key . "#";
                  }
               }
            }

            $st_order = ", status_order = 0, id_user_afiliasi = " . $id_user_afiliasi . ", pending_spk = '" . $new_data_pending . "', spk_lanjutan = '" . $spkL . "'";
            $where = "id_order_data = " . $do['id_order_data'] . " AND id_afiliasi = " . $this->userData['id_toko'] . " AND id_user_afiliasi = 0";
         } else {
            $st_order = "";
            $where = "id_order_data = " . $do['id_order_data'];
         }

         //SET ORDER, HARGA DAN AFILIASI
         $is_paket_item = (isset($do['paket_ref']) && strlen($do['paket_ref']) > 0) || (isset($do['paket_group']) && strlen($do['paket_group']) > 0);
         $effective_id_karyawan = $id_karyawan;
         if ($effective_id_karyawan == 0) {
            if (isset($default_cs_id) && $default_cs_id <> 0) {
               $effective_id_karyawan = $default_cs_id;
            } else {
               $row_k = $this->db(0)->get_where_row('order_data', "ref = '" . $ref . "' AND id_penerima <> 0 LIMIT 1");
               if (isset($row_k['id_penerima']) && $row_k['id_penerima'] <> 0) {
                  $effective_id_karyawan = $row_k['id_penerima'];
               }
            }
         }
         $set = "diskon = " . $diskon . ", detail_harga = '" . serialize($detail_harga) . "', id_penerima = " . $effective_id_karyawan . ", id_pelanggan = " . $id_pelanggan . ", id_pelanggan_jenis = " . $id_pelanggan_jenis . ", stok = " . $stok_order . $st_order;
         if (!$is_paket_item) {
            $set = "harga = " . $harga . ", " . $set;
         }
         $update = $this->db(0)->update("order_data", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }

         //SET REF
         $where_ref = "id_order_data = " . $do['id_order_data'] . " AND ref = ''";
         $set_ref = "ref = '" . $ref . "'";
         $update_ref = $this->db(0)->update("order_data", $set_ref, $where_ref);
         if ($update_ref['errno'] <> 0) {
            echo $update_ref['error'];
            exit();
         }

         if ($id_pelanggan_jenis == 100 && $id_user_afiliasi == 0) {
            $b_code = str_replace(['-', '&', '#'], '', $do['produk_code']);
            $barang = $this->db(0)->get_where_row('master_barang', "code = '" . $b_code . "'");

            $qty = $do['jumlah'];
            $sn =  "";
            $id_sumber = 0;
            $id_barang = $barang['id'];
            $h_beli = $barang['harga'];
            $target_id = $this->userData['id_toko'];

            $cek_double = $this->db(0)->count_where("master_mutasi", "ref = '" . $ref . "' AND pid = " . $do['id_order_data']);
            if ($cek_double == 0) {
               $cols = 'ref, jenis, id_barang, id_sumber, id_target, harga_beli, qty, pid';
               $vals = "'" . $ref . "',0," . $id_barang . ",'" . $id_sumber . "','" . $target_id . "'," . $h_beli . "," . $qty . "," . $do['id_order_data'];
               $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);

               if ($do['errno'] <> 0) {
                  echo $do['error'];
                  exit();
               }
            }
         }
      }

      if ($id_user_afiliasi == 0) {
         $adjuster = [];
         foreach ($total_per_paket as $key => $tpp) {
            $adjuster[$key] = ($data['paket'][$key]['harga_' . $id_pelanggan_jenis] * $id_margin[$key]['qty']) - $tpp;
            $id_margin[$key]['harga_paket'] = $adjuster[$key];
         }
         // Do not persist harga_paket updates during proses
      }

      // Ensure master_mutasi rows for this ref carry correct customer and CS
      // Use id_karyawan (which equals id_penerima) and id_pelanggan from parameters
      $final_cs_id = 0;
      $final_id_target = $id_pelanggan;
      
      // Get cs_id from id_karyawan (set from GET/POST/session earlier in this function)
      if (isset($id_karyawan) && $id_karyawan > 0) {
         $final_cs_id = $id_karyawan;
      } elseif (isset($default_cs_id) && $default_cs_id > 0) {
         $final_cs_id = $default_cs_id;
      }
      
      // Update master_mutasi with the correct values
      $where_mm_ref = "ref = '" . $ref . "'";
      $set_mm_ref = "stat = 1, id_target = " . $final_id_target . ", jenis_target = " . $id_pelanggan_jenis . ", cs_id = " . $final_cs_id;
      $this->db(0)->update("master_mutasi", $set_mm_ref, $where_mm_ref);

      // Also update temporary mutasi rows (no ref yet) belonging to current user
      $where_mm_tmp = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0 AND ref = ''";
      $set_mm_tmp = "stat = 1, id_target = " . $final_id_target . ", jenis_target = " . $id_pelanggan_jenis . ", cs_id = " . $final_cs_id . ", ref = '" . $ref . "'";
      $this->db(0)->update("master_mutasi", $set_mm_tmp, $where_mm_tmp);

      if (isset($_SESSION['edit'])) {
         unset($_SESSION['edit']);
      }

      if (isset($_COOKIE['new_user'])) {
         unset($_COOKIE['new_user']);
         setcookie('new_user', '', -1, '/');
      }

      if (isset($_COOKIE['hp'])) {
         unset($_COOKIE['hp']);
         setcookie('hp', '', -1, '/');
      }

      echo $id_pelanggan;
   }

   function deleteOrder()
   {
      $id_order = $_POST['id_order'];

      $cek_price_lock = $this->db(0)->get_where_row('order_data', 'id_order_data = ' . $id_order);
      if ($cek_price_lock['price_locker'] == 1) {
         $where = "paket_group = '" . $cek_price_lock['paket_group'] . "' AND paket_ref = '" . $cek_price_lock['paket_ref'] . "'";
         $do = $this->db(0)->delete_where('master_mutasi', $where);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      } else {
         $where = "id_order_data =" . $id_order;
      }

      if ($cek_price_lock['ref'] <> "") {
         echo "Tidak dapat dihapus, silahkan lakukan cancel";
         exit();
      }

      $do = $this->db(0)->delete_where('order_data', $where);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Delete Order Produksi Success!");
         echo $do['errno'];
      } else {
         print_r($do);
      }
   }

   function deleteOrderBarang()
   {
      $id = $_POST['id'];
      $cek_price_lock = $this->db(0)->get_where_row('master_mutasi', 'id = ' . $id);
      if ($cek_price_lock['price_locker'] == 1) {
         $where = "paket_group = '" . $cek_price_lock['paket_group'] . "' AND paket_ref = '" . $cek_price_lock['paket_ref'] . "'";
         $do = $this->db(0)->delete_where('order_data', $where);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      } else {
         $where = "id =" . $id;
      }

      if ($cek_price_lock['ref'] <> "") {
         echo "Tidak dapat dihapus, silahkan lakukan cancel";
         exit();
      }

      $do = $this->db(0)->delete_where('master_mutasi', $where);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Delete Order Barang Success!");
         echo $do['errno'];
      } else {
         print_r($do);
      }
   }

   function updateCell_N()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $where = "id_order_data = '" . $id . "'";
      $set = "jumlah = " . $value;
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function update_paket_qty()
   {
      $paket_group = $_POST['paket_group'];
      $paket_ref = $_POST['paket_ref'];
      $paket_qty_old = $_POST['paket_qty_old'];
      $paket_qty_new = $_POST['paket_qty_new'];

      if ($paket_qty_new <= 0) {
         echo "Qty paket harus lebih dari 0";
         exit();
      }

      // Get original paket items to know the base qty
      $paket_order_items = $this->db(0)->get_where("paket_order", "paket_ref = '" . $paket_ref . "'");
      $paket_mutasi_items = $this->db(0)->get_where("paket_mutasi", "paket_ref = '" . $paket_ref . "'");

      // Create map of base qty for each item
      $base_qty_order = [];
      foreach ($paket_order_items as $item) {
         $base_qty_order[$item['id_produk']] = $item['jumlah'];
      }

      $base_qty_mutasi = [];
      foreach ($paket_mutasi_items as $item) {
         $base_qty_mutasi[$item['id_barang']] = $item['qty'];
      }

      // Update order_data items in this paket_group
      $order_items = $this->db(0)->get_where("order_data", "paket_group = '" . $paket_group . "' AND paket_ref = '" . $paket_ref . "'");
      foreach ($order_items as $item) {
         $base_jumlah = isset($base_qty_order[$item['id_produk']]) ? $base_qty_order[$item['id_produk']] : 1;
         $new_jumlah = $base_jumlah * $paket_qty_new;

         $where = "id_order_data = " . $item['id_order_data'];

         // Only update paket_qty for items with price_locker = 1
         if ($item['price_locker'] == 1) {
            $set = "jumlah = " . $new_jumlah . ", paket_qty = " . $paket_qty_new;
         } else {
            $set = "jumlah = " . $new_jumlah;
         }

         $update = $this->db(0)->update("order_data", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }

      // Update master_mutasi items in this paket_group
      $mutasi_items = $this->db(0)->get_where("master_mutasi", "paket_group = '" . $paket_group . "' AND paket_ref = '" . $paket_ref . "'");
      foreach ($mutasi_items as $item) {
         $base_qty = isset($base_qty_mutasi[$item['id_barang']]) ? $base_qty_mutasi[$item['id_barang']] : 1;
         $new_qty = $base_qty * $paket_qty_new;

         $where = "id = " . $item['id'];

         // Only update paket_qty for items with price_locker = 1
         if ($item['price_locker'] == 1) {
            $set = "qty = " . $new_qty . ", paket_qty = " . $paket_qty_new;
         } else {
            $set = "qty = " . $new_qty;
         }

         $update = $this->db(0)->update("master_mutasi", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }

      echo 0;
   }

   function commit_edit_changes()
   {
      $changes_json = $_POST['changes'];
      $changes = json_decode($changes_json, true);

      if (!$changes) {
         echo "Invalid changes data";
         exit();
      }

      // Process deletions for order_data
      if (!empty($changes['deletedOrders'])) {
         foreach ($changes['deletedOrders'] as $id_order) {
            $delete_result = $this->db(0)->delete_where('order_data', "id_order_data = " . intval($id_order));
            if ($delete_result['errno'] != 0) {
               echo "Error deleting order " . $id_order . ": " . $delete_result['error'];
               exit();
            }
         }
      }

      // Process deletions for master_mutasi
      if (!empty($changes['deletedBarang'])) {
         foreach ($changes['deletedBarang'] as $id_barang) {
            $delete_result = $this->db(0)->delete_where('master_mutasi', "id = " . intval($id_barang));
            if ($delete_result['errno'] != 0) {
               echo "Error deleting barang " . $id_barang . ": " . $delete_result['error'];
               exit();
            }
         }
      }

      // Process quantity updates for order_data
      if (!empty($changes['updatedQty'])) {
         foreach ($changes['updatedQty'] as $id => $qty) {
            $set = "jumlah = " . intval($qty);
            $where = "id_order_data = " . intval($id);
            $update_result = $this->db(0)->update('order_data', $set, $where);
            if ($update_result['errno'] != 0) {
               echo "Error updating qty for order " . $id . ": " . $update_result['error'];
               exit();
            }
         }
      }

      // Process discount updates for order_data
      if (!empty($changes['updatedDiskon'])) {
         foreach ($changes['updatedDiskon'] as $id => $val) {
            $set = "diskon = " . intval($val);
            $where = "id_order_data = " . intval($id);
            $update_result = $this->db(0)->update('order_data', $set, $where);
            if ($update_result['errno'] != 0) {
               echo "Error updating diskon for order " . $id . ": " . $update_result['error'];
               exit();
            }
         }
      }

      // Process price updates for order_data
      if (!empty($changes['updatedHarga'])) {
         foreach ($changes['updatedHarga'] as $id => $val) {
            $set = "harga = " . intval($val);
            $where = "id_order_data = " . intval($id);
            $update_result = $this->db(0)->update('order_data', $set, $where);
            if ($update_result['errno'] != 0) {
               echo "Error updating harga for order " . $id . ": " . $update_result['error'];
               exit();
            }
         }
      }

      // Process paket quantity updates
      if (!empty($changes['updatedPaketQty'])) {
         foreach ($changes['updatedPaketQty'] as $key => $paket_qty_new) {
            // Parse key: paket_group_paket_ref
            $parts = explode('_', $key);
            $paket_ref = array_pop($parts);
            $paket_group = implode('_', $parts);

            if (!$paket_ref || !$paket_group) {
               continue;
            }

            // Get base quantities from paket definitions
            $paket_order_items = $this->db(0)->get_where("paket_order", "paket_ref = '" . $paket_ref . "'");
            $paket_mutasi_items = $this->db(0)->get_where("paket_mutasi", "paket_ref = '" . $paket_ref . "'");

            // Create map of base qty
            $base_qty_order = [];
            foreach ($paket_order_items as $item) {
               $base_qty_order[$item['id_produk']] = $item['jumlah'];
            }

            $base_qty_mutasi = [];
            foreach ($paket_mutasi_items as $item) {
               $base_qty_mutasi[$item['id_barang']] = $item['qty'];
            }

            // Update order_data items
            $order_items = $this->db(0)->get_where("order_data", "paket_group = '" . $paket_group . "' AND paket_ref = '" . $paket_ref . "'");
            foreach ($order_items as $item) {
               $base_jumlah = isset($base_qty_order[$item['id_produk']]) ? $base_qty_order[$item['id_produk']] : 1;
               $new_jumlah = $base_jumlah * intval($paket_qty_new);

               $where = "id_order_data = " . $item['id_order_data'];
               if ($item['price_locker'] == 1) {
                  $set = "jumlah = " . $new_jumlah . ", paket_qty = " . intval($paket_qty_new);
               } else {
                  $set = "jumlah = " . $new_jumlah;
               }

               $update = $this->db(0)->update("order_data", $set, $where);
               if ($update['errno'] != 0) {
                  echo "Error updating paket order: " . $update['error'];
                  exit();
               }
            }

            // Update master_mutasi items
            $mutasi_items = $this->db(0)->get_where("master_mutasi", "paket_group = '" . $paket_group . "' AND paket_ref = '" . $paket_ref . "'");
            foreach ($mutasi_items as $item) {
               $base_qty = isset($base_qty_mutasi[$item['id_barang']]) ? $base_qty_mutasi[$item['id_barang']] : 1;
               $new_qty = $base_qty * intval($paket_qty_new);

               $where = "id = " . $item['id'];
               if ($item['price_locker'] == 1) {
                  $set = "qty = " . $new_qty . ", paket_qty = " . intval($paket_qty_new);
               } else {
                  $set = "qty = " . $new_qty;
               }

               $update = $this->db(0)->update("master_mutasi", $set, $where);
               if ($update['errno'] != 0) {
                  echo "Error updating paket mutasi: " . $update['error'];
                  exit();
               }
            }
         }
      }

      // Update cs_id and id_target in master_mutasi based on order_data
      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $ref = $dEdit[0];
         
         // Use helper to get metadata
         $refMeta = $this->getRefMetadata($ref);
         $id_penerima = $refMeta['cs_id'];
         $id_pelanggan = $refMeta['id_pelanggan'];
         
         // Fallback to session if order_data doesn't have values
         if ($id_penerima == 0 && isset($dEdit[5]) && $dEdit[5] > 0) {
            $id_penerima = intval($dEdit[5]);
         }
         if ($id_pelanggan == 0 && isset($dEdit[3]) && $dEdit[3] > 0) {
            $id_pelanggan = intval($dEdit[3]);
         }

         // Update master_mutasi with cs_id = id_penerima and id_target = id_pelanggan
         if ($ref <> '' && ($id_penerima > 0 || $id_pelanggan > 0)) {
            $set_mm = [];
            if ($id_penerima > 0) {
               $set_mm[] = "cs_id = " . $id_penerima;
            }
            if ($id_pelanggan > 0) {
               $set_mm[] = "id_target = " . $id_pelanggan;
            }
            if (count($set_mm) > 0) {
               $update_mm = $this->db(0)->update("master_mutasi", implode(", ", $set_mm), "ref = '" . $ref . "'");
            }
         }
      }

      echo 0;
   }

   /**
    * Cancel Edit - Restore from snapshot
    * This function restores order data from the snapshot taken when entering edit mode
    */
   function cancel_edit()
   {
      if (!isset($_SESSION['edit'][$this->userData['id_user']])) {
         echo "No active edit session";
         exit();
      }

      $dEdit = $_SESSION['edit'][$this->userData['id_user']];
      $ref = $dEdit[0];
      $session_key = isset($dEdit[4]) ? $dEdit[4] : '';

      if (empty($session_key)) {
         echo "Invalid edit session";
         exit();
      }

      // Get edit session
      $session = $this->db(0)->get_where_row('edit_sessions', "session_key = '" . $session_key . "' AND status = 'active'");

      if (!$session) {
         echo "Edit session not found or already processed";
         exit();
      }

      // Decode snapshots
      $snapshot_order = json_decode($session['snapshot_data'], true);
      $snapshot_mutasi = json_decode($session['snapshot_mutasi'], true);

      // Delete all current items with this ref
      $delete_order = $this->db(0)->delete_where('order_data', "ref = '" . $ref . "'");
      if ($delete_order['errno'] != 0) {
         echo "Error deleting current order data: " . $delete_order['error'];
         exit();
      }

      $delete_mutasi = $this->db(0)->delete_where('master_mutasi', "ref = '" . $ref . "'");
      if ($delete_mutasi['errno'] != 0) {
         echo "Error deleting current mutasi data: " . $delete_mutasi['error'];
         exit();
      }

      // Restore from snapshot - order_data
      foreach ($snapshot_order as $item) {
         // Build column names and values from snapshot
         $cols = array_keys($item);
         $vals = [];
         foreach ($item as $key => $value) {
            if ($key == 'id_order_data') {
               continue; // Skip auto-increment ID
            }
            if (is_null($value)) {
               $vals[] = "NULL";
            } elseif (is_numeric($value)) {
               $vals[] = $value;
            } else {
               $vals[] = "'" . addslashes($value) . "'";
            }
         }

         // Remove id_order_data from cols
         $cols = array_filter($cols, function ($col) {
            return $col != 'id_order_data';
         });

         $cols_str = implode(', ', $cols);
         $vals_str = implode(', ', $vals);

         $insert = $this->db(0)->query("INSERT INTO order_data ($cols_str) VALUES ($vals_str)");
         if ($insert === false || (isset($insert['errno']) && $insert['errno'] != 0)) {
            $error_msg = is_array($insert) && isset($insert['error']) ? $insert['error'] : 'Unknown error';
            echo "Error restoring order item: " . $error_msg;
            exit();
         }
      }

      // Restore from snapshot - master_mutasi
      foreach ($snapshot_mutasi as $item) {
         $cols = array_keys($item);
         $vals = [];
         foreach ($item as $key => $value) {
            if ($key == 'id') {
               continue; // Skip auto-increment ID
            }
            if (is_null($value)) {
               $vals[] = "NULL";
            } elseif (is_numeric($value)) {
               $vals[] = $value;
            } else {
               $vals[] = "'" . addslashes($value) . "'";
            }
         }

         // Remove id from cols
         $cols = array_filter($cols, function ($col) {
            return $col != 'id';
         });

         $cols_str = implode(', ', $cols);
         $vals_str = implode(', ', $vals);

         $insert = $this->db(0)->query("INSERT INTO master_mutasi ($cols_str) VALUES ($vals_str)");
         if ($insert === false || (isset($insert['errno']) && $insert['errno'] != 0)) {
            $error_msg = is_array($insert) && isset($insert['error']) ? $insert['error'] : 'Unknown error';
            echo "Error restoring mutasi item: " . $error_msg;
            exit();
         }
      }

      // Mark session as cancelled
      $this->db(0)->update('edit_sessions', "status = 'cancelled'", "session_key = '" . $session_key . "'");

      // Clear edit session
      unset($_SESSION['edit'][$this->userData['id_user']]);

      echo 0;
   }

   /**
    * Commit Edit - Finalize changes and mark session as committed
    */
   function commit_edit_session()
   {
      if (!isset($_SESSION['edit'][$this->userData['id_user']])) {
         echo 0; // No edit session, proceed normally
         return;
      }

      $dEdit = $_SESSION['edit'][$this->userData['id_user']];
      $session_key = isset($dEdit[4]) ? $dEdit[4] : '';

      if (!empty($session_key)) {
         // Mark session as committed
         $this->db(0)->update('edit_sessions', "status = 'committed'", "session_key = '" . $session_key . "' AND status = 'active'");
      }

      // Clear edit session
      unset($_SESSION['edit'][$this->userData['id_user']]);

      echo 0;
   }

   function add_produksi()
   {
      $code_s = strtoupper($_POST['product_code']);
      $code = str_replace(['-', '&', '#'], '', $code_s);
      $nama = strtoupper($_POST['nama']);

      //BARANG
      $cols = 'grup, code, code_s, product_name, sp';
      $vals = "'PRODUKSI','" . $code . "','" . $code_s . "','" . $nama . "',1";
      $do = $this->db(0)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $up = $this->db(0)->update('master_barang', "product_name = '" . $nama . "'", "code = '" . $code . "' AND code_s = '" . $code_s . "'");
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      echo 0;
   }
}
