<?php

global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$uid = $_W['uid'];
load()->func('tpl');

$res = explode(',',$_GPC['code']);
$kid = trim(intval($res[1]));

# 获取用户填写的 快递单号
if (empty( $kid ) ){
    ajax_res(-1,'快递单号不能为空');
}

$pre  = m('expressage');
# 查询是否存在 该数据
$res = $pre->checKid($kid,true);

if (empty( $res ) || $res['state'] !=3){

    # 查询快递信息
    $rescod = $pre->getExpressage($kid,true);

    # 组成html页面显示数据
    $state = state($rescod['State']);
    $rescod['content'] = json_decode($rescod['content'],true);

    $show = array(
        'kname' => $rescod['ShipperName'],
        'kid' =>$kid,
        'state' =>$state,
        'Traces'=>$rescod['content']
    );
    ajax_res(1,$show);

}else if ($res['state']==3){

    $res['state']='已签收';

    $res['content'] = json_decode($res['content'],true);
    $show = array(
        'kname' => $res['kname'],
        'kid' => $res['kid'],
        'state' =>$res['state'],
        'Traces'=>$res['content']
    );

    ajax_res(1,$show);
}

ajax_res(-1,'查询失败');
