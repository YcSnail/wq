<?php
// +----------------------------------------------------------------------
// | WSHOTO [ 技术主导，服务至上，提供微信端解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2020 http://www.wshoto.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: yc <yc@yuanxu.top>
// +----------------------------------------------------------------------

####商品分类文件 category

global $_W,$_GPC;

load()->func('tpl');
#表名
$tbName = 'yc_shop_category';

$op = empty($_GPC['op']) ?'display':$_GPC['op'] ;

# 基本数组
$catData = array(
    'uniacid'=>$_W['uniacid'],
    'status'=>'1'
);
$parentid = $_GPC['parentid'];

if ($op == 'display'){

    if (empty($parentid)){
        $time = pdo_getall($tbName,$catData);
    }else{
        $time = pdo_getall($tbName,$catData);
    }


}

else if ($op == 'post'){

    # 编辑分类
    $id = intval($_GPC['id']);
    $catData['id']=$id;
    $item= pdo_get($tbName,$catData);
    #新增幻灯片
    if (checksubmit('submit')) {
        $catData['displayorder']  = intval($_GPC['displayorder']);
        # 若不存在父级id 则为添加 大分类
        $catData['parentid'] = empty($parentid) ? '' : $parentid ;
        $catData['name'] = $_GPC['name'];
        $catData['description'] = $_GPC['description'];
        $catData['thumb'] = $_GPC['thumb'];
        $catData['advurl'] = $_GPC['advurl'];
        $catData['enabled'] = $_GPC['enabled'];

        $res =  pdo_insert($tbName,$catData);

        if (!empty($res)){
            message('添加分类成功！', $this->createWebUrl('shop/category', array('op' => 'display')), 'success');
        }
        message('添加分类失败！', 'refresh' , 'error');
    }

}

else if ($op == 'delect'){
    # 若不存在父级id 则为删除大分类
    $catData['id'] = intval($_GPC['id']);
    pdo_update($tbName,array('status'=>'0'),$catData);
    message('删除分类成功！', $this->createWebUrl('shop/category', array('op' => 'display')), 'success');
}




include $this->template('shop/category');
