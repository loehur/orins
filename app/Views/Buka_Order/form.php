<div class="modal" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><small><span class="produk_harga"></span></small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Buka_Order/add_price/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Harga</label>
                        <input type="number" min="1" name="harga" class="form-control" required>
                        <input type="hidden" name="harga_code" class="form-control" required>
                        <input type="hidden" name="id_produk" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tetapkan HARGA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalUtama" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update</small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Buka_Order/update_note" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Catatan</label>
                        <input type="hidden" name="note_mode" class="form-control" required>
                        <input type="text" name="note_val" class="form-control" required>
                        <input type="hidden" name="note_id" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Update Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalPbarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Buka_Order/add_price_barang/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Harga</label>
                        <input type="number" min="1" name="harga" class="form-control" required>
                        <input type="hidden" name="code_barang" class="form-control" required>
                    </div>
                    <div class="mb-3 text-danger text-sm">
                        Perubahan harga hanya berlaku pada item yang masih di dalam keranjang, tidak berdampak pada item yang sudah di proses.
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tetapkan HARGA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="exampleModalPC" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><small><span class="produk_harga"></span></small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Buka_Order/add_produksi" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Nama Produk</label>
                        <input type="text" name="nama" class="form-control" required>
                        <input type="hidden" name="product_code" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tetapkan Nama Produk</button>
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
            <form class="ajax" action="<?= PV::BASE_URL ?>Buka_Order/diskon" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Diskon Rp</label>
                        <input type="number" min="0" name="diskon" class="form-control" required>
                        <input type="hidden" name="parse" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-purple border-purple">Tetapkan Diskon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="modalDiskonBarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b><span class="produk_harga"></span></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Buka_Order/diskon_barang" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Diskon Rp</label>
                        <input type="number" min="0" name="diskon" class="form-control" required>
                        <input type="hidden" name="id_barang_diskon" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-purple border-purple">Tetapkan Diskon</button>
                </div>
            </form>
        </div>
    </div>
</div>
