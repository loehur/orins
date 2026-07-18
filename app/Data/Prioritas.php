<?php

class Prioritas
{
    public function menuData($ctrl, $title = '')
    {
        $data = [
            'title' => $title,
            'show_aff' => in_array($ctrl->userData['user_tipe'], PV::PRIV[3]),
            'show_spk' => in_array($ctrl->userData['user_tipe'], PV::PRIV[4]),
            'aff' => [],
            'aff_c' => 0,
            'list_l' => [],
            'lanjut_c' => 0,
        ];

        if ($data['show_aff']) {
            $cols = "id_toko, id_pelanggan, ref";
            $where = "id_afiliasi = " . $ctrl->userData['id_toko'] . " AND id_penerima <> 0 AND (id_user_afiliasi = 0 OR status_order = 1) AND cancel = 0 GROUP BY id_toko, id_pelanggan, ref";
            $aff = $ctrl->db(0)->get_cols_where('order_data', $cols, $where, 1);
            if (is_array($aff) && !isset($aff['errno'])) {
                $data['aff'] = $aff;
                $data['aff_c'] = count($aff);
            }
        }

        if ($data['show_spk']) {
            $where = "(id_toko = " . $ctrl->userData['id_toko'] . " OR id_afiliasi = " . $ctrl->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND id_ambil = 0 AND spk_lanjutan <> '' ORDER BY id_order_data DESC";
            $data_spk_lnjut = $ctrl->db(0)->get_cols_where('order_data', 'ref, spk_lanjutan, spk_dvs', $where, 1);
            if (is_array($data_spk_lnjut) && !isset($data_spk_lnjut['errno'])) {
                $refs_spk_lnjut = [];
                $list_l = [];
                foreach ($data_spk_lnjut as $ds) {
                    $spk = explode('#', str_replace('D-', '', $ds['spk_lanjutan'] ?? ''));
                    $spk_dvs = (strlen($ds['spk_dvs'] ?? '') > 1) ? @unserialize($ds['spk_dvs']) : [];
                    if (!is_array($spk_dvs)) {
                        $spk_dvs = [];
                    }
                    $ada_pending = false;
                    foreach ($spk as $sl) {
                        if ($sl !== '' && isset($ctrl->dDvs[$sl])) {
                            $list_l[$sl] = 1;
                            $dv = $spk_dvs[$sl] ?? [];
                            $status = (int)($dv['status'] ?? 0);
                            $cm = (int)($dv['cm'] ?? 0);
                            $cm_status = (int)($dv['cm_status'] ?? 0);
                            $done = ($status == 1 && ($cm != 1 || $cm_status == 1));
                            if (!$done) {
                                $ada_pending = true;
                            }
                        }
                    }
                    if ($ada_pending) {
                        $refs_spk_lnjut[$ds['ref']] = 1;
                    }
                }
                $data['list_l'] = array_keys($list_l);
                $data['lanjut_c'] = count($refs_spk_lnjut);
            }
        }

        return $data;
    }
}
