<?php

global $_W,$_GPC;
load()->func('tpl');
#检测是否是微信登陆
checkMobile();
$tableFans = 'mc_mapping_fans';
$tableMc = 'mc_members';
$tableUser = 'yc_expressage_user';

if (empty($_W['openid'])){
    ajaxRes(-1,'用户不存在');
}

$check = array(
    'openid' => $_W['openid'],
    'uniacid'=>$_W['uniacid']
);

$member = pdo_get($tableFans,$check,array('nickname','uid'));
$member['images'] = pdo_get($tableMc,array('uid'=>$member['uid']),array('avatar'));//$res['avatar']
$kcode = pdo_getall($tableUser,$check,array('id','uniacid','kid','kcode','kcode','kname','state','createtime','is_subscribe') , '' ,' id DESC');

# 如果用户名或者 头像为空 设置默认头像

if (empty($member['nickname'])){
    $member['nickname'] = '去吧皮卡丘';
}

if (empty($member['images']['avatar'])){
    $member['images']['avatar'] = IA_IMAGES.'/empty.jpg';
}

# 统计有几条数据
$count = count($kcode);

// if ($_W['openid'] ==  'omdV8v4VCMOT6wa7G_bkWxgeLHY4'){

//     if ($_GPC['is_ajax'] == 1){

//         if (!empty($kcode)){

//             foreach ($kcode as &$value){
//                 $value['createtime'] = date('Y-m-d H:i:s',$value['createtime']);
//             }
//         }

//         ajaxRes(0,$kcode);
//     }

//     include $this->template('test/rock');
//     die();
// }



include $this->template('member');