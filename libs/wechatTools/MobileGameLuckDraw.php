<?php
namespace app\components;
use Yii;
use yii\base\ActionFilter;
use app\models\MobileGameOne;
use app\models\LmActivities;
use app\models\GPlaces;
/* 
 * MobileGameOne
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
class MobileGameLuckDraw {
   
    public $_prize;
    public $_aid;
    public $_wxid;
    public function __construct($_wxid) {
        
         $this->_wxid = $_wxid;
         
        }
    
    public function luckDraw($aid) {
        
         if(!$this->getPrizeInfo($aid)){
             return false;
         }
      
         foreach ($this->_prize['mobile'] as $key => $value) {
                    
                    if($value['prize_num']>0) { 
                        $goodLuck = $this->randPrize($this->setPirzes(intval($value['probability'])));
                          if ($goodLuck == 1) {
                          
                              if($this->modifyPrizeData($value['id'])){
                         
                              //  Mytool::L("WinningInformation:|wxid:| $this->_wxid |getprize:".$value['id'],'class_mobileGameLuckDraw');
                                return $value;
                              }
                         }
                    }
                }
        return false;
        
    }
    
    
    private function randPrize($prizearr) {
        $result = '';
        $proSum = array_sum($prizearr);
        foreach ($prizearr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum); 
            if ($randNum <= $proCur) { 
                $result = $key;
                break;
            } else {
                $proSum -= $proCur; 
            }
        }
        unset($prizearr);
        return $result;
    }
    
    
      private function setPirzes($num) {
        if (is_numeric($num) AND $num <= 100 && $num > 0) {
            $no = 100 - $num;
        } else {
            $no = 100;
            $num = 0;
        }
        $prize_arr = array(
            '0' => $no,
            '1' => $num,
        );
        return $prize_arr;
    }
    
    
  





    private function modifyPrizeData($prizeId) {
        
        $time = time();
        //修改獎品數量 事務處理
        $transaction = Yii::$app->db->beginTransaction(); //如果寫在鎖表裡面添加事務 那麼鎖表則不起作用 
        try {
            $sql = "update mobile_game_one set prize_num = prize_num-1 where id='$prizeId' AND prize_num>0";
            $updatExchange = Yii::$app->db->createCommand($sql)->execute();
            if (!$updatExchange) {
                throw new Exception("private function modifyPrizeData update mobile_game_one ERROR"); 
            }
      
            $sql_1 = "INSERT INTO g_places (`a_id`,`s_id`, `u_id`, `d_time`, `m_prize`) VALUES ('$this->_aid', '$prizeId' ,'$this->_wxid', '$time', '1')";
            $InsertGPlaces = Yii::$app->db->createCommand($sql_1)->execute();
            if (!$InsertGPlaces) {
                throw new Exception("private function modifyPrizeData INSERT INTO g_places ERROR");
            }
            $transaction->commit();
            
        } catch (Exception $e) {
            
            $transaction->rollBack();
            $errorInfo = $e->getMessage();
            
            //  Mytool::L("error:|$errorInfo",'class_mobileGameLuckDraw');
            return 0;
        }
        return 1;
    }
    
    
    private function getPrizeInfo($aid) {
        
          $this->_prize = LmActivities::find()
               ->With("mobile")
                ->asarray()
                ->where(['id' => $aid])
                ->one();
        
        if (empty($this->_prize)) {
           //  Mytool::L("error:|class mobileGameOne:function getPrizeInfo 1",'class_mobileGameLuckDraw');
       
             return false;
        } 
        
       if(empty($this->_prize['mobile'])){
           //  Mytool::L("error:|class mobileGameOnefunction getPrizeInfo 2",'class_mobileGameLuckDraw');
             return false;
         } 
        
        $this->_aid = $aid; 
        return true;
    }
    
}

