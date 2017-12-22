<?php
if (!class_exists('FileManagerClass')) {
	class FileManagerClass {

		public static $cols_info = array();

		function __construct() {
		}

		function get_directory($path) {
			if (!is_dir($path)) {
				return "No such directory : " . $path;
			}

			chdir($path);
			$output = "";
			$allfiles = $allfolders = array();
			if ($res = opendir($path)) {
				while ($file = readdir($res)) {
					if (($file != '.') && ($file != "..")) {
						if (is_dir($file)) {
							$allfolders[] = $file;
						} elseif (is_file($file)) {
							$allfiles[] = $file;
						}

					}
				}
			}



			array_unshift($allfolders, ".");
			$cur = getcwd();
			chdir("..");
			if (getcwd() != $cur) {
				array_unshift($allfolders, "..");
			}

			chdir($cur);

			natcasesort($allfolders);
			natcasesort($allfiles);

			$cols = self::get_cols_info();

			$totalFiles = count($allfiles);
			$totalFolders = 0;

			$allfoldersRes = array();
			foreach ($allfolders as $d) {
				$dRealPath = realpath($d) . DIRECTORY_SEPARATOR;
				$allfoldersRes[$dRealPath] = self::get_folder_info($d);

				if (!in_array($d, array('.', '..'))) {
					$totalFolders++;
				}
			}

			$allfilesRes = array();
			foreach ($allfiles as $f) {
				$dRealPath = realpath($f);
				$allfilesRes[$dRealPath] = self::get_file_info($f);
			}

			return array(
				'allfolders' => $allfoldersRes,
				'allfiles' => $allfilesRes,
				'counter' => array(
					'Files' => $totalFiles,
					'Folders' => $totalFolders,
				),
				'extraCols' => array_keys($cols),
			);
		}

		function explor_directory($path) {
		}

		function get_all_files($path) {
			$path = realpath($path) . DIRECTORY_SEPARATOR;
			$files = glob($path . '*');
			for ($i = 0; $i < count($files); $i++) {
				if (is_dir($files[$i])) {
					$subdir = glob($files[$i] . DIRECTORY_SEPARATOR . '*');
					if (is_array($files) && is_array($subdir)) {
						$files = array_merge($files, $subdir);
					}

				}
			}
			return $files;
		}

		function explor_files($dir_info) {
			if (!is_array($dir_info)) {
				if (is_string($dir_info) && sizeof($dir_info) > 0) {
					$dir_info = self::get_all_files($dir_info);
				}
			}

			if (isset($dir_info['targetFiles']) && is_array($dir_info['targetFiles'])) {
				foreach ($dir_info['targetFiles'] as $tf_k => $tf_v) {
					if (is_numeric($tf_k)) {
						$dir_info['targetFiles'][$tf_v]['name'] = $tf_v;
						$dir_info['targetFiles'][$tf_v] = array_merge($dir_info['targetFiles'][$tf_v], self::get_file_info($tf_v));
						unset($dir_info['targetFiles'][$tf_k]);
					} else {
						$dir_info['targetFiles'][$tf_k] = array_merge($dir_info['targetFiles'][$tf_k], self::get_file_info($tf_k));
					}
				}
			}

			$output = '';
			$output .= "<table id='xplTable' class='dataView sortable'><thead>";
			$output .= "<tr>
			<th class='col-cbox sorttable_nosort'><div class='cBoxAll'></div></th>
			<th class='col-name'>name</th>
			<th class='col-size'>size</th>";
			if (isset($dir_info['extra_info']) && is_array($dir_info['extra_info'])) {
				foreach ($dir_info['extra_info'] as $extra_info_key => $extra_info_value) {
					$output .= "<th class='col-$extra_info_value'>$extra_info_value</th>";
				}
			}

			foreach (self::get_cols_info() as $cols_key => $cols_value) {
				$output .= "<th class='col-" . $cols_key . "'>" . $cols_key . "</th>";
			}

			$output .= "</tr></thead><tbody>";

			foreach ($dir_info['targetFiles'] as $file => $info) {
				if (isset($info['extra_info']) && isset($info['extra_info']['errors']) && sizeof($info['extra_info']['errors']) > 0) {
					$errors_info = array();
					foreach ($info['extra_info']['errors'] as $key => $value) {
						array_push($errors_info, html_safe(stripslashes(str_replace('\s*', '', $value))));
					}
					$info['extra_info']['errors'] = implode('<br />', $errors_info);
				}

				$output .= "
		<tr data-path=\"" . html_safe($file) . "\">
		<td><div class='cBox'></div></td>
		<td class='explorer-row' style='white-space:normal;'>
			<a data-path='" . html_safe($file) . "' data-errors='" . (isset($info['extra_info']['errors'])?$info['extra_info']['errors']:'') . "' onclick='view_entry(this);'>" . html_safe($info['name']) . "</a>
			<span class='action floatRight'>Action</span>
		</td>
		<td title='" . $info['filesize'] . "'>" . $info['filesize_human'] . "</td>";

				if (isset($dir_info['extra_info']) && is_array($dir_info['extra_info'])) {
					foreach ($dir_info['extra_info'] as $ext_key => $ext_value) {
						if (isset($info['extra_info']) && isset($info['extra_info'][$ext_value])) {
							$output .= "<td class='explorer-row scanner-item-errors'>" . $info['extra_info'][$ext_value] . "</td>";
						}
					}
				}

				foreach ($info['cols'] as $col_value) {
					$sortable = " title='" . $info['filemtime'] . "'";
					$output .= "<td" . $sortable . ">" . $col_value . "</td>";
				}

				$output .= "</tr>";
			}

			$output .= "</tbody><tfoot>";

			$colspan = 1 + sizeof(self::get_cols_info());
			$counterInfo = '';
			if (isset($dir_info['counter']) && is_array($dir_info['counter'])) {
				$counterInfoArr = array();
				foreach ($dir_info['counter'] as $counter_key => $counter_value) {
					array_push($counterInfoArr, $counter_value . ' ' . $counter_key);
				}
				$counterInfo = implode(', ', $counterInfoArr);
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

		function get_folder_info($folder, $extra = false) {
			$info = array();
			$info['name'] = $folder;
			$info['filemtime'] = filemtime($folder);

			if (!in_array($folder, array('.', '..'))) {
				$info['action'] = "actiondot";
				$info['cboxException'] = " cBoxException";
			} else {
				$info['action'] = "actionfolder";
				$info['cboxException'] = "";
			}

			$info['cols'] = array();
			foreach (self::get_cols_info() as $k => $v) {
				array_push($info['cols'], $this->$v($folder));
			}

			if ($extra) {
				$info['fileowner'] = self::get_fileowner($file);
				$info['perms'] = self::get_fileperms($file);
			}

			return $info;
		}

		function get_file_info($file, $extra = false) {
			$info = array();
			$info['name'] = $file;
			if (is_file($file)) {
				$info['filesize'] = filesize($file);
				$info['filesize_human'] = self::get_filesize($file);
			} else {
				$info['filesize'] = 'DIR';
				$info['filesize_human'] = 'DIR';
			}
			$info['filemtime'] = filemtime($file);

			$info['cols'] = array();
			foreach (self::get_cols_info() as $k => $v) {
				array_push($info['cols'], $this->$v($file));
			}

			if ($extra) {
				$info['fileowner'] = self::get_fileowner($file);
				$info['perms'] = self::get_fileperms($file);
				$info['image_info'] = @getimagesize($file);
				$info['mime'] = self::get_file_mime($file);
			}
			return $info;
		}

		function get_cols_info() {
			if (sizeof(self::$cols_info) <= 0) {
				if (strtolower(substr(php_uname(), 0, 3)) == "win") {
					self::$cols_info = array(
						"perms" => "get_fileperms",
						"modified" => "get_filemtime",
					);
				} else {
					self::$cols_info = array(
						"owner" => "get_fileowner",
						"perms" => "get_fileperms",
						"modified" => "get_filemtime",
					);
				}
			}
			return self::$cols_info;
		}

		function get_file_mime($file) {
			$mime_list = get_resource('mime');
			$mime = "";
			$file_ext_pos = strrpos($file, ".");
			if ($file_ext_pos !== false) {
				$file_ext = trim(substr($file, $file_ext_pos), ".");
				if (preg_match("/([^\s]+)\ .*\b" . $file_ext . "\b.*/i", $mime_list, $res)) {
					$mime = $res[1];
				}
			}
			return $mime;
		}

		public static function clean_path($path) {
			$path = trim($path);
			$path = trim($path, '\\/');
			$path = str_replace(array('../', '..\\'), '', $path);
			if ($path == '..') {
				$path = '';
			}
			return str_replace('\\', '/', $path);
		}

		public static function get_parent_path($path) {
			$path = self::clean_path($path);
			if ($path != '') {
				$array = explode('/', $path);
				if (count($array) > 1) {
					$array = array_slice($array, 0, -1);
					return implode('/', $array);
				}
				return '';
			}
			return false;
		}

		public static function get_fileperms($file) {
			if ($perms = @fileperms($file)) {
				$flag = 'u';
				if (($perms & 0xC000) == 0xC000) {
					$flag = 's';
				} elseif (($perms & 0xA000) == 0xA000) {
					$flag = 'l';
				} elseif (($perms & 0x8000) == 0x8000) {
					$flag = '-';
				} elseif (($perms & 0x6000) == 0x6000) {
					$flag = 'b';
				} elseif (($perms & 0x4000) == 0x4000) {
					$flag = 'd';
				} elseif (($perms & 0x2000) == 0x2000) {
					$flag = 'c';
				} elseif (($perms & 0x1000) == 0x1000) {
					$flag = 'p';
				}

				$flag .= ($perms & 00400) ? 'r' : '-';
				$flag .= ($perms & 00200) ? 'w' : '-';
				$flag .= ($perms & 00100) ? 'x' : '-';
				$flag .= ($perms & 00040) ? 'r' : '-';
				$flag .= ($perms & 00020) ? 'w' : '-';
				$flag .= ($perms & 00010) ? 'x' : '-';
				$flag .= ($perms & 00004) ? 'r' : '-';
				$flag .= ($perms & 00002) ? 'w' : '-';
				$flag .= ($perms & 00001) ? 'x' : '-';
				return $flag;
			} else {
				return "?????????";
			}

		}

		public static function viewPermsColor($f) {
			if (!@is_readable($f)) {
				return '<font color=#FF0000><b>' . self::get_fileperms($f) . '</b></font>';
			} elseif (!@is_writable($f)) {
				return '<font color=white><b>' . self::get_fileperms($f) . '</b></font>';
			} else {
				return '<font color=#FFDB5F><b>' . self::get_fileperms($f) . '</b></font>';
			}

		}

		public static function format_bit($size) {
			$base = log($size) / log(1024);
			$suffixes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			return round(pow(1024, $base - floor($base)), 2) . " " . $suffixes[floor($base)];
		}

		public static function get_filesize($file) {
			$size = @filesize($file);
			if ($size !== false) {
				if ($size <= 0) {
					return 0;
				}

				return self::format_bit($size);
			} else {
				return "???";
			}

		}

		public static function get_filemtime($file) {
			return @date("d-M-Y H:i:s", filemtime($file));
		}

		public static function get_fileowner($file) {
			$owner = "?:?";
			if (function_exists("posix_getpwuid")) {
				$name = posix_getpwuid(fileowner($file));
				$group = posix_getgrgid(filegroup($file));
				$owner = $name['name'] . ":" . $group['name'];
			}
			return $owner;
		}

		public static function extension($path) {
			return pathinfo($path, PATHINFO_EXTENSION);
		}

		public static function size($path) {
			return filesize($path);
		}

		public static function modified($path) {
			return filemtime($path);
		}

		public static function type($path) {
			return filetype($path);
		}

		public static function mkdir($path, $chmod = 0777) {
			return (!is_dir($path)) ? mkdir($path, $chmod, true) : true;
		}

		public static function latest($directory, $skip_dots = true) {
			$latest = null;
			$time = 0;
			$items = new DirectoryIterator($directory);
			foreach ($items as $item) {
				if ($item->isDot() && $skip_dots) {
					continue;
				}
				if ($item->getMTime() > $time) {
					$latest = $item;
					$time = $item->getMTime();
				}
			}
			return $latest;
		}

		public static function append($path, $data) {
			return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
		}

		public static function put($path, $data) {
			return file_put_contents($path, $data, LOCK_EX);
		}

		public static function get($path, $default = null) {
			return (file_exists($path)) ? file_get_contents($path) : value($default);
		}

		public static function delete($path) {
			return (file_exists($path)) ? unlink($path) : false;
		}

		public static function exists($path) {
			return file_exists($path);
		}
	}
}
?>