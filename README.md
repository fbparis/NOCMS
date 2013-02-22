NOCMS
=====

NOCMS is a minimalist yet powerful system to serve and cache HTML / PHP pages.

It comes with some goodies such as sample robots.txt, sitemap.php, tips to refresh the cache and some example pages but all you need to start is:
* a configuration file for your web server (a working configuration for nginx is provided)
* the provided index.php file in your document root folder
* to create 3 directories in your document root folder: .pages/, .templates/ and .cache/ (.cache must be readable and writable by your web server)

***

How does it work ?
------------------

Using your favorite editor, you can create your template(s) in the .templates folder. Templates files must have a .php extension and you can insert any php and html code you want inside. In the place where you want the content of each specific page to be displayed, add this: <?php echo NOCMS::$content; ?>.

You'll need at least a default template in /.templates/default.php and a template for your 404 page in /.templates/404.php. Off course, the 404 template does not need to display NOCMS::$content.

Once you have at least a default template, for each page you want on your web site you need to create this page in your .pages folder with the exact same name and a php extension. For example, let say you want a page named http://example.com/other/about.html, you'll have to create a file named about.html.php in .pages/other/. If you want a page for your home on http://example.com/ you'll have to create the file .pages/.php, and so on.

The rendering of files in .pages/ will be assigned to NOCMS::$content in your template file. Again, you can insert any php and html you want in these files.

**Important notice:** _this means the file in .pages/ will be interpreted **BEFORE** your template file in .templates/. This is why you can define some custom php variables in your .pages/ files and then retrieve them in your template file._


