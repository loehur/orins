<main>
    <div class="ms-3 mt-3 me-3 bg-white">
        <div class="row mb-2">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <form action="<?php PV::BASE_URL ?>Export/export" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary" style="min-width: 100px;">Production Sales</span>
                        <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <form action="<?php PV::BASE_URL ?>Export/export_pbarang" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary" style="min-width: 100px;">Items Sales</span>
                        <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <form action="<?php PV::BASE_URL ?>Export/export_p" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-success" style="min-width: 100px;">Payment</span>
                        <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-success">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>