<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReporteModel extends CI_Model{
    
    public function __construct()
    {
        $this->load->database();
    }

    public function get_data_reporte($data){

        if($data["nombrePersona"] == ""){
            $this->db->select('ep2.encuesta_programacion_id,
            e.encuesta_id, 
            e.encuesta_nombre,
            g.grupo_id,
            g.grupo_nombre,
            cm.codigo_maestro_descripcion as encuesta_programacion_estado_str,
            ep2.encuesta_programacion_nombre,
            ep2.encuesta_programacion_fecha_inicio,
            ep2.encuesta_programacion_fecha_fin,
            ep2.encuesta_programacion_fecha_creacion');
            $this->db->from('encuesta_programacion ep2');
            $this->db->join('encuesta e', 'ep2.encuesta_id = e.encuesta_id', 'inner');
            $this->db->join('grupo g', 'ep2.grupo_id = g.grupo_id', 'inner');
            $this->db->join('codigo_maestro cm', 'ep2.encuesta_programacion_estado_id = cm.codigo_maestro_valor and cm.codigo_maestro_tipo_id = 1', 'inner');
            
        }else{
            $this->db->select('CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
            p.persona_id,
            ep2.encuesta_programacion_id,
            e.encuesta_id, 
            e.encuesta_nombre,
            g.grupo_id,
            g.grupo_nombre,
            cm.codigo_maestro_descripcion as encuesta_programacion_estado_str,
            ep2.encuesta_programacion_nombre,
            ep2.encuesta_programacion_fecha_inicio,
            ep2.encuesta_programacion_fecha_fin,
            ep2.encuesta_programacion_fecha_creacion');
            $this->db->from('encuesta_persona ep');
            $this->db->join('persona p', 'ep.persona_id = p.persona_id', 'inner');
            $this->db->join('encuesta_programacion ep2', 'ep.encuesta_programacion_id = ep2.encuesta_programacion_id', 'inner');
            $this->db->join('encuesta e', 'ep2.encuesta_id = e.encuesta_id', 'inner');
            $this->db->join('grupo g', 'ep2.grupo_id = g.grupo_id', 'inner');
            $this->db->join('codigo_maestro cm', 'ep2.encuesta_programacion_estado_id = cm.codigo_maestro_valor and cm.codigo_maestro_tipo_id = 1', 'inner');
            $this->db->where('(p.persona_apellido_paterno LIKE \'%'.$data["nombrePersona"].'%\' or p.persona_apellido_materno LIKE \'%'.$data["nombrePersona"].'%\' or p.persona_nombre LIKE \'%'.$data["nombrePersona"].'%\')', null, false);
        }
        
        $this->db->where('g.grupo_estado_id = 2', null, false);
        $this->db->where('e.encuesta_estado_id = 2', null, false);
        $this->db->where('ep2.encuesta_programacion_estado_id <> 0', null, false);

        if($data["grupo"] != ""){
            $this->db->where('g.grupo_id', $data["grupo"]);
        }

        if($data["encuesta"] != ""){
            $this->db->where('e.encuesta_id', $data["encuesta"]);
        }

        if($data["fechaDesde"] != "" && $data["fechaHasta"] != ""){
            $this->db->where('DATE_FORMAT(ep2.encuesta_programacion_fecha_creacion,"%Y-%m-%d") between DATE_FORMAT("'.$data["fechaDesde"].'","%Y-%m-%d") and DATE_FORMAT("'.$data["fechaHasta"].'","%Y-%m-%d")', null, false);
        }
        
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_data_reporte_by_programacion($id){

        
        $this->db->select('ep2.encuesta_programacion_id,
        e.encuesta_id, 
        e.encuesta_nombre,
        g.grupo_id,
        g.grupo_nombre,
        cm.codigo_maestro_descripcion as encuesta_programacion_estado_str,
        ep2.encuesta_programacion_nombre,
        ep2.encuesta_programacion_fecha_inicio,
        ep2.encuesta_programacion_fecha_fin,
        ep2.encuesta_programacion_fecha_creacion');
        $this->db->from('encuesta_programacion ep2');
        $this->db->join('encuesta e', 'ep2.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('grupo g', 'ep2.grupo_id = g.grupo_id', 'inner');
        $this->db->join('codigo_maestro cm', 'ep2.encuesta_programacion_estado_id = cm.codigo_maestro_valor and cm.codigo_maestro_tipo_id = 1', 'inner');
        
        $this->db->where('g.grupo_estado_id = 2', null, false);
        $this->db->where('e.encuesta_estado_id = 2', null, false);
        $this->db->where('ep2.encuesta_programacion_estado_id <> 0', null, false);
        $this->db->where('ep2.encuesta_programacion_id', $id);
        
        
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_Detalle_Persona($id_programacion, $id_persona){
        $this->db->select('CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
        e.encuesta_nombre, g.grupo_nombre');
        $this->db->from('encuesta_programacion ep');
        $this->db->join('encuesta e', 'ep.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('grupo g', 'ep.grupo_id = g.grupo_id', 'inner');
        $this->db->join('encuesta_persona ep2', 'ep.encuesta_programacion_id = ep2.encuesta_programacion_id', 'inner');
        $this->db->join('persona p', 'ep2.persona_id = p.persona_id', 'inner');
        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        $this->db->where('p.persona_id', $id_persona);

        $query = $this->db->get();
        $data = $query->result();
        return $data[0];
    }

    public function get_Total_Respondidas_Persona($id_programacion, $id_persona){
        $this->db->select('count(*) as total_respuestas');
        $this->db->from('encuesta_pregunta_respuesta_persona eprp');
        $this->db->join('encuesta_pregunta_respuesta epr', 'eprp.encuesta_pregunta_respuesta_id = epr.encuesta_pregunta_respuesta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'eprp.encuesta_persona_id = ep.encuesta_persona_id', 'inner');
        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        $this->db->where('ep.persona_id', $id_persona);
        $this->db->where('epr.encuesta_pregunta_respuesta_estado_id', 2);
        
        $query = $this->db->get();
        $data = $query->result();
        return $data[0];
    }

    public function get_Preguntas_Programacion($id_programacion){
        $this->db->select('ep.encuesta_pregunta_id, ep.encuesta_pregunta_nombre, \'\' as encuesta_pregunta_respuesta, 1 as encuesta_pregunta_estado_id');
        $this->db->from('encuesta_pregunta ep');
        $this->db->join('encuesta e', 'ep.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('encuesta_programacion ep2', 'e.encuesta_id = ep2.encuesta_id', 'inner');
        $this->db->where('ep2.encuesta_programacion_id', $id_programacion);
        $this->db->where('ep.encuesta_pregunta_estado_id', 2);
        $this->db->order_by('ep.encuesta_pregunta_id', 'ASC');

        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_Respuestas_Persona($id_programacion, $id_persona){
        $this->db->select('ep2.encuesta_pregunta_nombre, ep2.encuesta_pregunta_id , epr.encuesta_pregunta_respuesta_nombre');
        $this->db->from('encuesta_pregunta_respuesta_persona eprp');
        $this->db->join('encuesta_pregunta_respuesta epr', 'eprp.encuesta_pregunta_respuesta_id = epr.encuesta_pregunta_respuesta_id', 'inner');
        $this->db->join('encuesta_pregunta ep2', 'epr.encuesta_pregunta_id = ep2.encuesta_pregunta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'eprp.encuesta_persona_id = ep.encuesta_persona_id', 'inner');
        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        $this->db->where('ep.persona_id', $id_persona);
        $this->db->where('epr.encuesta_pregunta_respuesta_estado_id', 2);

        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    /*
    public function get_Personas_Respondieron($id_programacion){
        $this->db->select('p.persona_id, CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
        count(*) as total_preguntas');
        $this->db->from('encuesta_pregunta_respuesta_persona eprp');
        $this->db->join('encuesta_pregunta_respuesta epr', 'eprp.encuesta_pregunta_respuesta_id = epr.encuesta_pregunta_respuesta_id', 'inner');
        $this->db->join('encuesta_pregunta ep2', 'epr.encuesta_pregunta_id = ep2.encuesta_pregunta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'eprp.encuesta_persona_id = ep.encuesta_persona_id', 'inner');
        $this->db->join('persona p', 'ep.persona_id = p.persona_id', 'inner');
        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        $this->db->where('ep2.encuesta_pregunta_estado_id', 2);
        $this->db->group_by("p.persona_id"); 

        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }*/

    public function get_Personas_Respondieron($id_programacion){
        $this->db->select('ep.encuesta_persona_codigo,
        p.persona_email,
        p.persona_celular,
        p.persona_id,
        CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
        SUM(CASE WHEN eprp.encuesta_persona_id IS NULL THEN 0 ELSE 1 END) as total_preguntas');
        $this->db->from('encuesta_persona ep');
        $this->db->join('encuesta_pregunta_respuesta_persona eprp', 'ep.encuesta_persona_id = eprp.encuesta_persona_id', 'left');
        $this->db->join('encuesta_pregunta_respuesta epr', 'eprp.encuesta_pregunta_respuesta_id = epr.encuesta_pregunta_respuesta_id', 'left');
        $this->db->join('encuesta_pregunta ep2', 'epr.encuesta_pregunta_id = ep2.encuesta_pregunta_id and ep2.encuesta_pregunta_estado_id = 2', 'left');
        $this->db->join('persona p', 'ep.persona_id = p.persona_id', 'left');

        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        
        $this->db->group_by("p.persona_id"); 

        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_Personas_No_Respondieron($id_programacion){
        $this->db->select('ep.encuesta_persona_codigo,
        p.persona_email,
        p.persona_celular,
        p.persona_id,
        CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
        0 as total_preguntas');
        $this->db->from('persona p');
        $this->db->join('encuesta_grupo eg', 'eg.grupo_id = p.grupo_id and eg.encuesta_grupo_estado_id <> 0', 'inner');
        $this->db->join('encuesta_programacion ep2', 'eg.encuesta_id = ep2.encuesta_id and eg.grupo_id = ep2.grupo_id and ep2.encuesta_programacion_estado_id <> 0', 'inner');
        $this->db->join('encuesta_persona ep', 'ep.persona_id = p.persona_id and ep2.encuesta_programacion_id = ep.encuesta_programacion_id', 'left');
        $this->db->where('ep2.encuesta_programacion_estado_id', 2);
        $this->db->where('p.persona_estado_id', 2);
        $this->db->where('ep2.encuesta_programacion_id', $id_programacion);
        $this->db->where('eg.encuesta_programacion_id = ep2.encuesta_programacion_id', null, false);
        $this->db->where('ep.encuesta_persona_codigo is null', null, false);

        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_Encuesta_Detalle($id_programacion){
        $this->db->select('ep.encuesta_programacion_nombre, e.encuesta_nombre, g.grupo_nombre');
        $this->db->from('encuesta_programacion ep');
        $this->db->join('encuesta e', 'ep.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('grupo g', 'ep.grupo_id = g.grupo_id', 'inner');
        $this->db->where('ep.encuesta_programacion_id', $id_programacion);

        $query = $this->db->get();
        $data = $query->result();
        return $data[0];
    }

    public function get_excel_detalle_pregunta($id_programacion){
        $this->db->select('epr.encuesta_pregunta_respuesta_id,
        ep.encuesta_pregunta_id,
        e.encuesta_nombre,
        ep.encuesta_pregunta_nombre,
        epr.encuesta_pregunta_respuesta_nombre,
        0 as total_personas');
        $this->db->from('encuesta_pregunta_respuesta epr');
        $this->db->join('encuesta_pregunta ep', 'epr.encuesta_pregunta_id = ep.encuesta_pregunta_id', 'inner');
        $this->db->join('encuesta e', 'ep.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('encuesta_programacion ep2', 'e.encuesta_id = ep2.encuesta_id', 'inner');

        $this->db->where('ep2.encuesta_programacion_id', $id_programacion);
        $this->db->where('epr.encuesta_pregunta_respuesta_estado_id = 2', null, false);
        $this->db->where('ep.encuesta_pregunta_estado_id = 2', null, false);
        
        $query = $this->db->get();
        $data = $query->result();

        foreach($data as $row){
            $row->total_personas = sizeof($this->get_excel_detalle_pregunta_respuesta($id_programacion, $row->encuesta_pregunta_respuesta_id));
        }

        return $data;
    }

    public function get_excel_detalle_pregunta_respuesta($id_programacion, $id_respuesta){
        $this->db->select('eprp.encuesta_pregunta_respuesta_id');
        $this->db->from('encuesta_pregunta_respuesta_persona eprp');
        $this->db->join('encuesta_persona ep', 'eprp.encuesta_persona_id = ep.encuesta_persona_id', 'inner');
        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        $this->db->where('eprp.encuesta_pregunta_respuesta_persona_estado_id = 2', null, false);
        $this->db->where('eprp.encuesta_pregunta_respuesta_id', $id_respuesta);
        
        $query = $this->db->get();
        $data = $query->result();

        return $data;
    }

    public function get_excel_detalle_pregunta_persona($id_programacion){
        $this->db->select('eprp.encuesta_pregunta_respuesta_id,
        ep.persona_id,
        CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
        ep2.encuesta_pregunta_nombre,
        epr.encuesta_pregunta_respuesta_nombre,
        eprp.encuesta_pregunta_respuesta_persona_fecha_creacion');
        $this->db->from('encuesta_pregunta_respuesta_persona eprp');
        $this->db->join('encuesta_pregunta_respuesta epr', 'eprp.encuesta_pregunta_respuesta_id = epr.encuesta_pregunta_respuesta_id', 'inner');
        $this->db->join('encuesta_pregunta ep2', 'epr.encuesta_pregunta_id = ep2.encuesta_pregunta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'eprp.encuesta_persona_id = ep.encuesta_persona_id', 'inner');
        $this->db->join('persona p', 'ep.persona_id = p.persona_id', 'inner');

        $this->db->where('ep.encuesta_programacion_id', $id_programacion);
        $this->db->where('eprp.encuesta_pregunta_respuesta_persona_estado_id = 2', null, false);
        $this->db->order_by('ep.persona_id', 'ASC');
        $query = $this->db->get();
        $data = $query->result();

        return $data;
    }
}
?>