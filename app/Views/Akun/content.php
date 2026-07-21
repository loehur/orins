<?php
$hasPin = !empty($data['has_pin']);
$tokoNama = $this->dToko[$this->userData['id_toko']]['nama_toko'] ?? '-';
?>

<style>
    .akun-hero {
        background: linear-gradient(135deg, #0061f2 0%, #6900f2 55%, #00ac69 100%);
        border-radius: 1rem;
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .akun-hero::after {
        content: "";
        position: absolute;
        right: -2rem;
        top: -2rem;
        width: 10rem;
        height: 10rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .akun-avatar {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border: 2px solid rgba(255, 255, 255, 0.25);
    }

    .akun-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 0.35rem 1.25rem rgba(15, 23, 42, 0.08);
    }

    .akun-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        font-weight: 600;
    }

    .akun-pin-display {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 0.65rem;
        color: #0061f2;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }

    .akun-pin-dot {
        width: 0.85rem;
        height: 0.85rem;
        border-radius: 50%;
        background: #0061f2;
        display: inline-block;
    }
</style>

<main>
    <div class="container-xl px-4 py-4">
        <div class="akun-hero p-4 mb-4 position-relative">
            <div class="row align-items-center g-3 position-relative" style="z-index:1;">
                <div class="col-auto">
                    <div class="akun-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
                <div class="col">
                    <div class="small text-white-50 mb-1">Pengaturan Akun</div>
                    <h4 class="mb-1"><?= htmlspecialchars($this->userData['nama']) ?></h4>
                    <div class="small">
                        <span class="me-3"><i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($this->userData['user']) ?></span>
                        <span><i class="fa-solid fa-store me-1"></i><?= htmlspecialchars($tokoNama) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card akun-card h-100">
                    <div class="card-header d-flex align-items-center gap-2 py-3">
                        <i class="fa-solid fa-lock text-primary"></i>
                        <span>Ubah Password</span>
                    </div>
                    <div class="card-body">
                        <form id="formPass" action="<?= PV::BASE_URL ?>Akun/updatePass" method="post">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Password Lama</label>
                                <input type="password" class="form-control" name="pass" required autocomplete="current-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Password Baru</label>
                                <input type="password" class="form-control" name="pass_" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Ulangi Password Baru</label>
                                <input type="password" class="form-control" name="pass__" required autocomplete="new-password">
                            </div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card akun-card h-100">
                    <div class="card-header d-flex align-items-center gap-2 py-3">
                        <i class="fa-solid fa-key text-warning"></i>
                        <span>PIN Akses</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <?php if ($hasPin) { ?>
                            <div class="rounded-3 bg-light p-3 mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-success">PIN Aktif</span>
                                </div>
                                <div class="d-flex gap-2 mb-2">
                                    <span class="akun-pin-dot"></span>
                                    <span class="akun-pin-dot"></span>
                                    <span class="akun-pin-dot"></span>
                                    <span class="akun-pin-dot"></span>
                                </div>
                                <small class="text-muted">PIN tidak ditampilkan demi keamanan. Jika lupa, generate ulang PIN baru.</small>
                            </div>
                        <?php } else { ?>
                            <div class="rounded-3 bg-light p-3 mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-secondary">Belum Diatur</span>
                                </div>
                                <p class="small text-muted mb-0">PIN 4 digit untuk verifikasi cepat. Setelah dibuat, PIN hanya ditampilkan satu kali.</p>
                            </div>
                        <?php } ?>

                        <div class="mt-auto">
                            <button type="button" class="btn btn-outline-warning px-4" id="btnOpenPinModal">
                                <i class="fa-solid fa-rotate me-1"></i>
                                <?= $hasPin ? 'Generate Ulang PIN' : 'Generate PIN' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalPinConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="fa-solid fa-shield-halved text-warning me-2"></i>
                    <?= $hasPin ? 'Generate Ulang PIN' : 'Generate PIN' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($hasPin) { ?>
                    <div class="alert alert-warning py-2 small mb-3">
                        PIN lama akan diganti. PIN baru hanya ditampilkan sekali setelah proses ini.
                    </div>
                <?php } else { ?>
                    <p class="small text-muted mb-3">Sistem akan membuat PIN 4 digit secara acak. Masukkan password akun Anda untuk melanjutkan.</p>
                <?php } ?>
                <form id="formPin">
                    <label class="form-label small text-muted">Password Akun</label>
                    <input type="password" class="form-control" name="pass" id="pinConfirmPass" required autocomplete="current-password">
                    <div class="invalid-feedback" id="pinConfirmError"></div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnPinSubmit">
                    <i class="fa-solid fa-key me-1"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPinResult" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-gradient text-white border-0">
                <h5 class="modal-title">
                    <i class="fa-solid fa-circle-check me-2"></i> PIN Berhasil Dibuat
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="small text-muted mb-2">PIN Anda</div>
                <div class="akun-pin-display mb-3" id="pinResultValue">----</div>
                <div class="alert alert-danger py-2 small mb-0 text-start">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    <strong>Penting:</strong> PIN ini hanya ditampilkan sekali. Simpan sekarang. Jika lupa, Anda wajib generate ulang PIN baru.
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-primary px-4" id="btnPinDone">Saya Sudah Mencatat PIN</button>
            </div>
        </div>
    </div>
</div>

<script>
    var pinConfirmModal = null;
    var pinResultModal = null;

    function resetPinConfirmModal() {
        $('#formPin')[0].reset();
        $('#pinConfirmPass').removeClass('is-invalid');
        $('#pinConfirmError').text('');
        $('#btnPinSubmit').prop('disabled', false).html('<i class="fa-solid fa-key me-1"></i> Generate');
    }

    $("form#formPass").on("submit", function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true);
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    alert("Password berhasil diperbarui.");
                    content();
                } else {
                    alert(res);
                }
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    $('#btnOpenPinModal').on('click', function() {
        resetPinConfirmModal();
        pinConfirmModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPinConfirm'));
        pinConfirmModal.show();
        setTimeout(function() {
            $('#pinConfirmPass').trigger('focus');
        }, 250);
    });

    $('#btnPinSubmit').on('click', function() {
        var pass = $('#pinConfirmPass').val();
        if (!pass) {
            $('#pinConfirmPass').addClass('is-invalid');
            $('#pinConfirmError').text('Password wajib diisi.');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

        $.ajax({
            url: '<?= PV::BASE_URL ?>Akun/generatePin',
            data: { pass: pass },
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res && res.ok == 1) {
                    if (pinConfirmModal) {
                        pinConfirmModal.hide();
                    }
                    $('#pinResultValue').text(res.pin);
                    pinResultModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPinResult'));
                    pinResultModal.show();
                } else {
                    $('#pinConfirmPass').addClass('is-invalid');
                    $('#pinConfirmError').text((res && res.error) ? res.error : 'Gagal generate PIN.');
                    btn.prop('disabled', false).html('<i class="fa-solid fa-key me-1"></i> Generate');
                }
            },
            error: function() {
                $('#pinConfirmPass').addClass('is-invalid');
                $('#pinConfirmError').text('Gagal memproses permintaan.');
                btn.prop('disabled', false).html('<i class="fa-solid fa-key me-1"></i> Generate');
            }
        });
    });

    $('#pinConfirmPass').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#pinConfirmError').text('');
    });

    $('#formPin').on('submit', function(e) {
        e.preventDefault();
        $('#btnPinSubmit').trigger('click');
    });

    $('#btnPinDone').on('click', function() {
        if (pinResultModal) {
            pinResultModal.hide();
        }
        $('#pinResultValue').text('----');
        content();
    });

    $('#modalPinConfirm').on('hidden.bs.modal', function() {
        resetPinConfirmModal();
    });
</script>
