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
        transition: opacity .28s ease, transform .28s ease, max-height .28s ease, padding .28s ease;
    }
    .pcf-row-pend.pcf-row-out {
        opacity: 0;
        transform: translateX(12px);
        pointer-events: none;
    }
    .pcf-row-pend.pcf-row-ok td {
        background: #e8f6ee;
    }
    .pcf-row-topup {
        background: #f7fbf8;
        transition: opacity .28s ease, transform .28s ease;
    }
    .pcf-row-topup.pcf-row-out {
        opacity: 0;
        transform: translateX(12px);
        pointer-events: none;
    }
    .pcf-del-topup {
        line-height: 1;
        vertical-align: baseline;
        opacity: .55;
    }
    .pcf-del-topup:hover {
        opacity: 1;
    }
    .pcf-warn-box {
        background: #fff5f5;
        border: 1px solid #f1b0b7;
        border-left: 4px solid #dc3545;
        border-radius: .35rem;
        padding: .75rem .85rem;
        color: #842029;
        font-size: .875rem;
    }
    .pcf-warn-box strong {
        display: block;
        margin-bottom: .35rem;
        font-size: .95rem;
    }
    .pcf-year {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .pcf-year .btn {
        padding: .05rem .45rem;
        line-height: 1.2;
        font-size: .8rem;
    }
    .pcf-year .val {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        min-width: 2.6rem;
        text-align: center;
        color: var(--pcf-ink);
        font-size: .9rem;
    }
</style>

<main class="pcf">
    <div class="container pb-4">
        <div class="pcf-bar">
            <div class="pcf-saldo">
                <div class="lbl">Saldo Petty Cash</div>
                <div class="amt" id="pcfSaldoAmt">Rp<?= number_format((int)$data['saldo']) ?></div>
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
                <span class="pcf-badge<?= (int)$data['pending_total'] > 0 ? '' : ' ok' ?>" id="pcfPendingBadge">
                    <?php if ((int)$data['pending_total'] > 0) { ?>
                        <?= (int)$data['pending_shown'] ?> dari <?= (int)$data['pending_total'] ?>
                    <?php } else { ?>
                        Kosong
                    <?php } ?>
                </span>
            </div>

            <div id="pcfPendingList">
                <?php $this->view('Petty_Cash_F/pending_list', $data); ?>
            </div>
        </div>

        <div class="pcf-sec">
            <div class="pcf-sec-h">
                <h6>Riwayat Topup</h6>
                <div class="pcf-year">
                    <button type="button" class="btn btn-sm btn-outline-secondary pcf-year-btn" data-year="<?= (int)$data['year'] - 1 ?>" title="Tahun sebelumnya">&lsaquo;</button>
                    <span class="val" id="pcfYearVal"><?= (int)$data['year'] ?></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary pcf-year-btn" data-year="<?= (int)$data['year'] + 1 ?>" title="Tahun berikutnya">&rsaquo;</button>
                </div>
            </div>

            <div id="pcfTopupList">
                <?php $this->view('Petty_Cash_F/topup_list', $data); ?>
            </div>
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
                    <?php
                    $tglMax = date('Y-m-d');
                    $tglMin = date('Y-m-d', strtotime('-1 month'));
                    ?>
                    <div class="mb-2">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="<?= $tglMax ?>" min="<?= $tglMin ?>" max="<?= $tglMax ?>" required>
                    </div>
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

<div class="modal fade" id="modalDelTopup" tabindex="-1" aria-labelledby="modalDelTopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header py-2 bg-danger text-white">
                <h6 class="modal-title mb-0" id="modalDelTopupLabel">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Hapus Topup — Peringatan Keras
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="pcf-warn-box mb-3">
                    <strong>Tindakan ini tidak bisa dibatalkan.</strong>
                    Data topup akan dihapus permanen dari sistem dan dapat mengubah saldo petty cash.
                </div>
                <div class="small text-muted mb-1">Detail yang akan dihapus:</div>
                <div class="border rounded px-3 py-2 bg-light">
                    <div>Ref: <span class="fw-semibold" id="delTopupRef">—</span></div>
                    <div>Jumlah: <span class="fw-semibold text-danger" id="delTopupJumlah">—</span></div>
                    <div>Status: <span class="fw-semibold" id="delTopupSt">—</span></div>
                </div>
                <div class="form-check mt-3" id="delTopupCheckWrap" style="display:none;">
                    <input class="form-check-input" type="checkbox" id="delTopupConfirmCheck">
                    <label class="form-check-label text-danger small" for="delTopupConfirmCheck">
                        Saya mengerti topup ini sudah <b>Verified</b> dan tetap ingin menghapusnya.
                    </label>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnDelTopupConfirm">
                    Ya, Hapus Permanen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var pcfYear = <?= (int)$data['year'] ?>;
    var pcfLoading = false;

    function setYearUi(y) {
        pcfYear = y;
        $("#pcfYearVal").text(y);
        $(".pcf-year-btn").eq(0).attr("data-year", y - 1);
        $(".pcf-year-btn").eq(1).attr("data-year", y + 1);
    }

    function loadTopupYear(y) {
        y = parseInt(y, 10);
        if (!y || pcfLoading) {
            return;
        }
        pcfLoading = true;
        setYearUi(y);
        var $list = $("#pcfTopupList");
        $list.css("opacity", 0.45);
        $.ajax({
            url: "<?= PV::BASE_URL ?>Petty_Cash_F/topupList/" + y,
            type: "GET",
            complete: function() {
                pcfLoading = false;
                $list.css("opacity", 1);
            },
            success: function(html) {
                $list.html(html);
            },
            error: function() {
                $list.html('<div class="pcf-empty">Gagal memuat riwayat.</div>');
            }
        });
    }

    function updatePendingBadge(shown, total) {
        var $badge = $("#pcfPendingBadge");
        if (total <= 0) {
            $badge.addClass("ok").text("Kosong");
        } else {
            $badge.removeClass("ok").text(shown + " dari " + total);
        }
    }

    function refreshPendingList(pendingTotal) {
        $.ajax({
            url: "<?= PV::BASE_URL ?>Petty_Cash_F/pendingList",
            type: "GET",
            success: function(html) {
                $("#pcfPendingList").html(html);
                var shown = $("#pcfPendingTable tbody tr").length;
                updatePendingBadge(shown, pendingTotal);
            }
        });
    }

    function formatRp(n) {
        n = parseInt(n, 10) || 0;
        return "Rp" + n.toLocaleString("id-ID");
    }

    function updateSaldo(saldo) {
        $("#pcfSaldoAmt").text(formatRp(saldo));
    }

    var delTopupId = 0;
    var delTopupSt = 0;
    var $delTopupRow = null;

    $(document).off("click.pcfDel", ".pcf-del-topup").on("click.pcfDel", ".pcf-del-topup", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        delTopupId = parseInt($btn.attr("data-id"), 10) || 0;
        delTopupSt = parseInt($btn.attr("data-st"), 10) || 0;
        $delTopupRow = $btn.closest("tr");
        if (!delTopupId) {
            return;
        }

        $("#delTopupRef").text($btn.attr("data-ref") || "—");
        $("#delTopupJumlah").text(formatRp($btn.attr("data-jumlah")));
        $("#delTopupSt").text(delTopupSt === 1 ? "Verified" : (delTopupSt === 0 ? "Checking" : "—"));

        if (delTopupSt === 1) {
            $("#delTopupCheckWrap").show();
            $("#delTopupConfirmCheck").prop("checked", false);
            $("#btnDelTopupConfirm").prop("disabled", true);
        } else {
            $("#delTopupCheckWrap").hide();
            $("#delTopupConfirmCheck").prop("checked", false);
            $("#btnDelTopupConfirm").prop("disabled", false);
        }

        var modalEl = document.getElementById("modalDelTopup");
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    });

    $(document).off("change.pcfDelCheck", "#delTopupConfirmCheck").on("change.pcfDelCheck", "#delTopupConfirmCheck", function() {
        $("#btnDelTopupConfirm").prop("disabled", !this.checked);
    });

    $(document).off("click.pcfDelGo", "#btnDelTopupConfirm").on("click.pcfDelGo", "#btnDelTopupConfirm", function() {
        if (!delTopupId) {
            return;
        }
        if (delTopupSt === 1 && !$("#delTopupConfirmCheck").is(":checked")) {
            return;
        }
        var $btn = $(this);
        if ($btn.data("busy")) {
            return;
        }
        $btn.data("busy", 1).prop("disabled", true);
        $.ajax({
            url: "<?= PV::BASE_URL ?>Petty_Cash_F/deleteTopup/" + delTopupId,
            type: "POST",
            dataType: "json",
            complete: function() {
                $btn.data("busy", 0);
            },
            success: function(res) {
                if (!res || !res.ok) {
                    $btn.prop("disabled", false);
                    alert((res && res.error) ? res.error : "Gagal menghapus");
                    return;
                }
                var modalEl = document.getElementById("modalDelTopup");
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
                if (typeof res.saldo !== "undefined") {
                    updateSaldo(res.saldo);
                }
                var $row = $delTopupRow;
                delTopupId = 0;
                $delTopupRow = null;
                if ($row && $row.length) {
                    $row.addClass("pcf-row-out");
                    setTimeout(function() {
                        $row.remove();
                        if ($("#pcfTopupTable tbody tr").length === 0) {
                            $("#pcfTopupList").html('<div class="pcf-empty">Belum ada topup.</div>');
                        }
                    }, 280);
                } else {
                    loadTopupYear(pcfYear);
                }
            },
            error: function() {
                $btn.prop("disabled", false);
                alert("Gagal menghapus. Coba lagi.");
            }
        });
    });

    $(document).off("click.pcfYear", ".pcf-year-btn").on("click.pcfYear", ".pcf-year-btn", function(e) {
        e.preventDefault();
        loadTopupYear($(this).attr("data-year"));
    });

    $(document).off("click.pcfVerify", "a.ajax-verify").on("click.pcfVerify", "a.ajax-verify", function(e) {
        e.preventDefault();
        var href = $(this).attr("href");
        var $btn = $(this);
        var $tr = $btn.closest("tr");
        if ($btn.data("busy") || $tr.hasClass("pcf-row-out")) {
            return;
        }
        $btn.data("busy", 1).prop("disabled", true).addClass("disabled");
        $.ajax({
            url: href,
            type: "POST",
            dataType: "json",
            success: function(res) {
                if (!res || !res.ok) {
                    $btn.data("busy", 0).prop("disabled", false).removeClass("disabled");
                    alert((res && res.error) ? res.error : "Gagal verify");
                    return;
                }
                $tr.addClass("pcf-row-ok");
                $btn.replaceWith('<span class="text-success"><i class="fa-solid fa-circle-check"></i></span>');
                setTimeout(function() {
                    $tr.addClass("pcf-row-out");
                    setTimeout(function() {
                        $tr.remove();
                        var shown = $("#pcfPendingTable tbody tr").length;
                        var total = parseInt(res.pending_total, 10) || 0;
                        if (shown === 0) {
                            if (total > 0) {
                                refreshPendingList(total);
                            } else {
                                $("#pcfPendingList").html('<div class="pcf-empty">Tidak ada pemakaian menunggu verify.</div>');
                                updatePendingBadge(0, 0);
                            }
                        } else {
                            updatePendingBadge(shown, total);
                        }
                    }, 280);
                }, 180);
            },
            error: function() {
                $btn.data("busy", 0).prop("disabled", false).removeClass("disabled");
                alert("Gagal verify. Coba lagi.");
            }
        });
    });

    var submitting = false;

    function ymd(d) {
        var pad = function(n) { return (n < 10 ? "0" : "") + n; };
        return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate());
    }

    function resetTopupTanggal() {
        var $tgl = $("#formPettyTopup [name=tanggal]");
        if (!$tgl.length) {
            return;
        }
        var today = new Date();
        var minD = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
        $tgl.attr("max", ymd(today)).attr("min", ymd(minD)).val(ymd(today));
    }

    $("#modalPettyTopup").off("show.bs.modal.pcfTgl").on("show.bs.modal.pcfTgl", function() {
        resetTopupTanggal();
    });

    $("#formPettyTopup").off("submit.petty").on("submit.petty", function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (submitting) {
            return false;
        }
        var $form = $(this);
        var $btn = $("#btnPettyTopup");
        var $jumlah = $form.find("[name=jumlah]");
        var $tanggal = $form.find("[name=tanggal]");
        var jumlah = $jumlah.val();
        var tanggal = $tanggal.val();
        if (!jumlah || parseInt(jumlah, 10) <= 0) {
            alert("Jumlah tidak valid");
            return false;
        }
        if (!tanggal) {
            alert("Tanggal tidak valid");
            return false;
        }

        submitting = true;
        var btnText = $btn.text();
        $btn.prop("disabled", true).text("Memproses...");
        $jumlah.prop("disabled", true);
        $tanggal.prop("disabled", true);

        $.ajax({
            url: $form.attr("action"),
            data: { jumlah: jumlah, tanggal: tanggal },
            type: $form.attr("method") || "POST",
            complete: function() {
                submitting = false;
                $jumlah.prop("disabled", false);
                $tanggal.prop("disabled", false);
                $btn.prop("disabled", false).text(btnText);
            },
            success: function(res) {
                if (res == 0) {
                    var modalEl = document.getElementById("modalPettyTopup");
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                    }
                    $form[0].reset();
                    resetTopupTanggal();
                    var y = (tanggal && tanggal.length >= 4) ? parseInt(tanggal.substring(0, 4), 10) : pcfYear;
                    content(String(y || pcfYear));
                } else {
                    alert(res);
                }
            },
            error: function() {
                alert("Gagal memproses. Coba lagi.");
            }
        });
        return false;
    });
})();
</script>
