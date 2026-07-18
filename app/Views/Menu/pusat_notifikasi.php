<?php
$notif_c = (int) ($data['notif_c'] ?? 0);
$cashier_c = (int) ($data['notif_cashier_c'] ?? 0);
$cs_c = (int) ($data['notif_cs_c'] ?? 0);
$driver_c = (int) ($data['notif_driver_c'] ?? 0);
$can_prioritas = !empty($data['can_prioritas']);
$notifs_cashier = $data['notifs_cashier'] ?? [];
$notifs_cs = $data['notifs_cs'] ?? [];
$notifs_driver = $data['notifs_driver'] ?? [];

$renderNotifList = function (array $items, int $count, string $emptyLabel) {
	if ($count > 0 && count($items) > 0) {
		foreach ($items as $n) { ?>
			<a class="d-block text-decoration-none border-bottom py-2 px-1 text-dark" href="<?= htmlspecialchars($n['link'] ?? '#') ?>">
				<div class="fw-semibold" style="font-size: .9rem;"><?= htmlspecialchars($n['title'] ?? '') ?></div>
				<?php if (!empty($n['body'])) { ?>
					<div class="text-muted small"><?= htmlspecialchars($n['body']) ?></div>
				<?php } ?>
			</a>
		<?php }
	} else { ?>
		<div class="text-muted small py-2 px-1"><?= htmlspecialchars($emptyLabel) ?></div>
	<?php }
};
?>
<div id="pusatNotifikasiPayload" data-notif-count="<?= $notif_c ?>">
	<section class="mb-3">
		<div class="d-flex align-items-center justify-content-between mb-2">
			<span class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: .04em;">Cashier</span>
			<?php if ($cashier_c > 0) { ?>
				<span class="badge bg-danger rounded-pill"><?= $cashier_c ?></span>
			<?php } ?>
		</div>
		<div id="notifCashierList">
			<?php $renderNotifList($notifs_cashier, $cashier_c, 'Belum ada notifikasi cashier'); ?>
		</div>
	</section>

	<hr class="my-3">

	<section class="mb-3">
		<div class="d-flex align-items-center justify-content-between mb-2">
			<span class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: .04em;">Customer Service</span>
			<?php if ($cs_c > 0) { ?>
				<span class="badge bg-danger rounded-pill"><?= $cs_c ?></span>
			<?php } ?>
		</div>
		<div id="notifCsList">
			<?php $renderNotifList($notifs_cs, $cs_c, 'Belum ada notifikasi CS'); ?>
		</div>
	</section>

	<hr class="my-3">

	<section>
		<div class="d-flex align-items-center justify-content-between mb-2">
			<span class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: .04em;">Driver</span>
			<?php if ($driver_c > 0) { ?>
				<span class="badge bg-danger rounded-pill"><?= $driver_c ?></span>
			<?php } ?>
		</div>
		<div id="notifDriverList">
			<?php $renderNotifList($notifs_driver, $driver_c, 'Belum ada notifikasi driver'); ?>
		</div>
	</section>

	<?php if ($can_prioritas) { ?>
		<div id="notifPrioritasSync" class="d-none" aria-hidden="true">
			<?php include __DIR__ . '/prioritas.php'; ?>
		</div>
	<?php } ?>
</div>
