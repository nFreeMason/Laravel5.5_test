<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;

class Logistics extends Controller
{

    public function testUse()
    {
        return 'testUse';
    }

//---------------------------------------------

    public function __construct()
    {
        //电商ID
        defined('EBusinessID') or define('EBusinessID', '1313307');
        //电商加密私钥，快递鸟提供，注意保管，不要泄漏
        defined('AppKey') or define('AppKey', '9f327b0d-a357-4dc8-ac8f-b427a514e5f1');
        //请求url
        defined('ReqURL') or define('ReqURL', 'https://api.kdniao.com/api/oorderservice');
//        defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');
//        defined('ReqURL') or define('ReqURL', 'http://testapi.kdniao.cc:8081/api/oorderservice');
    }

    public function test()
    {

        //调用查询物流轨迹
        //---------------------------------------------
        $logisticResult = $this->getOrderTracesByJson();
        echo $logisticResult;

    }

    public function track()
    {
        $requestData = "
            {
                '':'',
                'EBusinessID':'',
                '':'',
                '':'',
            }
        ";

        $datas = [
            'EBusinessID' => EBusinessID,
            'RequestData' => $requestData,
            'RequestType' => 1008,
            'DataType' => '2',
        ];

    }

    public function getPlacingOrders()
    {
        $requestData = "
            {
                \"ShipperCode\": \"YTO\",
                \"LogisticCode\": \"887987778061785779\"
            }
        ";

        $datas = array(
            'EBusinessID' => EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData),
            'DataType' => '2'
        );

        $datas['DataSign'] = $this->encrypt($requestData, AppKey);
        $result = $this->sendPost(ReqURL, $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }

    /**
     * Json方式 查询订单物流轨迹
     */
    public function getOrderTracesByJson()
    {
        // $requestData= "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'12345678'}";

        $requestData = '{
             "OrderCode": "12345678",     
             "ShipperCode": "YTO",          
             "PayType": 1,
             "LogisticCode":"D1234678",
             "MonthCode": "7553045845",
             "ExpType": 1,
             "Cost": 1.0,
             "OtherCost": 1.0,
             "Sender": {
             "Company": "LV",
             "Name": "Taylor",
             "Mobile": "15018442396",
             "ProvinceName": "上海",
             "CityName": "上海",
             "ExpAreaName": "青浦区",
             "Address": "明珠路"
             },
             "Receiver": {
             "Company": "GCCUI",
             "Name": "Yann",
             "Mobile": "15018442396",
             "ProvinceName": "北京",
             "CityName": "北京",
             "ExpAreaName": "朝阳区",
             "Address": "三里屯街道"
             },
             "Commodity": [
            {
             "GoodsName": "鞋子",
             "Goodsquantity": 1,
             "GoodsWeight": 1.0
             }
             ],
             "AddService": [
             {
             "Name": "COD",
             "Value": "1020"
             }
             ],
             "Weight": 1.0,
             "Quantity": 1,
             "Volume": 0.0,
             "Remark": "小心轻放"
            }
             ';
        $datas = array(
            'EBusinessID' => EBusinessID,
            'RequestType' => '1001',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData, AppKey);
        $result = $this->sendPost(ReqURL, $datas);

        //根据公司业务处理返回的信息......

        return $result;

    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    public function sendPost($url, $datas)
    {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port'] = 80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        $httpheader .= $post_data;

        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";

        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    public function encrypt($data, $appkey)
    {
        return urlencode(base64_encode(md5($data . $appkey)));
    }

}
