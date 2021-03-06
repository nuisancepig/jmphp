<?php
	/*解析安卓apk包中的压缩XML文件，还原和读取XML内容
	依赖功能：需要PHP的ZIP包函数支持。*/
	include('Apkparser.php');
	$appObj  = new Apkparser(); 
	$targetFile = 'package/jmpda.apk';//apk所在的路径地址
	$res   = $appObj->open($targetFile);
	$appObj->getAppName();     // 应用名称
	$appObj->getPackage();    // 应用包名
	$version=$appObj->getVersionName();  // 版本名称
	$appObj->getVersionCode();  // 版本代码
	$json = json_encode($version);
    echo $json;
?>