edwrodrig\static_generator
========
A php library to generate static generated sites

[![Latest Stable Version](https://poser.pugx.org/edwrodrig/static_generator/v/stable)](https://packagist.org/packages/edwrodrig/static_generator)
[![Total Downloads](https://poser.pugx.org/edwrodrig/static_generator/downloads)](https://packagist.org/packages/edwrodrig/static_generator)
[![License](https://poser.pugx.org/edwrodrig/static_generator/license)](https://packagist.org/packages/edwrodrig/static_generator)
[![Build Status](https://travis-ci.org/edwrodrig/static_generator.svg?branch=master)](https://travis-ci.org/edwrodrig/static_generator)
[![codecov.io Code Coverage](https://codecov.io/gh/edwrodrig/static_generator/branch/master/graph/badge.svg)](https://codecov.io/github/edwrodrig/static_generator?branch=master)
[![Code Climate](https://codeclimate.com/github/edwrodrig/static_generator/badges/gpa.svg)](https://codeclimate.com/github/edwrodrig/static_generator)

## Motivational rant

I am some sort of full stack engineer that have to handle a lot of websites. My area of expertise is back-end so I want to deal the least with front-end technologies.
The easiest solutions are implemented in some popular systems like [Wordpress](https://wordpress.com), [Drupal](https://www.drupal.org) or other [LAMP](https://en.wikipedia.org/wiki/LAMP) based systems.
These are ok for most cases but have a problem. Generally, these sites are requirements of some optimistic clients that want to create the best blog of the history, full of notices, interactions, good images, etc.
Unfortunately, these expectations always are truncated by reality.
This dreamed site ends with an outdated incomplete blog with at most 3 posts that none wants to read.
Also, users do not remember their password so you become part of the login system recovering password every time that these users want to add something.
And sysadmins have to deal with insecure, configuration sensitive systems that make difficult to do updates of their current infrastructure machines.
This is pure nonsense, It is so full of BS that it is doesn't even funny.

New language websites technologies like [nodejs](https://nodejs.org/), [django](https://www.djangoproject.com), [rails](https://rubyonrails.org/) do not improve the situation, just make it worst.
Now instead to deal with [PHP](http://www.php.net), you have to deal with a shitload of new crappy languages.
I don't want to install new interpreters or compilers in my machines. It is the opposite, I want the less. It is just painful to deal with OS configurations alone to also deal with the configuration of particular apps.
If you want to deal with tools then that is for development machines, not production.

I figure out the best approach is to don't contaminate my pristine machines is the [static web page](https://en.wikipedia.org/wiki/Static_web_page).
In this approach, you only need to copy the static site files (HTML, CSS, JS, images) to some folder pointed by an [HTTP server](https://en.wikipedia.org/wiki/Web_server)
and that is all, very easy and clean.

I discovered that system to aid static page generators are very popular, and there are a lot of options like [jekyll](https://jekyllrb.com/), [sculpin](https://sculpin.io/), and [metalsmith](http://www.metalsmith.io/).
I discovered that event [github pages](https://pages.github.com/) integrates [jekyll](https://jekyllrb.com/) which is very convenient, so I started to use it.
With a little time, I figured out the convenience of the static site approach, but at the same time how wrong the actual implementations are.

I found that they are focusing in easy blogging and concise notation, and that can be resumed in the usage of some trendy data file formats like [markdown](https://en.wikipedia.org/wiki/Markdown), [yaml](https://en.wikipedia.org/wiki/YAML), and some [templating file format](http://shopify.github.io/liquid/).
Ok, they are concise and may be convenient, but in my honest opinion, web development has enough technologies to create new ones just for convenience.
You have to deal with [HTML](https://en.wikipedia.org/wiki/HTML), [CSS](https://en.wikipedia.org/wiki/Cascading_Style_Sheets), [Javascript](https://en.wikipedia.org/wiki/JavaScript) and if you also do some backend stuff, then add a server-side language and [SQL](https://en.wikipedia.org/wiki/SQL). We don't need more languages, we need less.
Write HTML [header tags](https://www.w3schools.com/tags/tag_hn.asp) instead of [underlining with #](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet#headers) is not a big deal that will change your life.
Also because of the usage of these convenient languages, they try to reinvent the wheel in their own form ([conditionals](http://shopify.github.io/liquid/tags/control-flow/) and [loops](http://shopify.github.io/liquid/tags/iteration/)), and in some way in a painful form ([functions](http://hamishwillee.github.io/2014/11/13/jekyll-includes-are-functions)). Please stop this nonsense.

I want to generate pages that are not tied to these new dialects. Just with a pair of concepts, configuration stuff plus plain vanilla PHP and that's all to start using your pages.
I build this for me so I don't focus on a totally ignorant basic user, I am a software engineering that has experience in programming, OOP, and design patterns. I'm not going to cry is I have to create functions, subclass objects or put semicolons after every statement.

So that. More simple, less BS.

### Why use a static generator instead of write the pages yourself?

Front-end it's just declarative. The only exception is javascript but it is not a decent ways to doing any serious programming, because the language is bad and the browser incompatibility. So less javascript is the best.
But there is a lot of work that can be automated, or encapsulated in functions. For example if you have a blog, every post page share the same header and footer, so it's very convenient to encapsulate the header and the footer in functions so you can print this in the following way:
```
<?php header() ?>
<div>
  <h1>My post</h1>
  <p>My content</p>
</div>
<?php footer() ?>
```
This is very natural for a old school php programmer.
The static generator allows you to write this file easier.
Generally you have some kind of sources in a folder structured that match and output file, then you traverse the folder and generate every page.
Something like this:
```
foreach ( $files as $file ) {
  ob_start();
  include $file;
  $content = ob_get_clean();
  file_put_contents('output/path/suitable/for/copy/to/httpd/' . $file, $content);
}
```
This is what a static generator does but handling with border cases and easily creating addons. It's not a very complicated system.

### What if I need something dynamic?

When you do a static page you can't directly put a dynamic content. It's true that dynamic content is common, like contact forms and user logins.
The best approach is to create backend web services that the static site can call using [AJAX](https://en.wikipedia.org/wiki/Ajax_(programming)).
This is a very convenient way to deal and in my opinion is the best way because:
 * Decouples the server logic with the page.
 * You can code the backend in a different language than the front-end.
 * Services are more manageable than a monolithic system.
 * Services are compatible with different deploy targets like mobile and desktop apps.

 
## My use cases

 * Creating different version of a site by language without pain.
 * Some facilities to see a page globally (you may have a lot of post pages, but you also need to create some index page so you need to get all the post pages).
 * Add metadata to pages in a not invasive way.
 * Easy to migrate a plain static html page to this system.
 * Some css and javascript [minify facilities](https://github.com/matthiasmullie/minify).
 * Easily create page templates in pure PHP.
 * Optimize and cache some assets like images and large file.
 * I want to maintain the things as [simple as possible](https://en.wikipedia.org/wiki/KISS_principle)  

My infrastructure is targeted to __Ubuntu 16.04__ machines with last __php7.2__ installed from [ppa:ondrej/php](https://launchpad.net/~ondrej/+archive/ubuntu/php).
I use some unix commands for some process like __cp__ or __ln__.
I'm sure that there are way to make it compatible with windows but I don't have time to program it and testing,
but I'm open for pull requests to make it more compatible.

## Documentation
The source code is documented using [phpDocumentor](http://docs.phpdoc.org/references/phpdoc/basic-syntax.html) style,
so it should pop up nicely if you're using IDEs like [PhpStorm](https://www.jetbrains.com/phpstorm) or similar.

### Examples

There is a [example page](https://github.com/edwrodrig/static_generator/tree/master/examples) using features of the generator. I have to add some other more clear cases.

My [personal page](https://www.edwin.cl) is a good example of the use of my library. The [source code](https://github.com/edwrodrig/edwin_site) is public.

The following pages are built by previous version of this generator, they are in process of migration:
* [Millenium Institute of Oceanography](http://en.imo-chile.cl/)
* [Amanda Morales Site](http://www.amandamorales.cl)
* [Aprende Site](http://www.a-prendechile.cl)
    

## Composer
```
composer require edwrodrig/static_generator
```

## Testing
The test are built using PhpUnit. It generates images and compare the signature with expected ones. Maybe some test fails due metadata of some generated images, but at the moment I haven't any reported issue.

## License
MIT license. Use it as you want at your own risk.

## About language
I'm not a native english writer, so there may be a lot of grammar and orthographical errors on text, I'm just trying my best. But feel free to correct my language, any contribution is welcome and for me they are a learning instance.

