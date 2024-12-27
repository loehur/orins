<?php $modeView = $data['parse'] ?>
<?php $target_jenis = $data['parse_2'] ?>
<?php $arr_tuntas = [] ?>

<style>
    tr:hover {
        background-color: ghostwhite;
    }
</style>
<main>
    <div class="position-fixed bg-white w-100" style="top:0; padding-top:70px;">
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
        $dibayar[$ref] = 0;
        $verify_payment[$ref] = 0;
    }

    foreach ($data['kas'] as $ref => $sd) {
        foreach ($sd as $dk) {
            $dibayar[$ref] += $dk['jumlah'];
            if ($dk['metode_mutasi'] == 1 && $dk['status_setoran'] == 1) {
                $verify_payment[$ref] += $dk['jumlah'];
            }
            if (($dk['metode_mutasi'] == 2 || $dk['metode_mutasi'] == 3 || $dk['metode_mutasi'] == 4) && $dk['status_mutasi'] == 1) {
                $verify_payment[$ref] += $dk['jumlah'];
            }
        }
    }

    foreach ($data['diskon'] as $ref => $sd) {
        foreach ($sd as $dk) {
            if ($dk['cancel'] == 0) {
                $dibayar[$ref] += $dk['jumlah'];
                $verify_payment[$ref] += $dk['jumlah'];
            }
        }
    }
    ?>

    <small>
        <div class="mx-2 rounded px-2 mt-3 pt-5">
            <div class="row">
                <div class="col px-0 overflow-auto mt-2" style="max-width: 500px;height: 700px;">
                    <?php foreach ($data['refs'] as $ref) { ?>

                        <?php
                        $no = 0;
                        $bill[$ref] = 0;
                        $lunas[$ref] = false;
                        $ambil_all[$ref] = true;
                        $id_afiliasi = 0;
                        $ada = false;

                        if (isset($data['order'][$ref])) {
                            foreach ($data['order'][$ref] as $do) {
                                $no++;
                                $cancel = $do['cancel'];
                                $id_ambil = $do['id_ambil'];
                                $id_user_afiliasi = $do['id_user_afiliasi'];
                                $id_afiliasi = $do['id_afiliasi'];
                                $id_toko = $do['id_toko'];

                                $jumlah = ($do['harga'] * $do['jumlah']) + $do['margin_paket'];
                                if ($cancel == 0) {
                                    $bill[$ref] += $jumlah;
                                }

                                if ($this->userData['id_toko'] <> $do['id_toko'] && $do['id_afiliasi'] <> 0 && $id_user_afiliasi == 0) {
                                    break;
                                }

                                $bill[$ref] -= $do['diskon'];
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
                                    $cs = $data['karyawan'][$do['id_penerima']]['nama']; ?>
                                <?php } ?>
                        <?php }
                        } ?>

                        <?php
                        if (isset($data['mutasi'][$ref])) {
                            foreach ($data['mutasi'][$ref] as $do) {
                                $no++;
                                $cancel = $do['stat'];
                                $id_toko = $do['id_sumber'];

                                $jumlah = ($do['harga_jual'] * $do['qty']) + $do['margin_paket'];
                                if ($cancel == 1) {
                                    $bill[$ref] += $jumlah;
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

                            $sisa[$ref] = $bill[$ref] - $dibayar[$ref];
                            if ($sisa[$ref] <= 0) {
                                $lunas[$ref] = true;
                            } else {
                                $lunas[$ref] = false;
                            }
                            if ($verify_payment[$ref] >= $bill[$ref] && $ambil_all[$ref] == true) {
                                array_push($arr_tuntas, $ref);
                            } ?>

                            <div class="row mx-0">
                                <div class="col px-1" style="min-width: 200px;">
                                    <table class="w-100 mb-1 target bg-white <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                        <tr data-id="<?= $id_pelanggan ?>" class="cekPLG" style="cursor: pointer;">
                                            <td class="p-1">
                                                <small><span class="text-danger"><?= substr($ref, -4) ?></span> <span class="text-purple text-nowrap"><b><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></b></span> #<?= substr($id_pelanggan, -2) ?></small>
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
                                                if ($id_user_afiliasi <> 0) {
                                                ?>
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
    $(document).ready(function() {
        clearTuntas();
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

    $("tr.cekPLG").click(function() {
        var id = $(this).attr("data-id");
        window.location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + id;
    });

    function clearTuntas() {
        var dataNya = '<?= serialize($arr_tuntas) ?>';
        var countArr = <?= count($arr_tuntas) ?>;

        if (countArr > 0) {
            $.ajax({
                url: '<?= PV::BASE_URL ?>Data_Operasi/clearTuntas',
                data: {
                    'data': dataNya,
                },
                type: 'POST',
                success: function() {
                    content();
                }
            });
        }
    }
</script>