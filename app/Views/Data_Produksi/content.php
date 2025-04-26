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
                            <div class="ps-0 pe-1">
                                <a href="" type="button" class="btn btn-sm btn-outline-dark">
                                    Gas
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

                            if ($no == 1) {
                                $ada = true;
                                $id_pelanggan = $do['id_pelanggan'];
                                $dateTime = substr($do['insertTime'], 0, 10);
                                $pelanggan = $data['pelanggan'][$do['id_pelanggan']]['nama'];
                                $cs = $data['karyawan'][$do['id_penerima']]['nama'];
                                $cs_id_aff = $do['id_user_afiliasi']; ?>
                            <?php } ?>
                        <?php } ?>

                        <?php

                        $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
                        $in_toko = "";
                        if ($id_toko_pelanggan <> $this->userData['id_toko']) {
                            $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
                        }
                        ?>

                        <div class="row mx-0">
                            <div class="col px-1" style="min-width: 200px;">
                                <table class="w-100 mb-1 target bg-white border-bottom">
                                    <tr data-id="<?= $id_pelanggan ?>">
                                        <td class="p-1">
                                            <small>
                                                <span data-bs-toggle="modal" data-bs-target="#modalOrder" style="cursor: pointer;" class="cekOrder" data-ref="<?= $ref ?>">
                                                    <span class="text-danger fw-bold"><?= substr($ref, -4) ?></span>
                                                </span>
                                                <span class="text-nowrap text-primary fw-bold"><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?>
                                            </small>
                                            <br>
                                            <small><?= substr($do['insertTime'], 2, -3) ?></small>
                                        </td>

                                        <?php if ($id_afiliasi == 0 || $this->userData['id_toko'] == $id_toko) { ?>
                                            <td class="text-end pe-1 align-top">
                                                <span class="text-sm text-success"><?= ucwords($cs) ?></span>
                                            </td>
                                            <?php } else {
                                            if ($id_user_afiliasi <> 0) { ?>
                                                <td class="text-end pe-1 text-success align-top">
                                                    <span class="text-sm"><?= ucwords($cs) ?></span><br>
                                                    <small class="text-warning">
                                                        <?php if ($cs_id_aff <> 0) {
                                                            $cs_aff = $data['karyawan'][$cs_id_aff]['nama']; ?>
                                                            <?= ucwords($cs_aff) ?>
                                                        <?php } else { ?>
                                                            AFF - Checking
                                                        <?php } ?>
                                                    </small>
                                                </td>
                                        <?php }
                                        } ?>
                                        <td style="width: 60px;" class="text-sm text-end pe-2 align-top">
                                            <span class="btnReady text-primary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-ref="<?= $ref ?>"> Ready<br>
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


<form action="<?= PV::BASE_URL; ?>Data_Produksi/ready" method="POST">
    <div class="modal" id="exampleModal4">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class=" form-label">Karyawan</label>
                                <input type="hidden" id="order_id" name="id">
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
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-sm btn-primary">Orderan Ready</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal" id="modalOrder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" id="cekOrder" style="min-height: 20px;">

        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $("span.btnReady").click(function() {
        id = $(this).attr("data-id");
        $("input[name=ambil_id]").val(id);
    })

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
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

    $('span.cekOrder').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cekOrder").html('');
        $("div#cekOrder").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
            $("div#cekOrder").load('<?= PV::BASE_URL ?>Cek/order/' + ref);
        });
    });
</script>