<?php
/*
----------------------------------
*|  auther:  yc  yc@yuanxu.top
*|  website: yuanxu.top
---------------------------------------
*/

global $_W;
$sql = "
DROP TABLE IF EXISTS `ims_yc_expressage_api_v2`;
DROP TABLE IF EXISTS `ims_yc_expressage_user_v2`;
";
pdo_query($sql);