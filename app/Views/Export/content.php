<main>
    <div class="ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <form action="<?php $this->BASE_URL ?>Export/export" method="POST">
                    <div class="input-group">
                        <span class="input-group-text text-primary">Sales</span>
                        <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col mb-2" style="min-width:270px;max-width:350px">
                <form action="<?php $this->BASE_URL ?>Export/export_p" method="POST">
                    <div class="input-group">
                        <span class="input-group-text text-success">Payment</span>
                        <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                        <button type="submit" class="btn btn-success">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>