<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<div class="row mx-0 mt-3 g-3">
    <!-- Saldo -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <span class="text-muted small">Saldo Deposit</span>
                    <span class="fs-5 fw-bold text-success">Rp <?= number_format($data['saldo']) ?></span>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal">
                        <i class="fa-solid fa-plus me-1"></i> Topup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Topup -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-transparent border-bottom py-2">
                <label class="text-success mb-0 fw-semibold">
                    <i class="fa-solid fa-wallet me-1"></i> Riwayat Topup
                </label>
            </div>
            <div class="card-body p-0 flex-grow-1 overflow-auto" style="max-height: 320px;">
                <?php if (empty($data['data'])) { ?>
                    <div class="text-muted text-center py-4 small">Belum ada riwayat topup</div>
                <?php } else { ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($data['data'] as $d) {
                            $cs = $this->dKaryawanAll[$d['id_user']]['nama'];
                        ?>
                            <div class="list-group-item list-group-item-action border-0 py-2 px-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <span class="small text-muted"><?= substr($d['insertTime'], 0, 10) ?></span>
                                        <span class="badge bg-light text-dark ms-1"><?= $d['jenis_mutasi'] == 1 ? 'Topup' : 'Refund' ?></span>
                                        <div class="mt-1">
                                            <i class="fa-solid fa-user-pen text-muted me-1"></i>
                                            <span class="small"><?= $cs ?></span>
                                        </div>
                                        <?php if (!empty($d['note'])) { ?>
                                            <div class="small text-muted"><?= $d['note'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-semibold"><?= number_format($d['jumlah']) ?></span>
                                        <div class="small mt-1">
                                            <?php if ($d['status_mutasi'] == 1) { ?>
                                                <span class="text-success">Success</span>
                                            <?php } elseif ($d['status_mutasi'] == 0) { ?>
                                                <span class="text-warning">Checking</span>
                                            <?php } else { ?>
                                                <span class="text-danger">Rejected</span>
                                                <?php if (!empty($d['note_batal'])) { ?>
                                                    <span class="d-block small">(<?= $d['note_batal'] ?>)</span>
                                                <?php } ?>
                                            <?php } ?>
                                            <span class="badge bg-secondary ms-1"><?= $d['metode_mutasi'] == 1 ? 'Tunai' : 'NonTunai' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Riwayat Pakai -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-transparent border-bottom py-2">
                <label class="text-primary mb-0 fw-semibold">
                    <i class="fa-solid fa-receipt me-1"></i> Riwayat Pakai
                </label>
            </div>
            <div class="card-body p-0 flex-grow-1 overflow-auto" style="max-height: 320px;">
                <?php if (empty($data['mutasi'])) { ?>
                    <div class="text-muted text-center py-4 small">Belum ada riwayat pakai</div>
                <?php } else { ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($data['mutasi'] as $d) { ?>
                            <div class="list-group-item list-group-item-action border-0 py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="<?= PV::BASE_URL ?>Cek/order/<?= $d['ref_transaksi'] ?>/<?= $data['id_pelanggan'] ?>" target="_blank" class="text-primary text-decoration-none fw-medium" title="Cek transaksi"><?= $d['ref_transaksi'] ?></a>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-primary fw-semibold"><?= number_format($d['jumlah']) ?></span>
                                        <?php if (isset($data['refs'][$d['ref_transaksi']])) { ?>
                                            <?php if ($d['status_mutasi'] == 1) { ?>
                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                                    <a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="btn btn-sm btn-outline-danger cancel text-decoration-none" data-id="<?= $d['id_kas'] ?>" href="#" title="Batalkan"><i class="fa-solid fa-square-xmark"></i></a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <small class="text-danger"><?= $d['note_batal'] ?></small>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
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
                                    <?php foreach ($this->dKaryawan_cs as $k) { ?>
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

<form action="<?= PV::BASE_URL ?>Deposit/cancel" method="POST">
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
    function showToast(message, type) {
        type = type || 'warning';
        var container = document.querySelector('.toast-container');
        if (!container) return;
        var bgClass = type === 'danger' ? 'bg-danger text-white' : type === 'success' ? 'bg-success text-white' : 'bg-warning text-dark';
        var icon = type === 'danger' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center border-0 shadow ' + bgClass;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = '<div class="d-flex">' +
            '<div class="toast-body d-flex align-items-center">' +
            '<i class="fas ' + icon + ' me-2 fs-5 flex-shrink-0"></i>' +
            '<span>' + message + '</span>' +
            '</div>' +
            '<button type="button" class="btn-close ' + (type === 'warning' ? '' : 'btn-close-white') + ' me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>';

        container.appendChild(toastEl);
        var toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toastEl.addEventListener('hidden.bs.toast', function() { toastEl.remove(); });
        toast.show();
    }

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    showToast('Berhasil!', 'success');
                    var modalEl = document.getElementById('modal');
                    var modalCancel = document.getElementById('exampleModalCancel');
                    if (modalCancel) {
                        var m = bootstrap.Modal.getInstance(modalCancel);
                        if (m) m.hide();
                    }
                    if (modalEl) {
                        var m2 = bootstrap.Modal.getInstance(modalEl);
                        if (m2) m2.hide();
                    }
                    $('.cek').click();
                } else {
                    showToast(res, 'danger');
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