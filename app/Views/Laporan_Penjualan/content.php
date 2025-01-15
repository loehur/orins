<main>
    <?php $wdth = 135; ?>
    <div class="ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <form target="_blank" action="<?php PV::BASE_URL ?>Laporan_Penjualan/cek_data" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary" style="width: <?= $wdth ?>px;">Data</span>
                        <input name="from" type="date" min="2023-07-01" style="max-width: 140px;" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                        <input name="to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-primary">Cek</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form target="_blank" action="<?php PV::BASE_URL ?>Laporan_Penjualan/cek_rekap_1" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary" style="width: <?= $wdth ?>px;">Rekap Produksi</span>
                        <input name="from" type="date" min="2023-07-01" style="max-width: 140px;" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                        <input name="to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-primary">Cek</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form target="_blank" action="<?php PV::BASE_URL ?>Laporan_Penjualan/cek_rekap_2" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary" style="width: <?= $wdth ?>px;">Rekap Barang</span>
                        <input name="from" type="date" min="2023-07-01" style="max-width: 140px;" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                        <input name="to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-primary">Cek</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>