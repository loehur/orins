<main>
    <div class="pt-2 pe-2 pb-0 ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <div class="input-group">
                    <span class="input-group-text text-primary">Bulan</span>
                    <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" value="<?= $data['m'] ?>" placeholder="YYYY-MM" class="form-control" required>
                    <button id="cekS" class="btn btn-primary">Cek</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-sm mb-2 ms-2">
                    <?php foreach ($data['setor'] as $set) {
                        $st_setor = "";
                        switch ($set['status_setoran']) {
                            case 0:
                                $st_setor = "<span class='text-warning'><i class='fa-regular fa-circle'></i> Finance Checking</span>";
                                break;
                            case 1:
                                $st_setor = "<span class='text-success'><i class='fa-solid fa-circle-check'></i> Verified</span>";
                                break;
                            default:
                                $st_setor = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                break;
                        }
                    ?>
                        <tr>
                            <td class="text-primary" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check"></i></small></span></td>
                            <td><?= $set['count'] ?> Transaksi</td>
                            <td><?= $set['ref_setoran'] ?></td>
                            <td class="text-end">Rp<?= number_format($set['jumlah']) ?></td>
                            <td style="width: 1px; white-space: nowrap;"><?= $st_setor ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal" id="modalCek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="cek_load"></div>
    </div>
</div>

<form action="<?= PV::BASE_URL; ?>Setoran/cancel" method="POST">
    <div class="modal" id="exampleModalCancel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pembatalan!</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Alasan Cancel</label>
                                <input type="text" name="reason" class="form-control form-control-sm" required>
                                <input type="hidden" name="id_kas">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Cancel Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("button#cekS").click(function() {
        var mon = $("input[name=month]").val();
        content(mon);
    });

    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cek_load").load('<?= PV::BASE_URL ?>Setoran/cek/' + ref);
    });
</script>