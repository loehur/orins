<?php
$t = $data['title'];
$total = (int) $data['aff_c'] + (int) $data['lanjut_c'];
?>
<div id="menuPrioritasItems" data-count="<?= $total ?>">
<?php if ($data['show_aff']) { ?>
    <div class="nav-link py-1 text-muted text-uppercase" style="font-size: 11px; pointer-events: none;">Afiliasi Order</div>
    <?php if ($data['aff_c'] > 0) { ?>
        <?php foreach ($data['aff'] as $af) {
            if (!isset($af['id_toko'], $af['ref'])) {
                continue;
            }
            $toko = $this->dToko[$af['id_toko']]['inisial'] ?? $af['id_toko'];
            if (isset($this->dPelangganAll[$af['id_pelanggan']])) {
                $pelanggan = $this->dPelangganAll[$af['id_pelanggan']]['nama'];
            } else {
                $pelanggan = $af['id_pelanggan'];
            } ?>
            <a class="nav-link py-1 <?= ($t == "Afiliasi Order - " . $af['ref']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>Buka_Order_Aff/index/<?= $af['ref'] ?>"><?= $toko ?> #<?= strtoupper($pelanggan) ?></a>
        <?php } ?>
    <?php } else { ?>
        <span class="nav-link py-1 text-muted ps-3">Belum ada order afiliasi</span>
    <?php } ?>
<?php } ?>

<?php if ($data['show_spk']) { ?>
    <div class="nav-link py-1 text-muted text-uppercase" style="font-size: 11px; pointer-events: none;">SPK Prioritas</div>
    <?php if ($data['lanjut_c'] > 0) { ?>
        <?php foreach ($data['list_l'] as $sl) {
            if (!isset($this->dDvs[$sl])) {
                continue;
            } ?>
            <a class="nav-link py-1 <?= ($t == "SPK - Lanjutan " . $this->dDvs[$sl]['divisi']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>SPK_L/index/<?= $sl ?>"><?= $this->dDvs[$sl]['divisi'] ?></a>
        <?php } ?>
    <?php } else { ?>
        <span class="nav-link py-1 text-muted ps-3">Belum ada SPK prioritas</span>
    <?php } ?>
<?php } ?>
</div>
