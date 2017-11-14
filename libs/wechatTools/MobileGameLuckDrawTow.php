<?php
namespace app\components;
use Yii;
use yii\base\ActionFilter;
use app\models\MobileGameOne;
use app\models\LmActivities;
use app\models\GPlaces;
/* 
 * MobileGameOne  第二版移动 投筛子游戏 限制 走6步
 */
use app\components\Mytool;
use app\components\Wechathandle;
class MobileGameLuckDrawTow {
   
    public $_prize;
    public $_aid;
    public $_wxid;
    public $tool;
    public function __construct($_wxid) {
         $this->tool = new Mytool();
         $this->tool->setLogFile('MobileGameLuckDrawTow');
         $this->_wxid = $_wxid;
         
        }
    
    public function luckDraw($aid,$myPosition) {
   
         if(!$this->getPrizeInfo($aid)){
             return false;
         }
       
         foreach ($this->_prize['mobile'] as $key => $value) {
                    
                    if($value['prize_num']>0) { 
                        $goodLuck = $this->randPrize($this->setPirzes(intval($value['probability'])));
                          if ($goodLuck == 1) {
                            
                              //奖品位置是否超出6步
                          if($this->_howManySteps($value,$myPosition)){
                             
                              if($this->modifyPrizeData($value['id'])){
                               // Mytool::L("WinningInformation:|wxid:| $this->_wxid |getprize:".$value['id'],'class_MobileGameLuckDrawTow');
                                  $this->tool->L("WinningInformation:|wxid:| $this->_wxid |getprize:".$value['id'],'class_MobileGameLuckDrawTow');
                                return $value;
                              }
                          }else{
                              return false;
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
            $this->tool->L("error:|$errorInfo",'class_MobileGameLuckDrawTow');
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
             $this->tool->L("error:|class mobileGameOne:function getPrizeInfo 1",'class_MobileGameLuckDrawTow');
       
             return false;
        } 
        
       if(empty($this->_prize['mobile'])){
             $this->tool->L("error:|class mobileGameOnefunction getPrizeInfo 2",'class_MobileGameLuckDrawTow');
             return false;
         } 
        
        $this->_aid = $aid; 
        return true;
    }
    
    
       /**
     * 
     * @param type $prize
     * @return boolean
     * 抽奖计算是否大于6步，如大于6步 不再抽奖 做 空奖处理 
     */
    private function _howManySteps($prize,$myPosition) {
        $destination = null;

        if($prize['prize_type']=='1') {
      
           $destination = 8;
        }
        
        if($prize['prize_type']=='2') {
       
           $destination = 16;
        }
        
        if($prize['prize_type']=='3') {
        
            $destination = 12;
        }
        
        if(!$destination){
             $this->tool->L('destination为空');
            return false;
        }
   
        
        if($myPosition>$destination) {
             $go = 16-$myPosition+$destination;
         }else{
             $go = $myPosition-$destination;
         }
         
       if($go==0){$go=16;};
       
     
       if(abs($go)>6){
           
            $prize_type =  $prize['prize_type'];
           $this->tool->L("抽中奖品 $prize_type 但是要走的步数大于6");
            return false;
        }
        return true;
    }
}

