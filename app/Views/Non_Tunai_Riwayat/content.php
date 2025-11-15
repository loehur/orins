<main>
    <div class="p-2 ms-3 me-3 bg-white">
        <div class="row">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <div class="input-group">
                    <span class="input-group-text text-primary">Tanggal</span>
                    <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" value="<?= $data['m'] ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                    <button class="cekS btn btn-primary">Cek</button>
                </div>
            </div>
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <div class="input-group">
                    <span class="input-group-text text-success">ID</span>
                    <input name="idKas" type="text" class="form-control" required>
                    <button class="cekID btn btn-success">Cek</button>
                </div>
            </div>
            <div class="col-auto ps-0" id="loading">
                <div class="lds-ellipsis">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <small>
                    <table class="table table-sm text-sm table-hover mt-2">
                        <?php
                        $no = 0;
                        foreach ($data['kas_done'] as $rb => $ref_bayar) {
                            foreach ($ref_bayar as $a) {
                                $no += 1;
                                $id =  $a['id_kas'];

                                $client = $a['id_client'];
                                $jumlah = $a['jumlah'];
                                $ref = $a['ref_transaksi'];

                                if (isset($data['ref'][$ref]['tuntas'])) {
                                    $tuntas = $data['ref'][$ref]['tuntas'];
                                } else {
                                    $tuntas = 0;
                                }

                                if (isset($data['payment_account'][$a['pa']]['payment_account'])) {
                                    $payment_account = $data['payment_account'][$a['pa']]['payment_account'] . " ";
                                } else {
                                    $payment_account = "";
                                }

                                $pelanggan = "Non";
                                $pelanggan = $data['pelanggan'][$client]['nama']; ?>
                                <tr>
                                    <td>
                                        <span class="text-purple"><?= $data['toko'][$a['id_toko']]['nama_toko'] ?></span><br>
                                        <small><?= $a['ref_transaksi'] == "" ? "Deposit" : $a['ref_transaksi'] ?></small>
                                    </td>
                                    <td><a href="<?= PV::BASE_URL ?>Cek/order/<?= $a['ref_transaksi'] ?>/<?= $a['id_client'] ?>" target="_blank">#<?= $a['id_kas'] ?></a><br><?= strtoupper($pelanggan) ?></td>
                                    <td align="right" class="pe-2">Rp<?= number_format($jumlah) ?><br><span class="text-primary"><?= $payment_account ?></span><?= $a['note'] ?></td>
                                    <td class="text-end">
                                        <?php
                                        switch ($a['status_mutasi']) {
                                            case 1:
                                                echo '<span class="text-success"><i class="fa-solid fa-check-to-slot"></i> Verified</span>';
                                                break;
                                            default:
                                                echo '<span class="text-danger"><i class="fa-solid fa-xmark"></i> Rejected</span>';
                                                break;
                                        }
                                        ?>
                                        <br>
                                        <?= $a['updateTime'] ?>
                                    </td>

                                    <?php

                                    if ($tuntas == 0) {
                                        switch ($a['status_mutasi']) {
                                            case 1: ?>
                                                <td align="right">
                                                    <button data-id="<?= $id ?>" data-val="2" class="action btn btn-sm btn-outline-secondary px-2 py-0 border-0">Reject</button>
                                                </td>
                                            <?php break;
                                            default: ?>
                                                <td align="right">
                                                    <button data-id="<?= $id ?>" data-val="1" class="action btn btn-sm btn-outline-secondary px-2 py-0 border-0">Verify</button>
                                                    <br>
                                                    <span class="pe-2"><?= $a['note_batal'] ?></span>
                                                </td>
                                            <?php break;
                                        }
                                    } else {
                                        switch ($a['status_mutasi']) {
                                            case 1: ?>
                                                <td align="right" class="text-secondary">
                                                    <small><span class="pe-2">Transaction Complete<br><?= $a['note_office'] ?></span></small>
                                                </td>
                                            <?php break;
                                            default: ?>
                                                <td align="right">
                                                    <small><span class="pe-2">Transaction Complete<br><?= $a['note_batal'] ?></span></small>
                                                </td>
                                    <?php break;
                                        }
                                    }
                                    ?>
                                </tr>
                        <?php }
                        }
                        ?>
                    </table>
                </small>
            </div>
        </div>
    </div>
</main>

<div class="modal" id="modalCek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="cekOrder"></div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("button.cekS").click(function() {
        $("#loading").show();
        var mon = $("input[name=month]").val();
        content(mon);
    });

    $("button.cekID").click(function() {
        $("#loading").show();
        var mon = $("input[name=idKas]").val();
        content(mon);
    });

    $("button.action").click(function() {
        var id_ = $(this).attr("data-id");
        var value = $(this).attr("data-val");
        var note = "";

        if (value == 2) {
            var note = prompt("Catatan", "");
            if (note === null) {
                return;
            }
        }

        $.ajax({
            url: "<?= PV::BASE_URL ?>Non_Tunai/action",
            data: {
                id: id_,
                val: value,
                note: note
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
</script>