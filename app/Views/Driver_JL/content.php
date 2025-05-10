<main class="container px-2">
    <div>
        <?php foreach ($data['jl_pro'] as $key => $jl_pro) { ?>
            <?php
            $ex = explode("#", $key);
            $id_afiliasi = $ex[0];
            $id_toko = $ex[1];

            ?>
            <div class="w-100 text-center mt-2">
                <label class="border-0 rounded-0" style="margin:0;color:<?= $this->dToko[$id_afiliasi]['color'] ?>"><?= $this->dToko[$id_afiliasi]['inisial'] ?></label> &nbsp;&nbsp;<i class="text-sm fa-solid fa-arrow-right text-secondary"></i>&nbsp;&nbsp; <label class="border-0 rounded-0" style="margin:0;color:<?= $this->dToko[$id_toko]['color'] ?>"><?= $this->dToko[$id_toko]['inisial'] ?></label>
            </div>
            <div class="border-top px-2 mb-4">
                <?php foreach ($data['ref_pro'][$key] as $ref => $a) { ?>
                    <div class="row">
                        <div class="col-auto pe-1">
                            <?= substr($ref, -4) ?>
                        </div>
                        <div class="col-auto pe-1" style="width: 70px;">
                            <?= strtoupper($this->dKaryawanAll[$a['cs']]['nama']) ?>
                        </div>
                        <div class="col pe-1">
                            <?= strtoupper($this->dPelangganAll[$a['id_pelanggan']]['nama']) ?> <small># <?= substr($a['id_pelanggan'], -2) ?></small>
                        </div>
                        <div class="col-auto text-end">
                            <?= $a['qty'] ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</main>