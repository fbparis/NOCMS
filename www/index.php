<?php
class NOCMS {
	const pages = '/.pages%s.php';
	const templates = '/.templates/%s.php';
	const cache = '/.cache%s.html';

	public static $lastModified = 0;
	public static $content = '';

	protected static $uri = null;
	protected static $args = null;
	protected static $headers = array();
	protected static $template = 'default';
	protected static $is = null;
	protected static $remoteHostname = null;
	protected static $cachable = true;
	protected static $html = '';
	
	public static function uri() {
		return self::$uri;
	}

	public static function args() {
		return self::$args;
	}

	public static function template($t=null) {
		if ($t === null) return self::$template; else self::$template = $t;
	}
	
	public static function the_content() {
		echo self::$content;
	}
	
	public static function add_header($name, $value, $force=false) {
		if ($force || !array_key_exists($name, self::$headers)) {
			self::$headers[$name] = $value;
			self::$cachable = false;
		}
	}
	
	public static function lastModified($t=null) {
		if ($t === null) return self::$lastModified; else self::$lastModified = max($t, self::$lastModified);
	}
	
	public static function nocache() {
		self::$cachable = false;
	}
	
	public static function file_get_contents($f) {
		$content = @file_get_contents($f);
		if (false !== $content) self::lastModified(@filemtime($f));
		return $content;
	}
	
	public static function is($ua_string, $hostname_regex='') {
		self::$cachable = false;
		if (self::$is === null) self::$is = new StdClass;
		if (!property_exists(self::$is, $ua_string)) {
			$ua = $_SERVER['HTTP_USER_AGENT'];
			$ip = $_SERVER['REMOTE_ADDR'];
		    if (stripos($ua, $ua_string) === false) self::$is->$ua_string = false; 
			else {
				if ($hostname_regex && (self::$remoteHostname === null)) self::$remoteHostname = @gethostbyaddr($ip);
			    if (!$hostname_regex || !self::$remoteHostname) self::$is->$ua_string = true;
			    else if (self::$remoteHostname == $ip) self::$is->$ua_string = true;
			    else self::$is->$ua_string = preg_match('#' . str_replace('#', '\#', $hostname_regex) . '#si', self::$remoteHostname) ? true : false;				
		    }
		}
		return self::$is->$ua_string;
	}

	protected static function write_cache() {
		$filename = dirname(__FILE__) . sprintf(self::cache, self::$uri);
		@mkdir(dirname($filename), 0777, true);
		@file_put_contents($filename, self::$html);
		@chmod($filename, 0666);
	}
	
	public static function init($cache=true) {
		header('X-Powered-By: https://github.com/fbparis/NOCMS', true); 
		self::$headers = array();
		list(self::$uri, self::$args) = preg_split('/\?/s', $_SERVER['REQUEST_URI'], 2);
		self::$uri = preg_replace('#/[/.~]+#s', '/', self::$uri);		
		self::$cachable = $cache && !self::$args && in_array($_SERVER['REQUEST_METHOD'], array('GET', 'HEAD'));
		$page = dirname(__FILE__) . sprintf(self::pages, self::$uri);
		if (is_readable($page)) {
			ob_start();
			include $page;
			self::$content = ob_get_clean();
			if (is_readable(dirname(__FILE__) . sprintf(self::templates, self::$template))) {
				ob_start();
				include dirname(__FILE__) . sprintf(self::templates, self::$template);
				foreach (self::$headers as $name=>$value) header("$name: $value");
				if ((self::$lastModified !== null) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
					foreach (get_included_files() as $filename) self::lastModified(@filemtime($filename));
					header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', self::$lastModified), true);
				}
				self::$html = ob_get_clean();
				if (self::$cachable) self::write_cache();
			} else {
				header('X-Robots-Tag: noindex, follow, noarchive', true, 204);
				self::$html = 'Unknown template file';
			}
		} else {
			header('X-Robots-Tag: noindex, follow, noarchive', true, 404);
			if (is_readable(dirname(__FILE__) . sprintf(self::templates, '404'))) {
				ob_start();
				include sprintf(dirname(__FILE__) . self::templates, '404');
				foreach (self::$headers as $name=>$value) header("$name: $value");
				self::$html = ob_get_clean();
			} else {
				self::$html = 'Page not found';
			}
		}
		flush();
		if (in_array($_SERVER['REQUEST_METHOD'], array('GET','POST'))) echo self::$html;
	}	
}

NOCMS::init();
?>