<?php $modeView = $data['parse'] ?>
<?php $parse_2 = $data['parse_2'] ?>

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


                        if ($parse_2 == 100) {
                            echo "<pre>";
                            print_r($data['refs']);
                            echo "</pre>";
                            exit();
                        }

                        $no = 0;
                        $lunas[$ref] = false;
                        $ambil_all[$ref] = true;
                        $id_ambil_aff[$ref] = 0;
                        $ambil_all_aff[$ref] = true;
                        $id_aff[$ref] = [];
                        $countSPK = [];
                        $ada = false;
                        $id_toko[$ref] = 0;

                        if (isset($data['order'][$ref])) {
                            foreach ($data['order'][$ref] as $do) {
                                $cancel = $do['cancel'];
                                $id_ambil = $do['id_ambil'];
                                $id_user_afiliasi = $do['id_user_afiliasi'];

                                $divisi_arr = unserialize($do['spk_dvs']);
                                $countSPK = count($divisi_arr);

                                if ($do['id_afiliasi'] <> 0) {
                                    $id_aff[$ref][$do['id_afiliasi']]['cs'] = $do['id_user_afiliasi'];
                                    $id_aff[$ref][$do['id_afiliasi']]['cs_ready'] = $do['ready_aff_cs'];
                                    $id_ambil_aff[$ref] = $do['id_ambil_aff'];
                                    if ($id_ambil_aff[$ref] == 0) {
                                        $ambil_all_aff[$ref] = false;
                                    }
                                }

                                $id_toko[$ref] = $do['id_toko'];

                                $jumlah = ($do['harga'] * $do['jumlah']) + $do['margin_paket'];

                                if ($cancel == 0 && $do['stok'] == 0) {
                                    $no++;
                                    $bill[$ref] += $jumlah;
                                    $bill[$ref] -= $do['diskon'];
                                }

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
                                    $cs = $data['karyawan'][$do['id_penerima']]['nama']; ?>
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
                                        <tr>
                                            <td class="px-1 pb-1 pt-1">
                                                <a href="<?= PV::BASE_URL ?>Data_Operasi/index/<?= $id_pelanggan ?>" class="cekPLG text-decoration-none" style="cursor: pointer;">
                                                    <small><span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-nowrap text-primary fw-bold"><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?></small><br>
                                                    <small class="text-dark"><?= ucwords($cs) ?> <?= substr($do['insertTime'], 2, -3) ?></small>
                                                </a>
                                                <?php if ($this->userData['id_toko'] == $id_toko[$ref]) { ?>
                                                    <div class="float-end text-sm">
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
                                                                if (isset($data['karyawan'][$do['id_ambil']])) {
                                                                    $cs_ambil = $data['karyawan'][$do['id_ambil']]['nama'];
                                                                } else {
                                                                    $cs_ambil = "";
                                                                } ?>
                                                                <i class="fa-solid fa-circle-check"></i> <?= $cs_ambil ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-success">
                                                            <?php if ($lunas[$ref] == true) { ?>
                                                                <i class="fa-solid fa-circle-check"></i>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <?php if (count($id_aff[$ref]) > 0) { ?>
                                                <td class="text-sm px-1 pt-0 pb-1 text-end">
                                                    <?php foreach ($id_aff[$ref] as $key => $val) { ?>
                                                        <span class="badge fw-normal text-dark border"><?= $this->dToko[$key]['inisial'] ?></span>
                                                        &nbsp;
                                                        <span class="text-dark">
                                                            <?php if ($val['cs'] <> 0) {
                                                                $cs_aff = $data['karyawan'][$val['cs']]['nama']; ?>
                                                                <i class="fa-solid fa-check"></i> <?= ucwords($cs_aff) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-dark">
                                                            <?php if ($val['cs_ready'] <> 0) {
                                                                $cs_aff_ready = $data['karyawan'][$val['cs_ready']]['nama']; ?>
                                                                <i class="fa-solid fa-check-double"></i> <?= ucwords($cs_aff_ready) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        &nbsp;
                                                        <span class="text-dark">
                                                            <?php if ($ambil_all_aff[$ref] == true) {
                                                                $cs_ambil = $data['karyawan'][$id_ambil_aff[$ref]]['nama']; ?>
                                                                <i class="fa-regular fa-circle-check"></i> <?= ucwords($cs_ambil) ?>
                                                            <?php } else { ?>
                                                                <i class="fa-regular fa-circle"></i>
                                                            <?php } ?>
                                                        </span>
                                                        <br>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
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
                                        <tr>
                                            <td class="p-1">
                                                <a href="<?= PV::BASE_URL ?>Data_Operasi/index/<?= $id_pelanggan ?>" class="cekPLG text-decoration-none" style="cursor: pointer;">
                                                    <small><span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-nowrap text-primary fw-bold"><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></span> #<?= substr($id_pelanggan, -2) ?></small><br>
                                                    <small class="text-dark"><?= ucwords($cs) ?> <?= substr($do['insertTime'], 2, -3) ?></small>
                                                </a>
                                            </td>
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
</script>