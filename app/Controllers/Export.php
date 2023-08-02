<?php

class Export extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      if (!in_array($this->userData['user_tipe'], $this->pAudit)) {
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
         "title" => "Audit - Afiliasi"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['pelanggan'] = $this->model('M_DB_1')->get('pelanggan');
      $data['_c'] = __CLASS__;
      $where = "metode_mutasi = 3 AND id_client <> 0 AND status_mutasi = 0 ORDER BY id_client ASC, id_kas ASC";
      $data['kas'] = $this->model('M_DB_1')->get_where('kas', $where);

      $where = "metode_mutasi = 3 AND id_client <> 0 AND (status_mutasi = 1 OR status_mutasi = 2) ORDER BY updateTime DESC LIMIT 20";
      $data['kas_done'] = $this->model('M_DB_1')->get_where('kas', $where);
      $this->view($this->v_content, $data);
   }

   public function exportCSV()
   {
      $month = $_POST['month'];
      $delimiter = ",";
      $filename = $this->userData['id_toko'] . "_SALES_" . $month . ".csv";
      $f = fopen('php://memory', 'w');

      $where = "insertTime LIKE '%" . $month . "%'";
      $data = $this->model('M_DB_1')->get_where("order_data", $where);

      $fields = array('ID', 'NO. REF', 'KODE BARANG', 'NAMA 
      
      BARANG', 'HARGA', 'JUMLAH', 'TOTAL', 'TANGGAL', 'CS', 'CS_AFILIASI', 'PELANGGAN', 'DELAY_DATE', 'DUE_DATE', 'DELAY_DATE_RESOLVE', 'CS_REMARK', 'REAL_REPAY_AMOUNT', 'CS_ID', 'RESOLVE_STATUS');
      fputcsv($f, $fields, $delimiter);
      foreach ($data as $a) {
         $lineData = array($a['id_cs_problem'], $a['emp_id'], $a['complain_date'], "'" . $a['loan_id'], $a['ticket_create_date'], $a['division'], $a['om'], $a['tl'], $a['remark'], $a['repay_amount'], $a['transaction_date'], $a['delay_date'], $a['due_date'], $a['delay_date_resolved'], $a['cs_remark'], $a['repay_amount_cs'], $a['cs_id'], $a['resolve']);
         fputcsv($f, $lineData, $delimiter);
      }

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
   }
}
