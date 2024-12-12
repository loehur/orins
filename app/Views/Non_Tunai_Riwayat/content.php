<style>
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
    }

    .lds-ellipsis div {
        position: absolute;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #fdd;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }

    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(0);
        }
    }

    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }

        100% {
            transform: translate(24px, 0);
        }
    }
</style>
<main>
    <div class="p-2 ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <div class="input-group">
                    <span class="input-group-text text-primary">Tanggal</span>
                    <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" value="<?= $data['m'] ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                    <button id="cekS" class="btn btn-primary">Cek</button>
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
                <div class="row border-bottom">
                    <div class="col ms-2">
                        <span><b>Riwayat Pembayaran Terkonfirmasi</b></span>
                    </div>
                </div>
                <small>
                    <table class="table table-sm table-hover mt-2">
                        <tr>
                            <th>Toko/Ref</th>
                            <th>Customer</th>
                            <th class="text-end">Jumlah/Via</th>
                            <th class="text-end">Status</th>
                            <th class="text-end">Re-Action</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['kas_done'] as $a) {
                            $no += 1;
                            $id =  $a['id_kas'];

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            $ref = $a['ref_transaksi'];

                            $pelanggan = "Non";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $client) {
                                    $pelanggan = $dp['nama'];
                                }
                            }
                        ?>
                            <tr>
                                <td>
                                    <span class="text-purple"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $a['id_toko']) ?></span><br>
                                    <small><?= $a['ref_transaksi'] ?></small>
                                </td>
                                <td>#<?= $a['id_kas'] ?><br><?= strtoupper($pelanggan) ?></td>
                                <td align="right" class="pe-2">Rp<?= number_format($jumlah) ?><br><?= $a['note'] ?></td>
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
                                $tuntas = $this->db(0)->get_where_row("order_data", "ref = '" . $ref . "'")['tuntas'];
                                if ($tuntas == 0) {
                                    switch ($a['status_mutasi']) {
                                        case 1:
                                ?>
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
                                        case 1:
                                        ?>
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
                        <?php } ?>
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

    $("button.actionMulti").click(function() {
        var id_ = $(this).attr("data-id");
        var value = $(this).attr("data-val");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Non_Tunai/actionMulti",
            data: {
                id: id_,
                val: value
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
        $("div#cekOrder").html("");
        $("div#cekOrder").load('<?= PV::BASE_URL ?>Non_Tunai/cekOrder/' + ref);
    });

    $("button#cekS").click(function() {
        $("#loading").show();
        var mon = $("input[name=month]").val();
        content(mon);
    });

    $(document).ready(function() {
        $("#loading").hide();
    });
</script>