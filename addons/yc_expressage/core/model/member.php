<?php
// +----------------------------------------------------------------------
// |  [ 我的梦想是星辰大海 ]
// +----------------------------------------------------------------------
// | Author: yc  yc@yuanxu.top
// +----------------------------------------------------------------------
// | Date: 17.7.1 Time: 14:54
// +----------------------------------------------------------------------

class Member_Model {


    /**
     * 检查用户信息
     * @param string $openid
     */
    public function checkMember($openid = ''){
        global $_W, $_GPC;
        if (strexists($_SERVER['REQUEST_URI'], '/web/')) {
            return;
        }
        if (empty( $openid )) {
            $openid = m('user')->getOpenid();
        }
        if (empty( $openid )) {
            return;
        }

        $followed = m('user')->followed($openid);
        load()->model('mc');
        if ($followed) {
            $uid = mc_openid2uid($openid);
            $mc = mc_fetch($uid, array( 'realname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist' ));
        }

    }
}