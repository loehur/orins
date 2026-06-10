<?php
$t = $data['title'];
$total = (int) $data['aff_c'] + (int) $data['lanjut_c'];
?>
<div id="menuPrioritasItems" data-count="<?= $total ?>">
<?php if ($data['aff_c'] > 0) { ?>
    <div class="nav-link py-1 text-muted text-uppercase" style="font-size: 11px; pointer-events: none;">Afiliasi Order</div>
    <?php foreach ($data['aff'] as $af) {
        $toko = $this->dToko[$af['id_toko']]['inisial'];
        if (isset($this->dPelangganAll[$af['id_pelanggan']])) {
            $pelanggan = $this->dPelangganAll[$af['id_pelanggan']]['nama'];
        } else {
            $pelanggan = $af['id_pelanggan'];
        } ?>
        <a class="nav-link py-1 <?= ($t == "Afiliasi Order - " . $af['ref']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>Buka_Order_Aff/index/<?= $af['ref'] ?>"><?= $toko ?> #<?= strtoupper($pelanggan) ?></a>
    <?php } ?>
<?php } ?>

<?php if ($data['lanjut_c'] > 0) { ?>
    <div class="nav-link py-1 text-muted text-uppercase" style="font-size: 11px; pointer-events: none;">SPK Prioritas</div>
    <?php foreach ($data['list_l'] as $sl) { ?>
        <a class="nav-link py-1 <?= ($t == "SPK - Lanjutan " . $this->dDvs[$sl]['divisi']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>SPK_L/index/<?= $sl ?>"><?= $this->dDvs[$sl]['divisi'] ?></a>
    <?php } ?>
<?php } ?>

<?php if ($total === 0) { ?>
    <span class="nav-link py-1 text-muted">Tidak ada prioritas</span>
<?php } ?>
</div>
