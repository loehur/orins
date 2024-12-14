<?php $modeView = $data['parse'] ?>
<style>
    tr:hover {
        background-color: ghostwhite;
    }
</style>
<main>
    <div class="position-fixed bg-white w-100" style="top:0; padding-top:70px;">
        <div class="p-2 rounded bg-light ms-2 mb-2 me-1 border pb-0" style="max-width: 600px;">
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
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/0" type="button" class="btn btn-sm btn-<?= $outline ?>primary">
                                    Terkini
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                            <div class="ps-0 pe-1">
                                <?php $outline = ($modeView == 1) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/1" type="button" class="btn btn-sm btn-<?= $outline ?>success">
                                    >1 Minggu
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                            <div class="ps-0 pe-1">
                                <?php $outline = ($modeView == 2) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/2" type="button" class="btn btn-sm btn-<?= $outline ?>info">
                                    >1 Bulan
                                </a>
                                <?php $outline = "outline-" ?>
                            </div>
                            <div class="ps-0 pe-1">
                                <?php $outline = ($modeView == 3) ? "" : "outline-" ?>
                                <a href="<?= PV::BASE_URL ?>Data_Order/index/3" type="button" class="btn btn-sm btn-<?= $outline ?>secondary">
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
    <!-- Main page content-->
    <small>
        <div class="ms-2 rounded pb-2 me-1 border mt-5 pt-5">
            <div class="row">
                <div class="col px-0">
                    <div class="row row-cols-1 mx-2 mt-2">
                        <?php foreach ($data['order'] as $ref => $do_) { ?>
                            <?php
                            $no = 0;
                            $lunas = false;
                            $dibayar = 0;
                            $ambil_all = true;
                            foreach ($data['kas'] as $dk) {
                                if ($dk['ref_transaksi'] == $ref && $dk['status_mutasi'] == 1) {
                                    $dibayar += $dk['jumlah'];
                                }
                            }
                            $bill = 0;

                            foreach ($do_ as $do) {
                                $no++;
                                $dateTime = substr($do['insertTime'], 0, 10);
                                $today = date("Y-m-d");
                                $cancel = $do['cancel'];
                                $id_ambil = $do['id_ambil'];
                                $id_user_afiliasi = $do['id_user_afiliasi'];

                                if ($this->userData['id_toko'] <> $do['id_toko'] && $do['id_afiliasi'] <> 0 && $id_user_afiliasi == 0) {
                                    break;
                                }

                                $jumlah = ($do['harga'] * $do['jumlah']) + $do['margin_paket'];
                                if ($cancel == 0) {
                                    $bill += $jumlah;
                                }

                                $divisi_arr = unserialize($do['spk_dvs']);
                                $countSPK = count($divisi_arr);

                                if ($id_ambil == 0) {
                                    if ($countSPK > 0 && $cancel == 0) {
                                        $ambil_all = false;
                                    }
                                }

                                if ($no == 1) {
                                    foreach ($data['pelanggan'] as $dp) {
                                        if ($dp['id_pelanggan'] == $do['id_pelanggan']) {
                                            $pelanggan = $dp['nama'];
                                        }
                                    }

                                    foreach ($data['karyawan'] as $dp) {
                                        if ($dp['id_karyawan'] == $do['id_penerima']) {
                                            $cs = $dp['nama'];
                                        }
                                    }
                            ?>
                                    <div class="col px-1">
                                        <table class="w-100 mb-1 target bg-white <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                            <tr data-id="<?= $do['id_pelanggan'] ?>" class="cekPLG" style="cursor: pointer;">
                                                <td class="p-1">
                                                    <span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-primary"><b><?= strtoupper($pelanggan) ?></b></span> #<?= substr($do['id_pelanggan'], 2) ?>
                                                    <br>
                                                    <small><?= ucwords($cs) ?> <?= substr($do['insertTime'], 2, -3) ?></small>
                                                </td>
                                            <?php }
                                            ?>
                                        <?php }
                                    $sisa = $bill - $dibayar;
                                    if ($sisa <= 0) {
                                        $lunas = true;
                                    }
                                        ?>
                                        <?php if ($do['id_afiliasi'] == 0 || $this->userData['id_toko'] == $do['id_toko']) { ?>
                                            <td class="text-end pe-1">
                                                <small>
                                                    Ambil
                                                    <?php if ($ambil_all == true) { ?>
                                                        <i class="fa-solid fa-circle-check text-purple"></i>
                                                    <?php } else { ?>
                                                        <i class="fa-regular fa-circle"></i>
                                                    <?php } ?>
                                                    <br>
                                                    Lunas
                                                    <?php if ($lunas == true) { ?>
                                                        <i class="fa-solid fa-circle-check text-success"></i>
                                                    <?php } else { ?>
                                                        <i class="fa-regular fa-circle"></i>
                                                    <?php } ?>
                                                </small>
                                            </td>
                                            <?php } else {
                                            if ($id_user_afiliasi <> 0) {
                                            ?>
                                                <td class="text-end pe-1 text-success">
                                                    <small>
                                                        Afiliated Order
                                                    </small>
                                                </td>
                                        <?php }
                                        } ?>
                                            </tr>
                                        </table>
                                    </div>
                                <?php
                            } ?>
                    </div>
                </div>
                <div class="col px-0">
                    <div class="row row-cols-1 mx-2 mt-2">
                        <?php foreach ($data['mutasi'] as $ref => $do_) { ?>
                            <?php
                            $no = 0;
                            $lunas = false;
                            $dibayar = 0;
                            $ambil_all = true;
                            foreach ($data['kas'] as $dk) {
                                if ($dk['ref_transaksi'] == $ref && $dk['status_mutasi'] == 1) {
                                    $dibayar += $dk['jumlah'];
                                }
                            }
                            $bill = 0;

                            foreach ($do_ as $do) {
                                $no++;
                                $dateTime = substr($do['insertTime'], 0, 10);
                                $today = date("Y-m-d");
                                $cancel = $do['stat'];

                                $jumlah = ($do['harga_jual'] * $do['qty']) + $do['margin_paket'];
                                if ($cancel == 1) {
                                    $bill += $jumlah;
                                }

                                if ($no == 1) {
                                    foreach ($data['pelanggan'] as $dp) {
                                        if ($dp['id_pelanggan'] == $do['id_target']) {
                                            $pelanggan = $dp['nama'];
                                        }
                                    }

                                    foreach ($data['karyawan'] as $dp) {
                                        if ($dp['id_karyawan'] == $do['cs_id']) {
                                            $cs = $dp['nama'];
                                        }
                                    }
                            ?>
                                    <div class="col px-1">
                                        <table class="w-100 mb-1 target bg-white <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                            <tr data-id="<?= $do['id_target'] ?>" class="cekPLG" style="cursor: pointer;">
                                                <td class="p-1">
                                                    <span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-primary"><b><?= strtoupper($pelanggan) ?></b></span> #<?= substr($do['id_target'], 2) ?>
                                                    <br>
                                                    <small><?= ucwords($cs) ?> <?= substr($do['insertTime'], 2, -3) ?></small>
                                                </td>
                                            <?php }
                                            ?>
                                        <?php }
                                    $sisa = $bill - $dibayar;
                                    if ($sisa <= 0) {
                                        $lunas = true;
                                    }
                                        ?>
                                        <td class="text-end pe-1">
                                            <small>
                                                Lunas
                                                <?php if ($lunas == true) { ?>
                                                    <i class="fa-solid fa-circle-check text-success"></i>
                                                <?php } else { ?>
                                                    <i class="fa-regular fa-circle"></i>
                                                <?php } ?>
                                            </small>
                                        </td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php
                            } ?>
                    </div>
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