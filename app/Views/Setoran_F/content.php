<main class="container">
    <?php $total = 0 ?>
    <?php if (count($data['setor']) > 0) { ?>
        <div class="pt-2 pe-2 pb-0 ms-3 mt-3 me-3 bg-white">
            <div class="row border-bottom mb-2">
                <div class="col ms-2">
                    <span class="text-danger">Setoran Kasir dalam Pengecekan</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table table-sm mb-2 ms-2">
                        <?php foreach ($data['setor'] as $set) {
                            if (isset($data['keluar'][$set['ref_setoran']]['count'])) {
                                $count_keluar = $data['keluar'][$set['ref_setoran']]['count'];
                                $jumlah_keluar = $data['keluar'][$set['ref_setoran']]['jumlah'];
                            } else {
                                $count_keluar = 0;
                                $jumlah_keluar = 0;
                            }
                            $totalSetor = $set['jumlah'] - $jumlah_keluar; ?>

                            <tr>
                                <td class="text-primary align-top" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid mt-1 fa-list-check"></i></small></span></td>
                                <td class="text-success"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $set['id_toko']) ?></td>
                                <td><?= $set['ref_setoran'] ?></small></td>
                                <td class="text-end"><?= $set['count'] + $count_keluar ?> Trx</td>
                                <td class="text-end">
                                    Rp<?= number_format($totalSetor) ?><br>
                                    <?php
                                    if (isset($data['split'][$set['ref_setoran']])) {
                                        $ds = $data['split'][$set['ref_setoran']];
                                        $st_slip = "";
                                        switch ($ds['st']) {
                                            case 0:
                                                $st_slip = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                                break;
                                            case 1:
                                                $st_slip = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                                break;
                                            default:
                                                $st_slip = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                                break;
                                        } ?>
                                        <?php $totalSetor -= $ds['jumlah'] ?>
                                        <div class="text-sm">
                                            Uang Kecil <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span><br>
                                        </div>
                                    <?php }
                                    if (isset($data['setor_office'][$set['ref_setoran']])) {
                                        $ds = $data['setor_office'][$set['ref_setoran']];
                                        $st_slip = "";
                                        switch ($ds['st']) {
                                            case 0:
                                                $st_slip = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                                break;
                                            case 1:
                                                $st_slip = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                                break;
                                            default:
                                                $st_slip = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                                break;
                                        } ?>
                                        <div class="text-sm">
                                            Kas Office <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span>
                                        </div>
                                        <?php $totalSetor -= $ds['jumlah'] ?>
                                    <?php } ?>
                                    <span class="fw-bold">Setor Bank <span class="text-success"><?= number_format($totalSetor) ?></span>
                                </td>
                                <td style="width: 80px;">
                                    <button data-id="<?= $set['ref_setoran'] ?>" data-val="1" class="verify btn btn-sm shadow-sm btn-primary bg-gradient rounded-pill">Verify</button>
                                </td>
                                <td class="text-end">
                                    <button data-id="<?= $set['ref_setoran'] ?>" data-val="2" class="verify btn btn-sm shadow-sm btn-outline-danger bg-gradient rounded-pill">Reject</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="pt-2 pe-2 pb-0 ms-3 mt-3 me-3 bg-white">
        <div class="row border-bottom mb-2">
            <div class="col ms-2">
                <span class="text-purple">Setoran Kasir Terkonfirmasi</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-sm mb-2 ms-2 text-sm">
                    <?php foreach ($data['setor_done'] as $set) {
                        if (isset($data['keluar'][$set['ref_setoran']]['count'])) {
                            $count_keluar = $data['keluar'][$set['ref_setoran']]['count'];
                            $jumlah_keluar = $data['keluar'][$set['ref_setoran']]['jumlah'];
                        } else {
                            $count_keluar = 0;
                            $jumlah_keluar = 0;
                        }
                        $totalSetor = $set['jumlah'] - $jumlah_keluar;

                        $st_setor = "";
                        switch ($set['status_setoran']) {
                            case 0:
                                $st_setor = "<span class='text-warning'>Finance</span>";
                                break;
                            case 1:
                                $st_setor = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                break;
                            default:
                                $st_setor = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                break;
                        }

                        $time = strtotime(substr($set['ref_setoran'], 0, 8));
                        $setor = date('d/m/Y', $time);
                    ?>
                        <tr>
                            <td class="text-primary pt-2" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check"></i></small></span></td>
                            <td class="text-success"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $set['id_toko']) ?></td>
                            <td><?= $set['ref_setoran'] ?></small></td>
                            <td class="text-end"><?= $set['count'] + $count_keluar ?> Trx</td>
                            <td class="text-end">
                                Rp<?= number_format($totalSetor) ?><br>
                                <?php
                                if (isset($data['split'][$set['ref_setoran']])) {
                                    $ds = $data['split'][$set['ref_setoran']];
                                    switch ($ds['st']) {
                                        case 0:
                                            $st_slip = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                            break;
                                        case 1:
                                            $st_slip = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                            break;
                                        default:
                                            $st_slip = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                            break;
                                    } ?>
                                    <?php $totalSetor -= $ds['jumlah'] ?>
                                    <div class="text-sm">
                                        <?= $st_slip ?> Uang Kecil <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span><br>
                                    </div>
                                <?php }
                                if (isset($data['setor_office'][$set['ref_setoran']])) {
                                    $ds = $data['setor_office'][$set['ref_setoran']];
                                    switch ($ds['st']) {
                                        case 0:
                                            $st_slip2 = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                            break;
                                        case 1:
                                            $st_slip2 = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                            break;
                                        default:
                                            $st_slip2 = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                            break;
                                    } ?>
                                    <?php $totalSetor -= $ds['jumlah'] ?>
                                    <div class="text-sm">
                                        <?= $st_slip2 ?> Kas Office <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span>
                                    </div>
                                    <?php $totalSetor -= $ds['jumlah'] ?>
                                <?php } ?>
                                <?= $st_setor ?> <span class="fw-bold">Setor Bank <span class="text-success"><?= number_format($totalSetor) ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal" id="modalCek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="cek_load">

        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("button.verify").click(function() {
        var val = $(this).attr('data-val');
        var ref_ = $(this).attr('data-id');
        $.ajax({
            url: "<?= PV::BASE_URL ?>Setoran_F/setor/" + val,
            data: {
                ref: ref_
            },
            type: "POST",
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result);
                }
            },
        });
    });

    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cek_load").load('<?= PV::BASE_URL ?>Setoran_F/cek/' + ref);
    });
</script>