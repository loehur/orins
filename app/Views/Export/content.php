<main>
    <div class="ms-3 me-3 bg-white">
        <div class="rounded border border-primary px-3 pt-2 pb-1">
            <div class="fw-bold mb-2"><span class="text-primary">Monthly</span></div>
            <div class="row">
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Production Sales</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_pbarang" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Item Sales</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_paket" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Package Sales</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_p" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-success" style="width: 120px;">Payment</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-success">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_ed" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-danger" style="width: 120px;">Extra Discount</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-danger">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_sc" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-success" style="width: 120px;">Surcharge</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-success">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_pc" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-dark" style="width: 120px;">Petty Cash</span>
                            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-dark">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="rounded border px-3 pt-2 pb-1 mt-4 border-success">
            <div class="fw-bold mb-2"><span class="text-success">Daily</span></div>
            <div class="row">
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Production Sales</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_pbarang" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Item Sales</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_paket" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Package Sales</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_p" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-success" style="width: 120px;">Payment</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-success">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_ed" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-danger" style="width: 120px;">Extra Discount</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-danger">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_sc" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-success" style="width: 120px;">Surcharge</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-success">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form action="<?php PV::BASE_URL ?>Export/export_pc" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-dark" style="width: 120px;">Petty Cash</span>
                            <input name="month" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-dark">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>