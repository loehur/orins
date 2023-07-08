<style>
    .selectize-control {
        padding: 0;
    }

    .selectize-input {
        border: none;
    }

    .selectize-input::after {
        visibility: hidden;
    }
</style>

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
            <div class="card-body">
                <form action="<?= $this->BASE_URL ?>Buka_Order_Aff/proses/<?= $data['parse'] ?>/<?= $data['pelanggan_jenis'] ?>" method="POST">
                    <div class="row pb-2">
                        <div class="col">
                            <label class="form-label">Pelanggan</label>
                            <input class="form-control" type="text" name="pelanggan_nama" value="<?= strtoupper($data['pelanggan_nama']) ?>" readonly>
                            <input class="form-control" type="hidden" name="pelanggan" value="<?= $data['pelanggan'] ?>" readonly>
                        </div>
                        <div class="col">
                            <label class="form-label">CS Afiliasi</label>
                            <input class="form-control" type="text" name="pelanggan_nama" value="<?= strtoupper($data['pengirim']) ?>" readonly>
                        </div>
                        <div class="col">
                            <label class="form-label">Customer Service</label>
                            <select class="border tize" name="id_karyawan" required>
                                <option></option>
                                <?php foreach ($data['karyawan'] as $k) {
                                    if ($k['id_toko'] == $this->userData['id_toko']) { ?>
                                        ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= strtoupper($k['nama']) ?></option>
                                <?php }
                                } ?>
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
                    foreach ($data['order'] as $do) {
                        $no++;
                        $id_order_data = $do['id_order_data'];
                        $id_produk = $do['id_produk'];
                        $detail_arr = unserialize($do['produk_detail']);
                        $detail = "";
                        foreach ($detail_arr as $da) {
                            $detail .= $da['detail_name'] . ", ";
                        }

                        foreach ($this->dProdukAll as $dp) {
                            if ($dp['id_produk'] == $id_produk) {
                                $produk = $dp['produk'];
                            }
                        }
                    ?>
                        <tr>
                            <td class="text-end"><?= $no  ?></td>
                            <td>
                                <table class="border-bottom">
                                    <tr>
                                        <td colspan="10"><span class="text-nowrap text-success"><small><?= ucwords($produk) ?></small></span><br>
                                    <tr>
                                    <tr>
                                        <?php
                                        foreach ($detail_arr as $da) { ?>
                                            <td class="pe-1" nowrap>
                                                <?= "<small>" . $da['group_name'] . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                </table>
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="text-nowrap">
                                            <small>Catatan Utama</small><br><span class="text-danger"><?= $do['note'] ?></span>
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-nowrap">
                                            <small>Catatan Produksi</small><br>
                                            <span>
                                                <?php
                                                foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                    echo $this->model('Arr')->get($this->dDvs, "id_divisi", "divisi", $ks) . ": " . $ns . ", ";
                                                }
                                                ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end"><span class="edit" data-id="<?= $do['produk_code'] ?>"><?= number_format($do['harga']) ?></span></td>
                            <td class="text-end"><span class="edit_n" data-id="<?= $do['id_order_data'] ?>"><?= number_format($do['jumlah']) ?></span></td>
                            <td class="text-end"><?= number_format($do['harga'] * $do['jumlah']) ?></td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

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
                    var parse = $("input[name=pelanggan]").val();
                    location.href = "<?= $this->BASE_URL ?>Data_Operasi/index/" + parse;
                } else {
                    alert(res);
                }
            }
        });
    });
</script>