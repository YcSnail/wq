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


    /**
     * 加载html 文件函数
     * @$filename 文件夹名字
     * @$file 文件名字 不填则默认index文件
    */
    public function template($filename){
        global $_W;
        $name = strtolower($this->modulename);

        if (defined('IN_SYS')) {
            $source = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/addons/{$name}/template/web/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$filename}.html";
            }
        } else {
            $template = "default";
            $file = IA_ROOT . "/addons/".$name."/data/template/shop_" . $_W['uniacid'];
            if (is_file($file)) {
                $template = file_get_contents($file);
                if (!is_dir(IA_ROOT . "/addons/".$name."/template/mobile/" . $template)) {
                    $template = "default";
                }
            }
            $compile = IA_ROOT . "/data/tpl/app/".$name."/{$template}/mobile/{$filename}.tpl.php";
            $source = IA_ROOT . "/addons/{$name}/template/mobile/{$template}/{$filename}.html";
            if (!is_file($source)) {
                $source = IA_ROOT . "/addons/{$name}/template/mobile/default/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/default/{$filename}.html";
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

    public function _exec($do, $web = true , $default=''){
        global $_GPC;
        $name = strtolower($this->modulename);

        $default = empty($default) ? 'index' : $default;

        $do = strtolower(substr($do, $web ? 5 : 8));
        $p = trim($_GPC['p']);
        empty( $p ) && ( $p = $default );
        if ($web) {
            $file = IA_ROOT . "/addons/".$name."/core/web/" . $do . "/" . $p . ".php";
        } else {
            $file = IA_ROOT . "/addons/".$name."/core/mobile/" . $do . "/" . $p . ".php";
        }
        if (!is_file($file)) {
            message("未找到 控制器文件 {$do}::{$p} : {$file}");
        }
        include $file;
        die;
    }

    /**
     * createWebUrl()加载web端页面
     *
     *
    */

    public function createWebUrl($do, $query = array()){
        global $_W;
        $do = explode('/', $do);
        if (count($do) > 1 && isset( $do[1] )) {
            $query = array_merge(array( 'p' => $do[1] ), $query);
        }
        return $_W['siteroot'] . 'web/' . substr(parent::createWebUrl($do[0], $query, true), 2);
    }

    public function createMobileUrl($do, $query = array(), $noredirect = true){
        global $_W, $_GPC;
        $do = explode('/', $do);
        if (isset( $do[1] )) {
            $query = array_merge(array( 'p' => $do[1] ), $query);
        }
        if (empty( $query['mid'] )) {
            $mid = intval($_GPC['mid']);
            if (!empty( $mid )) {
                $query['mid'] = $mid;
            }
        }
        return $_W['siteroot'] . 'app/' . substr(parent::createMobileUrl($do[0], $query, true), 2);
    }



    /**
     * 获取html文件的路径
     * parameter@
     *  $do 对应的方法名(文件夹的名字)
     *  $Hname 对应的html文件的名字
     **/

    public function getHtml($do,$Hname){
        global $_W;
        $Mname = $_W['current_module']['name'];
        $st = 'http://';
        $st .= $_SERVER['HTTP_HOST'];
        $st .= '/addons/'.$Mname.'/template/web/'.$do.'/'.$Hname.'.html';
        return $st;
    }


}