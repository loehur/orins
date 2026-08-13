<style>
    .ok-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .75rem;
    }
    .ok-head h6 {
        margin: 0;
        font-weight: 700;
        letter-spacing: .02em;
    }
    .ok-empty {
        color: #6c757d;
        font-size: .875rem;
        padding: .75rem 0;
    }
    .ok-amt {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .ok-meta {
        color: #6c757d;
        font-size: .8rem;
    }
</style>

<main>
    <div class="container pb-4">
        <div class="ok-head">
            <h6>Topup Petty Cash</h6>
            <button type="button" class="btn btn-sm btn-primary bg-gradient px-3" data-bs-toggle="modal" data-bs-target="#modalTopupOffice">
                + Topup
            </button>
        </div>

        <table class="table table-sm text-sm mb-0">
            <thead>
                <tr class="text-secondary">
                    <th>Waktu</th>
                    <th>Tujuan</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end" style="width:90px">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['keluar_list'])) { ?>
                    <tr>
                        <td colspan="4" class="ok-empty border-0">Belum ada topup.</td>
                    </tr>
                <?php } else {
                    foreach ($data['keluar_list'] as $a) {
                        $tokoNama = isset($this->dToko[$a['id_target']])
                            ? strtoupper($this->dToko[$a['id_target']]['nama_toko'])
                            : ('#' . $a['id_target']);
                        $st = (int)$a['st'];
                        ?>
                        <tr>
                            <td class="align-middle">
                                <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                                <div class="ok-meta"><?= htmlspecialchars($a['ref']) ?></div>
                            </td>
                            <td class="align-middle">
                                <span class="text-success fw-bold"><i class="fa-solid fa-arrow-right"></i></span>
                                <?= htmlspecialchars($tokoNama) ?>
                            </td>
                            <td class="align-middle text-end ok-amt fw-semibold">
                                <?= number_format((int)$a['jumlah']) ?>
                            </td>
                            <td class="align-middle text-end">
                                <?php if ($st === 0) { ?>
                                    <span class="text-warning">Checking</span>
                                <?php } elseif ($st === 1) { ?>
                                    <span class="text-success">Verified</span>
                                <?php } else { ?>
                                    <span class="text-secondary">—</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }
                } ?>
            </tbody>
        </table>

        <?php /* Setoran Kas Kantor — tidak ditampilkan (kode tetap disimpan)
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-success">Setoran Kas Kantor</th>
            </tr>
            <?php foreach ($data['split'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                    </td>
                    <td>
                        <?php if ($a['tipe'] == 0) { ?>
                            <?= strtoupper($this->dToko[$a['id_sumber']]['nama_toko']) ?> <span class='fw-bold text-success'><i class='fa-solid fa-arrow-right'></i></span> OFFICE
                        <?php } else { ?>
                            SDS <span class='fw-bold text-success'><i class='fa-solid fa-arrow-right'></i></span> OFFICE
                        <?php } ?>
                    </td>
                    <td>
                        <?= $a['note'] ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                    <td class="text-end" style="width:70px">
                        <a class="ajax" href="<?= PV::BASE_URL ?>Audit_KasKecil/verify_kasKecil/<?= $a['id'] ?>/1">Verify</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-secondary">Setoran Kas Kantor Terverifikasi</th>
            </tr>
            <?php foreach ($data['split_done'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                    </td>
                    <td>
                        <?= $a['ref'] ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                    <td class="text-end" style="width:70px">

                    </td>
                </tr>
            <?php } ?>
        </table>
        */ ?>
    </div>
</main>

<div class="modal fade" id="modalTopupOffice" tabindex="-1" aria-labelledby="modalTopupOfficeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="modalTopupOfficeLabel">Topup Petty Cash</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formOfficeTopup" action="<?= PV::BASE_URL ?>Office_Kas/topupPety" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Toko</label>
                        <select class="form-select" name="toko" required>
                            <option value="">— pilih toko —</option>
                            <?php foreach ($this->dToko as $dt) {
                                if ((int)$dt['id_toko'] === 0) {
                                    continue;
                                } ?>
                                <option value="<?= (int)$dt['id_toko'] ?>"><?= htmlspecialchars($dt['nama_toko']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Jumlah</label>
                        <input type="number" min="1" step="1" name="jumlah" class="form-control" placeholder="Rp" required>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnOfficeTopup" class="btn btn-sm btn-success">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var submitting = false;
    $("#formOfficeTopup").off("submit.petty").on("submit.petty", function(e) {
        e.preventDefault();
        if (submitting) {
            return false;
        }
        submitting = true;
        var $form = $(this);
        var $btn = $("#btnOfficeTopup");
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
                    var modalEl = document.getElementById("modalTopupOffice");
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
