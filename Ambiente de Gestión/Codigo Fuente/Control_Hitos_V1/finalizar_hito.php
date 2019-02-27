<?php require_once('Connections/conex.php'); ?>
<?php include('bootstrap/clases/cambia_fecha.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
$id_hito=$_GET['id'];

$colname_usuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_usuario = $_SESSION['MM_Username'];
}
mysql_select_db($database_conex, $conex);
$query_usuario = sprintf("SELECT * FROM funcionarios WHERE rut = %s", GetSQLValueString($colname_usuario, "text"));
$usuario = mysql_query($query_usuario, $conex) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);


mysql_select_db($database_conex, $conex);
$query_hito = sprintf("SELECT * FROM hitos,funcionarios,categorias 
WHERE hitos.id_hito = %s AND
hitos.id_funcionario=funcionarios.id_funcionario AND
hitos.id_categoria=categorias.id_categoria", GetSQLValueString($id_hito, "text"));
$hito = mysql_query($query_hito, $conex) or die(mysql_error());
$row_hito = mysql_fetch_assoc($hito);
$totalRows_hito = mysql_num_rows($hito);

mysql_select_db($database_conex, $conex);
$query_observacion = sprintf("SELECT * FROM observaciones WHERE id_hito = %s", GetSQLValueString($id_hito, "text"));
$observacion = mysql_query($query_observacion, $conex) or die(mysql_error());
$row_observacion = mysql_fetch_assoc($observacion);
$totalRows_observacion = mysql_num_rows($observacion);

mysql_select_db($database_conex, $conex);
$query_usuario = sprintf("SELECT * FROM usuarios, funcionarios WHERE usuarios.id_funcionario = funcionarios.id_funcionario AND
funcionarios.rut = %s", GetSQLValueString($_SESSION['MM_Username'], "text"));
$usuario = mysql_query($query_usuario, $conex) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

$bandera=0;
$error=array();

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "hito")) {
if($_POST['fecha_termino']==''){
  $error[]="<li>Debe ingresar la fecha de t&eacute;rmino *</li>";
  }
if($_POST['finales']==''){
  $error[]="<li>Debe ingresar las observaciones finales al cerrar un hito *</li>";
  }
}


if(!$error){
	
$cambio_fecha = new clase_orden_fecha;
$f_termino=$cambio_fecha->cambio(@$_POST['fecha_termino']);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "hito")) {
  $insertSQL = sprintf("INSERT INTO terminados (id_hito, id_usuario, id_estado, observaciones_finales, fecha_termino) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($row_hito['id_hito'], "int"),
                       GetSQLValueString($row_usuario['id_usuario'], "int"),
                       GetSQLValueString(5, "int"),
					   GetSQLValueString($_POST['finales'], "text"),
                       GetSQLValueString($f_termino, "date"));

  mysql_select_db($database_conex, $conex);
  $Result1 = mysql_query($insertSQL, $conex) or die(mysql_error());
  $ultimo_id = mysql_insert_id();
 
  if(!empty($ultimo_id)){
	  mysql_select_db($database_conex, $conex);
           $query_actualiza = "UPDATE hitos SET id_estado=5 WHERE id_hito='$row_hito[id_hito]'";
           $actualiza = mysql_query($query_actualiza, $conex) or die(mysql_error());  		   
	  	  }

     // Varios destinatarios
$para  = $row_destinatario['email'] . ','; // atención a la coma
$cc .= 'hcruz@minvu.cl' . ',';
$ccc .= 'jcfernandez@minvu.cl' . ',';	
$cco .= 'rafuentes@minvu.cl';

// título
$titulo = 'Control de Hitos Serviu Araucania';

// mensaje
$mensaje = '
<div>
  <h2><strong>Finalizaci&oacute;n Hito Serviu Araucania</strong></h2>
  <p>Un hito ha sido finalizado con la siguiente informaci&oacuten: </p>
  <br />
  <p><strong> CREADOR DEL HITO: </strong>'.strtoupper($row_usuario['nombre'])." ".strtoupper($row_usuario['paterno'])." ".strtoupper($row_usuario['materno']).'</p>
  
  <p><strong> RESPONSABLE EJECUCI&OacuteN: </strong>'.strtoupper($row_hito['nombre'])." ".strtoupper($row_hito['paterno'])." ".strtoupper($row_hito['materno']).'</p>
  
  <p><strong> NOMBRE DEL HITO: </strong>'.$row_hito['asunto'].'</p>
  <p><strong> OBSERVACIONES INICIALES: </strong>'.$row_observacion['observacion'].'</p>
  <p><strong> CATEGOR&IacuteA: </strong>'.$row_hito['nombre_categoria'].'</p>
  <p><strong> PRIORIDAD: </strong>'.$row_hito['prioridad'].'</p>
  <p><strong> FECHA DE PLAZO: </strong>'.date("d/m/Y",strtotime ($row_hito['fecha_plazo'])).'</p>
  <p><strong> FECHA DE T&EacuteRMINO: </strong>'.$_POST['fecha_termino'].'</p>
  <p><strong> OBSERVACIONES AL CIERRE DEL HITO: </strong>'.$_POST['finales'].'</p>
  <br /><br />
 
  <p>Para ver el estado de todos los hitos, ingrese aqui: <a href="http://www.serviu9.cl/alertas" title="Aceder al sistema control de hitos Serviu">Sistema Control de Hitos Serviu</a></p>   
</div>';

// Para enviar un correo HTML, debe establecerse la cabecera Content-type
$cabeceras = 
'From: no-responder@serviu9.cl' . "\r\n" .
'Reply-To: webmaster@serviu9.cl' . "\r\n" .
'Content-type: text/html; charset=iso-8859-1' . "\r\n".
'X-Mailer: PHP/' . phpversion();  

// Enviarlo
mail("$para,$cc,$ccc,$cco", $titulo, $mensaje, $cabeceras);
$bandera=1;
    }   
}  //termino ERROR   

 
?>
<html>
<head>
<!-- Meta tags -->
<meta name="description" content="Control de Hitos Serviu Araucania"/>
<meta name="keywords" content="control, hitos, serviu, araucania" />
<meta name="author" content="Ricardo Fuentes For w3r &#8226; Ingenier&iacute;a, Dise&ntilde;o Gr&aacute;fico, y Desarrollo de Sitios Web"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Language" content="es"/>
<meta charset="utf-8">
<meta name="revisit-after" content="15 days"/>
<meta name="robots" content="index,follow"/>
<meta name="distribution" content="global"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<!-- Datepicker -->
<link href="bootstrap/css/datepicker.css" rel="stylesheet">
<title>Control de Hitos Serviu Araucania</title>
</head>
<body>
<!--Barra de menu -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only"><i class="fas fa-home"></i> Control Hitos Serviu</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php"><i class="fas fa-home"></i> Control Hitos Serviu</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
     <ul class="nav navbar-nav navbar-right">
     <li class="dropdown">
     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-hands-helping"></i> Hitos <span class="caret"></span></a>
     <ul class="dropdown-menu">
     <li class="active"><a href="crear_hito.php"><i class="fas fa-plus-circle"></i> Crear Hito <span class="sr-only">Crear Hito</span></a></li>     <li><a href="listar_hitos.php"><i class="fas fa-minus-circle"></i> Finalizar Hito </a></li>     
     </ul>
     </li>   
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-search"></i> Consultar <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="consulta_hitos.php"><i class="fas fa-hands-helping"></i> Hitos</a></li>
            <li><a href="consulta_estados.php"><i class="fas fa-user-md"></i> Estados</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="consulta_funcionario.php"><i class="fas fa-users"></i> Funcionarios</a></li>            
          </ul>          
        </li>
        
         <li class="dropdown">
     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-table">     </i> Reportes <span class="caret"></span></a>
     <ul class="dropdown-menu">
     <li><a href="reporte_hitos_vigentes.php"><i class="fas fa-file-excel"></i> Hitos vigentes</a></li>
     <li><a href="reporte_hitos_finalizados.php"><i class="fas fa-file-excel"></i> Hitos finalizados</a></li>
     </ul>
     </li>  
        
        <li><a href="<?php echo $logoutAction ?>"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<!--Contenido -->
<div class="container-fluid">
<div align="right"><h4><i class="fas fa-briefcase" title="Has iniciado sesi&oacute;n correctamente"></i> <?php echo $row_usuario['nombre']." ".$row_usuario['paterno']." ".$row_usuario['materno']?></h4></div>
<?php if($error){ ?>
<div class='alert alert-danger' role='alert'>
<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong><i class="fa fa-times-circle" aria-hidden="true"></i> Se produjeron los siguientes errores :</strong><br>
<?php for($contador=0; $contador < count($error); $contador++ )
     echo $error[$contador];?>
</div>
<?php }?>
<?php if($bandera==1){ ?>
<div class='alert alert-success' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
<i class="fa fa-check-circle" aria-hidden="true"></i> El hito ha sido finalizado correctamente.</div>
<?php }?>
<div class="panel panel-danger">
<div class="panel-heading">
<h4><i class="fas fa-minus-circle"></i> Finalizar Hito</h4>
</div>
<div class="panel-body">
<form name="hito" id="hito" method="POST" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
 
 <!-- Fila 1 -->
  <div class="row">
  <div class="col-sm-4">
  <div class="form-group">  
   <label>Fecha de Reuni&oacute;n</label>
    <div class="input-group">
     <div class="input-group-addon">
     <i class="fas fa-calendar-alt" aria-hidden="true"></i>
     </div>
     <input type="text" class="form-control" name="fecha" id="fecha" value="<?php echo date("d-m-Y",strtotime($row_hito['fecha_reunion']));?>" disabled>
     </div>
    </div>
  </div>
  
  <div class="col-sm-4">
  <div class="form-group">
  <label>Responsable del Hito</label>
  <div class="input-group">
  <div class="input-group-addon">
     <i class="fas fa-users" aria-hidden="true"></i>
     </div>
     <input type="text" name="responsable" class="form-control" value="<?php echo strtoupper($row_hito['nombre']." ".$row_hito['paterno']." ".$row_hito['materno']);?>" disabled>     
  </div>
 </div>   
</div>
  
  <div class="col-sm-4">  
  <div class="form-group">
   <label>Prioridad</label>
   <div class="input-group">
   <div class="input-group-addon">
     <i class="fas fa-list-ol" aria-hidden="true"></i>
     </div>
    <input type="text" class="form-control" name="prioridad" id="prioridad" value="<?php echo $row_hito['prioridad'];?>" disabled> 
   
   </div>
  </div> 
 </div>  
</div>  
<!-- Fin Fila 1 --> 
    
<!-- Fila 2 --> 
 <div class="row">
  
  <div class="col-sm-4">
  <div class="form-group">  
   <label>Categor&iacute;a</label>
   <div class="input-group">
   <div class="input-group-addon">
    <i class="fas fa-bars" aria-hidden="true"></i>
   </div>
     <input type="text" class="form-control" name="categoria" id="categoria" value="<?php echo $row_hito['nombre_categoria'];?>" disabled>
     </div>
    </div>
    </div>
   
  <div class="col-sm-4">
   <div class="form-group">  
   <label>Fecha de Plazo</label>
   <div class="input-group">
   <div class="input-group-addon">
     <i class="fas fa-clock" aria-hidden="true"></i>
   </div>
     <input type="text" class="form-control" name="fecha_plazo" id="fecha_plazo" value="<?php echo date("d-m-Y",strtotime($row_hito['fecha_plazo']));?>" disabled>
   </div>
   </div>
   </div>

 
  <div class="col-sm-4">
  <div class="form-group">  
   <label><i class="fas fa-info-circle" style="color:red"></i> Fecha de T&eacute;rmino</label>
*   
<div class="input-group">
   <div class="input-group-addon">
     <i class="fas fa-minus-circle" aria-hidden="true"></i>
   </div>
     <input type="text" class="form-control" name="fecha_termino" id="fecha_termino" placeholder="Fecha de t&eacute;rmino">
   </div>
   </div>
  </div>
 
 </div>  
<!-- Fin Fila 2 -->
  
<!-- Fila 3 -->  
  <div class="row">
 
  <div class="col-sm-8"> 
  <div class="form-group">
   <label>Nombre del Hito (máximo 100 caracteres)</label>
    <div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-book" aria-hidden="true"></i>
     </div>
     <input type="text" class="form-control" name="hito" id="hito" value="<?php echo $row_hito['asunto'];?>" disabled>     
     </div> 
    </div>
   </div>  
  
  <!--CONTADOR-->
  <div class="col-sm-4">
  <div class="form-group">
  <label>Nro. Caracteres Observaciones</label>
   <div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-calculator" aria-hidden="true"></i>
      </div>
     <input type="text" class="form-control" name="final" id="final" size=4 readonly="readonly">
      </div>
     </div>
    </div>  
  <!--FIN CONTADOR -->  
  </div><!-- Fin Fila 3 -->

<!-- Fila 4 -->  
  <div class="row">
 
  <div class="col-sm-8"> 
  <div class="form-group">
   <label>Observaciones (máximo 250 caracteres)</label>
    <div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-binoculars" aria-hidden="true"></i>
     </div>
     <textarea rows="5" class="form-control" name="observaciones" id="observaciones" disabled>
     <?php if(!empty($totalRows_observacion))
	 { echo $row_observacion['observacion'];
	   }else{
		   echo "SIN OBSERVACIONES";}?>
     </textarea>     
     </div> 
    </div>
   </div>
   
   <!--CONTADOR OBS-->
   <div class="col-sm-4">
  <div class="form-group">
  <label><i class="fas fa-info-circle" style="color:red"></i> Observaciones Finales * (máximo 150 caracteres)</label>
   <div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-binoculars" aria-hidden="true"></i>
      </div>
     <textarea rows="5" class="form-control" name="finales" id="finales" placeholder="Ingrese observaciones finales" maxlength="150" 
     onKeyDown="cuenta_final(), mayuscula(this)" onKeyUp="cuenta_final(), mayuscula(this)"></textarea>
      </div>
     </div>
    </div>   
   <!--FIN CONTADOR OBS-->  
  </div><!-- Fila 4 -->
   
<!--Fila 5 -->
 <div class="row">
 <div class="col-sm-4">
  <button type="submit" class="btn btn-danger btn-lg"><i class="fas fa-minus-circle"></i>&nbsp; Finalizar Hito</button>
  </div>
 
 </div>
 <input type="hidden" name="MM_update" value="hito">
</form> 
 </div>
 </div>    
</div>
<!-- Jquery -->
<script type="text/javascript" src="bootstrap/js/jquery-1.11.3.min.js"></script>
<!-- Bootstrap JS -->
<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
<!-- Funciones -->
<script type="text/javascript" src="bootstrap/js/funciones.js"></script>
<!-- Datepicker JS -->
<script type="text/javascript" src="bootstrap/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap-filestyle.min.js"></script>
<script>
$(function(){
$('#fecha_termino').datepicker({
				dateFormat: 'dd-mm-yy'});				
});</script>
</body>
</html>
<?php
mysql_free_result($usuario);
?>