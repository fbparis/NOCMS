<?php
class NOCMS {
	const pages = '/.pages%s.php';
	const templates = '/.templates/%s.php';
	const cache = '/.cache%s.html';

	public static $lastModified = 0;
	public static $uri = null;
	public static $args = null;
	public static $template = 'default';
	public static $content = '';

	protected static $isGooglebot = null;
	protected static $cachable = true;
	protected static $headers = array();
	protected static $html = '';

	public static function set_template($t) {
		self::$template = $t;
	}
	
	public static function nocache() {
		self::$cachable = false;
	}
	
	public static function file_get_contents($f) {
		$content = @file_get_contents($f);
		if (false !== $content) self::$lastModified = max(self::$lastModified, @filemtime($f));
		return $content;
	}
	
	public static function add_header($name, $content) {
		self::$cachable = false;
		if (!array_key_exists($name, self::$headers)) self::$headers[$name] = $content;
	}
	
	public static function is_googlebot() {
		if (self::$isGooglebot === null) {
			self::$cachable = false;
			$ua = $_SERVER['HTTP_USER_AGENT'];
			$ip = $_SERVER['REMOTE_ADDR'];
		    if (stripos($ua, 'google') === false) self::$isGooglebot = false;
		    else if (!$hostname = @gethostbyaddr($ip)) self::$isGooglebot = true;
		    else if ($hostname == $ip) self::$isGooglebot = true;
		    else self::$isGooglebot = preg_match('#\.google(bot)?\.com$#si', $hostname) ? true : false;
		}
		return self::$isGooglebot;
	}

	protected static function write_cache() {
		$filename = dirname(__FILE__) . sprintf(self::cache, self::$uri);
		@mkdir(dirname($filename), 0777, true);
		@file_put_contents($filename, self::$html);
	}
	
	public static function init($cache=true) {
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
				if ((self::$lastModified !== null) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
					foreach (get_included_files() as $filename) self::$lastModified = max(self::$lastModified, @filemtime($filename));
					header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', self::$lastModified), true);
				}
				self::$html = ob_get_clean();
				if (self::$cachable) self::write_cache();
				else foreach (self::$headers as $k=>$v) header(sprintf('%s: %s', $k, $v), true);
			} else {
				header('X-Robots-Tag: noindex, follow, noarchive', true, 204);
				self::$html = 'Unknown template file';
			}
		} else {
			header('X-Robots-Tag: noindex, follow, noarchive', true, 404);
			if (is_readable(dirname(__FILE__) . sprintf(self::templates, '404'))) {
				ob_start();
				include sprintf(dirname(__FILE__) . self::templates, '404');
				self::$html = ob_get_clean();
				foreach (self::$headers as $k=>$v) header(sprintf('%s: %s', $k, $v), true);
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