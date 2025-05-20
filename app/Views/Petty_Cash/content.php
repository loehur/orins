<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <?php
    $date = $data['date'];
    ?>
    <!-- Main page content-->
    <div class="container">
        <div class="row mx-0 mb-2">
            <div class="col ps-0">
                <span class="btn btn-sm btn-outline-dark" onclick="content('<?= $date ?>',1)">Back</span>
                <span class="px-2 text-primary"><?= date("F Y", strtotime($date)) ?></span>
                <span class="btn btn-sm btn-outline-dark" onclick="content('<?= $date ?>',2)">Next</span>
            </div>
            <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                <div class="col text-end">
                    <div class="btn-group me-1">
                        <button type="button" class="btn shadow-none btn-sm btn-primary bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            Operasi
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start mt-2 p-0">
                            <li><a data-bs-toggle="modal" data-bs-target="#exampleModal" class="dropdown-item" href="#">Pakai</a></li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="col-auto text-end pt-2 pe-0">
                Saldo Rp<?= number_format($data['saldo']) ?>
            </div>
        </div>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-success">Riwayat Topup</th>
            </tr>
            <?php foreach ($data['topup'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                    </td>
                    <td>
                        <?= $a['ref'] ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                    <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                        <td class="text-end" style="width:70px">
                            <?php if ($a['st'] == 0) { ?>
                                <a class="ajax" href="<?= PV::BASE_URL ?>Petty_Cash/verify/<?= $a['id'] ?>/1">Verify</a>
                            <?php } else { ?>
                                <?php if ($a['st'] == 1) { ?>
                                    <span class="text-sm text-success">Verified</span>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } else { ?>
                        <td class="text-end">
                            <?= $a['st'] == 0 ? "Check" : "Verified" ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-danger">Pemakaian</th>
            </tr>
            <?php
            foreach ($data['pakai'] as $a) { ?>
                <tr id="tr<?= $a['id'] ?>">
                    <td class="align-middle">
                        <?= $a['tanggal'] == "" ? '<i class="fa-solid fa-server"></i> ' . date('d/m/y H:i', strtotime($a['insertTime'])) : '<i class="fa-solid fa-file-pen"></i> ' . $a['tanggal'] ?>
                    </td>
                    <td>
                        <span class='fw-bold text-danger'><i class='fa-solid fa-arrow-right'></i></span> <?= $data['jkeluar'][$a['id_target']]['nama'] ?>
                    </td>
                    <td>
                        <?php if ($a['st'] <> 0 || $a['tipe'] == 5) { ?>
                            <?= $a['note'] ?>
                        <?php } else { ?>
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-col="note"><?= $a['note'] ?></span>
                        <?php } ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                    <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                        <td class="text-end" style="width:70px">
                            <?php if ($a['st'] == 0) { ?>
                                <?php if ($a['tipe'] == 2) { ?>
                                    <span data-id="<?= $a['id'] ?>" class="cell_delete text-danger" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></span>
                                <?php } ?>
                                <span class="text-sm text-warning">Check</span>
                            <?php } else { ?>
                                <?php if ($a['st'] == 1) { ?>
                                    <span class="text-sm text-success">Verified</span>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } else { ?>
                        <td class="text-end">
                            <?= $a['st'] == 0 ? "Check" : "Verified" ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<form action="<?= PV::BASE_URL; ?>Petty_Cash/pakai" method="POST">
    <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pakai</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col-auto">
                                <label class="form-label">Tanggal Nota/Event</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control form-control-sm" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Jenis</label>
                                <select name="jenis" class="form-control form-control-sm" required>
                                    <option></option>
                                    <?php
                                    foreach ($data['jkeluar'] as $djk) { ?>
                                        <option value="<?= $djk['id'] ?>"><?= $djk['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="note" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Pakai</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("a.ajax").click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.ajax({
            url: href,
            type: 'POST',
            data: {},
            success: function(res) {
                if (res == 0) {
                    content('<?= $date ?>');
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
                } else {
                    alert(res);
                }
            }
        });
    });

    $(".cell_delete").dblclick(function() {
        var id = $(this).attr('data-id');
        $.ajax({
            url: '<?= PV::BASE_URL ?>Petty_Cash/delete',
            data: {
                'id': id,
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    $("#tr" + id).remove();
                } else {
                    alert(res);
                }
            },
        });
    });

    var click = 0;
    $(".cell_edit").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var col = $(this).attr('data-col');
        var value = $(this).html();
        var tipe = "text";
        var value_before = value;
        var el = $(this);
        var width = el.parent().width();
        var align = "left";

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
            if (value_after === value_before || value_after == "") {
                el.html(value);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Petty_Cash/update',
                    data: {
                        'id': id,
                        'col': col,
                        'val': value_after,
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            el.html(value_after);
                        } else {
                            el.html(value_before);
                        }
                    },
                });
            }
        });
    });
</script>