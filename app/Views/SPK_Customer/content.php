<?php $parse = $data['parse'];
?>
<style>
    .filter-select-wrap {
        position: relative;
        max-width: 600px;
        min-height: 38px;
    }

    .filter-select-wrap.is-loading .filter-select-fields {
        opacity: 0;
        pointer-events: none;
    }

    .filter-select-mini-loader {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0 0.5rem;
        z-index: 2;
    }

    .filter-select-wrap.is-ready .filter-select-mini-loader {
        display: none;
    }

    .filter-select-wrap.is-ready .filter-select-fields {
        opacity: 1;
    }

    .filter-select-mini-loader .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>
<main class="container">
    <div class="filter-select-wrap is-loading mx-0" id="filterSelectWrap">
        <div class="filter-select-mini-loader" aria-live="polite" aria-busy="true">
            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
            <small class="text-muted">Memuat pilihan...</small>
        </div>
        <div class="filter-select-fields row mx-0">
            <div class="col px-0">
                <select class="border rounded tize ajax-pelanggan" name="customer" required>
                    <option></option>
                    <?php foreach ($data['pelanggan'] as $p) { ?>
                        <option value="<?= $p['id_pelanggan'] ?>" selected><?= $this->dToko[$p['id_toko']]['inisial'] ?> <?= strtoupper($p['nama']) ?> #<?= substr($p['id_pelanggan'], -2) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto pt-auto mt-auto pe-0">
                <button type="button" class="cek btn btn-primary">Cek Order</button>
            </div>
        </div>
    </div>
    <?php if ($data['customer'] <> 0) { ?>
        <div class="row mx-2 mt-3">
            <?php
            for ($x = 1; $x <= 2; $x++) { ?>
                <div class="col ps-0 pe-1">
                    <?php foreach ($data['order'][$x] as $ref => $data['order_']) { ?>
                        <table class="table table-sm shadow-sm mb-2 border" style="min-width: 340px;">
                            <?php
                            $no = 0;
                            $total = 0;
                            foreach ($data['order_'] as $key => $do) {
                                $no++;
                                $spkDone = 0;
                                $spkCount = 0;
                                $id_order_data = $do['id_order_data'];
                                $id_produk = $do['id_produk'];
                                $detail_arr = unserialize($do['produk_detail']);
                                $detail = "";
                                foreach ($detail_arr as $da) {
                                    $detail .= $da['detail_name'] . ", ";
                                }

                                $id_pelanggan = $do['id_pelanggan'];
                                $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
                                $in_toko = "";
                                if ($id_toko_pelanggan <> $this->userData['id_toko']) {
                                    $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
                                }

                                $produk = ucwords($do['produk']);

                                $divisi_arr = unserialize($do['spk_dvs']);
                                $divisi = [];
                                foreach ($divisi_arr as $key => $dv) {
                                    foreach ($this->dDvs as $dv_) {
                                        if ($dv_['id_divisi'] == $key) {
                                            $divisi[$key] = $dv_['divisi'];
                                        }
                                    }
                                }

                                foreach ($divisi as $key => $dvs) {
                                    if ($key == $parse) {
                                        $spkCount += 1;
                                        if ($divisi_arr[$key]['status'] == 1) {
                                            $spkDone += 1;
                                        }
                                        if ($divisi_arr[$key]['cm'] == 1) {
                                            $spkCount += 1;
                                            if ($divisi_arr[$key]['cm_status'] == 1) {
                                                $spkDone += 1;
                                            }
                                        }
                                    }
                                }

                                $classTR = "";
                                if ($spkDone == $spkCount) {
                                    $classTR = "table-info";
                                } else {
                                    if ($spkDone == 1) {
                                        $classTR = "table-warning";
                                    }
                                }

                                if ($no == 1) {
                                    foreach ($data['pelanggan'] as $dp) {
                                        if ($dp['id_pelanggan'] == $do['id_pelanggan']) {
                                            $pelanggan = $dp['nama'];
                                        }
                                    }

                                    $cs = $data['karyawan_all'][$do['id_penerima']]['nama'];
                                    $cs_af = "";
                                    if ($do['id_user_afiliasi'] <> 0) {
                                        $cs_af = "/" . $data['karyawan_all'][$do['id_user_afiliasi']]['nama'];
                                    }
                            ?>
                                    <tr>
                                        <td colspan="5" class="table-secondary">
                                            <table class="w-100 p-0 m-0">
                                                <tr>
                                                    <td class="text-sm"><span><?= substr($ref, -4) ?></span> <span class="text-primary fw-bold"><?= $in_toko ?></span> <b><?= strtoupper($pelanggan) ?></b></td>
                                                    <td valign="top" class="text-end"><small><?= $cs . $cs_af ?> <?= substr($do['insertTime'], 2, -3) ?></span></small></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                <?php }
                                ?>
                                <tr class="<?= $classTR ?>">
                                    <td>
                                        <table class="float-start">
                                            <tr>
                                                <td class="pe-1 text-sm">
                                                    <span class="text-success"><?= $produk ?></span><br>
                                                    <?php
                                                    foreach ($detail_arr as $da) { ?>
                                                        <?= strtoupper($da['detail_name']) ?>
                                                    <?php } ?>
                                                </td>
                                                <td valign="bottom" class="text-end text-purple pe-2" style="width:40px"><b><?= number_format($do['jumlah']) ?></b>pcs</td>
                                            </tr>
                                            <?php
                                            $spkR = [];
                                            if (strlen($do['pending_spk']) > 1) {
                                                $spkR = unserialize($do['pending_spk']);
                                            }

                                            if (strlen($do['note']) > 0 || strlen($do['note_spk']) > 0) { ?>
                                                <tr>
                                                    <td colspan="10">
                                                        <?php
                                                        if (strlen($do['note']) > 0) { ?>
                                                            <span class='text-danger text-sm'><i class='fa-solid fa-circle-exclamation'></i> <?= $do['note'] ?></span>
                                                        <?php } ?>
                                                        <span class="text-sm">
                                                            <?php foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                                if ($ks == $parse && strlen($ns) > 0) {
                                                                    echo "<br><span class='text-primary'><i class='fa-solid fa-circle-exclamation'></i> " . $ns . "</span>";
                                                                }
                                                                if ($ks == $parse) {
                                                                    if (isset($spkR[$ks])) {
                                                                        $pendReady = explode("-", $spkR[$ks]); ?>
                                                                        <small><span class="badge bg-<?= $pendReady[1] == 'r' ? 'success' : 'danger' ?>"><?= $data['spk_pending'][$pendReady[0]][$pendReady[1]] ?></span></small>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                        <table class="float-end">
                                            <tr>
                                                <?php
                                                foreach ($divisi as $key => $dvs) {
                                                    if ($key == $parse) {
                                                        if ($divisi_arr[$key]['status'] == 0) {
                                                            if ($do['id_toko'] == $this->userData['id_toko'] || $do['id_afiliasi'] == $this->userData['id_toko']) { ?>
                                                                <?php if (!str_contains($do['spk_lanjutan'], "D-" . $parse . "#")) { ?>
                                                                    <td style="cursor: pointer;" class="pe-2 text-sm push" data-id="<?= $id_order_data ?>" data-val="<?= $parse ?>"><i class="fa-regular fa-circle-right"></i> Push</td>
                                                                <?php } else { ?>
                                                                    <td class="pe-2 text-sm text-danger"><small class="fw-bold">Pushed</small></td>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                <?php echo "<td class='pe-2 text-sm'>";
                                                        if ($divisi_arr[$key]['status'] == 1) {
                                                            $karyawan = $this->dKaryawanAll[$divisi_arr[$key]['user_produksi']]["nama"];
                                                            echo '<i class="fa-solid fa-check text-success"></i> ' . $karyawan;
                                                        } else {
                                                            if ($do['id_toko'] == $this->userData['id_toko'] || $do['id_afiliasi'] == $this->userData['id_toko']) {
                                                                echo '<span class="done" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#done" data-id="' . $id_order_data . '" data-mode="1"> <i class="fa-regular fa-circle"></i> Tahap 1';
                                                            }
                                                        }
                                                        echo "</td>";
                                                        if ($divisi_arr[$key]['cm'] == 1) {
                                                            echo "<td class='pe-2 text-sm'>";
                                                            if ($divisi_arr[$key]['cm_status'] == 1) {
                                                                $karyawan = $this->dKaryawanAll[$divisi_arr[$key]['user_cm']]["nama"];
                                                                echo '<i class="fa-solid text-success fa-check-double"></i> ' . $karyawan;
                                                            } else {
                                                                if ($do['id_toko'] == $this->userData['id_toko'] || $do['id_afiliasi'] == $this->userData['id_toko']) {
                                                                    echo '<span class="done" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#done" data-id="' . $id_order_data . '" data-mode="2"> <i class="fa-regular fa-circle"></i> Tahap 2';
                                                                }
                                                            }
                                                            echo "</td>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    </small>

    <form action="<?= PV::BASE_URL; ?>SPK_C/done/<?= $parse ?>" method="POST">
        <div class="modal" id="done">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row mb-2">
                                <div class="col">
                                    <label class=" form-label">Karyawan</label>
                                    <input type="hidden" name="id">
                                    <input type="hidden" name="mode">
                                    <select class="border tize" name="id_karyawan" required>
                                        <option></option>
                                        <?php foreach ($data['karyawan'] as $k) {
                                            if ($k['id_toko'] == $this->userData['id_toko']) { ?>
                                                <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Selesai</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        var parse = <?= $parse ?>;
        var parse_2 = <?= (int)$data['customer'] ?>;

        function markFilterSelectReady() {
            var wrap = document.getElementById('filterSelectWrap');
            if (wrap) {
                wrap.classList.remove('is-loading');
                wrap.classList.add('is-ready');
                wrap.querySelector('.filter-select-mini-loader')?.setAttribute('aria-busy', 'false');
            }
            if (typeof hideContentLoader === 'function') {
                hideContentLoader();
            }
        }

        function pelangganSelectizeOptions() {
            return {
                valueField: 'id',
                labelField: 'nama',
                searchField: ['nama', 'no_hp', 'id'],
                create: false,
                render: {
                    option: function(item, escape) {
                        return '<div style="padding: 6px 15px;">' +
                            '<span>' + escape(item.inisial || '') + ' ' + escape(item.nama) + '</span>' +
                            ' #<small>' + (item.id ? escape(String(item.id)).substring(String(item.id).length - 2) : '') + '</small>' +
                            ' <br><small>' + escape(item.no_hp || '') + '</small>' +
                            '</div>';
                    },
                    item: function(item, escape) {
                        return '<div style="padding: 2px 10px;">' + escape(item.inisial || '') + ' ' + escape(item.nama) + '</div>';
                    }
                }
            };
        }

        function initFilterSelectize() {
            var $el = $('select.ajax-pelanggan');
            if (!$el.length || $el[0].selectize) {
                markFilterSelectReady();
                return;
            }
            var pelangganOpts = pelangganSelectizeOptions();
            pelangganOpts.options = <?= $data['pelanggan_init'] ?>;
            pelangganOpts.load = function(query, callback) {
                if (query.length < 2) {
                    return callback();
                }
                $.ajax({
                    url: '<?= PV::BASE_URL ?>SPK_Customer/search_pelanggan',
                    type: 'GET',
                    dataType: 'json',
                    data: { q: query },
                    error: function() { callback(); },
                    success: function(res) { callback(res); }
                });
            };
            $el.selectize(pelangganOpts);
            markFilterSelectReady();
        }

        $(document).ready(function() {
            initFilterSelectize();
            $('select.tize:not(.ajax-pelanggan)').each(function() {
                if (!this.selectize) {
                    $(this).selectize();
                }
            });
        });

        function getCustomerId() {
            return getSelectizeVal('select[name=customer]');
        }

        $(document).on('click', 'button.cek', function() {
            var customer = getCustomerId();
            if (!isValidPelangganId(customer)) {
                alert('Pilih pelanggan terlebih dahulu');
                var el = $('select[name=customer]')[0];
                if (el && el.selectize) {
                    el.selectize.focus();
                }
                return;
            }
            parse_2 = customer;
            if (typeof loadAppContent === 'function') {
                loadAppContent('<?= PV::BASE_URL ?>SPK_Customer/content/' + parse + '/' + customer);
            } else {
                $("div#content").load('<?= PV::BASE_URL ?>SPK_Customer/content/' + parse + '/' + customer);
            }
        });

        $(".push").click(function() {
            var id = $(this).attr('data-id');
            var val = $(this).attr('data-val');

            $.ajax({
                url: "<?= PV::BASE_URL ?>SPK_C/push",
                data: {
                    id: id,
                    push: val
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        $("div#content").load('<?= PV::BASE_URL ?>SPK_Customer/content/' + parse + '/' + parse_2);
                    } else {
                        alert(res);
                    }
                }
            });
        })

        $("span.done").click(function() {
            id = $(this).attr("data-id");
            mode = $(this).attr("data-mode");
            $("input[name=id]").val(id);
            $("input[name=mode]").val(mode);
        })

        $("form").on("submit", function(e) {
            e.preventDefault();

            var parse = <?= $parse ?>;
            var parse_2 = getCustomerId();
            if (!isValidPelangganId(parse_2)) {
                alert('Pilih pelanggan terlebih dahulu');
                return;
            }

            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                success: function(res) {
                    if (res == 0) {
                        $('button.cek').click();
                    } else {
                        alert(res);
                    }
                }
            });
        });
    </script>