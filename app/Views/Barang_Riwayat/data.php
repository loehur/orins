    <table class="table table-sm text-sm">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'];
            $target_link = "Home";

            switch ($d['jenis']) {
                case 0:
                    $target_link = "Gudang_Input";
                    break;
                case 1:
                    $target_link = "Stok_Transfer";
                    break;
            } ?>

            <tr>
                <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                <td><a href="<?= PV::BASE_URL . $target_link ?>/list/<?= $d['ref'] ?>"><?= $d['ref'] ?></a></td>
                <td><span data-id="<?= $d['id'] ?>" class="<?= strlen($d['sn']) > 0 ? 'cell_edit' : "" ?>"><?= $d['sn'] ?></span></td>
                <td class="align-middle">
                    <?php
                    switch ($d['jenis']) {
                        case 0:
                            echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            break;
                        case 1:
                        case 2:
                            echo '<i class="fa-solid fa-arrow-up text-danger"></i>';
                            break;
                        case 3:
                            if ($d['id_target'] == 0) {
                                echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            } else {
                                echo '<i class="fa-solid fa-arrow-up text-danger"></i>';
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
                            echo 'Jual';
                            break;
                        case 3:
                            echo 'Retur';
                            break;
                    } ?>
                </td>
                <td class="">
                    <?php switch ($d['jenis']) {
                        case 0:
                            $target = isset($data['supplier'][$d['id_sumber']]['nama']) ? $data['supplier'][$d['id_sumber']]['nama'] : "UNDEFINED " . $d['id_sumber'];
                            break;
                        case 1:
                            $target = $this->dToko[$d['id_target']]['inisial'];
                            break;
                        case 2:
                            $target = $data['pelanggan'][$d['id_target']]['nama'];
                            break;
                        case 3:
                            if ($d['id_target'] == 0) {
                                $target = "GUDANG";
                            } else {
                                $target = $data['supplier'][$d['id_target']]['nama'];
                            }
                            break;
                    } ?>
                    <span><?= $target ?></span>
                </td>
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
    </script>