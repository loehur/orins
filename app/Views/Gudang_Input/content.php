<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>
<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Gudang_Input/add" method="POST">
            <div class="row mb-2 mx-0">
                <div class="col-auto px-1 mb-2">
                    <label>Supplier</label><br>
                    <select name="supplier" required class="border-0 tize" id="tujuan" style="text-transform: uppercase; width:200px">
                        <option></option>
                        <?php foreach ($data['supplier'] as $tj) { ?>
                            <option value="<?= $tj['id'] ?>"><?= strtoupper($tj['nama']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <label>Tanggal</label><br>
                    <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col-auto px-1 mb-2">
                    <label>No. DO</label><br>
                    <input class="border-bottom border-0" id="test" required name="no_po" style="text-transform: uppercase;">
                </div>
                <div class="col-auto px-1 mb-2">
                    <label>No. Faktur</label><br>
                    <input class="border-bottom border-0" required name="no_fak" style="text-transform: uppercase;">
                </div>
                <div class="col-auto px-1 mb-2">
                    <label>Note</label><br>
                    <input class="border-bottom border-0" name="note" maxlength="100">
                </div>
                <div class="col-auto px-1 mb-2">
                    <div class="pt-4">
                        <input name="sds" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label" for="flexCheckDefault">
                            SDS
                        </label>
                    </div>
                </div>
                <div class="col mt-auto mb-2">
                    <button type="submit" class="btn btn-outline-success">Create</button>
                </div>
            </div>
        </form>

        <table class="text-sm" id="dt_tb">
            <thead>
                <tr>
                    <th class="text-center"></th>
                    <th>Ref/Supplier</th>
                    <th>No. DO/Faktur/Note</th>
                    <th>ST</th>
                </tr>
            </thead>
            <?php foreach ($data['input'] as $a) { ?>
                <tr id="<?= $a['id'] ?>">
                    <td class="align-middle text-center">
                        <a href="<?= PV::BASE_URL ?>Gudang_Input/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                        <br>
                        <small><?= $a['sds'] == 1 ? "SDS" : "ABF" ?></small>
                    </td>
                    <td>
                        <?= $a['id'] ?> <span class="editSurat text-primary" style="cursor:pointer;" data-id="<?= $a['id'] ?>" data-bs-target="#modalEdit" data-bs-toggle="modal"><i class="fa-solid fa-pen-to-square"></i></span>
                        <br>
                        <span class="fw-bold">
                            <?= $data['supplier'][$a['id_sumber']]['nama'] ?>
                        </span>
                    </td>
                    <td>
                        <?= $a['no_po'] ?>/<?= $a['no_faktur'] ?><br>
                        <small><span><i class="fa-regular fa-note-sticky"></i> <?= $a['note'] ?></span></small>
                    </td>
                    <td>
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-danger cancel" style="cursor: pointer;" data-id="<?= $a['id'] ?>"><i class="fa-regular fa-circle-xmark"></i> Cancel</span><br>
                            <span class="badge bg-warning">CHECKING</span>
                        <?php } else { ?>
                            <?php if ($a['cek'] == 1) { ?>
                                <span class="badge bg-success">VERIFIED</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">REJECTED</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<div class="modal" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Edit Surat
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="formLoad" class="p-2"></div>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
        $('#dt_tb').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 530,
            "dom": "lfrti"
        });
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

    $(".editSurat").click(function() {
        var id = $(this).attr('data-id');
        $("div#formLoad").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
            $("div#formLoad").load('<?= PV::BASE_URL ?>Gudang_Input/loadEdit/' + id);
        });
    })

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
                            el.html("REJECTED");
                        }
                    },
                });
            }
        });
    });

    $(".cancel").dblclick(function() {
        var id = $(this).attr('data-id');
        $.ajax({
            url: '<?= PV::BASE_URL ?>Gudang_Input/cancel',
            data: {
                'id': id,
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    $("tr#" + id).remove();
                } else {
                    alert(res);
                }
            },
        });
    });
</script>