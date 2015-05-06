This project was forked from: 
==============================
php-client for [telegram-cli](https://github.com/vysheng/tg/)

As I wanted to add more features and create an (optional) laravel wrapper for the project but couldn't get any of my PRs accepted.

Requirements
------------
 - a running [telegram-cli](https://github.com/vysheng/tg/) listening on a unix-socket (`-S`) or a port (`-P`). Needs to be configured already (phone-number, etc.).
 - php >= 5.4.0
 - curl installed

Usage
-----

###Setup telegram-cli
[telegram-cli](https://github.com/vysheng/tg/) needs to run on a unix-socket (`-S`) or a port (`-P`), so *telegram-cli-client* can connect to it.  
You should also start it with `-W` so the contact-list gets loaded on startup.  
For this example we will take the following command (execute it from the dir, where you installed telegram-cli, not the php-client), `-d` lets it run as daemon.:

```shell
./bin/telegram-cli -k ./tg-server.pub -dWS /tmp/tg.sck &
```

If you never started telegram-cli before, you need to start it first in normal mode, so you can type in your telegram-phone-number and register it, if needed (`./bin/telegram-cli`).

To stop the daemon use `killall telegram-cli` or `kill -TERM [telegram-pid]`.

###Install telegram-cli-client with composer
In your project-root:

```shell
composer require williamson/laragram:dev-master
```

Composer will then automatically add the package to your project requirements and install it (also creates the `composer.json` if you don't have one already).

###Using it


###With Laravel
Open your ```config/app.php``` file and add
```php
        'Williamson\Laragram\LaragramServiceProvider'
```
to the list of service providers

further on in the file add

```php
'TG'          => 'Williamson\Laragram\Facades\Laragram'
```

to the list of Aliases.

You're done!


Now anywhere in your app you can send a telegram message like follows:

```php
 Route::get('/test', function (){
     TG::sendMsg('65342634', 'Hello there!');
 });
```


Of course there are many more commands than just sendMsg. [TBC]



####Non Laravel usage
```php
require('vendor/autoload.php');
$telegram = new \Williamson\Laragram\TgCommands('unix:///tmp/tg.sck');

$contactList = $telegram->getContactList();
$telegram->sendMsg($contactList[0], 'Hey man, what\'s up? :D');
```


[Documentation to be fleshed out at a later date]

License
-------
This software is licensed under the [Mozilla Public License v. 2.0](http://mozilla.org/MPL/2.0/). For more information, read the file `LICENSE`.