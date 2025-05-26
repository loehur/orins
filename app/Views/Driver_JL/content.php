<main class="container px-2" style="max-width: 500px;">
    <div>
        <?php foreach ($data['jl_pro'] as $key => $jl_pro) { ?>
            <?php
            $ex = explode("#", $key);
            $id_afiliasi = $ex[0];
            $id_toko = $ex[1];

            ?>
            <div class="w-100 text-center mt-2 border rounded bg-light bg-gradient fw-bold text-sm">
                <label class="border-0 rounded-0" style="margin:0;color:<?= $this->dToko[$id_afiliasi]['color'] ?>"><?= $this->dToko[$id_afiliasi]['inisial'] ?></label> &nbsp;&nbsp;<i class="text-sm fa-solid fa-arrow-right text-secondary"></i>&nbsp;&nbsp; <label class="border-0 rounded-0" style="margin:0;color:<?= $this->dToko[$id_toko]['color'] ?>"><?= $this->dToko[$id_toko]['inisial'] ?></label>
            </div>
            <div class="mb-4">
                <?php foreach ($data['ref_pro'][$key] as $ref => $a) { ?>
                    <div class="row py-1 mx-1 border-bottom" id="R<?= $ref ?>">
                        <div class="col-auto pe-1" style="width:70px">
                            <?= strtoupper($this->dKaryawanAll[$a['cs']]['nama']) ?>
                            <br>
                            <small><?= substr($ref, -4) ?></small>
                        </div>
                        <div class="col pe-1">
                            <?= strtoupper($this->dPelangganAll[$a['id_pelanggan']]['nama']) ?> <small>#<?= substr($a['id_pelanggan'], -2) ?></small>
                            <br>
                            <?= $a['qty'] ?>pcs
                        </div>
                        <?php if (in_array($this->userData['user_tipe'], [0, 9])) { ?>
                            <div class="col-auto pe-1 pt-1"><span class="btn btn-sm btn-success bg-gradient py-2 btnAmbilSemua" data-bs-toggle="modal" data-bs-target="#exampleModal3" data-ref="<?= $ref ?>" data-mode="0" data-id_toko="<?= $id_afiliasi ?>">Done</span></div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php foreach ($data['jl_exp'] as $key => $b) { ?>
            <?php
            $ex = explode("#", $key);
            $id_toko = $ex[0];
            $id_exp = $ex[1];

            ?>
            <div class="w-100 text-center mt-2 border rounded bg-light bg-gradient fw-bold text-sm">
                <label class="border-0 rounded-0" style="margin:0;color:<?= $this->dToko[$id_toko]['color'] ?>"><?= $this->dToko[$id_toko]['inisial'] ?></label> &nbsp;&nbsp;<i class="text-sm fa-solid fa-arrow-right text-secondary"></i>&nbsp;&nbsp; <label class="border-0 rounded-0 text-dark"><?= $data['ea'][$id_exp]['name'] ?></label>
            </div>
            <div class="mb-4">
                <?php foreach ($b as $ref => $a) { ?>
                    <div class="row py-1 mx-1 border-bottom" id="R<?= $ref ?>">
                        <div class="col-auto pe-1">
                            <?= strtoupper($this->dKaryawanAll[$a['cs']]['nama']) ?>
                            <br>
                            <small><?= substr($ref, -4) ?></small>
                        </div>
                        <div class="col pe-1">
                            <?= strtoupper($this->dPelangganAll[$a['id_pelanggan']]['nama']) ?> <small>#<?= substr($a['id_pelanggan'], -2) ?></small>
                            <br>
                            <?= $a['qty'] ?>pcs
                        </div>
                        <?php if (in_array($this->userData['user_tipe'], [0, 9])) { ?>
                            <div class="col-auto pe-1 pt-1"><span class="btn btn-sm btn-success bg-gradient py-2 btnAmbilSemua" data-bs-toggle="modal" data-bs-target="#exampleModal3" data-ref="<?= $ref ?>" data-mode="0" data-id_toko="<?= $id_toko ?>">Done</span></div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php foreach ($data['jl_ts'] as $key => $b) { ?>
            <?php
            $ex = explode("#", $key);
            $id_target = $ex[1];

            ?>
            <div class="w-100 text-center mt-2 border rounded bg-light bg-gradient fw-bold text-sm">
                <label class="border-0 rounded-0 text-dark">GUDANG</label> &nbsp;&nbsp;<i class="text-sm fa-solid fa-arrow-right text-secondary"></i>&nbsp;&nbsp; <label style="margin:0;color:<?= $this->dToko[$id_target]['color'] ?>" class="border-0 rounded-0"><?= $this->dToko[$id_target]['inisial'] ?></label>
            </div>
            <div class="mb-4">
                <?php foreach ($b as $ref => $a) { ?>
                    <div class="row py-1 mx-1 border-bottom" id="R<?= $ref ?>">
                        <div class="col-auto pe-1">
                            GUDANG
                            <br>
                            <small><?= substr($ref, -4) ?></small>
                        </div>
                        <div class="col pe-1">
                            <?= strtoupper($this->dToko[$a['id_pelanggan']]['inisial']) ?><br>
                            <?= $a['qty'] ?>pcs
                        </div>
                        <?php if (in_array($this->userData['user_tipe'], [0, 9])) { ?>
                            <div class="col-auto pe-1 pt-1"><span class="btn btn-sm btn-success bg-gradient py-2 btnAmbilSemua" data-bs-toggle="modal" data-bs-target="#exampleModal3" data-ref="<?= $ref ?>" data-mode="1" data-id_toko="<?= $id_toko ?>">Done</span></div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</main>

<form class="ajax" action="<?= PV::BASE_URL; ?>Data_Order/ambil_semua" method="POST">
    <div class="modal" id="exampleModal3">
        <div class="modal-dialog">
            <div class="modal-content" style="min-height:400px">
                <div class="modal-header">
                    <h5 class="modal-title">Data Pengantaran</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Pengantar</label>
                            <select class="form-select tize" name="id_driver" required>
                                <option value="0" selected></option>
                                <?php foreach ($this->dKaryawanAll_driver as $k) { ?>
                                    <option value="<?= $k['id_karyawan'] ?>"><?= ucwords($k['nama']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">CS Penyedia</label>
                            <input type="hidden" name="ambil_ref">
                            <input type="hidden" name="id_toko">
                            <input type="hidden" name="mode">
                            <select class="form-select tize" name="id_karyawan" required>
                                <option></option>
                                <?php foreach ($this->dKaryawanAll_cs as $k) { ?>
                                    <option value="<?= $k['id_karyawan'] ?>"><?= ucwords($k['nama']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <button type="submit" data-bs-dismiss="modal" class="btn btn-primary bg-gradient w-100 py-2">Selesai</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });
    var ref;

    $("span.btnAmbilSemua").click(function() {
        ref = $(this).attr("data-ref");
        var id_toko = $(this).attr("data-id_toko");
        var mode = $(this).attr("data-mode");
        $("input[name=ambil_ref]").val(ref);
        $("input[name=id_toko]").val(id_toko);
        $("input[name=mode]").val(mode);
    })

    $("form.ajax").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    $("div#R" + ref).fadeOut('fast');
                } else {
                    alert(res);
                }
            }
        });
    });
</script>