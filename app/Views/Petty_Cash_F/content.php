<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <?php
    $total_setor = 0;
    ?>
    <!-- Main page content-->
    <div class="container pb-4">
        <div class="row mx-0">
            <div class="col text-sm text-end fw-bold pe-0">
                Saldo Rp<?= number_format($data['saldo']) ?>
            </div>
            <div class="col-auto text-end">
                <div class="btn-group me-1">
                    <button type="button" class="btn shadow-none btn-sm btn-primary bg-gradient py-1 px-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Topup Petycash
                    </button>
                </div>
            </div>
        </div>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-success">Riwayat Topup</th>
            </tr>
            <?php foreach ($data['topup'] as $a) { ?>
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
                        <?php if ($a['st'] == 0) { ?>
                            <span class="text-sm text-warning">Checking</span>
                        <?php } else { ?>
                            <?php if ($a['st'] == 1) { ?>
                                <span class="text-sm text-success">Verified</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-danger">Pemakaian</th>
            </tr>
            <?php
            foreach ($data['pakai'] as $a) {
                if ($a['st'] == 1) {
                    $total_setor += $a['jumlah'];
                } ?>
                <tr>
                    <td>
                        <?= $a['tanggal'] == "" ? '<i class="fa-solid fa-server"></i> ' . date('d/m/y H:i', strtotime($a['insertTime'])) : '<i class="fa-solid fa-file-pen"></i> ' . $a['tanggal'] ?>
                        <br>
                        <span class="text-primary">
                            <i class="fa-regular fa-note-sticky"></i> <?= $a['note'] ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <span class='fw-bold text-danger'><i class='fa-solid fa-arrow-right'></i></span> <?= $data['jkeluar'][$a['id_target']]['nama'] ?><br>
                        <span class="text-primary"><?= number_format($a['jumlah']) ?></span>
                    </td>
                    <td class="text-end" style="width:70px">
                        <?php if ($a['st'] == 0) { ?>
                            <a class="ajax btn btn-sm btn-success bg-gradient" href="<?= PV::BASE_URL ?>Petty_Cash_F/verify/<?= $a['id'] ?>/1">Verify</a>
                        <?php } else { ?>
                            <?php if ($a['st'] == 1) { ?>
                                <span class="text-sm text-success">Verified</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Topup PetyCash
            </div>
            <form id="formPettyTopup" class="ajax" action="<?= PV::BASE_URL ?>Petty_Cash_F/topupPety" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Jumlah</label>
                        <input type="number" min="1" name="jumlah" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnPettyTopup" class="btn btn-success">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $("a.ajax").click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.ajax({
            url: href,
            type: 'POST',
            data: {},
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    })

    $("form").on("submit", function(e) {
        e.preventDefault();
    });

    (function() {
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
                        var modalEl = document.getElementById("exampleModal");
                        if (modalEl) {
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }
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