<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConfiguracionSMS extends CI_Controller {

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

        $data_sms = $programacion->get_mensaje_sms()[0];

		$data['data'] = $data_sms;

		$this->layouts->set_title('Configuración sms');
		$this->layouts->set_module_name('Notificación SMS');
		$this->layouts->add_include('files/custom/config_sms.js');
		$this->layouts->view('configuracion/sms_inicio', $data);
	}

	public function sms_create()
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
