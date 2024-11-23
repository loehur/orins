<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

if ($id_pelanggan_jenis == 1) {
    $pelanggan_jenis = "Umum";
} else {
    $pelanggan_jenis = "Rekanan";
}
?>

<main class="container mt-4">
    <!-- Main page content-->
    <div class="container-fluid px-2">
        <?php
        if (count($data['errorID']) > 0) {
            echo "<br><small class='text-danger'>Order Data Error! yang mungkin disebabkan oleh jaringan terputus atau pengaturan produk yang tidak valid:</small><br><hr class='my-1'>";
            foreach ($data['errorID'] as $k => $ei) { ?>
                - ID#<?= $ei['id'] ?> <?= $ei['produk'] ?> <button class="btn btn-sm btn-outline-danger delError border-0 shadow-sm py-1 mb-1 ms-1" data-id="<?= $ei['id'] ?>"><b>Hapus</b></button><br>
            <?php }
        } else {
            ?>

            <div class="row mb-4 <?= count($data['order']) == 0 ? "d-none" : "" ?>">
                <div class="col border-bottom">
                    <form action="<?= PV::BASE_URL ?>Buka_Order/proses/<?= $id_pelanggan_jenis ?>" method="POST">
                        <div class="row pb-2">
                            <div class="col px-1" style="max-width: 300px;">
                                <select class="tize shadow-none" name="id_pelanggan" required>
                                    <option value="">Customer Name (<?= $pelanggan_jenis ?>)</option>
                                    <?php foreach ($data['pelanggan'] as $p) { ?>
                                        <option value="<?= $p['id_pelanggan'] ?>"><?= strtoupper($p['nama']) ?> | <?= $p['no_hp'] ?></option>
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

            <div class="row mb-2">
                <div class="col pe-0">
                    <?php if ($data['count'] <= 15) { ?>
                        <button type="button" class="btn me-2 shadow-none btn-sm btn-primary bg-gradient py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">(&#43;) Jasa & Produksi</button>
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
                        <button type="button" class="btn me-2 shadow-none btn-sm btn-success bg-gradient py-1" data-bs-toggle="modal">(&#43;) Barang</button>
                    <?php } ?>
                </div>
            </div>
            <?php if (count($data['order']) > 0) { ?>
                <div class="row">
                    <div class="col border px-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php
                                $no = 0;
                                foreach ($data['order'] as $keyD => $do) {
                                    $no++;
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
                                                    <td class="ps-2"><span class="text-nowrap text-dark"><small class="text-secondary">#<?= $id_order_data ?></small><b><small> <?= ucwords($produk) ?></small></b></span></td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small>Price [
                                                            <?php
                                                            if ($harga_ok == false) {
                                                                echo $btnSetHarga;
                                                            } else {
                                                                if ($akum_diskon > 0) {
                                                                    echo "<del>" . number_format($do['harga']) . "</del> <small>" . number_format($do['harga'] - $akum_diskon);
                                                                } else {
                                                                    echo number_format($do['harga']);
                                                                }
                                                            } ?>
                                                            ]
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small>Qty [
                                                            <span class="edit_n" data-id="<?= $do['id_order_data'] ?>"><?= $do['jumlah'] ?></span>
                                                            ]</small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <b>
                                                            <small>Total [
                                                                <?php
                                                                if ($harga_ok == false) {
                                                                    echo $btnSetHarga;
                                                                } else {
                                                                    if ($akum_diskon > 0) {
                                                                        echo "<del>" . number_format($do['harga'] * $do['jumlah']) . "</del> " . number_format(($do['harga'] * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                                                                    } else {
                                                                        echo number_format($do['harga'] * $do['jumlah']);
                                                                    }
                                                                } ?>
                                                                ]
                                                            </small>
                                                        </b>
                                                    </td>
                                                    <td style="width: 30px;"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
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
                                                                                    <?php if ($disk > 0) { ?>
                                                                                        <del>Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']]) ?></del>
                                                                                    <?php } ?>
                                                                                    Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']] - $disk) ?>
                                                                                    <b><span data-bs-toggle="modal" data-code="<?= $ld_o['c_h'] ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#exampleModal1" style="cursor: pointer;" class="tetapkanHarga px-2">P</span></b>
                                                                                    <?php if ($harga_d > 0 && in_array($this->userData['user_tipe'], $this->pKasir)) { ?>
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
    </div>
</main>
<div class="modal fade" id="exampleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Produk - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add" method="POST">
                <div class="modal-body bg-primary bg-gradient bg-opacity-10 px-2">
                    <div class="mb-2">
                        <select class="tize loadDetail" name="id_produk" required>
                            <option></option>
                            <?php foreach ($this->dProduk as $dp) { ?>
                                <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="detail"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-primary bg-gradient rounded-pill" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalAff" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Produk (Afiliasi) - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="aff"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><small><span class="produk_harga"></span></small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add_price/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Harga</label>
                        <input type="number" min="1" name="harga" class="form-control" required>
                        <input type="hidden" name="harga_code" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Tetapkan HARGA</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDiskon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b><span class="produk_harga"></span></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/diskon" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Diskon Rp</label>
                        <input type="number" min="0" name="diskon" class="form-control" required>
                        <input type="hidden" name="parse" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-purple border-purple" data-bs-dismiss="modal">Tetapkan Diskon</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
        var produk = 1;
        $("div#detail").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
    });

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
        $("div#detail").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
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
        $("div#aff").load('<?= PV::BASE_URL ?>Buka_Order/load_aff/' + target);
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

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
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