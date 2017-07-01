<?php

/**
 * 定义一些常用的方法
 */


/**
 * 查查当前浏览方式是不是 手机端
 * @return bool
 */
function is_wechat(){
    if (empty( $_SERVER['HTTP_USER_AGENT'] ) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
        return false;
    }
    return true;
}

/**
 * @param string $name
 * @return mixed
 */
function m($name = ''){
    static $_modules = array();
    if (isset($_modules[$name])) {
        return $_modules[$name];
    }
    $model = IA_CORE . "/model/" . strtolower($name) . '.php';
    if (!is_file($model)) {
        die(' Model ' . $name . ' Not Found!');
    }
    require $model;
    $class_name = ucfirst($name) . '_Model';
    $_modules[$name] = new $class_name();
    return $_modules[$name];
}


/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true){
    $label = ( $label === null ) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo( $output );
        return null;
    }
    return $output;
}

# 匹配快递状态
function state($state){
    switch ($state){
        case '2':
            $state = '在途中';
            break;
        case '3':
            $state = '已签收';
            break;
        case '4':
            $state = '问题件';
            break;
        default:
            $state = '暂未查询到快递信息';
    }
    return $state;
}

/**
 * ajax 返回值
 * @param $code
 * @param $msg
 * @return string
 */
function ajax_res($code,$msg){
    $res = array(
        'status'=>$code,
        'msg'=>$msg
    );

    die(json_encode($res));
}


