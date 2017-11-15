<?php

//订单处理类
class HendlerXML {

    public $_app = array();

    function __construct() {
        $this->_app['appTime'] = date('YmdHis', time());
    }

    public function getXml($id, $order) {

        $this->_app['guid'] = $order['guid']; //企業統一生成32位唯一序列號
        $this->_app['orderNo'] = $order['orderNo']; //交易平台的订单编号，同一交易平台的订单编号应唯一。订单编号长度不能超过60位。
        $this->_app['goodsValue'] = $order['goodsValue']; // 商品实际成交价，含非现金抵扣金额。
        $this->_app['freight'] = $order['freight']; //運費 沒有就填0 不包含在商品价格中的运杂费，无则填写"0"。
        $this->_app['taxTotal'] = $order['taxTotal']; //稅費 企业预先代扣的税款金额，无则填写“0”
        $this->_app['acturalPaid'] = $order['acturalPaid']; //商品价格+运杂费+代扣税款-非现金抵扣金额，与支付凭证的支付金额一致。
        $this->_app['currency'] = '142'; //限定为人民币，填写“142”。
        $this->_app['buyerIdType'] = '1'; //1-身份证,2-其它。限定为身份证，填写“1”。
        $this->_app['buyerRegNo'] = $order['buyerRegNo']; // 订购人的交易平台注册号。 用戶名 手機號
        $this->_app['buyerName'] = $order['buyerName']; //订购人的真实姓名。
        $this->_app['buyerIdNumber'] = $order['buyerIdNumber']; //订购人的身份证件号码。
        $this->_app['consignee'] = $order['consignee']; // 收货人姓名，必须与电子运单的收货人姓名一致。
        $this->_app['consigneeTelephone'] = $order['consigneeTelephone']; // 收货人联系电话，必须与电子运单的收货人电话一致。
        $this->_app['consigneeAddress'] = $order['consigneeAddress']; // 收货地址，必须与电子运单的收货地址一致。

        $xml = new XMLWriter();
        $fileName = date('Y-m-d',  time()).'_'.$id;
        $_fileName = "/var/www/html/macauo2o/XML/$fileName.xml";
        $xml->openUri("$_fileName");
        $xml->setIndentString('   ');
        $xml->setIndent(true);

// start
        $xml->startDocument('1.0', 'UTF-8');

// <rss version="2.0">
        $xml->startElement('ceb:CEB311Message');
        $xml->writeAttribute('guid', $this->_app['guid']);
        $xml->writeAttribute('version', '1.0');
        $xml->writeAttribute('xmlns:ceb', 'http://www.chinaport.gov.cn/ceb');
        $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        // <channel> 
        $xml->startElement('ceb:Order');
        $xml->startElement('ceb:OrderHead');

        $xml->startElement('ceb:guid'); //企业系统生成36位唯一序号（英文字母大写）。
        $xml->text($this->_app['guid']);
        $xml->endElement();

        $xml->startElement('ceb:appType'); //企业报送类型。1-新增 2-变更 3-删除。默认为1。
        $xml->text('1');
        $xml->endElement();

        $xml->startElement('ceb:appTime'); //企业报送时间。格式:YYYYMMDDhhmmss。
        $xml->text($this->_app['appTime']);
        $xml->endElement();

        $xml->startElement('ceb:appStatus'); //业务状态:1-暂存,2-申报,默认为2。
        $xml->text('2');
        $xml->endElement();

        $xml->startElement('ceb:orderType'); //电子订单类型：I进口
        $xml->text('I');
        $xml->endElement();

        $xml->startElement('ceb:orderNo'); //交易平台的订单编号，同一交易平台的订单编号应唯一。订单编号长度不能超过60位。
        $xml->text($this->_app['orderNo']);
        $xml->endElement();

        $xml->startElement('ceb:ebpCode'); //电商平台的海关注册登记编号；电商平台未在海关注册登记，由电商企业发送订单的，以中国电子口岸发布的电商平台标识编号为准
        $xml->text('440454007V');
        $xml->endElement();

        $xml->startElement('ceb:ebpName');
        $xml->text('珠海市玛嘉斯科技有限公司');
        $xml->endElement();

        $xml->startElement('ceb:ebcCode');
        $xml->text('440454007V');
        $xml->endElement();

        $xml->startElement('ceb:ebcName');
        $xml->text('珠海市玛嘉斯科技有限公司');
        $xml->endElement();

        $xml->startElement('ceb:goodsValue'); //$this->_app['goodsValue'] 商品实际成交价，含非现金抵扣金额。
        $xml->text($this->_app['goodsValue']);
        $xml->endElement();

        $xml->startElement('ceb:freight'); //$this->_app['freight'] 運費 沒有就填0 不包含在商品价格中的运杂费，无则填写"0"。
        $xml->text($this->_app['freight']);
        $xml->endElement();

        $xml->startElement('ceb:discount'); //非现金抵扣金额 使用积分、虚拟货币、代金券等非现金支付金额，无则填写"0"。
        $xml->text('0');
        $xml->endElement();

        $xml->startElement('ceb:taxTotal'); //$this->_app['taxTotal'] 稅費 企业预先代扣的税款金额，无则填写“0”
        $xml->text($this->_app['taxTotal']);
        $xml->endElement();

        $xml->startElement('ceb:acturalPaid'); //$this->_app['acturalPaid'] 商品价格+运杂费+代扣税款-非现金抵扣金额，与支付凭证的支付金额一致。
        $xml->text($this->_app['acturalPaid']);
        $xml->endElement();

        $xml->startElement('ceb:currency'); //$this->_app['currency'] 限定为人民币，填写“142”。
        $xml->text($this->_app['currency']);
        $xml->endElement();

        $xml->startElement('ceb:buyerRegNo'); //$this->_app['buyerRegNo'] 订购人的交易平台注册号。
        $xml->text($this->_app['buyerRegNo']);
        $xml->endElement();

        $xml->startElement('ceb:buyerName'); //$this->_app['buyerName'] 订购人的真实姓名。
        $xml->text($this->_app['buyerName']);
        $xml->endElement();

        $xml->startElement('ceb:buyerIdType'); //$this->_app['buyerIdType'] 1-身份证,2-其它。限定为身份证，填写“1”。
        $xml->text($this->_app['buyerIdType']);
        $xml->endElement();

        $xml->startElement('ceb:buyerIdNumber'); //$this->_app['buyerIdNumber'] 订购人的身份证件号码。
        $xml->text($this->_app['buyerIdNumber']);
        $xml->endElement();

        $xml->startElement('ceb:payCode'); //支付企业的海关注册登记编号。 否
        $xml->text('');
        $xml->endElement();
        $xml->startElement('ceb:payName'); //支付企业在海关注册登记的企业名称。 否
        $xml->text('');
        $xml->endElement();
        $xml->startElement('ceb:payTransactionId'); //支付企业唯一的支付流水号。 否
        $xml->text('');
        $xml->endElement();
        $xml->startElement('ceb:batchNumbers'); //商品批次号。 否
        $xml->text('');
        $xml->endElement();

        $xml->startElement('ceb:consignee'); //$this->_app['consignee'] 收货人姓名，必须与电子运单的收货人姓名一致。
        $xml->text($this->_app['consignee']);
        $xml->endElement();

        $xml->startElement('ceb:consigneeTelephone'); //$this->_app['consigneeTelephone'] 收货人联系电话，必须与电子运单的收货人电话一致。
        $xml->text($this->_app['consigneeTelephone']);
        $xml->endElement();

        $xml->startElement('ceb:consigneeAddress'); //$this->_app['consigneeAddress'] 收货地址，必须与电子运单的收货地址一致。
        $xml->text($this->_app['consigneeAddress']);
        $xml->endElement();

        $xml->startElement('ceb:consigneeDistrict'); //参照国家统计局公布的国家行政区划标准填制。 否
        $xml->text('');
        $xml->endElement();
        $xml->startElement('ceb:note'); //備註  否
        $xml->text('');
        $xml->endElement();





        $xml->endElement();   //ceb:OrderHead
        //foreach 循環order商品
        foreach ($order['orderGoods'] as $key => $value) {


            $xml->startElement('ceb:OrderList');

            $xml->startElement('ceb:gnum'); //商品序号 从1开始的递增序号。 key+1
            $xml->text($key + 1);
            $xml->endElement();

            $xml->startElement('ceb:itemNo'); //电商企业自定义的商品货号（SKU）。  否
            $xml->text('');
            $xml->endElement();

            $xml->startElement('ceb:itemName'); //交易平台销售商品的中文名称。 應該是備案名稱
            $xml->text($value['filing_name']);
            $xml->endElement();

            $xml->startElement('ceb:itemDescribe'); //交易平台销售商品的描述信息。 否
            $xml->text('');
            $xml->endElement();

            $xml->startElement('ceb:barCode'); //国际通用的商品条形码，一般由前缀部分、制造厂商代码、商品代码和校验码组成。  否
            $xml->text('');
            $xml->endElement();

            $xml->startElement('ceb:unit'); // 填写海关标准的参数代码，参照《JGS-20 海关业务代码集》- 计量单位代码。
            $xml->text($this->_dw($value['unit']));
            $xml->endElement();

            $xml->startElement('ceb:qty'); //商品实际数量。
            $xml->text($value['goods_nums']);
            $xml->endElement();

            $xml->startElement('ceb:price'); //商品单价。赠品单价填写为“0”。
            $xml->text($value['real_price']);
            $xml->endElement();

            $xml->startElement('ceb:totalPrice'); //商品总价，等于单价乘以数量。
            $xml->text(number_format($value['real_price'] * $value['real_price'], 2));
            $xml->endElement();

            $xml->startElement('ceb:currency'); //限定为人民币，填写“142”。
            $xml->text('142');
            $xml->endElement();

            $xml->startElement('ceb:country'); //填写海关标准的参数代码，参照《JGS-20 海关业务代码集》-国家（地区）代码表。
            $xml->text($this->countryNumber($value['country']));
            $xml->endElement();

            $xml->startElement('ceb:note'); //促销活动，商品单价偏离市场价格的，可以在此说明。  否
            $xml->text('');
            $xml->endElement();

            $xml->endElement();
        }
        //foreach 循環order商品 END


        $xml->endElement(); // ceb:Order


        $xml->startElement('ceb:BaseTransfer');

        $xml->startElement('ceb:copCode'); //报文传输的企业代码（需要与接入客户端的企业身份一致）

        $xml->text('440454007V');
        $xml->endElement();

        $xml->startElement('ceb:copName'); //报文传输的企业名称
        $xml->text('珠海市玛嘉斯科技有限公司');
        $xml->endElement();

        $xml->startElement('ceb:dxpMode'); //默认为DXP；指中国电子口岸数据交换平台
        $xml->text('DXP');
        $xml->endElement();

        $xml->startElement('ceb:dxpId'); //向中国电子口岸数据中心申请数据交换平台的用户编号
        $xml->text('DXPENT0000014004');
        $xml->endElement();

        $xml->startElement('ceb:note'); //備註 否
        $xml->text('');
        $xml->endElement();


        $xml->endElement();  //ceb:BaseTransfer

        $xml->endElement();  // ceb:CEB311Message

        $xml->endDocument();
        //   $xml->flush();
 
        header('Content-type: text/xml'); 
        header('Content-Disposition: attachment; filename="'.$fileName.'.xml"');
        readfile("$_fileName"); 
        exit(); 
    }
    
    
    
    
    private function countryNumber($countryName) {
        $tw = $this->countListTw();
        foreach ($tw as $key => $value) {
            if($value==$countryName) {
                return $key;
            }
        }
        
        $cn = $this->countListCn();
        foreach ($cn as $key => $value) {
            if($value==$countryName) {
                return $key;
            }
        }
        return '*********';
        
    }


    private function _dw($d) {
        $str = '001=台%%%002=座%%%003=辆%%%004=艘%%%005=架%%%006=套%%%007=个%%%008=只%%%009=头%%%010=张%%%011=件%%%012=支%%%013=枝%%%014=根%%%015=条%%%016=把%%%017=块%%%018=卷%%%019=副%%%020=片%%%021=组%%%022=份%%%023=幅%%%025=双%%%026=对%%%027=棵%%%028=株%%%029=井%%%030=米%%%031=盘%%%032=平方米%%%033=立方米%%%034=筒%%%035=千克%%%036=克%%%037=盆%%%038=万个%%%039=具%%%040=百副%%%041=百支%%%042=百把%%%043=百个%%%044=百片%%%045=刀%%%046=疋%%%047=公担%%%048=扇%%%049=百枝%%%050=千只%%%051=千块%%%052=千盒%%%053=千枝%%%054=千个%%%055=亿支%%%056=亿个%%%057=万套%%%058=千张%%%059=万张%%%060=千伏安%%%061=千瓦%%%062=千瓦时%%%063=千升%%%067=英尺%%%070=吨%%%071=长吨%%%072=短吨%%%073=司马担%%%074=司马斤%%%075=斤%%%076=磅%%%077=担%%%078=英担%%%079=短担%%%080=两%%%081=市担%%%083=盎司%%%084=克拉%%%085=市尺%%%086=码%%%088=英寸%%%089=寸%%%095=升%%%096=毫升%%%097=英加仑%%%098=美加仑%%%099=立方英尺%%%101=立方尺%%%110=平方码%%%111=平方英尺%%%112=平方尺%%%115=英制马力%%%116=公制马力%%%118=令%%%120=箱%%%121=批%%%122=罐%%%123=桶%%%124=扎%%%125=包%%%126=箩%%%127=打%%%128=筐%%%129=罗%%%130=匹%%%131=册%%%132=本%%%133=发%%%134=枚%%%135=捆%%%136=袋%%%139=粒%%%140=盒%%%141=合%%%142=瓶%%%143=千支%%%144=万双%%%145=万粒%%%146=千粒%%%147=千米%%%148=千英尺%%%149=百万贝可%%%163=部%%%001=臺%%%002=座%%%003=輛%%%004=艘%%%005=架%%%006=套%%%007=個%%%008=只%%%009=頭%%%010=張%%%011=件%%%012=支%%%013=枝%%%014=根%%%015=條%%%016=把%%%017=塊%%%018=卷%%%019=副%%%020=片%%%021=組%%%022=份%%%023=幅%%%025=雙%%%026=對%%%027=棵%%%028=株%%%029=井%%%030=米%%%031=盤%%%032=平方米%%%033=立方米%%%034=筒%%%035=千克%%%036=克%%%037=盆%%%038=萬個%%%039=具%%%040=百副%%%041=百支%%%042=百把%%%043=百個%%%044=百片%%%045=刀%%%046=疋%%%047=公擔%%%048=扇%%%049=百枝%%%050=千只%%%051=千塊%%%052=千盒%%%053=千枝%%%054=千個%%%055=億支%%%056=億個%%%057=萬套%%%058=千張%%%059=萬張%%%060=千伏安%%%061=千瓦%%%062=千瓦時%%%063=千升%%%067=英尺%%%070=噸%%%071=長噸%%%072=短噸%%%073=司馬擔%%%074=司馬斤%%%075=斤%%%076=磅%%%077=擔%%%078=英擔%%%079=短擔%%%080=兩%%%081=市擔%%%083=盎司%%%084=克拉%%%085=市尺%%%086=碼%%%088=英寸%%%089=寸%%%095=升%%%096=毫升%%%097=英加侖%%%098=美加侖%%%099=立方英尺%%%101=立方尺%%%110=平方碼%%%111=平方英尺%%%112=平方尺%%%115=英制馬力%%%116=公制馬力%%%118=令%%%120=箱%%%121=批%%%122=罐%%%123=桶%%%124=紮%%%125=包%%%126=籮%%%127=打%%%128=筐%%%129=羅%%%130=匹%%%131=冊%%%132=本%%%133=發%%%134=枚%%%135=捆%%%136=袋%%%139=粒%%%140=盒%%%141=合%%%142=瓶%%%143=千支%%%144=萬雙%%%145=萬粒%%%146=千粒%%%147=千米%%%148=千英尺%%%149=百萬貝可%%%163=部';
        $arr = explode('%%%',$str);
        foreach ($arr as $key => $value) {
             $_arr = explode('=', $value);
             if($_arr[1]==$d){ return $_arr[0]; }
        }
        return '*********';
    }
    
    private function countListTw() {
        $list_tw = array(
            101=>'阿富汗',
            102=>'巴林',
            103=>'孟加拉國',
            104=>'不丹',
            105=>'文萊',
            106=>'緬甸',
            107=>'柬埔寨',
            108=>'塞浦路斯',
            109=>'朝鮮',
            110=>'香港',
            111=>'印度',
            112=>'印度尼西亞',
            113=>'伊朗',
            114=>'伊拉克',
            115=>'以色列',
            116=>'日本',
            117=>'約旦',
            118=>'科威特',
            119=>'老撾',
            120=>'黎巴嫩',
            121=>'澳門',
            122=>'馬來西亞',
            123=>'馬爾代夫',
            124=>'蒙古',
            125=>'尼泊爾聯邦民主共和國',
            126=>'阿曼',
            127=>'巴基斯坦',
            128=>'巴勒斯坦',
            129=>'菲律賓',
            130=>'卡塔爾',
            131=>'沙特阿拉伯',
            132=>'新加坡',
            133=>'韓國',
            134=>'斯裏蘭卡',
            135=>'敘利亞',
            136=>'泰國',
            137=>'土耳其',
            138=>'阿聯酋',
            139=>'也門',
            141=>'越南',
            142=>'中國',
            143=>'臺澎金馬關稅區',
            144=>'東帝汶',
            145=>'哈薩克斯坦',
            146=>'吉爾吉斯斯坦',
            147=>'塔吉克斯坦',
            148=>'土庫曼斯坦',
            149=>'烏茲別克斯坦',
            199=>'亞洲其他國家(地區)',
            201=>'阿爾及利亞',
            202=>'安哥拉',
            203=>'貝寧',
            204=>'博茨瓦那',
            205=>'布隆迪',
            206=>'喀麥隆',
            207=>'加那利群島',
            208=>'佛得角',
            209=>'中非',
            210=>'塞蔔泰(休達)',
            211=>'乍得',
            212=>'科摩羅',
            213=>'剛果(布)',
            214=>'吉布提',
            215=>'埃及',
            216=>'赤道幾內亞',
            217=>'埃塞俄比亞',
            218=>'加蓬',
            219=>'岡比亞',
            220=>'加納',
            221=>'幾內亞',
            222=>'幾內亞比紹',
            223=>'科特迪瓦',
            224=>'肯尼亞',
            225=>'利比裏亞',
            226=>'利比亞',
            227=>'馬達加斯加',
            228=>'馬拉維',
            229=>'馬裏',
            230=>'毛裏塔尼亞',
            231=>'毛裏求斯',
            232=>'摩洛哥',
            233=>'莫桑比克',
            234=>'納米比亞',
            235=>'尼日爾',
            236=>'尼日利亞',
            237=>'留尼汪',
            238=>'盧旺達',
            239=>'聖多美和普林西比',
            240=>'塞內加爾',
            241=>'塞舌爾',
            242=>'塞拉利昂',
            243=>'索馬裏',
            244=>'南非',
            245=>'西撒哈拉',
            246=>'蘇丹',
            247=>'坦桑尼亞',
            248=>'多哥',
            249=>'突尼斯',
            250=>'烏幹達',
            251=>'布基納法索',
            252=>'剛果(金)',
            253=>'贊比亞',
            254=>'津巴布韋',
            255=>'萊索托',
            256=>'梅利利亞',
            257=>'斯威士蘭',
            258=>'厄立特裏亞',
            259=>'馬約特',
            260=>'南蘇丹共和國',
            299=>'非洲其他國家(地區)',
            301=>'比利時',
            302=>'丹麥',
            303=>'英國',
            304=>'德國',
            305=>'法國',
            306=>'愛爾蘭',
            307=>'意大利',
            308=>'盧森堡',
            309=>'荷蘭',
            310=>'希臘',
            311=>'葡萄牙',
            312=>'西班牙',
            313=>'阿爾巴尼亞',
            314=>'安道爾',
            315=>'奧地利',
            316=>'保加利亞',
            318=>'芬蘭',
            320=>'直布羅陀',
            321=>'匈牙利',
            322=>'冰島',
            323=>'列支敦士登',
            324=>'馬耳他',
            325=>'摩納哥',
            326=>'挪威',
            327=>'波蘭',
            328=>'羅馬尼亞',
            329=>'聖馬力諾',
            330=>'瑞典',
            331=>'瑞士',
            334=>'愛沙尼亞',
            335=>'拉脫維亞',
            336=>'立陶宛',
            337=>'格魯吉亞',
            338=>'亞美尼亞',
            339=>'阿塞拜疆',
            340=>'白俄羅斯',
            343=>'摩爾多瓦',
            344=>'俄羅斯聯邦',
            347=>'烏克蘭',
            349=>'塞爾維亞和黑山',
            350=>'斯洛文尼亞',
            351=>'克羅地亞',
            352=>'捷克',
            353=>'斯洛伐克',
            354=>'前南馬其頓',
            355=>'波黑',
            356=>'梵蒂岡城國',
            357=>'法羅群島',
            358=>'塞爾維亞',
            359=>'黑山',
            399=>'歐洲其他國家(地區)',
            401=>'安提瓜和巴布達',
            402=>'阿根廷',
            403=>'阿魯巴',
            404=>'巴哈馬',
            405=>'巴巴多斯',
            406=>'伯利茲',
            408=>'多民族玻利維亞國',
            409=>'博內爾',
            410=>'巴西',
            411=>'開曼群島',
            412=>'智利',
            413=>'哥倫比亞',
            414=>'多米尼克',
            415=>'哥斯達黎加',
            416=>'古巴',
            417=>'庫臘索島',
            418=>'多米尼加共和國',
            419=>'厄瓜多爾',
            420=>'法屬圭亞那',
            421=>'格林納達',
            422=>'瓜德羅普',
            423=>'危地馬拉',
            424=>'圭亞那',
            425=>'海地',
            426=>'洪都拉斯',
            427=>'牙買加',
            428=>'馬提尼克',
            429=>'墨西哥',
            430=>'蒙特塞拉特',
            431=>'尼加拉瓜',
            432=>'巴拿馬',
            433=>'巴拉圭',
            434=>'秘魯',
            435=>'波多黎各',
            436=>'薩巴',
            437=>'聖盧西亞',
            438=>'聖馬丁島',
            439=>'聖文森特和格林納丁斯',
            440=>'薩爾瓦多',
            441=>'蘇裏南',
            442=>'特立尼達和多巴哥',
            443=>'特克斯和凱科斯群島',
            444=>'烏拉圭',
            445=>'委內瑞拉',
            446=>'英屬維爾京群島',
            447=>'聖其茨和尼維斯',
            448=>'聖皮埃爾和密克隆',
            449=>'荷屬安地列斯',
            499=>'拉丁美洲其他國家(地區)',
            501=>'加拿大',
            502=>'美國',
            503=>'格陵蘭',
            504=>'百慕大',
            599=>'北美洲其他國家(地區)',
            601=>'澳大利亞',
            602=>'庫克群島',
            603=>'斐濟',
            604=>'蓋比群島',
            605=>'馬克薩斯群島',
            606=>'瑙魯',
            607=>'新喀裏多尼亞',
            608=>'瓦努阿圖',
            609=>'新西蘭',
            610=>'諾福克島',
            611=>'巴布亞新幾內亞',
            612=>'社會群島',
            613=>'所羅門群島',
            614=>'湯加',
            615=>'土阿莫土群島',
            616=>'土布艾群島',
            617=>'薩摩亞',
            618=>'基裏巴斯',
            619=>'圖瓦盧',
            620=>'密克羅尼西亞聯邦',
            621=>'馬紹爾群島',
            622=>'帕勞',
            623=>'法屬波利尼西亞',
            625=>'瓦利斯和浮圖納',
            699=>'大洋洲其他國家(地區)',
            701=>'國(地)別不詳',
            702=>'聯合國及機構和國際組織',
            999=>'中性包裝原產國別'
        );
         
        return $list_tw;
    }


    private function countListCn() {
        $list_cn = array(
        101=>'阿富汗',
        102=>'巴林',
        103=>'孟加拉国',
        104=>'不丹',
        105=>'文莱',
        106=>'缅甸',
        107=>'柬埔寨',
        108=>'塞浦路斯',
        109=>'朝鲜',
        110=>'香港',
        111=>'印度',
        112=>'印度尼西亚',
        113=>'伊朗',
        114=>'伊拉克',
        115=>'以色列',
        116=>'日本',
        117=>'约旦',
        118=>'科威特',
        119=>'老挝',
        120=>'黎巴嫩',
        121=>'澳门',
        122=>'马来西亚',
        123=>'马尔代夫',
        124=>'蒙古',
        125=>'尼泊尔联邦民主共和国',
        126=>'阿曼',
        127=>'巴基斯坦',
        128=>'巴勒斯坦',
        129=>'菲律宾',
        130=>'卡塔尔',
        131=>'沙特阿拉伯',
        132=>'新加坡',
        133=>'韩国',
        134=>'斯里兰卡',
        135=>'叙利亚',
        136=>'泰国',
        137=>'土耳其',
        138=>'阿联酋',
        139=>'也门',
        141=>'越南',
        142=>'中国',
        143=>'台澎金马关税区',
        144=>'东帝汶',
        145=>'哈萨克斯坦',
        146=>'吉尔吉斯斯坦',
        147=>'塔吉克斯坦',
        148=>'土库曼斯坦',
        149=>'乌兹别克斯坦',
        199=>'亚洲其他国家(地区)',
        201=>'阿尔及利亚',
        202=>'安哥拉',
        203=>'贝宁',
        204=>'博茨瓦那',
        205=>'布隆迪',
        206=>'喀麦隆',
        207=>'加那利群岛',
        208=>'佛得角',
        209=>'中非',
        210=>'塞卜泰(休达)',
        211=>'乍得',
        212=>'科摩罗',
        213=>'刚果(布)',
        214=>'吉布提',
        215=>'埃及',
        216=>'赤道几内亚',
        217=>'埃塞俄比亚',
        218=>'加蓬',
        219=>'冈比亚',
        220=>'加纳',
        221=>'几内亚',
        222=>'几内亚比绍',
        223=>'科特迪瓦',
        224=>'肯尼亚',
        225=>'利比里亚',
        226=>'利比亚',
        227=>'马达加斯加',
        228=>'马拉维',
        229=>'马里',
        230=>'毛里塔尼亚',
        231=>'毛里求斯',
        232=>'摩洛哥',
        233=>'莫桑比克',
        234=>'纳米比亚',
        235=>'尼日尔',
        236=>'尼日利亚',
        237=>'留尼汪',
        238=>'卢旺达',
        239=>'圣多美和普林西比',
        240=>'塞内加尔',
        241=>'塞舌尔',
        242=>'塞拉利昂',
        243=>'索马里',
        244=>'南非',
        245=>'西撒哈拉',
        246=>'苏丹',
        247=>'坦桑尼亚',
        248=>'多哥',
        249=>'突尼斯',
        250=>'乌干达',
        251=>'布基纳法索',
        252=>'刚果(金)',
        253=>'赞比亚',
        254=>'津巴布韦',
        255=>'莱索托',
        256=>'梅利利亚',
        257=>'斯威士兰',
        258=>'厄立特里亚',
        259=>'马约特',
        260=>'南苏丹共和国',
        299=>'非洲其他国家(地区)',
        301=>'比利时',
        302=>'丹麦',
        303=>'英国',
        304=>'德国',
        305=>'法国',
        306=>'爱尔兰',
        307=>'意大利',
        308=>'卢森堡',
        309=>'荷兰',
        310=>'希腊',
        311=>'葡萄牙',
        312=>'西班牙',
        313=>'阿尔巴尼亚',
        314=>'安道尔',
        315=>'奥地利',
        316=>'保加利亚',
        318=>'芬兰',
        320=>'直布罗陀',
        321=>'匈牙利',
        322=>'冰岛',
        323=>'列支敦士登',
        324=>'马耳他',
        325=>'摩纳哥',
        326=>'挪威',
        327=>'波兰',
        328=>'罗马尼亚',
        329=>'圣马力诺',
        330=>'瑞典',
        331=>'瑞士',
        334=>'爱沙尼亚',
        335=>'拉脱维亚',
        336=>'立陶宛',
        337=>'格鲁吉亚',
        338=>'亚美尼亚',
        339=>'阿塞拜疆',
        340=>'白俄罗斯',
        343=>'摩尔多瓦',
        344=>'俄罗斯联邦',
        347=>'乌克兰',
        349=>'塞尔维亚和黑山',
        350=>'斯洛文尼亚',
        351=>'克罗地亚',
        352=>'捷克',
        353=>'斯洛伐克',
        354=>'前南马其顿',
        355=>'波黑',
        356=>'梵蒂冈城国',
        357=>'法罗群岛',
        358=>'塞尔维亚',
        359=>'黑山',
        399=>'欧洲其他国家(地区)',
        401=>'安提瓜和巴布达',
        402=>'阿根廷',
        403=>'阿鲁巴',
        404=>'巴哈马',
        405=>'巴巴多斯',
        406=>'伯利兹',
        408=>'多民族玻利维亚国',
        409=>'博内尔',
        410=>'巴西',
        411=>'开曼群岛',
        412=>'智利',
        413=>'哥伦比亚',
        414=>'多米尼克',
        415=>'哥斯达黎加',
        416=>'古巴',
        417=>'库腊索岛',
        418=>'多米尼加共和国',
        419=>'厄瓜多尔',
        420=>'法属圭亚那',
        421=>'格林纳达',
        422=>'瓜德罗普',
        423=>'危地马拉',
        424=>'圭亚那',
        425=>'海地',
        426=>'洪都拉斯',
        427=>'牙买加',
        428=>'马提尼克',
        429=>'墨西哥',
        430=>'蒙特塞拉特',
        431=>'尼加拉瓜',
        432=>'巴拿马',
        433=>'巴拉圭',
        434=>'秘鲁',
        435=>'波多黎各',
        436=>'萨巴',
        437=>'圣卢西亚',
        438=>'圣马丁岛',
        439=>'圣文森特和格林纳丁斯',
        440=>'萨尔瓦多',
        441=>'苏里南',
        442=>'特立尼达和多巴哥',
        443=>'特克斯和凯科斯群岛',
        444=>'乌拉圭',
        445=>'委内瑞拉',
        446=>'英属维尔京群岛',
        447=>'圣其茨和尼维斯',
        448=>'圣皮埃尔和密克隆',
        449=>'荷属安地列斯',
        499=>'拉丁美洲其他国家(地区)',
        501=>'加拿大',
        502=>'美国',
        503=>'格陵兰',
        504=>'百慕大',
        599=>'北美洲其他国家(地区)',
        601=>'澳大利亚',
        602=>'库克群岛',
        603=>'斐济',
        604=>'盖比群岛',
        605=>'马克萨斯群岛',
        606=>'瑙鲁',
        607=>'新喀里多尼亚',
        608=>'瓦努阿图',
        609=>'新西兰',
        610=>'诺福克岛',
        611=>'巴布亚新几内亚',
        612=>'社会群岛',
        613=>'所罗门群岛',
        614=>'汤加',
        615=>'土阿莫土群岛',
        616=>'土布艾群岛',
        617=>'萨摩亚',
        618=>'基里巴斯',
        619=>'图瓦卢',
        620=>'密克罗尼西亚联邦',
        621=>'马绍尔群岛',
        622=>'帕劳',
        623=>'法属波利尼西亚',
        625=>'瓦利斯和浮图纳',
        699=>'大洋洲其他国家(地区)',
        701=>'国(地)别不详',
        702=>'联合国及机构和国际组织',
        999=>'中性包装原产国别'
    );
    return $list_cn;
    }
}
