Codeigniter Matches CLI
======================

Codeigniter Matches is a cli script that allows you to write controllers, models and views faster.

#Usage

1. To verify if you have php cli installed, open your terminal command prompt, and then type.

  php -v

If you will receive the php version number, then you are good to go.

2. Go to your config.php file inside the application/config and make sure the following line looks like below:

  $config['uri_protocol'] = 'AUTO';

3. After that line, add the following line

  $config['uri_protocol'] = isset($_SERVER['REQUEST_URI']) ? 'PATH_INFO' : 'CLI';  

4. Save the file...

5. Put Matches.php inside the controllers directory, and matches_templates folder inside the views folder.

6. From terminal or command prompt go to the application's index.php and type:

  php index.php matches

7. If everything went well, you should be greeted by the Matches.

## Commands

You can start using the Matches by writing:

  php index.php matches [COMMAND]

...where [COMMAND] can be:

**create_app name_of_app** - creates a MVC stack (controller, model, view) having name_of_app as names.

**create_controller name_of_controller** - creates a controller having name_of_controller as name.

**create_model name_of_model** - creates a model having name_of_model as name.

**create_view name_of_view** - creates a controller having name_of_view as file name.


#History
This project started from a great idea posted by @veedeoo [veedeoo@gmail.com] on http://www.daniweb.com/web-development/php/code/477847/codeigniter-cli-trainer-script-creates-simple-application
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
