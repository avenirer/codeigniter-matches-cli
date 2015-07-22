Codeigniter Matches CLI
======================

Codeigniter Matches is a PHP CLI script that allows you to write controllers, models and views faster.

#Setup

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

#Usage

##Application

To create MVC stack (controller, model, view) you can use create:app.

Usage example
```php
// Create an MVC stack
php index.php matches create:app users
```

##Controllers

###create:controller name_of_controller

You can use Matches to create a Controller file. The command will need at leas a parameter which represents the name of the controller.

You can put the controller inside a directory. Directories are delimited with ".". So, if you want to create the controller inside controllers/admin, you can do create:controller admin.name_of_controller.

Usage examples
```php
// Create a Welcome controller that extends MY_Controller
php index.php matches create:controller welcome e=my

//Create a User controller inside admin directory that will extend Admin_Controller
php index.php matches create:controller admin.user extend=admin
```

##Models

###create:model name_of_model

Creates a model having name_of_model as name. You can put the model inside a directory. Directories are delimited with ".". So, if you want to create the model inside models/admin, you can do create model admin.name_of_model.

Usage examples

```php
//Create a user_model model that extends MY_Model
php index.php matches create:model user_model e=my

// Create a User model inside admin directory that will extend MY_Model
php index.php matches create:model admin.user extend=my
```

##Views

###create:view name_of_view

Creates a view having name_of_view as file name. You can put the view inside a directory. Directories are delimited with ".". So, if you want to create the view inside views/admin, you can do create view admin.name_of_view.

Usage examples

```php
//Create an index_view.php
php index.php matches create:view user_view

// Create an index_view.php inside users directory
php index.php matches create:view users.index_view
```

##Migrations

CodeIgniter Matches helps you create, do, undo, and reset migrations.

###create:migration

To create a migration you can call create:migration. As a result, a migration will be created in the migrations directory prefixed with version as file name. You can also pass a table name as parameter. If no table name is given, you will have to put the name of the table in the migration file. Below are usage examples:

Usage examples
```php
// Create a migration
php index.php matches create:migration create_users_table

//Create a migration with a table inside it
php index.php matches create:migration create_users_table table=users

//Create a migration with a table inside it
php index.php matches create:migration create_users_table t=users
```

###do:migration

do:migration executes the migrations' up() methods. If you pass the version of the migration a parameter, it will stop at that version of the migration.

Usage examples:
```php
// Execute all migrations until the last one
php index.php matches do:migration

// Execute all migrations until a certain version of migration
php index.php matches do:migration 20150722
```

###undo:migration

undo:migration returns you to the previous migration version. This one also can accept a migration version as parameter to return to a migration.

Usage examples:
```php
// Undo last migration
php index.php matches undo:migration

// Undo the migrations until a specified migration version
php index.php matches undo:migration 20150722
```

###reset:migration

reset:migration will reset the migrations until the migration mentioned in $config['migration_version'] (in the migration configuration file).

Usage example:
```php
// Reset the migrations
php index.php matches reset:migration
```

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
