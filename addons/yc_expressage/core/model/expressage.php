<?php

/*
----------------------------------
*|  auther:  yc  yc@yuanxu.top
*|  website: yuanxu.top
---------------------------------------
*/

class expressage_Model{

    public $tableApi = 'yc_expressage_api';
    public $tableUser = 'yc_expressage_user';

    /**
     * 检查数据库中是否已存在 该快递信息
     * @param $kid
     * @param string $openid
     * @return bool
     */
    public function checKid($kid,$openid = 'admin'){
        global $_W;

        if ( empty( intval(trim($kid,'')) ) ){

            if (is_wechat()){
                ajax_res(-1,'请输入正确的快递单号');
            }

            message('请输入正确的快递单号','','error');
        }

        $data = array(
            'uniacid'=>$_W['uniacid'],
            'openid'=>$openid,
            'kid'=>$kid
        );
        $res = pdo_get($this->tableUser,$data);

        if (empty($res)){
            return false;
        }

        return $res;
    }


    public function getExpressage($kid,$openid = 'admin'){
        global $_W;

        $api = pdo_get($this->tableApi, array('uniacid' => $_W['uniacid']), array('EBusinessID', 'key'));

        if (empty($api)){
            if (is_wechat()){
                ajax_res(-1,'快递api参数错误,请联系管理员');
            }
            message('快递api参数错误,请联系管理员','refresh', 'error');
        }

        $check = m('check');

        $result = $check->goGit($kid,$api['EBusinessID'],$api['key']);

        $Code = $result['Shippers'][0];
        if (empty($Code['ShipperCode'])){
            if (is_wechat()){
                ajax_res(-1,'暂不支持该快递公司.请换一个订单再试');
            }
            message('暂不支持该快递公司.请换一个订单再试', 'refresh' , 'error');
        }

        # 查询快递信息
        $goGit = m('api');
        $rescod = $goGit->goGit($Code['ShipperCode'],$kid,$api['EBusinessID'],$api['key']);

        if (empty($rescod['Success']) ){
            if (is_wechat()){
                ajax_res(-1,'快递单号输入有误');
            }
            message('快递单号输入有误','refresh' , 'error');
        }

        # 数据库倒序
        $res['Traces'] = array_reverse($rescod['Traces']);
        # 把数据转为json格式 存储到数据库
        $content = json_encode($res['Traces']);

        # 把第一次查询的数据 存入数据库
        $date['kname'] = $Code['ShipperName'];
        $date['kcode'] = $Code['ShipperCode'];
        $date['state'] = $rescod['State'];
        $date['content'] = $content;
        $date['createtime'] = time();

        $checKid = $this->checKid($kid);
        if (empty($checKid)){
            $result =  pdo_insert($this->tableUser,$date);
        }else{
            $pre = array(
                'uniacid'=>$_W['uniacid'],
                'kid'=>$kid,
                'openid'=>$openid
            );

            $result =  pdo_update($this->tableUser,$date,$pre);
        }

        if (empty( $result )){
            if (is_wechat()){
                ajax_res(-1,'添加数据失败,请联系管理员');
            }
            message('添加数据失败,请联系管理员', 'refresh' , 'error');
        }

        $ret = array(
            'State'=>$rescod['State'],
            'ShipperName'=>$Code['ShipperName'],
            'content'=>$content
        );

        return $ret;

    }





}
