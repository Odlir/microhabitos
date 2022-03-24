<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reporte extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->Library('Layouts');
		$this->load->model('MaestroModel');
        $this->load->model('EncuestaModel');
		$this->load->model('ReporteModel');

		if(!is_logged_in())
		{
			redirect('login');
		}
	}
	
	public function index(){
		$maestro = new MaestroModel;
        $encuesta = new EncuestaModel;

		$this->layouts->set_title('Reportes');
		$this->layouts->set_module_name('Reporte de micro hábitos');
		$this->layouts->add_include('files/custom/reporte.js');
		$data['comboEncuesta'] = $encuesta->get_encuestaCombo();
		$data['comboGrupo'] = $maestro->get_Grupos_Activos(true);
		
		$this->layouts->view('reporte/reporte_inicio', $data);
	}

	public function getResultados(){
		$reporte = new ReporteModel;

		$esPersona = ($this->input->post('nombrePersona') != "");

		$dataPost = array(
            'nombrePersona' => $this->input->post('nombrePersona'),
			'grupo' => $this->input->post('nombreGrupo'),
			'encuesta' => $this->input->post('nombreEncuesta'),
			'fechaDesde' => $this->input->post('fechaDesde'),
            'fechaHasta' => $this->input->post('fechaHasta')
        );
		
		$data["esPersona"] = $esPersona;
		$data["data"] = $reporte->get_data_reporte($dataPost);
		$this->load->view('reporte/reporte_resultado', $data);
	}

	public function getDetallePersona(){

		$persona_id = $this->input->post('persona_id');
		$programacion_id = $this->input->post('programacion_id');
	
		$reporte = new ReporteModel;

		$dt = $reporte->get_Preguntas_Programacion($programacion_id);
		$dt2 = $reporte->get_Respuestas_Persona($programacion_id, $persona_id);

		foreach ($dt as $value) {
			foreach ($dt2 as $value2) {
				if($value2->encuesta_pregunta_id ==  $value->encuesta_pregunta_id){
					$value->encuesta_pregunta_respuesta = $value2->encuesta_pregunta_respuesta_nombre;
					$value->encuesta_pregunta_estado_id = 2;
				}
			}
		}

		$data["dataPreguntas"] = $dt;
		$data["total_preguntas"] = sizeof($dt);
		$data["total_respondidas"] = $reporte->get_Total_Respondidas_Persona($programacion_id, $persona_id);
		$data["dataEncuesta"] = $reporte->get_Detalle_Persona($programacion_id, $persona_id);

		$this->load->view('reporte/reporte_detalle_persona', $data);
	}

	public function getDetalleEncuesta(){
		$reporte = new ReporteModel;
		
		$programacion_id = $this->input->post('programacion_id');

		$dt = $reporte->get_Preguntas_Programacion($programacion_id);
		$persona_respondidas = $reporte->get_Personas_Respondieron($programacion_id);
		$persona_sin_responder = $reporte->get_Personas_No_Respondieron($programacion_id);

		

		$TotalRespondidas = 0;
		foreach($persona_respondidas as $value){
			$TotalRespondidas = $TotalRespondidas + $value->total_preguntas;
		}

		$totalPreguntas = sizeof($dt)*(sizeof($persona_respondidas)+sizeof($persona_sin_responder));

		$data["dataEncuesta"] = $reporte->get_Encuesta_Detalle($programacion_id);
		$data["total_preguntas"] = $totalPreguntas;
		$data["total_preguntas_pro"] = sizeof($dt);
		$data["total_respondidas"] = $TotalRespondidas;

		$data["persona_respondidas"] = $persona_respondidas;
		$data["persona_sin_responder"] = $persona_sin_responder;

		$reporte_data_pregunta = $reporte->get_excel_detalle_pregunta($programacion_id);
		$reporte_data_pregunta_persona = $reporte->get_excel_detalle_pregunta_persona($programacion_id);

		$data["detalle_por_preguntas"] = $reporte_data_pregunta;

		$data_persona = [];

		$each_persona_id = 0;
		$each_persona = "";

		if (sizeof($reporte_data_pregunta_persona) > 0) {
			$each_persona_id = $reporte_data_pregunta_persona[0]->persona_id;
			$each_persona = $reporte_data_pregunta_persona[0]->persona_nombre_completo;
		}
		
		//valores
		$respuesta_1 = 0;
		$respuesta_2 = 0;
		$respuesta_3 = 0;
		$respuesta_4 = 0;
		$respuesta_5 = 0;
		$respuesta_no = 0;

		for ($i = 0; $i < sizeof($reporte_data_pregunta_persona); $i++){
		
			$valor = $reporte_data_pregunta_persona[$i];
			
			if($valor->persona_id == $each_persona_id){

				switch ($valor->encuesta_pregunta_respuesta_nombre) {
					case '1':
						$respuesta_1++;
						break;
					case '2':
						$respuesta_2++;
						break;
					case '3':
						$respuesta_3++;
						break;
					case '4':
						$respuesta_4++;
						break;
					case '5':
						$respuesta_5++;
						break;
					default:
						$respuesta_no++;
						break;
				}

			}

			if($each_persona_id != $valor->persona_id){
				$pregunta_array = array(
					"persona" => $each_persona,
					"respuesta_1" => $respuesta_1,
					"respuesta_2" => $respuesta_2,
					"respuesta_3" => $respuesta_3,
					"respuesta_4" => $respuesta_4,
					"respuesta_5" => $respuesta_5,
					"no_aplica" => $respuesta_no,
				);
				array_push($data_persona, $pregunta_array);

				$respuesta_1 = 0;
				$respuesta_2 = 0;
				$respuesta_3 = 0;
				$respuesta_4 = 0;
				$respuesta_5 = 0;
				$respuesta_no = 0;

				if($valor->persona_id == $each_persona_id){

					switch ($valor->encuesta_pregunta_respuesta_nombre) {
						case '1':
							$respuesta_1++;
							break;
						case '2':
							$respuesta_2++;
							break;
						case '3':
							$respuesta_3++;
							break;
						case '4':
							$respuesta_4++;
							break;
						case '5':
							$respuesta_5++;
							break;
						default:
							$respuesta_no++;
							break;
					}
	
				}
			}
			
			$each_persona_id = $valor->persona_id;
			$each_persona = $valor->persona_nombre_completo;

			if ($i == sizeof($reporte_data_pregunta_persona) -1) {
				$pregunta_array = array(
					"persona" => $each_persona,
					"respuesta_1" => $respuesta_1,
					"respuesta_2" => $respuesta_2,
					"respuesta_3" => $respuesta_3,
					"respuesta_4" => $respuesta_4,
					"respuesta_5" => $respuesta_5,
					"no_aplica" => $respuesta_no,
				);
				array_push($data_persona, $pregunta_array);

				$respuesta_1 = 0;
				$respuesta_2 = 0;
				$respuesta_3 = 0;
				$respuesta_4 = 0;
				$respuesta_5 = 0;
				$respuesta_no = 0;
			}
		}
		
		$data["detalle_por_persona"] = $data_persona;

		$this->load->view('reporte/reporte_detalle_encuesta', $data);
	}

	public function getComparar(){
		$reporte = new ReporteModel;
		
		$programacion_id_01 = $this->input->post('id_primero');
		$programacion_id_02 = $this->input->post('id_segundo');

		
		$dt01 = $reporte->get_Preguntas_Programacion($programacion_id_01);

		$persona_respondidas01 = $reporte->get_Personas_Respondieron($programacion_id_01);
		$persona_sin_responder01 = $reporte->get_Personas_No_Respondieron($programacion_id_01);

		$TotalRespondidas01 = 0;
		foreach($persona_respondidas01 as $value){
			$TotalRespondidas01 = $TotalRespondidas01 + $value->total_preguntas;
		}

		$totalPreguntas01 = sizeof($dt01)*(sizeof($persona_respondidas01)+sizeof($persona_sin_responder01));


		$data["dataEncuesta01"] = $reporte->get_Encuesta_Detalle($programacion_id_01);
		$data["total_preguntas01"] = $totalPreguntas01;
		$data["total_respondidas01"] = $TotalRespondidas01;
		$data["persona_respondidas01"] = $persona_respondidas01;
		$data["persona_sin_responder01"] = $persona_sin_responder01;

		//
		$dt02 = $reporte->get_Preguntas_Programacion($programacion_id_02);

		$persona_respondidas02 = $reporte->get_Personas_Respondieron($programacion_id_02);
		$persona_sin_responder02 = $reporte->get_Personas_No_Respondieron($programacion_id_02);

		$TotalRespondidas02 = 0;
		foreach($persona_respondidas02 as $value){
			$TotalRespondidas02 = $TotalRespondidas02 + $value->total_preguntas;
		}

		$totalPreguntas02 = sizeof($dt02)*(sizeof($persona_respondidas02)+sizeof($persona_sin_responder02));


		$data["dataEncuesta02"] = $reporte->get_Encuesta_Detalle($programacion_id_02);
		$data["total_preguntas02"] = $totalPreguntas02;
		$data["total_respondidas02"] = $TotalRespondidas02;
		$data["persona_respondidas02"] = $persona_respondidas02;
		$data["persona_sin_responder02"] = $persona_sin_responder02;

		$this->load->view('reporte/reporte_comparacion', $data);
	}

	function getExcel(){
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle('Programaciones');
		$reporte = new ReporteModel;

		$esPersona = ($this->input->get('nombrePersona') != "");

		$dataPost = array(
            'nombrePersona' => $this->input->get('nombrePersona'),
			'grupo' => $this->input->get('nombreGrupo'),
			'encuesta' => $this->input->get('nombreEncuesta'),
			'fechaDesde' => $this->input->get('fechaDesde'),
            'fechaHasta' => $this->input->get('fechaHasta')
        );
		
		if($esPersona){
			$table_columns = array("Programación", "Persona", "Micro Hábito", "Grupo", "Estado", "Fecha Inicio", "Fecha Fin", "Fecha Creación");
		}else{
			$table_columns = array("Programación", "Micro Hábito", "Grupo", "Estado", "Fecha Inicio", "Fecha Fin", "Fecha Creación");
		}

		$column = 2;

		foreach($table_columns as $field)
		{
			$sheet->setCellValueByColumnAndRow($column, 1, $field);
			$column++;
		}

		$reporte_data = $reporte->get_data_reporte($dataPost);

		$excel_row = 2;

		foreach($reporte_data as $row)
		{
			if($esPersona){
				$sheet->setCellValueByColumnAndRow(2, $excel_row, $row->encuesta_programacion_nombre);
				$sheet->setCellValueByColumnAndRow(3, $excel_row, $row->persona_nombre_completo);
				$sheet->setCellValueByColumnAndRow(4, $excel_row, $row->encuesta_nombre);
				$sheet->setCellValueByColumnAndRow(5, $excel_row, $row->grupo_nombre);
				$sheet->setCellValueByColumnAndRow(6, $excel_row, $row->encuesta_programacion_estado_str);
				$sheet->setCellValueByColumnAndRow(7, $excel_row, $row->encuesta_programacion_fecha_inicio);
				$sheet->setCellValueByColumnAndRow(8, $excel_row, $row->encuesta_programacion_fecha_fin);
				$sheet->setCellValueByColumnAndRow(9, $excel_row, $row->encuesta_programacion_fecha_creacion);
			}else{
				$sheet->setCellValueByColumnAndRow(2, $excel_row, $row->encuesta_programacion_nombre);
				$sheet->setCellValueByColumnAndRow(3, $excel_row, $row->encuesta_nombre);
				$sheet->setCellValueByColumnAndRow(4, $excel_row, $row->grupo_nombre);
				$sheet->setCellValueByColumnAndRow(5, $excel_row, $row->encuesta_programacion_estado_str);
				$sheet->setCellValueByColumnAndRow(6, $excel_row, $row->encuesta_programacion_fecha_inicio);
				$sheet->setCellValueByColumnAndRow(7, $excel_row, $row->encuesta_programacion_fecha_fin);
				$sheet->setCellValueByColumnAndRow(8, $excel_row, $row->encuesta_programacion_fecha_creacion);
			}
			
			$excel_row++;
		}


		if($esPersona){
			$sheet->getStyle("B1:I1")->applyFromArray(array("font" => array("bold" => true)));
			$sheet->setAutoFilter('B1:I1');
			foreach(range('B','I') as $columnID) {
				$sheet->getColumnDimension($columnID)->setAutoSize(true);

				
			}
		}else{
			$sheet->getStyle("B1:H1")->applyFromArray(array("font" => array("bold" => true)));
			$sheet->setAutoFilter('B1:H1');
			foreach(range('B','H') as $columnID) {
				$sheet->getColumnDimension($columnID)->setAutoSize(true);
			}
		}

		/*
		$spreadsheet->createSheet();
		// Zero based, so set the second tab as active sheet
		$spreadsheet->setActiveSheetIndex(1);
		$spreadsheet->getActiveSheet()->setTitle('Second tab');
		*/
		
		$writer = new Xlsx($spreadsheet);
		$date = new DateTime();

		$filename = 'reporte_'.$date->format('Ymd_His');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output'); 
	}

	function getDetalleExcel($var_programacion_id = 0){
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle('Micro hábito');
		$reporte = new ReporteModel;

		$default_columna = 2;
		$default_row = 2;

		$excel_row = $default_row;

		$sheet->setCellValueByColumnAndRow(2, $excel_row, "Datos de la programación");
		$sheet->getStyle("B".$excel_row)->applyFromArray(array("font" => array("bold" => true)));
		$excel_row++;

		$table_columns = array("Programación", "Micro Hábito", "Grupo", "Estado", "Fecha Inicio", "Fecha Fin", "Fecha Creación");

		$column = $default_columna;

		foreach($table_columns as $field)
		{
			$sheet->setCellValueByColumnAndRow($column, $excel_row, $field);
			$column++;
		}

		$sheet->getStyle("B".$excel_row.":H".$excel_row)->applyFromArray(array("font" => array("bold" => true)));

		$reporte_data = $reporte->get_data_reporte_by_programacion($var_programacion_id);

		$excel_row++;

		foreach($reporte_data as $row)
		{
			$sheet->setCellValueByColumnAndRow(2, $excel_row, $row->encuesta_programacion_nombre);
			$sheet->setCellValueByColumnAndRow(3, $excel_row, $row->encuesta_nombre);
			$sheet->setCellValueByColumnAndRow(4, $excel_row, $row->grupo_nombre);
			$sheet->setCellValueByColumnAndRow(5, $excel_row, $row->encuesta_programacion_estado_str);
			$sheet->setCellValueByColumnAndRow(6, $excel_row, $row->encuesta_programacion_fecha_inicio);
			$sheet->setCellValueByColumnAndRow(7, $excel_row, $row->encuesta_programacion_fecha_fin);
			$sheet->setCellValueByColumnAndRow(8, $excel_row, $row->encuesta_programacion_fecha_creacion);
			$excel_row++;
		}

		//Cantidad de preguntas respondidas por persona
		$excel_row = $excel_row + 2;
		
		$sheet->setCellValueByColumnAndRow(2, $excel_row, "Cantidad de preguntas respondidas por persona");
		$sheet->getStyle("B".$excel_row)->applyFromArray(array("font" => array("bold" => true)));
		$excel_row++;

		$detalle_nombre = array("Persona", "Email", "Celular", "Total Preguntas respondidas");

		$column = $default_columna;
		
		foreach($detalle_nombre as $field)
		{
			$sheet->setCellValueByColumnAndRow($column, $excel_row, $field);
			$column++;
		}
		$sheet->getStyle("B".$excel_row.":E".$excel_row)->applyFromArray(array("font" => array("bold" => true)));

		$excel_row++;

		$reporte_data_persona = $reporte->get_Personas_Respondieron($var_programacion_id);
		$reporte_data_persona2 = $reporte->get_Personas_No_Respondieron($var_programacion_id);

		$query = array_merge($reporte_data_persona, $reporte_data_persona2);

		foreach($query as $row)
		{
			$sheet->setCellValueByColumnAndRow(2, $excel_row, $row->persona_nombre_completo);
			$sheet->setCellValueByColumnAndRow(3, $excel_row, $row->persona_email);
			$sheet->setCellValueByColumnAndRow(4, $excel_row, $row->persona_celular);
			$sheet->setCellValueByColumnAndRow(5, $excel_row, $row->total_preguntas);
			$excel_row++;
		}

		
		//$sheet->setAutoFilter('B1:H1');
		foreach(range('B','H') as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(true);
		}

		//Detalle Preguntas
		$excel_row = $default_row;
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(1);
		$sheet->setTitle('Detalle Preguntas');

		$sheet->setCellValueByColumnAndRow(2, $excel_row, "Detalle de preguntas respondidas");
		$sheet->getStyle("B".$excel_row)->applyFromArray(array("font" => array("bold" => true)));
		$excel_row++;

		$table_columns = array("Pregunta", "Respuesta", "Total personas que contestaron");

		$column = $default_columna;

		foreach($table_columns as $field)
		{
			$sheet->setCellValueByColumnAndRow($column, $excel_row, $field);
			$column++;
		}

		$sheet->getStyle("B".$excel_row.":D".$excel_row)->applyFromArray(array("font" => array("bold" => true)));
		
		$excel_row++;

		$reporte_data_pregunta = $reporte->get_excel_detalle_pregunta($var_programacion_id);


		foreach($reporte_data_pregunta as $row)
		{
			$sheet->setCellValueByColumnAndRow(2, $excel_row, $row->encuesta_pregunta_nombre);
			$sheet->setCellValueByColumnAndRow(3, $excel_row, $row->encuesta_pregunta_respuesta_nombre);
			$sheet->setCellValueByColumnAndRow(4, $excel_row, $row->total_personas);
			$excel_row++;
		}

		foreach(range('B','D') as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(true);
		}


		//Detalle por persona
		$excel_row = $default_row;
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(2);
		$sheet->setTitle('Detalle por persona');

		$sheet->setCellValueByColumnAndRow(2, $excel_row, "Detalle de preguntas respondidas por persona");
		$sheet->getStyle("B".$excel_row)->applyFromArray(array("font" => array("bold" => true)));
		$excel_row++;

		$table_columns = array("Persona", "Pregunta", "Respuesta", "Fecha de respuesta");

		$column = $default_columna;

		foreach($table_columns as $field)
		{
			$sheet->setCellValueByColumnAndRow($column, $excel_row, $field);
			$column++;
		}
		
		$sheet->getStyle("B".$excel_row.":E".$excel_row)->applyFromArray(array("font" => array("bold" => true)));
		
		$excel_row++;

		$reporte_data_pregunta_persona = $reporte->get_excel_detalle_pregunta_persona($var_programacion_id);


		foreach($reporte_data_pregunta_persona as $row)
		{
			$sheet->setCellValueByColumnAndRow(2, $excel_row, $row->persona_nombre_completo);
			$sheet->setCellValueByColumnAndRow(3, $excel_row, $row->encuesta_pregunta_nombre);
			$sheet->setCellValueByColumnAndRow(4, $excel_row, $row->encuesta_pregunta_respuesta_nombre);
			$sheet->setCellValueByColumnAndRow(5, $excel_row, $row->encuesta_pregunta_respuesta_persona_fecha_creacion);
			$excel_row++;
		}

		foreach(range('B','E') as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(true);
		}

		//Volvemos a la primera hoja
		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$date = new DateTime();

		$filename = 'reporte_detalle_'.$date->format('Ymd_His');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output'); 
	}

}
