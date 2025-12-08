<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

switch ($id_pelanggan_jenis) {
    case 1:
        $pelanggan_jenis = "Umum";
        break;
    case 2:
        $pelanggan_jenis = "Rekanan";
        break;
    case 3:
        $pelanggan_jenis = "Online";
        break;
    default:
        $pelanggan_jenis = "Stok";
        break;
}

$total_order = 0;
$total_item = 0;
$paket = false;
$mgpaket = $data['margin_paket'];
?>

<main class="container">
    <div class="container px-2">
        <?php
        if (count($data['errorID']) > 0) {
            echo "<br><small class='text-danger'>Order Data Error! yang mungkin disebabkan oleh jaringan terputus atau pengaturan produk yang tidak valid:</small><br><hr class='my-1'>";
            foreach ($data['errorID'] as $k => $ei) { ?>
                - ID#<?= $ei['id'] ?> <?= $ei['produk'] ?> <button class="btn btn-sm btn-outline-danger delError border-0 shadow-sm py-1 mb-1 ms-1" data-id="<?= $ei['id'] ?>"><b>Hapus</b></button><br>
            <?php }
        } else {
            ?>
            <div class="row <?= (count($data['order']) == 0 && count($data['order_barang']) == 0) || isset($_SESSION['edit'][$this->userData['id_user']]) ? "d-none" : "" ?>">
                <div class="col px-2">
                    <form class="proses" action="<?= PV::BASE_URL ?>Buka_Order/proses/<?= $id_pelanggan_jenis ?>" method="POST">
                        <?php if ($id_pelanggan_jenis == 1) { ?>
                            <div class="row mb-2">
                                <div class="col px-1" style="max-width: 300px;">
                                    <input name="new_customer" value="<?= isset($_COOKIE['new_user']) ? $_COOKIE['new_user'] : '' ?>" class="form-control form-control-sm" placeholder="New Customer">
                                </div>
                                <div class="col px-1" style="max-width: 300px;">
                                    <input name="hp" value="<?= isset($_COOKIE['hp']) ? $_COOKIE['hp'] : '' ?>" class="form-control form-control-sm" placeholder="Phone Number">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row pb-2">
                            <div class="col px-1" style="max-width: 300px;">
                                <select class="tize shadow-none" id="pelanggan" name="id_pelanggan">
                                    <option value="">Customer Name (<?= $pelanggan_jenis ?>)</option>
                                    <?php foreach ($data['pelanggan'] as $p) { ?>
                                        <option value="<?= $p['id_pelanggan'] ?>"><?= strtoupper($p['nama']) ?> #<?= substr($p['id_pelanggan'], 2) ?> | <?= $p['no_hp'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col px-1" style="max-width: 300px;">
                                <select class="tize shadow-none" name="id_karyawan" required>
                                    <option value="">CS Name</option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= strtoupper($k['nama']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-auto px-1 mt-auto p-0">
                                <button type="submit" class="btn shadow-none btn-success bg-gradient w-100">Proses</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            if (isset($_SESSION['edit'][$this->userData['id_user']])) {
                $dEdit = $_SESSION['edit'][$this->userData['id_user']]; ?>
                <div class="row mb-3 text-sm border py-2">
                    <div class="col-auto">
                        <label>Pelanggan</label><br>
                        <span class="fw-bold"><?= strtoupper($data['pelanggan'][$dEdit[3]]['nama']) ?></span>
                    </div>
                    <div class="col-auto text-end">
                        <label>Paid</label><br>
                        <span class="fw-bold" id="paid" data-val="<?= $dEdit[2] ?>"><?= number_format($dEdit[2]) ?></span>
                    </div>
                    <div class="col mt-auto">
                        <a class="submit" href="<?= PV::BASE_URL ?>Buka_Order/proses/<?= $dEdit[1] ?>/<?= $dEdit[3] ?>"><span class="btn btn-sm btn-secondary bg-gradient">Update</span></a>
                    </div>
                </div>
            <?php }
            ?>

            <div class="row mb-2">
                <div class="col px-0 text-end">
                    <?php if ($data['count'] <= 40) {
                        if ($id_pelanggan_jenis <> 100) { ?>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-danger bg-gradient py-1" data-bs-target="#exampleModalPaket" data-bs-toggle="modal">(&#43;) Paket</button>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-primary bg-gradient py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">(&#43;) Produksi</button>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-dark bg-gradient py-1" data-bs-target="#exampleModalJasa" data-bs-toggle="modal">(&#43;) Jasa</button>
                            <button type="button" class="btn me-1 shadow-none btn-sm btn-success bg-gradient py-1" data-bs-target="#exampleModalB" data-bs-toggle="modal">(&#43;) Barang</button>
                            <div class="btn-group me-1">
                                <?php if ($this->userData['aff_id'] == 0) { ?>
                                    <button type="button" class="btn shadow-none btn-sm btn-warning bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        (&#43;) Afiliasi
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start p-0">
                                        <?php foreach ($this->dToko as $dt) {
                                            if ($dt['id_toko'] <> $this->userData['id_toko'] && $dt['produksi'] == 1) { ?>
                                                <li><a data-bs-toggle="modal" data-bs-target="#exampleModalAff" class="dropdown-item aff" data-id="<?= $dt['id_toko'] ?>" href="#"><?= $dt['nama_toko'] ?></a></li>
                                        <?php }
                                        } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <?php if ($this->userData['aff_id'] == 0) { ?>
                                <button type="button" class="btn me-1 shadow-none btn-sm btn-primary bg-gradient py-1" data-bs-toggle="modal" data-bs-target="#exampleModal">(&#43;) Produksi</button>
                                <div class="btn-group me-1">
                                    <button type="button" class="btn shadow-none btn-sm btn-warning bg-gradient py-1 px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        (&#43;) Afiliasi
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start mt-2 p-0">
                                        <?php foreach ($this->dToko as $dt) {
                                            if ($dt['id_toko'] <> $this->userData['id_toko']) { ?>
                                                <li><a data-bs-toggle="modal" data-bs-target="#exampleModalAff" class="dropdown-item aff" data-id="<?= $dt['id_toko'] ?>" href="#"><?= $dt['nama_toko'] ?></a></li>
                                        <?php }
                                        } ?>
                                    </ul>
                                </div>
                            <?php } ?>
                    <?php }
                    } ?>
                </div>
            </div>
            <?php
            // Prepare grouping: separate paket items (grouped by paket_group) and non-paket items
            $order_nonpaket = [];
            $order_paket_groups = []; // paket_group => ['paket_ref'=>..., 'items'=>[], 'harga_paket'=>...]
            foreach ($data['order'] as $keyD => $do) {
                if (isset($do['paket_ref']) && strlen($do['paket_ref']) > 0) {
                    $pg = $do['paket_group'];
                    if (!isset($order_paket_groups[$pg])) {
                        $order_paket_groups[$pg] = ['paket_ref' => $do['paket_ref'], 'items' => [], 'harga_paket' => $do['harga_paket']];
                    }
                    $order_paket_groups[$pg]['items'][] = ['key' => $keyD, 'do' => $do];
                } else {
                    $order_nonpaket[] = ['key' => $keyD, 'do' => $do];
                }
            }

            ?>
            <?php if (count($order_nonpaket) > 0 || count($order_paket_groups) > 0) { ?>
                <div class="row">
                    <div class="col border-start border-end border-top px-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php
                                $no = 0;
                                // Render non-paket items first
                                foreach ($order_nonpaket as $item) {
                                    $keyD = $item['key'];
                                    $do = $item['do'];
                                    $no++;
                                    $total_item += 1;
                                    $akum_diskon = 0;

                                    $id_order_data = $do['id_order_data'];
                                    $id_produk = $do['id_produk'];
                                    $detail_arr = unserialize($do['produk_detail']);
                                    $detail = "";
                                    $listDetail = unserialize($do['detail_harga']);

                                    foreach ($detail_arr as $da) {
                                        $detail .= $da['detail_name'] . ", ";
                                    }

                                    $produk = $do['produk'];

                                    $detail_harga = unserialize($do['detail_harga']);

                                    $harga_ok = true;
                                    $btnSetHarga = 'Uninitialized';

                                    foreach ($listDetail as $kl => $ld_o) {
                                        $disk = $ld_o['d'];
                                        $akum_diskon += $disk ?>
                                    <?php }

                                    ?>
                                    <tr>
                                        <td class="">
                                            <table class="table table-sm w-100 mb-0">
                                                <tr class="<?= $do['id_afiliasi'] == 0 ? 'bg-primary' : 'bg-warning' ?> bg-gradient bg-opacity-10">
                                                    <td class="ps-2 align-middle">
                                                        <span class="text-nowrap text-dark"><small class="text-secondary">#<?= $id_order_data ?></small><b><small> <?= ucwords($produk) ?></small></b><?= $do['price_locker'] == 1 ? ' <i class="fa-solid fa-key"></i>' : '' ?></span>
                                                        <?php if ($do['paket_ref'] <> "") { ?>
                                                            <span class="badge bg-light text-dark"><?= $data['paket'][$do['paket_ref']]['nama'] ?></span>
                                                        <?php } ?>
                                                        <?php if ($do['id_afiliasi'] <> 0) { ?>
                                                            <small class="fw-bold badge bg-warning text-dark">
                                                                <i class="fa-solid fa-arrow-right"></i> <?= $this->dToko[$do['id_afiliasi']]['inisial'] ?>
                                                            </small>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small>
                                                            <span class="edit_n" data-id="<?= $do['id_order_data'] ?>"><?= $do['jumlah'] ?></span>x
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small><?php
                                                                if ($harga_ok == false) {
                                                                    echo $btnSetHarga;
                                                                } else {
                                                                    // only show per-item harga for non-paket items (also hide when paket_group present)
                                                                    if ((!isset($do['paket_ref']) || $do['paket_ref'] == '') && (!isset($do['paket_group']) || $do['paket_group'] == '') && $do['price_locker'] == 0) {
                                                                        if ($akum_diskon > 0) {
                                                                            echo "<del>" . number_format($do['harga']) . "</del> <small>@" . number_format($do['harga'] - $akum_diskon);
                                                                        } else {
                                                                            echo "@" . number_format($do['harga']);
                                                                        }
                                                                    }
                                                                } ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <b>
                                                            <small>
                                                                <?php
                                                                if ($harga_ok == false) {
                                                                    echo $btnSetHarga;
                                                                } else {
                                                                    // only show per-item total for non-paket items (also hide when paket_group present)
                                                                    if ((!isset($do['paket_ref']) || $do['paket_ref'] == '') && (!isset($do['paket_group']) || $do['paket_group'] == '') && $do['price_locker'] == 0) {
                                                                        if ($akum_diskon > 0) {
                                                                            echo "<del>" . number_format($do['harga'] * $do['jumlah']) . "</del> " . number_format(($do['harga'] * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                                                                            $total_order -= ($akum_diskon * $do['jumlah']);
                                                                        } else {
                                                                            echo number_format($do['harga'] * $do['jumlah']);
                                                                        }
                                                                        $total_order += ($do['harga'] * $do['jumlah']);
                                                                    } else {
                                                                        // paket items: do not display per-item harga; keep using paket margin formula
                                                                        echo number_format(($do['harga'] * $do['jumlah']) + $mgpaket[$do['paket_ref']]['margin_paket']);
                                                                        $total_order += (($do['harga'] * $do['jumlah']) + $mgpaket[$do['paket_ref']]['margin_paket']);
                                                                    }
                                                                } ?>
                                                            </small>
                                                        </b>
                                                    </td>
                                                    <td class="align-middle" style="width: 30px;"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                                                </tr>
                                                <?php if ($id_pelanggan_jenis == 100) { ?>
                                                    <tr>
                                                        <?php $code = str_replace(['-', '&', '#'], '', $do['produk_code']); ?>
                                                        <td colspan="10"><b><span data-bs-toggle="modal" data-code="<?= $do['produk_code'] ?>" data-bs-target="#exampleModalPC" style="cursor: pointer;" class="tetapkanNama px-2">N</span></b> <span class="text-danger fw-bold"><?= isset($data['barang_code'][$code]) ? $data['barang_code'][$code]['product_name'] : "" ?></span></td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td colspan="10" class="border-bottom-0">
                                                        <table class="table table-sm table-borderless mb-1">
                                                            <tr>
                                                                <td class="pe-1 border-bottom-0" nowrap>
                                                                    <div class="row">
                                                                        <?php
                                                                        foreach ($detail_arr as $da) { ?>
                                                                            <div class="col-auto" style="line-height: 80%;">
                                                                                <small>
                                                                                    <small><u><?= $da['group_name'] ?></u></small><br> <?= strtoupper($da['detail_name']) ?>
                                                                                </small>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="10" valign="top" class="p-0 border border-top-0">
                                                                    <small>
                                                                        <?php
                                                                        foreach ($listDetail as $kl => $ld_o) {
                                                                            $harga_d = $data['harga'][$keyD][$ld_o['c_h']]; ?>
                                                                            <div class="border-bottom mx-0">
                                                                                <div class="ps-1 float-start"><?= strtoupper($ld_o['n_v']) ?></div>
                                                                                <div class="float-end">
                                                                                    <?php
                                                                                    if ($do['price_locker'] == 0) {
                                                                                        if ($disk > 0) { ?>
                                                                                            <del><?= number_format($data['harga'][$keyD][$ld_o['c_h']]) ?></del>
                                                                                        <?php } ?>
                                                                                        <?= number_format($data['harga'][$keyD][$ld_o['c_h']] - $disk) ?>
                                                                                    <?php } ?>

                                                                                    <?php if ((!isset($do['paket_ref']) || $do['paket_ref'] == '') && (!isset($do['paket_group']) || $do['paket_group'] == '')) { ?>
                                                                                        <b><span data-bs-toggle="modal" data-id_produk="<?= $id_produk ?>" data-code="<?= $ld_o['c_h'] ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#exampleModal1" style="cursor: pointer;" class="tetapkanHarga px-2">P</span></b>
                                                                                        <?php if ($harga_d > 0) { ?>
                                                                                            <b><span data-bs-toggle="modal" data-parse="<?= $id_order_data . "_" . $kl . "_" . $harga_d ?>" data-produk="<?= strtoupper($ld_o['n_b']) ?>" data-bs-target="#modalDiskon" style="cursor: pointer;" class="tetapkanDiskon px-2">D</span></b>
                                                                                        <?php } ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <br>
                                                                        <?php }
                                                                        ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <div class="row">
                                                            <div class="col text-sm">
                                                                <small style="cursor: pointer;" class="updateNote" data-id="<?= $do['id_order_data'] ?>" data-note_mode="main" data-note_val="<?= $do['note'] ?>" data-bs-toggle="modal" data-bs-target="#exampleModalUtama"><span class="fw-bold">Utama</span></small><br>
                                                                <?php if (strlen($do['note']) > 0) { ?>
                                                                    <span class="text-danger"><?= $do['note'] ?></span>
                                                                <?php } ?>
                                                            </div>

                                                            <?php
                                                            $spkR = [];
                                                            if (strlen($do['pending_spk']) > 0) {
                                                                $spkR = unserialize($do['pending_spk']);
                                                            }

                                                            foreach (unserialize($do['note_spk']) as $ks => $ns) { ?>
                                                                <div class="col text-sm">
                                                                    <small style="cursor: pointer;" class="updateNote" data-id="<?= $do['id_order_data'] ?>" data-note_mode="<?= $ks ?>" data-note_val="<?= $ns ?>" data-bs-toggle="modal" data-bs-target="#exampleModalUtama"><span class="fw-bold"><?= $this->dDvs_all[$ks]["divisi"] ?></span></small><br>
                                                                    <?php if (strlen($ns) > 0 || isset($spkR[$ks])) { ?>
                                                                        <?php if (isset($spkR[$ks])) {
                                                                            $pendReady = explode("-", $spkR[$ks]); ?>
                                                                            <span class="badge bg-danger"><?= $data['spk_pending'][$pendReady[0]][$pendReady[1]] ?></span>
                                                                        <?php } ?>
                                                                        <span class="text-primary"><?= $ns ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                <?php }
                                // Now render paket groups: show header + all items (items still deletable, but hide per-item harga and P/D)
                                foreach ($order_paket_groups as $pg => $group) {
                                    $no++;
                                    $paket_ref = $group['paket_ref'];
                                    $paket_nama = isset($data['paket'][$paket_ref]['nama']) ? $data['paket'][$paket_ref]['nama'] : $paket_ref;
                                    $harga_paket_val = 0;
                                    if (isset($data['paket'][$paket_ref])) {
                                        $harga_paket_val = $data['paket'][$paket_ref]['harga_' . $id_pelanggan_jenis];
                                    }
                                    if ($harga_paket_val == 0 && isset($group['harga_paket'])) {
                                        $harga_paket_val = $group['harga_paket'];
                                    }
                                    $total_order += $harga_paket_val;
                                ?>
                                    <tr>
                                        <td>
                                            <table class="table table-sm w-100 mb-0">
                                                <tr class="bg-secondary bg-gradient bg-opacity-10">
                                                    <td class="ps-2 align-middle">
                                                        <span class="text-nowrap text-dark"><small class="text-secondary">#PK<?= $pg ?></small><b><small> <?= ucwords($paket_nama) ?></small></b></span>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small>
                                                            <span class="">x</span>
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <small>
                                                            <?= '@' . number_format($harga_paket_val) ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                        <b>
                                                            <small>
                                                                <?= number_format($harga_paket_val) ?>
                                                            </small>
                                                        </b>
                                                    </td>
                                                    <td class="align-middle" style="width: 30px;"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                    // render each item in the paket group (show details, but hide harga and P/D controls)
                                    foreach ($group['items'] as $item) {
                                        $keyD = $item['key'];
                                        $do = $item['do'];
                                        $total_item += 1;
                                        $akum_diskon = 0;
                                        $id_order_data = $do['id_order_data'];
                                        $id_produk = $do['id_produk'];
                                        $detail_arr = unserialize($do['produk_detail']);
                                        $listDetail = unserialize($do['detail_harga']);
                                        foreach ($listDetail as $kl => $ld_o) {
                                            $akum_diskon += $ld_o['d'];
                                        }
                                    ?>
                                        <tr>
                                            <td class="">
                                                <table class="table table-sm w-100 mb-0">
                                                    <tr class="<?= $do['id_afiliasi'] == 0 ? 'bg-primary' : 'bg-warning' ?> bg-gradient bg-opacity-10">
                                                        <td class="ps-2 align-middle">
                                                            <span class="text-nowrap text-dark"><small class="text-secondary">#<?= $id_order_data ?></small><small> <?= ucwords($do['produk']) ?></small><?= $do['price_locker'] == 1 ? ' <i class="fa-solid fa-key"></i>' : '' ?></span>
                                                            <span class="badge bg-light text-dark"><?= $paket_nama ?></span>
                                                        </td>
                                                        <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                            <small>
                                                                <span class="edit_n" data-id="<?= $do['id_order_data'] ?>"><?= $do['jumlah'] ?></span>x
                                                            </small>
                                                        </td>
                                                        <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                            <small>
                                                                <!-- per-item harga hidden for paket items -->
                                                            </small>
                                                        </td>
                                                        <td class="text-end" style="width: 1px;white-space: nowrap;">
                                                            <b>
                                                                <small>
                                                                    <!-- per-item total hidden; totals computed earlier -->
                                                                </small>
                                                            </b>
                                                        </td>
                                                        <td class="align-middle" style="width: 30px;"><a class="deleteItem" data-id_order="<?= $id_order_data ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="10" class="border-bottom-0">
                                                            <table class="table table-sm table-borderless mb-1">
                                                                <tr>
                                                                    <td class="pe-1 border-bottom-0" nowrap>
                                                                        <div class="row">
                                                                            <?php foreach ($detail_arr as $da) { ?>
                                                                                <div class="col-auto" style="line-height: 80%;">
                                                                                    <small>
                                                                                        <small><u><?= $da['group_name'] ?></u></small><br> <?= strtoupper($da['detail_name']) ?>
                                                                                    </small>
                                                                                </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <!-- listDetail hidden for paket items -->
                                                            </table>
                                                            <div class="row">
                                                                <div class="col text-sm">
                                                                    <small style="cursor: pointer;" class="updateNote" data-id="<?= $do['id_order_data'] ?>" data-note_mode="main" data-note_val="<?= $do['note'] ?>" data-bs-toggle="modal" data-bs-target="#exampleModalUtama"><span class="fw-bold">Utama</span></small><br>
                                                                    <?php if (strlen($do['note']) > 0) { ?>
                                                                        <span class="text-danger"><?= $do['note'] ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                <?php } // end items loop
                                } // end groups loop
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php }
        } ?>

        <div class="row mt-2">
            <div class="col border border-bottom-0 px-0">
                <table class="table table-sm m-0 text-sm">
                    <?php
                    // Group order_barang by paket_group as well
                    $ob_nonpaket = [];
                    $ob_paket_groups = [];
                    foreach ($data['order_barang'] as $db) {
                        if (isset($db['paket_ref']) && strlen($db['paket_ref']) > 0) {
                            $pg = $db['paket_group'];
                            if (!isset($ob_paket_groups[$pg])) {
                                $ob_paket_groups[$pg] = ['paket_ref' => $db['paket_ref'], 'items' => [], 'harga_paket' => $db['harga_paket']];
                            }
                            $ob_paket_groups[$pg]['items'][] = $db;
                        } else {
                            $ob_nonpaket[] = $db;
                        }
                    }
                    foreach ($ob_nonpaket as $db) {
                        $total_item += 1;
                        $dp = $data['barang'][$db['id_barang']];

                        if ($db['harga_jual'] > 0) {
                            $harga_satuan = $db['harga_jual'];
                        } else {
                            $harga_satuan = $dp['harga_' . $id_pelanggan_jenis];
                        }

                        if ($db['price_locker'] == 1) {
                            $classKeyPrice = 'text-danger';
                            $total_order += (($harga_satuan * $db['qty']) + $mgpaket[$db['paket_ref']]['margin_paket']);
                            $totalnya = ($harga_satuan * $db['qty']) + $mgpaket[$db['paket_ref']]['margin_paket'];
                        } else {
                            $total_order += ($harga_satuan * $db['qty']);
                            $totalnya = ($harga_satuan * $db['qty']);
                        }

                        $total_order -= ($db['diskon'] * $db['qty']);

                    ?>
                        <tr>
                            <td class="text-secondary text-end ps-2">
                                #<?= $db['id'] ?><br><?= $db['sds'] == 1 ? "<span class='text-danger'>S</span>" : "" ?>
                            </td>
                            <td>
                                <?= trim($dp['brand'] . " " . $dp['model'])  ?><?= $dp['product_name'] ?><?= $db['price_locker'] == 1 ? ' <i class="fa-solid fa-key"></i>' : '' ?>
                                <?= $db['sn'] <> "" ? "<br>" . $db['sn'] : "" ?>

                            </td>
                            <td class="text-end">
                                <?= number_format($db['qty']) ?>x<br>
                                <?php if ((!isset($db['paket_ref']) || $db['paket_ref'] == '') && (!isset($db['paket_group']) || $db['paket_group'] == '')) { ?>
                                    <?php if ($db['ref'] == '') { ?>
                                        <b><span data-bs-toggle="modal" data-code="<?= $db['id_barang'] ?>" data-jenis="<?= $db['jenis_target'] ?>" data-bs-target="#exampleModalPbarang" style="cursor: pointer;" class="tetapkanHargaBarang px-2">P</span></b>
                                    <?php } ?>
                                    <b><span data-bs-toggle="modal" data-id="<?= $db['id'] . "_" . $dp['harga_' . $id_pelanggan_jenis] ?>" data-bs-target="#modalDiskonBarang" style="cursor: pointer;" class="tetapkanDiskonBarang pe-2">D</span></b>
                                <?php } ?>

                                <?php
                                $harga_semula = "";
                                if ($db['diskon'] > 0) {
                                    $harga_semula = "<s>" . number_format($harga_satuan) . "</s>";
                                    $harga_satuan -= $db['diskon'];
                                }

                                $totalnya -= ($db['diskon'] * $db['qty']);
                                ?>
                                <?= ($db['price_locker'] == 0 && (!isset($db['paket_ref']) || $db['paket_ref'] == '') && (!isset($db['paket_group']) || $db['paket_group'] == '')) ? $harga_semula . " @" . number_format($harga_satuan)  : "" ?>
                            </td>
                            <td class="text-end pe-2"></td>
                            <td class="pt-2" style="width: 30px;"><a class="deleteItemBarang" data-id="<?= $db['id'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                        </tr>
                    <?php } ?>
                    <?php
                    // Render grouped paket barang: header + individual items (hide per-item harga/P/D but keep delete)
                    foreach ($ob_paket_groups as $pg => $group) {
                        $paket_ref = $group['paket_ref'];
                        $paket_nama = isset($data['paket'][$paket_ref]['nama']) ? $data['paket'][$paket_ref]['nama'] : $paket_ref;
                        $harga_paket_val = 0;
                        if (isset($data['paket'][$paket_ref])) {
                            $harga_paket_val = $data['paket'][$paket_ref]['harga_' . $id_pelanggan_jenis];
                        }
                        if ($harga_paket_val == 0 && isset($group['harga_paket'])) {
                            $harga_paket_val = $group['harga_paket'];
                        }
                        // do not render paket header or add to total in master_mutasi section
                    ?>
                        <?php foreach ($group['items'] as $db) {
                            $total_item += 1;
                            $dp = $data['barang'][$db['id_barang']];

                            if ($db['harga_jual'] > 0) {
                                $harga_satuan = $db['harga_jual'];
                            } else {
                                $harga_satuan = $dp['harga_' . $id_pelanggan_jenis];
                            }

                            if ($db['price_locker'] == 1) {
                                $classKeyPrice = 'text-danger';
                                $totalnya = ($harga_satuan * $db['qty']) + $mgpaket[$db['paket_ref']]['margin_paket'];
                            } else {
                                $totalnya = ($harga_satuan * $db['qty']);
                            }
                        ?>
                            <tr>
                                <td class="text-secondary text-end ps-2">
                                    #<?= $db['id'] ?><br><?= $db['sds'] == 1 ? "<span class='text-danger'>S</span>" : "" ?>
                                </td>
                                <td>
                                    <?= trim($dp['brand'] . " " . $dp['model'])  ?><?= $dp['product_name'] ?><?= $db['price_locker'] == 1 ? ' <i class="fa-solid fa-key"></i>' : '' ?>
                                    <?= $db['sn'] <> "" ? "<br>" . $db['sn'] : "" ?>
                                    <?= $db['paket_ref'] <> "" ? "<br><span class='badge text-dark bg-light'>" . $data['paket'][$db['paket_ref']]['nama'] . "</span>" : "" ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($db['qty']) ?>x<br>
                                    <!-- hide P/D controls for paket items -->
                                    <?php
                                    $harga_semula = "";
                                    if ($db['diskon'] > 0) {
                                        $harga_semula = "<s>" . number_format($harga_satuan) . "</s>";
                                        $harga_satuan -= $db['diskon'];
                                    }

                                    $totalnya -= ($db['diskon'] * $db['qty']);
                                    ?>
                                    <?= ($db['price_locker'] == 0 && (!isset($db['paket_ref']) || $db['paket_ref'] == '') && (!isset($db['paket_group']) || $db['paket_group'] == '')) ? $harga_semula . " @" . number_format($harga_satuan)  : "" ?>
                                </td>
                                <td class="text-end pe-2"><?= (!isset($db['paket_ref']) || $db['paket_ref'] == '') ? number_format($totalnya) : '' ?></td>
                                <td class="pt-2" style="width: 30px;"><a class="deleteItemBarang" data-id="<?= $db['id'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a></td>
                            </tr>
                        <?php } // end items loop 
                        ?>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col text-end border">
                <?= $total_item ?> Items
            </div>
            <div class="col text-end border">
                <span class="fw-bold" id="total_order" data-val="<?= $total_order ?>">Total Rp<?= number_format($total_order) ?></span>
            </div>
        </div>
    </div>
</main>

<?php require_once('form.php') ?>
<div class="modal fade" id="modalUpdateError" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Terjadi Kesalahan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="updateErrorText" class="text-danger"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('.tize').selectize();
    });

    $(".updateNote").click(function() {
        $("input[name=note_val]").val($(this).attr('data-note_val'));
        $("input[name=note_mode]").val($(this).attr('data-note_mode'));
        $("input[name=note_id]").val($(this).attr('data-id'));
    })

    $('select[name=id_pelanggan]').change(function() {
        $('input[name=new_customer]').val("");
        $('input[name=hp]').val("");
    })

    $('input[name=new_customer]').keypress(function() {
        $("#pelanggan")[0].selectize.clear();
    })

    $('input[name=new_customer]').keyup(function() {
        setCookie('new_user', $(this).val(), 1);
    })

    $('input[name=hp]').keyup(function() {
        setCookie('hp', $(this).val(), 1);
    })

    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    $("button.delError").click(function() {
        var id_ = $(this).attr('data-id');
        $.post("<?= PV::BASE_URL ?>Buka_Order/delete_error", {
                id: id_
            },
            function() {
                location.reload(true);
            });
    })

    $('select.loadDetail').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
                $("div#detail").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
            });
        }
    });


    $('select.loadDetail_aff').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_aff").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
                $("div#detail_aff").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
            });
        }
    });

    $('select.loadDetail_Jasa').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_Jasa").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
        }
    });


    $('select.loadDetail_Barang').on('change', function() {
        var produk = this.value;
        if (produk != "") {
            $("div#detail_barang").load('<?= PV::BASE_URL ?>Buka_Order/load_detail_barang/' + produk + '/<?= $id_pelanggan_jenis ?>');
        }
    });

    $("span.tetapkanHarga").click(function() {
        var produk = $(this).attr("data-produk");
        var harga_code = $(this).attr("data-code");
        var id_produk = $(this).attr("data-id_produk");
        $("span.produk_harga").html(produk);
        $("input[name=harga_code]").val(harga_code);
        $("input[name=id_produk]").val(id_produk);
    })

    $("span.tetapkanHargaBarang").click(function() {
        var code = $(this).attr("data-code");
        var jenis_pelanggan = $(this).attr("data-jenis");
        $("input[name=code_barang").val(code);
    })

    $("span.tetapkanNama").click(function() {
        var product_code = $(this).attr("data-code");
        $("input[name=product_code").val(product_code);
    })

    $("span.tetapkanDiskon").click(function() {
        var produk = $(this).attr("data-produk");
        var parse = $(this).attr("data-parse");
        $("span.produk_harga").html(produk);
        $("input[name=parse").val(parse);
    })

    $("span.tetapkanDiskonBarang").click(function() {
        var id = $(this).attr("data-id");
        $("input[name=id_barang_diskon").val(id);
    })

    $("a.aff").click(function() {
        $('input#aff_target').val($(this).attr("data-id"));
    })

    $("a.deleteItem").click(function() {
        var id = $(this).attr("data-id_order");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Buka_Order/deleteOrder",
            data: {
                id_order: id
            },
            type: "POST",
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    })

    $("a.deleteItemBarang").click(function() {
        var id = $(this).attr("data-id");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Buka_Order/deleteOrderBarang",
            data: {
                id: id
            },
            type: "POST",
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    })

    $("form.ajax").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    });

    $("form.proses").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (isNumeric(res) == false) {
                    alert(res);
                } else {
                    location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + res;
                }
            }
        });
    });

    $('a.submit').on('click', function(e) {
        e.preventDefault();

        var paid = parseInt($("#paid").attr('data-val'));
        var total_o = parseInt($("#total_order").attr('data-val'));

        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            data: {},
            cache: false,
            success: function(res) {
                if (isNumeric(res) == false) {
                    $("#updateErrorText").text(res);
                    var m = new bootstrap.Modal(document.getElementById('modalUpdateError'));
                    m.show();
                } else {
                    location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + res;
                }
            }
        });
    });

    function isNumeric(str) {
        if (typeof str != "string") return false
        return !isNaN(str) &&
            !isNaN(parseFloat(str))
    }

    var click = 0;
    $("span.edit_n").on('dblclick', function() {
        var value = $(this).html();

        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var value_before = value;
        var span = $(this);
        span.html("<input type='number' id='value_3313' style='text-align:center;width:70px' value=" + value + ">");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after === value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Buka_Order/updateCell_N',
                    data: {
                        'id': id,
                        'value': value_after,
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            content();
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });
</script>