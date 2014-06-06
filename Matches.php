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
	private $_m_extends = 'CI';
	private $_tab = "\t";
	private $_tab2 = "\t\t";
	private $_tab3 = "\t\t\t";
	
	private $_ret = "\n";
	private $_ret2 = "\n\n";
	private $_rettab = "\n\t";
    private $_tabret= "\t\n";
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
			$f = '';
			## must use single quotes when it is necessary
			$f .='<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

class '.$class_name.' extends '.$this->_c_extends.'_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->model(\''.strtolower($class_name).'_model\');
		$data[\'content\'] = $this->'.strtolower($class_name).'_model->get_all();
		$this->load->view(\''.strtolower($class_name).'_view\', $data);
	}
		
	public function get($id)
	{
		$id = intval($id);
		if($id!=0)
		{
			$this->load->model(\''.$class_name.'_model\');
			$data[\'content\'] = $this->'.$class_name.'_model->get($id);
			$this->load->view(\''.$class_name.'_view\', $data);
		}
		else
		{
			redirect(site_url(),\'refresh\');
		}
	}
	
	public function add()
	{
		$this->form_validation->set_rules(\'element\',\'Element label\',\'trim|required\');
		if($this->form_validation->run()===FALSE)
		{
			$data[\'input_element\'] = array(\'name\'=>\'element\', \'id\'=>\'element\', \'value\'=>set_value(\'element\'));
			$this->load->view(\''.strtolower($class_name).'_view\', $data);
		}
		else
		{
			$field = $this->input->post(\'element\');
			$this->load->model(\''.strtolower($class_name).'_model\');
			if($this->'.strtolower($class_name).'_model->add(array(\'field_name\'=>$field)))
			{
				$this->load->view(\'success_page_view\');
			}
			else
			{
				$this->load->view(\'error_page_view\');
			}
		}
	}
	
	public function edit()
	{
		$this->form_validation->set_rules(\'element\',\'Element label\',\'trim|required\');
		$this->form_validation->set_rules(\'id\',\'ID\',\'trim|is_natural_no_zero|required\');
		if($this->form_validation->run()===FALSE)
		{
			if(!$this->input->post())
			{
				$id = intval($this->uri->segment($this->uri->total_segments()));
			}
			else
			{
				$id = set_value(\'id\');
			}
			$data[\'input_element\'] = array(\'name\'=>\'element\', \'id\'=>\'element\', \'value\'=>set_value(\'element\'));
			$data[\'hidden\'] = array(\'id\'=>set_value(\'id\',$id));
			$this->load->view(\''.strtolower($class_name).'_view\', $data);
		}
		else
		{
			$element = $this->input->post(\'element\');
			$id = $this->input->post(\'id\');
			$this->load->model(\''.strtolower($class_name).'_model\');
			if($this->'.strtolower($class_name).'_model->update(array(\'element\'=>$element),array(\'id\'=>$id)))
			{
				$this->load->view(\'success_page_view\', $data);
			}
			else
			{
				$this->load->view(\'error_page_view\');
			}
		}
	}
	public function delete($id)
	{
		$id = intval($id);
		if($id!=0)
		{
			$this->load->model(\''.strtolower($class_name).'_model\');
			$data[\'content\'] = $this->'.strtolower($class_name).'_model->delete();
			$this->load->view(\''.strtolower($class_name).'_view\', $data);
		}
		else
		{
			redirect(site_url(),\'refresh\');
		}
	}
}
/* End of file '.$file_name.' */
/* Location: ./application/controllers/'.$file_name.'.php */';
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
			$f = '';
			## must use single quotes when it is necessary
			$f .='<?php  if(!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');
class '.$class_name.' extends '.$this->_m_extends.'_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_all()
	{
		return (\'This is your first application\');
	}
}
/* End of file '.$file_name.' */
/* Location: ./application/models/'.$file_name.'.php */';
			$writeThisFile = fopen('application/models/'.$file_name.'.php',"w");
			if(fwrite($writeThisFile,$f))
			{
				fclose($writeThisFile);
				echo $this->_ret.'Model '.$class_name.' has been created. What now?';
			}
			else
			{
				echo 'Couldn\'t write model.';
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
			$f ='<!DOCTYPE html>
<html>
	<head>
		<title>'.$file_name.' view page</title>
	</head>
	<body>
		<h1>This is my first application</h1>
		<?php echo $content; ?>
	</body>
</html>';
			$writeThisFile = fopen('application/views/'.$file_name.'_view.php',"w");
			if(fwrite($writeThisFile,$f))
			{
				fclose($writeThisFile);
				echo $this->_ret.'View '.$file_name.'_view has been created. What now?';
			}
			else
			{
				echo 'Couldn\'t write view.'; 
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
