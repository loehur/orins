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
        <form action="<?= PV::BASE_URL ?>Gudang_Penjualan/add" method="POST">
            <div class="row mb-2 mx-0">
                <div class="col-auto px-1 mb-2">
                    <label>Tujuan</label><br>
                    <select name="tujuan" required class="border-0 tize" id="tujuan" style="text-transform: uppercase; width:200px">
                        <option></option>
                        <?php foreach ($data['tujuan'] as $tj) { ?>
                            <option value="<?= $tj['id_pelanggan'] ?>"><?= strtoupper($tj['nama']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <label>Tanggal</label><br>
                    <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
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
                    <th>Ref</th>
                    <th>Tanggal</th>
                    <th>Tujuan</th>
                    <th>ST</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data['input'] as $a) { ?>
                <tr id="<?= $a['id'] ?>">
                    <td class="align-middle">
                        <a href="<?= PV::BASE_URL ?>Gudang_Penjualan/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $a['tanggal'] ?>
                    </td>
                    <td class="">
                        <?= strtoupper($data['tujuan'][$a['id_target']]['nama']) ?>
                    </td>
                    <td class="align-middle">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-danger cancel" style="cursor: pointer;" data-id="<?= $a['id'] ?>"><i class="fa-regular fa-circle-xmark"></i> Cancel</span><br>
                            <span class="badge bg-warning">CHECKING</span>
                        <?php } else { ?>
                            <span class="badge bg-success">CONFIRMED</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
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

    $(".cancel").click(function() {
        var id = $(this).attr('data-id');
        showConfirmAction('Yakin ingin cancel surat <b>' + id + '</b>?', function() {
            $.ajax({
                url: '<?= PV::BASE_URL ?>Gudang_Penjualan/cancel',
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
                }
            });
        });
    });
</script>