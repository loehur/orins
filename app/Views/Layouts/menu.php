<?php
$cols = "id_toko, id_pelanggan, ref";
$where = "id_afiliasi = " . $this->userData['id_toko'] . " AND id_penerima <> 0 AND status_order = 1 AND cancel = 0 AND tuntas = 0 GROUP BY id_toko, id_pelanggan, ref";
$aff_ = $this->db(0)->get_cols_where('order_data', $cols, $where, 1);
$aff_c = count($aff_);

$where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND spk_lanjutan <> '' ORDER BY id_order_data DESC";
$data_spk_lnjut = $this->db(0)->get_where('order_data', $where);
$lanjut_c = count($data_spk_lnjut);

$yearNow = date('Y');
?>

<div id="layoutSidenav">
	<div id="layoutSidenav_nav">
		<nav class="sidenav sidenav-light border-end" style="z-index: -100;">
			<div class="sidenav-menu">
				<div class="nav accordion pt-3" id="accordionSidenav">
					<?php if (in_array($this->userData['user_tipe'], PV::PRIV[3])) { ?>
						<?php if ($aff_c > 0) { ?>
							<a class="nav-link <?= (str_contains($t, "Afiliasi Order")) ? 'active' : 'collapsed' ?> py-1" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#collapseAff" aria-expanded="true" aria-controls="collapseAff">
								<div class="nav-link-icon"><i data-feather="plus-square"></i></div>
								Afiliasi Order <span class="badge bg-danger-soft text-danger ms-2"><?= $aff_c ?></span>
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Afiliasi Order")) ? 'show' : '' ?>" id="collapseAff" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<?php foreach ($aff_ as $af) {
										$toko = $this->dToko[$af['id_toko']]['inisial'];
										$pelanggan = $this->dPelangganAll[$af['id_pelanggan']]['nama'] ?>
										<a class="nav-link py-1 <?= ($t == "Afiliasi Order - " . $af['ref']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>Buka_Order_Aff/index/<?= $af['ref'] ?>"><?= $toko ?> #<?= strtoupper($pelanggan) ?></a>
									<?php } ?>
								</nav>
							</div>
						<?php } ?>
					<?php } ?>
					<?php if (in_array($this->userData['user_tipe'], PV::PRIV[4])) { ?>
						<?php if ($lanjut_c > 0) { ?>
							<a class="nav-link <?= (str_contains($t, "SPK - Lanjutan")) ? 'active' : 'collapsed' ?> py-1" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseSPKP" aria-expanded="true" aria-controls="collapseSPKP">
								<div class="nav-link-icon"><i data-feather="alert-triangle"></i></div>
								SPK - Prioritas <span class="badge bg-danger-soft text-danger ms-2"><?= $lanjut_c ?></span>
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "SPK - Lanjutan")) ? 'show' : '' ?>" id="collapseSPKP" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<?php
									$list_l = [];
									foreach ($data_spk_lnjut as $ds) {
										$spk_e = str_replace('D-', '', $ds['spk_lanjutan']);
										$spk = explode('#', $spk_e);

										foreach ($spk as $sl) {
											if ($sl <> "") {
												array_push($list_l, $sl);
											}
										}
									}
									$list_l = array_unique($list_l) ?>

									<?php foreach ($list_l as $sl) { ?>
										<a class="nav-link py-1 <?= ($t == "SPK - Lanjutan " . $this->dDvs_all[$sl]['divisi']) ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>SPK_L/index/<?= $sl ?>"><?= $this->dDvs[$sl]['divisi'] ?></a>
									<?php } ?>
								</nav>
							</div>
						<?php } ?>
					<?php } ?>
					<?php foreach (Pv::MENU as $key => $md) { ?>
						<?php foreach ($md['access'] as $mda) { ?>
							<?php if (in_array($this->userData['user_tipe'], PV::PRIV[$mda])) { ?>
								<a class="nav-link <?= in_array($t, $md['active']) ? 'active' : 'collapsed' ?> py-2" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapse<?= $key ?>">
									<div class="nav-link-icon"><i data-feather="<?= $md['icon'] ?>"></i></div>
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
							<div class="nav-link-icon"><i class="fa-solid fa-file-signature"></i></div>
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
							<div class="nav-link-icon"><i data-feather="file-text"></i></div>
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
							<div class="nav-link-icon"><i data-feather="file-text"></i></div>
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
							<div class="nav-link-icon"><i data-feather="user"></i></div>
							Orins User
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "User")) ? 'show' : '' ?>" id="collapseFlowsUser" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "User Kasir") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/2">Kasir</a>
								<a class="nav-link <?= ($t == "User CS") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/3">CS</a>
								<a class="nav-link <?= ($t == "User Produksi") ? 'active' : '' ?>" href="<?= PV::BASE_URL ?>User/index/4">Produksi</a>
							</nav>
						</div>
					<?php } ?>
					<?php if (in_array($this->userData['user_tipe'], PV::PRIV[1])) { ?>
						<a class="nav-link <?= (str_contains($t, "Karyawan")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseKar" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="user"></i></div>
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