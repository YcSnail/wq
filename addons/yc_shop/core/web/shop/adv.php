<?php
// +----------------------------------------------------------------------
// | WSHOTO [ 技术主导，服务至上，提供微信端解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2020 http://www.wshoto.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: yc <yc@yuanxu.top>
// +----------------------------------------------------------------------
## 商城幻灯片管理
global $_W,$_GPC;

$do = $_GPC['do'];

load()->func('tpl');
#表名
$tbName = 'yc_shop_adv';

$op = empty($_GPC['op']) ?'display':$_GPC['op'] ;

# 基本数组
$advData = array(
    'uniacid'=>$_W['uniacid'],
);

# 默认展示已有幻灯片列表
if ($op== 'display'){
    #参数 $tablename, $params = array(), $fields = array(), $keyfield = '', $orderby = array(), $limit = array()
    $list  = pdo_getall($tbName,$advData,'','','displayorder DESC','10');

    if (empty($list)){
        message('幻灯片列表为空,请前去添加', $this->createWebUrl('adv', array('op'=>'add'))  , 'error');
    }

    if (!empty($_GPC['displayorder'])) {
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update($tbName, array('displayorder' => $displayorder), array('id' => $id));
        }
        message('分类排序更新成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
    }

}

else if ($op== 'post'){
    # 编辑幻灯片
    $id = intval($_GPC['id']);
    $advData['id']=$id;
    $item= pdo_get($tbName,$advData);

    #新增幻灯片
    if (checksubmit('submit')) {

        #拼接数据
        $advData['advname'] = trim($_GPC['advname']);
        $advData['link'] = trim($_GPC['link']);
        $advData['enabled'] = intval($_GPC['enabled']);
        $advData['displayorder'] = intval($_GPC['displayorder']);
        $advData['thumb'] = tomedia($_GPC['thumb']);

        #判断是否为修改提交
        if (empty($_GPC['id'])){
            pdo_insert($tbName,$advData);
            message('添加幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
        }else{
            pdo_update($tbName,$advData,array('id'=>$_GPC['id']));
            message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
        }

    }

}

else if ($op== 'delete'){
    # 删除幻灯片
    $id = intval($_GPC['id']);
    $advData['id']=$id;
    $res = pdo_get($tbName,$advData);
    if (empty($res)){
        message('您要删除的幻灯片不存在！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
    }else{
        pdo_delete($tbName,$advData);
        message('您要删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
    }
}


include $this->template('shop/adv');