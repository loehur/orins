<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <?php
    $total_setor = 0;
    ?>
    <!-- Main page content-->
    <div class="container">
        <div class="row mx-0">
            <div class="col text-sm text-end fw-bold pe-0">
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
                    <td class="text-end" style="width:70px">
                        <?php if ($a['st'] == 0) { ?>
                            <span class="text-sm text-warning">Checking</span>
                        <?php } else { ?>
                            <?php if ($a['st'] == 1) { ?>
                                <span class="text-sm text-success">Verified</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-danger">Pemakaian</th>
            </tr>
            <?php
            foreach ($data['pakai'] as $a) {
                if ($a['st'] == 1) {
                    $total_setor += $a['jumlah'];
                } ?>
                <tr>
                    <td class="align-middle">
                        <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                    </td>
                    <td>
                        <span class='fw-bold text-danger'><i class='fa-solid fa-arrow-right'></i></span> <?= $data['jkeluar'][$a['id_target']]['nama'] ?>
                    </td>
                    <td>
                        <?= $a['note'] ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                    <td class="text-end" style="width:70px">
                        <?php if ($a['st'] == 0) { ?>
                            <a class="ajax" href="<?= PV::BASE_URL ?>Petty_Cash_F/verify/<?= $a['id'] ?>/1">Verify</a>
                        <?php } else { ?>
                            <?php if ($a['st'] == 1) { ?>
                                <span class="text-sm text-success">Verified</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

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
                    content();
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
</script>