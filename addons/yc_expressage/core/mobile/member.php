<?php

global $_W,$_GPC;
load()->func('tpl');
#检测是否是微信登陆
checkMobile();
$tableFans = 'mc_mapping_fans';
$tableMc = 'mc_members';
$tableUser = 'yc_expressage_user';

$check = array(
    'openid' => $_W['openid'],
    'uniacid'=>$_W['uniacid']
);

$member = pdo_get($tableFans,$check,array('nickname','uid'));
$member['images'] = pdo_get($tableMc,array('uid'=>$member['uid']),array('avatar'));//$res['avatar']
$kcode = pdo_getall($tableUser,$check);

# 如果用户名或者 头像为空 设置默认头像

if (empty($member['nickname'])){
    $member['nickname'] = '去吧皮卡丘';
}
if (empty($member['images']['avatar'])){
    $member['images']['avatar'] = IA_IMAGES.'/empty.jpg';
}

# 统计有几条数据
$count = count($kcode);


include $this->template('member');