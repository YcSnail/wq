<?php
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$uid = $_W['uid'];
load()->func('tpl');
#检测是否是微信登陆
checkMobile();

if(checksubmit('check') || !empty($_GPC['kid']) ) {
    # 获取用户填写的 快递单号
    $kid = trim($_GPC['kid'],'');
    if (empty( $kid ) ){
        message('快递单号不能为空', $this->createMobileUrl('cover1', array() )  , 'error');
    }
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
            message('快递api参数错误,请联系管理员', $this->createWebUrl('show1', array() )  , 'error');
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
            message('快递单号输入有误', $this->createMobileUrl('cover1', array() )  , 'error');
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
            message('添加数据失败,请联系管理员', $this->createMobileUrl('cover1', array() )  , 'error');
        }
        # 组成html页面显示数据
        $state = $this->state($rescod['State']);
        $show = array(
            'kname' => $Code['ShipperName'],
            'kid' =>$kid,
            'state' =>$state
        );
        include $this->template('show');
        die();

    }else if ($res['state']==3){
        $res['state']='已签收';
        $show = array(
            'kname' => $res['kname'],
            'kid' => $res['kid'],
            'state' =>$res['state']
        );

        #json 转数组
        $res['Traces'] = json_decode($res['content'],true);

        include $this->template('show');
        die();
    }else {
        # 未签收,用已知的数据查询快递状态 api请求api物流,更新数据
        # 获取api参数
        $api = pdo_get($this->tableApi, array( 'uniacid' => $uniacid ), array( 'EBusinessID', 'key' ));
        if (empty( $api )) {
            message('快递api参数错误,请联系管理员', $this->createMobileUrl('cover1', array()), 'error');
        }

        # 查询快递信息
        require  IA_CORE_API."/api.php";
        $goGit = new express();
        $rescod = $goGit->goGit($res['kcode'], $kid, $api['EBusinessID'], $api['key']);

        # 判断查询是否成功
        if (empty( $rescod['Success'] )) {
            message('快递单号输入有误', $this->createMobileUrl('cover1', array()), 'error');
        }

        # 把快递信息进行倒序
        $res['Traces'] = array_reverse($rescod['Traces']);

        # 转为json
        $content = json_encode($res['Traces']);

        # 把最新的数据  插入数据库
        if (empty( $rescod['State']) || $rescod['State'] == '2'){
            $newdate = array();
        }else{
            $newdate = array(
                'state' => $rescod['State'],
                'content' => $content
            );
            $result = pdo_update($this->tableUser, $newdate, $date);

            if (empty( $result )) {
                message('更新数据失败,请联系管理员', $this->createMobileUrl('cover1', array()), 'error');
            }
        }

        # 组成html页面显示数据
        $state = $this->state($rescod['State']);
        $show = array(
            'kname' => $res['kname'],
            'kid' =>$kid,
            'state' =>$state
        );

        include $this->template('show');
        die();
    }
}

# 显示快递名字
include $this->template('index');