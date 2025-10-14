    <table class="table table-sm text-sm">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'];
            $target_link = "Home";

            switch ($d['jenis']) {
                case 0:
                    $href = PV::BASE_URL . "Gudang_Input/list/" . $d['ref'];
                    break;
                case 1:
                    $href = PV::BASE_URL . "Stok_Transfer/list/" . $d['ref'];
                    break;
                case 2:
                    if ($d['id_sumber'] == $this->userData['id_toko']) {
                        $href = PV::BASE_URL . "Cek/order/" . $d['ref'] . "/" . $d['id_target'];
                    } else {
                        $href = PV::BASE_URL . "Gudang_Penjualan/list/" . $d['ref'];
                    }
                    break;
                case 3:
                    if ($d['id_sumber'] == 0) {
                        $href = PV::BASE_URL . "Retur_Barang_G/list/" . $d['ref'];
                    } else {
                        $href = PV::BASE_URL . "Gudang_BMasuk/list/" . $d['ref'];
                    }
                    break;
                default:
                    $href = "#";
                    break;
            } ?>

            <tr>
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
                            echo '<i class="fa-solid fa-arrow-up text-danger"></i>';
                            break;
                        case 4:
                            echo '<i class="fa-solid fa-arrow-up text-warning"></i>';
                            break;
                        case 3: //retur
                            if ($d['id_target'] == 0) {
                                echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            } else {
                                echo '<i class="fa-solid fa-arrow-up text-primary"></i>';
                            }
                            break;
                    } ?>
                </td>
                <td>
                    <?php
                    switch ($d['jenis']) {
                        case 0:
                            echo 'Masuk';
                            break;
                        case 1:
                            echo 'Transfer';
                            break;
                        case 2:
                            if ($d['id_sumber'] == 0) {
                                echo 'Gudang Jual';
                            } else {
                                echo 'ABF Jual';
                            }
                            break;
                        case 3:
                            echo 'Retur';
                            break;
                        case 4:
                            echo 'Pakai';
                            break;
                    } ?>
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
                            }
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