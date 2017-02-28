<?php

global $_W,$_GPC;
load()->func('tpl');
$uniacid = $_W['uniacid'];
$uid = $_W['uid'];
//查询数据库已存在的参数
$api = pdo_get($this->tableApi, array('uniacid' => $uniacid), array('EBusinessID', 'key'));
//修改api参数
if (checksubmit('submit')) {
    $EBusinessID = $_GPC['EBusinessID'];
    $key = $_GPC['key'];
    $data = array(
        'uid'=>$uid,
        'uniacid'=>$uniacid,
        'EBusinessID' => $EBusinessID,
        'key'=> $key
    );
    //插入数据  表明 数据
    //如果不存在 insert ,否则 updata
    if (empty( $api )){
        $result = pdo_insert($this->tableApi, $data);
    }else{
        $result = pdo_update($this->tableApi, $data, array('uniacid' => $uniacid));
    }
    //判断是否插入成功
    if (empty($result)) {
        message('修改失败', $this->createWebUrl('api', array() )  , 'error');
    }
    message('修改成功', $this->createWebUrl('api', array() )  , 'success');
}
include $this->template('web/api');