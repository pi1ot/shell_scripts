<?php
require_once 'meekrodb.2.2.class.php';

function convert_tables( $db, $rules, $table_name ) {
	$mysql = new MeekroDB( $db['host'], $db['user'], $db['pass'], $db['db'], $db['port'], $db['enc'] );

	$tables = array();
	if ( $table_name != '' ) {
		$tables = array( $table_name );
	} else {
		$tables = $mysql->tableList();
	}
	
	foreach ( $tables as $t ) {
		if ( $rules[$t] == NULL ) {
			continue;	// no convert rule
		}
		$tablename	= $rules[$t]['table'];	// new table name
		$convertor	= $rules[$t]['func'];	// convert function
		
		$rows = $mysql->query( "SELECT * FROM %b", $t );
		foreach ( $rows as $r ) {
			$newrow = $convertor( $mysql, $r );	// exec convert
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
	mysql> desc zc_app_version;
	+--------------+--------------+------+-----+---------+----------------+
	| Field        | Type         | Null | Key | Default | Extra          |
	+--------------+--------------+------+-----+---------+----------------+
	| id           | int(11)      | NO   | PRI | NULL    | auto_increment |
	| type         | varchar(10)  | NO   | MUL | NULL    |                |
	| version      | varchar(10)  | NO   |     | NULL    |                |
	| device       | varchar(10)  | NO   |     | NULL    |                |
	| download_url | varchar(255) | NO   |     | NULL    |                |
	| is_publish   | int(11)      | NO   |     | NULL    |                |
	| remark       | text         | NO   |     | NULL    |                |
	| create_time  | datetime     | NO   |     | NULL    |                |
	+--------------+--------------+------+-----+---------+----------------+
	mysql> desc common_app_versions;
	+--------------+------------------+------+-----+---------------------+----------------+
	| Field        | Type             | Null | Key | Default             | Extra          |
	+--------------+------------------+------+-----+---------------------+----------------+
	| id           | int(10) unsigned | NO   | PRI | NULL                | auto_increment |
	| download_url | varchar(255)     | NO   |     | NULL                |                |
	| is_focus     | int(11)          | NO   |     | 1                   |                |
	| content      | varchar(255)     | NO   |     | NULL                |                |
	| is_publish   | int(11)          | NO   |     | 2                   |                |
	| version      | varchar(20)      | NO   |     | NULL                |                |
	| app_type     | int(11)          | NO   |     | NULL                |                |
	| created_by   | int(11)          | NO   |     | NULL                |                |
	| updated_by   | int(11)          | NO   |     | NULL                |                |
	| created_at   | timestamp        | NO   |     | 0000-00-00 00:00:00 |                |
	| updated_at   | timestamp        | NO   |     | 0000-00-00 00:00:00 |                |
	+--------------+------------------+------+-----+---------------------+----------------+	
	*/	

	'zc_app_version' =>	array(
		'table'	=>	'common_app_versions',
		'func'	=>	$func = function($mysql,$f) {
			$r = array();
			$r['id']		= $f['id'];
			$r['download_url']	= $f['download_url'];
			$r['is_focus']		= '2';
			$r['content']		= $f['remark'];
			$r['is_publish']	= $f['is_publish'];
			$r['version']		= $f['version'];
			if ( $f['type']=='driver' ) {
				$r['app_type'] = '2';
			} else if ( $f['type']=='passenger' && $f['device']=='android' ) {
				$r['app_type'] = '1';
			} else {
				$r['app_type'] = '3';
			}
			$r['created_by']	= '0';
			$r['updated_by']	= '0';
			$r['created_at']	= $f['create_time'];
			return $r;
		},
	),
);

$table_name = '';
if ( $argc >= 2 ) {
	$table_name = $argv[1];
}
convert_tables( $db, $rules, $table_name );

?>
