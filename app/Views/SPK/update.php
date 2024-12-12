<?php foreach ($data['order'] as $key => $d) {
    $tgl = date('d/m/y', strtotime($d['insertTime']));
    $no = substr($d['ref'], -4) ?>
    <div class="col">
        <div class="form-check">
            <input class="form-check-input" name="cek[]" type="checkbox" value="<?= $key ?>">
            <label class="form-check-label">
                <?php
                foreach ($data['pelanggan'] as $p) {
                    if ($d['id_pelanggan'] == $p['id_pelanggan']) {
                        echo "Tgl. " . $tgl . " <span class='text-danger'>" . $no . "</span> <span class='text-primary'>" . strtoupper($p['nama']) . "</span> " . $d['jumlah'] . "pcs ";
                    }
                }
                ?>
            </label>
        </div>
    </div>
<?php } ?>