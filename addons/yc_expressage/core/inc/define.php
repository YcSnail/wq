<?php
// +----------------------------------------------------------------------
// | WSHOTO [ 技术主导，服务至上，提供微信端解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2020 http://www.wshoto.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: yc <yc@yuanxu.top>
// +----------------------------------------------------------------------
/**
 * 自定义函数定义文件
 *
*/
# 插件名称

define('DS', DIRECTORY_SEPARATOR);
define('IA_ADDONS', IA_ROOT . DS .'addons');
define('IA_FRAMEWORK', IA_ROOT . DS .'framework');
define('Mname', $_W['current_module']['name']);
define('IA_Mname', IA_ADDONS . DS . Mname);

define('IA_STATIC', $_W['siteroot'] .'addons'. DS . $_W['current_module']['name'] . DS . 'static');
define('IA_IMAGES', IA_STATIC . DS . 'images');
define('IA_JS', IA_STATIC . DS . 'js');
define('IA_CSS', IA_STATIC . DS . 'css');


define('IA_CORE', IA_Mname . DS .'core');
define('IA_CORE_API', IA_CORE . DS . 'api');
