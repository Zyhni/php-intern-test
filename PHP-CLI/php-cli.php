<?php
$n = 7;

for($i = 0; $i < $n; $i++) {
    $baris=[];
    for($j = 0; $j < $n; $j++) {
        if($j == $i || $j == ($n - 1 - $i)) {
            $baris[] = 'X';
        } else {
            $baris[] = 'O';
        }
    }
    echo implode(' ', $baris) . PHP_EOL;
}