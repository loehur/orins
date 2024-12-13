<div class="modal" id="exampleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Produk - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add" method="POST">
                <div class="modal-body bg-primary bg-gradient bg-opacity-10 px-2">
                    <div class="mb-2">
                        <select class="tize loadDetail" name="id_produk" required>
                            <option></option>
                            <?php foreach ($data['produk'] as $dp) { ?>
                                <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="detail"></div>
                    <?php if (count($mgpaket) > 0) { ?>
                        <div class="mb-2 mt-5">
                            <label class="text-sm fw-bold text-danger ps-1">Link to Package</label>
                            <select class="tize" name="id_paket">
                                <option></option>
                                <?php foreach ($mgpaket as $dp) { ?>
                                    <option value="<?= $dp['id'] . "-" . $dp['primary'] . "-" . $dp['tb'] ?>"><?= $dp['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-primary bg-gradient rounded-pill" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalPaket">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Paket - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add_paket/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body bg-danger bg-gradient bg-opacity-10 px-2">
                    <div class="row mb-2">
                        <div class="col">
                            <select class="tize" name="id" required>
                                <option></option>
                                <?php foreach ($data['paket'] as $dp) { ?>
                                    <option value="<?= $dp['id'] ?>"><?= $dp['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mx-0 mb-3">
                        <div class="col text-center px-0 m-auto" style="max-width: 100px;">
                            <label>Jumlah</label><br>
                            <input type="number" min="1" value="1" name="jumlah" class="form-control float-end text-center border-0 shadow-none" id="qtyIn" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-primary bg-gradient rounded-pill" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalJasa">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Jasa - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add" method="POST">
                <div class="modal-body bg-secondary bg-gradient bg-opacity-10 px-2">
                    <div class="mb-2">
                        <select class="tize loadDetail_Jasa" name="id_produk" required>
                            <option></option>
                            <?php foreach ($data['produk_jasa'] as $dp) { ?>
                                <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="detail_jasa"></div>
                    <?php if (count($mgpaket) > 0) { ?>
                        <div class="mb-2 mt-5">
                            <label class="text-sm fw-bold text-danger ps-1">Link to Package</label>
                            <select class="tize" name="id_paket">
                                <option></option>
                                <?php foreach ($mgpaket as $dp) { ?>
                                    <option value="<?= $dp['id'] . "-" . $dp['primary'] . "-" . $dp['tb'] ?>"><?= $dp['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-primary bg-gradient rounded-pill" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalB">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Barang - <b><?= $id_pelanggan_jenis == 1 ? "Umum" : "Dealer" ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add_barang" method="POST">
                <div class="modal-body bg-success bg-gradient bg-opacity-10 px-2">
                    <div class="mb-2">
                        <select class="tize loadDetail_Barang" name="id_produk" required>
                            <option></option>
                            <?php
                            foreach ($data['stok'] as $dps) {
                                $dp = $data['barang'][$dps['kode_barang']];
                                $harga = $dp['harga_' . $id_pelanggan_jenis] ?>
                                <option value="<?= $dps['kode_barang'] ?>"><?= trim($dp['brand'] . " " . $dp['model'])  ?> Rp<?= number_format($harga) ?> #<?= $dps['kode_barang'] ?><?= strlen($dp['code_f']) > 0 ? "#" . $dp['code_f'] : "" ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <?php if (count($mgpaket) > 0) { ?>
                        <div class="mb-2 mt-5">
                            <label class="text-sm fw-bold text-danger ps-1">Link to Package</label>
                            <select class="tize" name="id_paket">
                                <option></option>
                                <?php foreach ($mgpaket as $dp) { ?>
                                    <option value="<?= $dp['id'] . "-" . $dp['primary'] . "-" . $dp['tb'] ?>"><?= $dp['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div id="detail_barang" style="min-height: 300px;"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalAff" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Produk (Afiliasi) - <b><?= $pelanggan_jenis ?></b></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add" method="POST">
                <div class="modal-body bg-primary bg-gradient bg-opacity-10 px-2">
                    <div class="mb-2">
                        <input type="hidden" name="aff_target" id="aff_target">
                        <select class="tize loadDetail_aff" name="id_produk" required>
                            <option></option>
                            <?php foreach ($data['produk'] as $dp) { ?>
                                <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="detail_aff"></div>
                    <?php if (count($mgpaket) > 0) { ?>
                        <div class="mb-2 mt-5">
                            <label class="text-sm fw-bold text-danger ps-1">Link to Package</label>
                            <select class="tize" name="id_paket">
                                <option></option>
                                <?php foreach ($mgpaket as $dp) { ?>
                                    <option value="<?= $dp['id'] . "-" . $dp['primary'] . "-" . $dp['tb'] ?>"><?= $dp['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-primary bg-gradient rounded-pill" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><small><span class="produk_harga"></span></small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/add_price/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Harga</label>
                        <input type="number" min="1" name="harga" class="form-control" required>
                        <input type="hidden" name="harga_code" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Tetapkan HARGA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="modalDiskon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b><span class="produk_harga"></span></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Buka_Order/diskon" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Diskon Rp</label>
                        <input type="number" min="0" name="diskon" class="form-control" required>
                        <input type="hidden" name="parse" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-purple border-purple" data-bs-dismiss="modal">Tetapkan Diskon</button>
                </div>
            </form>
        </div>
    </div>
</div>