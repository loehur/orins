<style>
    tr:hover {
        background-color: ghostwhite;
    }
</style>
<main>
    <div class="bg-white w-100">
        <div class="px-2 py-2 rounded bg-light mx-2 my-0 border" style="max-width: 500px;">
            <div class="row">
                <div class="col pe-0">
                    <input type="text" placeholder="Cari Pelanggan..." id="myInput" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <input type="date" id="dateNya" class="form form-control form-control-sm" max="<?= date("Y-m-d") ?>" value="<?= $data['parse'] ?>">
                </div>
            </div>
        </div>
    </div>
    <?php $cs_arr = [] ?>
    <?php $cs_arr_data = [] ?>
    <small>
        <div class="mx-2 rounded px-2">
            <div class="row">
                <div class="col px-0 overflow-auto mt-2" style="max-width: 500px;height: 700px;">
                    <?php if (count($data['order']) == 0) { ?>
                        <div class="row mx-0">
                            <div class="col px-1" style="min-width: 200px;">
                                <table class="w-100 mb-0 target bg-white border-bottom table">
                                    <tr>
                                        <td><small><i>No Data</i></small></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php } ?>

                    <?php foreach ($data['order'] as $ref => $do) { ?>
                        <?php
                        $insertTime = $do['insertTime'];
                        $id_pelanggan = $do['id_pelanggan'];
                        $dateTime = substr($do['insertTime'], 0, 10);
                        $pelanggan = $data['pelanggan'][$do['id_pelanggan']]['nama'];
                        $cs = $data['karyawan_toko'][$do['id_penerima']]['nama'];
                        $detail_arr = unserialize($do['produk_detail']);
                        ?>

                        <div class="row mx-0">
                            <div class="col px-1" style="min-width: 200px;">
                                <table class="w-100 mb-0 target bg-white border-bottom table <?= $ref ?>">
                                    <tr>
                                        <td style="width: 30px;"><span class="cekOrder text-purple" data-bs-toggle="modal" data-bs-target="#modalOrder" style="cursor: pointer;" data-ref="<?= $ref ?>"><i class="fa-regular fa-eye"></i></span></td>
                                        <td class="p-1">
                                            <a href="<?= PV::BASE_URL ?>Data_Operasi/index/<?= $id_pelanggan ?>" class="cekPLG text-decoration-none text-dark" style="cursor: pointer;">
                                                <small>
                                                    <span class="text-danger"><?= substr($ref, -4) ?></span>
                                                    <span class="text-nowrap text-primary fw-bold"><span class="text-success"></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?>
                                                </small>
                                                <small><?= ucwords($cs) ?>,<br><span class="text-secondary"><i><?= $do['produk'] ?>...</i></span></small>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col px-0 overflow-auto mt-2" style="max-width: 500px;height: 700px;">
                    <?php if (count($data['order']) == 0) { ?>
                        <div class="row mx-0">
                            <div class="col px-1" style="min-width: 200px;">
                                <table class="w-100 mb-0 target bg-white border-bottom table">
                                    <tr>
                                        <td><small><i>No Data</i></small></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                    <?php foreach ($data['mutasi'] as $ref => $do) { ?>
                        <?php
                        $insertTime = $do['insertTime'];
                        $id_pelanggan = $do['id_target'];
                        $dateTime = substr($do['insertTime'], 0, 10);
                        $pelanggan = $data['pelanggan'][$id_pelanggan]['nama'];
                        $cs = $data['karyawan_toko'][$do['cs_id']]['nama'];
                        $mBarang = $data['barang'][$do['id_barang']];
                        $barang = $mBarang['brand'] . " " . $mBarang['model'];
                        ?>

                        <div class="row mx-0">
                            <div class="col px-1" style="min-width: 200px;">
                                <table class="w-100 mb-0 target bg-white border-bottom table <?= $ref ?>">
                                    <tr data-id="<?= $id_pelanggan ?>">
                                        <td style="width: 30px;"><span class="cekOrder text-purple" data-bs-toggle="modal" data-bs-target="#modalOrder" style="cursor: pointer;" data-ref="<?= $ref ?>"><i class="fa-regular fa-eye"></i></span></td>
                                        <td class="p-1">
                                            <a href="<?= PV::BASE_URL ?>Data_Operasi/index/<?= $id_pelanggan ?>" class="cekPLG text-decoration-none text-dark" style="cursor: pointer;">
                                                <small>
                                                    <span class="text-danger"><?= substr($ref, -4) ?></span>
                                                    <span class="text-nowrap text-primary fw-bold"><span class="text-success"></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?>
                                                </small>
                                                <small><?= ucwords($cs) ?>,<br><span class="text-secondary"><i><?= $barang ?>...</i></span></small>
                                            </a>
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
            <div class="modal-content" style="height: 450px;">
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
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
                        <div class="row mb-3">
                            <div class="col">
                                <label class=" form-label">Pengiriman</label>
                                <input type="hidden" id="ref" name="ref">
                                <select class="form-select tize" name="expedisi" required>
                                    <option value="0">-</option>
                                    <?php foreach ($data['ea'] as $ea) { ?>
                                        <option value="<?= $ea['id'] ?>"><?= ucwords($ea['name']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-dark w-100 bg-gradient"><i class="fa-solid fa-check-double"></i> Ready</button>
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
        <div class="modal-content" style="min-height: 20px;">
            <div class="modal-header">
                <h5 class="modal-title">Data Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="cekOrder"></div>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $("#dateNya").change(function() {
        content($(this).val());
    })
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

    $('.cekOrder').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cekOrder").html('');
        $("div#cekOrder").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
            $("div#cekOrder").load('<?= PV::BASE_URL ?>Cek/order/' + ref);
        });
    });
</script>