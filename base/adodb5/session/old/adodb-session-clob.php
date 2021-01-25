<?php


if (!defined('_ADODB_LAYER')) {
	include (dirname(__FILE__).'/adodb.inc.php');
}

if (!defined('ADODB_SESSION')) {

 define('ADODB_SESSION',1);
 
 
 define('ADODB_SESSION_SYNCH_SECS',60); 


GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_LIFE,
	$ADODB_SESS_DEBUG,
	$ADODB_SESSION_EXPIRE_NOTIFY,
	$ADODB_SESSION_CRC,
	$ADODB_SESSION_USE_LOBS,
	$ADODB_SESSION_TBL;
	
	if (!isset($ADODB_SESSION_USE_LOBS)) $ADODB_SESSION_USE_LOBS = 'CLOB';
	
	$ADODB_SESS_LIFE = ini_get('session.gc_maxlifetime');
	if ($ADODB_SESS_LIFE <= 1) {
	 
	 
	 	$ADODB_SESS_LIFE=1440;
	}
	$ADODB_SESSION_CRC = false;
	
	
	
	
	
	
	if (empty($ADODB_SESSION_DRIVER)) {
		$ADODB_SESSION_DRIVER='mysql';
		$ADODB_SESSION_CONNECT='localhost';
		$ADODB_SESSION_USER ='root';
		$ADODB_SESSION_PWD ='';
		$ADODB_SESSION_DB ='xphplens_2';
	}
	
	if (empty($ADODB_SESSION_EXPIRE_NOTIFY)) {
		$ADODB_SESSION_EXPIRE_NOTIFY = false;
	}
	
	if (empty($ADODB_SESSION_TBL)){
		$ADODB_SESSION_TBL = 'sessions';
	}
	

	
	if (!isset($ADODB_SESSION_USE_LOBS) || empty($ADODB_SESSION_USE_LOBS)) {
		$ADODB_SESSION_USE_LOBS = false;
	}

	
	

function adodb_sess_open($save_path, $session_name,$persist=true) 
{
GLOBAL $ADODB_SESS_CONN;
	if (isset($ADODB_SESS_CONN)) return true;
	
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_DEBUG;
	
	
	$ADODB_SESS_CONN = ADONewConnection($ADODB_SESSION_DRIVER);
	if (!empty($ADODB_SESS_DEBUG)) {
		$ADODB_SESS_CONN->debug = true;
		ADOConnection::outp( " conn=$ADODB_SESSION_CONNECT user=$ADODB_SESSION_USER pwd=$ADODB_SESSION_PWD db=$ADODB_SESSION_DB ");
	}
	if ($persist) $ok = $ADODB_SESS_CONN->PConnect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	else $ok = $ADODB_SESS_CONN->Connect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	
	if (!$ok) ADOConnection::outp( "
-- Session: connection failed</p>",false);
}


function adodb_sess_close() 
{
global $ADODB_SESS_CONN;

	if ($ADODB_SESS_CONN) $ADODB_SESS_CONN->Close();
	return true;
}


function adodb_sess_read($key) 
{
global $ADODB_SESS_CONN,$ADODB_SESSION_TBL,$ADODB_SESSION_CRC;

	$rs = $ADODB_SESS_CONN->Execute("SELECT data FROM $ADODB_SESSION_TBL WHERE sesskey = '$key' AND expiry >= " . time());
	if ($rs) {
		if ($rs->EOF) {
			$v = '';
		} else 
			$v = rawurldecode(reset($rs->fields));
			
		$rs->Close();
		
		
		$ADODB_SESSION_CRC = strlen($v).crc32($v);
		
		return $v;
	}
	
	return ''; 
}


function adodb_sess_write($key, $val) 
{
	global
		$ADODB_SESS_CONN, 
		$ADODB_SESS_LIFE, 
		$ADODB_SESSION_TBL,
		$ADODB_SESS_DEBUG, 
		$ADODB_SESSION_CRC,
		$ADODB_SESSION_EXPIRE_NOTIFY,
		$ADODB_SESSION_DRIVER,			
		$ADODB_SESSION_USE_LOBS;		

	$expiry = time() + $ADODB_SESS_LIFE;
	
	
	
	if ($ADODB_SESSION_CRC !== false && $ADODB_SESSION_CRC == strlen($val).crc32($val)) {
		if ($ADODB_SESS_DEBUG) echo "
-- Session: Only updating date - crc32 not changed</p>";
		$qry = "UPDATE $ADODB_SESSION_TBL SET expiry=$expiry WHERE sesskey='$key' AND expiry >= " . time();
		$rs = $ADODB_SESS_CONN->Execute($qry);	
		return true;
	}
	$val = rawurlencode($val);
	
	$arr = array('sesskey' => $key, 'expiry' => $expiry, 'data' => $val);
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		$var = reset($ADODB_SESSION_EXPIRE_NOTIFY);
		global $$var;
		$arr['expireref'] = $$var;
	}

	
	if ($ADODB_SESSION_USE_LOBS === false) {	
		$rs = $ADODB_SESS_CONN->Replace($ADODB_SESSION_TBL,$arr, 'sesskey',$autoQuote = true);
		if (!$rs) {
			$err = $ADODB_SESS_CONN->ErrorMsg();
		}
	} else {
		
		switch ($ADODB_SESSION_DRIVER) {
			
			case "oracle":
			case "oci8":
			case "oci8po":
			case "oci805":
				$lob_value = sprintf("empty_%s()", strtolower($ADODB_SESSION_USE_LOBS));
				break;

			
			default:
				$lob_value = "null";
				break;
		}

		
		$res = $ADODB_SESS_CONN->Execute("select count(*) as cnt from $ADODB_SESSION_TBL where sesskey = '$key'");
		if ($res && reset($res->fields) > 0) {
			$qry = sprintf("update %s set expiry = %d, data = %s where sesskey = '%s'", $ADODB_SESSION_TBL, $expiry, $lob_value, $key);
		} else {
			
			$qry = sprintf("insert into %s (sesskey, expiry, data) values ('%s', %d, %s)", $ADODB_SESSION_TBL, $key, $expiry, $lob_value);
		}

		$err = "";
		$rs1 = $ADODB_SESS_CONN->Execute($qry);
		if (!$rs1) {
			$err .= $ADODB_SESS_CONN->ErrorMsg()."\n";
		}
		$rs2 = $ADODB_SESS_CONN->UpdateBlob($ADODB_SESSION_TBL, 'data', $val, "sesskey='$key'", strtoupper($ADODB_SESSION_USE_LOBS));
		if (!$rs2) {
			$err .= $ADODB_SESS_CONN->ErrorMsg()."\n";
		}
		$rs = ($rs1 && $rs2) ? true : false;
	}

	if (!$rs) {
		ADOConnection::outp( '
-- Session Replace: '.nl2br($err).'</p>',false);
	}  else {
		
		
		if ($ADODB_SESS_CONN->databaseType == 'access') 
			$rs = $ADODB_SESS_CONN->Execute("select sesskey from $ADODB_SESSION_TBL WHERE sesskey='$key'");
	}
	return !empty($rs);
}

function adodb_sess_destroy($key) 
{
	global $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;
	
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		reset($ADODB_SESSION_EXPIRE_NOTIFY);
		$fn = next($ADODB_SESSION_EXPIRE_NOTIFY);
		$savem = $ADODB_SESS_CONN->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ADODB_SESS_CONN->Execute("SELECT expireref,sesskey FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
		$ADODB_SESS_CONN->SetFetchMode($savem);
		if ($rs) {
			$ADODB_SESS_CONN->BeginTrans();
			while (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				$fn($ref,$key);
				$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$ADODB_SESS_CONN->CommitTrans();
		}
	} else {
		$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE sesskey = '$key'";
		$rs = $ADODB_SESS_CONN->Execute($qry);
	}
	return $rs ? true : false;
}

function adodb_sess_gc($maxlifetime) 
{
	global $ADODB_SESS_DEBUG, $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;
	
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		reset($ADODB_SESSION_EXPIRE_NOTIFY);
		$fn = next($ADODB_SESSION_EXPIRE_NOTIFY);
		$savem = $ADODB_SESS_CONN->SetFetchMode(ADODB_FETCH_NUM);
		$t = time();
		$rs = $ADODB_SESS_CONN->Execute("SELECT expireref,sesskey FROM $ADODB_SESSION_TBL WHERE expiry < $t");
		$ADODB_SESS_CONN->SetFetchMode($savem);
		if ($rs) {
			$ADODB_SESS_CONN->BeginTrans();
			while (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				$fn($ref,$key);
				$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$rs->Close();
			
			
			$ADODB_SESS_CONN->CommitTrans();
			
		}
	} else {
		$ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE expiry < " . time());
	
		if ($ADODB_SESS_DEBUG) ADOConnection::outp("
-- <b>Garbage Collection</b>: $qry</p>");
	}
	
	if (defined('ADODB_SESSION_OPTIMIZE')) {
	global $ADODB_SESSION_DRIVER;
	
		switch( $ADODB_SESSION_DRIVER ) {
			case 'mysql':
			case 'mysqlt':
				$opt_qry = 'OPTIMIZE TABLE '.$ADODB_SESSION_TBL;
				break;
			case 'postgresql':
			case 'postgresql7':
				$opt_qry = 'VACUUM '.$ADODB_SESSION_TBL;	
				break;
		}
		if (!empty($opt_qry)) {
			$ADODB_SESS_CONN->Execute($opt_qry);
		}
	}
	if ($ADODB_SESS_CONN->dataProvider === 'oci8') $sql = 'select  TO_CHAR('.($ADODB_SESS_CONN->sysTimeStamp).', \'RRRR-MM-DD HH24:MI:SS\') from '. $ADODB_SESSION_TBL;
	else $sql = 'select '.$ADODB_SESS_CONN->sysTimeStamp.' from '. $ADODB_SESSION_TBL;
	
	$rs = $ADODB_SESS_CONN->SelectLimit($sql,1);
	if ($rs && !$rs->EOF) {
	
		$dbts = reset($rs->fields);
		$rs->Close();
		$dbt = $ADODB_SESS_CONN->UnixTimeStamp($dbts);
		$t = time();
		if (abs($dbt - $t) >= ADODB_SESSION_SYNCH_SECS) {
			$msg = 
			__FILE__.": Server time for webserver {$_SERVER['HTTP_HOST']} not in synch with database: database=$dbt ($dbts), webserver=$t (diff=".(abs($dbt-$t)/3600)." hrs)";
			error_log($msg);
			if ($ADODB_SESS_DEBUG) ADOConnection::outp("
-- $msg</p>");
		}
	}
	
	return true;
}

session_module_name('user'); 
session_set_save_handler(
	"adodb_sess_open",
	"adodb_sess_close",
	"adodb_sess_read",
	"adodb_sess_write",
	"adodb_sess_destroy",
	"adodb_sess_gc");
}



if (0) {

	session_start();
	session_register('AVAR');
	$_SESSION['AVAR'] += 1;
	ADOConnection::outp( "
-- \$_SESSION['AVAR']={$_SESSION['AVAR']}</p>",false);
}

?>
