<?php

/*
----------------------------------
*|  auther:  yc  yc@yuanxu.top
*|  website: yuanxu.top
---------------------------------------
*/
defined('IN_IA') or exit('Access Denied');
require 'core/inc/core.php';
require 'core/inc/define.php';
require 'core/inc/user.php';

class Yc_expressageModuleSite extends Core {
	public $tableApi = 'yc_expressage_api';
	public $tableUser = 'yc_expressage_user';

	# web端 查询快递
	public function doWebIndex(){
        $this->_exec(__FUNCTION__,true);
	}

	# 设置快递api 参数
	public function doWebapi() {
        $this->_exec(__FUNCTION__,true);
	}

	# 用户查询快递
	public function doMobileindex() {
        $this->_exec(__FUNCTION__,false);
	}

	#扫一扫查询快递
	public function doMobileSweep() {
        $this->_exec(__FUNCTION__,false);
	}

	# 通过扫一扫查快递
	public function doMobileget(){
        $this->_exec(__FUNCTION__,false);
	}

	# 个人中心
	public function doMobileMember(){
        $this->_exec(__FUNCTION__,false);
	}

	public function doMobiletest(){
		global $_W;
		include $this->template('test');
	}
	# 匹配快递状态
	public function state($state){
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

}