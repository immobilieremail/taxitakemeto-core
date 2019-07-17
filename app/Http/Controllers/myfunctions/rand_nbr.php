<?php

function rand_large_nbr()
{
    $result = 0;

    for ($i = 1; $result < 99999999999999999; $i = $i * 10)
        $result += rand(0, 9) * $i;
    while ($result > 18446744073709551615)
        $result /= 10;
    return $result;
}

?>