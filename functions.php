<?php

function slideNumbers ($images) {
    $str = [];

    for($i = 1; $i <= count($images); $i++) {
        $str[] = $i;
    }

    return implode(', ', $str);
}