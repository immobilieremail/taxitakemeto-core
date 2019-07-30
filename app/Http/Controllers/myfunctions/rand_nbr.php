<?php

function rand_large_nbr()
{
    $result = str_replace("/", "", base64_encode(gmp_export(gmp_random_bits(128))));
    return $result;
}

?>