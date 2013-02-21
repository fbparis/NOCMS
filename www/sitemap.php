<?php 
header('Content-Type: application/xml',true);
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

$hostname = $_SERVER['HTTP_HOST'];

$exclude_file = array();
$exclude_dir = array();

$base = dirname(__FILE__) . '/.pages';
$stack = array($base);
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php 
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
			printf("	<url><loc>http://%s%s</loc></url>\n", $hostname, $dirname . $m[1]);			
		}
	}
}
?>
</urlset> 
