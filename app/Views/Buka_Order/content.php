<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

switch ($id_pelanggan_jenis) {
    case 1:
        $pelanggan_jenis = "Umum";
        break;
    case 2:
        $pelanggan_jenis = "Rekanan";
        break;
    case 3:
        $pelanggan_jenis = "Online";
        break;
    default:
        $pelanggan_jenis = "Stok";
        break;
}

$total_order = 0;
$total_item = 0;
$paket = false;
$mgpaket = $data['margin_paket'];
?>

<main class="container">
    <!-- Main page content-->
    <div class="container px-2">
        <?php
        if (count($data['errorID']) > 0) {
            echo "<br><small class='text-danger'>Order Data Error! yang mungkin disebabkan oleh jaringan terputus atau pengaturan produk yang tidak valid:</small><br><hr class='my-1'>";
            foreach ($data['errorID'] as $k => $ei) { ?>
                - ID#<?= $ei['id'] ?> <?= $ei['produk'] ?> <button class="btn btn-sm btn-outline-danger delError border-0 shadow-sm py-1 mb-1 ms-1" data-id="<?= $ei['id'] ?>"><b>Hapus</b></button><br>
            <?php }
        } else {
            ?>
            <div class="row <?= (count($data['order']) == 0 && count($data['order_barang']) == 0) || isset($_SESSION['edit'][$this->userData['id_user']]) ? "d-none" : "" ?>">
                <div class="col px-2">
                    <form class="proses" action="<?= PV::BASE_URL ?>Buka_Order/proses/<?= $id_pelanggan_jenis ?>" method="POST">
                        <?php if ($id_pelanggan_jenis == 1 || $id_pelanggan_jenis == 2) { ?>
                            <div class="row mb-2">
                                <div class="col px-1" style="max-width: 300px;">
                                    <input name="new_customer" class="form-control form-control-sm" placeholder="New Customer">
                                </div>
                                <div class="col px-1" style="max-width: 300px;">
                                    <input name="hp" class="form-control form-control-sm" placeholder="Phone Number">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row pb-2">
                            <div class="col px-1" style="max-width: 300px;">
                                <select class="tize shadow-none" id="pelanggan" name="id_pelanggan">
                                    <option value="">Customer Name (<?= $pelanggan_jenis ?>)</option>
                                    <?php foreach ($data['pelanggan'] as $p) { ?>
                                        <option value="<?= $p['id_pelanggan'] ?>"><?= strtoupper($p['nama']) ?> #<?= substr($p['id_pelanggan'], 2) ?> | <?= $p['no_hp'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col px-1" style="max-width: 300px;">
                                <select class="tize shadow-none" name="id_karyawan" required>
                                    <option value="">CS Name</option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= strtoupper($k['nama']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-auto px-1 mt-auto p-0">
                                <button type="submit" class="btn shadow-none btn-success bg-gradient w-100">Proses</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            if (isset($_SESSION['edit'][$this->userData['id_user']])) {
                $dEdit = $_SESSION['edit'][$this->userData['id_user']]; ?>
                <div class="row mb-3 text-sm border py-2">
                    <div class="col-auto">
                        <label>Pelanggan</label><br>
                        <span class="fw-bold"><?= strtoupper($data['pelanggan'][$dEdit[3]]['nama']) ?></span>
                    </div>
                    <div class="col-auto text-end">
                        <label>Paid</label><br>
                        <span class="fw-bold" id="paid" data-val="<?= $dEdit[2] ?>"><?= number_format($dEdit[2]) ?></span>
                    </div>
                    <div class="col mt-auto">
                        <a class="submit" href="<?= PV::BASE_URL ?>Buka_Order/proses/<?= $dEdit[1] ?>/<?= $dEdit[3] ?>"><span class="btn btn-sm btn-secondary bg-gradient">Update</span></a>
                    </div>
                </div>
            <?php }
            ?>

            <div class="row mb-2">
                <div class="col px-0 text-end">
                    <?php if ($data['count'] <= 30) {
                        if ($id_pelanggan_jenis <> 100) { ?>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-danger bg-gradient py-1" data-bs-target="#exampleModalPaket" data-bs-toggle="modal">(&#43;) Paket</button>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-primary bg-gradient py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">(&#43;) Produksi</button>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-dark bg-gradient py-1" data-bs-target="#exampleModalJasa" data-bs-toggle="modal">(&#43;) Jasa</button>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-success bg-gradient py-1" data-bs-target="#exampleModalB" data-bs-toggle="modal">(&#43;) Barang</button>
                            <div class="btn-group me-1">
                                <button type="button" class="btn shadow-none btn-sm btn-warning bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    (&#43;) Afiliasi
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-start mt-2 p-0">
                                    <?php foreach ($this->dToko as $dt) {
                                        if ($dt['id_toko'] <> $this->userData['id_toko']) { ?>
                                            <li><a data-bs-toggle="modal" data-bs-target="#exampleModalAff" class="dropdown-item aff" data-id="<?= $dt['id_toko'] ?>" href="#"><?= $dt['nama_toko'] ?></a></li>
                                    <?php }
                                    } ?>
                                </ul>
                            </div>
                        <?php } else { ?>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-primary bg-gradient py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">(&#43;) Produksi</button>
                            <div class="btn-group me-1">
                                <button type="button" class="btn shadow-none btn-sm btn-warning bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    (&#43;) Afiliasi
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-start mt-2 p-0">
                                    <?php foreach ($this->dToko as $dt) {
                                        if ($dt['id_toko'] <> $this->userData['id_toko']) { ?>
                                            <li><a data-bs-toggle="modal" data-bs-target="#exampleModalAff" class="dropdown-item aff" data-id="<?= $dt['id_toko'] ?>" href="#"><?= $dt['nama_toko'] ?></a></li>
                                    <?php }
                                    } ?>
                                </ul>
                            </div>
                    <?php }
                    } ?>
                </div>
            </div>
            <?php if (count($data['order']) > 0) { ?>
                <div class="row">
                    <div class="col border-start border-end border-top px-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php
                                $no = 0;
                                foreach ($data['order'] as $keyD => $do) {
                                    $no++;
                                    $total_item += 1;
                                    $akum_diskon = 0;

                                    $id_order_data = $do['id_order_data'];
                                    $id_produk = $do['id_produk'];
                                    $detail_arr = unserialize($do['produk_detail']);
                                    $detail = "";
                                    $listDetail = unserialize($do['detail_harga']);

                                    foreach ($detail_arr as $da) {
                                        $detail .= $da['detail_name'] . ", ";
                                    }

                                    $produk = $do['produk'];

                                    $detail_harga = unserialize($do['detail_harga']);

                                    $harga_ok = true;
                                    $btnSetHarga = 'Uninitialized';

                                    foreach ($listDetail as $kl => $ld_o) {
                                        $disk = $ld_o['d'];
                                        $akum_diskon += $disk ?>
                                    <?php }

                                    ?>
                                    <tr>
                                        <td class="">
                                            <table class="table table-sm w-100 mb-0">
                                                <tr class="<?= $do['id_afiliasi'] == 0 ? 'bg-primary' : 'bg-warning' ?> bg-gradient bg-opacity-10">
                                                    <td class="ps-2 align-middle">
                                                        <?php if ($do['paket_ref'] <> "") { ?>
                                                            <span class="badge bg-danger"><?= $data['paket'][$do['paket_ref']]['nama'] ?> <?= $do['price_locker'] == 1 ? '<i class="fa-solid fa-key"></i>' : '' ?></span>
                                                        <?php } ?>

                                                        <span class="text-nowrap text-dark"><small class="text-secondary">#<?= $id_order_data ?></small><b><small> <?= ucwords($produk) ?></small></b></span>
                                                        <?php if ($do['paket_ref'] <> "") { ?>
                                                            <div class="btn-group me-1 d-none">
                                                                <button type="button" class="btn shadow-none btn-sm btn-warning bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    (&#43;) Afiliasi
                                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-start mt-2 p-0">
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalAff" class="dropdown-item aff" data-id="<?= $dt['id_toko'] ?>" href="#"><?= $dt['nama_toko'] ?></a></li>
                                                                </ul>
                                                            </div>
                                                        <?php } ?>

                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small>
                                                            <span class="edit_n" data-id="<?= $do['id_order_data'] ?>"><?= $do['jumlah'] ?></span>x
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small><?php
                                                                if ($harga_ok == false) {
                                                                    echo $btnSetHarga;
                                                                } else {
                                                                    if ($do['price_locker'] == 0) {
                                                                        if ($akum_diskon > 0) {
                                                                            echo "<del>" . number_format($do['harga']) . "</del> <small>@" . number_format($do['harga'] - $akum_diskon);
                                                                        } else {
                                                                            echo "@" . number_format($do['harga']);
                                                                        }
                                                                    }
                                                                } ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <b>
                                                            <small>
                                                                <?php
                                                                if ($harga_ok == false) {
                                                                    echo $btnSetHarga;
                                                                } else {
                                                                    if ($do['price_locker'] == 0) {
                                                                        if ($akum_diskon > 0) {
                                                                            echo "<del>" . number_format($do['harga'] * $do['jumlah']) . "</del> " . number_format(($do['harga'] * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                                                                        } else {
                                                                            echo number_format($do['harga'] * $do['jumlah']);
                                                                        }
                                                                        $total_order += ($do['harga'] * $do['jumlah']);
                                                                    } else {
                                                                        echo number_format(($do['harga'] * $do['jumlah']) + $mgpaket[$do['paket_ref']]['margin_paket']);
                                                                        $total_order += (($do['harga'] * $do['jumlah']) + $mgpaket[$do['paket_ref']]['margin_paket']);
                                                                    }
                                                                } ?>
                                                            </small>
                                                        </b>
                                                    </td>
                                                    <td class="align-middle" style="width: 30px;"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                                                </tr>
                                                <?php if ($id_pelanggan_jenis == 100) { ?>
                                                    <tr>
                                                        <?php $code = str_replace(['-', '&', '#'], '', $do['produk_code']); ?>
                                                        <td colspan="10"><b><span data-bs-toggle="modal" data-code="<?= $do['produk_code'] ?>" data-bs-target="#exampleModalPC" style="cursor: pointer;" class="tetapkanNama px-2">N</span></b> <span class="text-danger fw-bold"><?= isset($data['barang'][$code]) ? $data['barang'][$code]['product_name'] : "" ?></span></td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td colspan="10" class="border-bottom-0">
                                                        <table class="table table-sm table-borderless mb-1">
                                                            <tr>
                                                                <td class="pe-1 border-bottom-0" nowrap>
                                                                    <div class="row">
                                                                        <?php
                                                                        foreach ($detail_arr as $da) { ?>
                                                                            <div class="col-auto" style="line-height: 80%;">
                                                                                <small>
                                                                                    <small><u><?= $da['group_name'] ?></u></small><br> <?= strtoupper($da['detail_name']) ?>
                                                                                </small>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="10" valign="top" class="p-0 border border-top-0">
                                                                    <small>
                                                                        <?php
                                                                        foreach ($listDetail as $kl => $ld_o) {
                                                                            $harga_d = $data['harga'][$keyD][$ld_o['c_h']]; ?>
                                                                            <div class="border-bottom mx-0">
                                                                                <div class="ps-1 float-start"><?= strtoupper($ld_o['n_v']) ?></div>
                                                                                <div class="float-end">

                                                                                    <?php
                                                                                    if ($do['price_locker'] == 0) {
                                                                                        if ($disk > 0) { ?>
                                                                                            <del><?= number_format($data['harga'][$keyD][$ld_o['c_h']]) ?></del>
                                                                                        <?php } ?>
                                                                                        <?= number_format($data['harga'][$keyD][$ld_o['c_h']] - $disk) ?>
                                                                                    <?php } ?>

                                                                                    <b><span data-bs-toggle="modal" data-code="<?= $ld_o['c_h'] ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#exampleModal1" style="cursor: pointer;" class="tetapkanHarga px-2">P</span></b>
                                                                                    <?php if ($harga_d > 0 && in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                                                                        <b><span data-bs-toggle="modal" data-parse="<?= $id_order_data . "_" . $kl . "_" . $harga_d ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#modalDiskon" style="cursor: pointer;" class="tetapkanDiskon px-2">D</span></b>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <br>
                                                                        <?php }
                                                                        ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <div class="row">
                                                            <div class="col">
                                                                <small>Catatan <span class="fw-bold">Utama</span></small><br>
                                                                <span class="text-danger cell_edit" data-mode="main" data-id="<?= $id_order_data ?>" data-col=""><?= $do['note'] ? $do['note'] : "_" ?></span>
                                                            </div>
                                                            <?php
                                                            foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                                if (strlen($ns) > 0) { ?>
                                                                    <div class="col">
                                                                        <small>Catatan <span class="fw-bold"><?= $this->model('Arr')->get($this->dDvs, "id_divisi", "divisi", $ks) ?></span></small><br>
                                                                        <span data-id="<?= $id_order_data ?>" data-col="<?= $ks ?>" data-mode="<?= $ks ?>" class="cell_edit text-primary"><?= $ns ?></span>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <div class="col">
                                                                        <small>Catatan <span class="fw-bold"><?= $this->model('Arr')->get($this->dDvs, "id_divisi", "divisi", $ks) ?></span></small><br>
                                                                        <span data-id="<?= $id_order_data ?>" data-col="<?= $ks ?>" data-mode="<?= $ks ?>" class="cell_edit text-primary">_</span>
                                                                    </div>
                                                            <?php }
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php }
        } ?>

        <div class="row mt-2">
            <div class="col border border-bottom-0 px-0">
                <table class="table table-sm m-0 text-sm">
                    <?php foreach ($data['order_barang'] as $db) {
                        $total_item += 1;
                        $dp = $data['barang'][$db['kode_barang']];

                        if ($db['price_locker'] == 1) {
                            $classKeyPrice = 'text-danger';
                            $total_order += (($dp['harga'] * $db['jumlah']) + $mgpaket[$db['paket_ref']]['margin_paket']);
                            $totalnya = ($dp['harga_' . $id_pelanggan_jenis] * $db['qty']) + $mgpaket[$db['paket_ref']]['margin_paket'];
                        } else {
                            $total_order += ($dp['harga_' . $id_pelanggan_jenis] * $db['qty']);
                            $totalnya = ($dp['harga_' . $id_pelanggan_jenis] * $db['qty']);
                        }

                    ?>
                        <tr>
                            <td class="text-secondary text-end ps-2">
                                <span class="badge bg-danger"><?= $db['paket_ref'] <> "" ? $data['paket'][$db['paket_ref']]['nama'] : "" ?></span>
                                #<?= $db['id'] ?><br><?= $db['sds'] == 1 ? "<span class='text-danger'>S</span>" : "" ?>
                            </td>
                            <td><?= trim($dp['brand'] . " " . $dp['model'])  ?><br><?= $db['sn'] ?></td>
                            <td class="text-end"><?= number_format($db['qty']) ?>x<br><?= $db['price_locker'] == 0 ? "@" . number_format($dp['harga_' . $id_pelanggan_jenis]) : "" ?></td>
                            <td class="text-end pe-2"><?= number_format($totalnya) ?></td>
                            <td class="pt-2" style="width: 30px;"><a class="deleteItemBarang" data-id="<?= $db['id'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col text-end border">
                <?= $total_item ?> Items
            </div>
            <div class="col text-end border">
                <span class="fw-bold" id="total_order" data-val="<?= $total_order ?>">Total Rp<?= number_format($total_order) ?></span>
            </div>
        </div>
    </div>
</main>

<?php require_once('form.php') ?>

<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('.tize').selectize();
    });

    $('select[name=id_pelanggan]').change(function() {
        $('input[name=new_customer]').val("");
        $('input[name=hp]').val("");
    })

    $('input[name=new_customer]').keypress(function() {
        $("#pelanggan")[0].selectize.clear();
    })

    $("button.delError").click(function() {
        var id_ = $(this).attr('data-id');
        $.post("<?= PV::BASE_URL ?>Buka_Order/delete_error", {
                id: id_
            },
            function() {
                location.reload(true);
            });
    })

    $('select.loadDetail').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
        }
    });


    $('select.loadDetail_aff').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_aff").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
        }
    });

    $('select.loadDetail_Jasa').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_Jasa").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
        }
    });


    $('select.loadDetail_Barang').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_barang").load('<?= PV::BASE_URL ?>Buka_Order/load_detail_barang/' + produk + '/<?= $id_pelanggan_jenis ?>');
        }
    });

    $("span.tetapkanHarga").click(function() {
        var produk = $(this).attr("data-produk");
        var harga_code = $(this).attr("data-code");
        $("span.produk_harga").html(produk);
        $("input[name=harga_code").val(harga_code);
    })

    $("span.tetapkanNama").click(function() {
        var product_code = $(this).attr("data-code");
        $("input[name=product_code").val(product_code);
    })

    $("span.tetapkanDiskon").click(function() {
        var produk = $(this).attr("data-produk");
        var parse = $(this).attr("data-parse");
        $("span.produk_harga").html(produk);
        $("input[name=parse").val(parse);
    })

    $("a.aff").click(function() {
        $('input#aff_target').val($(this).attr("data-id"));
    })

    $("a.deleteItem").click(function() {
        var id = $(this).attr("data-id_order");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Buka_Order/deleteOrder",
            data: {
                id_order: id
            },
            type: "POST",
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    })

    $("a.deleteItemBarang").click(function() {
        var id = $(this).attr("data-id");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Buka_Order/deleteOrderBarang",
            data: {
                id: id
            },
            type: "POST",
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    })

    $("form.ajax").on("submit", function(e) {
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

    $("form.proses").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (isNumeric(res) == false) {
                    alert(res);
                } else {
                    location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + res;
                }
            }
        });
    });

    $('a.submit').on('click', function(e) {
        e.preventDefault();

        var paid = parseInt($("#paid").attr('data-val'));
        var total_o = parseInt($("#total_order").attr('data-val'));

        if (total_o < paid) {
            alert("Jumlah pembayaran lebih besar dari order, lakukan pembatalan bayar terlebih dahulu");
            return;
        }

        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            data: {},
            cache: false,
            success: function(res) {
                if (isNumeric(res) == false) {
                    $('body').html(res);
                } else {
                    location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + res;
                }
            }
        });
    });

    function isNumeric(str) {
        if (typeof str != "string") return false
        return !isNaN(str) &&
            !isNaN(parseFloat(str))
    }

    var click = 0;
    $("span.edit_n").on('dblclick', function() {
        var value = $(this).html();

        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var value_before = value;
        var span = $(this);
        span.html("<input type='number' id='value_3313' style='text-align:center;width:70px' value=" + value + ">");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after === value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Buka_Order/updateCell_N',
                    data: {
                        'id': id,
                        'value': value_after,
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            content();
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });

    $(".cell_edit").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var tipe = $(this).attr('data-tipe');
        var value = $(this).html();
        var mode = $(this).attr('data-mode');
        var col = $(this).attr('data-col');
        var default_val = value;
        if (value == "_") {
            value = "";
        }
        var value_before = value;
        var el = $(this);
        var width = el.parent().width();
        if (tipe == "number") {
            align = "right";
        } else {
            align = "left";
        }

        el.parent().css("width", width);
        el.html("<input required type=" + tipe + " style='outline:none;border:none;width:" + width + "px;text-align:" + align + "' id='value_' value='" + value + "'>");

        $("#value_").focus();
        $("#value_").focusout(function() {
            var value_after = $(this).val();
            if (value_after === value_before) {
                el.html(default_val);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Buka_Order/update_catatan',
                    data: {
                        'id': id,
                        'mode': mode,
                        'col': col,
                        'value': value_after,
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        if (res == 1) {
                            if (value_after === "") {
                                value_after = "_";
                            }
                            el.html(value_after);
                            click = 0;
                        } else {
                            alert(res);
                            el.html(default_val);
                        }
                    },
                });
            }
        });
    });
</script>