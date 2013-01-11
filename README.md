# xyCMS

This is a simple PHP CMS based on [Twitters Bootstrap](http://twitter.github.com/bootstrap/), also using a [Javascript Syntax Highlighter](http://shjs.sourceforge.net) and the [reCAPTCHA PHP Library](http://code.google.com/p/recaptcha/downloads/list?q=label:phplib-Latest).
In addition, iOS Users will be redirected to a special version of the website, developed using [iWebKit](http://snippetspace.com/portfolio/iwebkit/).
It uses the same Database layout as its predecessor [xythobuzCMS](https://github.com/xythobuz/xythobuzCMS). The admin interface and the iOS version are taken from xythobuzCMS, only the Desktop Version was rewritten using Bootstrap.

## Quick Setup

Copy all the files to your webserver. Run Setup.php to create the SQL Tables. Delete the config file created by setup.php, replace it with your modified example config.

This should have prepared everything. The admin interface is reachable via admin.php.
