<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php $d = $data['input']; ?>

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2 text-sm">
            <div class="col-auto mt-auto px-1 mb-2">
                <a href="<?= PV::BASE_URL ?>Barang_Masuk"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>Sumber</label><br>
                <input name="sumber" value="<?= isset($data['toko'][$d['id_sumber']]['nama_toko']) ? $data['toko'][$d['id_sumber']]['nama_toko'] : "Gudang" ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>No. Ref</label><br>
                <input name="supplier_c" id="supplier_c" value="<?= $d['id'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2 text-center">
                <label>Tanggal</label><br>
                <input type="date" name="tanggal" readonly class="text-center border-bottom border-0" value="<?= $d['tanggal'] ?>">
            </div>
            <div class="col text-end mt-auto">
                <?php if ($d['cek'] == 0) { ?>
                    <span data-ref="<?= $d['id'] ?>" style="cursor: pointer;" class="btn btn-sm btn-outline-success btnTerima" data-bs-toggle="modal" data-bs-target="#exampleModalTerima"><i class="fa-solid fa-check"></i> Verify</span>
                <?php } else { ?>
                    <?php if ($d['cek'] == 1) { ?>
                        <span class="badge bg-success">VERIFIED</span> | <span class="text-danger reject_ref" data-ref="<?= $d['id'] ?>" style="cursor: pointer;">Reject</span>
                    <?php } else { ?>
                        <span class="badge bg-danger">REJECTED</span>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <table class="table table-sm text-sm mx-1 table-hover">
            <?php
            $no = 0;
            foreach ($data['mutasi'] as $a) {
                $no++; ?>
                <tr id="tr<?= $a['id'] ?>">
                    <td class="text-end">
                        <?= $no ?>#<?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $data['barang'][$a['id_barang']]['code'] ?>
                    </td>
                    <td class="">
                        <?= $data['barang'][$a['id_barang']]['nama'] ?>
                        <?= $data['barang'][$a['id_barang']]['product_name'] ?>
                    </td>
                    <td class="text-end">
                        <?= $a['qty'] ?>
                    </td>
                    <td>
                        <?= $a['sn'] ?>
                    </td>
                    <td>
                        <?= $a['sds'] == 1 ? "SDS-YES" : "SDS-NO" ?>
                    </td>
                    <td class="align-middle text-end">
                        <?php if ($a['stat'] == 0) { ?>
                            <span class="badge bg-warning">Check</span>
                        <?php } else { ?>
                            <?php if ($a['stat'] == 1) { ?>
                                <span class="text-success"><i class="fa-solid fa-check"></i></span>
                            <?php } else { ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<form class="ajax" action="<?= PV::BASE_URL ?>Barang_Masuk/update" method="POST">
    <div class="modal" id="exampleModalTerima">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Penerima Barang Masuk</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class=" form-label">Karyawan</label>
                                <input type="hidden" id="ref" name="ref">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan_toko'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= ucwords($k['nama']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-sm btn-success">Terima</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });


    $("span.btnTerima").click(function() {
        ref = $(this).attr("data-ref");
        $("input#ref").val(ref);
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

    $(".reject_ref").on('dblclick', function() {
        var ref = $(this).attr('data-ref');
        $.ajax({
            url: '<?= PV::BASE_URL ?>Barang_Masuk/reject',
            data: {
                ref: ref
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            },
        });
    });
</script>