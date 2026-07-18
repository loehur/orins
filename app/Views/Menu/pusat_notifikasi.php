<?php
$notif_c = (int) ($data['notif_c'] ?? 0);
$can_prioritas = !empty($data['can_prioritas']);
?>
<div id="pusatNotifikasiPayload" data-notif-count="<?= $notif_c ?>">
	<div class="mb-3">
		<div class="d-flex align-items-center justify-content-between mb-2">
			<span class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: .04em;">Notifikasi</span>
			<?php if ($notif_c > 0) { ?>
				<span class="badge bg-danger rounded-pill"><?= $notif_c ?></span>
			<?php } ?>
		</div>
		<div id="notifItemsList">
			<?php if ($notif_c > 0 && !empty($data['notifs'])) { ?>
				<?php foreach ($data['notifs'] as $n) { ?>
					<a class="d-block text-decoration-none border-bottom py-2 px-1 text-dark" href="<?= htmlspecialchars($n['link'] ?? '#') ?>">
						<div class="fw-semibold" style="font-size: .9rem;"><?= htmlspecialchars($n['title'] ?? '') ?></div>
						<?php if (!empty($n['body'])) { ?>
							<div class="text-muted small"><?= htmlspecialchars($n['body']) ?></div>
						<?php } ?>
					</a>
				<?php } ?>
			<?php } else { ?>
				<div class="text-muted small py-2 px-1">Belum ada notifikasi</div>
			<?php } ?>
		</div>
	</div>

	<?php if ($can_prioritas) { ?>
		<hr class="my-3">
		<div>
			<div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: .04em;">Prioritas</div>
			<div class="notif-prioritas-wrap">
				<?php include __DIR__ . '/prioritas.php'; ?>
			</div>
		</div>
	<?php } ?>
</div>
