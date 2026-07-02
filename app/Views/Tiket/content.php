<?php
$mode = $data['mode'];
$isDev = !empty($data['is_dev']);
$tipeLabel = [1 => 'Perbaikan', 2 => 'Fitur Baru', 3 => 'Usulan'];
?>

<style>
    .tiket-badge-perbaikan {
        background-color: #fff3cd;
        color: #856404;
    }

    .tiket-badge-fitur {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .tiket-badge-usulan {
        background-color: #e2e3f3;
        color: #414497;
    }

    .tiket-row {
        cursor: pointer;
    }

    .tiket-row:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
</style>

<main>
    <div class="container">
        <?php if ($mode === 'proses') { ?>
            <div class="row mx-0 mb-3 align-items-center">
                <div class="col ps-0">
                    <span class="fw-bold text-primary">Tiket Berjalan</span>
                </div>
                <div class="col-auto text-end pe-0">
                    <button type="button" class="btn btn-sm btn-primary bg-gradient" data-bs-toggle="modal" data-bs-target="#modalTiketBaru">
                        <i class="fa-solid fa-plus"></i> Tiket Baru
                    </button>
                </div>
            </div>

            <?php if (count($data['tiket']) === 0) { ?>
                <div class="alert alert-light border text-center">Belum ada tiket berjalan.</div>
            <?php } else { ?>
                <table class="table table-sm table-hover text-sm">
                    <thead>
                        <tr>
                            <th style="width:130px">Tanggal</th>
                            <th>Judul</th>
                            <th style="width:100px">Tipe</th>
                            <th>Karyawan</th>
                            <th>Pembuat</th>
                            <?php if ($isDev) { ?>
                                <th>Toko</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['tiket'] as $t) {
                            $namaKaryawan = $data['karyawan'][$t['id_karyawan']]['nama'] ?? '-';
                            $namaUser = $data['users'][$t['id_user']]['nama'] ?? $data['users'][$t['id_user']]['user'] ?? '-';
                            if ((int) $t['tipe'] === 2) {
                                $badgeClass = 'tiket-badge-fitur';
                            } elseif ((int) $t['tipe'] === 3) {
                                $badgeClass = 'tiket-badge-usulan';
                            } else {
                                $badgeClass = 'tiket-badge-perbaikan';
                            }
                        ?>
                            <tr class="tiket-row" data-id="<?= $t['id_tiket'] ?>">
                                <td><?= date('d/m/y H:i', strtotime($t['insertTime'])) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($t['judul']) ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $tipeLabel[(int) $t['tipe']] ?? '-' ?></span></td>
                                <td><?= htmlspecialchars($namaKaryawan) ?></td>
                                <td><?= htmlspecialchars($namaUser) ?></td>
                                <?php if ($isDev) { ?>
                                    <td><?= htmlspecialchars($this->dToko[$t['id_toko']]['inisial'] ?? $t['id_toko']) ?></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>

            <div class="modal fade" id="modalTiketBaru" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">Buat Tiket Baru</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formTiketBaru" action="<?= PV::BASE_URL ?>Tiket/create" method="POST">
                            <div class="modal-body">
                                <div class="row mb-2">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Karyawan</label>
                                        <select name="id_karyawan" class="form-select form-select-sm" required>
                                            <option value="">Pilih karyawan</option>
                                            <?php foreach ($data['karyawan'] as $k) { ?>
                                                <option value="<?= $k['id_karyawan'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Tipe</label>
                                        <select name="tipe" class="form-select form-select-sm" required>
                                            <option value="1">Perbaikan</option>
                                            <option value="2">Fitur Baru</option>
                                            <option value="3">Usulan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Judul Tiket</label>
                                    <input type="text" name="judul" class="form-control form-control-sm" maxlength="255" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Isi Tiket</label>
                                    <textarea name="isi" class="form-control form-control-sm" rows="8" required></textarea>
                                </div>
                                <div class="small text-muted">
                                    Pembuat: <strong><?= htmlspecialchars($this->userData['nama'] ?? $this->userData['user']) ?></strong>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary bg-gradient">Simpan Tiket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php } else {
            $month = $data['month'];
        ?>
            <div class="row mx-0 mb-3 align-items-center">
                <div class="col ps-0">
                    <span class="btn btn-sm btn-outline-dark" onclick="loadAppContent('<?= PV::BASE_URL ?>Tiket/content/selesai/<?= $month ?>/1')">Back</span>
                    <span class="px-2 text-primary fw-bold"><?= date('F Y', strtotime($month . '-01')) ?></span>
                    <span class="btn btn-sm btn-outline-dark" onclick="loadAppContent('<?= PV::BASE_URL ?>Tiket/content/selesai/<?= $month ?>/2')">Next</span>
                </div>
            </div>

            <?php if (count($data['tiket']) === 0) { ?>
                <div class="alert alert-light border text-center">Tidak ada tiket selesai pada bulan ini.</div>
            <?php } else { ?>
                <table class="table table-sm table-hover text-sm">
                    <thead>
                        <tr>
                            <th style="width:130px">Selesai</th>
                            <th>Judul</th>
                            <th style="width:100px">Tipe</th>
                            <th>Karyawan</th>
                            <th>Pembuat</th>
                            <?php if ($isDev) { ?>
                                <th>Toko</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['tiket'] as $t) {
                            $namaKaryawan = $data['karyawan'][$t['id_karyawan']]['nama'] ?? '-';
                            $namaUser = $data['users'][$t['id_user']]['nama'] ?? $data['users'][$t['id_user']]['user'] ?? '-';
                            if ((int) $t['tipe'] === 2) {
                                $badgeClass = 'tiket-badge-fitur';
                            } elseif ((int) $t['tipe'] === 3) {
                                $badgeClass = 'tiket-badge-usulan';
                            } else {
                                $badgeClass = 'tiket-badge-perbaikan';
                            }
                        ?>
                            <tr class="tiket-row" data-id="<?= $t['id_tiket'] ?>">
                                <td><?= $t['selesai_time'] ? date('d/m/y H:i', strtotime($t['selesai_time'])) : '-' ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($t['judul']) ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $tipeLabel[(int) $t['tipe']] ?? '-' ?></span></td>
                                <td><?= htmlspecialchars($namaKaryawan) ?></td>
                                <td><?= htmlspecialchars($namaUser) ?></td>
                                <?php if ($isDev) { ?>
                                    <td><?= htmlspecialchars($this->dToko[$t['id_toko']]['inisial'] ?? $t['id_toko']) ?></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        <?php } ?>
    </div>
</main>

<div class="modal fade" id="modalTiketDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" id="tiketDetailBody">
            <div class="modal-body text-center py-5 text-muted">Memuat...</div>
        </div>
    </div>
</div>

<script>
    function tiketShowAlert(msg, type) {
        if (typeof showAlert === 'function') {
            showAlert(msg, type || 'danger');
        } else if (typeof showToast === 'function') {
            showToast(msg, type || 'danger');
        } else {
            alert(msg);
        }
    }

    function tiketOpenDetail(id) {
        var $modal = $('#modalTiketDetail');
        $('#tiketDetailBody').html('<div class="modal-body text-center py-5 text-muted">Memuat...</div>');
        bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        $('#tiketDetailBody').load('<?= PV::BASE_URL ?>Tiket/detail/' + id);
    }

    $(document).off('click.tiketRow', '.tiket-row').on('click.tiketRow', '.tiket-row', function() {
        tiketOpenDetail($(this).data('id'));
    });

    $('#formTiketBaru').off('submit.tiketCreate').on('submit.tiketCreate', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type=submit]');
        $btn.prop('disabled', true);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(res) {
                if (res == 0) {
                    bootstrap.Modal.getInstance(document.getElementById('modalTiketBaru')).hide();
                    $form[0].reset();
                    content('proses');
                } else {
                    tiketShowAlert(res, 'danger');
                }
                $btn.prop('disabled', false);
            },
            error: function() {
                tiketShowAlert('Gagal menyimpan tiket.', 'danger');
                $btn.prop('disabled', false);
            }
        });
    });
</script>
