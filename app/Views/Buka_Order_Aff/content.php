<main>
    <!-- Main page content-->
    <div class="container px-2">
        <div class="card shadow-none mb-1">
            <div class="card-body bg-warning-soft pb-0 pt-2 bg-gradient">
                <form class="mb-2" action="<?= PV::BASE_URL ?>Buka_Order/proses/<?= $data['pelanggan_jenis'] ?>/0/<?= $data['parse'] ?>" method="POST">
                    <div class="row">
                        <div class="col px-1">
                            <input class="form-control border-0 bg-light text-success fw-bold" type="text" name="pelanggan_nama" value="<?= strtoupper($data['pelanggan_nama']) ?>" required readonly>
                            <input class="form-control" type="hidden" name="id_pelanggan" value="<?= $data['pelanggan'] ?>" required readonly>
                        </div>
                        <div class="col px-1">
                            <input class="form-control border-0 bg-light text-primary fw-bold" type="text" name="pelanggan_nama" value="<?= strtoupper($data['pengirim']) ?>" required readonly>
                        </div>
                        <div class="col px-1">
                            <select class="tize" name="id_karyawan_aff" required>
                                <option value="">CS</option>
                                <?php foreach ($data['karyawan'] as $k) {
                                    if ($k['id_toko'] == $this->userData['id_toko']) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= strtoupper($k['nama']) ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-auto mt-auto p-0 px-1">
                            <button type="submit" class="btn btn-warning bg-gradient w-100">Proses</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm mt-2">
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
                                    <tr class="bg-warning-soft bg-gradient">
                                        <td class="ps-2">
                                            <span class="text-nowrap text-dark"><b><small><?= ucwords($produk) ?></small></b></span>
                                            <small><?= $this->model('Arr')->get($this->dUser, "id_user", "nama", $do['id_user']) ?></small>
                                        </td>
                                        <td class="text-end" style="width: 1px;white-space: nowrap;">
                                            <small>
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
                                            </small>
                                        </td>
                                        <td class="text-end" style="width: 1px;white-space: nowrap;"><span class="edit_n" data-id="<?= $do['id_order_data'] ?>">
                                                <small>
                                                    <?= number_format($do['jumlah']) ?>
                                                </small>
                                        </td>
                                        <td class="text-end pe-1" style="width: 1px;white-space: nowrap;">
                                            <b>
                                                <small>
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
                                                </small>
                                            </b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" class="border-bottom-0">
                                            <table class="table mb-1">
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
                                                                    <div class="float-end pe-1">
                                                                        <?php if ($disk > 0) { ?>
                                                                            <del>Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']]) ?></del>
                                                                        <?php } ?>
                                                                        Rp<?= number_format($data['harga'][$keyD][$ld_o['c_h']] - $disk) ?>
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
                                                <?php if (strlen($do['note']) > 0) { ?>
                                                    <div class="col">
                                                        <span>
                                                            <small>Catatan Utama</small><br><span class="text-danger"><?= $do['note'] ?></span>
                                                        </span>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                $spkR = [];
                                                if (strlen($do['pending_spk']) > 1) {
                                                    $spkR = unserialize($do['pending_spk']);
                                                }

                                                foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                    if (strlen($ns) > 0 || isset($spkR[$ks])) { ?>
                                                        <div class="col text-sm">
                                                            <small>Catatan <span class="fw-bold"><?= $this->dDvs_all[$ks]["divisi"] ?></span></small><br>
                                                            <?php if (isset($spkR[$ks])) {
                                                                $pendReady = explode("-", $spkR[$ks]); ?>
                                                                <span class="badge bg-<?= $pendReady[1] == 'r' ? 'success' : 'danger' ?>"><?= $data['spk_pending'][$pendReady[0]][$pendReady[1]] ?></span>
                                                            <?php } ?>
                                                            <span data-id="<?= $id_order_data ?>" data-col="<?= $ks ?>" data-mode="<?= $ks ?>" class="cell_edit text-primary"><?= $ns ?></span>
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
</main>

<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    function isNumeric(str) {
        if (typeof str != "string") return false
        return !isNaN(str) &&
            !isNaN(parseFloat(str))
    }

    $("form").on("submit", function(e) {
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
</script>