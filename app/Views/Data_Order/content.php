<?php $modeView = $data['parse'] ?>
<?php $target_jenis = $data['parse_2'] ?>
<?php $arr_tuntas = [] ?>

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
                                <?php $outline = ($modeView == 0) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/0/<?= $data['parse_2'] ?>" type="button" class="btn btn-sm btn-<?= $outline ?>primary">
                                    Terkini
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                            <div class="ps-0 pe-1">
                                <?php $outline = ($modeView == 1) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/1/<?= $data['parse_2'] ?>" type="button" class="btn btn-sm btn-<?= $outline ?>success">
                                    >1 Minggu
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                            <div class="ps-0 pe-1">
                                <?php $outline = ($modeView == 2) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/2/<?= $data['parse_2'] ?>" type="button" class="btn btn-sm btn-<?= $outline ?>info">
                                    >1 Bulan
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                            <div class="ps-0 pe-1">
                                <?php $outline = ($modeView == 3) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/3/<?= $data['parse_2'] ?>" type="button" class="btn btn-sm btn-<?= $outline ?>secondary">
                                    >1 Tahun
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    $today = date("Y-m-d");

    foreach ($data['refs'] as $ref) {
        $verify_payment[$ref] = 0;
        $bill[$ref] = 0;

        if (isset($data['kas'][$ref])) {
            $verify_payment[$ref] += $data['kas'][$ref]['jumlah'];
        }
        if (isset($data['charge'][$ref])) {
            $bill[$ref] += $data['charge'][$ref]['jumlah'];
        }
        if (isset($data['diskon'][$ref])) {
            $verify_payment[$ref] += $data['diskon'][$ref]['jumlah'];
        }
    }
    ?>

    <small>
        <div class="mx-2 rounded px-2">
            <div class="row">
                <div class="col px-0 overflow-auto mt-2" style="max-width: 500px;height: 700px;">
                    <?php foreach ($data['refs'] as $key => $ref) { ?>
                        <?php
                        $no = 0;
                        $lunas[$ref] = false;
                        $ambil_all[$ref] = true;
                        $id_afiliasi = 0;
                        $cs_id_aff = 0;
                        $ada = false;

                        if (isset($data['order'][$ref])) {
                            foreach ($data['order'][$ref] as $do) {
                                $cancel = $do['cancel'];
                                $id_ambil = $do['id_ambil'];
                                $id_user_afiliasi = $do['id_user_afiliasi'];

                                if ($do['id_afiliasi'] <> 0) {
                                    $id_afiliasi = $do['id_afiliasi'];
                                }
                                $id_toko = $do['id_toko'];

                                $jumlah = ($do['harga'] * $do['jumlah']) + $do['margin_paket'];

                                if ($cancel == 0 && $do['stok'] == 0) {
                                    $no++;
                                    $bill[$ref] += $jumlah;
                                    $bill[$ref] -= $do['diskon'];
                                }

                                $divisi_arr = unserialize($do['spk_dvs']);
                                $countSPK = count($divisi_arr);

                                if ($id_ambil == 0) {
                                    if ($countSPK > 0 && $cancel == 0) {
                                        $ambil_all[$ref] = false;
                                    }
                                }

                                if ($do['id_user_afiliasi'] <> 0) {
                                    $cs_id_aff = $do['id_user_afiliasi'];
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

                            <?php unset($data['refs'][$key]); ?>
                        <?php } ?>

                        <?php
                        if ($ada == true) {
                            $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
                            $in_toko = "";
                            if ($id_toko_pelanggan <> $this->userData['id_toko']) {
                                $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
                            }

                            $sisa[$ref] = $bill[$ref] - $verify_payment[$ref];
                            if ($sisa[$ref] <= 0) {
                                $lunas[$ref] = true;
                            } else {
                                $lunas[$ref] = false;
                            } ?>

                            <div class="row mx-0">
                                <div class="col px-1" style="min-width: 200px;">
                                    <table class="w-100 target bg-white <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                        <tr data-id="<?= $id_pelanggan ?>" class="cekPLG" style="cursor: pointer;">
                                            <td class="px-1 pb-0 pt-1">
                                                <small><span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-nowrap text-primary fw-bold"><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?></small>
                                                <?php if ($id_afiliasi <> 0) { ?>
                                                    <span class="badge text-dark"><i class="fa-solid fa-arrow-right"></i> <?= $this->dToko[$id_afiliasi]['inisial'] ?></span>
                                                <?php } ?>
                                                <br>
                                                <small><?= ucwords($cs) ?> <?= substr($do['insertTime'], 2, -3) ?></small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <?php if ($id_afiliasi == 0 || $this->userData['id_toko'] == $id_toko) { ?>
                                                <?php if ($id_afiliasi <> 0) { ?>
                                                    <td class="text-sm px-1 pt-0 pb-1 text-end">
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
                                                                <i class="fa-solid fa-check-double"></i> <?= ucwords($cs_aff_ready) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-purple">
                                                            <?php if ($data['data_ref'][$ref]['ready_cs'] <> 0) {
                                                                $cs_ready = $data['karyawan'][$data['data_ref'][$ref]['ready_cs']]['nama']; ?>
                                                                <i class="fa-solid fa-check-double"></i> <?= ucwords($cs_ready) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-primary">
                                                            <?php if ($ambil_all[$ref] == true) {
                                                                $cs_ambil = $data['karyawan'][$do['id_ambil']]['nama']; ?>
                                                                <i class="fa-solid fa-circle-check"></i> <?= $cs_ambil ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-success">
                                                            <?php if ($lunas[$ref] == true) { ?>
                                                                <i class="fa-solid fa-circle-check"></i> Paid
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                    </td>
                                                <?php } else { ?>
                                                    <td class="text-sm px-1 pt-0 pb-1 text-end">
                                                        <span class="text-purple">
                                                            <?php if ($data['data_ref'][$ref]['ready_cs'] <> 0) {
                                                                $cs_ready = $data['karyawan'][$data['data_ref'][$ref]['ready_cs']]['nama']; ?>
                                                                <i class="fa-solid fa-check-double"></i> <?= ucwords($cs_ready) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-primary">
                                                            <?php if ($ambil_all[$ref] == true) {
                                                                $cs_ambil = $data['karyawan'][$do['id_ambil']]['nama']; ?>
                                                                <i class="fa-solid fa-circle-check"></i> <?= $cs_ambil ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-success">
                                                            <?php if ($lunas[$ref] == true) { ?>
                                                                <i class="fa-solid fa-circle-check"></i> Paid
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                    </td>
                                                <?php } ?>
                                                <?php } else {
                                                if ($id_afiliasi <> 0) { ?>
                                                    <td class="text-sm px-1 pt-0 pb-1 text-end">
                                                        <span class="text-primary">
                                                            <?php if ($cs_id_aff <> 0) {
                                                                $cs_aff = $data['karyawan'][$cs_id_aff]['nama']; ?>
                                                                <i class="fa-solid fa-check"></i> <?= ucwords($cs_aff) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-success">
                                                            <?php if ($data['data_ref'][$ref]['ready_aff_cs'] <> 0) {
                                                                $cs_aff_ready = $data['karyawan'][$data['data_ref'][$ref]['ready_aff_cs']]['nama']; ?>
                                                                <i class="fa-solid fa-circle-check"></i> <?= ucwords($cs_aff_ready) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                    </td>
                                            <?php }
                                            } ?>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="col px-0 overflow-auto mt-2" style="max-width: 500px;height: 700px;">
                    <?php foreach ($data['refs'] as $ref) { ?>
                        <?php
                        $no = 0;
                        $bill[$ref] = 0;
                        $lunas[$ref] = false;
                        $ambil_all[$ref] = true;
                        $id_afiliasi = 0;
                        $ada = false;

                        if (isset($data['mutasi'][$ref])) {
                            foreach ($data['mutasi'][$ref] as $do) {
                                $cancel = $do['stat'];
                                $id_toko = $do['id_sumber'];

                                $jumlah = ($do['harga_jual'] * $do['qty']) + $do['margin_paket'];
                                $diskon = $do['diskon'] * $do['qty'];
                                if ($cancel <> 2) {
                                    $no++;
                                    $bill[$ref] += $jumlah;
                                    $bill[$ref] -= $diskon;
                                }

                                if ($no == 1) {
                                    $ada = true;
                                    $id_pelanggan = $do['id_target'];
                                    $dateTime = substr($do['insertTime'], 0, 10);
                                    $pelanggan = $data['pelanggan'][$do['id_target']]['nama'];
                                    $cs = $data['karyawan'][$do['cs_id']]['nama']; ?>
                                <?php } ?>
                        <?php }
                        } ?>

                        <?php
                        if ($ada == true) {
                            $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
                            $in_toko = "";
                            if ($id_toko_pelanggan <> $this->userData['id_toko']) {
                                $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
                            }

                            $sisa[$ref] = $bill[$ref] - $verify_payment[$ref];
                            if ($sisa[$ref] <= 0) {
                                $lunas[$ref] = true;
                            } else {
                                $lunas[$ref] = false;
                            } ?>

                            <div class="row mx-0">
                                <div class="col px-1" style="min-width: 200px;">
                                    <table class="w-100 mb-1 target bg-white <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                        <tr data-id="<?= $id_pelanggan ?>" class="cekPLG" style="cursor: pointer;">
                                            <td class="p-1">
                                                <small><span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-nowrap text-primary fw-bold"><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?></small>
                                                <br>
                                                <small><?= ucwords($cs) ?> <?= substr($do['insertTime'], 2, -3) ?></small>
                                            </td>

                                            <?php if ($id_afiliasi == 0 || $this->userData['id_toko'] == $id_toko) { ?>
                                                <td class="text-end pe-1">
                                                    <small>
                                                        &nbsp;
                                                        <?php if ($ambil_all[$ref] == true) { ?>
                                                            <i class="fa-solid fa-circle-check text-primary"></i>
                                                        <?php } else { ?>
                                                            <i class="fa-regular fa-circle"></i>
                                                        <?php } ?>
                                                        <br>
                                                        &nbsp;
                                                        <?php if ($lunas[$ref] == true) { ?>
                                                            <i class="fa-solid fa-circle-check text-success"></i>
                                                        <?php } else { ?>
                                                            <i class="fa-regular fa-circle"></i>
                                                        <?php } ?>
                                                    </small>
                                                </td>
                                                <?php } else {
                                                if ($id_user_afiliasi <> 0) { ?>
                                                    <td class="text-end pe-1 text-success">
                                                        <small>
                                                            AF
                                                        </small>
                                                        <br>
                                                        &nbsp;
                                                    </td>
                                            <?php }
                                            } ?>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </small>
</main>
<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
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

    $("tr.cekPLG").click(function() {
        var id = $(this).attr("data-id");
        window.location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + id;
    });
</script>