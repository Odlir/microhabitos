<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->Library('Layouts');
		$this->load->model('PersonaModel');

		if(!is_logged_in())
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$persona = new PersonaModel;
		$data['data'] = $persona->get_persona_encuesta();
		
		$this->layouts->set_title('Inicio');
		$this->layouts->set_module_name('Inicio');
        $this->layouts->add_include('files/custom/home.js');
		$this->layouts->view('home/inicio', $data);
	}
}
