<style>
    tr:hover {
        background-color: ghostwhite;
    }
</style>
<main>
    <div class="bg-white w-100">
        <div class="p-2 rounded bg-light ms-2 mb-2 me-1 border pb-0" style="max-width: 500px;">
            <div class="row mb-1">
                <div class="col-auto pe-0">
                    <input type="text" placeholder="Cari Pelanggan..." id="myInput" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <form id="main">
                        <div class="d-flex align-items-start align-items-end pt-1">
                            <div class="ps-0 pe-1 text-sm">
                                <?php
                                foreach ($data['cs'] as $dc) {
                                    $cs = $data['karyawan'][$dc]['nama']; ?>
                                    <span class="<?= $dc ?> myInput2" data-cs="<?= ucwords($cs) ?>" style="cursor: pointer;"><?= ucwords($cs) ?> <span id="<?= $dc ?>"></span>,</span>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $cs_arr = [] ?>
    <small>
        <div class="mx-2 rounded px-2">
            <div class="row">
                <div class="col px-0 overflow-auto mt-2" style="max-width: 500px;height: 700px;">
                    <?php foreach ($data['order'] as $ref => $dos) { ?>
                        <?php
                        $no = 0;
                        $id_afiliasi = 0;

                        foreach ($dos as $do) {
                            $cancel = $do['cancel'];
                            $id_ambil = $do['id_ambil'];
                            $id_user_afiliasi = $do['id_user_afiliasi'];
                            $id_afiliasi = $do['id_afiliasi'];
                            $id_toko = $do['id_toko'];

                            $insertTime = $do['insertTime'];
                            $start_date = new DateTime($insertTime);
                            $since_start = $start_date->diff(new DateTime(date("Y-m-d H:i:s")));

                            $jumlah = ($do['harga'] * $do['jumlah']) + $do['margin_paket'];

                            if ($cancel == 0 && $do['stok'] == 0) {
                                $no++;
                            }

                            $divisi_arr = unserialize($do['spk_dvs']);
                            $countSPK = count($divisi_arr);

                            if ($id_ambil == 0) {
                                if ($countSPK > 0 && $cancel == 0) {
                                    $ambil_all[$ref] = false;
                                }
                            }

                            $cs_id_aff = 0;
                            if ($do['id_user_afiliasi'] <> 0) {
                                $cs_id_aff = $do['id_user_afiliasi'];
                            }

                            if ($no == 1) {
                                if ($id_afiliasi == $this->userData['id_toko']) {
                                    if (isset($cs_arr[$do['id_user_afiliasi']])) {
                                        $cs_arr[$do['id_user_afiliasi']] += 1;
                                    } else {
                                        $cs_arr[$do['id_user_afiliasi']] = 1;
                                    }
                                } else {
                                    if (isset($cs_arr[$do['id_penerima']])) {
                                        $cs_arr[$do['id_penerima']] += 1;
                                    } else {
                                        $cs_arr[$do['id_penerima']] = 1;
                                    }
                                }

                                $ada = true;
                                $id_pelanggan = $do['id_pelanggan'];
                                $dateTime = substr($do['insertTime'], 0, 10);
                                $pelanggan = $data['pelanggan'][$do['id_pelanggan']]['nama'];
                                $cs = $data['karyawan'][$do['id_penerima']]['nama']; ?>
                            <?php } ?>
                        <?php } ?>

                        <?php

                        $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
                        $in_toko = "";
                        if ($id_toko_pelanggan <> $this->userData['id_toko']) {
                            $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
                        }
                        ?>

                        <div class="row mx-0" id="<?= $ref ?>">
                            <div class="col px-1" style="min-width: 200px;">
                                <table class="w-100 mb-0 target bg-white border-bottom table <?= $ref ?>">
                                    <tr data-id="<?= $id_pelanggan ?>">
                                        <td class="p-1">
                                            <small>
                                                <span data-bs-toggle="modal" data-bs-target="#modalOrder" style="cursor: pointer;" class="cekOrder" data-ref="<?= $ref ?>">
                                                    <span class="text-danger fw-bold"><?= substr($ref, -4) ?></span>
                                                </span>
                                                <span class="text-nowrap text-primary fw-bold"><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?>
                                            </small>
                                            <br>
                                            <small><?= ucwords($cs) ?>, <?= $since_start->days ?> Hari, <?= $since_start->h ?> Jam</small>
                                        </td>
                                        <?php if ($id_afiliasi == 0 || $this->userData['id_toko'] == $id_toko) { ?>
                                            <?php if ($id_afiliasi <> 0) { ?>
                                                <td class="text-sm text-end">
                                                    <?php if ($this->userData['id_toko'] <> $id_toko) { ?>
                                                        <span class="text-dark">
                                                            <?php if ($cs_id_aff <> 0) {
                                                                $cs_aff = $data['karyawan'][$cs_id_aff]['nama']; ?>
                                                                <i class="fa-solid fa-check"></i> <?= ucwords($cs_aff) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                    <?php } ?>

                                                    <span class="text-dark">
                                                        <?php if ($data['data_ref'][$ref]['ready_aff_cs'] <> 0) {
                                                            $cs_aff_ready = $data['karyawan'][$data['data_ref'][$ref]['ready_aff_cs']]['nama']; ?>
                                                            <i class="fa-solid fa-check-double"></i> <?= ucwords($cs_aff_ready) ?>
                                                        <?php } else { ?>
                                                            <i class="fa-regular fa-circle"></i>
                                                        <?php } ?>
                                                    </span>
                                                </td>
                                            <?php } ?>
                                            <?php } else {
                                            if ($id_afiliasi <> 0) { ?>
                                                <td class="text-sm text-end">
                                                    <span class="text-dark">
                                                        <?php if ($cs_id_aff <> 0) {
                                                            $cs_aff = $data['karyawan'][$cs_id_aff]['nama']; ?>
                                                            <i class="fa-solid fa-check"></i> <?= ucwords($cs_aff) ?>
                                                        <?php } else { ?>
                                                            <i class="fa-regular fa-circle"></i>
                                                        <?php } ?>
                                                    </span>
                                                    &nbsp;
                                                    <span class="text-dark">
                                                        <?php if ($data['data_ref'][$ref]['ready_aff_cs'] <> 0) {
                                                            $cs_aff_ready = $data['karyawan'][$data['data_ref'][$ref]['ready_aff_cs']]['nama']; ?>
                                                            <i class="fa-solid fa-check-double"></i> </i> <?= ucwords($cs_aff_ready) ?>
                                                        <?php } else { ?>
                                                            <i class="fa-regular fa-circle"></i>
                                                        <?php } ?>
                                                    </span>
                                                </td>
                                        <?php }
                                        } ?>
                                        <td style="width: 70px;" class="text-sm text-end pe-2 align-top">
                                            <span class="btnReady fw-bold" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-cs="<?= $id_afiliasi == $this->userData['id_toko'] ? $do['id_user_afiliasi'] : $do['id_penerima'] ?>" data-ref="<?= $ref ?>"> <i class="fa-solid fa-check-double"></i> Ready
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </small>
</main>

<?php $cs_json = json_encode($cs_arr) ?>

<form action="<?= PV::BASE_URL; ?>Data_Produksi/ready" method="POST">
    <div class="modal" id="exampleModal4">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="height: 350px;">
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class=" form-label">Karyawan</label>
                                <input type="hidden" id="ref" name="ref">
                                <select class="form-select tize" name="staf_id" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan_toko'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= ucwords($k['nama']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-sm btn-dark">Orderan Ready</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal" id="modalOrder" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" id="cekOrder" style="min-height: 20px;">

        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    var cs_json = '<?= $cs_json ?>';
    var cs_data = JSON.parse(cs_json);

    cs_show();

    setInterval(cs_show, 1000);

    function cs_show() {
        Object.keys(cs_data).forEach(function(key) {
            if (cs_data[key] < 1) {
                $("span." + key).remove();
            }
            $("span#" + key).html(cs_data[key]);
        })
    }

    $(document).ready(function() {
        $('select.tize').selectize();
    });

    var ref = "";
    var cs = 0;

    $("span.btnReady").click(function() {
        ref = $(this).attr("data-ref");
        cs = $(this).attr("data-cs");
        $("table").removeClass("table-secondary");
        $("table." + ref).addClass("table-secondary");
        $("input#ref").val(ref);
    })

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    cs_data[cs] -= 1;
                    cs_show();
                    $("div#" + ref).remove();
                } else {
                    alert(res);
                }
            }
        });
    });

    $("#myInput").on("keyup", function() {
        var input = this.value;
        var filter = input.toLowerCase();
        var nodes = document.getElementsByClassName('target');

        if (filter.length > 0) {
            for (i = 0; i < nodes.length; i++) {
                if (nodes[i].innerText.toLowerCase().includes(filter)) {
                    nodes[i].style.display = "table";
                } else {
                    nodes[i].style.display = "none";
                }
            }
        } else {
            for (i = 0; i < nodes.length; i++) {
                nodes[i].style.display = "table";
            }
        }
    });

    $(".myInput2").on("click", function() {
        var input = $(this).attr('data-cs') + ",";
        var filter = input.toLowerCase();
        var nodes = document.getElementsByClassName('target');

        if (filter.length > 0) {
            for (i = 0; i < nodes.length; i++) {
                if (nodes[i].innerText.toLowerCase().includes(filter)) {
                    nodes[i].style.display = "table";
                } else {
                    nodes[i].style.display = "none";
                }
            }
        } else {
            for (i = 0; i < nodes.length; i++) {
                nodes[i].style.display = "table";
            }
        }
    });

    $('span.cekOrder').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cekOrder").html('');
        $("div#cekOrder").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
            $("div#cekOrder").load('<?= PV::BASE_URL ?>Cek/order/' + ref);
        });
    });
</script>