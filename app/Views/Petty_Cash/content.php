<?php
$year = (int)$data['year'];
$ym = $data['ym'];
$ymLabel = date('M Y', strtotime($ym . '-01'));
$ymPrev = date('Y-m', strtotime($ym . '-01 -1 month'));
$ymNext = date('Y-m', strtotime($ym . '-01 +1 month'));
$canOps = !empty($data['can_ops']);
?>
<style>
    .pc {
        --pc-ink: #1a2e22;
        --pc-muted: #6b7c72;
        --pc-line: #e4ebe6;
        --pc-accent: #1f7a4d;
        --pc-warn: #b7791f;
        --pc-danger: #b42318;
    }
    .pc > .container {
        max-width: 1040px;
        margin-left: 0;
        margin-right: auto;
    }
    .pc-bar {
        display: flex;
        align-items: stretch;
        gap: .75rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        max-width: 500px;
    }
    .pc-saldo {
        flex: 1 1 140px;
        background: linear-gradient(135deg, #1f7a4d 0%, #2d9b66 55%, #3cb371 100%);
        color: #fff;
        border-radius: .5rem;
        padding: .85rem 1rem;
        min-width: 0;
    }
    .pc-saldo .lbl {
        font-size: .72rem;
        opacity: .85;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: .15rem;
    }
    .pc-saldo .amt {
        font-size: 1.35rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
    }
    .pc-actions {
        display: flex;
        align-items: stretch;
        flex: 0 0 auto;
    }
    .pc-actions .btn {
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
    .pc-month-bar {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .85rem;
        max-width: 500px;
    }
    .pc-nav {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .pc-nav .btn {
        padding: .05rem .45rem;
        line-height: 1.2;
        font-size: .8rem;
    }
    .pc-nav .val {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        min-width: 2.6rem;
        text-align: center;
        color: var(--pc-ink);
        font-size: .9rem;
    }
    .pc-nav.pc-nav-month .val {
        min-width: 4.5rem;
    }
    .pc-cols {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 1.25rem;
        align-items: flex-start;
    }
    .pc-sec {
        flex: 1 1 280px;
        max-width: 500px;
        min-width: 0;
    }
    #pcPakaiList {
        max-height: 500px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    .pc-sec-h {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .4rem;
        padding-bottom: .35rem;
        border-bottom: 1px solid var(--pc-line);
    }
    .pc-sec-h h6 {
        margin: 0;
        font-size: .9rem;
        font-weight: 700;
        color: var(--pc-ink);
    }
    .pc-badge {
        font-size: .7rem;
        font-weight: 650;
        background: #fff4df;
        color: var(--pc-warn);
        border: 1px solid #f0d9a8;
        border-radius: 999px;
        padding: .12rem .55rem;
    }
    .pc-badge.ok {
        background: #e8f6ee;
        color: var(--pc-accent);
        border-color: #b9e0c8;
    }
    .pc table { margin-bottom: 0; }
    .pc table td { vertical-align: middle; }
    .pc .amt {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .pc-meta {
        color: var(--pc-muted);
        font-size: .78rem;
    }
    .pc-note {
        color: #3d6b9a;
        font-size: .8rem;
    }
    .pc-jenis {
        font-style: italic;
        color: #8a9690;
        font-size: .85rem;
    }
    .pc-empty {
        color: var(--pc-muted);
        font-size: .85rem;
        padding: .85rem 0;
    }
    .pc-row-pend {
        background: #fffdf8;
        transition: opacity .28s ease, transform .28s ease;
    }
    .pc-row-pend.pc-row-out {
        opacity: 0;
        transform: translateX(12px);
        pointer-events: none;
    }
    .pc-row-pend.pc-row-ok td { background: #e8f6ee; }
    .pc-row-topup { background: #f7fbf8; }
    .pc-row-pakai { background: #fff8f8; transition: opacity .28s ease, transform .28s ease; }
    .pc-row-pakai.pc-row-out {
        opacity: 0;
        transform: translateX(12px);
        pointer-events: none;
    }
    .pc-del-pakai {
        line-height: 1;
        vertical-align: baseline;
        opacity: .55;
    }
    .pc-del-pakai:hover { opacity: 1; }
    .pc-warn-box {
        background: #fff5f5;
        border: 1px solid #f1b0b7;
        border-left: 4px solid #dc3545;
        border-radius: .35rem;
        padding: .75rem .85rem;
        color: #842029;
        font-size: .875rem;
    }
    .pc-warn-box strong {
        display: block;
        margin-bottom: .35rem;
        font-size: .95rem;
    }
    .cell_edit { cursor: text; border-bottom: 1px dashed #9bb8d4; }
</style>

<main class="pc">
    <div class="container pb-4">
        <div class="pc-bar">
            <div class="pc-saldo">
                <div class="lbl">Saldo Petty Cash</div>
                <div class="amt" id="pcSaldoAmt">Rp<?= number_format((int)$data['saldo']) ?></div>
            </div>
            <?php if ($canOps) { ?>
                <div class="pc-actions">
                    <button type="button" class="btn btn-danger bg-gradient" data-bs-toggle="modal" data-bs-target="#modalPettyPakai">
                        + Pakai
                    </button>
                </div>
            <?php } ?>
        </div>

        <div class="pc-cols">
            <div class="pc-sec">
                <div class="pc-sec-h">
                    <h6>Pemakaian</h6>
                    <div class="pc-nav pc-nav-month">
                        <button type="button" class="btn btn-sm btn-outline-secondary pc-month-btn" data-ym="<?= $ymPrev ?>">&lsaquo;</button>
                        <span class="val" id="pcMonthVal"><?= $ymLabel ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary pc-month-btn" data-ym="<?= $ymNext ?>">&rsaquo;</button>
                    </div>
                </div>
                <div id="pcPakaiList">
                    <?php $this->view('Petty_Cash/pakai_list', $data); ?>
                </div>
            </div>

            <div class="pc-sec">
                <div class="pc-sec-h">
                    <h6>Topup Menunggu Verify</h6>
                    <span class="pc-badge<?= (int)$data['pending_total'] > 0 ? '' : ' ok' ?>" id="pcPendingBadge">
                        <?php if ((int)$data['pending_total'] > 0) { ?>
                            <?= (int)$data['pending_shown'] ?> dari <?= (int)$data['pending_total'] ?>
                        <?php } else { ?>
                            Kosong
                        <?php } ?>
                    </span>
                </div>
                <div id="pcPendingList">
                    <?php $this->view('Petty_Cash/pending_list', $data); ?>
                </div>

                <div class="pc-sec-h mt-3">
                    <h6>Riwayat Topup</h6>
                    <div class="pc-nav">
                        <button type="button" class="btn btn-sm btn-outline-secondary pc-year-btn" data-year="<?= $year - 1 ?>">&lsaquo;</button>
                        <span class="val" id="pcYearVal"><?= $year ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary pc-year-btn" data-year="<?= $year + 1 ?>">&rsaquo;</button>
                    </div>
                </div>
                <div id="pcTopupList">
                    <?php $this->view('Petty_Cash/topup_list', $data); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php if ($canOps) { ?>
<form id="formPettyPakai" action="<?= PV::BASE_URL ?>Petty_Cash/pakai" method="POST">
    <div class="modal fade" id="modalPettyPakai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2 bg-danger text-white">
                    <h6 class="modal-title mb-0">Pakai Petty Cash</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tanggal Nota/Event</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col">
                            <label class="form-label">Jumlah</label>
                            <input type="number" min="1" name="jumlah" class="form-control form-control-sm" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select form-select-sm" required>
                                <option value="">— pilih —</option>
                                <?php foreach ($data['jkeluar'] as $djk) { ?>
                                    <option value="<?= (int)$djk['id'] ?>"><?= htmlspecialchars($djk['nama']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="note" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnPettyPakai" class="btn btn-sm btn-danger">Pakai</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="modalDelPakai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header py-2 bg-danger text-white">
                <h6 class="modal-title mb-0">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Hapus Pemakaian
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="pc-warn-box mb-3">
                    <strong>Tindakan ini tidak bisa dibatalkan.</strong>
                    Pemakaian yang belum diverifikasi akan dihapus permanen.
                </div>
                <div class="border rounded px-3 py-2 bg-light small">
                    <div>Jenis: <span class="fw-semibold" id="delPakaiJenis">—</span></div>
                    <div>Jumlah: <span class="fw-semibold text-danger" id="delPakaiJumlah">—</span></div>
                    <div>Note: <span class="fw-semibold" id="delPakaiNote">—</span></div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnDelPakaiConfirm">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
(function() {
    var pcYear = <?= (int)$year ?>;
    var pcYm = "<?= $ym ?>";
    var pcLoadingYear = false;
    var pcLoadingMonth = false;

    function formatRp(n) {
        n = parseInt(n, 10) || 0;
        return "Rp" + n.toLocaleString("id-ID");
    }

    function updateSaldo(saldo) {
        $("#pcSaldoAmt").text(formatRp(saldo));
    }

    function monthLabel(ym) {
        var p = (ym || "").split("-");
        if (p.length < 2) return ym;
        var names = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        var m = parseInt(p[1], 10) - 1;
        return (names[m] || p[1]) + " " + p[0];
    }

    function shiftYm(ym, delta) {
        var p = ym.split("-");
        var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1 + delta, 1);
        var m = d.getMonth() + 1;
        return d.getFullYear() + "-" + (m < 10 ? "0" + m : m);
    }

    function setYearUi(y) {
        pcYear = parseInt(y, 10);
        $("#pcYearVal").text(pcYear);
        $(".pc-year-btn").eq(0).attr("data-year", pcYear - 1);
        $(".pc-year-btn").eq(1).attr("data-year", pcYear + 1);
    }

    function setMonthUi(ym) {
        pcYm = ym;
        $("#pcMonthVal").text(monthLabel(ym));
        $(".pc-month-btn").eq(0).attr("data-ym", shiftYm(ym, -1));
        $(".pc-month-btn").eq(1).attr("data-ym", shiftYm(ym, 1));
    }

    function loadTopupYear(y) {
        y = parseInt(y, 10);
        if (!y || pcLoadingYear) return;
        pcLoadingYear = true;
        setYearUi(y);
        var $list = $("#pcTopupList").css("opacity", 0.45);
        $.get("<?= PV::BASE_URL ?>Petty_Cash/topupList/" + y)
            .done(function(html) { $list.html(html); })
            .fail(function() { $list.html('<div class="pc-empty">Gagal memuat.</div>'); })
            .always(function() {
                pcLoadingYear = false;
                $list.css("opacity", 1);
            });
    }

    function loadPakaiMonth(ym) {
        ym = String(ym || "");
        if (!ym || pcLoadingMonth) return;
        pcLoadingMonth = true;
        setMonthUi(ym);
        var $list = $("#pcPakaiList").css("opacity", 0.45);
        $.get("<?= PV::BASE_URL ?>Petty_Cash/pakaiList/" + ym)
            .done(function(html) { $list.html(html); })
            .fail(function() { $list.html('<div class="pc-empty">Gagal memuat.</div>'); })
            .always(function() {
                pcLoadingMonth = false;
                $list.css("opacity", 1);
            });
    }

    function updatePendingBadge(shown, total) {
        var $badge = $("#pcPendingBadge");
        if (total <= 0) $badge.addClass("ok").text("Kosong");
        else $badge.removeClass("ok").text(shown + " dari " + total);
    }

    function refreshPendingList(pendingTotal) {
        $.get("<?= PV::BASE_URL ?>Petty_Cash/pendingList", function(html) {
            $("#pcPendingList").html(html);
            var shown = $("#pcPendingTable tbody tr").length;
            updatePendingBadge(shown, pendingTotal);
        });
    }

    $(document).off("click.pcYear", ".pc-year-btn").on("click.pcYear", ".pc-year-btn", function(e) {
        e.preventDefault();
        loadTopupYear($(this).attr("data-year"));
    });

    $(document).off("click.pcMonth", ".pc-month-btn").on("click.pcMonth", ".pc-month-btn", function(e) {
        e.preventDefault();
        loadPakaiMonth($(this).attr("data-ym"));
    });

    $(document).off("click.pcVerify", "a.ajax-verify").on("click.pcVerify", "a.ajax-verify", function(e) {
        e.preventDefault();
        var href = $(this).attr("href");
        var $btn = $(this);
        var $tr = $btn.closest("tr");
        if ($btn.data("busy") || $tr.hasClass("pc-row-out")) return;
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
                if (typeof res.saldo !== "undefined") updateSaldo(res.saldo);
                $tr.addClass("pc-row-ok");
                $btn.replaceWith('<span class="text-success"><i class="fa-solid fa-circle-check"></i></span>');
                setTimeout(function() {
                    $tr.addClass("pc-row-out");
                    setTimeout(function() {
                        $tr.remove();
                        var shown = $("#pcPendingTable tbody tr").length;
                        var total = parseInt(res.pending_total, 10) || 0;
                        if (shown === 0) {
                            if (total > 0) refreshPendingList(total);
                            else {
                                $("#pcPendingList").html('<div class="pc-empty">Tidak ada topup menunggu verify.</div>');
                                updatePendingBadge(0, 0);
                            }
                        } else {
                            updatePendingBadge(shown, total);
                        }
                        loadTopupYear(pcYear);
                    }, 280);
                }, 180);
            },
            error: function() {
                $btn.data("busy", 0).prop("disabled", false).removeClass("disabled");
                alert("Gagal verify. Coba lagi.");
            }
        });
    });

    <?php if ($canOps) { ?>
    var submitting = false;
    $("#formPettyPakai").off("submit.petty").on("submit.petty", function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (submitting) return false;
        var $form = $(this);
        var $btn = $("#btnPettyPakai");
        var payload = {
            tanggal: $form.find("[name=tanggal]").val(),
            jumlah: $form.find("[name=jumlah]").val(),
            jenis: $form.find("[name=jenis]").val(),
            note: $form.find("[name=note]").val()
        };
        if (!payload.tanggal || !payload.jumlah || !payload.jenis || !payload.note) {
            alert("Data tidak lengkap");
            return false;
        }
        submitting = true;
        var btnText = $btn.text();
        $btn.prop("disabled", true).text("Memproses...");
        $form.find("input,select").prop("disabled", true);
        $.ajax({
            url: $form.attr("action"),
            data: payload,
            type: "POST",
            complete: function() {
                submitting = false;
                $form.find("input,select").prop("disabled", false);
                $btn.prop("disabled", false).text(btnText);
            },
            success: function(res) {
                if (res == 0) {
                    var modalEl = document.getElementById("modalPettyPakai");
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                    }
                    $form[0].reset();
                    content(String(pcYear), pcYm);
                } else {
                    alert(res);
                }
            },
            error: function() { alert("Gagal memproses. Coba lagi."); }
        });
        return false;
    });

    var delPakaiId = 0;
    var $delPakaiRow = null;

    $(document).off("click.pcDel", ".pc-del-pakai").on("click.pcDel", ".pc-del-pakai", function(e) {
        e.preventDefault();
        var $btn = $(this);
        delPakaiId = parseInt($btn.attr("data-id"), 10) || 0;
        $delPakaiRow = $btn.closest("tr");
        if (!delPakaiId) return;
        $("#delPakaiJenis").text($btn.attr("data-jenis") || "—");
        $("#delPakaiJumlah").text(formatRp($btn.attr("data-jumlah")));
        $("#delPakaiNote").text($btn.attr("data-note") || "—");
        var modalEl = document.getElementById("modalDelPakai");
        (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)).show();
    });

    $(document).off("click.pcDelGo", "#btnDelPakaiConfirm").on("click.pcDelGo", "#btnDelPakaiConfirm", function() {
        if (!delPakaiId) return;
        var $btn = $(this);
        if ($btn.data("busy")) return;
        $btn.data("busy", 1).prop("disabled", true);
        $.ajax({
            url: "<?= PV::BASE_URL ?>Petty_Cash/delete",
            type: "POST",
            dataType: "json",
            data: { id: delPakaiId },
            complete: function() { $btn.data("busy", 0); },
            success: function(res) {
                if (!res || !res.ok) {
                    $btn.prop("disabled", false);
                    alert((res && res.error) ? res.error : "Gagal menghapus");
                    return;
                }
                var modalEl = document.getElementById("modalDelPakai");
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                if (typeof res.saldo !== "undefined") updateSaldo(res.saldo);
                var $row = $delPakaiRow;
                delPakaiId = 0;
                $delPakaiRow = null;
                if ($row && $row.length) {
                    $row.addClass("pc-row-out");
                    setTimeout(function() {
                        $row.remove();
                        if ($("#pcPakaiTable tbody tr").length === 0) {
                            $("#pcPakaiList").html('<div class="pc-empty">Belum ada pemakaian di bulan ini.</div>');
                        }
                    }, 280);
                } else {
                    loadPakaiMonth(pcYm);
                }
                $btn.prop("disabled", false);
            },
            error: function() {
                $btn.prop("disabled", false);
                alert("Gagal menghapus. Coba lagi.");
            }
        });
    });

    var click = 0;
    $(document).off("dblclick.pcEdit", ".cell_edit").on("dblclick.pcEdit", ".cell_edit", function() {
        click += 1;
        if (click != 1) return;
        var id = $(this).attr("data-id");
        var value = $(this).text();
        var value_before = value;
        var el = $(this);
        var width = el.parent().width();
        el.html("<input required type='text' style='outline:none;border:none;width:" + width + "px;text-align:left' id='value_' value='" + value.replace(/'/g, "&#39;") + "'>");
        $("#value_").focus();
        $("#value_").keypress(function(e) { if (e.which == 13) $(this).blur(); });
        $("#value_").focusout(function() {
            var value_after = $(this).val();
            if (value_after === value_before || value_after == "") {
                el.html(value_before);
                click = 0;
                return;
            }
            $.ajax({
                url: "<?= PV::BASE_URL ?>Petty_Cash/update",
                data: { id: id, col: "note", val: value_after },
                type: "POST",
                success: function(res) {
                    click = 0;
                    el.html(res == 0 ? value_after : value_before);
                },
                error: function() {
                    click = 0;
                    el.html(value_before);
                }
            });
        });
    });
    <?php } ?>
})();
</script>
