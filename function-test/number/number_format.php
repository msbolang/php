<?php
echo number_format("5000000")."\n";
echo number_format("5000000",2)."\n";
echo number_format("5000000",2,",",".");
echo "\n";
$num = 4999.9;
$formattedNum = number_format($num)."\n";
echo $formattedNum;
$formattedNum = number_format($num, 2);
echo $formattedNum;

