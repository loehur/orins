<?php

class Afiliasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[8]) && !in_array($this->userData['user_tipe'], PV::PRIV[5])) {
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
         "title" => "Audit - Afiliasi"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['_c'] = __CLASS__;
      $where = "metode_mutasi = 3 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->db(0)->get_where('kas', $where);

      $where = "metode_mutasi = 3 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 20";
      $data['kas_done'] = $this->db(0)->get_where('kas', $where);

      // Status tuntas dari tabel ref (sama seperti Non_Tunai) — dipakai untuk Re-Action
      $data['ref'] = [];
      if (is_array($data['kas_done']) && count($data['kas_done']) > 0) {
         $refs = [];
         foreach ($data['kas_done'] as $row) {
            $r = (string)($row['ref_transaksi'] ?? '');
            if ($r !== '') {
               $refs[$r] = true;
            }
         }
         $refKeys = array_keys($refs);
         if (count($refKeys) > 0) {
            $ref_list = "'" . implode("','", array_map('addslashes', $refKeys)) . "'";
            $data['ref'] = $this->db(0)->get_where('ref', "ref IN (" . $ref_list . ")", 'ref');
            if (!is_array($data['ref']) || isset($data['ref']['errno'])) {
               $data['ref'] = [];
            }
         }
      }

      $this->view($this->v_content, $data);
   }

   function action()
   {
      $id = $_POST['id'];
      $val = $_POST['val'];
      $note = $_POST['note'] ?? '';

      $where_kas = "id_kas = " . (int)$id;
      $kasRow = $this->db(0)->get_where_row("kas", $where_kas);
      if (!is_array($kasRow) || empty($kasRow['id_kas'])) {
         echo "Data kas tidak ditemukan";
         exit();
      }

      // Mirror Non_Tunai: un_tuntas hanya saat Reject
      if ((int)$val === 2) {
         $ref = (string)($kasRow['ref_transaksi'] ?? '');
         if ($ref !== '') {
            $undo = $this->data('Operasi')->un_tuntas($ref);
            if ($undo['status'] == 'failed') {
               echo $undo['error'];
               exit();
            }
         }
         $set = "note_batal = '" . addslashes($note) . "', status_mutasi = 2, id_audit_afiliasi = " . (int)$this->userData['id_user'];
      } else {
         $set = "note_office = '" . addslashes($note) . "', status_mutasi = 1, id_audit_afiliasi = " . (int)$this->userData['id_user'];
      }

      $update = $this->db(0)->update("kas", $set, $where_kas);
      echo $update['errno'];
   }

   function actionMulti()
   {
      $id = explode("_", $_POST['id']);
      $val = $_POST['val'];
      $note = $_POST['note'] ?? '';

      $processed_refs = [];
      foreach ($id as $i) {
         $i = (int)$i;
         if ($i <= 0) {
            continue;
         }

         $where_kas = "id_kas = " . $i;
         $kasRow = $this->db(0)->get_where_row("kas", $where_kas);
         if (!is_array($kasRow) || empty($kasRow['id_kas'])) {
            echo "Data kas #" . $i . " tidak ditemukan";
            exit();
         }

         if ((int)$val === 2) {
            $ref = (string)($kasRow['ref_transaksi'] ?? '');
            if ($ref !== '' && !in_array($ref, $processed_refs, true)) {
               $undo = $this->data('Operasi')->un_tuntas($ref);
               if ($undo['status'] == 'failed') {
                  echo "Ref " . $ref . ": " . $undo['error'];
                  exit();
               }
               $processed_refs[] = $ref;
            }
            $set = "note_batal = '" . addslashes($note) . "', status_mutasi = 2, id_audit_afiliasi = " . (int)$this->userData['id_user'];
         } else {
            $set = "note_office = '" . addslashes($note) . "', status_mutasi = 1, id_audit_afiliasi = " . (int)$this->userData['id_user'];
         }

         $update = $this->db(0)->update("kas", $set, $where_kas);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }

      echo 0;
   }

   function cekOrder($ref, $id_pelanggan)
   {
      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");
      $data['barang'] = $this->db(0)->get('master_barang', 'id');

      $where = "ref = '" . addslashes($ref) . "'";
      $data['order'] = [];
      $data['mutasi'] = [];
      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where);
      if (!is_array($data['order']) || isset($data['order']['errno'])) {
         $data['order'] = [];
      }
      if (!is_array($data['mutasi']) || isset($data['mutasi']['errno'])) {
         $data['mutasi'] = [];
      }

      $ref1 = array_unique(array_column($data['order'], 'ref'));
      $ref2 = array_unique(array_column($data['mutasi'], 'ref'));
      $refs = array_unique(array_merge($ref1, $ref2));

      $ref_list = "";
      foreach ($refs as $r) {
         $ref_list .= "'" . addslashes($r) . "',";
      }
      $ref_list = rtrim($ref_list, ',');

      if (count($refs) > 0) {
         $where = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where);

         $cols = "ref_bayar, metode_mutasi, sum(jumlah) as total, sum(bayar) as bayar, sum(kembali) as kembali, status_mutasi";
         $where_2 = "id_toko = " . $this->userData['id_toko'] . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ") GROUP BY ref_bayar";
         $data['r_kas'] = $this->db(0)->get_cols_where('kas', $cols, $where_2, 1);

         $where = "id_toko = " . $this->userData['id_toko'] . " AND ref_transaksi IN (" . $ref_list . ")";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where);
      }

      $data_ = [];
      $data['mode'] = 0;
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $data_m = [];
      foreach ($data['mutasi'] as $key => $do) {
         $data_m[$do['ref']][$key] = $do;
      }

      rsort($refs);
      $data['refs'] = $refs;
      $data['order'] = $data_;
      $data['mutasi'] = $data_m;
      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan);

      foreach ($refs as $r) {
         $data['head'][$r]['cs_to'] = 0;
         $data['head'][$r]['id_afiliasi'] = 0;
         $data['head'][$r]['tuntas'] = 0;
      }

      foreach ($data['order'] as $refK => $do) {
         foreach ($do as $dd) {
            $data['head'][$refK]['cs'] = $dd['id_penerima'];
            $data['head'][$refK]['cs_to'] = $dd['id_user_afiliasi'];
            $data['head'][$refK]['id_afiliasi'] = $dd['id_afiliasi'];
            $data['head'][$refK]['insertTime'] = $dd['insertTime'];
            $data['head'][$refK]['tuntas'] = $dd['tuntas'];
            break;
         }
      }

      foreach ($data['mutasi'] as $refK => $do) {
         foreach ($do as $dd) {
            if (!isset($data['head'][$refK]['cs'])) {
               $data['head'][$refK]['cs'] = $dd['cs_id'];
            }
            if (!isset($data['head'][$refK]['insertTime'])) {
               $data['head'][$refK]['insertTime'] = $dd['insertTime'];
            }
            // Jangan overwrite tuntas dari order jika sudah ada; isi jika baru dari mutasi
            if (!isset($data['head'][$refK]['tuntas']) || (int)$data['head'][$refK]['tuntas'] === 0) {
               $data['head'][$refK]['tuntas'] = $dd['tuntas'] ?? 0;
            }
            break;
         }
      }

      $data['id_pelanggan'] = $id_pelanggan;

      $this->view(__CLASS__ . "/cek", $data);
   }
}
