<?php

global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$uid = $_W['uid'];
load()->func('tpl');
#检测是否是微信登陆
checkMobile();


$res = explode(',',$_POST['code']);
$kid = trim(intval($res[1]));

if ($kid == false){
    $res['status'] = -1;
    $res['mag'] = '这不是快递单号';
    return json_encode($res);

}
# 查询的参数
$date = array(
    'uniacid' => $_W['uniacid'],
    'openid' => $_W['openid'],
    'kid' => $kid
);
# 查询是否存在 该数据
$res = pdo_get($this->tableUser,$date);

if (empty( $res )){
    # 数据库返回结果为空,发送两个api请求
    # 获取api参数
    $api = pdo_get($this->tableApi, array('uniacid' => $uniacid), array('EBusinessID', 'key'));
    if (empty($api)){
        $res['status'] = -2;
        $res['mag'] = '获取API参数失败';
        return json_encode($res);
    }
    # 用快递名字.换取快递代码
    require  IA_CORE_API."/check.php";
    $check = new check();
    $result = $check->goGit($kid,$api['EBusinessID'],$api['key']);
    $Code = $result['Shippers'][0];

    # 查询快递信息
    require  IA_CORE_API."/api.php";
    $goGit = new express();
    $rescod = $goGit->goGit($Code['ShipperCode'],$kid,$api['EBusinessID'],$api['key']);

    if (empty($rescod['Success']) ){
        $res['status'] = -3;
        $res['mag'] = '快递查询失败';
        return json_encode($res);
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

    $result =  pdo_insert($this->tableUser,$date);
    if (empty( $result )){
        $res['status'] = -3;
        $res['mag'] = '添加数据失败,请联系管理员';
        return json_encode($res);

    }
    # 组成html页面显示数据
    $state = $this->state($rescod['State']);

    $res['state'] = $state;
    $res['kid'] = $kid;
    $res['kname'] = $Code['ShipperName'];
    $res['status'] = 1;
    return json_encode($res);

}else if ($res['state']==3){
    $res['state']='已签收';
    #json 转数组
    $res['Traces'] = json_decode($res['content'],true);

    $res['status'] = 1;
    return json_encode($res);
}else {
    # 未签收,用已知的数据查询快递状态 api请求api物流,更新数据

    # 获取api参数
    $api = pdo_get($this->tableApi, array( 'uniacid' => $uniacid ), array( 'EBusinessID', 'key' ));
    if (empty( $api )) {
        $res['status'] = -1;
        $res['mag'] = '获取API参数失败';
        return json_encode($res);
    }

    # 查询快递信息
    require  IA_CORE_API."/api.php";
    $goGit = new express();
    $rescod = $goGit->goGit($res['kcode'], $kid, $api['EBusinessID'], $api['key']);

    # 判断查询是否成功
    if (empty( $rescod['Success'] )) {
        $res['status'] = -3;
        $res['mag'] = '快递单号输入有误';
        return json_encode($res);
    }

    # 把快递信息进行倒序
    $res['Traces'] = array_reverse($rescod['Traces']);

    # 转为json
    $content = json_encode($res['Traces']);

    # 把最新的数据  插入数据库
    if (empty( $rescod['State'] )){
        $newdate = array();
    }else{
        $newdate = array(
            'state' => $rescod['State'],
            'content' => $content
        );
        $result = pdo_update($this->tableUser, $newdate, $date);
        if (empty( $result )) {
            $res['status'] = -3;
            $res['mag'] = '更新数据失败,请联系管理员';
            return json_encode($res);
        }
    }

    # 组成html页面显示数据
    $state = $this->state($rescod['State']);

    $res['state'] = $state;
    $res['status'] = 1;
    return json_encode($res);
}