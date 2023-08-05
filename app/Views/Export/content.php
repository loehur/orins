<main>
    <div class="ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <form action="<?php $this->BASE_URL ?>Export/export" method="POST">
                    <div class="input-group">
                        <span class="input-group-text text-primary">Penjualan</span>
                        <input name="month" type="month" min="2023-07" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col">
                <form action="<?php $this->BASE_URL ?>Export/export" method="POST">
                    <div class="input-group">
                        <span class="input-group-text text-success">Payment</span>
                        <input name="month" type="month" min="2023-07" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>