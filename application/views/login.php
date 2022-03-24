<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ingreso – Effectus Fischman</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
      <!-- Meta -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="description" content="Gradient Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
      <meta name="keywords" content="bootstrap, bootstrap admin template, admin theme, admin dashboard, dashboard template, admin template, responsive" />
      <meta name="author" content="Phoenixcoded" />
      <!-- Favicon icon -->

      <link rel="icon" href="<?php echo base_url('files/assets/images/favicon.ico'); ?>" type="image/x-icon">
      <!-- Google font-->     
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/bower_components/bootstrap/css/bootstrap.min.css'); ?>">
      <!-- waves.css -->
      <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('files/assets/pages/waves/css/waves.min.css'); ?>" >
      <!-- feather icon -->
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/assets/icon/feather/css/feather.css'); ?>">
      <!-- themify-icons line icon -->
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/assets/icon/themify-icons/themify-icons.css'); ?>">
      <!-- ico font -->
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/assets/icon/icofont/css/icofont.css'); ?>">
      <!-- Font Awesome -->
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/assets/icon/font-awesome/css/font-awesome.min.css'); ?>">
      <!-- Style.css -->
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/assets/css/style.css'); ?>">
      <link rel="stylesheet" type="text/css" href="<?php echo base_url('files/assets/css/pages.css'); ?>">
  </head>

  <body style="background: #1C7BD1;">
  <!-- Pre-loader start -->
  <div class="theme-loader">
      <div class="loader-track">
          <div class="preloader-wrapper">
              <div class="spinner-layer spinner-blue">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
              <div class="spinner-layer spinner-red">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
            
              <div class="spinner-layer spinner-yellow">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
            
              <div class="spinner-layer spinner-green">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- Pre-loader end -->
    <section class="login-block">
        <!-- Container-fluid starts -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Authentication card start -->
                    <form class="md-float-material form-material" method="post" action="<?=site_url('login/validation')?>">
                        
                        <div class="auth-box card">
                            <div class="card-block" style="padding: 40px;">
                                <div class="row m-b-20">
                                    <div class="col-md-12 text-center" >
                                        <img style="margin-bottom: 40px;" src="<?php echo base_url('files/assets/images/logo_login.png'); ?>" alt="logo_login.png">
                                        <h3 class="txt-primary">Bienvenido</h3>
                                    </div>
                                </div>
                                <p class="text-muted text-center p-b-5">Ingrese sus datos para iniciar sesión</p>
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                    <?php
                                        if($this->session->flashdata('message'))
                                        {
                                            echo '<div class="alert alert-danger">'.$this->session->flashdata("message").'</div>';
                                            $this->session->set_flashdata('message', '');
                                        }
                                    ?>
                                    </div>
                                </div>
                                <div class="form-group form-primary">
                                    <input type="text" name="user_nombre" id="user_nombre" class="form-control" >
                                    <span class="form-bar"></span>
                                    <span class="text-danger"><?php echo form_error('user_nombre'); ?></span>
                                    <label class="float-label">Usuario</label>
                                </div>
                                <div class="form-group form-primary">
                                    <input type="password" name="user_password" id="user_password" class="form-control" >
                                    <span class="form-bar"></span>
                                    <span class="text-danger"><?php echo form_error('user_password'); ?></span>
                                    <label class="float-label">Contraseña</label>
                                </div>
                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">INGRESAR</button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                        <!-- end of form -->
                    </div>
                    <!-- Authentication card end -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <footer>
                <p class="text-center" style="color: white; margin-top: 20px;">Todos los derechos reservados &copy; 2021 Effectus Fischman</p>
            </footer>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>
    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="<?php echo base_url('files/assets/images/browser/chrome.png'); ?>" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="<?php echo base_url('files/assets/images/browser/firefox.png'); ?>" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="<?php echo base_url('files/assets/images/browser/opera.png'); ?>" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="<?php echo base_url('files/assets/images/browser/safari.png'); ?>" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="<?php echo base_url('files/assets/images/browser/ie.png'); ?>" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
<!-- Warning Section Ends -->
<!-- Required Jquery -->
<script type="text/javascript" src="<?php echo base_url('files/bower_components/jquery/js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('files/bower_components/jquery-ui/js/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('files/bower_components/popper.js/js/popper.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('files/bower_components/bootstrap/js/bootstrap.min.js'); ?>"></script>
<!-- waves js -->
<script src="<?php echo base_url('files/assets/pages/waves/js/waves.min.js'); ?>"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="<?php echo base_url('files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js'); ?>"></script>
<!-- modernizr js -->
<script type="text/javascript" src="<?php echo base_url('files/bower_components/modernizr/js/modernizr.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('files/bower_components/modernizr/js/css-scrollbars.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('files/assets/js/common-pages.js'); ?>"></script>
</body>

</html>
