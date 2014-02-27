<?php
require_once 'meekrodb.2.2.class.php';

function convert_tables( $db, $rules ) {
	$mysql = new MeekroDB( $db['host'], $db['user'], $db['pass'], $db['db'], $db['port'], $db['enc'] );
	$tables = $mysql->tableList();
	
	foreach ( $tables as $t ) {
		if ( $rules[$t] == NULL ) {
			continue;	// no convert rule
		}
		$tablename	= $rules[$t]['table'];	// new table name
		$convertor	= $rules[$t]['func'];	// convert function
		
		$rows = $mysql->query( "SELECT * FROM %b", $t );
		foreach ( $rows as $r ) {
			$newrow = $convertor( $r );	// exec convert
			$values = '';
			$keys 	= array_keys( $newrow );
			foreach( $keys as $k ) {
				$values .= ' '.$k."='".mysql_escape_string($newrow[$k])."',";
			}
			if ( strlen($values) > 0 ) {
				$values = substr( $values, 0, -1 );
			}
			$sql = 'INSERT INTO '.$tablename.' SET'.$values.';';
			echo $sql."\n";
		}
	}
}

// example rule
$db = array(
	'host'	=> '127.0.0.1',
	'port'	=> '3306',
	'enc'	=> 'utf8',
	'user'	=> 'root',
	'pass'	=> '',
	'db'	=> 'zhaoche',
);

$rules = array(

	/* rule example
	'old_table_name' =>	array(
		'table'	=>	'new_table_name',
		'func'	=>	function($f) {
			// $f: old_table_row
			// return new_table_row
		};
	),
	*/

	/*
	SELECT COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT,EXTRA,COLUMN_COMMENT FROM information_schema.columns WHERE table_name = 'zc_app_version';
	+--------------+--------------+-------------+----------------+----------------+---------------------------+
	| COLUMN_NAME  | COLUMN_TYPE  | IS_NULLABLE | COLUMN_DEFAULT | EXTRA          | COLUMN_COMMENT            |
	+--------------+--------------+-------------+----------------+----------------+---------------------------+
	| id           | int(11)      | NO          | NULL           | auto_increment | 自增ID                    |
	| type         | varchar(10)  | NO          | NULL           |                | app类型：乘客/司机        |
	| version      | varchar(10)  | NO          | NULL           |                | 版本号                    |
	| device       | varchar(10)  | NO          | NULL           |                | 设备：ios/android         |
	| download_url | varchar(255) | NO          | NULL           |                | 下载地址                  |
	| is_publish   | int(11)      | NO          | NULL           |                | 发布标记（唯一）          |
	| remark       | text         | NO          | NULL           |                | 更新说明                  |
	| create_time  | datetime     | NO          | NULL           |                | 创建时间                  |
	+--------------+--------------+-------------+----------------+----------------+---------------------------+
	SELECT COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT,EXTRA,COLUMN_COMMENT FROM information_schema.columns WHERE table_name = 'common_app_versions';
	+--------------+------------------+-------------+---------------------+----------------+-------------------------------------------------------------------------------------+
	| COLUMN_NAME  | COLUMN_TYPE      | IS_NULLABLE | COLUMN_DEFAULT      | EXTRA          | COLUMN_COMMENT                                                                      |
	+--------------+------------------+-------------+---------------------+----------------+-------------------------------------------------------------------------------------+
	| id           | int(10) unsigned | NO          | NULL                | auto_increment | 主建ID                                                                              |
	| download_url | varchar(255)     | NO          | NULL                |                | 下载的URL                                                                           |
	| is_focus     | int(11)          | NO          | 1                   |                | 是否强制更新，1为强制更新，2为不用                                                  |
	| content      | varchar(255)     | NO          | NULL                |                | 更新的内容,用\n分割                                                                  |
	| is_publish   | int(11)          | NO          | 2                   |                | 是否对法发布,1为发布，2为不发布                                                     |
	| version      | varchar(20)      | NO          | NULL                |                | 版本号码                                                                            |
	| app_type     | int(11)          | NO          | NULL                |                | APP的类型，1为android客户端，2为android的司机端，3为ios的客户端                     |
	| created_by   | int(11)          | NO          | NULL                |                | 条目的创建人                                                                        |
	| updated_by   | int(11)          | NO          | NULL                |                | 条目的更新人                                                                        |
	| created_at   | timestamp        | NO          | 0000-00-00 00:00:00 |                |                                                                                     |
	| updated_at   | timestamp        | NO          | 0000-00-00 00:00:00 |                |                                                                                     |
	+--------------+------------------+-------------+---------------------+----------------+-------------------------------------------------------------------------------------+
	*/

	'zc_app_version' =>	array(
		'table'	=>	'common_app_versions',
		'func'	=>	function($f) {
			$r = array();
			$r['version']		= $f['version'];
			$r['download_url']	= $f['download_url'];
			$r['is_publish']	= $f['is_publish'];
			$r['content']		= $f['remark'];
			$r['is_focus']		= '2';
			$r['created_by']	= '0';
			$r['updated_by']	= '0';
			if ( $f['type']=='driver' ) {
				$r['app_type'] = '2';
			} else if ( $f['type']=='passenger' && $f['device']=='android' ) {
				$r['app_type'] = '1';
			} else {
				$r['app_type'] = '3';
			}
			return $r;
		},
	),

	// more rules...
);

convert_tables( $db, $rules );

?>
