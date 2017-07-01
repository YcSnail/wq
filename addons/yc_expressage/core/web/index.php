<?php

/**
 * 查询逻辑梳理
 *   传入快递单号 kid
 *      查询数据库中是否不存在这条数据 或者 物流状态 不为3(已签收)
 *          true  不存在或者 状态未签收.则进行 查询快递操作
 *          false  直接展示.数据库中的数据
 *
 */

# 查询快递
# 后台用户查询快递

global $_W,$_GPC;
$uniacid = $_W['uniacid'];
load()->func('tpl');
$op = empty($_GPC['op']) ? 'index' : $_GPC['op'];


if( checksubmit('check') ) {

    # 获取用户填写的 快递单号
    $kid = intval(trim($_GPC['kid'],''));
    if (empty( $kid ) ){
        message('快递单号不能为空', $this->createWebUrl('index','refresh' , 'error'));
    }

    $pre  = m('expressage');
    # 查询是否存在 该数据
    $res = $pre->checKid($kid);

    if (empty( $res ) || $res['state'] !=3){

        # 查询快递信息
        $rescod = $pre->getExpressage($kid);

        # 组成html页面显示数据
        $state = state($rescod['State']);
        $show = array(
            'kname' => $rescod['ShipperName'],
            'kid' =>$kid,
            'state' =>$state
        );

        $traces = json_decode($rescod['content'],true);

    }else if ($res['state']==3){

        $res['state']='已签收';
        $show = array(
            'kname' => $res['kname'],
            'kid' => $res['kid'],
            'state' =>$res['state']
        );

        #json 转数组
        $traces = json_decode($res['content'],true);
    }

}

# 显示快递名字
include $this->template('web/'.$op);