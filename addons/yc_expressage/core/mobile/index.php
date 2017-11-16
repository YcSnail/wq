<?php
// +----------------------------------------------------------------------
// |  [ 我的梦想是星辰大海 ]
// +----------------------------------------------------------------------
// | Author: yc  yc@yuanxu.top
// +----------------------------------------------------------------------
// | Date: 17.7.1 Time: 14:54
// +----------------------------------------------------------------------

global $_W,$_GPC;
$uniacid = $_W['uniacid'];
load()->func('tpl');
$op = empty($_GPC['op']) ? 'index' : $_GPC['op'];


if( checksubmit('check') || !empty($_GPC['member']) ) {

    # 获取用户填写的 快递单号
    $kid = intval(trim($_GPC['kid'],''));
    if (empty( $kid ) ){
        message('快递单号不能为空', 'refresh' , 'error');
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
        $goGit['Traces'] = json_decode($res['content'],true);
    }


}
# 显示快递名字
include $this->template($op);