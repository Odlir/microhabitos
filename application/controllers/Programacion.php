<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require './Twilio/autoload.php';

class Programacion extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->Library('Layouts');
		$this->load->model('ProgramacionModel');
        $this->load->model('MaestroModel');
        $this->load->model('EncuestaModel');

		if(!is_logged_in())
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$programacion = new ProgramacionModel;
        $maestro = new MaestroModel;
        $encuesta = new EncuestaModel;

		$data['data'] = $programacion->get_programaciones();

        $data['comboProgramacion'] = $maestro->get_Tipo_Programacion();
        $data['comboEncuesta'] = $encuesta->get_encuestaCombo();
		$data['comboGrupo'] = $maestro->get_Grupos_Activos(true);
		
		$this->layouts->set_title('Programación');
		$this->layouts->set_module_name('Mantenimiento de programaciones');
		$this->layouts->add_include('files/custom/programacion.js');
		$this->layouts->view('programacion/programacion_inicio', $data);
	}

	public function programacion_get_pregunta($idEncuesta){
		$encuesta = new EncuestaModel;
		$respuesta = $encuesta->get_pregunta_programacion_nuevo($idEncuesta);

		//add the header here
		header('Content-Type: application/json');
		echo json_encode($respuesta);
	}

	public function programacion_create(){
		$programacion = new ProgramacionModel;
		$encuesta = new EncuestaModel;
        $idProgramacion = $this->input->post('idProgramacion');

		$pregunta_programacion = json_decode($this->input->post('idProgramacion'));

		$this->form_validation->set_error_delimiters('', '');

		$this->form_validation->set_rules('nombre_programacion','Nombre', 'required');
		$this->form_validation->set_rules('mensaje_email','Mensaje email', 'required');
		$this->form_validation->set_rules('asunto_email','Asunto email', 'required');
		$this->form_validation->set_rules('mensaje_sms','Mensaje sms', 'required');

        if ($idProgramacion==0){
            $this->form_validation->set_rules('encuesta','Encuesta', 'required');
            $this->form_validation->set_rules('grupo','Grupo', 'required');
        }
		
        $this->form_validation->set_rules('fecha_inicio','Fecha de inicio', 'required');
        $this->form_validation->set_rules('fecha_fin','Fecha de fin', 'required');
        $this->form_validation->set_rules('tipo_notifi','Tipo de notificación', 'required');

		

		if ($this->form_validation->run() == FALSE) {
			$data = validation_errors();
			$data_r = str_replace(".", ". </br>", $data);
			echo $data_r;
		} 
		else {
			
			$state = $this->input->post('state');

			if($state == "on"){
				$state = "2";
			}else{
				$state = "1";
			}


            $jsonDias = array(
				'Lunes' => $this->input->post('cLunes') == "on" ? true : false,
                'Martes' => $this->input->post('cMartes') == "on" ? true : false,
                'Miercoles' => $this->input->post('cMiercoles') == "on" ? true : false,
                'Jueves' => $this->input->post('cJueves') == "on" ? true : false,
                'Viernes' => $this->input->post('cViernes') == "on" ? true : false,
                'Sabado' => $this->input->post('cSabado') == "on" ? true : false,
				'Domingo' => $this->input->post('cDomingo') == "on" ? true : false
			);

            //

			$data = array(
				'encuesta_programacion_nombre' => $this->input->post('nombre_programacion'),
				'encuesta_programacion_mensaje_email' => $this->input->post('mensaje_email'),
				'encuesta_programacion_asunto_email' => $this->input->post('asunto_email'),
				'encuesta_programacion_mensaje_sms' => $this->input->post('mensaje_sms'),
                'encuesta_programacion_fecha_inicio' => $this->input->post('fecha_inicio'),
                'encuesta_programacion_fecha_fin' => $this->input->post('fecha_fin'),
                'encuesta_programacion_tipo_id' => $this->input->post('tipo_notifi'),
				'encuesta_programacion_hora_job' => $this->input->post('hora_job'),
                'encuesta_programacion_json_dias' => json_encode($jsonDias),
				'encuesta_programacion_estado_id' => $state
			);
            
			//Actualizar
			$id_programacion = 0;
			if($idProgramacion != 0){
				$respuesta = $programacion->programacion_actualizar($idProgramacion, $data);
				$id_programacion = $idProgramacion;
			}else{//Insertar

                $data["encuesta_id"] = $this->input->post('encuesta');
                $data["grupo_id"] = $this->input->post('grupo');

				$idGenerado = $programacion->programacion_insertar($data);

                $dataGrupo = array(
					'encuesta_programacion_id'=> $idGenerado,
					'encuesta_id' => $this->input->post('encuesta'),
					'grupo_id' => $this->input->post('grupo'),
					'encuesta_grupo_estado_id' => 2
				);

				$id_programacion = $idGenerado;
				$encuesta->encuesta_grupo_insertar($dataGrupo);
                //$this->job_microhabito_create($id_programacion, $this->input->post('tipo_notifi'), $idGenerado);
			}


			$response = array(
				'estado' => true,
				'encuesta_programacion_id' => $id_programacion
			);

			header('Content-Type: application/json');
			echo json_encode($response);
		}
		
	}

	public function getPersonasEncuesta($id_programacion = 0){
		/*$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
		$result = array(
			"draw" => $draw,
			"recordsTotal" => sizeof($data),
			"recordsFiltered" => sizeof($data),
			"data" => $data
		);
		echo json_encode($result);
  		exit();*/

		$encuesta = new EncuestaModel;
		$data["data"] = $encuesta->get_lista_persona_programacion($id_programacion);
		//$this->layouts->view('programacion/programacion_lista_persona', $data);
		$this->load->view('programacion/programacion_lista_persona', $data);
	}

	public function programacion_pregunta_create(){

		$programacion = new ProgramacionModel;
		$input_data = json_decode(trim(file_get_contents('php://input')), true);

		$pregunta_programacion = $input_data["preguntas"];
		$programacion_id = $input_data["programacion_id"];

		foreach($pregunta_programacion as $value){


			$dataPreguntas = array(
				'encuesta_programacion_id' => $programacion_id,
				'encuesta_programacion_pregunta_fecha_inicio' => $value["encuesta_pregunta_fecha_inicio"],
				'encuesta_programacion_pregunta_estado_id' => 2,
			);

			if ($value["es_insert"] == "1"){
				$dataPreguntas['encuesta_pregunta_id'] = $value["encuesta_pregunta_id"];
				$programacion->programacion_pregunta_insertar($dataPreguntas);
			}else{
				$programacion->programacion_pregunta_actualizar($value["encuesta_pregunta_id"], $dataPreguntas);
			}


			
		}

		header('Content-Type: application/json');
		echo json_encode(true);

	}

	public function programacion_get($idProgramacion = null){
		$programacion = new ProgramacionModel;
		$encuesta = new EncuestaModel;

		$dataProgramacion = $programacion->get_programacion($idProgramacion)[0];
		$data['data'] = $dataProgramacion;
		$data['data_pregunta'] = $encuesta->get_pregunta_programacion_editar($idProgramacion);

		//add the header here
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function programacion_eliminar(){
        
		$programacion = new ProgramacionModel;

        $input_data = json_decode(trim(file_get_contents('php://input')), true);

        $idEncuesta = $input_data["id_encuesta"];
        $idGrupo = $input_data["id_grupo"];
        $idProgramacion = $input_data["id_programacion"];

		$respuesta = $programacion->programacion_eliminar($idProgramacion);
        $programacion->programacion_grupo_eliminar($idProgramacion, $idGrupo);

		//add the header here
		header('Content-Type: application/json');
		echo json_encode($respuesta);
	}
	
	public function enviar_notificacion($tipo_alerta = 0){

		$input_data = json_decode(trim(file_get_contents('php://input')), true);

        $idProgramacion = $input_data["idProgramacion"];
		$dataPersona = $input_data["dataPersona"];

		$this->load->library('uuid');
		$encuesta = new EncuestaModel;

		foreach ($dataPersona as $row){
			$Persona_ID = $row["idPersona"];
			$datos = $encuesta->get_encuesta_programacion_by_persona($idProgramacion, $Persona_ID);

			$encuesta_persona_id = $datos->encuesta_persona_id;

			$id_codigo = "";
			if($datos->encuesta_persona_codigo == "0"){
				$id_codigo = $this->uuid->v4();
				$id_codigo = str_replace('-', '', $id_codigo);
	
				$dataEncuesta = array(
					'encuesta_persona_codigo'=> $id_codigo,
					'encuesta_programacion_id' => $idProgramacion,
					'persona_id' => $Persona_ID
				);
				$encuesta_persona_id = $encuesta->encuesta_persona_insertar($dataEncuesta);
			}else{
				$id_codigo = $datos->encuesta_persona_codigo;
			}
			

			$nombre_completo = $datos->persona_nombre;//$datos->persona_apellido_paterno." ".$datos->persona_apellido_materno.", ".$datos->persona_nombre;
			$encuestaNombre = $datos->encuesta_nombre;
			$correo = $datos->persona_email;
			$celular = $datos->persona_celular;
			$mensaje_email = $datos->encuesta_programacion_mensaje_email;
			$asunto_email = $datos->encuesta_programacion_asunto_email;
			
			$mensaje_sms = $datos->encuesta_programacion_mensaje_sms;
			
			
			if($tipo_alerta == 1){//Email
				$this->job_envia_correo($asunto_email, $mensaje_email, $id_codigo, $correo, $nombre_completo, $encuestaNombre, $encuesta_persona_id);
			}else if ($tipo_alerta == 2){ // SMS
				$this->job_envia_sms($mensaje_sms, $celular, $nombre_completo,$id_codigo, $encuesta_persona_id);
			}
		}

		header('Content-Type: application/json');
		echo json_encode(true);
	}

    public function job_microhabito_create($id, $tipo_alerta, $idProgramacion){
		$this->load->library('uuid');
		
		$encuesta = new EncuestaModel;
		$data = $encuesta->get_encuesta_persona_byId($id);
		
		foreach ($data as $datos) {
			$id_codigo = $this->uuid->v4();
			$id_codigo = str_replace('-', '', $id_codigo);

			$dataEncuesta = array(
				'encuesta_persona_codigo'=> $id_codigo,
				'encuesta_programacion_id' => $idProgramacion,
				'persona_id' => $datos->persona_id
			);

			$nombre_completo = $datos->persona_nombre;//$datos->persona_apellido_paterno." ".$datos->persona_apellido_materno.", ".$datos->persona_nombre;
			$encuestaNombre = $datos->encuesta_nombre;
			$correo = $datos->persona_email;
			$celular = $datos->persona_celular;
			
			$mensaje_email = $datos->encuesta_programacion_mensaje_email;
			$asunto_email = $datos->encuesta_programacion_asunto_email;
			$mensaje_sms = $datos->encuesta_programacion_mensaje_sms;
			
			$encuesta_persona_id = $encuesta->encuesta_persona_insertar($dataEncuesta);

			
			
			if($tipo_alerta == 1){//Email
				//Enviar correo
				$this->job_envia_correo($asunto_email, $mensaje_email, $id_codigo, $correo, $nombre_completo, $encuestaNombre, $encuesta_persona_id);
			}else if ($tipo_alerta == 2){ // SMS
				//Enviar sms
				$this->job_envia_sms($mensaje_sms, $celular, $nombre_completo,$id_codigo, $encuesta_persona_id);
			}
		}
		
		
		
		return true;
	}

	public function job_envia_correo($asunto, $msj, $codig, $email, $nombre, $encuestaNombre, $enc_persona_id){
		
		try {
			$this->load->config('email');
			$this->load->library('email');
			
			$from = $this->config->item('smtp_user');
			$subject = $asunto;//$encuestaNombre; //$this->input->post('subject');
			
			$this->email->set_newline("\r\n");
			$this->email->from($from);
			$this->email->to($email);
			$this->email->subject($subject);

			$data["nombre"] = $nombre;
			$data["mensaje"] = $msj; //"Necesitamos saber de tu opinion respondiendo la siguiente encuesta:";
			$data["codigo"] = $codig;

			$body = $this->load->view('email/plantilla', $data, true);

			$this->email->message($body);

			if ($this->email->send()) {
				$encuesta = new EncuestaModel;
				$encuesta->encuesta_persona_noti_email($enc_persona_id);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function job_envia_sms($msj, $celular, $nombre, $codig, $enc_persona_id){
		
		try {
			$celular = "+51".$celular;

			$this->load->config('sms');
			// Your Account SID and Auth Token from twilio.com/console
			$sid = $this->config->item('twilio_sid');
			$token = $this->config->item('twilio_token');
			$client = new Twilio\Rest\Client($sid, $token);
			
            $url = site_url('formulario/index')."/".$codig;
			$from = $this->config->item('twilio_from');

			$mensaje_re = str_replace("{{nombre}}", $nombre, $msj);
			$mensaje = $mensaje_re.$url;//"Estimado(a) ".$nombre.", tienes un micro habito pendiente. Link: ".$url;

			$client->messages->create(
				$celular,
				array(
					'from' => $from,
					'body' => $mensaje
				)
			);
			
			$encuesta = new EncuestaModel;
			$encuesta->encuesta_persona_noti_sms($enc_persona_id);

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function test_email(){
		
		try {

			$codig = "ASDASDASD";
			$email = "jrrivasdiaz@gmail.com"; 
			$nombre = "Opción Contraseña";
			$encuestaNombre = "Micro hábito";

			$programacion = new ProgramacionModel;
			$data_email = $programacion->get_mensaje_email()[0];
			
			$this->load->config('email');
			$this->load->library('email');
			
			$from = $this->config->item('smtp_user');
			$subject = $encuestaNombre; //$this->input->post('subject');
			
			$this->email->set_newline("\r\n");
			$this->email->from($from);
			$this->email->to($email);
			$this->email->subject($subject);

			$data["nombre"] = $nombre;
			$data["mensaje"] = $data_email->codigo_maestro_descripcion; //"Necesitamos saber de tu opinion respondiendo la siguiente encuesta:";
			$data["codigo"] = $codig;

			$body = $this->load->view('email/plantilla', $data, true);

			$this->email->message($body);

			if ($this->email->send()) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	public function test_sms(){
		
		try {
			$programacion = new ProgramacionModel;
			$data_sms = $programacion->get_mensaje_sms()[0];

			$nombre = "Javier Rivas";
			$celular = "+51917439583";
			$codig = "asdasdasd";

			$this->load->config('sms');
			// Your Account SID and Auth Token from twilio.com/console
			$sid = $this->config->item('twilio_sid');
			$token = $this->config->item('twilio_token');
			$client = new Twilio\Rest\Client($sid, $token);
			
            $url = site_url('formulario/index')."/".$codig;
			$from = $this->config->item('twilio_from');

			$mensaje_re = str_replace("{{nombre}}", $nombre, $data_sms->codigo_maestro_descripcion);
			$mensaje = $mensaje_re.$url;//"Estimado(a) ".$nombre.", tienes un micro habito pendiente. Link: ".$url;

			$client->messages->create(
				$celular,
				array(
					'from' => $from,
					'body' => $mensaje
				)
			);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}
