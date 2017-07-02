<?php
// +----------------------------------------------------------------------
// |  [ 我的梦想是星辰大海 ]
// +----------------------------------------------------------------------
// | Author: yc  yc@yuanxu.top
// +----------------------------------------------------------------------
// | Date: 17.7.1 Time: 14:56
// +----------------------------------------------------------------------

class User_Model{

    private $sessionid;
    public function __construct()
    {
        global $_W;
        $cookieTime = time();
        $this->sessionid = "__cookie_yc_expressage_{$cookieTime}_{$_W['uniacid']}";
    }
    function getOpenid()
    {
        $userinfo = $this->getInfo(false, true);
        return $userinfo['openid'];
    }

    function getInfo($base64 = false, $debug = false)
    {
        global $_W, $_GPC;
        $userinfo = array();
        load()->model('mc');
        $userinfo = mc_oauth_userinfo();
        $need_openid = true;
        if ($_W['container'] != 'wechat') {
            if ($_GPC['do'] == 'order' && $_GPC['p'] == 'pay') {
                $need_openid = false;
            }
            if ($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge') {
                $need_openid = false;
            }
        }
        if (empty($userinfo['openid']) && $need_openid) {
            die("<!DOCTYPE html>
                            <html>
                                <head>
                                    <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                                    <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                                </head>
                                <body>
                                <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                                </body>
                            </html>");
        }
        if ($base64) {
            return urlencode(base64_encode(json_encode($userinfo)));
        }
        return $userinfo;
    }

    function followed($openid = '')
    {
        global $_W;
        $followed = !empty($openid);
        if ($followed) {
            $mf = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid and uniacid=:uniacid limit 1", array(":openid" => $openid, ':uniacid' => $_W['uniacid']));
            $followed = $mf['follow'] == 1;
        }
        return $followed;
    }


}