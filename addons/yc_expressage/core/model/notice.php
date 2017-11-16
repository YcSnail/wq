<?php
// +----------------------------------------------------------------------
// |  [ 我的梦想是星辰大海 ]
// +----------------------------------------------------------------------
// | Author: yc  yc@yuanxu.top
// +----------------------------------------------------------------------
// | Date: 17.7.23 Time: 15:54
// +----------------------------------------------------------------------



// 快递推送
class notice_Model {

    public function sendMessage($kidRes){
        global $_W;

        if (empty($_W['uniacid'])){

            if (empty($kidRes['uniacid'])){
                return false;
            }

            $_W['acid'] = $_W['uniacid'] = $kidRes['uniacid'];
        }

        if (empty($kidRes['openid'])){
            echo 'openid 不能为空';
            die();
        }

        //获取 模版ID
        $getTemplate = array(
            'uniacid'=>$_W['uniacid']
        );
        $templateId = pdo_getcolumn('yc_expressage_api',$getTemplate,'template_id');

        // 如若不存在,则自动获取
        if (empty($templateId)){
            $templateId = $this->getWxTemplateId();
        }
        $send = WeAccount::create($_W['acid']);

        $postdata = array(

            "first"=>array(
                "value"=>'您的 '.$kidRes['kname'].' 快递有最新的信息',
                "color"=>"#000000"
            ),
            "order_id"=>array(
                "value"=>"暂无订单号",
                "color"=>"#000000"
            ),
            "package_id"=>array(
                "value"=>$kidRes['kid'],
                "color"=>"#000000"
            ),
            "remark"=>array(
                "value"=>
                    '最新信息:  '.$kidRes['content']['AcceptStation']."\n\r".
                    '更新时间:  '.$kidRes['content']['AcceptTime'],
                "color"=>"#000000"
            ),

        );

        $url = "http://twq.yuanxu.top/app/index.php?i={$_W['uniacid']}&c=entry&do=Member&m=yc_expressage";

        $res =  $send->sendTplNotice($kidRes['openid'], $templateId, $postdata,$url);

        return $res;
    }

    /**
     * 获取 模版ID
     * @return string
     */
    public function getWxTemplateId(){
        global $_W;
        $templateId = '';

        $access_token = $this->getAccess_token();

        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$access_token}";

        load()->func('communication');
        $template = ihttp_request($url);

        $setRes =  $this->setTemplate();


        if ( empty($template['content'] )  ){
            # 设置模版
            echo '获取模版ID失败,请登录公众平台手动设置模版.订单包裹跟踪通知,行业IT科技 - 互联网|电子商务';
            die();

        }else{

            $content = json_decode($template['content'],true);
            $template_list = $content['template_list'] ;
            if (!empty($template_list)){
                foreach ($template_list as $value){

                    if ($value['title'] == '订单包裹跟踪通知'){
                        $templateId = $value['template_id'];
                        break;
                    }
                }

            }

        }

        if (!empty($templateId)){
            $this->setYc_expressage_api($templateId);
        }

        return $templateId;
    }


    public function setTemplate(){
        global $_W;
        $access_token = $this->getAccess_token();

//        $url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token={$access_token}";
//        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$access_token}";

        // 设置所属行业
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token={$access_token}";
        load()->func('communication');

        $arr = array(
            "industry_id1"=>"1",
            "industry_id2"=>"2",
        );

        //TM00665

//                    "template_id_short"=>"OPENTM202243318"

        $template = ihttp_post($url,$arr);
        dump($template);
        die();


    }

    /**
     * 获取 access_token
     * @return array|mixed|stdClass
     */
    public function getAccess_token(){
        global $_W;

        if (!empty($_W['account']['access_token'])){
            $access_token = $_W['account']['access_token'];
            return $access_token;
        }

        load()->model('account');
        $acc = WeAccount::create($_W['acid']);
        $access_token = $acc->getAccessToken();
        return $access_token;
    }


    /**
     * 更新 模版消息 数据
     * @param $templateId
     * @return bool
     */
    public function setYc_expressage_api($templateId){
        global $_W;

        if (empty($templateId)){
            return false;
        }

        $upDataRes = pdo_update('yc_expressage_api',array('template_id'=>$templateId),array('uniacid'=>$_W['uniacid']));

        return $upDataRes;
    }



}


