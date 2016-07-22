<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

session_start();

// the default limit if the parameter is not specified
define('DEFAULT_LIMIT', 50);

if ( !isset($_SESSION['data']) ) {
	$buffer = file_get_contents('data.txt');
	$data = array();
	foreach (explode("\n", $buffer) as $line) {
		list($code,$lang) = split("=", $line);
		if (!empty($code)) $data[$code] = trim($lang,"\r");
	}
	$_SESSION['data'] = $data;
} else {
	$data = $_SESSION['data'];
}


header('Content-type: text/plain; charset=utf-8');

$limit = isset($_GET['limit']) && !empty($_GET['limit']) ? $_GET['limit'] : DEFAULT_LIMIT;

if ( isset($_GET['q']) && !empty($_GET['q']) ) {

	$count = 0;
	foreach ($data as $code => $lang) {
		if ( false !== stripos($lang, $_GET['q']) ) {
			echo $code . '=' . trim($lang) . "\n";
			if ( ++$count >= $limit ) break;
		}
	}

} else {

	if ( !isset($_SESSION['default_indexes']) ) {
		$_SESSION['default_indexes'] = array_rand($data, $limit);
	}

	foreach ($_SESSION['default_indexes'] as $code) {
		echo $code . '=' . $data[$code] . "\n";
	}

}
