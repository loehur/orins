<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

if ($id_pelanggan_jenis == 1) {
    $pelanggan_jenis = "Umum";
} else {
    $pelanggan_jenis = "Rekanan";
}
?>

<main>
    <!-- Main page content-->
    <div class="container-fluid px-2">
        <div class="card mt-2 shadow-sm">
            <div class="card-header ">Buka Order - <b><?= $pelanggan_jenis ?></b>
                <?php if ($data['count'] <= 15) { ?>
                    <button type="button" class="float-end btn btn-outline-primary py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
                    <div class="btn-group float-end me-3">
                        <button type="button" class="border bg-white py-1 px-3 rounded dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            Order Afiliasi
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu p-0">
                            <?php foreach ($this->dToko as $dt) {
                                if ($dt['id_toko'] <> $this->userData['id_toko']) { ?>
                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalAff" class="dropdown-item aff" data-id="<?= $dt['id_toko'] ?>" href="#"><?= $dt['nama_toko'] ?></a></li>
                            <?php }
                            } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <div class="card-body">
                <form action="<?= $this->BASE_URL ?>Buka_Order/proses/<?= $id_pelanggan_jenis ?>" method="POST">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Pelanggan <?= $pelanggan_jenis ?></label>
                            <select class="border tize" name="id_pelanggan" required>
                                <option></option>
                                <?php foreach ($data['pelanggan'] as $p) { ?>
                                    <option value="<?= $p['id_pelanggan'] ?>"><?= strtoupper($p['nama']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Customer Service</label>
                            <select class="border tize" name="id_karyawan" required>
                                <option></option>
                                <?php foreach ($data['karyawan'] as $k) { ?>
                                    <option value="<?= $k['id_karyawan'] ?>"><?= strtoupper($k['nama']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-auto mt-auto">
                            <span class="ms-1"><small><?= $data['count'] ?> Item</small></span><br>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Proses </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm mt-1">
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
                            <td>
                                <table class="table table-sm w-100 mb-0">
                                    <tr class="bg-warning-soft">
                                        <td class="ps-2"><span class="text-nowrap text-dark"><b><small><?= ucwords($produk) ?></small></b></span></td>
                                        <td class="text-end" style="width: 80px;">
                                            <small>
                                                <?php
                                                if ($harga_ok == false) {
                                                    echo $btnSetHarga;
                                                } else {
                                                    if ($akum_diskon > 0) {
                                                        echo "<del>" . number_format($do['harga']) . "</del><br><small>D. Rp" . number_format($akum_diskon) . "</small><br>" . number_format($do['harga'] - $akum_diskon);
                                                    } else {
                                                        echo number_format($do['harga']);
                                                    }
                                                } ?>
                                            </small>
                                        </td>
                                        <td class="text-end" style="width: 50px;"><span class="edit_n" data-id="<?= $do['id_order_data'] ?>">
                                                <small>
                                                    <?= number_format($do['jumlah']) ?>
                                                </small>
                                        </td>
                                        <td class="text-end" style="width: 100px;">
                                            <b>
                                                <small>
                                                    <?php
                                                    if ($harga_ok == false) {
                                                        echo $btnSetHarga;
                                                    } else {
                                                        if ($akum_diskon > 0) {
                                                            echo "<del>" . number_format($do['harga'] * $do['jumlah']) . "</del><br><small>D. Rp" . number_format($akum_diskon * $do['jumlah']) . "</small><br>" . number_format(($do['harga'] * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                                                        } else {
                                                            echo number_format($do['harga'] * $do['jumlah']);
                                                        }
                                                    } ?>
                                                </small>
                                            </b>
                                        </td>
                                        <td style="width: 30px;"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td colspan="10">
                                            <table class="table">
                                                <tr>
                                                    <td class="pe-1" nowrap>
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
                                                    <td colspan="10" valign="top" class="p-0 border border-bottom-0">
                                                        <small>
                                                            <?php
                                                            foreach ($listDetail as $kl => $ld_o) {
                                                                $harga_d = $data['harga'][$keyD][$ld_o['c_h']]; ?>
                                                                <div class="row border-bottom mx-0">
                                                                    <div class="col ps-1"><?= strtoupper($ld_o['n_v']) ?></div>
                                                                    <div class="text-end pe-0" style="width: 80px;">D. <?= number_format($ld_o['d']) ?></div>
                                                                    <div class="text-end" style="width: 100px;">
                                                                        <?php if ($disk > 0) { ?>
                                                                            <del>Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']]) ?></del>
                                                                        <?php } ?>
                                                                        Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']] - $disk) ?>
                                                                    </div>
                                                                    <div class="col-auto p-0">
                                                                        <b><span data-bs-toggle="modal" data-code="<?= $ld_o['c_h'] ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#exampleModal1" style="cursor: pointer;" class="tetapkanHarga px-2">P</span></b>
                                                                    </div>
                                                                    <?php if ($harga_d > 0 && in_array($this->userData['user_tipe'], $this->pKasir)) { ?>
                                                                        <div class="col-auto p-0"><b><span data-bs-toggle="modal" data-parse="<?= $id_order_data . "_" . $kl . "_" . $harga_d ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#modalDiskon" style="cursor: pointer;" class="tetapkanDiskon px-2">D</span></b></div>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php }
                                                            ?>

                                                        </small>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="row">
                                                <div class="col-auto">
                                                    <span>
                                                        <small>Catatan Utama</small><br><span class="text-danger"><?= $do['note'] ?></span>
                                                    </span>
                                                </div>
                                                <div class="col-auto">
                                                    <span>
                                                        <small>Catatan Produksi</small><br>
                                                        <span class="text-primary">
                                                            <?php
                                                            foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                                if (strlen($ns) > 0) {
                                                                    echo "<b>" . $this->model('Arr')->get($this->dDvs, "id_divisi", "divisi", $ks) . "</b>: " . $ns . ", ";
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </span>
                                                </div>
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
</main>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Produk - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $this->BASE_URL ?>Buka_Order/add" method="POST">
                <div class="modal-body">
                    <div class="mb-3 border border-success rounded">
                        <select class="border tize loadDetail" name="id_produk" required>
                            <option></option>
                            <?php foreach ($this->dProduk as $dp) { ?>
                                <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="detail"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalAff" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Produk - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="aff"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b><span class="produk_harga"></span></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $this->BASE_URL ?>Buka_Order/add_price/<?= $id_pelanggan_jenis ?>" method="POST">
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
            <form action="<?= $this->BASE_URL ?>Buka_Order/diskon" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Diskon Rp</label>
                        <input type="number" min="1" name="diskon" class="form-control" required>
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
    });

    $('select.loadDetail').on('change', function() {
        var produk = this.value;
        $("div#detail").load('<?= $this->BASE_URL ?>Buka_Order/load_detail/' + produk);
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
        $("div#aff").load('<?= $this->BASE_URL ?>Buka_Order/load_aff/' + target);
    })

    $("a.deleteItem").click(function() {
        var id = $(this).attr("data-id_order");
        $.ajax({
            url: "<?= $this->BASE_URL ?>Buka_Order/deleteOrder",
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
                    location.href = "<?= $this->BASE_URL ?>Data_Operasi/index/" + parse;
                } else {
                    alert(res);
                }
            }
        });
    });

    var click = 0;
    $("span.edit_n").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var value = $(this).html();
        var value_before = value;
        var span = $(this);
        span.html("<input type='number' id='value_3313' style='text-align:center;width:70px' value='" + value + "'>");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after == value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= $this->BASE_URL ?>Buka_Order/updateCell_N',
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
</script>