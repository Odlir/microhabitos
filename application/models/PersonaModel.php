<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PersonaModel extends CI_Model{
    
    public function __construct()
    {
        $this->load->database();
    }

    public function get_persona_encuesta(){
        
        $this->db->select('ep.encuesta_persona_codigo,
        e.encuesta_nombre,
        g.grupo_nombre,
        ep.encuesta_persona_fecha_creacion');
        $this->db->from('encuesta_persona ep');
        $this->db->join('usuario u', 'ep.persona_id = u.persona_id', 'inner');
        $this->db->join('encuesta_programacion ep2', 'ep.encuesta_programacion_id = ep2.encuesta_programacion_id', 'inner');
        $this->db->join('encuesta e', 'ep2.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('grupo g', 'ep2.grupo_id = g.grupo_id', 'inner');
        $this->db->where('u.usuario_id', get_usuario_login_id());
        $this->db->where('ep.encuesta_persona_estado_id', 1);
        $this->db->where('ep2.encuesta_programacion_estado_id', 2);
        

        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }
    
    public function get_personas($filtro_grupo = "", $filtro_estado = ""){
        $estados = array('1', '2');
        $this->db->select('p2.persona_id,
        CASE WHEN p2.persona_apellido_paterno = "" and p2.persona_apellido_materno = "" THEN p2.persona_nombre  ELSE CONCAT(p2.persona_nombre, " ",p2.persona_apellido_paterno, " ", p2.persona_apellido_materno) END as persona_nombre_completo,
        p2.persona_email,
        g.grupo_nombre,
        cm.codigo_maestro_descripcion as persona_estado_str,
        p2.persona_estado_id,
        u.usuario_nombre as persona_usuario_creacion_str,
        p2.persona_fecha_creacion,
        u2.usuario_nombre as persona_usuario_modificacion_str,
        p2.persona_fecha_modificacion');
        $this->db->from('persona p2');
        $this->db->join('codigo_maestro cm', 'p2.persona_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('usuario u', 'p2.persona_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'p2.persona_usuario_modificacion_id = u2.usuario_id', 'left');
        $this->db->join('grupo g', 'p2.grupo_id = g.grupo_id', 'inner');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);

        if ($filtro_grupo != ""){
            $this->db->where('p2.grupo_id', $filtro_grupo);
        }

        if ($filtro_estado != ""){
            $this->db->where('p2.persona_estado_id', $filtro_estado);
        }else{
            $this->db->where_in('p2.persona_estado_id', $estados);
        }
        

        
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function valida_usuario($nombre_usuario){
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->where('usuario_nombre', $nombre_usuario);
        $this->db->where('usuario_estado_id <> 0', null, false);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function valida_grupo($nombre_grupo){
        $estados = array('1', '2');
        $this->db->select('*');
        $this->db->from('grupo');
        $this->db->where('grupo_nombre', $nombre_grupo);
        $this->db->where_in('grupo_estado_id', $estados);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_persona($id){
        $this->db->select('p.*,
        u.usuario_nombre,
        u.usuario_contrasena,
        u.usuario_tipo_id');
        $this->db->from('persona p');
        $this->db->join('usuario u', 'p.persona_id = u.persona_id', 'left');
        $this->db->where('p.persona_id', $id);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function persona_actualizar($id, $data){
        
        $this->db->set('persona_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('persona_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('persona_id', $id);
        $this->db->update('persona', $data);
        return true;
    }

    public function persona_insertar($data){
        
        $this->db->set('persona_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('persona_fecha_creacion', 'NOW()', FALSE);
        $this->db->set('persona_tiene_encuesta', 0);
        $this->db->insert('persona', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function usuario_actualizar($id, $data){
        
        $this->db->set('usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('usuario_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('persona_id', $id);
        $this->db->update('usuario', $data);
        return true;
    }

    public function usuario_insertar($data){
        
        $this->db->set('usuario_creacion_id', get_usuario_login_id());
        $this->db->set('usuario_fecha_creacion', 'NOW()', FALSE);
        $this->db->insert('usuario', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function usuario_eliminar($id){
        
        $this->db->set('usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('usuario_fecha_modificacion', 'NOW()', FALSE);
        $this->db->set('usuario_estado_id', 0);
        $this->db->where('persona_id', $id);
        $this->db->update('usuario');
        return true;
    }

    public function persona_eliminar($id){
        
        $this->db->set('persona_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('persona_estado_id', 0);
        $this->db->set('persona_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('persona_id', $id);
        $this->db->update('persona');
        return true;
    }

    public function actualizar_persona_encuesta($codigo){

        //$this->db->set('encuesta_persona_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_persona_estado_id', 2);
        $this->db->set('encuesta_persona_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_persona_codigo', $codigo);
        $this->db->update('encuesta_persona');
        return true;
    }
}
?>