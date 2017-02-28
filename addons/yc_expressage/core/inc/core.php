<?php
/*
----------------------------------
*|  auther:  yc  yc@yuanxu.top
*|  website: yuanxu.top
---------------------------------------
*/
# 重新定义 template() 方法




if (!defined('IN_IA')) {
    die( 'Access Denied' );
}

class Core extends WeModuleSite {
    public $footer = array();
    public $header = null;

    public function template($filename, $type = TEMPLATE_INCLUDEPATH){
        global $_W;
        $name = strtolower($this->modulename);
        $defineDir = dirname($this->__define);
        if (defined('IN_SYS')) {
            $source = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/addons/{$name}/template/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$filename}.html";
            }
            if (!is_file($source)) {
                $explode = explode('/', $filename);
                $temp = array_slice($explode, 1);
                $source = IA_ROOT . "/addons/{$name}/plugin/" . $explode[0] . "/template/" . implode('/', $temp) . ".html";
            }
        } else {
            $source = IA_ROOT . "/app/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = IA_ROOT . "/data/tpl/app/{$_W['template']}/{$name}/{$filename}.tpl.php";
            # 自定义变量
            if(!is_file($source)) {
                $source = IA_ROOT . "/addons/{$name}/template/mobile/{$filename}.html";
            }

            if(!is_file($source)) {
                $source = IA_ROOT . "/app/themes/default/{$name}/{$filename}.html";
            }
            if(!is_file($source)) {
                $source = $defineDir . "/template/mobile/{$filename}.html";
            }
            if(!is_file($source)) {
                $source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
            }
            if(!is_file($source)) {
                if (in_array($filename, array('header', 'footer', 'slide', 'toolbar', 'message'))) {
                    $source = IA_ROOT . "/app/themes/default/common/{$filename}.html";
                } else {
                    $source = IA_ROOT . "/app/themes/default/{$filename}.html";
                }
            }
        }
        if (!is_file($source)) {
            die( "Error: template source '{$filename}' is not exist!" );
        }

        if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
            template_compile($source, $compile, true);
        }

        return $compile;
    }

    /**
     * 获取控制器对应的PHP文件
     * parameter@
     *  $do 方法名
     *  $web 是否是web(后台文件) 默认为 ture, false为手机端
     *  $default 加载对应的php文件.默认为index
     **/

    public function _exec($do, $web = true){
        global $_GPC;
        $name = strtolower($this->modulename);
        $do = strtolower(substr($do, $web ? 5 : 8));

        if ($web) {
            $file = IA_ROOT . "/addons/".$name."/core/web/" . $do .  ".php";
        } else {
            $file = IA_ROOT . "/addons/".$name."/core/mobile/" . $do . ".php";
        }
        if (!is_file($file)) {
            message("未找到 控制器文件 {$do} : {$file}");
        }
        include $file;
        die;
    }

}