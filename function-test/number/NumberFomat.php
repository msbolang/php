<?php


class NumberFomat {
    public function _numberFormt() {
        echo number_format("5000000") . "\n";//5,000,000
        echo number_format("5000000", 2) . "\n";//5,000,000.00
        echo number_format("5000000", 2, ",", ".");//5.000.000,00
        echo "\n";
        $num = 4554455999.95454;
        $formattedNum = number_format($num) . "\n";
        echo $formattedNum; //4,554,456,000
        $formattedNum = number_format($num, 2, '.', '');
        echo $formattedNum;//4554455999.95
    }

}
