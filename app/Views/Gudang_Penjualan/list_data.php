<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<?php $d = $data['input']; ?>

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2">
            <div class="col-auto mt-auto px-1 mb-2">
                <a href="<?= PV::BASE_URL ?>Gudang_Penjualan"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>No. Ref</label><br>
                <input name="id" value="<?= $d['id'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase;">
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>Tujuan</label><br>
                <input name="supplier_c" value="<?= $data['tujuan'][$d['id_target']]['nama'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2 text-center">
                <label>Tanggal</label><br>
                <input type="date" name="tanggal" readonly class="text-center border-bottom border-0" value="<?= $d['tanggal'] ?>">
            </div>
            <?php if ($d['cek'] == 0) { ?>
                <div class="col-auto mt-auto px-1 mb-2">
                    <span class="text-danger cancel-surat" style="cursor: pointer;" data-id="<?= $d['id'] ?>"><i class="fa-regular fa-circle-xmark"></i> Cancel Surat</span>
                </div>
            <?php } ?>
        </div>
        <hr>
        <?php if ($d['cek'] == 0) { ?>
            <div class="row mb-2 mx-0">
                <div class="col px-1 mb-2">
                    <label>Barang</label><br>
                    <select name="barang" class="tize border-0 w-100" required id="barang">
                        <option></option>
                        <?php foreach ($data['barang'] as $br) { ?>
                            <?php if (strlen($br['nama']) > 1) { ?>
                                <option value="<?= $br['id'] ?>"><?= $br['nama'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <div id="stok_data"></div>

        <table class="table table-sm mx-1 bg-light text-sm" id="mutasi_table">
            <?php
            $no = 0;
            foreach ($data['mutasi'] as $a) {
                $no++; ?>
                <tr id="tr<?= $a['id'] ?>">
                    <td class="text-end">
                        <?= $a['id'] ?>#
                    </td>
                    <td class="text-end">
                        <?= $no ?>
                    </td>
                    <td class="">
                        <?= $data['barang'][$a['id_barang']]['nama'] ?>
                    </td>
                    <td class="">
                        <?= $a['sds'] == 0 ? "SDS-NO" : "SDS-YES" ?>
                    </td>
                    <td class="">
                        <?= $a['sn'] == "" ? "NO-SN" : $a['sn'] ?>
                    </td>
                    <td class="text-end">
                        <?= $a['qty'] ?>
                    </td>
                    <td class="align-middle text-end">
                        <?php if ($a['stat'] == 0) { ?>
                            <span data-id="<?= $a['id'] ?>" data-primary="id" data-tb="master_mutasi" class="cell_delete text-danger" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></span>
                        <?php } else { ?>
                            <span class="text-success"><i class="fa-solid fa-check"></i></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<div class="modal" id="modalConfirmAction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmActionTitle">Konfirmasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalConfirmActionText">
                Yakin?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="modalConfirmActionYes">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    function showConfirmAction(message, onConfirm, options) {
        options = options || {};
        $('#modalConfirmActionTitle').text(options.title || 'Konfirmasi');
        $('#modalConfirmActionText').html(message);
        $('#modalConfirmActionYes').text(options.confirmText || 'Ya, Hapus');

        var modal = new bootstrap.Modal(document.getElementById('modalConfirmAction'));
        $('#modalConfirmActionYes').off('click').on('click', function() {
            modal.hide();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });
        modal.show();
    }

    function refreshMutasiTable() {
        $.get('<?= PV::BASE_URL ?>Gudang_Penjualan/list_data/<?= $d['id'] ?>', function(html) {
            var $html = $('<div>').append($.parseHTML(html));
            var $tbl = $html.find('#mutasi_table');
            if ($tbl.length) {
                $('#mutasi_table').html($tbl.html());
            }
        });
    }

    $(document).off('submit.gjualAdd', 'form.form-add-mutasi').on('submit.gjualAdd', 'form.form-add-mutasi', function(e) {
        e.preventDefault();
        var $form = $(this);
        var barangId = $form.find('input[name="kode"]').val() || ($('#barang').val() || '');
        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: $form.attr('method'),
            success: function(result) {
                if (result == 0) {
                    refreshMutasiTable();
                    if (barangId) {
                        $('#stok_data').load('<?= PV::BASE_URL ?>Gudang_Penjualan/stok_data/' + barangId + '/<?= $d['id'] ?>');
                    }
                } else {
                    alert(result);
                }
            },
        });
    });

    $(document).off('click.gjualDel', '.cell_delete').on('click.gjualDel', '.cell_delete', function() {
        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var tb = $(this).attr('data-tb');

        showConfirmAction('Yakin ingin menghapus item ini?', function() {
            $.ajax({
                url: '<?= PV::BASE_URL ?>Functions/deleteCell',
                data: {
                    'id': id,
                    'primary': primary,
                    'tb': tb
                },
                type: 'POST',
                dataType: 'html',
                success: function(res) {
                    if (res == 0) {
                        $("#tr" + id).remove();
                    } else {
                        alert(res);
                    }
                }
            });
        });
    });

    $(document).off('change.gjualBarang', '#barang').on('change.gjualBarang', '#barang', function() {
        var get = $(this).val();
        if (get != "") {
            $('#stok_data').load('<?= PV::BASE_URL ?>Gudang_Penjualan/stok_data/' + get + '/' + '<?= $d['id'] ?>');
        }
    });

    $(document).off('click.gjualCancel', '.cancel-surat').on('click.gjualCancel', '.cancel-surat', function() {
        var id = $(this).attr('data-id');
        showConfirmAction('Yakin ingin cancel surat <b>' + id + '</b>?', function() {
            $.ajax({
                url: '<?= PV::BASE_URL ?>Gudang_Penjualan/cancel',
                data: { id: id },
                type: 'POST',
                success: function(res) {
                    if (res == 0) {
                        location.href = '<?= PV::BASE_URL ?>Gudang_Penjualan';
                    } else {
                        alert(res);
                    }
                }
            });
        });
    });
</script>
