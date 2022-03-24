<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConfiguracionEmail extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->Library('Layouts');
		$this->load->model('ProgramacionModel');

		if(!is_logged_in())
		{
			redirect('login');
		}
	}

	public function index()
	{
		$programacion = new ProgramacionModel;

        $data_email = $programacion->get_mensaje_email()[0];

		$data['data'] = $data_email;

		$this->layouts->set_title('Configuración email');
		$this->layouts->set_module_name('Notificación Email');
		$this->layouts->add_include('files/custom/config_email.js');
		$this->layouts->view('configuracion/email_inicio', $data);
	}

	public function email_create()
	{
		$programacion = new ProgramacionModel;
        $idMensaje = $this->input->post('idMensaje');
        $textoMensaje = $this->input->post('textoMensaje');

        $data = array(
            'codigo_maestro_descripcion' => $textoMensaje
        );
        $respuesta = $programacion->codigo_maestro_actualizar($idMensaje, $data);

        echo $respuesta;

	}
}
