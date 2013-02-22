<?php
/* 
 * Quickly regenerate static files.
 * You don't really need it, another way is to regulary empty the .cache directory...
 *
 */
 
set_time_limit(0);

$exclude_file = array();
$exclude_dir = array();

$base = dirname(__FILE__) . '/../www/.pages';
$stack = array($base);

$_SERVER['REQUEST_METHOD'] = 'HEAD';

while (@count($stack)) {
	$d = dir(array_shift($stack));
	$dirname = substr($d->path, strlen($base)) . '/';
	while (false !== ($entry = $d->read())) {
		if (($entry == '.') || ($entry == '..')) continue;
		if (is_dir($d->path . '/' . $entry)) {
			if (in_array($entry, $exclude_dir)) continue;
			$stack[] = $d->path . '/' . $entry;	
		} 
		else if (preg_match('#^(.*)\.php$#s', $entry, $m)) {
			if (in_array($m[1], $exclude_file)) continue;
			$_SERVER['REQUEST_URI'] = $dirname . $m[1];
			if (class_exists('NOCMS')) {
				NOCMS::init();
			} else {
				include_once dirname(__FILE__) . '/../www/index.php';
			}
		}
	}
}

?>