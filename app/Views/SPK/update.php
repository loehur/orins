<?php foreach ($data['order'] as $key => $d) {
    $tgl = date('d/m/y', strtotime($d['insertTime']));
    $no = substr($d['ref'], -4);
    $isBertahap = !empty($d['bertahap']);
    $cekId = isset($d['cek_id']) ? $d['cek_id'] : $key;
    ?>
    <div class="col">
        <div class="form-check <?= $isBertahap ? 'spk-bertahap-cek p-2 rounded border border-info bg-info bg-opacity-10' : '' ?>">
            <input class="form-check-input" name="cek[]" type="checkbox" value="<?= htmlspecialchars($cekId, ENT_QUOTES) ?>">
            <label class="form-check-label">
                <?php
                foreach ($data['pelanggan'] as $p) {
                    if ($d['id_pelanggan'] == $p['id_pelanggan']) {
                        echo "Tgl. " . $tgl . " <span class='text-danger'>" . $no . "</span> <span class='text-primary'>" . strtoupper($p['nama']) . "</span> ";
                        if ($isBertahap) {
                            $bt = $d['bertahap'];
                            echo "<span class='badge bg-info text-dark'><i class='fa-solid fa-layer-group'></i> T" . (int)$bt['tahap'] . "</span> ";
                            echo "<span class='text-dark'><i class='fa-solid fa-scissors'></i> <b>" . (int)$bt['qty_tahap'] . "</b>pcs</span> ";
                            echo "<small class='text-muted'>(induk <i class='fa-solid fa-boxes-stacked'></i> " . (int)$bt['qty_induk'] . " · sisa <i class='fa-solid fa-hourglass-half'></i> " . (int)$bt['qty_sisa'] . ")</small>";
                        } else {
                            echo $d['jumlah'] . "pcs ";
                        }
                    }
                }
                ?>
            </label>
        </div>
    </div>
<?php } ?>
