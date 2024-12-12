<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

switch ($id_pelanggan_jenis) {
    case 1:
        $pelanggan_jenis = "Umum";
        break;
    case 2:
        $pelanggan_jenis = "R/D";
        break;
    default:
        $pelanggan_jenis = "Online";
        break;
}

$total_order = 0;
$total_item = 0;
?>

<main class="container">
    <!-- Main page content-->
    <div class="container px-2">
        <div class="row mb-2">
            <div class="col px-0">
                <select name="paket" class="tize border-0" required id="paket">
                    <option value=""></option>
                    <?php foreach ($data['main'] as $k => $a) { ?>
                        <option value="<?= $k ?>" <?= $data['ref'] == $k ? 'selected' : '' ?>><?= $a['nama'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <?php
        if (count($data['errorID']) > 0) {
            echo "<br><small class='text-danger'>Order Data Error! yang mungkin disebabkan oleh jaringan terputus atau pengaturan produk yang tidak valid:</small><br><hr class='my-1'>";
            foreach ($data['errorID'] as $k => $ei) { ?>
                - ID#<?= $ei['id'] ?> <?= $ei['produk'] ?> <button class="btn btn-sm btn-outline-danger delError border-0 shadow-sm py-1 mb-1 ms-1" data-id="<?= $ei['id'] ?>"><b>Hapus</b></button><br>
            <?php }
        } else {
            $nama_paket = isset($data['main'][$data['ref']]['nama']) ? $data['main'][$data['ref']]['nama'] : "";
            $harga_paket = isset($data['main'][$data['ref']]['harga_' . $id_pelanggan_jenis]) ? $data['main'][$data['ref']]['harga_' . $id_pelanggan_jenis] : "";
            ?>
            <div class="row <?= count($data['order']) == 0 ? "d-none" : "" ?>">
                <div class="col px-2">
                    <form action="<?= PV::BASE_URL ?>Paket/save/<?= $id_pelanggan_jenis ?>/<?= $data['ref'] ?>" method="POST">
                        <div class="row pb-2">
                            <div class="col px-1" style="max-width: 300px;">
                                <input class="form-control form-control-sm" value="<?= $nama_paket ?>" required name="paket" placeholder="Nama Paket">
                            </div>
                            <div class="col px-1" style="max-width: 300px;">
                                <input type="number" required class="form-control form-control-sm text-end" value="<?= $harga_paket ?>" name="harga_paket" placeholder="Harga">
                            </div>
                            <div class="col-auto px-1 mt-auto p-0">
                                <button type="submit" class="btn btn-sm shadow-none btn-danger bg-gradient w-100">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col pe-0">
                    <?php if ($data['count'] <= 15) { ?>
                        <button type="button" class="btn me-2 shadow-none btn-sm btn-primary bg-gradient py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">(&#43;) Produksi</button>
                        <button type="button" class="btn me-2 shadow-none btn-sm btn-dark bg-gradient py-1" data-bs-target="#exampleModalJasa" data-bs-toggle="modal">(&#43;) Jasa</button>
                        <button type="button" class="btn me-2 shadow-none btn-sm btn-success bg-gradient py-1" data-bs-target="#exampleModalB" data-bs-toggle="modal">(&#43;) Barang</button>
                        <div class="btn-group me-1">
                            <button type="button" class="btn shadow-none btn-sm btn-warning bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                (&#43;) Produksi Afiliasi
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
                    <?php } ?>
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
                                    $akum_diskon = 0;
                                    $total_item += 1;
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

                                    $classKeyPrice = 'text-secondary';
                                    if ($do['price_locker'] == 1) {
                                        $classKeyPrice = 'text-danger';
                                    }
                                    ?>
                                    <tr>
                                        <td class="">
                                            <table class="table table-sm w-100 mb-0">
                                                <tr class="<?= $do['id_afiliasi'] == 0 ? 'bg-primary' : 'bg-warning' ?> bg-gradient bg-opacity-10">
                                                    <td class="ps-2"><span class="text-nowrap"><span data-id="<?= $id_order_data ?>" data-primary="id_order_data" data-tb="paket_order" data-ref="<?= $data['ref'] ?>" class="price_key <?= $classKeyPrice ?>" style="cursor: pointer;"><i class="fa-solid fa-key"></i></span> <small class="text-secondary">#<?= $id_order_data ?></small><b><small> <?= ucwords($produk) ?></small></b></span></td>
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
                                                                            echo number_format($do['harga']);
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
                                                                        echo number_format(($do['harga'] * $do['jumlah']) + $do['margin_paket']);
                                                                        $total_order += (($do['harga'] * $do['jumlah']) + $do['margin_paket']);
                                                                    }
                                                                } ?>
                                                            </small>
                                                        </b>
                                                    </td>
                                                    <td class="align-middle" style="width: 30px;"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                                                </tr>
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
                                                                                    <?php }  ?>

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
                <table class="table table-sm m-0">
                    <?php foreach ($data['order_barang'] as $db) {
                        $classKeyPrice = 'text-secondary';
                        $total_item += 1;
                        $dp = $data['barang'][$db['kode_barang']];

                        if ($db['price_locker'] == 1) {
                            $classKeyPrice = 'text-danger';
                            $total_order += (($dp['harga_' . $id_pelanggan_jenis] * $db['qty']) + $db['margin_paket']);
                            $totalnya = ($dp['harga_' . $id_pelanggan_jenis] * $db['qty']) + $do['margin_paket'];
                        } else {
                            $total_order += ($dp['harga_' . $id_pelanggan_jenis] * $db['qty']);
                            $totalnya = ($dp['harga_' . $id_pelanggan_jenis] * $db['qty']);
                        } ?>
                        <tr>
                            <td class="text-secondary text-end ps-2"><span data-id="<?= $db['id'] ?>" data-primary="id" data-tb="paket_mutasi" data-ref="<?= $data['ref'] ?>" class="price_key <?= $classKeyPrice ?>" style="cursor: pointer;"><i class="fa-solid fa-key"></i></span> #<?= $db['id'] ?><br><?= $db['sds'] == 1 ? "<span class='text-danger fw-bold'>S</span>" : "" ?></td>
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
                <?= number_format($total_item) ?> Items
            </div>
            <div class="col text-end border fw-bold">
                Total <?= number_format($total_order) ?>
            </div>
        </div>
    </div>
</main>

<?php require_once('form.php') ?>

<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $("button.delError").click(function() {
        var id_ = $(this).attr('data-id');
        $.post("<?= PV::BASE_URL ?>Paket/delete_error", {
                id: id_
            },
            function() {
                location.reload(true);
            });
    })

    $('select.loadDetail').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail").load('<?= PV::BASE_URL ?>Paket/load_detail/' + produk);
        }
    });

    $('.price_key').click(function() {
        var tb_ = $(this).attr('data-tb');
        var id_ = $(this).attr('data-id');
        var ref_ = $(this).attr('data-ref');
        var primary_ = $(this).attr('data-primary');
        $.post("<?= PV::BASE_URL ?>Paket/price_key", {
                id: id_,
                tb: tb_,
                ref: ref_,
                primary: primary_
            },
            function(res) {
                if (res == 0) {
                    content(<?= $id_pelanggan_jenis ?>, ref_);
                } else {
                    alert(res);
                }
            });
    })

    $('select.loadDetail_Jasa').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_Jasa").load('<?= PV::BASE_URL ?>Paket/load_detail/' + produk);
        }
    });


    $('select.loadDetail_Barang').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_barang").load("<?= PV::BASE_URL ?>Paket/load_detail_barang/" + produk + "/<?= $id_pelanggan_jenis ?>/<?= $data['ref'] ?>");
        }
    });

    $("span.tetapkanHarga").click(function() {
        var produk = $(this).attr("data-produk");
        var harga_code = $(this).attr("data-code");
        $("span.produk_harga").html(produk);
        $("input[name=harga_code").val(harga_code);
    })

    $("span.tetapkanDiskon").click(function() {
        var produk = $(this).attr("data-produk");
        var parse = $(this).attr("data-parse");
        $("span.produk_harga").html(produk);
        $("input[name=parse").val(parse);
    })


    $("a.aff").click(function() {
        var target = $(this).attr("data-id");
        $("div#aff").load('<?= PV::BASE_URL ?>Paket/load_aff/' + target);
    })

    $("a.deleteItem").click(function() {
        var id = $(this).attr("data-id_order");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Paket/deleteOrder",
            data: {
                id_order: id
            },
            type: "POST",
            success: function(res) {
                if (res == 0) {
                    content(<?= $id_pelanggan_jenis ?>, <?= $data['ref'] ?>);
                } else {
                    alert(res);
                }
            }
        });
    })

    $("a.deleteItemBarang").click(function() {
        var id = $(this).attr("data-id");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Paket/deleteOrderBarang",
            data: {
                id: id
            },
            type: "POST",
            success: function(res) {
                if (res == 0) {
                    content(<?= $id_pelanggan_jenis ?>, <?= $data['ref'] ?>);
                } else {
                    alert(res);
                }
            }
        });
    })

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content(<?= $id_pelanggan_jenis ?>, <?= $data['ref'] ?>);
                } else if (res == 1) {
                    var parse = $("select[name=id_pelanggan]").val();
                    location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + parse;
                } else {
                    alert(res);
                }
            }
        });
    });

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
                    url: '<?= PV::BASE_URL ?>Paket/updateCell_N',
                    data: {
                        'id': id,
                        'value': value_after,
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            content(<?= $id_pelanggan_jenis ?>, <?= $data['ref'] ?>);
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });

    $("#paket").change(function() {
        var get = $(this).val();
        if (get != "") {
            content(<?= $id_pelanggan_jenis ?>, get);
        } else {
            content(<?= $id_pelanggan_jenis ?>, '');
        }
    })

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
                    url: '<?= PV::BASE_URL ?>Paket/update_catatan',
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