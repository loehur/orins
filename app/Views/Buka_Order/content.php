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
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4"></div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mt-n10">
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
                    <div class="row pb-2">
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
                        <div class="col mt-auto">
                            <button type="submit" class="btn btn-primary">Proses - <span class="ms-1"><b><?= $data['count'] ?> Item</b></span></button>
                        </div>
                    </div>
                </form>
            </div>
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <td class="text-purple text-end">No</td>
                        <td class="text-purple">Produk</td>
                        <td class="text-purple text-end">Harga</td>
                        <td class="text-purple text-end">Jumlah</td>
                        <td class="text-purple text-end">Total</td>
                        <td class="text-purple"></td>
                    </tr>
                </thead>
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

                    ?>
                        <tr>
                            <td class="text-end"><?= $no  ?></td>
                            <td>
                                <table>
                                    <tr>
                                        <td colspan="10"><span class="text-nowrap text-success"><b><small><?= ucwords($produk) ?></small></small></span><br>
                                    <tr>
                                    <tr>
                                        <?php
                                        foreach ($detail_arr as $da) { ?>
                                            <td class="pe-1" nowrap>
                                                <?= "<small>" . $da['group_name'] . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td colspan="10">
                                            <small>
                                                <table class="border">
                                                    <?php
                                                    foreach ($listDetail as $kl => $ld_o) {
                                                        $harga_d = $data['harga'][$keyD][$ld_o['c_h']];
                                                        $disk = $ld_o['d'];
                                                        $akum_diskon += $disk ?>
                                                        <tr>
                                                            <td class="text-success"><b><span data-bs-toggle="modal" data-code="<?= $ld_o['c_h'] ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#exampleModal1" style="cursor: pointer;" class="tetapkanHarga px-2">P</span></b></td>
                                                            <?php if ($harga_d > 0 && in_array($this->userData['user_tipe'], $this->pKasir)) { ?>
                                                                <td class="text-purple"><b><span data-bs-toggle="modal" data-parse="<?= $id_order_data . "_" . $kl . "_" . $harga_d ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#modalDiskon" style="cursor: pointer;" class="tetapkanDiskon px-2">D</span></b></td>
                                                            <?php } elseif (in_array($this->userData['user_tipe'], $this->pKasir)) {
                                                                echo "<td></td>";
                                                            } ?>
                                                            <td class="ps-2"><?= strtoupper($ld_o['n_v']) . " " ?></td>
                                                            <td class="ps-2 text-end">Disc. <?= number_format($ld_o['d']) ?></td>
                                                            <?php if ($disk > 0) { ?>
                                                                <td class="ps-2 text-end"><del>Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']]) ?></del></td>
                                                            <?php } ?>
                                                            <td class="ps-2 text-end">Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']] - $disk) ?></td>
                                                            <td class="ps-2 pe-2">
                                                                <?php if ($data['harga'][$keyD][$ld_o['c_h']] > 0) { ?>
                                                                    <i class="fa-solid fa-circle-check text-success"></i>
                                                                <?php } else { ?>
                                                                    <i class="fa-regular fa-circle text-warning"></i>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                </table>
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
                            <td class="text-end">
                                <?php
                                if ($harga_ok == false) {
                                    echo $btnSetHarga;
                                } else {
                                    if ($akum_diskon > 0) {
                                        echo "<del>" . number_format($do['harga']) . "</del><br><small>Disc. Rp" . number_format($akum_diskon) . "</small><br>" . number_format($do['harga'] - $akum_diskon);
                                    } else {
                                        echo number_format($do['harga']);
                                    }
                                } ?>
                            </td>
                            <td class="text-end"><span class="edit_n" data-id="<?= $do['id_order_data'] ?>"><?= number_format($do['jumlah']) ?></span></td>
                            <td class="text-end">
                                <?php
                                if ($harga_ok == false) {
                                    echo $btnSetHarga;
                                } else {
                                    if ($akum_diskon > 0) {
                                        echo "<del>" . number_format($do['harga'] * $do['jumlah']) . "</del><br><small>Disc. Rp" . number_format($akum_diskon * $do['jumlah']) . "</small><br>" . number_format(($do['harga'] * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                                    } else {
                                        echo number_format($do['harga'] * $do['jumlah']);
                                    }
                                } ?>
                            </td>
                            <td class="pe-3"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
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