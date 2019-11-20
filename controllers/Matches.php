<?php
#!/usr/bin/php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
* Copyright (C) 2014 @avenirer [avenir.ro@gmail.com]
* Everyone is permitted to copy and distribute verbatim or modified copies of this license document,
* and changing it is allowed as long as the name is changed.
* DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
*
***** Do whatever you like with the original work, just don't be a dick.
***** Being a dick includes - but is not limited to - the following instances:
********* 1a. Outright copyright infringement - Don't just copy this and change the name.
********* 1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
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
ini_set('memory_limit', '512M');

/**
 * Class Matches
 *
 * @property CI_Config    $config
 * @property CI_Migration $migration
 *
 * @property string       $_c_extends
 * @property string       $_mo_extends
 * @property string       $_mi_extends
 * @property string       $_templates_loc
 * @property string       $_tab
 * @property string       $_tab2
 * @property string       $_tab3
 * @property string       $_ret
 * @property string       $_ret2
 * @property string       $_rettab
 * @property string       $_tabret
 * @property array        $_find_replace
 */
class Matches extends CI_Controller
{
    private $_c_extends;
    private $_mo_extends;
    private $_mi_extends;
    private $_templates_loc;
    private $_tab = "\t";
    private $_tab2 = "\t\t";
    private $_tab3 = "\t\t\t";
    private $_ret = "\n";
    private $_ret2 = "\n\n";
    private $_rettab = "\n\t";
    private $_tabret = "\t\n";
    private $_find_replace = array();

    /**
     * Matches constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->config->load('matches', true);
        $this->_templates_loc = APPPATH . $this->config->item('templates', 'matches');
        $this->_c_extends = $this->config->item('c_extends', 'matches');
        $this->_mo_extends = $this->config->item('mo_extends', 'matches');
        $this->_mi_extends = $this->config->item('mi_extends', 'matches');
        if (ENVIRONMENT === 'production') {
            echo "\n";
            echo "======== WARNING ========" . $this->_ret;
            echo "===== IN PRODUCTION =====" . $this->_ret;
            echo "=========================" . $this->_ret;
            echo "Are you sure you want to work with CLI on a production app? (y/n)";
            $line = fgets(STDIN);
            if (trim($line) != 'y') {
                echo "Aborting!" . $this->_ret;
                exit;
            }
            echo "\n";
            echo "Thank you, continuing..." . $this->_ret2;
        }
        $this->load->helper('file');
    }

    /**
     * @param       $method
     * @param array $params
     *
     * @return mixed|void
     */
    public function _remap($method, $params = array())
    {
        if (strpos($method, ':')) {
            $method = str_replace(':', '_', $method);
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        // Some code here...
    }

    /**
     *
     * return string
     */
    public function index()
    {
        echo 'Hello. Need help to ignite somethin\'?' . $this->_ret;
    }

    /**
     * CLI tester
     * returns string
     *
     * @param $name
     */
    public function hello($name)
    {
        echo 'Hello ' . $name . $this->_ret;
    }

    /**
     *
     * list the available commands
     *
     */
    public function help()
    {
        echo $this->_ret . 'Available commands:';
        echo $this->_ret2 . ' create';
        echo $this->_ret . '  app name_of_app';
        echo $this->_ret . '  controller name_of_controller';
        echo $this->_ret . '  migration name_of_migration name_of_table-(OPTIONAL)';
        echo $this->_ret . '  model name_of_model';
        echo $this->_ret . '  view name_of_view';
        echo $this->_ret2 . ' encryption_key string_to_hash-(OPTIONAL)';
        echo $this->_ret2 . $this->_ret2;
    }

    /**
     * create application's controller file, model file, view file and migration file
     *
     * @param null $what
     * @param null $name
     *
     * @return bool|void
     */
    public function create($what = null, $name = null)
    {
        $what = filter_var($what, FILTER_SANITIZE_STRING);
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $can_create = array('app', 'controller', 'model', 'view', 'migration');
        if (in_array($what, $can_create)) {
            if (empty($name)) {
                echo $this->_ret . 'You didn\'t provide a name for ' . $what;
                return false;
            }
            switch ($what) {
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
            }
        } else {
            echo $this->_ret . 'I can only create: app, controller, model, migration';
        }
    }

    /**
     * @param null $app
     */
    public function create_app($app = null)
    {
        if (isset($app)) {
            if (file_exists('application/controllers/' . $this->_filename($app) . '.php') OR (class_exists('' . $app . '')) OR (class_exists('' . $app . '_model'))) {
                echo $app . ' Controller or Model already exists in the application/controllers directory.';
            } else {
                $this->create_controller($app);
                $this->create_model($app);
                $this->create_view($app);
            }
        } else {
            echo $this->_ret . 'You need to provide a name for the app';
        }
    }

    /**
     * create controller
     * returns boolean true
     *
     * @return bool|void
     */
    public function create_controller()
    {
        $available = array('extend' => 'extend', 'e' => 'extend');
        $params = func_get_args();
        $arguments = array();
        foreach ($params as $parameter) {
            $argument = explode(':', $parameter);
            if (sizeof($argument) == 1 && !isset($controller)) {
                $controller = $argument[0];
            } elseif (array_key_exists($argument[0], $available)) {
                $arguments[$available[$argument[0]]] = $argument[1];
            }
        }
        if (isset($controller)) {
            $names = $this->_names($controller);
            $class_name = $names['class'];
            $file_name = $names['file'];
            $directories = $names['directories'];
            if (file_exists(APPPATH . 'controllers/' . $file_name . '.php')) {
                echo $this->_ret . $class_name . ' Controller already exists in the application/controllers' . $directories . ' directory.';
            } else {
                $f = $this->_get_template('controller');
                if ($f === false) {
                    return false;
                }
                $this->_find_replace['{{CONTROLLER}}'] = $class_name;
                $this->_find_replace['{{CONTROLLER_FILE}}'] = $file_name . '.php';
                $this->_find_replace['{{MV}}'] = strtolower($class_name);
                $extends = array_key_exists('extend', $arguments) ? $arguments['extend'] : $this->_c_extends;
                $extends = in_array(strtolower($extends), array('my', 'ci')) ? strtoupper($extends) : ucfirst($extends);
                $this->_find_replace['{{C_EXTENDS}}'] = $extends;
                $f = strtr($f, $this->_find_replace);
                if (strlen($directories) > 0 && !file_exists(APPPATH . 'controllers/' . $directories)) {
                    mkdir(APPPATH . 'controllers/' . $directories, 0777, true);
                }
                if (write_file(APPPATH . 'controllers/' . $file_name . '.php', $f)) {
                    echo $this->_ret . 'Controller ' . $class_name . ' has been created inside ' . APPPATH . 'controllers/' . $directories . '.';
                    return true;
                } else {
                    echo $this->_ret . 'Couldn\'t write Controller.';
                    return false;
                }
            }
        } else {
            echo $this->_ret . 'You need to provide a name for the controller.';
        }
    }

    /**
     * create model
     * returns boolean true
     *
     * @return bool|void
     */
    public function create_model()
    {
        $available = array('extend' => 'extend', 'e' => 'extend');
        $params = func_get_args();
        $arguments = array();
        foreach ($params as $parameter) {
            $argument = explode(':', $parameter);
            if (sizeof($argument) == 1 && !isset($model)) {
                $model = $argument[0];
            } elseif (array_key_exists($argument[0], $available)) {
                $arguments[$available[$argument[0]]] = $argument[1];
            }
        }
        if (isset($model)) {
            $names = $this->_names($model);
            $class_name = $names['class'];
            $file_name = $names['file'];
            $directories = $names['directories'];
            if (file_exists(APPPATH . 'models/' . $file_name . '.php')) {
                echo $this->_ret . $class_name . ' Model already exists in the application/models' . $directories . ' directory.';
            } else {
                $f = $this->_get_template('model');
                if ($f === false) {
                    return false;
                }
                $this->_find_replace['{{MODEL}}'] = $class_name;
                $this->_find_replace['{{MODEL_FILE}}'] = $file_name . '.php';
                $extends = array_key_exists('extend', $arguments) ? $arguments['extend'] : $this->_mo_extends;
                $extends = in_array(strtolower($extends), array('my', 'ci')) ? strtoupper($extends) : ucfirst($extends);
                $this->_find_replace['{{MO_EXTENDS}}'] = $extends;
                $f = strtr($f, $this->_find_replace);
                if (strlen($directories) > 0 && !file_exists(APPPATH . 'models/' . $directories)) {
                    mkdir(APPPATH . 'models/' . $directories, 0777, true);
                }
                if (write_file(APPPATH . 'models/' . $file_name . '.php', $f)) {
                    echo $this->_ret . 'Model ' . $class_name . ' has been created inside ' . APPPATH . 'models/' . $directories . '.';
                    return true;
                } else {
                    echo $this->_ret . 'Couldn\'t write Model.';
                    return false;
                }
            }
        } else {
            echo $this->_ret . 'You need to provide a name for the model.';
        }
    }

    /**
     * create view
     *
     * @param null $view
     *
     * @return bool|string|void
     */
    public function create_view($view = null)
    {
        $available = array();
        $params = func_get_args();
        $arguments = array();
        foreach ($params as $parameter) {
            $argument = explode(':', $parameter);
            if (sizeof($argument) == 1 && !isset($view)) {
                $view = $argument[0];
            } elseif (array_key_exists($argument[0], $available)) {
                $arguments[$available[$argument[0]]] = $argument[1];
            }
        }
        if (isset($view)) {
            $names = $this->_names($view);
            $file_name = strtolower($names['file']);
            $directories = $names['directories'];
            if (file_exists(APPPATH . 'views/' . $file_name . '.php')) {
                echo $this->_ret . $file_name . ' View already exists in the application/views/' . $directories . ' directory.';
            } else {
                $f = $this->_get_template('view');
                if ($f === false) {
                    return false;
                }
                $this->_find_replace['{{VIEW}}'] = $file_name . '.php';
                $f = strtr($f, $this->_find_replace);
                if (strlen($directories) > 0 && !file_exists(APPPATH . 'views/' . $directories)) {
                    mkdir(APPPATH . 'views/' . $directories, 0777, true);
                }
                if (write_file(APPPATH . 'views/' . $file_name . '.php', $f)) {
                    echo $this->_ret . 'View ' . $file_name . ' has been created inside ' . APPPATH . 'views/' . $directories . '.';
                    return true;
                } else {
                    echo $this->_ret . 'Couldn\'t write View.';
                    return false;
                }
            }
        } else {
            echo $this->_ret . 'You need to provide a name for the view file.';
        }
    }

    /**
     * @param null $version
     *
     * @return bool
     */
    public function do_migration($version = null)
    {
        $this->load->library('migration');
        if (isset($version) && ($this->migration->version($version) === false)) {
            show_error($this->migration->error_string());
        } elseif (is_null($version) && $this->migration->latest() === false) {
            show_error($this->migration->error_string());
        } else {
            echo $this->_ret . 'The migration has concluded successfully.';
        }
        return true;
    }

    /**
     * @param null $version
     *
     * @return bool|void
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
            echo $this->_ret . 'The migration was reset to the version: ' . $version;
            return true;
        } elseif (isset($version) && !array_key_exists($version, $migrations)) {
            echo $this->_ret . 'The migration with version number ' . $version . ' doesn\'t exist.';
        } else {
            $penultimate = (sizeof($migration_keys) == 1) ? 0 : $migration_keys[sizeof($migration_keys) - 2];
            if ($this->migration->version($penultimate)) {
                echo $this->_ret . 'The migration has been rolled back successfully.';
                return true;
            } else {
                echo $this->_ret . 'Couldn\'t roll back the migration.';
                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function reset_migration()
    {
        $this->load->library('migration');
        if ($this->migration->current() !== false) {
            echo $this->_ret . 'The migration was reset to the version set in the config file.';
            return true;
        } else {
            echo $this->_ret . 'Couldn\'t reset migration.';
            show_error($this->migration->error_string());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function verify_migration_enabled()
    {
        $migration_enabled = $this->config->item('migration_enabled');
        if ($migration_enabled === false) {
            echo $this->_ret . 'Your app is not migration enabled. Enable it inside application/config/migration.php';
        }
        return true;
    }

    /**
     * @return bool
     */
    public function create_migration()
    {
        $available = array('extend' => 'extend', 'e' => 'extend', 'table' => 'table', 't' => 'table');
        $params = func_get_args();
        $arguments = array();
        foreach ($params as $parameter) {
            $argument = explode(':', $parameter);
            if (sizeof($argument) == 1 && !isset($action)) {
                $action = $argument[0];
            } elseif (array_key_exists($argument[0], $available)) {
                $arguments[$available[$argument[0]]] = $argument[1];
            }
        }
        if (isset($action)) {
            $class_name = 'Migration_' . ucfirst($action);
            $this->config->load('migration', true);
            $migration_path = $this->config->item('migration_path', 'migration');
            if (!file_exists($migration_path)) {
                if (mkdir($migration_path, 0755)) {
                    echo $this->_ret . 'Folder migrations created.';
                } else {
                    echo $this->_ret . 'Couldn\'t create folder migrations.';
                    return false;
                }
            }
            $this->verify_migration_enabled();
            $migration_type = $this->config->item('migration_type', 'migration');
            if (empty($migration_type)) {
                $migration_type = 'sequential';
            }
            if ($migration_type == 'timestamp') {
                $file_name = date('YmdHis') . '_' . strtolower($action);
            } else {
                $latest_migration = 0;
                foreach (glob($migration_path . '*.php') as $migration) {
                    $pattern = '/[0-9]{3}/';
                    if (preg_match($pattern, $migration, $matches)) {
                        $migration_version = intval($matches[0]);
                        $latest_migration = ($migration_version > $latest_migration) ? $migration_version : $latest_migration;
                    }
                }
                $latest_migration = (string)++$latest_migration;
                $file_name = str_pad($latest_migration, 3, '0', STR_PAD_LEFT) . '_' . strtolower($action);
            }
            if (file_exists($migration_path . $file_name) OR (class_exists($class_name))) {
                echo $this->_ret . $class_name . ' Migration already exists.';
                return false;
            } else {
                $f = $this->_get_template('migration');
                if ($f === false) {
                    return false;
                }
                $this->_find_replace['{{MIGRATION}}'] = $class_name;
                $this->_find_replace['{{MIGRATION_FILE}}'] = $file_name;
                $this->_find_replace['{{MIGRATION_PATH}}'] = $migration_path;
                $extends = array_key_exists('extend', $arguments) ? $arguments['extend'] : $this->_mi_extends;
                $extends = in_array(strtolower($extends), array('my', 'ci')) ? strtoupper($extends) : ucfirst($extends);
                $this->_find_replace['{{MI_EXTENDS}}'] = $extends;
                $table = 'SET_YOUR_TABLE_HERE';

                if (array_key_exists('table', $arguments)) {
                    if ($arguments['table'] == '%inherit%' || $arguments['table'] == '%i%') {
                        $table = preg_replace('/rename_|remove_|modify_|delete_|add_|create_|_table|_tbl/i', '', $action);
                    } else {
                        $table = $arguments['table'];
                    }
                }

                $this->_find_replace['{{TABLE}}'] = $table;
                $f = strtr($f, $this->_find_replace);
                if (write_file($migration_path . $file_name . '.php', $f)) {
                    echo $this->_ret . 'Migration ' . $class_name . ' has been created.';
                    return true;
                } else {
                    echo $this->_ret . 'Couldn\'t write Migration.';
                    return false;
                }
            }
        } else {
            echo $this->_ret . 'You need to provide a name for the migration.';
            return false;
        }
    }

    /**
     * @param null $string
     */
    public function encryption_key($string = null)
    {
        if (is_null($string)) {
            $string = microtime();
        }
        $key = hash('ripemd128', $string);
        $files = $this->_search_files(APPPATH . 'config/', 'config.php');
        if (!empty($files)) {
            $search = '$config[\'encryption_key\'] = \'\';';
            $replace = '$config[\'encryption_key\'] = \'' . $key . '\';';
            foreach ($files as $file) {
                $file = trim($file);
                // is weird, but it seems that the file cannot be found unless I do some trimming
                $f = file_get_contents($file);
                if (strpos($f, $search) !== false) {
                    $f = str_replace($search, $replace, $f);
                    if (write_file($file, $f)) {
                        echo $this->_ret . 'Encryption key ' . $key . ' added to ' . $file . '.';
                    } else {
                        echo $this->_ret . 'Couldn\'t write encryption key ' . $key . ' to ' . $file . '.';
                    }
                } else {
                    echo $this->_ret . 'Couldn\t find encryption_key or encryption_key already exists in ' . $file . '.';
                }
            }
        } else {
            echo $this->_ret . 'Couldn\'t find config.php';
        }
    }

    /**
     * @param $path
     * @param $file
     *
     * @return array
     */
    private function _search_files($path, $file)
    {
        $dir = new RecursiveDirectoryIterator($path);
        $ite = new RecursiveIteratorIterator($dir);
        $files = array();
        foreach ($ite as $oFile) {
            if ($oFile->getFilename() == 'config.php') {
                $found = str_replace('\\', '/', $this->_ret . $oFile->getPath() . '/' . $file);
                $files[] = $found;
            }
        }
        return $files;
    }

    /**
     * @param $str
     *
     * @return array
     */
    private function _names($str)
    {
        $str = strtolower($str);
        if (strpos($str, '.')) {
            $structure = explode('.', $str);
            $class_name = array_pop($structure);
        } else {
            $structure = array();
            $class_name = $str;
        }
        $class_name = ucfirst($class_name);
        $file_name = $class_name;
        if (substr(CI_VERSION, 0, 1) != '2') {
            $file_name = ucfirst($file_name);
        }
        $directories = implode('/', $structure);
        $file = $directories . '/' . $file_name;
        return array('file' => $file, 'class' => $class_name, 'directories' => $directories);
    }

    /**
     * @param $str
     *
     * @return string
     */
    private function _filename($str)
    {
        $file_name = strtolower($str);
        if (substr(CI_VERSION, 0, 1) != '2') {
            $file_name = ucfirst($file_name);
        }
        return $file_name;
    }

    /**
     * @param $type
     *
     * @return bool|false|string
     */
    private function _get_template($type)
    {
        $template_loc = $this->_templates_loc . $type . '_template.txt';
        if (file_exists($template_loc)) {
            $f = file_get_contents($template_loc);
            return $f;
        } else {
            echo $this->_ret . 'Couldn\'t find ' . $type . ' template.';
            return false;
        }
    }
}
