<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Persona extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->Library('Layouts');
		$this->load->model('PersonaModel');
		$this->load->model('MaestroModel');
		$this->load->library('pagination');
		$this->load->model('GrupoModel');

		if(!is_logged_in())
		{
			redirect('login');
		}
	}
	
	public function index(){
		$persona = new PersonaModel;
		$maestro = new MaestroModel;
		

		$filtroGrupoID = $this->input->get('fgrupo', TRUE);
		$filtroEstadoID = $this->input->get('festado', TRUE);

		if(!isset($filtroGrupoID)){
			$filtroGrupoID = "";
		}

		if(!isset($filtroEstadoID)){
			$filtroEstadoID = "";
		}

		$data['data'] = $persona->get_personas($filtroGrupoID, $filtroEstadoID);
		
		$data['comboDocumento'] = $maestro->get_Tipo_Documento();
		$data['comboUsuario'] = $maestro->get_Tipo_Usuario();
		$data['comboEstadoCivil'] = $maestro->get_Estado_Civil();
		$data['comboEstado'] = $maestro->get_Estados();
		$data['comboGenero'] = $maestro->get_Genero();
		$data['comboGrupo'] = $maestro->get_Grupos_Activos(true);
		
		$this->layouts->set_title('Personas');
		$this->layouts->set_module_name('Mantenimiento de personas');
		$this->layouts->add_include('files/custom/persona.js');
		$this->layouts->view('persona/persona_inicio', $data);
	}

	public function persona_create(){
		$persona = new PersonaModel;
		$val_tipoDocumento = $this->input->post('tipoDocumento');
		$idPersona = $this->input->post('idPersona');

		$this->form_validation->set_error_delimiters('', '');

		if($idPersona == "0"){
			$this->form_validation->set_rules('usuario', 'Usuario', 'trim|required|callback_validar_usuario');
			$this->form_validation->set_message('validar_usuario','El usuario ingresado ya existe.');
		}
		
		$this->form_validation->set_rules('contrasena','Contraseña', 'required');
		$this->form_validation->set_rules('tipoUsuario','Tipo de usuario', 'required');
		$this->form_validation->set_rules('nombres','Nombres', 'required|max_length[50]');
		$this->form_validation->set_rules('apellidoPaterno','Apellido paterno', 'required|max_length[100]');
		//$this->form_validation->set_rules('apellidoMaterno','Apellido materno', 'required|max_length[100]');
		//$this->form_validation->set_rules('genero','Género', 'required');
		$this->form_validation->set_rules('email','Email', 'trim|required|valid_email|max_length[50]');
		$this->form_validation->set_rules('celular','Celular', 'required|numeric|min_length[9]|max_length[9]');
		$this->form_validation->set_rules('grupo','Grupo', 'required');

		

		if ($val_tipoDocumento != ""){
			$this->form_validation->set_rules('nroDocumento','Número de documento', 'required|numeric');
		}else{
			$this->form_validation->set_rules('nroDocumento','Número de documento', 'numeric');
		}
		
		$this->form_validation->set_rules('lugarNacimiento','Lugar de nacimiento', 'max_length[100]');
		

		if ($this->form_validation->run() == FALSE) {
			$data = validation_errors();
			$data_r = str_replace(".", ". </br>", $data);
			echo $data_r;
		} 
		else {
			
			$nombres = $this->input->post('nombres');
			$apellidoPaterno = $this->input->post('apellidoPaterno');
			$apellidoMaterno = $this->input->post('apellidoMaterno');
			$tipoDocumento = $this->input->post('tipoDocumento');
			$nroDocumento = $this->input->post('nroDocumento');
			$estadoCivil = $this->input->post('estadoCivil');
			$fechaNacimiento = $this->input->post('fechaNacimiento');
			$lugarNacimiento = $this->input->post('lugarNacimiento');
			$genero = $this->input->post('genero');
			$email = $this->input->post('email');
			$telefono = $this->input->post('telefono');
			$celular = $this->input->post('celular');
			$grupo = $this->input->post('grupo');
			$state = $this->input->post('state');

			$usuarioNombre = $this->input->post('usuario');
			$usuarioContrasena = $this->input->post('contrasena');
			$usuarioTipo = $this->input->post('tipoUsuario');

			if($state == "on"){
				$state = "2";
			}else{
				$state = "1";
			}

			$data = array(
				'persona_nombre' => $nombres,
				'persona_apellido_paterno' => $apellidoPaterno,
				'persona_apellido_materno' => $apellidoMaterno,
				'persona_genero_id' => $genero,
				'persona_email' => $email,
				'persona_celular' => $celular,
				'grupo_id' => $grupo,
				'persona_estado_id' => $state
			);

			

			if ($tipoDocumento != ""){
				$data["persona_tipo_documento_id"] = $tipoDocumento;
			}

			if ($nroDocumento != ""){
				$data["persona_numero_documento"] = $nroDocumento;
			}

			if ($estadoCivil != ""){
				$data["persona_estado_civil_id"] = $estadoCivil;
			}

			if ($fechaNacimiento != ""){
				$data["persona_fecha_nacimiento"] = $fechaNacimiento;
			}

			if ($lugarNacimiento != ""){
				$data["persona_lugar_nacimiento"] = $lugarNacimiento;
			}

			if ($telefono != ""){
				$data["persona_telefono"] = $telefono;
			}
			
			//Actualizar
			if($idPersona != 0){
				$respuesta = $persona->persona_actualizar($idPersona, $data);

				$dataUsuario = array(
					'usuario_contrasena' => $usuarioContrasena,
					'usuario_tipo_id' => $usuarioTipo,
					'usuario_estado_id' => $state
				);
				$persona->usuario_actualizar($idPersona, $dataUsuario);

			}else{//Insertar
				$idPersona = $persona->persona_insertar($data);
				$dataUsuario = array(
					'persona_id' => $idPersona,
					'usuario_nombre' => $usuarioNombre,
					'usuario_contrasena' => $usuarioContrasena,
					'usuario_tipo_id' => $usuarioTipo,
					'usuario_estado_id' => $state
				);
				$persona->usuario_insertar($dataUsuario);
				$respuesta = true;
			}

			
			
			echo $respuesta;
		}
		
	}

	public function persona_get($idPersona = null){
		$persona = new PersonaModel;
		$data['data'] = $persona->get_persona($idPersona);

		//add the header here
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function persona_eliminar($idPersona){
		$persona = new PersonaModel;
		$respuesta = $persona->persona_eliminar($idPersona);
		$respuesta = $persona->usuario_eliminar($idPersona);

		//add the header here
		header('Content-Type: application/json');
		echo json_encode($respuesta);
	}

	function carga_masiva(){
		$persona = new PersonaModel;
		

		// Count total files
		$countfiles = count($_FILES['files']['name']);
 
		// Looping all files
		for($i=0;$i<$countfiles;$i++){
   
			if(!empty($_FILES['files']['name'][$i])){

				// Define new $_FILES array - $_FILES['file']
				$_FILES['file']['name'] = $_FILES['files']['name'][$i];
				$_FILES['file']['type'] = $_FILES['files']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
				$_FILES['file']['error'] = $_FILES['files']['error'][$i];
				$_FILES['file']['size'] = $_FILES['files']['size'][$i];


				$date = date('YmdHis');
				//$mi_archivo = 'files[]';
				$config['upload_path'] = FCPATH."uploads";
				$config['file_name'] = "carga_persona_".$date;
				$config['allowed_types'] = "xls|xlsx";

				chmod(FCPATH."uploads", 0777);
				$this->load->library('upload', $config);
				// File upload
				if($this->upload->do_upload('file')){
					// Get data about the file
					$uploadData = $this->upload->data();
					$file_full_path = $uploadData['full_path'];

					$spreadsheet = IOFactory::load($file_full_path);
					$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

					$primero = false;

					$dataEncuesta = array();
					foreach($sheetData as $row){

						if(!$primero){
							$primero = true;
						}else{

							$idGrupo = 0;
							$grupo = $persona->valida_grupo($row["H"]);
							if ($grupo){
								$grupo = $grupo[0];
								$idGrupo = $grupo->grupo_id;
								
							}else{
								//Insertar
								$grupoModel = new GrupoModel;
								$data = array(
									'grupo_nombre' => $row["H"],
									'grupo_estado_id' => 2
								);

								$idGrupo = $grupoModel->grupo_insertar_v2($data);
							}


							//Validar usuario
							if($this->validar_usuario($row["A"])){

								$estadoPersona  = (strtolower($row["I"]) == "activo") ? "2": "1";
								$tipo_Persona  = (strtolower($row["B"]) == "normal") ? "2": "1";
								
								$dataPersona = array(
									'persona_nombre' => $row["C"],
									'persona_apellido_paterno' => $row["D"],
									'persona_apellido_materno' => $row["E"],
									'persona_email' => $row["F"],
									'persona_celular' => $row["G"],
									'grupo_id' => $idGrupo,
									'persona_estado_id' => $estadoPersona
								);
								$idPersona = $persona->persona_insertar($dataPersona);
								
								$dataUsuario = array(
									'persona_id' => $idPersona,
									'usuario_nombre' => $row["A"],
									'usuario_contrasena' => "123456",
									'usuario_tipo_id' => $tipo_Persona, // 2= Usuario / 1= Administrador
									'usuario_estado_id' => $estadoPersona
								);

								$persona->usuario_insertar($dataUsuario);

								/*$dataUsuario = array(
									'encuesta_nombre' => $row["A"], //Usuario
									'encuesta_tiempo_alerta_horas' => $row["B"],//Correo electrónico
									'encuesta_estado_id' => $row["C"],//Nombre de usuario
									'encuesta_fecha_inicio_alerta' => $row["D"],//Tipo de usuario
									'encuesta_fecha_fin_alerta' => $row["E"], //Estado
									'encuesta_tipo_alerta_id' => $row["F"], //Registro
									'encuesta_json_dias_alerta' => $row["G"] //Último inicio de sesión
								);*/

								//break;
							}
						}
					}
				}
			}
		}
	}

	function test_file(){
		$this->load->view('test_file');
	}

	public function validar_usuario($usuario)
	{
		$persona = new PersonaModel;
		if ($persona->valida_usuario($usuario))
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function descargar_plantilla()
    {
		$path = FCPATH."uploads/carga_persona_plantilla.xlsx";
		if(is_file($path))
		{
			$this->load->helper('download');
			force_download($path, NULL);
		}
    }

}
