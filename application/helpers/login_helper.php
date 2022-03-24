<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function is_logged_in() {
    // Get current CodeIgniter instance
    $CI =& get_instance();
    // We need to use $CI->session instead of $this->session
    $user = $CI->session->userdata('user_data');
    if (!isset($user)) { return false; } else { return true; }
}


function can_edited($idUsuario) {
    // Get current CodeIgniter instance
    $CI =& get_instance();
    // We need to use $CI->session instead of $this->session
    $user = $CI->session->userdata('user_data');

    if (!isset($user)) {
        return false; 
    } 
    else { 

        $sUsuario = $user["usuario_id"];
        $sTipo = $user["usuario_tipo_id"];

        if($sTipo == 1){
            return true; 
        }else if ($sUsuario == $idUsuario){
            return true; 
        }else{
            return false;
        }
    }
}

function get_usuario_login_id(){
    $CI =& get_instance();
    $user_data = $CI->session->userdata('user_data');

    if (isset($user_data)) {
        return $user_data["usuario_id"];
    }else{
        return "1";
    }
}

function get_usuario_persona_id(){
    $CI =& get_instance();
    $user_data = $CI->session->userdata('user_data');

    if (isset($user_data)) {
        if($user_data["persona_id"] == ""){
            return "1";
        }else{
            return $user_data["persona_id"];
        }
        
    }else{
        return "1";
    }
}

function get_usuario_login_tipo(){
    $CI =& get_instance();
    $user_data = $CI->session->userdata('user_data');

    if (isset($user_data)) {
        return $user_data["usuario_tipo_id"];
    }else{
        return "-1";
    }
}