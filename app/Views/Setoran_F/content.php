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
                            $time = strtotime(substr($set['ref_setoran'], 0, 8));
                            $setor = date('d/m/Y', $time);
                        ?>
                            <tr>
                                <td class="text-primary align-middle" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check"></i></small></span></td>
                                <td class="text-success"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $set['id_toko']) ?></td>
                                <td><?= $setor ?><small class="text-secondary">-<?= substr($set['ref_setoran'], 8) ?></small></td>
                                <td><?= $set['count'] ?> Transaksi</td>
                                <td class="text-end">Rp<?= number_format($set['jumlah']) ?></td>
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
                <span class="text-purple">Setoran Kasir Terkonfirmasi</span> <small>(Last 20)</small>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-sm mb-2 ms-2">
                    <?php foreach ($data['setor_done'] as $set) {
                        $st_setor = "";
                        switch ($set['status_setoran']) {
                            case 0:
                                $st_setor = "<span class='text-warning'>Finance Checking</span>";
                                break;
                            case 1:
                                $st_setor = "<span class='text-success'><i class='fa-solid fa-circle-check'></i> Verified</span>";
                                break;
                            default:
                                $st_setor = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                break;
                        }

                        $time = strtotime(substr($set['ref_setoran'], 0, 8));
                        $setor = date('d/m/Y', $time);
                    ?>
                        <tr>
                            <td class="text-primary" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check"></i></small></span></td>
                            <td class="text-success"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $set['id_toko']) ?></td>
                            <td><?= $setor ?><small class="text-secondary">-<?= substr($set['ref_setoran'], 8) ?></small></td>
                            <td><?= $set['count'] ?> Transaksi</td>
                            <td class="text-end">Rp<?= number_format($set['jumlah']) ?></td>
                            <td style="width: 80px;"><?= $st_setor ?></td>
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