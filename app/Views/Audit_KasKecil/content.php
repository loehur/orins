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
        <?php if (count($data['split']) > 0) { ?>
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
        <?php } ?>
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
                            <?= $data['jkeluar'][$a['ref_transaksi']]['nama'] ?>
                        </td>
                        <td>
                            <?= $a['note'] ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($a['jumlah']) ?>
                        </td>
                        <td class="text-end" style="width:200px">
                            <a class="ajax" href="<?= PV::BASE_URL ?>Audit_KasKecil/setor_pengeluaran/<?= $a['id_kas'] ?>/1"><span class="badge bg-success ">Verify</span></a>
                            <a class="ajax" href="<?= PV::BASE_URL ?>Audit_KasKecil/reimburse/<?= $a['id_kas'] ?>"><span class="badge bg-primary ">Reimburse</span></a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>

        <?php if (count($data['pengeluaran_done']) > 0) { ?>
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
                                <?= $data['jkeluar'][$a['ref_transaksi']]['nama'] ?>
                            </td>
                            <td>
                                <?= $a['note'] ?>
                            </td>
                            <td class="text-end">
                                <?= number_format($a['jumlah']) ?>
                            </td>
                            <td><a class="ajax" href="<?= PV::BASE_URL ?>Audit_KasKecil/reimburse/<?= $a['id_kas'] ?>"><span class="badge bg-primary ">Reimburse</span></a></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        <?php } ?>

        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-primary">Riwayat Reimburse</th>
            </tr>
            <?php foreach ($data['reim_done'] as $a) { ?>
                <tr>
                    <td>
                        #<?= $a['id'] ?>
                    </td>
                    <td>
                        <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                    </td>
                    <td>
                        <?= $data['jkeluar'][$a['id_target']]['nama'] ?>
                    </td>
                    <td>
                        <?= $a['note'] ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="row mx-0">
            <div class="col text-end fw-bold pt-2">
                Total Saldo <?= number_format($data['setor']) ?>
            </div>
            <div class="col"><span id="setor" class="btn btn-primary">Setor</span></div>
        </div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("#setor").dblclick(function() {
        $.ajax({
            url: "<?= PV::BASE_URL ?>Audit_KasKecil/setor",
            data: [],
            type: "POST",
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result);
                }
            },
        });
    })

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