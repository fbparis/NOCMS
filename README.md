NOCMS
=====

NOCMS is a minimalist yet powerful system to serve and cache HTML / PHP pages.

It comes with some goodies such as sample robots.txt, sitemap.php, tips to refresh the cache and some example pages but all you need to start is:
* a configuration file for your web server (a working configuration for nginx is provided)
* the provided index.php file in your document root folder
* to create 3 directories in your document root folder: .pages/, .templates/ and .cache/ (.cache/ must be readable and writable by your web server)


How does it work ?
------------------

Using your favorite editor, you can create your template(s) in the .templates folder. Templates files must have a .php extension and you can insert any php and html code you want inside. In the place where you want the content of each specific page to be displayed, add this: <?php NOCMS::the_content(); ?> (or <?php echo NOCMS::$content; ?>, see NOCMS API).

You'll need at least a default template in /.templates/default.php and a template for your 404 page in /.templates/404.php. Off course, the 404 template does not need to display NOCMS::$content.

Once you have at least a default template, for each page you want on your web site you need to create this page in your .pages folder with the exact same name and a php extension. For example, let say you want a page named http://example.com/other/about.html, you'll have to create a file named about.html.php in .pages/other/. If you want a page for your home on http://example.com/ you'll have to create the file .pages/.php, and so on.

The rendering of files in .pages/ will be assigned to NOCMS::$content in your template file. Again, you can insert any php and html you want in these files.

**Important notice:** _this means the file in .pages/ will be interpreted **BEFORE** your template file in .templates/. This is why you can define some custom php variables in your .pages/ files and then retrieve them in your template file._


What about caching ?
--------------------

Each time a page is requested and generated by index.php, the resulting html will be stored in .cache/ folder if:
* the URL has been requested via a GET or a HEAD method and,
* no arguments have been passed through a query string and,
* the matching page and template exist and,
* no call have been done to the NOCMS::nocache() method (see NOCMS API) and,
* no call have been done to the NOCMS::add_header() method (see NOCMS API) and,
* no call have been done to the NOCMS::is() method (see NOCMS API).

Each time a page is requested, the web server will render a static html page instead of index.php if:
* the URL has been requested via a GET or a HEAD method and,
* a matching file in .cache/ folder exists.

**Important notice:** _by default, no maintenance or something is done with the cached files. This means pages are cached permanently. You may want to refresh the cache sometimes, the best way is probably to empty the .cache/ folder (manually or automatically). A script named "update-cache.php" is also provided in the utils/ folder to quickly (re)generate the static files in .cache/._

NOCMS API
---------

NOCMS class provides a few public properties and methods you can use in your templates and / or files.

### In your templates

**property NOCMS::$content**
_string NOCMS::$content_

**method NOCMS::the_content()**
_void NOCMS::the_content()_


### In your pages

**method NOCMS::template()**
_void NOCMS::template(string)_
_string NOCMS::template()_ 

### In your templates or pages

Tips and tricks
---------------


