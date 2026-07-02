<?php
$t = $data['ticket'];
$isDev = !empty($data['is_dev']);
$canReply = !empty($data['can_reply']);
$canComplete = !empty($data['can_complete']);
$tipeLabel = [1 => 'Perbaikan', 2 => 'Fitur Baru', 3 => 'Usulan'];
if ((int) $t['tipe'] === 2) {
    $badgeClass = 'tiket-badge-fitur';
} elseif ((int) $t['tipe'] === 3) {
    $badgeClass = 'tiket-badge-usulan';
} else {
    $badgeClass = 'tiket-badge-perbaikan';
}

$namaKaryawan = $data['karyawan'][$t['id_karyawan']]['nama'] ?? '-';
$namaUser = $data['users'][$t['id_user']]['nama'] ?? $data['users'][$t['id_user']]['user'] ?? '-';
$selesaiOleh = '';
if ((int) $t['selesai_oleh'] > 0) {
    $selesaiOleh = $data['users'][$t['selesai_oleh']]['nama'] ?? $data['users'][$t['selesai_oleh']]['user'] ?? '-';
}
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

    .tiket-reply-dev {
        background: #e8f4fd;
        border-left: 3px solid #0d6efd;
    }

    .tiket-reply-user {
        background: #f8f9fa;
        border-left: 3px solid #6c757d;
    }

    .tiket-isi {
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>

<div class="modal-header">
    <div>
        <h5 class="modal-title mb-1"><?= htmlspecialchars($t['judul']) ?></h5>
        <div class="small text-muted">
            <span class="badge <?= $badgeClass ?>"><?= $tipeLabel[(int) $t['tipe']] ?? '-' ?></span>
            <?php if ((int) $t['status'] === 1) { ?>
                <span class="badge bg-success ms-1">Selesai</span>
            <?php } else { ?>
                <span class="badge bg-warning text-dark ms-1">Proses</span>
            <?php } ?>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="row small text-muted mb-3">
        <div class="col-md-6">
            <div><strong>Karyawan:</strong> <?= htmlspecialchars($namaKaryawan) ?></div>
            <div><strong>Pembuat:</strong> <?= htmlspecialchars($namaUser) ?></div>
        </div>
        <div class="col-md-6">
            <div><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($t['insertTime'])) ?></div>
            <?php if ((int) $t['status'] === 1 && $t['selesai_time']) { ?>
                <div><strong>Selesai:</strong> <?= date('d/m/Y H:i', strtotime($t['selesai_time'])) ?> oleh <?= htmlspecialchars($selesaiOleh) ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header py-2 fw-bold">Isi Tiket</div>
        <div class="card-body tiket-isi"><?= htmlspecialchars($t['isi']) ?></div>
    </div>

    <div class="mb-2 fw-bold">Balasan</div>
    <div id="tiketReplyList">
        <?php foreach ($data['replies'] as $r) {
            $replyUser = $data['users'][$r['id_user']] ?? [];
            $replyName = $replyUser['nama'] ?? $replyUser['user'] ?? 'User';
            $replyIsDev = isset($replyUser['user_tipe']) && in_array((int) $replyUser['user_tipe'], PV::PRIV[0]);
            $replyClass = $replyIsDev ? 'tiket-reply-dev' : 'tiket-reply-user';
        ?>
            <div class="p-2 mb-2 rounded <?= $replyClass ?>">
                <div class="small fw-bold mb-1">
                    <?= htmlspecialchars($replyName) ?>
                    <?php if ($replyIsDev) { ?><span class="badge bg-primary ms-1">Dev</span><?php } ?>
                    <span class="text-muted fw-normal">· <?= date('d/m/y H:i', strtotime($r['insertTime'])) ?></span>
                </div>
                <div class="tiket-isi small"><?= htmlspecialchars($r['isi']) ?></div>
            </div>
        <?php } ?>
    </div>
    <div id="tiketReplyEmpty" class="text-muted small mb-3 <?= count($data['replies']) > 0 ? 'd-none' : '' ?>">Belum ada balasan.</div>

    <?php if ($canReply) { ?>
        <form id="formTiketReply" class="mt-3" action="<?= PV::BASE_URL ?>Tiket/reply" method="POST">
            <input type="hidden" name="id_tiket" value="<?= $t['id_tiket'] ?>">
            <label class="form-label fw-bold">Tulis Balasan</label>
            <textarea name="isi" id="tiketReplyIsi" class="form-control form-control-sm mb-2" rows="4" required placeholder="Tulis balasan..."></textarea>
            <button type="submit" class="btn btn-sm btn-primary" id="btnTiketReply">Kirim Balasan</button>
        </form>
    <?php } ?>
</div>

<?php if ($canComplete) { ?>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" id="btnTiketSelesai" data-id="<?= $t['id_tiket'] ?>">
            <i class="fa-solid fa-check"></i> Tandai Selesai
        </button>
    </div>
<?php } ?>
