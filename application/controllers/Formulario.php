<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require './Twilio/autoload.php';

class Formulario extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->helper('form');
        $this->load->helper('url');
        $this->load->model('EncuestaModel');
		$this->load->model('PersonaModel');
		$this->load->model('ProgramacionModel');
		$this->load->helper('file');
	}
	
	public function index($codigo_encuesta = null){
		$eData = $this->get_microhabito_json($codigo_encuesta);
		if($eData == 1){
			$this->load->view('formulario_contestado');
		} else if($eData == 2){
			$this->load->view('formulario_error');
		} else{
			$this->load->view('formulario', $eData);
		}
	}

	public function get_microhabito($codigo_encuesta = null){
		$eData = $this->get_microhabito_json($codigo_encuesta);
		if($eData == 1){
			$this->load->view('formulario_contestado');
		} else if($eData == 2){
			$this->load->view('formulario_error');
		} else{
			$this->load->view('formulario', $eData);
		}
	}

	public function guardar_microhabito(){
		$encuesta = new EncuestaModel;
		$input_data = json_decode(trim(file_get_contents('php://input')), true);
		$encuesta_persona_id = $input_data["encuesta"]["encuesta_persona"];
		
		foreach($input_data["pregunta"] as $data){

			$json_id_respuesta_persona = $data["id_respuesta_persona"];
			$json_id_respuesta = $data["id_respuesta"];
			$json_estado = ($data["estado"]) ? 2 : 0;
			//Insertar nuevos con valor true
			if($json_id_respuesta_persona == 0 && $json_estado == 2){
				$dataInsert = array(
					'encuesta_persona_id' => $encuesta_persona_id,
					'encuesta_pregunta_respuesta_id' => $json_id_respuesta,
					'encuesta_pregunta_respuesta_persona_estado_id' => $json_estado
				);
				$encuesta->encuesta_persona_respuesta_insertar($dataInsert);
			}

			//Actualizar existentes
			if($json_id_respuesta_persona != 0 && $json_estado != 2){
				$encuesta->encuesta_persona_respuesta_actualizar($json_id_respuesta_persona);
			}
		}

		$arr = array('error' => false);

		header('Content-Type: application/json');
		echo json_encode($arr);
	}

	public function get_microhabito_json($idTest){
		//$idTest = "31e64ad19aa24c90bce86e45082ca68c";

		$encuesta = new EncuestaModel;
		$persona = new PersonaModel;
		
		$dataEncuesta = $encuesta->get_encuesta_persona_byCodigo($idTest);

		if(sizeof($dataEncuesta) <= 0){
			return 2;
		}else{
			$dataEncuesta = $dataEncuesta[0];
		}

		$idEncuesta = $dataEncuesta->encuesta_id;
		$idProgramacion =  $dataEncuesta->encuesta_programacion_id;
		$idencuestaPersona = $dataEncuesta->encuesta_persona_id;

		$data['persona'] = $dataEncuesta;

		$data['encuesta'] = $encuesta->get_encuesta($idEncuesta)[0];
		$lista_preguntas = $encuesta->get_encuesta_pregunta_formulario($idProgramacion, true);
		
		
		$indexPregunta = 0;
		$totalRespondidas = 0;
		foreach ($lista_preguntas as $pregunta) {
			$dataRespuesta = $encuesta->get_encuesta_pregunta_respuesta_new($pregunta->encuesta_pregunta_id, $idencuestaPersona, true);
			
			$tieneRespuesta = false;

			foreach ($dataRespuesta as $value)
			{
				if($value->encuesta_pregunta_respuesta_persona_id != 0){
					$tieneRespuesta = true;
					$totalRespondidas = $totalRespondidas + 1;
					break;
				}
			}

			$lista_preguntas[$indexPregunta]->flag_contestada = $tieneRespuesta;
			$lista_preguntas[$indexPregunta]->respuestas = $dataRespuesta;
			$indexPregunta++;
		}
		$data['preguntas'] = $lista_preguntas;


		if (sizeof($lista_preguntas) == $totalRespondidas){
			//Actualizar estado
			$persona->actualizar_persona_encuesta($idTest);
			return 1;
		}else{
			return $data;
		}
		
		
		
		//add the header here
		//header('Content-Type: application/json');
		//echo json_encode($data);
		
	}

	public function job_microhabito_create()
	{
		//crea log
		$this->test_hora_server();

		$this->load->library('uuid');
		$encuesta = new EncuestaModel;
		$data = $encuesta->get_encuesta_persona();

		foreach ($data as $datos) {

			$jsonDias = $datos->encuesta_programacion_json_dias;
			$valido = $this->validarDia($jsonDias);


			//Fecha Ultima Notificacion
			$fechaNoti = $datos->encuesta_persona_fecha_notificacion;
			$horaJob = $datos->encuesta_programacion_hora_job;

			$mandarNotificacion = $this->validarFechaHora($fechaNoti, $horaJob);
			$FechaHoraNT = $this->getFechaNotificacion($horaJob);
			if($valido && $mandarNotificacion){

				$id_codigo = $datos->encuesta_persona_codigo;
				$idPersonaEncuesta = $datos->encuesta_persona_id;

				if($datos->encuesta_persona_id == 0){
					$id_codigo = $this->uuid->v4();
					$id_codigo = str_replace('-', '', $id_codigo);
	
					$dataEncuesta = array(
						'encuesta_persona_codigo'=> $id_codigo,
						'encuesta_programacion_id' => $datos->encuesta_programacion_id,
						'persona_id' => $datos->persona_id
					);

					$idPersonaEncuesta = $encuesta->encuesta_persona_insertar_job($dataEncuesta);
				}

				$nombre_completo = $datos->persona_nombre;//$datos->persona_apellido_paterno." ".$datos->persona_apellido_materno.", ".$datos->persona_nombre;
				$encuestaNombre = $datos->encuesta_nombre;
				$correo = $datos->persona_email;
				$celular = $datos->persona_celular;

				$mensaje_email = $datos->encuesta_programacion_mensaje_email;
				$asunto_email = $datos->encuesta_programacion_asunto_email;
				$mensaje_sms = $datos->encuesta_programacion_mensaje_sms;

				$mandoNoti = false;
				
				if($datos->encuesta_programacion_tipo_id == 1){
					//Enviar correo
					$mandoNoti = $this->job_envia_correo($asunto_email, $mensaje_email, $id_codigo, $correo, $nombre_completo,$encuestaNombre, $idPersonaEncuesta);
					sleep(1);
				}else if($datos->encuesta_programacion_tipo_id == 2){
					//Enviar sms
					$mandoNoti = $this->job_envia_sms($mensaje_sms, $celular, $nombre_completo, $id_codigo, $idPersonaEncuesta);
					sleep(1);
				}

				if($mandoNoti){
					$encuesta->encuesta_persona_notificacion_actualizar($FechaHoraNT, $idPersonaEncuesta);
				}
			}
		}
		//add the header here
		$data2["data"] = $data;
		
		header('Content-Type: application/json');
		echo json_encode($data2);
	}

	public function validarFechaHora($fecha_notificacion, $horaJob)
	{
		//Fecha hora del JOB
		$fecha = new DateTime(date("Y-m-d"));
		
		$phoraJob = explode(":", $horaJob);
		$fecha->setTime($phoraJob[0], $phoraJob[1]);
		$fecha_job = $fecha->format('Y-m-d H:i:s');

		//Fecha Hora actual
		$fechaA = new DateTime(date("Y-m-d"));
		$fechaA->setTime(date("H"), 00);
		$fechaA = $fechaA->format('Y-m-d H:i:s');

		$dateDif = false;

		if($fecha_notificacion == ""){
			if ($fechaA >= $fecha_job){
				$dateDif = true;
			}
		}else{
			if ($fecha_job != $fecha_notificacion){
				if ($fechaA >= $fecha_job){
					$dateDif = true;
				}
			}
		}

		return $dateDif;
	}

	public function getFechaNotificacion($horaJob)
	{
		//Fecha hora del JOB
		$fecha = new DateTime(date("Y-m-d"));
		$phoraJob = explode(":", $horaJob);
		$fecha->setTime($phoraJob[0], $phoraJob[1]);
		$fecha_job = $fecha->format('Y-m-d H:i:s');
		return $fecha_job;
	}

	public function validarDia($jsonDias)
	{
		$jsonDecode = json_decode($jsonDias, true);
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		if ($jsonDecode[$dias[date("w")]] == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public function job_envia_correo($asunto, $msj, $codig, $email, $nombre, $encuestaNombre, $idPersonaEncuesta){
		
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
				$encuesta->encuesta_persona_noti_email($idPersonaEncuesta);
				return true;
			} else {
				return false;
			}
			
		} catch (Exception $e) {
			return false;
		}
	}

	public function job_envia_sms($msj, $celular, $nombre, $codig, $idPersonaEncuesta){
		
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
			$encuesta->encuesta_persona_noti_sms($idPersonaEncuesta);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function test_envia_correo(){

		$this->load->config('email');
        $this->load->library('email');
        
        $from = $this->config->item('smtp_user');
        $subject = "Prueba"; //$this->input->post('subject');
        
        $this->email->set_newline("\r\n");
        $this->email->from($from);
        $this->email->to("jrrivasdiaz@gmail.com");
        $this->email->subject("Test");

		$data["nombre"] = "Javier";
		$data["codigo"] = "prueba_codigo";

		$body = $this->load->view('email/plantilla', $data, true);

        $this->email->message($body);

        if ($this->email->send()) {
			//return true;
            echo 'Your Email has successfully been sent.';
        } else {
			//return false;
            show_error($this->email->print_debugger());
        }
	}

	public function test_envia_sms(){

		$this->load->config('sms');
		require './Twilio/autoload.php';

		// Your Account SID and Auth Token from twilio.com/console
		$sid = $this->config->item('twilio_sid');
		$token = $this->config->item('twilio_token');
		$client = new Twilio\Rest\Client($sid, $token);
		
		$from = $this->config->item('twilio_from');
		$nombre = "Javier Rivas";
		$celular = "+51917439583";
		$mensaje = "Estimado ".$nombre.", tienes una encuesta pendiente.";

		$client->messages->create(
			$celular,
			array(
				'from' => $from,
				'body' => $mensaje
			)
		);

		echo "sms";
	}

	public function test_hora_server(){

		$this->guardar_log();
	}

	public function guardar_log(){

		$file_log = FCPATH."log/log.txt";
		chmod($file_log, 0777);
		$date = date('m/d/Y h:i:s a', time());
		
		$data = 'Fecha: '.$date;
		if ( ! write_file($file_log, $data))
		{
				//echo 'Unable to write the file';
		}
		else
		{
				//echo 'File written!';
		}
	}

}
