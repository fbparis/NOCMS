<?php
/* 
 * Quickly regenerate static files.
 * You don't really need it, another way is to regulary empty the .cache directory...
 *
 * If you choose to use it, here are a few IMPORTANT advices:
 * - DO NOT USE include_once or require_once in your pages / templates, use include or require instead
 * - In index.php, call the NOCMS::init() like this: NOCMS::init(@$_SERVER['HTTP_HOST'] ? false : true); (so the cache will be generated only by this script)
 * - In your crontab, call this script like this: php-cgi -q PATH/update-cache.php where PATH is the absolute path to update-cache.php
 *
 */
 
set_time_limit(0);

$exclude_file = array();
$exclude_dir = array();

$base = dirname(__FILE__) . '/../www/.pages';
$cache_mask = str_replace('%', '%%', dirname(__FILE__)) . '/../www/.cache%s%s.html';
$stack = array($base);

$_SERVER['REQUEST_METHOD'] = 'HEAD';

while (@count($stack)) {
	$d = dir(array_shift($stack));
	$dirname = substr($d->path, strlen($base)) . '/';
	while (false !== ($entry = $d->read())) {
		if (is_link($entry) || ($entry == '.') || ($entry == '..')) continue;
		if (is_dir($d->path . '/' . $entry)) {
			if (in_array($entry, $exclude_dir)) continue;
			$stack[] = $d->path . '/' . $entry;	
		} else if (preg_match('#^(.*)\.php$#s', $entry, $m)) {
			if (in_array($m[1], $exclude_file)) continue;
			@unlink(sprintf($cache_mask, $dirname, $m[1]));
			$_SERVER['REQUEST_URI'] = $dirname . $m[1];
			if (class_exists('NOCMS')) {
				NOCMS::template('default');
				NOCMS::$lastModified = 0;
				NOCMS::init();
			} else {
				include_once dirname(__FILE__) . '/../www/index.php';
			}
		}
	}
}

?>