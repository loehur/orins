<main>
    <div class="ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col-auto">
                <form action="<?php $this->BASE_URL ?>Export/export" method="POST">
                    <div class="input-group">
                        <span class="input-group-text">Penjualan</span>
                        <input name="month" type="month" min="2023-07" aria-label="First name" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>