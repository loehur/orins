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
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-success">Setoran Pecahan</th>
            </tr>
            <?php foreach ($data['split'] as $a) { ?>
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
                        <a class="ajax" href="<?= PV::BASE_URL ?>Audit_KasKecil/verify_kasKecil/<?= $a['id'] ?>/1">Verify</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-danger">Pengeluaran</th>
            </tr>
            <?php foreach ($data['pengeluaran'] as $ref => $keluar) { ?>
                <?php
                $jumlah_keluar = 0;
                foreach ($keluar as $a) { ?>
                    <tr>
                        <td>
                            #<?= $a['id_kas'] ?>
                        </td>
                        <td>
                            <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                        </td>
                        <td>
                            <?= $a['note'] ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($a['jumlah']) ?>
                        </td>
                        <td class="text-end" style="width:200px">
                            <a class="ajax" href="<?= PV::BASE_URL ?>Audit_KasKecil/setor_pengeluaran/<?= $a['id_kas'] ?>/1">Verify</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-success">Riwayat Setoran Pecahan</th>
            </tr>
            <?php foreach ($data['split_done'] as $a) { ?>
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
                </tr>
            <?php } ?>
        </table>
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-danger">Riwayat Pengeluaran</th>
            </tr>
            <?php foreach ($data['pengeluaran_done'] as $ref => $keluar) { ?>
                <?php
                $jumlah_keluar = 0;
                foreach ($keluar as $a) { ?>
                    <tr>
                        <td>
                            #<?= $a['id_kas'] ?>
                        </td>
                        <td>
                            <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                        </td>
                        <td>
                            <?= $a['note'] ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($a['jumlah']) ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
    <div class="row mx-0">
        <div class="col text-end fw-bold pt-2">
            Total Saldo <?= number_format($data['setor']) ?>
        </div>
        <div class="col"><span class="btn btn-primary">Setor</span></div>
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
</script>