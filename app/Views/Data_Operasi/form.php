<form action="<?= PV::BASE_URL; ?>Data_Order/ambil_semua" method="POST">
    <div class="modal" id="exampleModal3">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengambilan Semua</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Karyawan</label>
                                <input type="hidden" name="ambil_ref">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-sm btn-primary">Ambil Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Order/ambil" method="POST">
    <div class="modal" id="exampleModal4">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengambilan</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class=" form-label">Karyawan</label>
                                <input type="hidden" name="ambil_id">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-sm btn-primary">Ambil</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Order/cancel" method="POST">
    <div class="modal" id="exampleModalCancel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pembatalan!</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Karyawan</label>
                                <input type="hidden" name="cancel_id">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($this->dKaryawan as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Alasan Cancel</label>
                                <input type="text" name="reason" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Cancel Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Order/cancel_diskon" method="POST">
    <div class="modal" id="modalCancelDiskon">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pembatalan Diskon!</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Alasan Cancel</label>
                                <input type="hidden" name="cancel_id_diskon">
                                <input type="text" name="reason" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Cancel Diskon</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Operasi/xtraDiskon" method="POST">
    <div class="modal" id="exampleModalDiskon">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Extra Diskon</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Extra Diskon Rp</label>
                                <input type="number" name="diskon" class="form-control form-control-sm text-end" required>
                                <input name="ref_diskon" type="hidden">
                                <input name="max_diskon" type="hidden">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-success">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL ?>Data_Order/bayar" method="POST">
    <div class="modal" id="exampleModal2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Pembayaran</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <label class="form-label">Jumlah Bill (Rp)</label>
                                <input type="number" name="bill" class="form-control bill text-end" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Metode</label>
                                <select name="method" class="form-select metodeBayar" required>
                                    <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                        <option value="1">Tunai</option>
                                    <?php } ?>
                                    <option value="2">Non Tunai</option>
                                    <option value="3">Afiliasi</option>
                                    <?php if ($data['saldo'] > 0) { ?>
                                        <option selected value="4">Saldo [ <?= number_format($data['saldo']) ?> ]</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <label class="form-label">Bayar (Rp) <small><span style="cursor: pointer;" class="bayarPas text-danger">Bayar Pas (Click)</span></small></label>
                                <input type="number" name="jumlah" class="form-control dibayar text-end" required>
                                <input type="hidden" name="ref" id="refBayar" required>
                                <input type="hidden" name="client" id="client" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Kembalian (Rp)</label>
                                <input type="number" name="kembalian" class="form-control kembalian text-end" readonly>
                            </div>
                        </div>
                        <div class="row mb-2" id="noteBayar">
                            <div class="col">
                                <label class="form-label"><span class="text-danger">Catatan Transaksi</span></label>
                                <input type="text" name="note" class="form-control border border-danger">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6 pt-3">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Bayar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>