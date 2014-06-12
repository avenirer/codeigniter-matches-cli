<?php
#!/usr/bin/php
if (!defined('BASEPATH')) exit('No direct script access allowed');

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
if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');
/* raise or eliminate limits we would otherwise put on http requests */
set_time_limit(0);
ini_set('memory_limit', '256M');

/* here we go */
class Matches extends CI_Controller {
	private $_c_extends = 'CI';
	private $_mo_extends = 'CI';
	private $_mi_extends = 'CI';
	private $_templates_loc = 'application/views/matches_templates/';
	private $_tab = "\t";
	private $_tab2 = "\t\t";
	private $_tab3 = "\t\t\t";
	
	private $_ret = "\n";
	private $_ret2 = "\n\n";
	private $_rettab = "\n\t";
    private $_tabret= "\t\n";
	private $_find_replace = array();
	public function __construct()
	{
		parent::__construct();
		
		if (ENVIRONMENT === 'production')
		{
			echo "\n";
			echo "======== WARNING ========".$this->_ret;
			echo "===== IN PRODUCTION =====".$this->_ret;
			echo "=========================".$this->_ret;
			echo "Are you sure you want to work with CLI on a production app? (y/n)";
			$line = fgets(STDIN);
			if(trim($line) != 'y')
			{
				echo "Aborting!".$this->_ret;
				exit;
			}
			echo "\n";
			echo "Thank you, continuing...".$this->_ret2;
		}
	}
	/*
	* return string
	*/
	public function index()
	{
		echo 'Hello. Need help to ignite somethin\'?'.$this->_ret;
	}
	/*
	* list the available commands
	* 
	*/
	public function help()
	{
		echo 'todo';
	}
	
	
	
	
	/*
	* CLI tester
	* returns string 
	*/
	public function hello($name)
	{
		echo 'Hello '. $name;
	}
	/*
	* create application's controller file, model file, and view file
	* @migration, this you can extend
	*/
	public function create_app($app)
	{
		if(file_exists('application/controllers/'.$this->_filename($app).'.php') OR (class_exists(''.$app.'')) OR (class_exists(''.$app.'_model')))
		{
			echo $app.' Controller or Model already exists in the application/controllers directory.';
		}
		else
		{
			$this->create_controller($app);
			$this->create_model($app);
			$this->create_view($app);
			
		}
	}
	/*
	* create controller
	* returns boolean true
	*/
	public function create_controller($controller)
	{
		$class_name = ucfirst($controller);
		$file_name = $this->_filename($class_name);
		if(file_exists('application/controllers/'.$file_name.'.php') OR (class_exists($class_name)))
		{
			echo $this->_ret.$class_name.' Controller already exists in the application/controllers directory.';
		}
		else
		{
			if(file_exists($this->_templates_loc.'controller_template.txt'))
			{
				$f = file_get_contents($this->_templates_loc.'controller_template.txt');
			}
			else
			{
				echo $this->_ret.'Couldn\'t find Controller template.';
				return FALSE;
			}
			$this->_find_replace['{{CONTROLLER}}'] = $class_name;
			$this->_find_replace['{{CONTROLLER_FILE}}'] = $file_name;
			$this->_find_replace['{{MV}}'] = strtolower($class_name);
			$this->_find_replace['{{C_EXTENDS}}'] = $this->_c_extends;
			$f = strtr($f,$this->_find_replace);
			$writeThisFile = fopen('application/controllers/'.$file_name.'.php',"w");
			if(fwrite($writeThisFile,$f))
			{
				fclose($writeThisFile);
				echo $this->_ret.'Controller '.$class_name.' has been created.';
				return TRUE;
			}
			else
			{
				echo $this->_ret.'Couldn\'t write Controller.';
				return FALSE;
			}
		}
	}
	/*
	* create model
	* returns boolean true
	*/
	public function create_model($model)
	{
		$class_name = ucfirst($model).'_model';
		$file_name = $this->_filename($class_name);
		if(file_exists('application/models/'.$file_name.'.php') OR (class_exists($class_name)))
		{
			echo $this->_ret.$class_name.' Model already exists in the application/models directory.';
		}
		else
		{
			if(file_exists($this->_templates_loc.'model_template.txt'))
			{
				$f = file_get_contents($this->_templates_loc.'model_template.txt');
			}
			else
			{
				echo $this->_ret.'Couldn\'t find Model template.';
				return FALSE;
			}
			$this->_find_replace['{{MODEL}}'] = $class_name;
			$this->_find_replace['{{MODEL_FILE}}'] = $file_name;
			$this->_find_replace['{{MO_EXTENDS}}'] = $this->_mo_extends;
			$f = strtr($f,$this->_find_replace);
			$writeThisFile = fopen('application/models/'.$file_name.'.php',"w");
			if(fwrite($writeThisFile,$f))
			{
				fclose($writeThisFile);
				echo $this->_ret.'Model '.$class_name.' has been created.';
				return TRUE;
			}
			else
			{
				echo $this->_ret.'Couldn\'t write Model.';
				return FALSE;
			}
		}
	}

	/*
	* create view 
	* returns string
	*/
	public function create_view($view)
	{
		$file_name = $view;
		if(file_exists('application/views/'.$file_name.'_view.php'))
		{
			echo $this->_ret.$file_name.' View already exists in the application/views directory.';
		}
		else
		{
			if(file_exists($this->_templates_loc.'view_template.txt'))
			{
				$f = file_get_contents($this->_templates_loc.'view_template.txt');
			}
			else
			{
				echo $this->_ret.'Couldn\'t find View template.';
				return FALSE;
			}
			$this->_find_replace['{{VIEW}}'] = $file_name;
			$f = strtr($f,$this->_find_replace);
			$writeThisFile = fopen('application/views/'.$file_name.'_view.php',"w");
			if(fwrite($writeThisFile,$f))
			{
				fclose($writeThisFile);
				echo $this->_ret.'View '.$class_name.' has been created.';
				return TRUE;
			}
			else
			{
				echo $this->_ret.'Couldn\'t write View.';
				return FALSE;
			}
		}
	}

	public function create_migration($action, $table = NULL)
	{
		$class_name = 'Migration_'.ucfirst($action);
		$this->config->load('migration',TRUE);
		$migration_path = $this->config->item('migration_path','migration');
		if(!file_exists($migration_path))
		{
			if(mkdir($migration_path,0755))
			{
				echo $this->_ret.'Folder migrations created.';
			}
			else
			{
				echo $this->_ret.'Couldn\'t create folder migrations.';
				return FALSE;
			}
		}
		$migration_type = $this->config->item('migration_type','migration');
		if(empty($migration_type))
		{
			$migration_type = 'sequential';
		}
		if($migration_type == 'timestamp')
		{
			$file_name = date('YmdHis').'_'.strtolower($action);
		}
		else
		{
			$latest_migration = 0;
			foreach (glob($migration_path.'*.php') as $migration)
			{
				$pattern = '/[0-9]{3}/';
				if(preg_match($pattern, $migration,$matches))
				{
					$migration_version = intval($matches[0]);
					$latest_migration = ($migration_version>$latest_version) ? $migration_version : $latest_version;
				}
			}
			$latest_migration = (string)++$latest_migration;
			$file_name = str_pad($latest_migration, 3, '0', STR_PAD_LEFT).'_'.strtolower($action);
		}
		if(file_exists($migration_path.$file_name) OR (class_exists($class_name)))
		{
			echo $this->_ret.$class_name.' Migration already exists.';
			return FALSE;
		}
		else
		{
			if(file_exists($this->_templates_loc.'migration_template.txt'))
			{
				$f = file_get_contents($this->_templates_loc.'migration_template.txt');
			}
			else
			{
				echo $this->_ret.'Couldn\'t find Migration template.';
				return FALSE;
			}
			$this->_find_replace['{{MIGRATION}}'] = $class_name;
			$this->_find_replace['{{MIGRATION_FILE}}'] = $file_name;
			$this->_find_replace['{{MIGRATION_PATH}}'] = $migration_path;
			$this->_find_replace['{{MI_EXTENDS}}'] = $this->_mi_extends;
			if(empty($table))
			{
				$table = $action;
			}
			$this->_find_replace['{{TABLE}}'] = $table;
			$f = strtr($f,$this->_find_replace);
			$writeThisFile = fopen($migration_path.$file_name.'.php',"w");
			if(fwrite($writeThisFile,$f))
			{
				fclose($writeThisFile);
				echo $this->_ret.'Migration '.$class_name.' has been created.';
				return TRUE;
			}
			else
			{
				echo $this->_ret.'Couldn\'t write Migration.';
				return FALSE;
			}
		}
	}

	private function _filename($str)
	{
		$file_name = strtolower($str);
		if (substr(CI_VERSION, 0, 1) != '2')
		{
			$file_name = ucfirst($file_name);
		}
		return $file_name;
	}
	
	
}
