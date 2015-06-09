Laravel Wrapper for telegram-cli 
==============================

This project was originally forked from: [php-telegram-cli-client](https://github.com/zyberspace/php-telegram-cli-client) and credit to [zyberspace](https://github.com/zyberspace) for the start I needed to get this project working.


What is it?
------------
This project allows you to use the lovely Laravel syntax you are familar with to quickly and easily send messages / images / documents / audio files / location via [Telegram Messenger](https://telegram.org) with the aid of [telegram-cli](https://github.com/vysheng/tg/) running on your server.

It allows you do things like:

```php
	TG::sendMessage('User_name', 'Hi there your account has been set up! Thanks!');
```  

or

```php
	TG::sendDocument('User_name', '/home/data/files/important.pdf');
```

or

```php
	TG::sendImage('User_name', 'http://upload.wikimedia.org/wikipedia/commons/1/16/HDRI_Sample_Scene_Balls_(JPEG-HDR).jpg');
```  


Of course if you don't like using Facades you can always revert to

```php
	// $tg is an instantiated TgCommands Object. Laravel will create this out of the IOC container for
	// you if typehinted in constructor etc. 
	$tg->sendMessage('User_name', 'Hi there your account has been set up! Thanks!');
```


Requirements
------------
 - A **running** [telegram-cli](https://github.com/vysheng/tg/) listening on a unix-socket (`-S`) or a port (`-P`). Needs to be configured already (phone-number, etc.).
 - php >= 5.4.0
 - curl installed
 - json flag added to the telegram daemon startup command

Installing Telegram-cli
-----------------------

### Setup telegram-cli ###
[Telegram-cli](https://github.com/vysheng/tg/) needs to run on a unix-socket (`-S`) or a port (`-P`), so *laragram* can connect to it. Please read the instructions at [telegram-cli](https://github.com/vysheng/tg/) on how to `configure` and then `make`

If your build was successful you can continue!


### First time running telegram-cli ###
If you never started telegram-cli before, you need to start it first in normal mode, so you can type in your telegram-phone-number and register it.

Assuming you installed telegram in `~/telegram` your command should look like this:
`~/telegram/bin/telegram-cli`.

Once registered, you can exit telegram-cli (`safe_quit`) and launch it as a daemon. 
  
### Running telegram-cli as a daemon ###
To run telegram-cli as a daemon you need to use the `-d` flag and set a unix socket `-S`. 

```shell
~/telegram/bin/telegram-cli -k ~/telegram/tg-server.pub -dWS /tmp/tg.sck --json
```
Or instead you may use a TCP port
```
~/telegram/bin/telegram-cli -k ~/telegram/tg-server.pub -dWP 7777 --json
```

The `-W` switch means the contact-list gets loaded on startup - this allows you to send messages straight away. The `&` at the ends means that the command will load in the background allowing you to continue with the script.

To stop the daemon use `killall telegram-cli` or `kill -TERM [telegram-pid]`.

You now have [Telegram-cli](https://github.com/vysheng/tg/) working and waiting to be told what to send! 


To ensure your daemon does not exit unexpectantly, you can use something like supervisor to make sure the process always is running. See notes at the end of how this can be set up.



Installing Laragram
-----

### Install Laragram with composer ###
In your laravel project-root:

```shell
composer require williamson/laragram:~0.1
```

### Service Providers and Facades ###
Open `config/app.php` file, find the providers array and add to the bottom

```php
        'Williamson\Laragram\Laravel\LaragramServiceProvider'
```

NEW! If you're running Laravel 5.1 or greater you can use this syntax instead in the `config/app.php` file:

```php
        Williamson\Laragram\Laravel\LaragramServiceProvider::class
```
Note the lack of single quotes for the laravel 5 version.

The Facade alias of `TG` is automatcially registered for you in the serviceprovider boot method.

Open `config/services.php` file, add the following array to specifiy what type of socket you would like to connect to the
telegram daemon.

```php
    'telegram' => [
        'socket' =>'tcp://127.0.0.1:7778'
    ]
```
Other values include unix sockets. ie `'socket' => 'unix:///tmp/tg.sck'`

You're done!


Using Laragram
---------------

Now anywhere in your app you can send a telegram message quickly and easily like follows:

```php
   // routes.php

     Route::get('/test', function (){
          TG::sendMsg('<name or telegram id number>', 'Hello there!');
     });
```

Remember that the name should have underscores instead of spaces eg `firstname_lastname`, OR you can use `user#`and then the persons telegram ID directly.  This method is far more reliable. eg `user#123456789`


In addition to sendMessage, the following commands are available to you:
```
broadcastMsg
chatAddUser
chatCreateGroup
chatCreateSecret
chatDelUser
chatExportLink
chatInfo
chatRename
chatSetPhoto
contactAdd
contactDelete
contactList
contactRename
deleteMsg
exportCard
getContactList
getDialogList
getHistory
getUserInfo
markRead
msg
sendAudio
sendContact
sendDocument
sendLocation
sendMsg
sendPhoto
sendText
sendTypingStart
sendTypingStop
sendVideo
setProfileName
setProfilePhoto
setStatusOffline
setStatusOnline
setUsername
```

License
-------
This software is licensed under the [Mozilla Public License v. 2.0](http://mozilla.org/MPL/2.0/). For more information, read the file `LICENSE`.




Using Supervisor
-------

Assuming that you have 

- supervisord installed on your system (if not [supervisord install](http://supervisord.org/installing.html) ) and 
- telegram has been installed in `/home/username/telegram`

Then create a new file `/etc/supervisor/conf.d/telegram.conf`

and copy the following into it, adding/replacing any log files you wish to create
```shell
[program:telegram]
command=/bin/bash -c "rm -f /tmp/tg.sck && /home/username/telegram/bin/telegram-cli -k /home/username/telegram/tg-server.pub -dWS /tmp/tg.sck --json"
;For tcp socket this could be
;command=/bin/bash -c "/home/username/telegram/bin/telegram-cli -k /home/username/telegram/tg-server.pub -dWP 7777 --json"
directory=/home/username/telegram
redirect_stderr=true
stopsignal=KILL
stopasgroup=true
autostart=true
autorestart=true
startretries=3
user=<insert linux username>
stdout_logfile=<insert your own log folder and log filename. Must exist>
stderr_logfile=<insert your own log folder and log filename. Must exist>
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=10
stdout_capture_maxbytes=1MB
```

Save the file and restart supervisor (make sure that telegram-cli is NOT running before you do this, supervisor will now take care of insuring that the process always runs even if it crashes).

```shell
sudo service supervisor restart
```

You can view how the process is doing by using the monitoring program ```sudo supervisorctl```