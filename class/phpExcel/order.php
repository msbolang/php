<?php

/**
 * description...
 * @package AdminController
 */
set_time_limit(0);

class OrderController extends Controller {

 

    public function export_logistics() {
        $model = new Model("order");
        $selecteOrderNo = Req::args("selecte");
        $status = Filter::int(Req::args("status"));
        $order_no = Filter::sql(Req::args("order_no"));
        $start_time = Filter::sql(Req::args("start_time"));
        $end_time = Filter::sql(Req::args("end_time"));
        $orders = $this->listOrderByStatus($status, $order_no, $start_time, $end_time, $selecteOrderNo);
        $areas = $model->table("area")->findAll();
        $parse_area = array();
        foreach ($areas as $area) {
            $parse_area[$area['id']] = $area['name'];
        }
        $expresses = $model->table("express_company")->findAll();
        $parse_express = array();
        foreach ($expresses as $express) {
            $parse_express[$express['id']] = $express['name'];
        }
        $this->expLogistics($orders, $parse_area);
    }

    private function expLogistics($orders, $parse_area) {
        $dir = $_SERVER['DOCUMENT_ROOT'];
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel.php";
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel/Worksheet/Drawing.php";
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $objPHPExcel = $this->setExpExcel($objPHPExcel, '物流');  // header
        $plus = 5;

      

//        $orders = []; //test 導出表頭
        for ($i = 0; $i < count($orders); $i++) {
            $model = new Model("order_goods");
            $join = " left join goods as g on g.id=og.goods_id";
            $join .= " left join products as p on g.id=og.product_id";
            $join .= " left join brand as b on b.id=g.brand_id";
            $products = $model->table("order_goods as og")
                            ->fields("g.img,g.country_id,g.weight,g.unit,g.bar_code,g.name as good_name, g.hwname as good_hwname,g.goods_no as goods_no,b.name as brand_name,og.goods_nums,og.real_price,og.spec")
                            ->where("og.order_id=" . $orders[$i]['id'])->join($join)->findAll();
            $product_num = count($products);
            
//            if($product_num>1){
//                echo '<pre>';print_r($products);exit;
//            }
            
            for ($j = 0; $j < count($products); $j++) {
                $specs = unserialize($products[$j]['spec']);
                $spec = '';
                if (is_array($specs)) {
                    foreach ($specs as $row) {
                        $spec .= $row['name'] . ':' . $row['value'][1] . ';';
                    }
                }
                $plus++;

                //值全填滿，所以不加判斷了
                if ($j == 0) {
//                    if ($product_num > 1) {
//                        $objPHPExcel->getActiveSheet()->mergeCells('A' . $plus . ':' . 'A' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('B' . $plus . ':' . 'B' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('K' . $plus . ':' . 'K' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('L' . $plus . ':' . 'L' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('M' . $plus . ':' . 'M' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('N' . $plus . ':' . 'N' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('O' . $plus . ':' . 'O' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('P' . $plus . ':' . 'P' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('Q' . $plus . ':' . 'Q' . ($plus + $product_num - 1));
//                        $objPHPExcel->getActiveSheet()->mergeCells('R' . $plus . ':' . 'R' . ($plus + $product_num - 1));
//                    }

                    $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $plus, $i + 1);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('B' . $plus, $orders[$i]['order_no'] . $orders[$i]['replacecallback'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('C' . $plus, $orders[$i]['express_no'],PHPExcel_Cell_DataType::TYPE_STRING);

                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . $plus, $orders[$i]['pay_no'],PHPExcel_Cell_DataType::TYPE_STRING); //(支付单号需要开发)
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $plus, $orders[$i]['pay_time']); //(支付时间)20171109152934
     
                    $userInfo = $this->getUserInfoForXML($orders[$i]['user_id']);
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $plus, $userInfo['real_name']); //订购人
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('G' . $plus, $userInfo['identity_card'],PHPExcel_Cell_DataType::TYPE_STRING); //订购人证件号
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('H' . $plus, $userInfo['mobile'],PHPExcel_Cell_DataType::TYPE_STRING); //订购人电话
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $plus, $orders[$i]['accept_name']); //收货人姓名
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('J' . $plus, $orders[$i]['mobile'],PHPExcel_Cell_DataType::TYPE_STRING); //收货人电话
                    $objPHPExcel->getActiveSheet(0)->setCellValue('K' . $plus, $parse_area[$orders[$i]['province']] . $parse_area[$orders[$i]['city']] . $parse_area[$orders[$i]['county']] . $orders[$i]['addr']); //收货地址全
                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . $plus, '公路运输'); //运输方式  公路运输

                    $objPHPExcel->getActiveSheet(0)->setCellValue('M' . $plus, ''); //运输工具编号  空
                    $objPHPExcel->getActiveSheet(0)->setCellValue('N' . $plus, ''); //航班航次号   空
                    $objPHPExcel->getActiveSheet(0)->setCellValue('O' . $plus, ''); //提运单号   空

                    $objPHPExcel->getActiveSheet(0)->setCellValue('P' . $plus, '澳門'); //起运国（地区）
                
                    $objPHPExcel->getActiveSheet(0)->setCellValue('Q' . $plus,  $orders[$i]['real_freight'] > 0 ? $orders[$i]['real_freight']: 0); //运费
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue('R' . $plus, 0); //保费
                    $objPHPExcel->getActiveSheet(0)->setCellValue('S' . $plus, ''); //包装种类   空

                    $objPHPExcel->getActiveSheet(0)->setCellValue('T' . $plus, ''); //包裹毛重（千克）空起來 自己填
                     
                }
                
                    $objPHPExcel->getActiveSheet(0)->setCellValue('U' . $plus, $products[$j]['weight']*$products[$j]['goods_nums']); //包裹净重（千克）
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue('V' . $plus, $products[$j]['goods_no']); //企业商品货号SKU*
                    $objPHPExcel->getActiveSheet(0)->setCellValue('W' . $plus, $products[$j]['good_name']); //企业商品品名good_name
        
                    $objPHPExcel->getActiveSheet(0)->setCellValue('X' . $plus, $this->_getGoodsCountry($products[$j]['country_id'])); //原产国
                  
                    $objPHPExcel->getActiveSheet(0)->setCellValue('Y' . $plus, $products[$j]['goods_nums']); //数量
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue('Z' . $plus, $products[$j]['unit']); //成交单位
                    $objPHPExcel->getActiveSheet(0)->setCellValue('AA' . $plus, $products[$j]['real_price']); //单价
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue('AB' . $plus, number_format(($products[$j]['real_price']*$products[$j]['goods_nums']),2)); //总价
                    

                $objPHPExcel->getActiveSheet()->getRowDimension($plus)->setRowHeight(16);
//                $objPHPExcel->getActiveSheet()->getStyle('A' . $plus . ':R' . $plus)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//                $objPHPExcel->getActiveSheet()->getStyle('A' . $plus . ':R' . $plus)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        $this->setExpExcleFoot($objPHPExcel, '物流報文'); // footer
    }

    public function export_orders() {

        $model = new Model("order");

        $selecteOrderNo = Req::args("selecte");


//        $join = " left join order_refund as r on r.order_id=o.id";
//        $orders = $model->table("order as o")
//                ->where("o.status=3 AND o.pay_status=1 AND o.id in(select order_id from tiny_order_market where order_id=o.id and delivery_status=0)")
//                ->findAll();

        $status = Filter::int(Req::args("status"));
        $order_no = Filter::sql(Req::args("order_no"));
        $start_time = Filter::sql(Req::args("start_time"));
        $end_time = Filter::sql(Req::args("end_time"));
        $orders = $this->listOrderByStatus($status, $order_no, $start_time, $end_time, $selecteOrderNo);



        $merchant = Filter::int(Req::args("merchant"));
        if ($merchant) {
            foreach ($orders as $key => $value) {
                $isMYOrder = $this->getMyOrder($value['id']);
                if (!$isMYOrder) {
                    unset($orders[$key]);
                }
            }
            $orders = array_merge($orders);
        }


        $areas = $model->table("area")->findAll();
        $parse_area = array();
        foreach ($areas as $area) {
            $parse_area[$area['id']] = $area['name'];
        }
        $expresses = $model->table("express_company")->findAll();

        $parse_express = array();
        foreach ($expresses as $express) {
            $parse_express[$express['id']] = $express['name'];
        }

        $this->expOrder($orders, $parse_area);
    }

    private function expOrder($orders, $parse_area) {
        $dir = $_SERVER['DOCUMENT_ROOT'];
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel.php";
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel/Worksheet/Drawing.php";
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $objPHPExcel = $this->setExpExcel($objPHPExcel);  //header
        $plus = 3;
        for ($i = 0; $i < count($orders); $i++) {

            $model = new Model("order_goods");
            $join = " left join goods as g on g.id=og.goods_id";
            $join .= " left join products as p on g.id=og.product_id";
            $join .= " left join brand as b on b.id=g.brand_id";
            $products = $model->table("order_goods as og")
                            ->fields("g.img,g.bar_code,g.name as good_name, g.hwname as good_hwname,g.goods_no as goods_no,b.name as brand_name,og.goods_nums,og.real_price,og.spec")
                            ->where("og.order_id=" . $orders[$i]['id'])->join($join)->findAll();
            $product_num = count($products);

            for ($j = 0; $j < count($products); $j++) {
                $specs = unserialize($products[$j]['spec']);
                $spec = '';
                if (is_array($specs)) {
                    foreach ($specs as $row) {
                        $spec .= $row['name'] . ':' . $row['value'][1] . ';';
                    }
                }
                $plus++;

                //值全填滿，所以不加判斷了
                if ($j == 0) {
                    if ($product_num > 1) {
                        $objPHPExcel->getActiveSheet()->mergeCells('A' . $plus . ':' . 'A' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('B' . $plus . ':' . 'B' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('K' . $plus . ':' . 'K' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('L' . $plus . ':' . 'L' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('M' . $plus . ':' . 'M' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('N' . $plus . ':' . 'N' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('O' . $plus . ':' . 'O' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('P' . $plus . ':' . 'P' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('Q' . $plus . ':' . 'Q' . ($plus + $product_num - 1));
                        $objPHPExcel->getActiveSheet()->mergeCells('R' . $plus . ':' . 'R' . ($plus + $product_num - 1));
                    }

                    $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $plus, $orders[$i]['id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $plus, $orders[$i]['create_time']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('K' . $plus, $orders[$i]['order_no']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . $plus, $orders[$i]['user_remark']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('M' . $plus, $parse_area[$orders[$i]['province']] . $parse_area[$orders[$i]['city']] . $parse_area[$orders[$i]['county']] . $orders[$i]['addr']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('N' . $plus, $orders[$i]['mobile']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('O' . $plus, $orders[$i]['accept_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('P' . $plus, $parse_express[$orders[$i]['express_company_id']] . ' ' . $orders[$i]['express_no']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('Q' . $plus, $parse_express[$orders[$i]['express_company_id2']] . ' ' . $orders[$i]['express_no2']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('R' . $plus, $products[$j]['bar_code']);
                }
                $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $plus, '　' . $products[$j]['goods_no']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $plus, $products[$j]['brand_name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $plus, $products[$j]['good_name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $plus, $products[$j]['good_hwname']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $plus, $spec);
                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $plus, $products[$j]['goods_nums']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('J' . $plus, $products[$j]['real_price']);
                $img_path = $dir . '/' . $products[$j]['img'];

                if (file_exists($img_path)) {
                    $objPHPExcel->getActiveSheet()->getRowDimension($plus)->setRowHeight(42);
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    //  设置图片路径 切记：只能是本地图片
                    $objDrawing->setPath($img_path);
                    // 设置图片高度
                    $objDrawing->setHeight(50);
                    $objDrawing->setWidth(50);
                    $objDrawing->setOffsetX(3);
                    $objDrawing->setOffsetY(3);
                    $objDrawing->setRotation(5);
                    $objDrawing->getShadow()->setVisible(true);
                    $objDrawing->getShadow()->setDirection(50);
                    // 设置图片要插入的单元格
                    $objDrawing->setCoordinates('C' . $plus);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                } else {
                    $objPHPExcel->getActiveSheet()->getRowDimension($plus)->setRowHeight(16);
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . $plus . ':R' . $plus)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $plus . ':R' . $plus)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        $this->setExpExcleFoot($objPHPExcel, '訂單列表'); //footer
    }

    private function setExpExcleFoot($objPHPExcel, $expName) {
        $fileName = $expName == '訂單列表' ? 'order' : 'Logistics';
        // Rename sheet
        $filename = iconv("UTF-8", "GBK", $expName); //解決在IE浏览器中文件名亂碼
        $objPHPExcel->getActiveSheet()->setTitle("" . date("Y-m-d"), $fileName);
        $objPHPExcel->setActiveSheetIndex(0);
        // 输出
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="MO' . date("Y-m-d") . $fileName . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        echo '<pre>';print_r($objWriter);exit;
        $objWriter->save('php://output');
    }

    private function setExpExcel($objPHPExcel, $w = false) {

        // Set properties
        $objPHPExcel->getProperties()->setCreator("ctos")
                ->setLastModifiedBy("ctos")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        if ($w) {
//             $style_A2 = array(
//                        'width'=>'10',
//                        'font' => array(
//                            'bold' => true,
//                            'size'=>12,
//                            'color'=>array(
//                              'argb' => '00000000',
//                          ),
//                        ),
//                        'alignment' => array(
//                          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                        ),
//                    );
//                    // 将A1单元格设置为加粗，居中
//                    $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_A2);
//                    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
            //  合并
            $objPHPExcel->getActiveSheet()->mergeCells('A1:AB1'); //总署版清单报文

            $objPHPExcel->getActiveSheet()->mergeCells('A2:B2'); //电商平台代码
//            $objPHPExcel->getActiveSheet()->getColumnDimension('A2')->setWidth(150);
//            $objPHPExcel->getActiveSheet()->getColumnDimension('A3')->setWidth(150);

            $objPHPExcel->getActiveSheet()->mergeCells('A3:B3');

            // $objPHPExcel->getActiveSheet()->mergeCells('G2:L2');//物流平台名称
            // $objPHPExcel->getActiveSheet()->mergeCells('G3:L3');

            $objPHPExcel->getActiveSheet()->mergeCells('D2:E2'); //电商企业代码
            $objPHPExcel->getActiveSheet()->mergeCells('D3:E3');

            $objPHPExcel->getActiveSheet()->mergeCells('F2:I2');
            $objPHPExcel->getActiveSheet()->mergeCells('F3:I3');

            $objPHPExcel->getActiveSheet()->mergeCells('J2:K2');
            $objPHPExcel->getActiveSheet()->mergeCells('J3:K3');

            $objPHPExcel->getActiveSheet()->mergeCells('L2:N2');
            $objPHPExcel->getActiveSheet()->mergeCells('L3:N3');

            $objPHPExcel->getActiveSheet()->mergeCells('O2:P2');
            $objPHPExcel->getActiveSheet()->mergeCells('O3:P3');

            $objPHPExcel->getActiveSheet()->mergeCells('Q2:S2');
            $objPHPExcel->getActiveSheet()->mergeCells('Q3:S3');

            $objPHPExcel->getActiveSheet()->mergeCells('T2:U2');
            $objPHPExcel->getActiveSheet()->mergeCells('T3:U3');

            $objPHPExcel->getActiveSheet()->mergeCells('V2:AB3');

            $objPHPExcel->getActiveSheet()->mergeCells('A4:U4');
            $objPHPExcel->getActiveSheet()->mergeCells('V4:AB4');


            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '总署版清单报文')
                    ->setCellValue('A2', '电商平台代码')
                    ->setCellValue('A3', '44---56')
                    ->setCellValue('C2', '电商平台名称')
                    ->setCellValue('C3', '珠海市---公司')
                    ->setCellValue('D2', '电商企业代码')
                    ->setCellValue('D3', '44---56')
                    ->setCellValue('F2', '电商企业名称')
                    ->setCellValue('F3', '珠海市玛嘉斯科技有限公司')
                    ->setCellValue('J2', '物流企业代码')
                    ->setCellValue('J3', '440-----6S')
                    ->setCellValue('L2', '物流企业名称')
                    ->setCellValue('L3', '珠海易跨境供应链服务有限公司')
                    ->setCellValue('O2', '担保企业代码')
                    ->setCellValue('O3', '44---56')
                    ->setCellValue('Q2', '支付企业代码')
                    ->setCellValue('Q3', '914-----6601T')
                    ->setCellValue('T2', '支付企业名称')
                    ->setCellValue('T3', '广州银联网络支付有限公司')
                    ->setCellValue('V2', '说明：1、重量单位为KG，毛重最多保留2位小数，净重最多保留3位小数；2、价格和运费、保费币制为人民币，最多保留2位小数；3、身份证号末尾的字母请用大写。')
                    ->setCellValue('A4', '表  头  部')
                    ->setCellValue('V4', '表  体  部')
                    ->setCellValue('A5', '序号')
                    ->setCellValue('B5', '订单编号')
                    ->setCellValue('C5', '物流运单编号')
                    ->setCellValue('D5', '支付单号')
                    ->setCellValue('E5', '支付时间')
                    ->setCellValue('F5', '订购人姓名')
                    ->setCellValue('G5', '订购人证件号码')
                    ->setCellValue('H5', '订购人电话')
                    ->setCellValue('I5', '收货人姓名')
                    ->setCellValue('J5', '收货人电话')
                    ->setCellValue('K5', '收件地址')
                    ->setCellValue('L5', '运输方式')
                    ->setCellValue('N5', '运输工具编号')
                    ->setCellValue('M5', '航班航次号')
                    ->setCellValue('O5', '提运单号')
                    ->setCellValue('P5', '起运国(地区)')
                    ->setCellValue('Q5', '运费')
                    ->setCellValue('R5', '保费')
                    ->setCellValue('S5', '包装种类')
                    ->setCellValue('T5', '包裹毛重(千克)')
                    ->setCellValue('U5', '包裹净重(千克)')
                    ->setCellValue('V5', '企业商品货号SKU*')
                    ->setCellValue('W5', '企业商品品名')
                    ->setCellValue('X5', '原产国')
                    ->setCellValue('Y5', '数量')
                    ->setCellValue('Z5', '成交单位')
                    ->setCellValue('AA5', '单价')
                    ->setCellValue('AB5', '总价');

            //              $objPHPExcel->getActiveSheet()->getColumnDimension('A2')->setWidth(120);
        } else {
            // 设置宽度自动
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(35);

            // 设置行高度
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

            // 字体和样式
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(11);
            $objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            // 设置水平居中
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A:R')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //  合并
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');

            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Macauo2o 订单信息(' . date('Y-m-d H:i') . ')')
                    ->setCellValue('A2', 'NO')
                    ->setCellValue('B2', '交易日期')
                    ->setCellValue('C2', 'IMG')
                    ->setCellValue('D2', '商品编号')
                    ->setCellValue('E2', '品牌')
                    ->setCellValue('F2', '商品名陈')
                    ->setCellValue('G2', '别名')
                    ->setCellValue('H2', '规格')
                    ->setCellValue('I2', '数量')
                    ->setCellValue('J2', '金额')
                    ->setCellValue('K2', '订单号')
                    ->setCellValue('L2', '备注')
                    ->setCellValue('M2', '地址')
                    ->setCellValue('N2', '电话')
                    ->setCellValue('O2', '姓名')
                    ->setCellValue('P2', '配送单号(国际)')
                    ->setCellValue('Q2', '配送单号(中转)')
                    ->setCellValue('R2', '条形码');
        }
        return $objPHPExcel;
    }

    private function listOrderByStatus($status, $orderNo = '', $startTime = '', $endTime = '', $selecteOrderNo = null) {

        $join = "left join user as u on u.id=od.user_id";
        $join .= " left join doc_invoice as i on od.id=i.order_id";
        if ($selecteOrderNo) {
            $selecteOrderNo = substr($selecteOrderNo, 0, strlen($selecteOrderNo) - 1);
            $where = "od.order_no in ($selecteOrderNo)";
        } else {
            $where = "od.order_no like '%$orderNo%'";
        }
        if ($status > 0) {
            switch ($status) {
                case 1:
                    $where .= " and od.status<3";
                    break;
                case 2:
                    $where .= " and od.pay_status=0";
                    break;
                case 3:
                    $where .= " and od.pay_status=1";
                    break;
                case 4://待发货
                    $where .= " and od.status=3 and od.pay_status=1 and od.id in(select order_id from tiny_order_market where order_id=od.id and delivery_status=0)";
                    break;
                case 5://已发货(國際)
                    $where .= " and (od.status=3 and i.express_no2='' and (od.id in(select order_id from tiny_order_market where order_id=od.id and delivery_status=1)))";
                    break;
                case 10://已发货(中轉)
                    $where .= " and (od.status=3 and i.express_no2!='' and (od.id in(select order_id from tiny_order_market where order_id=od.id and delivery_status=1)))";
                    break;
                case 6://已完成
                    $where .= " and (od.status=4 or (od.id in(select order_id from tiny_order_market where order_id=od.id and delivery_status=2)))";
                    break;
                case 7://已关闭
                    $where .= " and (od.status=5 or od.status=6)";
                    break;
                case 8://换货
                    $where .= " and (od.status=3 and (od.id in(select om.order_id from tiny_order_market as om join tiny_order_replace as rp on rp.market_id=om.market_id and rp.order_id=om.order_id and rp.status<4 where om.order_id=od.id and om.delivery_status=1 and om.stype=1)))";
                    break;
                case 9://退款退货
                    $where .= " and (od.status=3 and (od.id in(select om.order_id from tiny_order_market as om join tiny_order_refund as rf on rf.market_id=om.market_id and om.order_id=rf.order_id and rf.status<4 where om.order_id=od.id and om.delivery_status=1 and om.stype=2)))";
                    break;
            }
        }
        if (!empty($startTime) && !empty($endTime)) {
            $where .= " and (od.create_time>='$startTime' and od.create_time<='$endTime')";
        }

        $model = new Model("order as od");

        $order = $model->fields("od.*,i.express_no,i.express_no2,i.express_company_id,i.express_company_id2")->join($join)->where($where)->order('od.id asc')->findAll();
        return $order;
    }

    // 导出海关订单模板
    public function export_orders_cus() {

        $model = new Model("order");
        $join = " left join order_refund as r on r.order_id=o.id";
        $join .= "left join doc_receiving as dc on dc.order_id=o.id";
        $orders = $model->table("order as o")->join("left join doc_receiving as rec on rec.order_id=o.id")->where("o.status=3 AND o.pay_status=1 AND o.id in(select order_id from tiny_order_market where order_id=o.id and delivery_status=0)")->findAll();
        // $rec = $model->table("doc_receiving as rec")->where("rec.order_id=o.id")->find();
        $status = Filter::int(Req::args("status"));
        $order_no = Filter::sql(Req::args("order_no"));
        $start_time = Filter::sql(Req::args("start_time"));
        $end_time = Filter::sql(Req::args("end_time"));
        $orders = $this->listOrderByStatus($status, $order_no, $start_time, $end_time);
        $areas = $model->table("area")->findAll();

        $parse_area = array();
        foreach ($areas as $area) {
            $parse_area[$area['id']] = $area['name'];
        }
        $expresses = $model->table("express_company")->findAll();
        $parse_express = array();
        foreach ($expresses as $express) {
            $parse_express[$express['id']] = $express['name'];
        }


        $cy = $model->table("country as cy")->findAll();

        $dir = $_SERVER['DOCUMENT_ROOT'];
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel.php";
        //  require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel/Worksheet/Drawing.php";
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel/IOFactory.php";
        $templateName = '/ExcelTemplet/B3-c.xls';
        $todate = date("Y_m_d", time());
        error_reporting(E_ALL);


        //创建一个读Excel模版的对象
        $objReader = new PHPExcel_Reader_Excel2007();

        $objPHPExcel = $objReader->load($dir . $templateName);



        //获取当前活动的表
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('总表');
        $objActSheet->setCellValue('A1', '总署版清单报文-数据');
        //我现在就开始输出列头了
        //具体有多少列 看你的数据走  会涉及到计算
        //现在就开始填充数据了  （从数据库中）  $data
        $baseRow = 6; //数据从N-1行开始往下输出  这里是避免头信息被覆盖

        foreach ($orders as $r => $dataRow) {
            $row = $baseRow + $r;

            $invoice = $model->table("doc_invoice as inv")->fields('inv.express_no')->where('order_id=' . $dataRow['id'])->find();
            $user = $model->table("customer as cus")->fields('cus.real_name')->where('user_id=' . $dataRow['user_id'])->find();

            //$rec = $model->table("doc_receiving as rec ")->where('rec.order_id='.$dataRow['id'])->find();


            $model = new Model("order_goods");
            $join = " left join goods as g on g.id=og.goods_id";
            $join .= " left join products as p on g.id=og.product_id";
            $join .= " left join brand as b on b.id=g.brand_id";
            // $join .= "left join doc_receiving as re on re.order_id=og.order_id";
            $products = $model->table("order_goods as og")
                            ->fields("g.weight,g.goods_no,og.order_id,g.country_id,g.name as good_name, g.hwname as good_hwname,g.goods_no as goods_no,b.name as brand_name,og.goods_nums,og.real_price,og.spec")
                            ->where("og.order_id=" . $dataRow['id'])->join($join)->find();

            $re = $model->table("doc_receiving as re")->fields("re.payment_time")->where("re.order_id=" . $dataRow['id'])->find();

            //将数据填充到相对应的位置

            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $dataRow['order_no']); //学员编号
            if (!empty($invoice)) {
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $invoice['express_no']);
            }

            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $dataRow['order_no']);
            if (!empty($re)) {
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $re['payment_time']);
            }
            if (!empty($user)) {
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $user['real_name']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $dataRow['identity_card']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $dataRow['mobile']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $dataRow['addr']);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $dataRow['real_freight']);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, "0");
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $row, $products['goods_no']);
            //查找原产地国家
            foreach ($cy as $key => $value) {
                if ($key == $products['country_id']) {
                    $objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $value['country_name']);
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $products['weight']);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $products['good_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $products['goods_nums']);
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $products['real_price']);
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $dataRow['order_amount']);
        }


        //导出
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $todate . '.xls"'); //"'.$filename.'.xls"
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //在内存中准备一个excel2003文件
        $objWriter->save('php://output');
    }

    public function export_cancel_order() {

        $model = new Model("order");
        $join = " left join order_refund as r on r.order_id=o.id";
        $join .= "left join doc_receiving as dc on dc.order_id=o.id";
        $orders = $model->table("order as o")->join("left join doc_receiving as rec on rec.order_id=o.id")->where("o.status=3 AND o.pay_status=1 AND o.id in(select order_id from tiny_order_market where order_id=o.id and delivery_status=0)")->findAll();
        // $rec = $model->table("doc_receiving as rec")->where("rec.order_id=o.id")->find();
        $status = Filter::int(Req::args("status"));
        $order_no = Filter::sql(Req::args("order_no"));
        $start_time = Filter::sql(Req::args("start_time"));
        $end_time = Filter::sql(Req::args("end_time"));
        $orders = $this->listOrderByStatus($status, $order_no, $start_time, $end_time);
        $areas = $model->table("area")->findAll();

        $parse_area = array();
        foreach ($areas as $area) {
            $parse_area[$area['id']] = $area['name'];
        }
        $expresses = $model->table("express_company")->findAll();
        $parse_express = array();
        foreach ($expresses as $express) {
            $parse_express[$express['id']] = $express['name'];
        }


        $cy = $model->table("country as cy")->findAll();

        $dir = $_SERVER['DOCUMENT_ROOT'];
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel.php";
        //  require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel/Worksheet/Drawing.php";
        require_once $dir . "/j/application/libraries/PHPExcel/PHPExcel/IOFactory.php";
        $templateName = '/ExcelTemplet/return_of_goods.xls';
        $todate = date("Y_m_d", time());
        error_reporting(E_ALL);


        //创建一个读Excel模版的对象
        $objReader = new PHPExcel_Reader_Excel2007();

        if (!$objReader->canRead($dir . $templateName)) {
            $objReader = new PHPExcel_Reader_Excel5();
            if (!$objReader->canRead($dir . $templateName)) {
                return false;
            }
        }


        $objPHPExcel = $objReader->load($dir . $templateName);



        //获取当前活动的表
        $objActSheet = $objPHPExcel->getSheet(0);


        //具体有多少列 看你的数据走  会涉及到计算
        //现在就开始填充数据了  （从数据库中）  $data
        $baseRow = 3; //数据从N-1行开始往下输出  这里是避免头信息被覆盖

        foreach ($orders as $r => $dataRow) {
            $row = $baseRow + $r;

            $invoice = $model->table("doc_invoice as inv")->fields('inv.express_no')->where('order_id=' . $dataRow['id'])->find();
            // $refund = $model->table("doc_refund as dre")->where('order_no='.$dataRow['order_no'])->find();  //退货
            //$rec = $model->table("doc_receiving as rec ")->where('rec.order_id='.$dataRow['id'])->find();


            $model = new Model("order_goods");
            $join = " left join goods as g on g.id=og.goods_id";
            $join .= " left join products as p on g.id=og.product_id";
            $join .= " left join brand as b on b.id=g.brand_id";
            // $join .= "left join doc_receiving as re on re.order_id=og.order_id";
            $products = $model->table("order_goods as og")
                            ->fields("g.weight,g.goods_no,og.order_id,g.country_id,g.name as good_name, g.hwname as good_hwname,g.goods_no as goods_no,b.name as brand_name,og.goods_nums,og.real_price,og.spec")
                            ->where("og.order_id=" . $dataRow['id'])->join($join)->find();

            $re = $model->table("doc_receiving as re")->fields("re.payment_time")->where("re.order_id=" . $dataRow['id'])->find();

            //将数据填充到相对应的位置

            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $dataRow['order_no']); //订单编号
            if (!empty($invoice)) {
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $invoice['express_no']);
            }


            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, '身份');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $dataRow['identity_card']);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $dataRow['accept_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $dataRow['mobile']);
            // $objPHPExcel->getActiveSheet()->setCellValue('U'.$row,$refund['content']);
            //  $objPHPExcel->getActiveSheet()->setCellValue('I'.$row,$dataRow['addr']);
            //  $objPHPExcel->getActiveSheet()->setCellValue('O'.$row, $dataRow['real_freight']);
            //  $objPHPExcel->getActiveSheet()->setCellValue('P'.$row, "0");
            //  $objPHPExcel->getActiveSheet()->setCellValue('T'.$row, $products['goods_no']);
            //查找原产地国家
            //  foreach ($cy as $key => $value) {
            //     if ($key == $products['country_id']) {
            //          $objPHPExcel->getActiveSheet()->setCellValue('V'.$row, $value['country_name']);
            //        }
            //      }
            // $objPHPExcel->getActiveSheet()->setCellValue('S'.$row, $products['weight']);
            // $objPHPExcel->getActiveSheet()->setCellValue('U'.$row, $products['good_name']);
            // $objPHPExcel->getActiveSheet()->setCellValue('W'.$row, $products['goods_nums']);    
            // $objPHPExcel->getActiveSheet()->setCellValue('Y'.$row, $products['real_price']);
            // $objPHPExcel->getActiveSheet()->setCellValue('Z'.$row, $dataRow['order_amount']);
        }

        $objActSheet = $objPHPExcel->getSheet(1);  //获取第二个工作表
        foreach ($orders as $r => $dataRow) {
            $row = $baseRow + $r;

            $invoice = $model->table("doc_invoice as inv")->fields('inv.express_no')->where('order_id=' . $dataRow['id'])->find();
            //   $refund = $model->table("doc_refund as dre")->where('order_no='.$dataRow['order_no'])->find();  //退货
            //   $refund = $model->table("goods as gd")->where('id='.$dataRow['order_no'])->find();  //退货

            $model = new Model("order_goods");
            $join = " left join goods as g on g.id=og.goods_id";
            $join .= " left join products as p on g.id=og.product_id";
            $join .= " left join brand as b on b.id=g.brand_id";
            $products = $model->table("order_goods as og")
                            ->fields("g.weight,g.goods_no,og.order_id,g.country_id,g.name as good_name, g.hwname as good_hwname,g.goods_no as goods_no,b.name as brand_name,og.goods_nums,og.real_price,og.spec")
                            ->where("og.order_id=" . $dataRow['id'])->join($join)->find();
            var_dump('<pre>');
            var_dump($products);
            exit;
            // $re =  $model->table("doc_receiving as re")->fields("re.payment_time")->where("re.order_id=".$dataRow['id'])->find();
            //将数据填充到相对应的位置

            if (!empty($invoice)) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $invoice['express_no']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $dataRow['goods_id']);

            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $dataRow['goods_nums']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $dataRow['goods_weight']);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $dataRow['accept_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $dataRow['mobile']);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $refund['content']);
        }



        //导出
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . '撤销申请订单数据报文' . '.xls"'); //"'.$filename.'.xls"
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //在内存中准备一个excel2003文件
        $objWriter->save('php://output');
    }

    //接收訂單ID 查詢訂單裡面我的商品 價格
    private function getMyOrderAmount($id) {
        $model = new Model("order_goods");
        $order = $model->where("order_id=" . $id)->findAll();
        $amount = 0;
        if (!empty($order)) {
            foreach ($order as $key => $value) {
                $sell_price = $this->getIsMyGood($value['goods_id']);
                if ($sell_price) {
                    $amount+=$value['real_price'] * $value['goods_nums']; //真實價格
                }
            }
        }
        return number_format($amount, 2, ".", "");
//         return $amount;
    }

    private function getMyOrder($orderId) {

        if ($orderId) {
            $model = new Model("order_goods");
            $orderGood = $model->where('order_id=' . $orderId)->findAll();
            if (!empty($orderGood)) {
                foreach ($orderGood as $key => $value) {
                    if ($this->getIsMyGood($value['goods_id'])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function getIsMyGood($goodid) {
        $model = new Model("goods");
        $good = $model->where('id=' . $goodid)->find();

        if ($good['shopuser'] == $this->merchant['id']) {
            return $good['sell_price'];
        }
    }

    public function order_mops() {
        $id = Filter::sql(Req::args('id'));

        $model = new Model("order");
        $pay_time = date('Y-m-d H:i:s', time());

        $admin_remark = $model->where("order_no ='$id' ")->find();

        $remark = $admin_remark['admin_remark'] . "  ($pay_time-经管理员操作此订单已收现金)";

        $orders = $model->where("order_no ='$id' ")->data(array('pay_status' => 1, 'pay_time' => $pay_time, 'admin_remark' => $remark))->update();

        echo $orders;
        //  $model->table("voucher")->where("id=" . $order['voucher_id'])->data(array('status' => 0))->update();
    }

    // 2017.10.13  货物准备完成  发送邮件到澳门街通知
    public function order_ready() {

        $id = Req::args("id");
        $order_no = Req::args("order_no");
        $rowdata = Req::args("rowdata");

        //echo JSON::encode($order_no);
        // exit;
        $test_email = '381101509@qq.com';
        if (Validator::email($test_email)) {
            $mail = new Mail();
            //var_dump($mail);exit();
            try {
                $flag = $mail->send_email($test_email, '邮箱验证测试', "<div><h3>尊敬的澳门街商城:</h3><p>ID：" . $id . "</p><p style='text-indent:2em;'>订单号:" . $order_no . "已经备货完成</p></div>");
                $info = array('status' => 'success', 'msg' => '备货通知，邮件已成功发送到平台。');
                if (!$flag) {
                    $info = array('status' => 'fail', 'msg' => '邮件发送失败，请检测配制信息');
                }
                echo JSON::encode($info);
            } catch (Exception $e) {
                $msg = $e->errorMessage();
                $info = array('status' => 'fail', 'msg' => $msg);
                echo JSON::encode($info);
            }
        } else {
            $info = array('status' => 'fail', 'msg' => '发送邮箱地址格式不正确，核实后再测试');
            echo JSON::encode($info);
        }
    }

    private function getMYorderCount($arr) {
        $count = 0;
        if (isset($arr['id'])) {
            $id = $arr['id'];
            $model = new Model("goods");
            $myGoods = $model->where('shopuser=' . $id)->findAll();
            if (!empty($myGoods)) {
                $str = '';
                foreach ($myGoods as $key => $value) {
                    $str .= $value['id'] . ',';
                }
                $str = substr($str, 0, strlen($str) - 1);
                $count = $this->_getMyOrderCounts($str);
            }
        }
        return $count;
    }

    private function _getMyOrderCounts($str) {
        $count = 0;
        $model = new Model("order_goods");
        $orderGood = $model->where('goods_id in (' . $str . ') ')->group("order_id")->findAll();
//            echo '<pre>';var_dump($orderGood);exit;
        if (!empty($orderGood)) {
            $count = $this->_getMyOrderCountEnd($orderGood);
        }
        return $count;
    }

    private function _getMyOrderCountEnd($arr) {

        $str = '';
        foreach ($arr as $key => $value) {
            $str .= $value['order_id'] . ',';
        }
        $str = substr($str, 0, strlen($str) - 1);
//        echo $str;exit;
        if ($str) {
//             echo $str;exit;
            $model = new Model("order");
            $orderCount = $model->where('id in (' . $str . ') and is_del=0')->count();
            return $orderCount;
        }
        return 0;
    }

    public function get_xml() {
        $id = Filter::sql(Req::args('id'));
        $xml = new HendlerXML();
        $orderArr = $this->getOrderToXML($id);
        $xml->getXml($id, $orderArr);
    }

    private function getOrderToXML($id) {
        $model = new Model("order");
        $order = $model->where("id=$id")->find();
        if (!empty($order)) {
            $newOrder = $this->assembleOrder($order);
            return $newOrder;
        }
        echo 'ERROR:沒有此訂單！';
        exit;
    }

    private function assembleOrder($order) {
        $arr = array();
        $orderGoods = $this->getXmlOrderGoods($order['id']);



        foreach ($orderGoods as $key => $value) {
            $orderGoods[$key]['country'] = $this->getGoodsCountry($value['goods_id']);
        }



        $arr['orderGoods'] = $orderGoods;
        $arr['guid'] = $this->_rundStr(14) . $order['order_no']; //隨機生成32位字符串
        $arr['orderNo'] = $order['order_no'] . $order['replacecallback'];
        $arr['goodsValue'] = $order['payable_amount']; //原訂單金額
        $arr['goodsValue'] = $order['real_amount']; //實際應付金額
        $arr['freight'] = $order['real_freight'] > 0 ? $order['real_freight'] : 0;
        $arr['taxTotal'] = $order['customs_taxes'] > 0 ? $order['customs_taxes'] : 0;
        $arr['acturalPaid'] = $order['order_amount']; //支付憑證支付金額一致 實際
        $userInfo = $this->getUserInfoForXML($order['user_id']);
        $arr['buyerRegNo'] = $userInfo['mobile']; // 订购人的交易平台注册号。 用戶名 手機號
        $arr['buyerName'] = $userInfo['real_name']; //订购人的真实姓名。
        $arr['buyerIdNumber'] = $userInfo['identity_card']; //订购人的身份证件号码。
        $arr['consignee'] = $order['accept_name']; // 收货人姓名，必须与电子运单的收货人姓名一致。
        $arr['consigneeTelephone'] = $order['mobile']; // 收货人联系电话，必须与电子运单的收货人电话一致。
        $arr['consigneeAddress'] = $this->cityForXML($order['province'], $order['city'], $order['county'], $order['addr']); // 收货地址，必须与电子运单的收货地址一致。
        return $arr;
    }

    private function getGoodsCountry($goodId) {
        $model = new Model("goods");
        $good = $model->where("id=$goodId")->find();
        if (!empty($good)) {

            if (empty($good['country_id']))
                return '無';

            $cuntry = $this->_getGoodsCountry($good['country_id']);

            return $cuntry;
        }
    }

    private function _getGoodsCountry($country_id) {
        $model = new Model("country");
        $cuntry = $model->where("country_id=$country_id")->find();

        if (!empty($cuntry)) {
            return $cuntry['country_name'];
        }
        return FALSE;
    }

    private function getXmlOrderGoods($id) {
        $model = new Model("order_goods");
        $orderGoods = $model->where("order_id=$id")->findAll();
        if (!empty($orderGoods)) {
            return $orderGoods;
        }
        echo 'ERROR:訂單商品不存在';
        exit;
//        echo '<pre>';print_r($orderGoods);exit;
    }

    //收貨地址串聯
    private function cityForXML($province, $city, $county, $addr) {
        $str = '';
        $str .= $this->areaForXml($province);
        $str .= $this->areaForXml($city);
        $str .= $this->areaForXml($county);
        $str .= $addr;
        return $str;
    }

    private function areaForXml($id) {
        $model = new Model("area");
        $name = $model->fields('name')->where("id=$id")->find();
        if (!empty($name)) {
            return $name['name'];
        }
        echo 'ERROR:地址錯誤！';
        exit;
    }

    /**
     * 
     * @param type $uid
     * 獲取平台註冊用戶信息
     */
    private function getUserInfoForXML($uid) {
        $model = new Model("customer");
        $user = $model->where("user_id=$uid")->find();
        if (!empty($user)) {
            return $user;
        }
        echo 'ERROR:用戶信息錯誤！';
        exit;
    }

    private function _rundStr($length = 14) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

}
