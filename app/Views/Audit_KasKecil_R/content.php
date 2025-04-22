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
        <?php if (count($data['kas_kecil']) > 0) { ?>
            <table class="table table-sm text-sm">
                <?php foreach ($data['kas_kecil'] as $a) { ?>
                    <tr>
                        <td>
                            <?= $a['ref_setoran'] ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($a['jumlah']) ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>