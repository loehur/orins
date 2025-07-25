<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<?php $d = $data['input']; ?>

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2">
            <div class="col-auto mt-auto px-1">
                <a href="<?= PV::BASE_URL ?>Gudang_Input"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
        </div>
        <div class="border rounded px-2 py-2 pb-0">
            <table class="mb-2 text-sm mb-0 w-100">
                <tr>
                    <td style="width: 100px;">Tanggal</td>
                    <td>: <?= $d['tanggal'] ?></td>
                    <td><?= $d['sds'] == 1 ? "#SDS" : "" ?></td>
                    <td class="text-end">
                        <?php if ($d['cek'] == 0) { ?>
                            <span class="badge bg-warning">Checking</span>
                        <?php } else { ?>
                            <?php if ($d['cek'] == 1) { ?>
                                <span class="badge bg-success">VERIFIED</span> | <span class="text-danger reject_ref" data-ref="<?= $d['id'] ?>" style="cursor: pointer;">Reject</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">REJECTED</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Supplier
                    </td>
                    <td colspan="3">
                        : <?= $data['supplier'][$d['id_sumber']]['nama'] ?>
                    </td>
                </tr>
                <tr>
                    <td class="pe-2">No. Faktur/PO</td>
                    <td>: <?= $d['no_faktur'] ?>/<?= $d['no_po'] ?></td>
                </tr>
                <tr>
                    <td>Note</td>
                    <td colspan="3">: <?= $d['note'] ?></td>
                </tr>
            </table>
        </div>

        <?php if ($d['cek'] == 0) { ?>
            <form action="<?= PV::BASE_URL ?>Gudang_Input/add_mutasi" method="POST">
                <div class="row mt-3 mx-0">
                    <div class="col px-0 mb-2">
                        <input type="hidden" name="head_id" value="<?= $d['id'] ?>">
                        <select name="barang" class="ac tize border-0 w-100" required id="barang">
                            <option value="">Pilih Barang</option>
                            <?php foreach ($data['barang'] as $br) {
                                $code_split = str_split($br['code'], 2); ?>
                                <option value="<?= $br['id'] ?>"><?= $code_split[0] ?> <?= $br['product_name'] ?><?= $br['nama'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-auto px-1 mb-2 text-end" id="col_qty">
                        <input id="qty" placeholder="qty" required type="decimal" min="1" class="text-end border-bottom border-0" name="qty" style="text-transform: uppercase;width:60px">
                    </div>
                    <div class="col-auto px-0 mt-auto mb-2">
                        <button type="submit" class="btn btn-outline-success">Add</button>
                    </div>
                </div>
            </form>
        <?php } ?>

        <table class="table table-sm mx-1 text-sm">
            <?php
            $nos = 0;
            foreach ($data['mutasi'] as $id_) {
                $nos += 1;
                $no = 0;
                foreach ($id_ as $a) {
                    $no++; ?>
                    <tr id="tr<?= $a['id'] ?>">
                        <td class="text-end text-sm text-secondary">
                            #<?= $a['id'] ?>
                        </td>
                        <td class="text-end fw-bold"><?= $no == 1 ? $nos . "." : ""; ?></td>
                        <td class="text-end">
                            <?= $no ?>.
                        </td>
                        <td class="">
                            <?= $data['barang'][$a['id_barang']]['nama'] ?> <?= $data['barang'][$a['id_barang']]['product_name'] ?>
                        </td>
                        <td>
                            <?= $a['sds'] == 1 ? "S" : "T" ?>
                        </td>
                        <td class="text-end">
                            <?= $a['qty'] ?>
                        </td>
                        <td>
                            <?php if ($a['sn_c'] == 1 && $a['stat'] == 0) { ?>
                                <span data-id="<?= $a['id'] ?>" data-kode="<?= $a['id_barang'] ?>" data-col="sn" data-nos="<?= $nos ?>" data-tipe="text" data-primary="id" data-no="<?= $no ?>" data-tb="master_mutasi" class="cell_edit n<?= $nos ?>r<?= $no ?>"><?= strlen($a['sn']) == 0 ? "[ ]" : $a['sn'] ?></span>
                            <?php } else { ?>
                                <?= $a['sn'] ?>
                            <?php } ?>
                        </td>
                        <td class="align-middle">
                            <?php if ($a['stat'] == 0) { ?>
                                <span data-id="<?= $a['id'] ?>" data-primary="id" data-tb="master_mutasi" class="cell_delete text-danger" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></span>
                            <?php } else { ?>
                                <?php if ($a['stat'] == 1) { ?>
                                    <span class="text-success"><i class="fa-solid fa-check"></i></span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
                ?>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $(document).on('keydown', function(event) {
        if (event.keyCode == 112) {
            event.preventDefault();
            $('.selectize-input').click();
        }
    });

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result)
                }
            },
        });
    });

    var click = 0;
    $(".cell_edit").on('click', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var no = $(this).attr('data-no');
        var nos = $(this).attr('data-nos');
        var kode = $(this).attr('data-kode');
        var primary = $(this).attr('data-primary');
        var col = $(this).attr('data-col');
        var tb = $(this).attr('data-tb');
        var tipe = $(this).attr('data-tipe');
        var value = $(this).html();
        var value_before = value;
        if (value == "[ ]") {
            value = "";
        }
        var el = $(this);
        var width = el.parent().width();
        var align = "left";
        if (tipe == "number") {
            align = "right";
        }

        el.parent().css("width", width);
        el.html("<input required type=" + tipe + " style='text-transform:uppercase;outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

        $("#value_").focus();
        $('#value_').keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        });
        $("#value_").focusout(function() {
            var value_after = $(this).val().toUpperCase();
            if (value_after === value_before) {
                el.html(value);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Gudang_Input/update_pbsn',
                    data: {
                        'id': id,
                        'no': no,
                        'value': value_after,
                        'col': col,
                        'primary': primary,
                        'tb': tb,
                        'kode': kode
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            if (value_after == "") {
                                el.html("[ ]");
                            } else {
                                var next = parseInt(no) + 1;
                                el.html(value_after);
                                $("span.n" + nos + "r" + next).click();
                            }
                        } else {
                            el.html("REJECTED");
                        }
                    },
                });
            }
        });
    });

    $(".cell_delete").dblclick(function() {
        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var tb = $(this).attr('data-tb');

        console.log(id, primary, tb);

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
                }
            },
        });
    });
</script>