<?php
$mode = $data['mode'];
$isDev = !empty($data['is_dev']);
$tipeLabel = [1 => 'Perbaikan', 2 => 'Fitur Baru', 3 => 'Usulan'];
?>

<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

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

    #modalTiketConfirm {
        z-index: 1065;
    }

    #modalTiketConfirm + .modal-backdrop {
        z-index: 1060;
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

            <div id="tiketProsesWrap">
                <table class="table table-sm table-hover text-sm <?= count($data['tiket']) === 0 ? 'd-none' : '' ?>" id="tiketProsesTable">
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
                    <tbody id="tiketProsesList">
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
                <div class="alert alert-light border text-center <?= count($data['tiket']) > 0 ? 'd-none' : '' ?>" id="tiketProsesEmpty">Belum ada tiket berjalan.</div>
            </div>

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
                                        <select class="tize shadow-none" name="id_karyawan" id="tiketKaryawan" required>
                                            <option value="">CS Name</option>
                                            <?php foreach ($data['karyawan_form'] as $k) { ?>
                                                <option value="<?= $k['id_karyawan'] ?>"><?= strtoupper($k['nama']) ?></option>
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
                                <button type="submit" class="btn btn-primary bg-gradient" id="btnTiketSimpan">Simpan Tiket</button>
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

<div class="modal fade" id="modalTiketConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success bg-gradient text-white border-0" id="modalTiketConfirmHeader">
                <h6 class="modal-title d-flex align-items-center gap-2 mb-0" id="modalTiketConfirmTitle">
                    <i class="fa-solid fa-circle-check"></i> Konfirmasi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center" id="modalTiketConfirmText"></div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-success px-3" id="modalTiketConfirmYes">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script>
    var tiketCreateSubmitting = false;
    var tiketReplySubmitting = false;
    var tiketIsDev = <?= !empty($isDev) ? 'true' : 'false' ?>;

    function tiketEscapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function tiketParseRes(res) {
        if (typeof res === 'object') {
            return res;
        }
        try {
            return JSON.parse(res);
        } catch (e) {
            if (res == 0) {
                return { ok: 1 };
            }
            return { ok: 0, error: res };
        }
    }

    function tiketCleanupModalBackdrop() {
        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
        $('.modal-backdrop').remove();
    }

    function tiketCloseModal(modalId, done) {
        var modalEl = document.getElementById(modalId);
        if (!modalEl) {
            tiketCleanupModalBackdrop();
            if (typeof done === 'function') {
                done();
            }
            return;
        }

        var inst = bootstrap.Modal.getInstance(modalEl);
        if (!inst) {
            tiketCleanupModalBackdrop();
            if (typeof done === 'function') {
                done();
            }
            return;
        }

        $(modalEl).one('hidden.bs.modal.tiketClose', function() {
            tiketCleanupModalBackdrop();
            if (typeof done === 'function') {
                done();
            }
        });
        inst.hide();
    }

    function tiketCloseModalBaru(done) {
        tiketCloseModal('modalTiketBaru', done);
    }

    function tiketCloseModalDetail(done) {
        tiketCloseModal('modalTiketDetail', done);
    }

    function tiketBuildRow(t) {
        var cols = '<td>' + tiketEscapeHtml(t.waktu) + '</td>'
            + '<td class="fw-bold">' + tiketEscapeHtml(t.judul) + '</td>'
            + '<td><span class="badge ' + tiketEscapeHtml(t.badge_class) + '">' + tiketEscapeHtml(t.tipe_label) + '</span></td>'
            + '<td>' + tiketEscapeHtml(t.karyawan) + '</td>'
            + '<td>' + tiketEscapeHtml(t.pembuat) + '</td>';
        if (tiketIsDev) {
            cols += '<td>' + tiketEscapeHtml(t.toko) + '</td>';
        }
        return '<tr class="tiket-row" data-id="' + t.id_tiket + '">' + cols + '</tr>';
    }

    function tiketPrependRow(t) {
        $('#tiketProsesEmpty').addClass('d-none');
        $('#tiketProsesTable').removeClass('d-none');
        $('#tiketProsesList').prepend(tiketBuildRow(t));
    }

    function tiketRemoveRow(id) {
        $('.tiket-row[data-id="' + id + '"]').remove();
        if ($('#tiketProsesList tr').length === 0) {
            $('#tiketProsesTable').addClass('d-none');
            $('#tiketProsesEmpty').removeClass('d-none');
        }
    }

    function tiketBuildReplyHtml(reply) {
        var replyClass = reply.is_dev ? 'tiket-reply-dev' : 'tiket-reply-user';
        var devBadge = reply.is_dev ? '<span class="badge bg-primary ms-1">Dev</span>' : '';
        return '<div class="p-2 mb-2 rounded ' + replyClass + '">'
            + '<div class="small fw-bold mb-1">' + tiketEscapeHtml(reply.nama) + devBadge
            + '<span class="text-muted fw-normal">· ' + tiketEscapeHtml(reply.waktu) + '</span></div>'
            + '<div class="tiket-isi small">' + tiketEscapeHtml(reply.isi) + '</div></div>';
    }

    function tiketAppendReply(reply) {
        $('#tiketReplyEmpty').addClass('d-none');
        $('#tiketReplyList').append(tiketBuildReplyHtml(reply));
    }

    function initTiketKaryawanSelectize() {
        var el = document.getElementById('tiketKaryawan');
        if (!el || el.selectize) {
            return;
        }
        $(el).selectize();
    }

    function resetTiketKaryawanSelectize() {
        var el = document.getElementById('tiketKaryawan');
        if (el && el.selectize) {
            el.selectize.clear(true);
        }
    }

    function tiketDestroyKaryawanSelectize() {
        var el = document.getElementById('tiketKaryawan');
        if (el && el.selectize) {
            el.selectize.destroy();
        }
    }

    $(document).off('shown.bs.modal.tiketBaru', '#modalTiketBaru').on('shown.bs.modal.tiketBaru', '#modalTiketBaru', initTiketKaryawanSelectize);
    $(document).off('hidden.bs.modal.tiketBaru', '#modalTiketBaru').on('hidden.bs.modal.tiketBaru', '#modalTiketBaru', tiketCleanupModalBackdrop);
    $(document).off('hidden.bs.modal.tiketDetail', '#modalTiketDetail').on('hidden.bs.modal.tiketDetail', '#modalTiketDetail', tiketCleanupModalBackdrop);

    function tiketShowAlert(msg, type) {
        if (typeof showAlert === 'function') {
            showAlert(msg, type || 'danger');
        } else if (typeof showToast === 'function') {
            showToast(msg, type || 'danger');
        } else {
            alert(msg);
        }
    }

    function tiketShowConfirm(message, onConfirm, options) {
        options = options || {};
        var title = options.title || 'Konfirmasi';
        var confirmText = options.confirmText || 'Ya, Lanjutkan';
        var confirmClass = options.confirmClass || 'btn-success';
        var type = options.type || 'success';
        var icon = options.icon || 'fa-circle-check';

        $('#modalTiketConfirmText').html(message);
        $('#modalTiketConfirmTitle').html('<i class="fa-solid ' + icon + '"></i> ' + tiketEscapeHtml(title));
        $('#modalTiketConfirmYes').text(confirmText);
        $('#modalTiketConfirmYes')
            .removeClass('btn-danger btn-success btn-primary btn-warning btn-secondary')
            .addClass(confirmClass);

        var $header = $('#modalTiketConfirmHeader');
        var $close = $header.find('.btn-close');
        $header.removeClass('bg-danger bg-success bg-warning bg-info bg-primary text-white text-dark');
        $close.removeClass('btn-close-white');

        if (type === 'success') {
            $header.addClass('bg-success bg-gradient text-white');
            $close.addClass('btn-close-white');
        } else if (type === 'danger') {
            $header.addClass('bg-danger bg-gradient text-white');
            $close.addClass('btn-close-white');
        } else if (type === 'warning') {
            $header.addClass('bg-warning bg-gradient text-dark');
        } else {
            $header.addClass('bg-primary bg-gradient text-white');
            $close.addClass('btn-close-white');
        }

        var modalEl = document.getElementById('modalTiketConfirm');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        $('#modalTiketConfirmYes').off('click.tiketConfirm').on('click.tiketConfirm', function() {
            modal.hide();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }

    function tiketOpenDetail(id) {
        var $modal = $('#modalTiketDetail');
        var modalEl = $modal[0];
        if (!modalEl) {
            return;
        }

        var inst = bootstrap.Modal.getOrCreateInstance(modalEl);
        if (!$modal.hasClass('show')) {
            $('#tiketDetailBody').html('<div class="modal-body text-center py-5 text-muted">Memuat...</div>');
            inst.show();
        }

        $('#tiketDetailBody').load('<?= PV::BASE_URL ?>Tiket/detail/' + id);
    }

    $(document).off('click.tiketRow', '.tiket-row').on('click.tiketRow', '.tiket-row', function() {
        tiketOpenDetail($(this).data('id'));
    });

    $(document).off('submit.tiketCreate', '#formTiketBaru').on('submit.tiketCreate', '#formTiketBaru', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        if (tiketCreateSubmitting) {
            return false;
        }

        var $form = $(this);
        var $btn = $('#btnTiketSimpan');
        tiketCreateSubmitting = true;
        $btn.prop('disabled', true);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function(res) {
                var data = tiketParseRes(res);
                if (data.ok) {
                    resetTiketKaryawanSelectize();
                    tiketDestroyKaryawanSelectize();
                    $form[0].reset();
                    if (data.tiket) {
                        tiketPrependRow(data.tiket);
                    }
                    tiketCloseModalBaru(function() {
                        tiketCreateSubmitting = false;
                    });
                } else {
                    tiketCreateSubmitting = false;
                    $btn.prop('disabled', false);
                    tiketShowAlert(data.error || 'Gagal menyimpan tiket.', 'danger');
                }
            },
            error: function() {
                tiketCreateSubmitting = false;
                $btn.prop('disabled', false);
                tiketShowAlert('Gagal menyimpan tiket.', 'danger');
            }
        });

        return false;
    });

    $(document).off('submit.tiketReply', '#formTiketReply').on('submit.tiketReply', '#formTiketReply', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        if (tiketReplySubmitting) {
            return false;
        }

        var $form = $(this);
        var $btn = $('#btnTiketReply');
        tiketReplySubmitting = true;
        $btn.prop('disabled', true);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function(res) {
                var data = tiketParseRes(res);
                if (data.ok && data.reply) {
                    tiketAppendReply(data.reply);
                    $form.find('textarea[name=isi]').val('');
                    tiketReplySubmitting = false;
                    $btn.prop('disabled', false);
                } else {
                    tiketReplySubmitting = false;
                    $btn.prop('disabled', false);
                    tiketShowAlert(data.error || 'Gagal mengirim balasan.', 'danger');
                }
            },
            error: function() {
                tiketReplySubmitting = false;
                $btn.prop('disabled', false);
                tiketShowAlert('Gagal mengirim balasan.', 'danger');
            }
        });

        return false;
    });

    $(document).off('click.tiketDone', '#btnTiketSelesai').on('click.tiketDone', '#btnTiketSelesai', function() {
        var id = $(this).data('id');
        var $btn = $(this);

        tiketShowConfirm(
            'Tiket akan dipindahkan ke daftar <strong>Selesai</strong> dan tidak dapat dibalas lagi.',
            function() {
                $btn.prop('disabled', true);
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Tiket/selesai',
                    type: 'POST',
                    dataType: 'json',
                    data: { id_tiket: id },
                    success: function(res) {
                        var data = tiketParseRes(res);
                        if (data.ok) {
                            tiketRemoveRow(data.id_tiket);
                            tiketCloseModalDetail();
                        } else {
                            $btn.prop('disabled', false);
                            tiketShowAlert(data.error || 'Gagal menyelesaikan tiket.', 'danger');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        tiketShowAlert('Gagal menyelesaikan tiket.', 'danger');
                    }
                });
            },
            {
                title: 'Selesaikan Tiket?',
                confirmText: 'Ya, Selesai',
                confirmClass: 'btn-success',
                type: 'success',
                icon: 'fa-check-double'
            }
        );
    });
</script>
