<?php
if (!class_exists('ScannerClass')) {
	class ScannerClass {
		public static $types = array(
			'php' => array(
				'exts' => array('php', 'inc', 'txt', 'html'),
				'rule' => array('php'),
			),
			'js' => array(
				'exts' => array('js'),
				'rule' => array('php'),
			),
			'asx' => array(
				'exts' => array('asp', 'asa', 'cer', 'aspx', 'ascx'),
				'rule' => array('asx'),
			),
			'jpg' => array(
				'exts' => array('jpg', 'jpeg'),
				'rule' => array('php'),
			),
			'gif_bmp' => array(
				'exts' => array('bmp', 'gif', 'png'),
				'rule' => array('php'),
			),
			'txt' => array(
				'exts' => array('txt'),
				'rule' => array('php'),
			),
		);

		public static $rules = array(
			'php' => array(
				'一句话后门特征' => array(
					array('regexp', 'function\_exists\s*\(\s*[\'|\"](popen|exec|proc\_open|system|passthru)+[\'|\"]\s*\)'),
					array('regexp', '(exec|shell\_exec|system|passthru)+\s*\(\s*\$\_(\w+)\[(.*)\]\s*\)'),
					array('regexp', '((udp|tcp)\:\/\/(.*)\;)+'),
					array('regexp', 'preg\_replace\s*\((.*)\/e(.*)\,\s*\$\_(.*)\,(.*)\)'),
					array('regexp', 'preg\_replace\s*\((.*)\(base64\_decode\(\$'),
					array('regexp', '(eval|assert|include|require|include\_once|require\_once)+\s*\(\s*(base64\_decode|str\_rot13|gz(\w+)|file\_(\w+)\_contents|(.*)php\:\/\/input)+'),
					array('regexp', '(eval|assert|include|require|include\_once|require\_once|array\_map|array\_walk)+\s*\(\s*\$\_(GET|POST|REQUEST|COOKIE|SERVER|SESSION)+\[(.*)\]\s*\)'),
					array('regexp', 'eval\s*\(\s*\(\s*\$\$(\w+)'),
					array('regexp', '(include|require|include\_once|require\_once)+\s*\(\s*[\'|\"](\w+)\.(jpg|gif|ico|bmp|png|txt|zip|rar|htm|css|js)+[\'|\"]\s*\)'),
					array('regexp', '\$\_(\w+)(.*)(eval|assert|include|require|include\_once|require\_once)+\s*\(\s*\$(\w+)\s*\)'),
					array('regexp', '\(\s*\$\_FILES\[(.*)\]\[(.*)\]\s*\,\s*\$\_(GET|POST|REQUEST|FILES)+\[(.*)\]\[(.*)\]\s*\)'),
					array('regexp', '(fopen|fwrite|fputs|file\_put\_contents)+\s*\((.*)\$\_(GET|POST|REQUEST|COOKIE|SERVER)+\[(.*)\](.*)\)'),
					array('regexp', 'echo\s*curl\_exec\s*\(\s*\$(\w+)\s*\)'),
					array('regexp', 'new com\s*\(\s*[\'|\"]shell(.*)[\'|\"]\s*\)'),
					array('regexp', '\$(.*)\s*\((.*)\/e(.*)\,\s*\$\_(.*)\,(.*)\)'),
					array('regexp', '\$\_\=(.*)\$\_'),
					array('regexp', '\$\_(GET|POST|REQUEST|COOKIE|SERVER)+\[(.*)\]\(\s*\$(.*)\)'),
					array('regexp', '\$(\w+)\s*\(\s*\$\_(GET|POST|REQUEST|COOKIE|SERVER)+\[(.*)\]\s*\)'),
					array('regexp', '\$(\w+)\s*\(\s*\$\{(.*)\}'),
					array('regexp', '\$(\w+)\s*\(\s*chr\(\d+\)'),
					array('regexp', '^>\\$[a-z_A-Z]+\('),

					array('regexp', '(httpcopy|array\_map|strrev|call_user_func)+\s*\(\s*\$\_(GET|POST|REQUEST|COOKIE|SERVER|SESSION)+\[(.*)\]\s*\)'),
					array('regexp', 'echo\(file_get_contents\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)'),
					array('regexp', 'error_reporting\(0\)\s*;\S+'),
					array('regexp', '[^\W|^(strr)]chr\('),
					array('regexp', '\)\.@\$_\(\$_GET\['),
					// 'curl_exec',
					array('regexp', 'if \(\!defined\("'),
					array('regexp', 'if\(\\$_GET\[\{'),
					array('regexp', '"Access"\);'),
					array('regexp', '\],\\$_FILES\['),
					array('regexp', '\],\$_POST\['),
					array('regexp', '@eval_r\(\$_POST\['),
					array('regexp', '\(\$_POST\[chr\('),
					array('regexp', 'true\);global'),
					// 'file_get_contents\(.+',
					array('regexp', '\]\(\$_POST\['),
					array('regexp', '\)\.@\$_\(\$_POST\['),
					// 'chmod\(',
					array('regexp', '\]\(\$_REQUEST\['),
					array('regexp', 'php\:\/\/input'),
					array('regexp', 'popen\(.+\)'),
					// 'system',
					// 'str_replace\(',
					// ' str_replace',
					// 'touch',
				),
				'后门外站' => array(
					array('string', '114.215.88.150'),
					array('string', '180.97.220.64'),
					array('string', '180.97.220.5'),
					array('string', '211.219.83.69'),
					array('string', '23.225.154.210'),
					array('string', '173.234.214.139'),
					array('string', '23.244.184.188'),
					array('string', '23.225.153.122'),
					array('string', 'thjbcw.com'),
					array('string', 'kfedu.com'),
					array('string', 't.cn'),
					array('string', 'vidun.com'),
					array('string', 'anyaoying.pw'),
					array('string', 'dedecms.com'),
					array('string', 'banbingjiatiao.com'),
					array('string', 'jiaoqing.com'),
					array('string', '第三方网站加载中'),
					array('string', '天龙八部'),
					array('string', 'maijiasz'),
					array('string', '2.3.a'),
					array('string', 'matsuokaprint'),
					array('string', 'xnchem'),
					array('string', 'dfepe'),
					array('string', 'hzforward'),
					array('string', 'longsheng'),
					array('string', 'jinggongair'),
					array('string', 'auxgroup'),
					array('string', '22pk10'),
					array('string', '7jyewu'),
					array('string', 'zch5858'),
					array('string', '开奖网'),
				),
				'后门key' => array(
					array('string', '\'ninja\''),
					array('string', 'MetaSr'),
					array('string', 'snc3'),
					array('string', 'UUdWMllXd'),
					array('string', 'cE93P'),
					array('string', 'ct7'),
					array('string', 'cgs1'),
					array('string', 'IEBldmFbsK'),
					array('string', 'CRfUE9TVF'),
					array('string', 'QWE321321'),
					array('string', 'W0BldmFsKGJhc2U2NF9kZWNvZGUoJF9QT1NUW3owXSkpO10'),
					array('string', 'tmp/js.php'),
					array('string', 'ceshi2012'),
					array('string', 'luyilu68'),
					array('string', 'MTE4LjE5My4xNjkuNDcvMDE3'),
					array('string', '6YOR5beeUENQ6L'),
					array('string', 'J3Nhb3Nhb'),
					array('string', 'ydidKiTisg'),
					array('string', 'IEBldimFsIC'),
					array('string', 'qsqtqrq_replqace'),
					array('string', 'gkX1BPU1Rb'),
					array('string', 'vbasev6v4_vdvevcovdve'),
					array('string', 'ciccircieciacitcie_cifciucinciccitiocin'),
					array('string', 'slstlsrls_rlselsplslaclse'),
					array('string', 'a`s`s`e`r`t'),
					array('string', '6A767C687B77'),
					array('string', 'L2luZGV4LnBocD9ob3N0PQ'),
					array('string', 'aHR0cDovL3podXpodXouY24v'),
					array('string', 'aHR0cDovLw'),
					array('string', 'set_writeable'),
					array('string', 'myhack58'),
					array('string', '$_SESSION[\'PhpCode\']'),
					array('string', '@$_="s"'),
					array('string', 'asping'),
					array('string', 'axsxxsxexrxxt'),
					array('string', '"a"."s"."s"."e"."r."t";'),
					array('string', '"ass"."ert"'),
					array('string', 'include("$file")'),
					array('string', '1833596'),
					array('string', '0155'),
					array('string', '<script language="php'),
				),
				'执行脚本' => array(
					array('string', 'batch-replace'),
					array('string', 'g00nshell'),
				),
				'可疑文件' => array(
					array('regexp', 'upload\/.*.txt'),
					array('string', 'index.txt'),
					array('string', '在线解压ZIP文件及删除文件'),
					array('string', 'utesta_uploadicci'),
				),
				'赌博网站外链' => array(
					array('string', 'www.hljlzy.com'),
					array('string', 'hj.ntzj.gov.cn'),
					array('string', 'www.81china.org'),
					array('string', 'rwgl.hbjd.edu.cn'),
					array('string', 'haojinw.com'),
					array('string', 'www.nkdw.net'),
					array('string', '66888777'),
					array('string', '13148866'),
					array('string', '1314001'),
					array('string', '13148866'),
					array('string', 'ckl.date'),
				),
				'赌博网站关键字' => array(
					array('string', '百家乐'),
					array('string', '乐透'),
					array('string', '赌博'),
					array('string', '棋牌'),
					array('string', '百家乐'),
					array('string', '娱乐城'),
					array('string', '斗地主'),
					array('string', '双色球'),
				),
				'探针' => array(
					array('string', 'UenuCom探针'),
				),
				'后门特征' => array(
					array('string', 'gutou'),
					array('string', 'gzread'),
					array('string', 'alexa'),
					array('string', 'ckfd'),
					array('string', '186056'),
					array('string', '映阳网络'),
					array('string', 'YXNzZXJ0'),
					array('string', 'thjbcw'),
					array('string', 'key.txt'),
					array('string', 'new.txt'),
					array('string', 'content.txt'),
					array('string', 'article.html'),
					array('string', 'list.html'),
					array('string', 'home.html'),
					array('string', 'ckj1.cn'),
					array('string', '$_SERVER[\'QUERY\_STRING\']'),
					array('string', 'cha88.cn', 'cha88.cn'),
					array('string', 'c99shell', 'c99shell'),
					array('string', '51shell', '51shell大马'),
					array('string', 'ts7', 'ts7shell'),
					array('string', 'Scanners', 'Scanners'),
					array('string', 'cmd.php', 'cmd.php'),
					array('string', 'str_rot13', 'str_rot13'),
					array('string', 'webshell', 'webshell'),
					array('string', 'EgY_SpIdEr', 'EgY_SpIdEr'),
					array('string', 'tools88.com', 'tools88.com'),
					array('string', 'SECFORCE', 'SECFORCE'),
					array('string', 'eval\((\'|")\?>', 'eval("?>'),
					array('string', 'phpspy', 'phpspy1'),
					array('string', 'admin = array()', 'phpspy2'),
					array('string', '\/\/angel', 'phpspy3'),
					array('string', 'PHPJackal', 'PHPJackal-1'),
					array('string', 'Web shell', 'PHPJackal-2'),
					array('string', 'H4NN1B4L', '国外shell-1'),
					array('string', 'N3tshexit', '国外shell-2'),
					array('string', 'admin\[', 'php特征admin'),
					array('string', '大马', '大马特征1'),
					array('string', '小马', '大马特征2'),
					array('string', '打包下载', '后门特征->打包马1'),
					array('string', '选择要压缩的文件或目录', '打包马2'),
					array('string', '打包程序扩展名', '打包马3'),
					array('string', 'faisunZIP', '打包马4'),
					array('string', 'copy($_FILES', 'php小马1'),
					array('string', 'copy ($_FILES', 'php小马2'),
					array('string', '$fp = @fopen($_POST', 'php小马3'),
					array('string', '保存成功', 'php小马4'),
					array('string', 'fputs(fopen', 'php小马5'),
					array('string', 'xise', 'xise'),
					array('string', 'lin.php'),
				),
				'上传后门特征' => array(
					array('regexp', 'move_uploaded_file \($_FILES'),
					array('regexp', 'file_put_contents\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)\[([^\]]+)\],(\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)'),
					array('regexp', 'fputs\(fopen\((.+),(\'|")w(\'|")\),(\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)\['),
				),
				'可疑代码特征' => array(
					array('regexp', '\)\)\)\;return\;\?', '威盾加密'),
					array('regexp', 'eval\((\'|"|\s*)\\$', 'eval($'),
					array('regexp', 'assert\((\'|"|\s*)\\$', 'assert($'),
					array('string', 'shell_exec('),
					array('string', 'exec('),
					array('string', 'proc_open'),
					array('string', 'escapeshellarg'),
					array('string', 'system('),
					array('string', '替换内容'),
					array('string', 'phpinfo();'),
				),
				'加密后门特征' => array(
					array('regexp', 'eval\(gzinflate\('),
					array('regexp', 'eval\(base64_decode\('),
					array('regexp', 'eval\(gzuncompress\('),
					array('regexp', 'eval\(gzdecode\('),
					array('regexp', 'eval\(str_rot13\('),
					array('regexp', 'gzuncompress\(base64_decode\('),
					array('regexp', 'base64_decode\(gzuncompress\('),
					array('regexp', 'base64_decode\(([^\]]{0,256})\$_(POST|GET|REQUEST|COOKIE)\[([^\]]+)\]'),
					array('regexp', 'base64_decode\(([^\]]+)\$_(POST|GET|REQUEST|COOKIE)\[([^\]]+)\]'),
				),
				'危险MYSQL代码' => array(
					array('string', 'returnsstringsoname'),
					array('string', 'intooutfile'),
					array('regexp', 'select(\s+)(.*)load_file'),
				),
				'提权' => array(
					array('string', 'returns string soname', 'UDF-1'),
					array('string', 'c\:\\windows\\system32', 'UDF-2'),
					array('string', 'C\:\\Winnt\\udf.dll', 'UDF-3'),
					array('string', 'mixdll', 'mix-1'),
				),
				'.htaccess插马特征' => array(
					array('string', 'SetHandlerapplication\/x-httpd-php', 'SetHandler application/x-httpd-php'),
					array('string', 'php_valueauto_prepend_file', 'php_value auto_prepend_file'),
					array('string', 'php_valueauto_append_file', 'php_value auto_append_file'),
				),
			),
			'asx' => array(
				'asp小马特征' => array(
					array('string', '输入马的内容', '2'),
					array('string', 'fso.createtextfile(path,true)', '3'),
				),
				'asp一句话特征' => array(
					array('string', '<%execute(request', '4'),
					array('string', '<%eval request', '5'),
					array('string', 'execute session(', '6'),
				),
				'asp大小马特征' => array(
					array('string', 'WScript.Shell', '8'),
					array('string', '<%@ LANGUAGE = VBScript.Encode %>', '9'),
				),
				'asp数据库后门特征' => array(
					array('string', '--Created!', 'asp数据库后门特征7'),
				),
				'aspx大马特征' => array(
					array('string', 'www.rootkit.net.cn', '10'),
					array('string', 'Process.GetProcesses', '11'),
					array('string', 'lake2', '12'),
				),
			),
		);

		function __construct() {

		}

		function get_rules($type = 'php') {
			$type_info = isset(self::$types[$type]) ? self::$types[$type] : null;
			if ($type_info != null) {
				$type_info['rules'] = array();
				foreach (self::$rules as $key => $value) {
					if (in_array($key, $type_info['exts'])) {
						$type_info['rules'][$key] = $value;
					}
				}
			} else {
				$type_info['error'] = 'The scanner type of ' . $type . ' is not defined';
			}
			return $type_info;
		}

		function check_file($file, $type_info) {
			$res = array();
			$file_size = FileManagerClass::size($file);
			if ($file_size <= pow(2, 22)) {
				// 4M
				$file_content = FileManagerClass::get($file);
				$file_content_lower = strtolower(FileManagerClass::get($file));
				if (is_array($type_info) && isset($type_info['rules']) && sizeof($type_info['rules']) > 0) {
					foreach ($type_info['rules'] as $rule_level_0 => $value_level_0) {
						foreach ($value_level_0 as $rule_level_1 => $value_level_1) {
							foreach ($value_level_1 as $rule_info) {
								$rule_name_arr = array($rule_level_0, $rule_level_1);
								if (sizeof($rule_info) >= 3) {
									array_push($rule_name_arr, $rule_info[2]);
								} elseif (sizeof($rule_info) == 2) {
									array_push($rule_name_arr, $rule_info[1]);
								}
								$rule_name = implode('->', $rule_name_arr);
								$rule_name = convert_string_to_sys($rule_name);
								if (sizeof($res) <= 3) {
									switch ($rule_info[0]) {
									case 'regexp':
										$pos = preg_match("/$rule_info[1]/i", $file_content);
										if ($pos) {
											array_push($res, $rule_name);
										}
										break;
									case 'string':
									default:
										if (strpos($file_content_lower, strtolower($rule_info[1])) !== false) {
											array_push($res, $rule_name);
										}
										break;
									}
								}
							}
						}
					}
				}
			} else {
				array_push($res, 'The file of ' . $file . ' is too large.');
			}
			return $res;
		}

		function filter_files_type($files = array(), $type_info) {
			$res = array();
			if (is_array($files) && sizeof($files) > 0) {
				foreach ($files as $key => $value) {
					if (in_array(FileManagerClass::extension($value), $type_info['exts'])) {
						array_push($res, $value);
					}
				}
			}
			return $res;
		}

		function filter_files_rule($files = array(), $type_info) {
			$files = $this->filter_files_type($files, $type_info);
			$res = array();
			if (is_array($files) && sizeof($files) > 0) {
				foreach ($files as $index => $item) {
					$check_res = $this->check_file($item, $type_info);
					if (sizeof($check_res) > 0) {
						$res[$item]['errors'] = $check_res;
					}
				}
			}
			return $res;
		}

	}
}

$GLOBALS['module']['scanner']['id'] = "scanner";
$GLOBALS['module']['scanner']['title'] = "Scanner";
$GLOBALS['module']['scanner']['js_ontabselected'] = "";
$GLOBALS['module']['scanner']['content'] = "
<table class='boxtbl box-scanner'>
<thead>
	<tr>
		<th style='width:256px;'>Type</th>
		<th>Path</th>
		<th style='width:128px;'></th>
	</tr>
</thead>
<tbody>
	<tr class='scannerInfoRow'>
		<td><select id='type'></select></td>
		<td><input type='text' id='path' value='" . '' . "'></td>
		<td><span class='button' onclick='scanner_go();'>Go</span></td>
	</tr>
	<tr class='dbConnectRow'>
		<td colspan='3' class='scannerError' style='color: #FF0000;'></td>
	</tr>
</tbody>
</table>
<div colspan='3' id='scannerResult'><tr><td colspan='2'>You can also press ctrl+enter to submit</td></tr></div>";

if (isset($p['scannerGetTypeSupported'])) {
	$types = array();
	foreach (ScannerClass::$types as $key => $value) {
		array_push($types, $key);
	}
	output(implode('{[|b374k|]}', $types), 'utf-8');
} elseif (isset($p['scannerPath'])) {
	$sc = new ScannerClass();
	$fmc = new FileManagerClass();
	$scannerPath = trim($p['scannerPath']);
	$scannerType = trim($p['scannerType']);
	$scannerTypeInfo = $sc->get_rules($scannerType);

	if (isset($scannerTypeInfo['error'])) {
		output($scannerTypeInfo['error']);
		die();
	}

	$candidate = $fmc->get_all_files($scannerPath);
	$meta_files = array_filter($candidate, "is_file");
	$meta_dirs = array_filter($candidate, "is_dir");
	$candidate = $sc->filter_files_rule($meta_files, $scannerTypeInfo);

	if (sizeof($candidate) > 0) {
		$res = $fmc->explor_files($candidate);
	} else {
		$res = "";
	}

	output($res);
}
?>