<?php
// +----------------------------------------------------------------------
// |  [ 我的梦想是星辰大海 ]
// +----------------------------------------------------------------------
// | Author: yc  yc@yuanxu.top
// +----------------------------------------------------------------------
// | Date: 17.7.24 Time: 22:25
// +----------------------------------------------------------------------
//twq.yuanxu.top/addons/yc_expressage/core/api/subscribePull.php


define('IN_SYS', true);

// 传递判断

if (empty($_POST)){
    ajaxRes(-1,'参数不能为空');
}

if ($_POST['RequestType'] != 101){
    ajaxRes(-1,'暂不支持该接口');
}

$RequestData = json_decode($_POST['RequestData'],true);

if (empty($RequestData['Data'])){
    ajaxRes(-1,'数据不能为空');
}

$RequestDataArr = $RequestData['Data'];
require '../../../../framework/bootstrap.inc.php';

foreach ($RequestDataArr as $value){

//    $setData['kid'] = $value['LogisticCode'];
//    $setData['kcode'] = $value['ShipperCode'];
    $setData['kid'] = '70378911132851';
    $setData['kcode'] = 'HTKY';
    $setData['state'] = $value['State'];
    $tracesArr = array_reverse($value['Traces']);
    $setData['content'] = json_encode($tracesArr);
    $setData['update_time'] = time();
    $setData['wait_notification'] = 1; //等待通知

    $kidRes = getKid($setData['kid'],$setData['kcode']);
    // 更新数据
    if (!empty($kidRes)){

        $kidRes['kid'] = $setData['kid'];
        $kidRes['kcode'] = $setData['kcode'];
        $kidRes['content'] = $tracesArr[0];

        $UpdataRes = Updata($setData);
        if (!empty($UpdataRes)){
            // 模版通知用户
            $sendRes = sendMessage($kidRes);

            if (empty($sendRes)){
                ajaxRes(-1,$msg ='推送失败',$RequestData['EBusinessID']);
            }
        }
    }else{
        ajaxRes(-1,$msg ='推送失败',$RequestData['EBusinessID']);
    }

}

ajaxRes(0,$msg ='',$RequestData['EBusinessID']);

/**
 * 推送物流信息
 * @param $kidRes
 * @return bool
 */
function sendMessage($kidRes){

    if (empty($kidRes['openid'])){
        return false;
    }

    define('DS', DIRECTORY_SEPARATOR);
    define('IA_ADDONS', IA_ROOT . DS .'addons');
    define('Mname', 'yc_expressage');
    define('IA_Mname', IA_ADDONS . DS . Mname);
    define('IA_CORE', IA_Mname . DS .'core');

    require '../../function.php';
    $notice = m('notice');
    load()->model('account');
    $noticeRes = $notice->sendMessage($kidRes);

    return $noticeRes;
}


/**
 * 查询数据库中是否已存在数据
 * @param $kid
 * @param $kcode
 * @param $userTbname
 * @return bool
 */
function getKid($kid,$kcode, $userTbname = 'yc_expressage_user'){

    $returnRes = false;
    $kidRes = pdo_get($userTbname,array('kid'=>$kid,'kcode'=>$kcode,'is_subscribe'=>'1'),array('uniacid','openid','kname'));

    if (!empty($kidRes)){
        $returnRes = $kidRes;
    }
    return $returnRes;
}


/**
 * 更新数据
 * @param $Updata
 * @param string $userTbname
 * @return bool
 */
function Updata($Updata,$userTbname = 'yc_expressage_user'){

    // 更新条件
    $UpWhere = array(
        'kid'=>$Updata['kid'],
        'kcode'=>$Updata['kcode']
    );

   $UpdataRes = pdo_update($userTbname,$Updata,$UpWhere);
   return $UpdataRes;
}



//file_put_contents('pull.log',var_export($_POST, true), 8);


/**
 * @param $code
 * @param string $msg
 * @param string $EBusinessID
 */
function ajaxRes($code,$msg ='',$EBusinessID = '000000'){
    $success = false;

    if ($code == 0){
        $success = true;
    }

    $Resarr = array(
        "EBusinessID"=>$EBusinessID,
        "UpdateTime"=>date('Y-m-d H:i:s',time()),
        "Success"=> $success,
        "Reason"=> $msg
    );

    die(json_encode($Resarr));
}