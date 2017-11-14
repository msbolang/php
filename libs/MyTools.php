<?php


Class MyTools {

    public $logFile;
    public $logDirPath = '/var/www/html/macauo2o/logs/';

    public function __construct() {
        
    }

    public function setLogFile($logFile) {
        $this->logFile = $logFile;
    }

    /**
     * 
     * @param type $param
     * @return type
     * 將字符串中的逗號替換
     * 適合帶逗號的金額使用
     */
    public function strReplace($param) {
        return str_replace(',', '', $param);
    }

    public function L($str_msg = '', $str) {

        if (!$this->logFile) {
            $this->EC('no LogFile', null);
        }
        $time = date('Y-m-d H:i:s', time());

        if (is_array($str)) {
            $str = $time . ' ||| type:array ||| info::' . json_encode($str);
        } elseif (is_object($str)) {
            $str = $time . ' ||| type:object ||| info::' . json_encode($str);
        } else {
            $str = $time . ' ||| type:str ||| info::' . $str;
        }
        $str = $str_msg . "：\r\n" . $str . "\r\n";
        error_log($str, 3, $this->logDirPath . date('Y-m-d', time()) . '_' . $this->logFile . '.txt');
    }

    public static function EC($msg, $param) {
        echo '<br>';
        echo $msg;
        echo '<br>';
        echo '<pre>';

        if (is_array($param)) {
            print_r($param);
        } else {
            var_dump($param);
        }
        exit;
    }


    
    //隨機生成字符串
    public function _rundStr($length = 8) {
        // $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        //$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
    

}


$tool = new MyTools();
echo $tool->_rundStr(14);


