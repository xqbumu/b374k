<?php
if (!defined('BASE_COMMON_MAIN_FILELOADED')) {
	define('BASE_COMMON_MAIN_FILELOADED', true);

	// 预定义值，停止eval过程中无法正确返回系统常量
	$GLOBALS['config']['main_file'] = __FILE__;
}

if (!defined('BASE_COMMON_LOADED')) {
	define('BASE_COMMON_LOADED', true);

	// 排除替换变量
	$GLOBALS['config']['obscure_except_replace_var'] = array('$this', '$_', '$1_', '$1', '$2',
		'$_SERVER', '$_SESSION', '$_REQUEST', '$_POST', '$_GET',
		'$_COOKIE', '$_FILES', '$GLOBALS', '$_COOKIE');

	if (!defined('DETECT_SYS_CHARSET')) {
		define('DETECT_SYS_CHARSET', DIRECTORY_SEPARATOR == '\\' ? 'gb2312' : 'utf-8');
	}
}
?>
