<?php

Class MyTools {

    /**
     * 
     * @param type $param
     * @return type
     * 將字符串中的逗號替換
     * 適合帶逗號的金額使用
     */
    public function strReplace($param) {
        return str_replace(',','',$param);
    }
    
}