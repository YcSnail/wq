<?php
/**
 * yc_shop商城模块处理程序
 *
 * @author yc
 * @url http://yuanxu.top
 */
defined('IN_IA') or exit('Access Denied');

class Yc_shopModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码

        $api_key = "VwPnv4pnlci6cfz4SUckrDmT99guHXEF";
        $api_secret = "H78UXk_fw1c1lZXAzOwC_UgvOsifiyLY";
        $picurl = $this->message['picurl'];
        $url = "https://api-cn.faceplusplus.com/facepp/v3/detect";

        $data =array(
            'api_key'=>$api_key,
            'api_secret'=>$api_secret,
            'image_url'=>$picurl,
            'return_attributes'=>'gender,age,smiling,glass,headpose,facequality,blur'
        );
        # yc
        function post2($url, $data){
            $postdata = http_build_query($data);
            $opts = array( 'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
            );
            $context = stream_context_create($opts);
            $result = file_get_contents($url, false, $context);
            return $result;
        }
        $show = post2($url,$data);
        return $this->respText($show);

    }
}