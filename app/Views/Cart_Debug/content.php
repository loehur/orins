<?php
$date = htmlspecialchars($data['date'] ?? date('Y-m-d'));
$rid = htmlspecialchars($data['rid'] ?? '');
$q = htmlspecialchars($data['q'] ?? '');
$limit = (int) ($data['limit'] ?? 300);
$lines = $data['lines'] ?? [];
$dates = $data['dates'] ?? [];
?>
<style>
    .cart-debug-log {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12px;
        line-height: 1.45;
        white-space: pre-wrap;
        word-break: break-word;
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.85rem 1rem;
        max-height: 70vh;
        overflow: auto;
    }
    .cart-debug-log .hit-rid { color: #38bdf8; }
    .cart-debug-log .hit-write { color: #4ade80; font-weight: 600; }
    .cart-debug-log .hit-dup { color: #f87171; font-weight: 700; }
    .cart-debug-log .hit-idem { color: #fbbf24; }
    .cart-debug-log .hit-enter { color: #c4b5fd; }
    .cart-debug-hint code {
        background: #f1f5f9;
        padding: 0.1rem 0.35rem;
        border-radius: 0.25rem;
        font-size: 0.85em;
    }
</style>

<main>
    <div class="container-fluid px-3 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <div class="fw-bold">Cart Debug Log</div>
                <div class="small text-muted">
                    File: <code><?= htmlspecialchars($data['file'] ?? '') ?></code>
                    <?php if (!empty($data['exists'])) { ?>
                        · <?= number_format((int) ($data['size'] ?? 0)) ?> bytes
                        · menampilkan <?= (int) ($data['count'] ?? 0) ?> baris terbaru
                    <?php } else { ?>
                        · belum ada log untuk tanggal ini (akan muncul setelah ada input cart)
                    <?php } ?>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCartDebugRefresh">Refresh</button>
        </div>

        <form id="formCartDebug" class="row g-2 align-items-end mb-3">
            <div class="col-auto">
                <label class="form-label small mb-0">Tanggal</label>
                <select name="date" class="form-select form-select-sm">
                    <?php if (!in_array($data['date'], $dates, true)) { ?>
                        <option value="<?= $date ?>" selected><?= $date ?></option>
                    <?php } ?>
                    <?php foreach ($dates as $d) { ?>
                        <option value="<?= htmlspecialchars($d) ?>" <?= $d === $data['date'] ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
                    <?php } ?>
                    <?php if (count($dates) === 0) { ?>
                        <option value="<?= $date ?>" selected><?= $date ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">Filter rid</label>
                <input type="text" name="rid" value="<?= $rid ?>" class="form-control form-control-sm" placeholder="contoh: a1b2c3d4" style="width:140px">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">Cari teks</label>
                <input type="text" name="q" value="<?= $q ?>" class="form-control form-control-sm" placeholder="WRITE / DUPDEF / paket" style="width:180px">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">Limit</label>
                <select name="limit" class="form-select form-select-sm">
                    <?php foreach ([100, 300, 500, 1000, 2000] as $opt) { ?>
                        <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </div>
        </form>

        <div class="cart-debug-hint small text-muted mb-2">
            Cara baca:
            <code>rid</code> sama + beberapa <code>WRITE</code> = 1 request menulis berkali-kali (hipotesis B).
            Beberapa <code>rid</code> berbeda dalam detik yang sama = multi POST (hipotesis A).
            <code>DUPDEF=1</code> = definisi paket punya baris kembar.
            <code>calls</code>/<code>sent</code>/<code>handlers</code> dari browser.
        </div>

        <?php if (empty($lines)) { ?>
            <div class="alert alert-light border">Tidak ada baris log yang cocok.</div>
        <?php } else { ?>
            <div class="cart-debug-log"><?php
                foreach ($lines as $line) {
                    $esc = htmlspecialchars($line);
                    $esc = preg_replace('/\brid=([a-zA-Z0-9]+)\b/', '<span class="hit-rid">rid=$1</span>', $esc);
                    $esc = preg_replace('/\b(WRITE=(?:MERGE|INSERT|FAIL)[^\s]*)/', '<span class="hit-write">$1</span>', $esc);
                    $esc = preg_replace('/\b(DUPDEF=1)\b/', '<span class="hit-dup">$1</span>', $esc);
                    $esc = preg_replace('/\b(IDEM=REJECT)\b/', '<span class="hit-idem">$1</span>', $esc);
                    $esc = preg_replace('/\b(ENTER|PAKETDEF|LOOP|DONE)\b/', '<span class="hit-enter">$1</span>', $esc);
                    echo $esc . "\n";
                }
            ?></div>
        <?php } ?>
    </div>
</main>

<script>
(function() {
    function cartDebugLoad() {
        var qs = $('#formCartDebug').serialize();
        if (typeof loadAppContent === 'function') {
            loadAppContent('<?= PV::BASE_URL ?>Cart_Debug/content?' + qs);
        } else {
            location.href = '<?= PV::BASE_URL ?>Cart_Debug?' + qs;
        }
    }
    $('#formCartDebug').on('submit', function(e) {
        e.preventDefault();
        cartDebugLoad();
    });
    $('#btnCartDebugRefresh').on('click', function() {
        cartDebugLoad();
    });
})();
</script>
