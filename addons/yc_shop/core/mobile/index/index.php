<?php
// +----------------------------------------------------------------------
// | WSHOTO [ 技术主导，服务至上，提供微信端解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2020 http://www.wshoto.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: yc <yc@yuanxu.top>
// +----------------------------------------------------------------------

if ($_GPC['about']){
    include $this->template('index','about');
    die();
}

# 测试手机端 框架兼容性
include $this->template('index');