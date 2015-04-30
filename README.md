# Silex-Cops

## Description

Silex-Cops is a web based Ebook sharing (and later full management) software.

It's a port and enhancement of COPS (Calibre OPDS PHP Server) on the Silex micro framework.

## Why ?

Because like many people i use Calibre as library manager, but the html export doesn't suit my needs.

Because like many other people i took a look at Cops (https://github.com/seblucas/cops) but the way it's developped doesn't allow me to make the modifications i want beside the fact that i'm using PHP for now twelve years.

I also wanted to play with Silex micro-framework :D

## What's new with this ?

The main goal is to provide a nicely designed software with good performances that can be hosted either on a small machine like a NAS at home or a real web hosting server.
That's why i decided to create some kind of Cops but based on OOP good practices and relying on **Silex** (http://silex.sensiolabs.org), Twig template engine (http://twig.sensiolabs.org) and the **Symfony 2 components**.

Anyone with basic Silex / SF2 knowledge is able to modify, enhance and play with Silex-Cops.

Anyone comfortable with Twig can create a theme or modify the default one.

## Available features

* Facet browsing (by author / serie / tag)
* Full serie / author / tag archive download (zip / targz)
* Edit your books information while browsing
* Basic user & permissions management

## Requirements

First you will need a Calibre database

Silex-Cops works with PHP **version >= 5.3**

*Please note that it is not intended to work on any Windows version, try at your own risks*

The following PHP modules are required :
* **imagick** or **gd**
* **DOM** for the opds feed

**apc** (opcode and data cache) is not required but **highly recommended**

## Installation

The application is still under heavy development tough the book browsing feature is working well.

### 1. To install, first clone the repository :

    git clone git@github.com:mduplouy/silex-cops.git

### 2. Get composer

    $ curl -s https://getcomposer.org/installer | php

### 3. Install the dependencies using Composer :

    $ cd silex-cops
    $ php ../composer.phar install

### 4. Configure the app

* Give apache access to your Calibre library database change accordingly the **app/src/config.ini** file (you can also create a symlink)
* Check apache user has write permissions on the following folders :
    * /cache (create it if needed)
    * /web/assets/

Addtionnal configuration for synology users :
* Remove open_basedir stuff
* Create symlink for assets and /web/index.php if you use virtual host or don't want the app into a subdir


    $ cd /wherever-you-put-it/silex-cops
    $ ln -s web/assets ./assets
    $ ln -s web/index.php ./index.php

Do not forget to deny access to all files but the index.php and assets !

### 5. Configure HTTP authentication

By default the public part is protected by simple HTTP auth.

There is one builtin account
 - admin / password : This user has admin privileges and can connect to the backoffice (url is (fr|en)/admin/)

Note : You can change default password in config file and reinit database (see below)

Logins can be changed in the back office under "User management" section

### 6. Configure your web server

Your web server must point to the web/ directory of the app (DocumentRoot directive with Apache HTTP server)

## Available commands

If you don't want your visitor to wait for thumbnails to be generated, do it using cli by running following command :

    $ php app/console.php generate:thumbnails

Reset user account with the following command :

    $ php app/console.php database:init

## License

License for this is **Do What The Fuck You Want To Public License**, (http://www.wtfpl.net/about/) guess you don't need more explanations ;)

## Troubleshooting & feature request

Open an issue on github ;)


## Badges

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/mduplouy/silex-cops/badges/quality-score.png?s=5a85f0b8dc7ebe1900b2064bf5d7fa3acc320b3a)](https://scrutinizer-ci.com/g/mduplouy/silex-cops/)

[![Code Coverage](https://scrutinizer-ci.com/g/mduplouy/silex-cops/badges/coverage.png?s=7f1e330f0fe400db7beacece040df1ea36e7ce2e)](https://scrutinizer-ci.com/g/mduplouy/silex-cops/)

[![Build Status](https://travis-ci.org/mduplouy/silex-cops.png?branch=master)](https://travis-ci.org/mduplouy/silex-cops)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f143beee-52c5-4ada-9162-1f707650b9d4/mini.png)](https://insight.sensiolabs.com/projects/f143beee-52c5-4ada-9162-1f707650b9d4)
