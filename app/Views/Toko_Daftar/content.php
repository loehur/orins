<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4">
        <div class="card mt-n10" style="max-width: 500px;">
            <div class="card-header">Daftar Toko</div>
            <div class="card-body">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Toko</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <?php
                    foreach ($data as $a) { ?>
                        <tr>
                            <td>
                                <?= $a['id_toko'] ?>
                            </td>
                            <td>
                                <?= $a['nama_toko'] ?>
                            </td>
                            <td>
                                <?= substr($a['insertTime'], 0, 10) ?>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </table>
            </div>
        </div>
    </div>
</main>