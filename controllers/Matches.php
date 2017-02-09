<?php
#!/usr/bin/php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Copyright (C) 2014 @avenirer [avenir.ro@gmail.com]
 * Everyone is permitted to copy and distribute verbatim or modified copies of this license document,
 * and changing it is allowed as long as the name is changed.
 * DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 ***** Do whatever you like with the original work, just don't be a dick.
 ***** Being a dick includes - but is not limited to - the following instances:
 ********* 1a. Outright copyright infringement - Don't just copy this and change the name.
 ********* 1b. Selling the unmodified original with no work done option-so-ever, that's REALLY being a dick.
 ********* 1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.
 ***** If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.
 ***** Code is provided with no warranty.
 *********** Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick.
 *********** Fix the problem yourself. A non-dick would submit the fix back.
 *
 *
 * filename: Matches.php
 * This project started from a great idea posted by @veedeoo [veedeoo@gmail.com] on http://www.daniweb.com/web-development/php/code/477847/codeigniter-cli-trainer-script-creates-simple-application
 * License info: http://www.dbad-license.org/
 */

/* first we make sure this isn't called from a web browser */
if (PHP_SAPI !== 'cli') {
    exit('No web access allowed');
}

/* raise or eliminate limits we would otherwise put on http requests */
set_time_limit(0);
ini_set('memory_limit', '256M');

class Matches extends CI_Controller
{

    /**
     * file name of the file
     *
     * @var string
     */
    private $file_name;

    /**
     * get folder and file name
     *
     * @var
     */
    private $lockup;

    /**
     * migration file path
     *
     * @var string path
     */
    private $migration_path;

    /**
     * formatted migration file name
     *
     * @var string
     */
    private $class_name;

    /**
     * The controller the what gonna extends from
     *
     * @var string
     */
    private $controller_extends;

    /**
     * The model the what gonna extends from
     *
     * @var string
     */
    private $model_extends;

    /**
     * The migration the what gonna extends from
     *
     * @var string
     */
    private $migration_extends;

    /**
     * The template path location
     *
     * @var string
     */
    private $template_location;

    /**
     * The controller the what gonna extends from
     *
     * @var string
     */
    private $find_replace = array();

    /**
     * extend options
     *
     * @var array
     */
    private $available = array('extend' => 'extends', 'e' => 'extends', 'table' => 'table', 't' => 'table');

    /**
     * Cli arguments
     *
     * @var array
     */
    private $arguments = array();

    /**
     * the type of handling 'controller, model, views, migration';
     *
     * @var string
     */
    private $type;

    /**
     * the content of template place holder
     *
     * @var
     */
    private $template_data;

    /**
     * the displayed image for error or on success
     *
     * @var
     */
    private $message;

    // CLI characters
    const TAB = "\t";
    const DOUBLE_TAB = "\t\t";
    const TRIPLE_TAB = "\t\t\t";
    const RETURN_LINE  = "\n";
    const DOUBLE_RETURN = "\n\n";
    const RETURN_WITH_TAB = "\n\t";
    const TAB_WITH_RETURN = "\t\n";

    // cLI output color constant
    const BLACK = "\033[0;30m";
    const GREEN = "\033[0;32m";
    const LIGHT_GREEN = "\033[1;32m";
    const RED = "\033[0;31m";
    const LIGHT_RED = "\033[1;31m";
    const WHITE = "\033[1;37m";
    const BG_RED = "\033[41m";
    const BG_GREEN = "\033[42m";

    // boot up matches
    public function __construct()
    {
        parent::__construct();

        $this->config->load('matches', true);

        $this->template_location = APPPATH . $this->config->item('templates', 'matches');

        $this->controller_extends = $this->config->item('controller_extends', 'matches');
        $this->model_extends = $this->config->item('model_extends', 'matches');
        $this->migration_extends = $this->config->item('migration_extends', 'matches');

        if (ENVIRONMENT === 'production') {
            echo self::RETURN_LINE;
            echo "======== WARNING ========" . self::RETURN_LINE;
            echo "===== IN PRODUCTION =====" . self::RETURN_LINE;
            echo "=========================" . self::RETURN_LINE;
            echo "Are you sure you want to work with CLI on a production app? (y/n)";

            $line = fgets(STDIN);

            if (trim($line) != 'y') {
                $this->info('Aborting!')->error();
            }

            echo self::RETURN_LINE . "Thank you, continuing...";
            echo self::DOUBLE_RETURN;
        }

        $this->load->helper('file');
    }

    /**
     * remap cli argument to object method
     *
     * @param  string $method
     * @param  array  $parameters
     * @return Object|bool
     */
    public function _remap($method, $parameters = array())
    {
        if (strpos($method, ':')) {
            $method = str_replace(':', '_', $method);
        }

        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $parameters);
        }

        return false;
    }

    /**
     * list the available commands
     *
     * @return object help method
     */
    public function index()
    {
        echo self::GREEN . self::RETURN_LINE . 'Available commands:';
        echo self::WHITE . self::DOUBLE_RETURN . 'create';
        echo self::WHITE . self::RETURN_LINE . 'app name_of_app';
        echo self::WHITE . self::RETURN_LINE . 'controller name_of_controller';
        echo self::WHITE . self::RETURN_LINE . 'migration name_of_migration name_of_table ' . self::LIGHT_GREEN . '(OPTIONAL)';
        echo self::WHITE . self::RETURN_LINE . 'model name_of_model';
        echo self::WHITE . self::RETURN_LINE . 'view name_of_view';
        echo self::WHITE . self::RETURN_LINE . 'encryption_key string_to_hash ' . self::LIGHT_GREEN . '(OPTIONAL)';
        echo self::DOUBLE_RETURN ;
    }

    /*
     * create application's controller file, model file, view file and migration file
     */
    public function create($option = NULL, $name = NULL) {

        $option = filter_var($option, FILTER_SANITIZE_STRING);
        $name   = filter_var($name, FILTER_SANITIZE_STRING);

        $options = array('app', 'controller', 'model', 'view', 'migration');

        if (in_array($option, $options)) {
            if (empty($name)) {
                $this->info('You didn\'t provide a name for ')->error();
            }

            switch ($option):
                case 'app':
                    $this->create_app($name);
                    break;
                case 'controller':
                    $this->create_controller($name);
                    break;
                case 'model':
                    $this->create_model($name);
                    break;
                case 'view':
                    $this->create_view($name);
                    break;
                case 'migration':
                    $this->create_migration($name);
                    break;
            endswitch;
        } else {
            $this->info('I can only create: app, controller, model, migration')->error();
        }
    }

    /**
     * generate app "controller, model, view"
     *
     * @param string $app
     */
    public function create_app($app = null)
    {
        if (isset($app)) {
            if (file_exists('application/controllers/' . $this->_filename($app) . '.php')
                or (class_exists('' . $app . ''))
                or (class_exists('' . $app . '_model'))) {
                $this->info($app . ' Controller or Model already exists in the application/controllers directory.')->error();
            } else {
                $this->create_controller($app);
                $this->create_model($app);
                $this->create_view($app);
            }
        } else {
            $this->info('You need to provide a name for the app')->error();
        }
    }

    /**
     * create controller.
     *
     * @return bool
     */
    public function create_controller()
    {
        $this->type = 'controller';

	    $args = func_get_args();

        $this->parseArguments($args);

        $this->buildFilePath();

        $this->formatTemplateFile();

        $this->saveFile();
    }

    /**
     * create model
     *
     * @return bool
     */
    public function create_model()
    {
        $this->type = 'model';

        $args = func_get_args();

        $this->parseArguments($args);

        $this->buildFilePath();

        $this->formatTemplateFile();

        $this->saveFile();
    }

    /*
     * create view
     *
     * returns bool
     */
    public function create_view($view = null)
    {
        $this->type = 'view';

        $args = func_get_args();

        $this->parseArguments($args);

        $this->buildFilePath();

        $this->getFileTemplate();

        $this->saveFile();
    }

    /**
     * create migration file
     *
     * @return bool
     */
    public function create_migration()
    {
        $this->type = 'migration';

        $args = func_get_args();

        $this->parseArguments($args);

        $this->buildFilePath();

        $this->config->load('migration', true);
        $this->migration_path = $this->config->item('migration_path', 'migration');

        $this->createMigrationDirectoryIfNotAlreadyExist();

        $this->verify_migration_enabled();

        $this->setMigrationFileNameByMigrationType();

        return $this->createMigrationFile();
    }

    /**
     * run migration by specific version or by latest one's
     *
     * @param  string $version
     * @return bool
     */
    public function do_migration($version = null)
    {
        $this->load->library('migration', 'ci_migration');

        if (isset($version) && ($this->migration->version($version) === false)) {
            $this->info($this->migration->error_string())->error();
        } elseif (is_null($version) && $this->migration->latest() === false) {
            $this->info($this->migration->error_string())->error();
        }

        return $this->info('The migration has concluded successfully.')->success();
    }

    /**
     * undo the migration by version or the latest one's
     *
     * @param  string $version
     * @return bool
     */
    public function undo_migration($version = null)
    {
        $this->load->library('migration');

        $migrations = $this->migration->find_migrations();
        $migration_keys = array();

        foreach ($migrations as $key => $migration) {
            $migration_keys[] = $key;
        }

        if (isset($version) && array_key_exists($version, $migrations) && $this->migration->version($version)) {
            return $this->info('The migration was reset to the version: ' . $version)->success();
        } elseif (isset($version) && !array_key_exists($version, $migrations)) {
            $this->info('The migration with version number ' . $version . ' doesn\'t exist.')->error();
        } else {
            $penultimate = (count($migration_keys) == 1) ? 0 : $migration_keys[count($migration_keys) - 2];

            if ($this->migration->version($penultimate)) {
                return $this->info('The migration has been rolled back successfully.')->success();
            } else {
                $this->info('Couldn\'t roll back the migration.')->error();
            }
        }
    }

    /**
     * reset the migrations until the migration mentioned
     * in the migration config file
     */
    public function reset_migration()
    {
        $this->load->library('migration');

        if ($this->migration->current() !== false) {
            return $this->info('The migration was reset to the version set in the config file.')->success();
        } else {
            show_error($this->migration->error_string());

            $this->info('The migration was reset to the version set in the config file.')->error();
        }
    }

    private function verify_migration_enabled()
    {
        $migration_enabled = $this->config->item('migration_enabled');

        if ($migration_enabled === false) {
            return $this->info('Your app is not migration enabled. Enable it inside application/config/migration.php')->error();
        }

        return true;
    }

    /**
     * generate migration key in config.php file
     *
     * @param  string $string
     * @return bool
     */
    public function encryption_key($string = null)
    {
        if (is_null($string)) {
            $string = microtime();
        }

        $key = hash('ripemd128', $string);
        $files = $this->search_files(APPPATH . 'config/', 'config.php');

        if (! empty($files)) {
            $search = '$config[\'encryption_key\'] = \'\';';
            $replace = '$config[\'encryption_key\'] = \'' . $key . '\';';

            foreach ($files as $file) {
                $file = trim($file);

                // is weird, but it seems that the file cannot be found unless I do some trimming
                $template = file_get_contents($file);

                if (strpos($template, $search) !== false) {
                    $template = str_replace($search, $replace, $template);

                    if (! write_file($file, $template)) {
                        $this->info('Couldn\'t write encryption key !')->error();
                    }

                    return $this->info("Encryption key {$key} added to {$file}")->success();
                }

                $this->info("Couldn\'t find encryption_key or encryption_key already exists in {$file}")->error();
            }
        }

        $this->info("Couldn\'t find config.php")->error();
    }

    /**
     * search migration files
     *
     * @param  string $path
     * @param  string $file
     * @return string
     */
    private function search_files($path, $file)
    {
        $dir = new RecursiveDirectoryIterator($path);
        $ite = new RecursiveIteratorIterator($dir);

        $files = array();

        foreach ($ite as $oFile) {
            if ($oFile->getFilename() == 'config.php') {
                $found = str_replace('\\', '/', self::RETURN_LINE  . $oFile->getPath() . '/' . $file);
                $files[] = $found;
            }
        }

        return $files;
    }

    /**
     * create migration directory if not already exist
     *
     * @return void
     */
    private function createMigrationDirectoryIfNotAlreadyExist()
    {
        if (! file_exists($this->migration_path)) {
            if (mkdir($this->migration_path, 0755)) {
                return $this->info('Folder migrations created.')->success();
            }

            $this->info("Couldn\'t create folder migrations.")->error();
        }
    }

    /**
     * set migration file name by what was set in migration config file
     *
     * @return string   migration file name (sequential, timestamp)
     */
    private function setMigrationFileNameByMigrationType()
    {
        $migration_type = $this->config->item('migration_type', 'migration');

        if (empty($migration_type)) {
            $migration_type = 'sequential';
        }

        if ($migration_type == 'timestamp') {
            $this->class_name = date('YmdHis') . '_' . strtolower($this->file_name);
        } else {
            $latest_migration = 0;

            foreach (glob($this->migration_path . '*.php') as $migration) {
                $pattern = '/[0-9]{3}/';
                if (preg_match($pattern, $migration, $matches)) {
                    $migration_version = intval($matches[0]);
                    $latest_migration = ($migration_version > $latest_migration) ? $migration_version : $latest_migration;
                }
            }

            $latest_migration = (string) ++$latest_migration;
            $this->class_name = str_pad($latest_migration, 3, '0', STR_PAD_LEFT) . '_' . strtolower($this->file_name);
        }
    }

    /**
     * create migration file
     *
     * @return bool the migration file
     */
    private function createMigrationFile()
    {
        if (file_exists($this->migration_path . $this->class_name) or (class_exists($this->class_name))) {
            return $this->info("{$this->class_name} Migration already exists.")->error();
        } else {
            $template_data = $this->GetFileTemplate('migration');

            if ($template_data === false) {
                return false;
            }

            $extends = array_key_exists('extends', $this->arguments) ? $this->arguments['extends'] : $this->migration_extends;
            $extends = in_array(strtolower($extends), array('my', 'ci')) ? strtoupper($extends) : ucfirst($extends);

            $table = 'SET_YOUR_TABLE_HERE';

            if (array_key_exists('table', $this->arguments)) {
                if ($this->arguments['table'] == '%inherit%' || $this->arguments['table'] == '%i%') {
                    $table = preg_replace('/rename_|remove_|modify_|delete_|add_|create_|_table|_tbl/i', '', $this->file_name);
                } else {
                    $table = $this->arguments['table'];
                }
            }

            $this->find_replace['{{MIGRATION_NAME}}'] = $this->file_name;
            $this->find_replace['{{MIGRATION_EXTENDS_FROM}}'] = $extends;
            $this->find_replace['{{MIGRATION_FILE}}'] = $this->class_name;
            $this->find_replace['{{MIGRATION_PATH}}'] = str_replace('\\', '/', $this->migration_path);
            $this->find_replace['{{TABLE_NAME}}'] = $table;

            $template_data = strtr($template_data, $this->find_replace);

            if (! write_file($this->migration_path . $this->class_name . '.php', $template_data)) {
                $this->info('Couldn\'t write Migration.')->error();
            } else {
                return $this->info("{$this->class_name} has been created.")->success();
            }
        }
    }

    /**
     * extract cli arguments
     *
     * @param  array $parameters
     * @return bool
     */
    private function parseArguments($arguments)
    {
        if (count($arguments) < 1) {
            return $this->info('no file name provided !')->error();
        }

        foreach ($arguments as $parameter) {
            $argument = explode(':', $parameter);

            // Check if passing just file name without setting extends controller
            // otherwise set the extended controller to the controller given
            if (count($argument) == 1 && !isset($this->file_name)) {
                $this->file_name = $argument[0];
            } elseif (array_key_exists($argument[0], $this->available)) {
                $this->arguments[$this->available[$argument[0]]] = $argument[1];
            }
        }


        $this->validateName();
    }

    /**
     * lock up for the file given
     *
     * @return array
     */
    private function buildFilePath()
    {
        $str = strtolower($this->file_name);

        if (strpos($str, '.')) {
            $structure = explode('.', $str);
            $this->class_name = array_pop($structure);
        } else {
            $structure = array();
            $this->class_name = $str;
        }

        $this->class_name = ucfirst($this->class_name);

        if (substr(CI_VERSION, 0, 1) != '2') {
            $this->class_name = ucfirst($this->class_name);
        }

        $directories = implode('/', $structure);

        $file_path = ((strlen($directories) > 1) ? $directories . DIRECTORY_SEPARATOR : '') . $this->class_name . '.php';

        return $this->lockup = array(
            'file_path' => $file_path,
            'class_name' => $this->class_name,
            'directories' => $directories,
        );
    }

    /**
     * format file name by codeigniter version
     *
     * @param string $str
     * @return string
     */
    private function _filename($str)
    {
        $this->class_name = strtolower($str);

        if (substr(CI_VERSION, 0, 1) != '2') {
            $this->class_name = ucfirst($this->class_name);
        }

        return $this->class_name;
    }

    /**
     *  check if the template placeholder is exists and get the content
     *
     * @return bool
     */
    private function GetFileTemplate()
    {
        $template_location = $this->template_location . $this->type . '_template.txt';

        if (file_exists($template_location)) {
            return $this->template_data = file_get_contents($template_location);
        }

        $this->info("Couldn\'t find {$this->type} template.")->error();
    }

    /**
     * check if the file exists
     *
     * @return bool
     */
    private function fileExists()
    {
        return file_exists(APPPATH . $this->type . 's/' . $this->lockup['file_path']);
    }

    /**
     * Create the file and save it
     *
     * @return bool
     */
    private function createFile()
    {
        $this->GetFileTemplate();

        return $this->formatTemplateFile();
    }

    /**
     * Format template file
     *
     * @return string
     */
    private function formatTemplateFile()
    {
        $this->GetFileTemplate();

        $name = strtoupper($this->type);

        // if the controller is extends not the default controller
        // replace it with the given extended controller and the extends
        $extends = array_key_exists('extends', $this->arguments) ? $this->arguments['extends'] : $this->{$this->type . '_extends'};

        // controller is 'my' or 'ci' make them upper case or if other then that
        // capitalize the first letter from word given
        $extends = in_array(strtolower($extends), array('my', 'ci')) ? strtoupper($extends) : ucfirst($extends);

        // Replace given data controller template
        $this->find_replace["{{{$name}_NAME}}"] = $this->lockup['class_name'];
        $this->find_replace["{{{$name}_FILE_PATH}}"] = str_replace('\\', '/', $this->lockup['file_path']);
        $this->find_replace["{{{$name}_EXTENDS_FROM}}"] = $extends;

        // replace all the placeholder with the information given
        $this->template_data = strtr($this->template_data, $this->find_replace);

    }

    /**
     * Create directories for the file
     *
     * @return bool
     */
    private function createDirectories()
    {
        if (strlen($this->lockup['directories']) > 0 && !file_exists(APPPATH . $this->type . 's/' . $this->lockup['directories'])) {
            return mkdir(APPPATH . $this->type . 's/' . $this->lockup['directories'], 0777, true);
        }

        return true;
    }

    /**
     * Write the file in proper path
     *
     * @return bool
     */
    private function saveFile()
    {
        if ($this->fileExists()) {
            $this->info("{$this->type} already exists!")->error();
        }

        if (! $this->createDirectories()) {
            $this->info('Error creating directories')->error();
        }

        if (! write_file(APPPATH . $this->type .'s'. DIRECTORY_SEPARATOR . $this->lockup['file_path'], $this->template_data)) {
            $this->info("Couldn\'t write {$this->type}.")->error();
        }

        return $this->info("{$this->type} created successfully.")->success();
    }

    /**
     * set the message
     *
     * @param string $message
     * @return $this
     */
    private function info($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * display success message
     *
     * @return string
     */
    private function success()
    {
        echo self::RETURN_LINE .
            self::LIGHT_GREEN . $this->message;

        return true;
    }

    /**
     * display error message
     *
     * @return string
     */
    private function error()
    {
        exit(self::RETURN_LINE .
            self::BG_RED .
            self::WHITE . $this->message . self::RETURN_LINE);
    }

    /**
     * validate file name if is contain number in the string
     *
     */
    private function validateName()
    {
        if (! preg_match('/^([^0-9]+)$/', $this->file_name)) {
            $this->info('The file name must not contain a number.')->error();
        }
    }
}
