<?php include_once('menuData.php');
$cols = "id_toko, id_pelanggan, ref";
$where = "id_afiliasi = " . $this->userData['id_toko'] . " AND id_penerima <> 0 AND status_order = 1 AND cancel = 0 AND tuntas = 0 GROUP BY id_toko, id_pelanggan, ref";
$aff_ = $this->db(0)->get_cols_where('order_data', $cols, $where, 1);
$aff_c = count($aff_);
?>

<div id="layoutSidenav">
	<div id="layoutSidenav_nav">
		<nav class="sidenav sidenav-light border-end" style="z-index: -100;">
			<div class="sidenav-menu">
				<div class="nav accordion" id="accordionSidenav">
					<?php if (in_array($this->userData['user_tipe'], $this->pCS)) { ?>
						<?php if ($aff_c > 0) { ?>
							<a class="nav-link <?= (str_contains($t, "Afiliasi Order")) ? 'active' : 'collapsed' ?> mt-2" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAff" aria-expanded="true" aria-controls="collapseAff">
								<div class="nav-link-icon"><i data-feather="plus-square"></i></div>
								Afiliasi Order <span class="badge bg-danger-soft text-danger ms-auto"><?= $aff_c ?></span>
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Afiliasi Order")) ? 'show' : '' ?>" id="collapseAff" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<?php foreach ($aff_ as $af) {
										$toko = $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $af['id_toko']); ?>
										<a class="nav-link <?= ($t == "Afiliasi Order - " . $af['ref']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Buka_Order_Aff/index/<?= $af['ref'] ?>"><?= $toko ?> - C#<?= $af['id_pelanggan'] ?></a>
									<?php } ?>
								</nav>
							</div>
						<?php } ?>
					<?php } ?>

					<?php foreach ($mdata as $key => $md) { ?>
						<?php foreach ($md['access'] as $mda) { ?>
							<?php if (in_array($this->userData['user_tipe'], $mda)) { ?>
								<a class="nav-link <?= (str_contains($t, $md['name'])) ? 'active' : 'collapsed' ?> mt-2" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapse<?= $key ?>">
									<div class="nav-link-icon"><i data-feather="<?= $md['icon'] ?>"></i></div>
									<?= $md['name'] ?>
									<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
								</a>
								<div class="collapse <?= (str_contains($t, $md['name'])) ? 'show' : '' ?>" id="collapse<?= $key ?>" data-bs-parent="#accordionSidenav">
									<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
										<?php if (isset($md['sub'])) { ?>
											<?php foreach ($md['sub'] as $sb) { ?>
												<a class="nav-link <?= ($t == $md['name'] . " - " . $sb['name']) ? 'active' : '' ?>" href="<?= $this->BASE_URL . $sb['link'] ?>"><?= $sb['name'] ?></a>
											<?php } ?>
										<?php } ?>
									</nav>
								</div>
								<?php break; ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pCS)) { ?>
						<a class="nav-link <?= (str_contains($t, "CS Fitur")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlowCSF" aria-expanded="false" aria-controls="collapseFlowCSF">
							<div class="nav-link-icon"><i data-feather="columns"></i></div>
							CS Fitur
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "CS Fitur")) ? 'show' : '' ?>" id="collapseFlowCSF" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "CS Fitur - Item Detail") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Group_Detail_CS">Item Detail (+)</a>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pProduksi)) { ?>
						<hr class="p-0 m-0">
						<a class="nav-link <?= (str_contains($t, "SPK_Search")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#SPK_Search" aria-expanded="true" aria-controls="SPK">
							<div class="nav-link-icon"><i class="fa-solid fa-file-signature"></i></div>
							SPK - Customer
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "SPK_Search")) ? 'show' : '' ?>" id="SPK_Search" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
								<?php foreach ($this->dDvs as $dv) {
									if ($dv['viewer'] <> 0) { ?>
										<a class="nav-link <?= ($t == "SPK_Search - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>SPK_Customer/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
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
										<a class="nav-link <?= ($t == "SPK_C - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>SPK_C/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
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
										<a class="nav-link <?= ($t == "SPK_R - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>SPK/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
								<?php }
								} ?>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pKasir)) { ?>
						<hr class="p-0 m-0">
						<!-- CASHIER PANEL -->
						<a class="nav-link <?= (str_contains($t, "Cashier")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseCashier" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="archive"></i></div>
							Cashier
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Cashier")) ? 'show' : '' ?>" id="collapseCashier" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Cashier - Setoran") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Setoran">Setoran</a>
								<a class="nav-link <?= ($t == "Cashier - Setoran Riwayat") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Setoran_Riwayat">Setoran Riwayat</a>
								<a class="nav-link <?= ($t == "Cashier - Non Tunai") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Non_Tunai_C">Transaksi Non Tunai</a>
								<a class="nav-link <?= ($t == "Cashier - Afiliasi") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Afiliasi_C">Transaksi Afiliasi</a>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pAdmin)) { ?>
						<hr class="p-0 m-0">
						<a class="nav-link <?= (str_contains($t, "User")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlowsUser" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="user"></i></div>
							Orins User
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "User")) ? 'show' : '' ?>" id="collapseFlowsUser" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "User Kasir") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>User/index/2">Kasir</a>
								<a class="nav-link <?= ($t == "User CS") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>User/index/3">CS</a>
								<a class="nav-link <?= ($t == "User Produksi") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>User/index/4">Produksi</a>
							</nav>
						</div>
						<a class="nav-link <?= (str_contains($t, "Karyawan")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseKar" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="user"></i></div>
							Karyawan
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Karyawan")) ? 'show' : '' ?>" id="collapseKar" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Karyawan Aktif") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Karyawan">Aktif</a>
								<a class="nav-link <?= ($t == "Karyawan Non Aktif") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Karyawan_N">Non Aktif</a>
							</nav>
						</div>
						<a class="nav-link <?= (str_contains($t, "Set Produksi")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlows2" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="tool"></i></div>
							Pengaturan Produksi
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Set Produksi")) ? 'show' : '' ?>" id="collapseFlows2" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Set Produksi - Divisi") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Divisi">Divisi</a>
								<a class="nav-link <?= ($t == "Set Produksi - Group Detail") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Group_Detail">Item Produksi</a>
								<a class="nav-link <?= ($t == "Set Produksi - Produk") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Produk">Produk Produksi</a>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pFinance)) { ?>
						<hr class="p-0 m-0">
						<!-- FINANCE PANEL -->
						<a class="nav-link <?= (str_contains($t, "Finance")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="dollar-sign"></i></div>
							Finance
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Finance")) ? 'show' : '' ?>" id="collapseFinance" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Finance - Non Tunai") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Non_Tunai">Non Tunai Transaksi</a>
								<a class="nav-link <?= ($t == "Finance - Non Tunai Riwayat") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Non_Tunai_Riwayat">Non Tunai Riwayat</a>
								<a class="nav-link <?= ($t == "Finance - Setoran") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Setoran_F">Setoran Kasir</a>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pAudit)) { ?>
						<hr class="p-0 m-0">
						<!-- FINANCE PANEL -->
						<a class="nav-link <?= (str_contains($t, "Audit")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAudit" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="check-square"></i></div>
							Audit
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Audit")) ? 'show' : '' ?>" id="collapseAudit" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Audit - Afiliasi") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Afiliasi">Afiliasi Transaksi</a>
								<a class="nav-link <?= ($t == "Audit - Afiliasi Riwayat") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Afiliasi_Riwayat">Afiliasi Riwayat</a>
								<a class="nav-link <?= ($t == "Audit - Data Export") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Export">Data Export</a>
							</nav>
						</div>
					<?php } ?>

					<?php if (in_array($this->userData['user_tipe'], $this->pMaster)) { ?>
						<hr class="p-0 m-0">
						<!-- MASTER PANEL -->
						<a class="nav-link <?= (str_contains($t, "Managment")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseToko" aria-expanded="false" aria-controls="collapseFlows">
							<div class="nav-link-icon"><i data-feather="server"></i></div>
							Managment
							<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse <?= (str_contains($t, "Managment")) ? 'show' : '' ?>" id="collapseToko" data-bs-parent="#accordionSidenav">
							<nav class="sidenav-menu-nested nav">
								<a class="nav-link <?= ($t == "Managment - Toko") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Toko_Daftar">Data Toko</a>
								<a class="nav-link <?= ($t == "Managment - Admin Toko") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Toko_Admin">Admin Toko</a>
								<a class="nav-link <?= ($t == "Managment - Admin Officer") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Admin_Officer">Admin Officer</a>
							</nav>
						</div>
					<?php } ?>
				</div>
			</div>
			<!-- Sidenav Footer-->
			<div class="sidenav-footer bg-light">
				<div class="sidenav-footer-content">
					<div class="sidenav-footer-subtitle"><?= $this->userData['id_toko'] ?>#<?= $this->userData['id_user'] ?></div>
					<div class="sidenav-footer-title"><?= $this->userData['nama'] ?></div>
				</div>
			</div>
		</nav>
	</div>
	<div id="layoutSidenav_content">
		<main>
			<div id="content"></div>
		</main>
	</div>
</div>