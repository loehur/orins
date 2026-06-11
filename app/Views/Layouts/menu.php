<?php
$canPrioritasMenu = in_array($this->userData['user_tipe'], PV::PRIV[4]);
$yearNow = date('Y');
?>


<style>
	div {
		scrollbar-color: pink white;
	}

	#collapsePrioritas.collapse:not(.show) {
		display: none;
	}

	#collapsePrioritas.collapse.show {
		flex: 0 0 auto;
		height: auto !important;
	}

	/* Hindari text node (spasi/BOM) antar anak flex jadi slot kosong */
	#accordionSidenav {
		font-size: 0;
		line-height: 0;
	}

	#accordionSidenav > a,
	#accordionSidenav > .collapse {
		font-size: 0.9rem;
		line-height: normal;
	}
</style>


<div id="layoutSidenav">
	<div id="layoutSidenav_nav">
		<nav class="sidenav sidenav-light border-end" style="z-index: -100;">
			<div class="sidenav-menu">
				<div class="nav accordion pt-3" id="accordionSidenav">
					<?php if ($canPrioritasMenu) {
						$prioritasShow = !empty($openPrioritasMenu);
						$prioritasState = $prioritasShow ? 'active' : 'collapsed';
						$prioritasExpanded = $prioritasShow ? 'true' : 'false';
						$prioritasPanelClass = $prioritasShow ? ' show' : '';
					?><a class="nav-link <?= $prioritasState ?> py-2" href="javascript:void(0)" id="prioritasToggle" aria-expanded="<?= $prioritasExpanded ?>"><div class="nav-link-icon text-danger"><i data-feather="alert-triangle"></i></div>Prioritas<span class="badge bg-danger-soft text-danger ms-2 d-none" id="menuPrioritasBadge"></span><div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div></a><div class="collapse<?= $prioritasPanelClass ?>" id="collapsePrioritas"><nav class="sidenav-menu-nested nav" id="prioritasSubmenu"></nav></div><?php } ?><?php foreach (Menu::items() as $key => $md) { ?>
						<?php foreach ($md['access'] as $mda) { ?>
							<?php if (in_array($this->userData['user_tipe'], PV::PRIV[$mda])) { ?>
								<a class="nav-link <?= in_array($t, $md['active']) ? 'active' : 'collapsed' ?> py-2" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapse<?= $key ?>">
									<div class="nav-link-icon text-<?= $md['icon-color'] ?>"><i data-feather="<?= $md['icon'] ?>"></i></div>
									<?= $md['name'] ?>
									<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
								</a>
								<div class="collapse <?= in_array($t, $md['active']) ? 'show' : '' ?>" id="collapse<?= $key ?>" data-bs-parent="#accordionSidenav">
									<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
										<?php if (isset($md['sub'])) { ?>
											<?php foreach ($md['sub'] as $sb) { ?>
												<a class="nav-link py-2 <?= $t == $sb['active'] ? 'active' : '' ?>" href="<?= PV::BASE_URL . $sb['link'] ?>"><?= $sb['name'] ?></a>
											<?php } ?>
										<?php } ?>
									</nav>
								</div>
								<?php break; ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], PV::PRIV[4])) { ?>
						<a class="nav-link <?= (str_contains($t, "SPK_Search")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#SPK_Search" aria-expanded="true" aria-controls="SPK">
							<div class="nav-link-icon text-success"><i class="fa-solid fa-file-signature"></i></div>
							SPK - Customer
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "SPK_Search")) ? 'show' : '' ?>" id="SPK_Search" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
								<?php foreach ($this->dDvs as $dv) {
									if ($dv['viewer'] <> 0) { ?>
										<a class="nav-link <?= ($t == "SPK_Search - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>SPK_Customer/index/<?= $dv['id_divisi'] ?>/0"><?= $dv['divisi'] ?></a>
								<?php }
								} ?>
							</nav>
						</div>
						<a class="nav-link <?= (str_contains($t, "SPK_C")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#SPK_C" aria-expanded="true" aria-controls="SPK">
							<div class="nav-link-icon text-purple"><i data-feather="file-text"></i></div>
							SPK - Harian
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "SPK_C")) ? 'show' : '' ?>" id="SPK_C" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
								<?php foreach ($this->dDvs as $dv) {
									if ($dv['viewer'] <> 0) { ?>
										<a class="nav-link <?= ($t == "SPK_C - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>SPK_C/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
								<?php }
								} ?>
							</nav>
						</div>
						<a class="nav-link <?= (str_contains($t, "SPK_R")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#SPK" aria-expanded="true" aria-controls="SPK">
							<div class="nav-link-icon text-info"><i data-feather="file-text"></i></div>
							SPK - Rekap
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "SPK_R")) ? 'show' : '' ?>" id="SPK" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
								<?php foreach ($this->dDvs as $dv) {
									if ($dv['viewer'] <> 0) { ?>
										<a class="nav-link <?= ($t == "SPK_R - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>SPK/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
								<?php }
								} ?>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], PV::PRIV[0])) { ?>
						<a class="nav-link <?= (str_contains($t, "User")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlowsUser" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon text-dark"><i data-feather="user"></i></div>
							Orins User
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "User")) ? 'show' : '' ?>" id="collapseFlowsUser" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "User Kasir") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/2">Kasir</a>
								<a class="nav-link <?= ($t == "User CS") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/3">CS</a>
								<a class="nav-link <?= ($t == "User Produksi") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/4">Produksi</a>
								<a class="nav-link <?= ($t == "User Driver") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/9">Driver</a>
							</nav>
						</div>
					<?php } ?>
					<?php if (in_array($this->userData['user_tipe'], PV::PRIV[1])) { ?>
						<a class="nav-link <?= (str_contains($t, "Karyawan")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseKar" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon text-dark"><i data-feather="user"></i></div>
							Karyawan
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Karyawan")) ? 'show' : '' ?>" id="collapseKar" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Karyawan Aktif") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>Karyawan">Aktif</a>
								<a class="nav-link <?= ($t == "Karyawan Non Aktif") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>Karyawan_N">Non Aktif</a>
							</nav>
						</div>
					<?php } ?>
				</div>
				<!-- Sidenav Footer-->
				<div class="sidenav-footer bg-light">
					<div class="sidenav-footer-content">
						<div class="sidenav-footer-subtitle"></div>
						<div class="sidenav-footer-title"></div>
					</div>
				</div>
		</nav>
	</div>
	<div id="layoutSidenav_content">
		<div style="margin-top: 20px;max-width:1100px" id="content"></div>
	</div>
</div>
</div>