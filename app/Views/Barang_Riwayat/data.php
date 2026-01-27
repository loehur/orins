    <div class="mb-2">
        <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">Semua</button>
        <button class="btn btn-sm btn-outline-success filter-btn" data-filter="Masuk">Masuk</button>
        <button class="btn btn-sm btn-outline-dark filter-btn" data-filter="Toko Jual">Toko Jual</button>
        <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Gudang Jual">Gudang Jual</button>
        <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Transfer">Transfer</button>
        <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="Retur">Retur</button>
        <button class="btn btn-sm btn-outline-info filter-btn" data-filter="Pakai">Pakai</button>
    </div>

    <table class="table table-sm text-sm" id="table-riwayat">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'];
            $target_link = "Home";
            
            $filter_type = "";
            switch ($d['jenis']) {
                case 0:
                    $href = PV::BASE_URL . "Gudang_Input/list/" . $d['ref'];
                    $filter_type = "Masuk";
                    break;
                case 1:
                    $href = PV::BASE_URL . "Stok_Transfer/list/" . $d['ref'];
                    $filter_type = "Transfer";
                    break;
                case 2:
                    if ($d['id_sumber'] == $this->userData['id_toko']) {
                        $href = PV::BASE_URL . "Cek/order/" . $d['ref'] . "/" . $d['id_target'];
                    } else {
                        $href = PV::BASE_URL . "Gudang_Penjualan/list/" . $d['ref'];
                    }
                    if ($d['id_sumber'] == 0) {
                        $filter_type = "Gudang Jual";
                    } else {
                        $filter_type = "Toko Jual";
                    }
                    break;
                case 3:
                    if ($d['id_sumber'] == 0) {
                        $href = PV::BASE_URL . "Retur_Barang_G/list/" . $d['ref'];
                    } else {
                        $href = PV::BASE_URL . "Gudang_BMasuk/list/" . $d['ref'];
                    }
                    $filter_type = "Retur";
                    break;
                case 4:
                    $filter_type = "Pakai"; // Assuming logic for Pakai
                    $href = "#";
                    break;
                default:
                    $href = "#";
                    break;
            }

            $target = "UNDEFINED"; ?>

            <tr class="data-row <?= $d['stat'] == 2 ? 'table-danger text-secondary' : '' ?>" data-filter="<?= $filter_type ?>">
                <td>#<?= $d['id'] ?></td>
                <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                <td><a target="_blank" href="<?= $href ?>"><?= $d['ref'] ?></a></td>
                <td><span data-id="<?= $d['id'] ?>" class="<?= strlen($d['sn']) > 0 ? 'cell_edit' : "" ?>"><?= $d['sn'] ?></span></td>
                <td class="align-middle">
                    <?php
                    switch ($d['jenis']) {
                        case 0:
                            echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            break;
                        case 1:
                            echo '<i class="fa-solid fa-arrow-right text-warning"></i>';
                            break;
                        case 2:
                            if ($d['id_target'] == 0) {
                                echo '<i class="fa-solid fa-arrow-up text-danger"></i>';
                            } else {
                                echo '<i class="fa-solid fa-arrow-up text-purple"></i>';
                            }
                            break;
                        case 3: //retur
                            if ($d['id_target'] == 0) {
                                echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            } else {
                                echo '<i class="fa-solid fa-arrow-up text-primary"></i>';
                            }
                            break;
                        case 4:
                            echo '<i class="fa-solid fa-arrow-up text-warning"></i>';
                            break;
                    } ?>
                </td>
                <td>
                    <?php
                    echo $filter_type;
                     ?>
                </td>
                <td class="">
                    <?php switch ($d['jenis']) {
                        case 0: //masuk
                            if ($d['id_target'] == 0) {
                                $target = isset($data['supplier'][$d['id_sumber']]['nama']) ? $data['supplier'][$d['id_sumber']]['nama'] : "UNDEFINED " . $d['id_sumber'];
                            }
                            break;
                        case 1: //transfer
                            if ($d['id_sumber'] == 0) {
                                $target = $this->dToko[$d['id_target']]['inisial'];
                            }
                            break;
                        case 2: //jual
                            $target = $data['pelanggan'][$d['id_target']]['nama'];
                            break;
                        case 3: //retur
                            if ($d['id_sumber'] == 0) {
                                if ($d['id_target'] == 0) {
                                    $target = "GUDANG";
                                } else {
                                    $target = $data['supplier'][$d['id_target']]['nama'];
                                }
                            } else {
                                $target = $this->dToko[$d['id_sumber']]['inisial'];
                            }
                            break;
                        case 4: //pakai
                            $target = strtoupper($data['akun_pakai'][$d['id_target']]['nama']);
                            break;
                        default:
                            $target = "UNDEFINED";
                            break;
                    } ?>
                    <span><?= $target ?></span>
                </td>
                <td>S#<span style="cursor: pointer;" data-id="<?= $d['id'] ?>" class='sds_edit'><?= $d['sds'] == 1 ? "1" : "0" ?></span> <small class="fw-bold"><?= $d['sds'] == 1 ? "SDS" : "ABF" ?></small></td>
                <td class="text-end"><?= $d['qty'] ?></td>
            </tr>
        <?php } ?>
    </table>

    <script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

    <script>
        $(".filter-btn").click(function() {
            $(".filter-btn").removeClass("active");
            $(this).addClass("active");
            var filter = $(this).attr("data-filter");
            
            if (filter == "all") {
                $(".data-row").show();
            } else {
                $(".data-row").hide();
                $(".data-row[data-filter='" + filter + "']").show();
            }
        });
        var click = 0;
        $(".cell_edit").on('click', function() {
            click = click + 1;
            if (click != 1) {
                return;
            }

            var id = $(this).attr('data-id');
            var value = $(this).html();
            var value_before = value;
            var tipe = "text";

            var el = $(this);
            var width = el.parent().width();
            var align = "left";

            el.parent().css("width", width);
            el.html("<input required type=" + tipe + " style='outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

            $("#value_").focus();
            $('#value_').keypress(function(e) {
                if (e.which == 13) {
                    $(this).blur();
                }
            });
            $("#value_").focusout(function() {
                var value_after = $(this).val().toUpperCase();
                if (value_after === value_before || value_after == "") {
                    el.html(value);
                    click = 0;
                } else {
                    $.ajax({
                        url: '<?= PV::BASE_URL ?>Barang_Riwayat/update_sn',
                        data: {
                            'id': id,
                            'value': value_after,
                        },
                        type: 'POST',
                        dataType: 'html',
                        success: function(res) {
                            click = 0;
                            if (res == 0) {
                                el.html(value_after);
                            } else {
                                el.html(value_before);
                            }
                        },
                    });
                }
            });
        });

        $(".sds_edit").on('dblclick', function() {
            var el = $(this);
            var id = $(this).attr('data-id');
            var value = $(this).html();
            $.ajax({
                url: '<?= PV::BASE_URL ?>Barang_Riwayat/update_sds',
                data: {
                    'id': id,
                    'value': value,
                },
                type: 'POST',
                dataType: 'html',
                success: function(res) {
                    $("#cek").click()
                },
            });
        });
    </script>