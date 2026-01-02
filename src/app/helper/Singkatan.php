<?php
function singkatanMatkul($nama) {
    $kata = preg_split('/\s+/', trim($nama));
    $singkatan = '';

    foreach ($kata as $k) {
        if (strlen($k) > 0) {
            $singkatan .= strtoupper($k[0]);
        }
    }

    return $singkatan;
}
?>