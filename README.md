Codeigniter Matches CLI
======================

Codeigniter Matches is a PHP CLI script that allows you to write controllers, models and views faster.

#Usage

To verify if you have php cli installed, type into terminal/command prompt.
```php
php -v
```
If you receive the php version number, then you are good to go.

Go to your *config.php* file inside the *application/config* and make sure the following line looks like below:
```php
$config['uri_protocol'] = 'AUTO';
```
After that line, add:
```php
$config['uri_protocol'] = isset($_SERVER['REQUEST_URI']) ? 'PATH_INFO' : 'CLI';
```

Save the file...

Put *Matches.php* (or *matches.php* if you have a lesser than v.3 Codeigniter) inside the controllers directory. Put *config/matches.php* inside your app config folder. Also, put *matches_templates* folder inside the views folder.
From terminal or command prompt go to the application's index.php and type:
```php
php index.php matches
```

If everything went well, you should be greeted by Matches.

#Commands

You can start using Matches by writing:
```php
php index.php matches [COMMAND]
```

...where [COMMAND] can be:

##help

displays all commands available

##create:...

###create:app name_of_app###
Creates a MVC stack (controller, model, view) having name_of_app as names.

###create:controller name_of_controller
Creates a controller having name_of_controller as name. You can put the controller inside a directory. Directories are delimited with ".". So, if you want to create the controller inside controllers/admin, you can do create controller admin.name_of_controller

###create:migration name_of_migration t:name_of_table(OPTIONAL)
Creates a migration having name_of_migration prefixed with version as file name. If no name_of_table is given, will name the table as the name_of_migration.

###create:model name_of_model 
Creates a model having name_of_model as name. You can put the model inside a directory. Directories are delimited with ".". So, if you want to create the model inside models/admin, you can do create model admin.name_of_model

###create:view name_of_view
Creates a view having name_of_view as file name.. You can put the view inside a directory. Directories are delimited with ".". So, if you want to create the view inside views/admin, you can do create view admin.name_of_controller

##encryption_key

encryption_key string_to_hash-(OPTIONAL) - creates an encryption key inside all config.php's found in config folder. If $config['encryption_key'] = ''; doesn't exist or has a value, the encryption key won't be written.


#Copyright

Copyright (C) 2014 @avenirer [avenir.ro@gmail.com]
Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long as the name is changed.

DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

Do whatever you like with the original work, just don't be a dick.
Being a dick includes - but is not limited to - the following instances:
1a. Outright copyright infringement - Don't just copy this and change the name.
1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.
If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.
Code is provided with no warranty. 
Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick. 
Fix the problem yourself. A non-dick would submit the fix back.
License info: http://www.dbad-license.org/
