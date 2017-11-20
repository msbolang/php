<?php
class _base64 {
    public $str = '9b821c4ae9dc9b709b2636cb9933e136'; //返回的 HMAC是密钥相关的哈希运算消息认证码
    
    
      public $key = '3f0374b461284c1b8e61b6333a1cd96c'; //固定
      
//    public $p1_MerchantNo = '888100000003083';//传入
//    public $p2_OrderNo = '20171117104050709_6K';//传入
//    public $p3_Amount = '0.10';//传入
//    public $p4_Cur = 1;//传入
//    public $p5_ProductName = '澳門街商城購物訂單';  // 固定
//    public $p6_Mp = 123; // '公用回传参数' 固定
//    public $p7_ReturnUrl = 'http://www.xxx.com/payment/callback/pa/'; /  $this->callbackUrl = str_replace('plugins/', '', Url::fullUrlFormat("/payment/callback/payment_id/{$paymentId}/"));
//    public $p8_NotifyUrl = 'http://www.xxxx.com/payment/async_callback/p/';  // $this->asyncCallbackUrl = str_replace('plugins/', '', Url::fullUrlFormat("/payment/async_callback/payment_id/{$paymentId}/"));
//
//    public $pa_OrderPeriod = 120; //订单有效期  2小時 //固定  

      
public $r1_MerchantNo='888100000003083';
public $r2_OrderNo='20171117104050709_6K';
public $r3_Amount='0.10';
public $r4_Cur=1;
public $r5_Mp=123;
public $r6_Status=100;
public $r7_TrxNo='100217111732327290';
public $r8_BankOrderNo='100217111732327290';
public $r9_BankTrxNo='175550365935201711173101088356';
public $ra_PayTime='2017-11-17+10%3A41%3A37';
public $rb_DealTime='2017-11-17+10%3A41%3A37';
public $rc_BankCode='WEIXIN_PAY';
              
              
    private function base64Encode() {
        
    }

    //base64_decode 输出解码后的内容
    public function base64Decode() {
   
        echo urldecode($this->ra_PayTime);exit;
        $s = $this->Signature2();  
        var_dump($s);
        var_dump($this->str);
        var_dump($s===$this->str);
    }
    
    
      private function Signature2() {
          
        $str = $this->r1_MerchantNo .
                $this->r2_OrderNo .
                $this->r3_Amount .
                $this->r4_Cur .
                $this->r5_Mp .
                $this->r6_Status .
                $this->r7_TrxNo .
                $this->r8_BankOrderNo .
                $this->r9_BankTrxNo .
                urldecode($this->ra_PayTime) .
                urldecode($this->rb_DealTime) .
                $this->rc_BankCode;
  echo "\n";
         urldecode($this->ra_PayTime);
       echo "\n";
       echo $str;
       echo "\n";
        $Signa = md5($str . $this->key);
          echo "\n";
        return $Signa;
      }
    
    
    
    //验证签名1
    private function Signature() {
        echo 'p1_MerchantNo =' .$this->p1_MerchantNo,"\n";
        echo 'p2_OrderNo =' .$this->p2_OrderNo,"\n";
        echo 'p3_Amount = '.$this->p3_Amount,"\n";
        
         echo 'p4_Cur = '.$this->p4_Cur,"\n";
         echo 'p5_ProductName = '.$this->p5_ProductName,"\n";
          echo 'p6_Mp = '.$this->p6_Mp,"\n";
           echo 'p7_ReturnUrl = '.$this->p7_ReturnUrl,"\n";
            echo 'p8_NotifyUrl = '.$this->p8_NotifyUrl,"\n";
         echo 'pa_OrderPeriod = '.$this->pa_OrderPeriod,"\n";
        
        
        
        $str = $this->p1_MerchantNo . $this->p2_OrderNo . $this->p3_Amount . $this->p4_Cur . $this->p5_ProductName . $this->p6_Mp . $this->p7_ReturnUrl . $this->p8_NotifyUrl . $this->pa_OrderPeriod;
      
       echo "\n";
       echo $str;
       echo "\n";
        $Signa = md5($str . $this->key);
          echo "\n";
        return $Signa;
    }
    
}

