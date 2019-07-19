<?php

function rand_large_nbr()
{
    $result = 0;

    $result = random_int(0, PHP_INT_MAX);
    return $result;
}

?>