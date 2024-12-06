<?php
$a = $data['main'];
$c_item = count($data['item']);
?>
<table class="table table-sm w-auto table-borderless">
    <tr>
        <td class="align-middle">
            <span class="edit_grup pe-2 text-nowrap fw-bold text-success" data-id='<?= $a['id_index'] ?>'><?= $a['detail_group'] ?></span>
        </td>
        <td class="align-middle">
            <span class="cell_edit pe-2 text-nowrap" data-id='<?= $a['id_index'] ?>' data-tb="detail_group" data-primary="id_index" data-col="note"><?= $a['note'] == "" ? "[ ]" : $a['note'] ?></span>
        </td>
        <td class="align-middle">
            <?php
            if ($c_item == 0) { ?>
                <span style="cursor: pointer;" data-id="<?= $a['id_index'] ?>" class="deleteGrup text-danger"><i class=" fa-regular fa-circle-xmark"></i></span>
            <?php } ?>
        </td>
        <td class="align-middle">
            <span class="px-1 pb-1 rounded border">
                <small>CS Add: <input id="check" data-val="<?= $a['id_index'] ?>" type="checkbox" <?= ($a['cs'] == 1) ? "checked" : "" ?>></small>
            </span>
        </td>
        <td class="align-middle text-end">Item/Varian<button type="button" class="bg-white border rounded py-1 ms-2" data-bs-toggle="modal" data-bs-target="#itemMulti"> <i class="fa-regular fa-square-plus text-primary"></i></button></td>
    </tr>
    <tr>
        <td colspan="10" class="text-end">
            <div class="overflow-auto" style="height: 290px;">
                <?php
                foreach ($data['item'] as $di) {
                    $varian = [];
                    $varian = $this->db(0)->get_where('detail_item_varian', 'id_detail_item = ' . $di['id_detail_item']) ?>
                    <div class="rounded border-0 mx-1 text-sm py-1 text-nowrap overflow-auto">
                        <?php if (count($varian) == 0) { ?>
                            <span style="cursor: pointer;" data-id="<?= $di['id_detail_item'] ?>" class="deleteItem text-danger"><i class=" fa-regular fa-circle-xmark"></i></span>
                        <?php } ?>
                        <span class="edit pe-2 text-nowrap text-purple" data-id='<?= $di['id_detail_item'] ?>'><?= strtoupper($di['detail_item']) ?></span>
                        <i class="fa-regular me-1 fa-square-plus text-secondary varian" style="cursor: pointer;" data-id='<?= $di['id_detail_item'] ?>' data-bs-toggle="modal" data-bs-target="#varian"></i>
                        <?php

                        if (count($varian) > 0) {
                            echo "<br>";
                            foreach ($varian as $vr) { ?>
                                <small><span class="border rounded px-2"><span style="cursor: pointer;" data-id="<?= $vr['id_varian'] ?>" class="deleteVarian text-secondary"><i class="fa-regular fa-circle-xmark"></i></span><span class="ms-2 editVarian" data-id="<?= $vr['id_varian'] ?>"><?= strtoupper($vr['varian']) ?></span></span></small>
                        <?php }
                        } ?>

                    </div>
                <?php }
                ?>
            </div>
        </td>
    </tr>
</table>

<div class="modal fade" id="itemMulti" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah MULTI <span class="text-success groupDetail"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Group_Detail/add_item_multi/<?= $data['parse'] ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Detail - <small>Pisahkan dengan Koma ( , )</small></label>
                        <input type="text" name="item" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="varian" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Varian <span class="text-success groupDetail"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addItem" action="<?= PV::BASE_URL ?>Group_Detail/add_varian/<?= $data['parse'] ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Varian - <small>Pisahkan dengan Koma ( , )</small></label>
                        <input type="text" name="varian" class="form-control" required>
                        <input type="hidden" name="id_item" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    load(<?= $data['parse'] ?>);
                } else {
                    alert(res);
                }
            },
        });
    });

    $('#check').change(function() {
        var id = $(this).attr('data-val');
        if ($(this).is(':checked')) {
            val = 1;
        } else {
            val = 0;
        }

        $.ajax({
            url: "<?= PV::BASE_URL ?>Group_Detail/update_add",
            data: {
                id: id,
                value: val
            },
            type: "POST",
            success: function(res) {
                if (res != 0) {
                    alert(res);
                }
            },
        });
    })

    $("span.deleteItem").click(function() {
        if (confirm("Yakin Hapus?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Group_Detail/delete_item/<?= $data['parse'] ?>",
                data: {
                    id: id
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        load(<?= $data['parse'] ?>);
                    } else {
                        alert(res);
                    }
                },
            });
        } else {
            return false;
        }
    });

    $("span.deleteVarian").click(function() {
        if (confirm("Yakin Hapus?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Group_Detail/delete_varian/<?= $data['parse'] ?>",
                data: {
                    id: id
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        load(<?= $data['parse'] ?>);
                    } else {
                        alert(res);
                    }
                },
            });
        } else {
            return false;
        }
    });

    $(".varian").click(function() {
        var id_item = $(this).attr('data-id');
        $('input[name=id_item').val(id_item);
    });

    $("span.deleteGrup").click(function() {
        if (confirm("Yakin Hapus?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Group_Detail/delete_grup",
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
                },
            });
        } else {
            return false;
        }
    });

    var click = 0;
    $("span.edit").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var value = $(this).html();
        var value_before = value;
        var span = $(this);
        span.html("<input type='text' id='value_3313' style='text-align:center;width:200px' value='" + value.toUpperCase() + "'>");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after == value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Group_Detail/updateCell',
                    data: {
                        'id': id,
                        'value': value_after,
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            load(<?= $data['parse'] ?>);
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });

    $("span.edit_grup").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var value = $(this).html();
        var value_before = value;
        var span = $(this);
        span.html("<input type='text' id='value_3313' style='text-align:left;width:200px;border:0' value='" + value + "'>");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after == value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Group_Detail/updateCell_grup',
                    data: {
                        'id': id,
                        'value': value_after,
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            load(<?= $data['parse'] ?>);
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });

    $("span.editVarian").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var value = $(this).html();
        var value_before = value;
        var span = $(this);
        span.html("<input type='text' id='value_3313' style='text-align:center;width:100px' value='" + value.toUpperCase() + "'>");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after == value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Group_Detail/updateCellVarian',
                    data: {
                        'id': id,
                        'value': value_after,
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            load(<?= $data['parse'] ?>);
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });

    var click = 0;
    $(".cell_edit").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
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
        el.html("<input required type=" + tipe + " style='outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

        $("#value_").focus();
        $('#value_').keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        });
        $("#value_").focusout(function() {
            var value_after = $(this).val();
            if (value_after === value_before) {
                el.html(value);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Functions/updateCell',
                    data: {
                        'id': id,
                        'value': value_after,
                        'col': col,
                        'primary': primary,
                        'tb': tb
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            if (value_after == "") {
                                el.html("[ ]");
                            } else {
                                el.html(value_after);
                            }
                        } else {
                            el.html(res);
                        }
                    },
                });
            }
        });
    });
</script>