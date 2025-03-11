<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<div class="row mx-0 mt-4">
    <div class="col" style="max-width: 500px;">
        <div class="row border-bottom pb-2">
            <div class="col">Saldo</div>
            <div class="col text-end">Rp <span class="fw-bold"><?= number_format($data['saldo']) ?></span></div>
            <div class="col text-end">
                <span class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal">Topup</span>
            </div>
        </div>
    </div>
</div>
<div class="row mx-0 mt-2 text-sm">
    <div class="col" style="max-width: 500px;">
        <label class="text-success">Riwayat Topup</label>
        <?php foreach ($data['data'] as $d) { ?>
            <?php
            $cs = $this->dKaryawanAll[$d['id_user']]['nama'];
            ?>
            <div class="row border-bottom">
                <div class="col-auto">
                    <?= substr($d['insertTime'], 0, 10) ?><br>
                    <small><?= $d['jenis_mutasi'] == 1 ? 'Topup' : 'Refund' ?></small>
                </div>
                <div class="col">
                    <i class="fa-solid fa-user-pen"></i> <?= $cs ?>
                    <br>
                    <span><?= $d['note'] ?></span>
                </div>
                <div class="col text-end">
                    <?= number_format($d['jumlah']) ?><br>
                    <span class="text-sm">
                        <?= $d['status_mutasi'] == 1 ? '<span class="text-success">Sukses</span>' : '<span class="text-warning">Office Checking</span>'  ?> - <?= $d['metode_mutasi'] == 1 ? 'Tunai' : 'NonTunai' ?>
                    </span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="row mx-0 mt-2 text-sm">
    <div class="col" style="max-width: 500px;">
        <label class="text-primary">Riwayat Pakai</label>
        <?php foreach ($data['mutasi'] as $d) { ?>
            <div class="row border-bottom">
                <div class="col-auto">
                    <?= $d['ref_transaksi'] ?>
                </div>
                <div class="col text-end">
                    <?= number_format($d['jumlah']) ?>
                </div>
                <div class="col-auto">
                    <?php if ($d['status_mutasi'] == 1) { ?>
                        <a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="px-2 text-decoration-none text-danger cancel rounded" data-id="<?= $d['id_kas'] ?>" href="#"><i class="fa-solid fa-square-xmark"></i></a>
                    <?php } else { ?>
                        <small class="text-danger"><?= $d['note_batal'] ?></small>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<form action="<?= PV::BASE_URL ?>Deposit/topup/<?= $data['id_pelanggan'] ?>" method="POST">
    <div class="modal" id="modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Topup Deposit</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Customer Service</label>
                                <select class="tize form-control form-control-sm" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($this->dKaryawan as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control form-control-sm text-end" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Metode</label>
                                <select name="metode" class="form-select metodeBayar" required>
                                    <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                        <option value="1">Tunai</option>
                                    <?php } ?>
                                    <option value="2">Non Tunai</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label"><span>Catatan Transaksi</span></label>
                                <input type="text" required name="catatan" class="form-control form-control-sm border-warning">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-success bg-gradient">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Setoran/cancel" method="POST">
    <div class="modal" id="exampleModalCancel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pembatalan!</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Alasan Cancel</label>
                                <input type="text" name="reason" class="form-control form-control-sm" required>
                                <input type="hidden" name="id_kas">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Cancel</button>
                            </div>
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
    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    $('.cek').click();
                } else {
                    alert(res);
                }
            }
        });
    });

    $("a.cancel").click(function() {
        id = $(this).attr("data-id");
        $("input[name=id_kas]").val(id);
    })

    $(document).ready(function() {
        $('select.tize').selectize();
    });
</script>