<?php

class Cart_Debug extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[0])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      // Simpan filter dari URL full-page (bookmark / refresh browser)
      if (isset($_GET['date']) || isset($_GET['rid']) || isset($_GET['q']) || isset($_GET['limit'])) {
         $_SESSION['cart_debug_filter'] = [
            'date' => $_GET['date'] ?? date('Y-m-d'),
            'rid' => $_GET['rid'] ?? '',
            'q' => $_GET['q'] ?? '',
            'limit' => $_GET['limit'] ?? 300,
         ];
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Developer - Cart Debug Log"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   private function readFilters()
   {
      $sess = $_SESSION['cart_debug_filter'] ?? [];
      $date = isset($_GET['date']) ? trim((string) $_GET['date']) : trim((string) ($sess['date'] ?? date('Y-m-d')));
      if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
         $date = date('Y-m-d');
      }

      $ridRaw = isset($_GET['rid']) ? (string) $_GET['rid'] : (string) ($sess['rid'] ?? '');
      $rid = preg_replace('/[^a-zA-Z0-9]/', '', $ridRaw);

      $q = isset($_GET['q']) ? trim((string) $_GET['q']) : trim((string) ($sess['q'] ?? ''));

      $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : (int) ($sess['limit'] ?? 300);
      if ($limit < 50) {
         $limit = 50;
      }
      if ($limit > 2000) {
         $limit = 2000;
      }

      $_SESSION['cart_debug_filter'] = [
         'date' => $date,
         'rid' => $rid,
         'q' => $q,
         'limit' => $limit,
      ];

      return [$date, $rid, $q, $limit];
   }

   public function content($parse = "")
   {
      list($date, $rid, $q, $limit) = $this->readFilters();

      $logDir = 'logs/' . $date;
      $logFile = $logDir . '/cartdebug.log';
      $lines = [];
      $exists = is_file($logFile);
      $size = $exists ? filesize($logFile) : 0;

      if ($exists) {
         $raw = @file($logFile, FILE_IGNORE_NEW_LINES);
         if (is_array($raw)) {
            $raw = array_reverse($raw);
            foreach ($raw as $line) {
               if ($rid !== '' && strpos($line, 'rid=' . $rid) === false) {
                  continue;
               }
               if ($q !== '' && stripos($line, $q) === false) {
                  continue;
               }
               $lines[] = $line;
               if (count($lines) >= $limit) {
                  break;
               }
            }
         }
      }

      $dates = [];
      foreach (glob('logs/*', GLOB_ONLYDIR) as $dir) {
         $d = basename($dir);
         if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && is_file($dir . '/cartdebug.log')) {
            $dates[] = $d;
         }
      }
      rsort($dates);

      $data = [
         'date' => $date,
         'dates' => $dates,
         'rid' => $rid,
         'q' => $q,
         'limit' => $limit,
         'lines' => $lines,
         'exists' => $exists,
         'size' => $size,
         'file' => $logFile,
         'count' => count($lines),
      ];

      $this->view($this->v_content, $data);
   }
}
