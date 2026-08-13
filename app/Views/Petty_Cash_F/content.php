<style>
    .pcf {
        --pcf-ink: #1a2e22;
        --pcf-muted: #6b7c72;
        --pcf-line: #e4ebe6;
        --pcf-accent: #1f7a4d;
        --pcf-warn: #b7791f;
    }
    .pcf > .container {
        max-width: 1040px;
        margin-left: 0;
        margin-right: auto;
    }
    .pcf-bar {
        display: flex;
        align-items: stretch;
        gap: .75rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        max-width: 500px;
    }
    .pcf-saldo {
        flex: 1 1 140px;
        background: linear-gradient(135deg, #1f7a4d 0%, #2d9b66 55%, #3cb371 100%);
        color: #fff;
        border-radius: .5rem;
        padding: .85rem 1rem;
        min-width: 0;
    }
    .pcf-saldo .lbl {
        font-size: .72rem;
        opacity: .85;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: .15rem;
    }
    .pcf-saldo .amt {
        font-size: 1.35rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
    }
    .pcf-actions {
        display: flex;
        align-items: stretch;
        flex: 0 0 auto;
    }
    .pcf-actions .btn {
        height: 100%;
        min-height: 100%;
        padding: .85rem 1.15rem;
        border-radius: .5rem;
        font-weight: 700;
        font-size: .95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }
    .pcf-cols {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 1.25rem;
        align-items: flex-start;
    }
    .pcf-sec {
        flex: 1 1 280px;
        max-width: 500px;
        min-width: 0;
        margin-top: 0;
    }
    .pcf-sec-h {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .4rem;
        padding-bottom: .35rem;
        border-bottom: 1px solid var(--pcf-line);
    }
    .pcf-sec-h h6 {
        margin: 0;
        font-size: .9rem;
        font-weight: 700;
        color: var(--pcf-ink);
    }
    .pcf-badge {
        font-size: .7rem;
        font-weight: 650;
        background: #fff4df;
        color: var(--pcf-warn);
        border: 1px solid #f0d9a8;
        border-radius: 999px;
        padding: .12rem .55rem;
    }
    .pcf-badge.ok {
        background: #e8f6ee;
        color: var(--pcf-accent);
        border-color: #b9e0c8;
    }
    .pcf table {
        margin-bottom: 0;
    }
    .pcf table td {
        vertical-align: middle;
    }
    .pcf .amt {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .pcf-meta {
        color: var(--pcf-muted);
        font-size: .78rem;
    }
    .pcf-note {
        color: #3d6b9a;
        font-size: .8rem;
    }
    .pcf-empty {
        color: var(--pcf-muted);
        font-size: .85rem;
        padding: .85rem 0;
    }
    .pcf-row-pend {
        background: #fffdf8;
    }
    .pcf-row-topup {
        background: #f7fbf8;
    }
</style>

<main class="pcf">
    <div class="container pb-4">
        <div class="pcf-bar">
            <div class="pcf-saldo">
                <div class="lbl">Saldo Petty Cash</div>
                <div class="amt">Rp<?= number_format((int)$data['saldo']) ?></div>
            </div>
            <div class="pcf-actions">
                <button type="button" class="btn btn-primary bg-gradient" data-bs-toggle="modal" data-bs-target="#modalPettyTopup">
                    + Topup
                </button>
            </div>
        </div>

        <div class="pcf-cols">
        <div class="pcf-sec">
            <div class="pcf-sec-h">
                <h6>Menunggu Verifikasi</h6>
                <?php if ((int)$data['pending_total'] > 0) { ?>
                    <span class="pcf-badge"><?= (int)$data['pending_shown'] ?> dari <?= (int)$data['pending_total'] ?></span>
                <?php } else { ?>
                    <span class="pcf-badge ok">Kosong</span>
                <?php } ?>
            </div>

            <?php if (empty($data['pakai'])) { ?>
                <div class="pcf-empty">Tidak ada pemakaian menunggu verify.</div>
            <?php } else { ?>
                <table class="table table-sm text-sm">
                    <tbody>
                        <?php foreach ($data['pakai'] as $a) {
                            $jenis = isset($data['jkeluar'][$a['id_target']])
                                ? $data['jkeluar'][$a['id_target']]['nama']
                                : ('#' . $a['id_target']);
                            $waktu = ($a['tanggal'] ?? '') === ''
                                ? date('d/m/y H:i', strtotime($a['insertTime']))
                                : $a['tanggal'];
                            ?>
                            <tr class="pcf-row-pend">
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($waktu) ?></div>
                                    <?php if (!empty($a['note'])) { ?>
                                        <div class="pcf-note"><i class="fa-regular fa-note-sticky"></i> <?= htmlspecialchars($a['note']) ?></div>
                                    <?php } ?>
                                </td>
                                <td class="text-end">
                                    <div>
                                        <span class="text-danger fw-bold"><i class="fa-solid fa-arrow-right"></i></span>
                                        <?= htmlspecialchars($jenis) ?>
                                    </div>
                                    <div class="amt fw-semibold"><?= number_format((int)$a['jumlah']) ?></div>
                                </td>
                                <td class="text-end" style="width:88px">
                                    <a class="ajax-verify btn btn-sm btn-success bg-gradient py-0 px-2"
                                       href="<?= PV::BASE_URL ?>Petty_Cash_F/verify/<?= (int)$a['id'] ?>/1">Verify</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>

        <div class="pcf-sec">
            <div class="pcf-sec-h">
                <h6>Riwayat Topup</h6>
            </div>

            <?php if (empty($data['topup'])) { ?>
                <div class="pcf-empty">Belum ada topup.</div>
            <?php } else { ?>
                <table class="table table-sm text-sm">
                    <tbody>
                        <?php foreach ($data['topup'] as $a) {
                            $st = (int)$a['st'];
                            ?>
                            <tr class="pcf-row-topup">
                                <td>
                                    <div class="fw-semibold"><?= date('d/m/y H:i', strtotime($a['insertTime'])) ?></div>
                                    <div class="pcf-meta"><?= htmlspecialchars($a['ref']) ?></div>
                                </td>
                                <td class="text-end">
                                    <div class="amt fw-semibold"><?= number_format((int)$a['jumlah']) ?></div>
                                </td>
                                <td class="text-end" style="width:88px">
                                    <?php if ($st === 0) { ?>
                                        <span class="text-warning">Checking</span>
                                    <?php } elseif ($st === 1) { ?>
                                        <span class="text-success">Verified</span>
                                    <?php } else { ?>
                                        <span class="text-secondary">—</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalPettyTopup" tabindex="-1" aria-labelledby="modalPettyTopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="modalPettyTopupLabel">Topup Petty Cash</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPettyTopup" action="<?= PV::BASE_URL ?>Petty_Cash_F/topupPety" method="POST">
                <div class="modal-body">
                    <label class="form-label">Jumlah</label>
                    <input type="number" min="1" step="1" name="jumlah" class="form-control" placeholder="Rp" required autofocus>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnPettyTopup" class="btn btn-sm btn-success">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    $(document).off("click.pcfVerify", "a.ajax-verify").on("click.pcfVerify", "a.ajax-verify", function(e) {
        e.preventDefault();
        var href = $(this).attr("href");
        var $btn = $(this);
        if ($btn.data("busy")) {
            return;
        }
        $btn.data("busy", 1).prop("disabled", true).addClass("disabled");
        $.ajax({
            url: href,
            type: "POST",
            complete: function() {
                $btn.data("busy", 0).prop("disabled", false).removeClass("disabled");
            },
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            },
            error: function() {
                alert("Gagal verify. Coba lagi.");
            }
        });
    });

    var submitting = false;
    $("#formPettyTopup").off("submit.petty").on("submit.petty", function(e) {
        e.preventDefault();
        if (submitting) {
            return false;
        }
        submitting = true;
        var $form = $(this);
        var $btn = $("#btnPettyTopup");
        $btn.prop("disabled", true);
        $.ajax({
            url: $form.attr("action"),
            data: $form.serialize(),
            type: $form.attr("method") || "POST",
            complete: function() {
                submitting = false;
                $btn.prop("disabled", false);
            },
            success: function(res) {
                if (res == 0) {
                    var modalEl = document.getElementById("modalPettyTopup");
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                    }
                    $form[0].reset();
                    content();
                } else {
                    alert(res);
                }
            },
            error: function() {
                alert("Gagal memproses. Coba lagi.");
            }
        });
    });
})();
</script>
