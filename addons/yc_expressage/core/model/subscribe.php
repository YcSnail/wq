<?php

/**
 *
 * 快递鸟订阅推送2.0接口
 *
 * @技术QQ群: 340378554
 * @see: http://kdniao.com/api-follow
 * @copyright: 深圳市快金数据技术服务有限公司
 *
 * ID和Key请到官网申请：http://kdniao.com/reg
 */

class subscribe_Model{

    public $tableApi = 'yc_expressage_api';
    public $tableUser = 'yc_expressage_user';

    /**
     * @param $data
     * @param bool $is_ajax
     */
    public function getSubscribe($data,$is_ajax = false){
        global $_W;

        if (empty($data['kid']) ){
            ajax_res(-1,'快递单号不能为空');
        }

        $api = pdo_get($this->tableApi, array('uniacid' => $_W['uniacid']), array('EBusinessID', 'key'));

        if (empty($api)){
            if ($is_ajax){
                ajax_res(-1,'快递api参数错误,请联系管理员');
            }
            message('快递api参数错误,请联系管理员','refresh', 'error');
        }

        $logisticResult = $this->orderTracesSubByJson($data['kid'],$api['EBusinessID'],$api['key']);
        $logisticResult = json_decode($logisticResult,true);

        if ($logisticResult['Success'] == true){
            // 修改数据库数据
            $subRes = $this->changeSubscribe($data);

            if (!$subRes){
                ajax_res(0,'订阅成功');
            }
            ajax_res(-1,'订阅失败');
        }

        ajax_res(-1,$logisticResult['Reason']);
    }

    /**
     *
     * 修改用户订阅信息
     *
     */
    public function changeSubscribe($data){
        global $_W;

        if (empty($data) || empty($data['id']) || empty($data['openid']) ){
            return false;
        }

        $pre = array(
            'uniacid'=>$_W['uniacid'],
            'kid'=>$data['kid'],
            'openid'=>$data['openid']
        );
        $date = array(
            'is_subscribe'=>'1',
        );

        $result =  pdo_update($this->tableUser,$date,$pre);

        return $result;
    }

    //-------------------------------------------------------------

    /**
     * Json方式  物流信息订阅
     */
    public function orderTracesSubByJson($kid,$EBusinessID,$key){
        global $_W;
//        $ReqURL = 'http://testapi.kdniao.cc:8081/api/dist';
        $ReqURL = 'http://api.kdniao.cc/api/dist'; //正式环境

        $kidRes = pdo_get($this->tableUser,array('uniacid'=>$_W['uniacid'],'kid'=>$kid),array('kcode','kid'));

        if (empty($kidRes)){
            ajax_res(-1,'快递信息不存在');
        }

        $requestData = array(
            'ShipperCode'=>$kidRes['kcode'],
            'LogisticCode'=>$kidRes['kid']
        );

        $requestData = json_encode($requestData);

        $datas = array(
            'EBusinessID' => $EBusinessID,
            'RequestType' => '1008',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData, $key);
        $result=$this->sendPost($ReqURL, $datas);

        //根据公司业务处理返回的信息......
        return $result;
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    public function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
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
            $gets.= fread($fd, 128);
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
    public function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }


}

