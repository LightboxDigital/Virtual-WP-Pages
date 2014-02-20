#Virtual-WP-Pages#

##### Table of Contents  
- [How to install](#how-to-install)
- [How to use](#how-to-use)
- [Arguments](#arguments)
- [To do list](#to-do-list)
- [Feedback](#feedback)

==========

This is a simple PHP class for WordPress that will allow you to add false/static/virtual pages to your site.

This is especially useful for login pages, carts etc - which require no administration from a back end perspective but are aso required to permanently exist.

This will also handle custom post types, and also handle templates using a 'template' argument.

We have done our best to comment this and make it clear so that modifying it to your needs are easier.

##How to install##

All you need to do is place the 'virtual-wp-pages.php' inside your theme and then include it like:
```php
  require_once( get_template_directory() . '/virtual-wp-pages.php' );
```
##How to use##

Simply use `register_virtual_page( $args );` to utilise this class, it requires an array of arguments passed to it.

Example for creating a static/faux login page:

```php
register_virtual_page(
	array(
		'slug'		=>	'login',
		'title'		=>	'Login to your Account',
		'template'	=>	'templates/login'
	));
```

See 'Arguments' below.

##Arguments##

Only the slug is strictly required, but using only that would make it pointless!

- 'slug', this will be used for the URL, if pretty permalinks are enabled the page/post will exist at 'example.com/slug/'. If pretty permalinks are not enabled the post will exist at 'example.com/?vp=slug'.
- 'title', this is the title used by the page/post.
- 'content', this is the content used by the page/post.
- 'author', set the author of the page/post, this defaults to 1.
- 'date', defaults to current date.
- 'type', this is the post type of the object, this allows you to create virtual anything!
- 'template', allows you to filter the template that is being loaded by the page/post - eg 'templates/login' would load the following file 'wp-content/themes/mytheme/templates/login.php'.

##To do list##

Providing we find time to continually improve this code, we have the following list!

- Look into improving the way it catches urls, and making it more configurable
- Expand it to work with virtual taxonomy terms
- Allow for more of the posts options to be configurable
- Look into compatibility for Yoast SEO and other SEO plugins to optimise these pages
- Create a user interface and work it into a plugin, obviously leaving the ability for a 'lite' code only version.

##Feedback##

We would love to hear feedback on this, many of these classes are available but we were unable to find on GitHub and felt a lot of developers were potentially missing out on these great snippets.

Pull requests would be greatly appreciated for any improvements, and please report any issues.

Feel free to contact the team on developers@lightbox-design.co.uk
