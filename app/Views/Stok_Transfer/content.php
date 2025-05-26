<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>
<main>
    <!-- Main page content-->
    <div class="container">
        <?php if (in_array($this->userData['user_tipe'], PV::PRIV[7])) { ?>
            <form action="<?= PV::BASE_URL ?>Stok_Transfer/add" method="POST">
                <div class="row mb-2 mx-0">
                    <div class="col-auto px-1 mb-2 text-center">
                        <label>Tanggal</label><br>
                        <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="col-auto">
                        <label>Note</label><br>
                        <input class="border-bottom border-0" name="note">
                    </div>
                    <div class="col mt-auto mb-2">
                        <button type="submit" class="btn btn-outline-success">Create</button>
                    </div>
                </div>
            </form>
        <?php } ?>

        <table class="text-sm stripe" id="dt_tb">
            <thead>
                <tr>
                    <th>Ref/Tanggal</th>
                    <th>Note</th>
                    <th></th>
                </tr>
            </thead>
            <?php foreach ($data['input'] as $a) { ?>
                <?php if (!in_array($this->userData['user_tipe'], PV::PRIV[7])) {
                    if ($a['cek'] == 0) {
                        continue;
                    }
                } ?>

                <tr>
                    <td class="align-top">
                        <a href="<?= PV::BASE_URL ?>Stok_Transfer/list/<?= $a['id'] ?>"> <?= $a['id'] ?></a><br>
                        <?= $a['tanggal'] ?>
                    </td>
                    <td class="align-top"><?= $a['note'] ?></td>
                    <td class="align-top">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-warning"><i class="fa-regular fa-circle"></i> Checking</span><br>
                            <?php if ($a['delivery'] == 0) { ?>
                                <span style="cursor: pointer;" class="reqAntar" data-bs-toggle="modal" data-bs-target="#exampleModalReq" data-id="<?= $a['id'] ?>">
                                    <i class="fa-solid fa-truck-arrow-right"></i> Minta Antar
                                </span>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($a['cek'] == 1) { ?>
                                <span class="text-success"><i class="fa-solid fa-user-check"></i></span> <?= $a['id_target_cs'] <> 0 ? $this->dKaryawanAll[$a['id_target_cs']]['nama'] : "" ?><br>
                                <?php if ($a['delivery'] == 1) { ?>
                                    <span>
                                        <i class="fa-solid text-purple fa-truck-arrow-right"></i> <?= $a['id_driver'] <> 0 ? $this->dKaryawanAll_driver[$a['id_driver']]['nama'] : "In Delivery" ?>
                                    </span>
                                <?php } ?>
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

<form action="<?= PV::BASE_URL; ?>Stok_Transfer/req_antar" method="POST">
    <div class="modal" id="exampleModalReq">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="fa-solid fa-truck-arrow-right"></i> Permintaan Antar</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Catatan untuk Driver</label>
                                <input type="text" name="note" class="form-control form-control-sm" required>
                                <input name="id" type="hidden" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Minta</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
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

    $(".reqAntar").click(function() {
        $('input[name=id]').val($(this).attr('data-id'));
    })

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
</script>