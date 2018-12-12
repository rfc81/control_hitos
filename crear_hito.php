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
//buscamos el ID de usuario segun el funcionario que se loguea en el sistema
mysql_select_db($database_conex, $conex);
$usuario = "SELECT * FROM funcionarios, usuarios WHERE funcionarios.rut='$_SESSION[MM_Username]' AND funcionarios.id_funcionario = usuarios.id_funcionario";
$consulta = mysql_query($usuario, $conex) or die(mysql_error());
$fila_usuario = mysql_fetch_assoc($consulta);

$bandera=0;
$error=array();

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_usuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_usuario = $_SESSION['MM_Username'];
}
mysql_select_db($database_conex, $conex);
$query_usuario = sprintf("SELECT * FROM funcionarios WHERE rut = %s", GetSQLValueString($colname_usuario, "text"));
$usuario = mysql_query($query_usuario, $conex) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "hito")) {
if($_POST['fecha']==''){
  $error[]="<li>Debe ingresar la fecha de reuni&oacute;n *</li>";
  }
if($_POST['fecha_plazo']==''){
  $error[]="<li>Debe ingresar la fecha de plazo *</li>";
  }
if($_POST['hito']==''){
  $error[]="<li>Debe ingresar el nombre del hito *</li>";
  } 
if($_POST['observaciones']==''){
  $error[]="<li>Debe ingresar alguna observacion *</li>";
  }      

$cambio_fecha = new clase_orden_fecha;
$f_reunion=$cambio_fecha->cambio(@$_POST['fecha']);

$cambio_fecha = new clase_orden_fecha;
$f_plazo=$cambio_fecha->cambio(@$_POST['fecha_plazo']);
  

// almacenamos los parametros del archivo
  $tipo = @$_FILES['archivo']['type']; 
  $directorio = "actas/"; 
  
  
  if(!empty($_FILES['archivo']['name'])&&!empty($_FILES['archivo']['type']))
       { 
       $subido = $directorio.basename.($_FILES['archivo']['name']); 
	   move_uploaded_file($_FILES['archivo']['tmp_name'], $subido);	   
       }
}


if(!$error){
	
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "hito")) {
  $insertSQL = sprintf("INSERT INTO hitos (id_categoria, id_estado, id_usuario, id_funcionario, prioridad, asunto, fecha_reunion, fecha_plazo, archivo, tipo_archivo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['categoria'], "int"),
                       GetSQLValueString(1, "int"),
					   GetSQLValueString($fila_usuario["id_usuario"], "int"),
					   GetSQLValueString($_POST['funcionarios'], "int"),
                       GetSQLValueString($_POST['prioridad'], "text"),
                       GetSQLValueString($_POST['hito'], "text"),
                       GetSQLValueString($f_reunion, "date"),
                       GetSQLValueString($f_plazo, "date"),
					   GetSQLValueString($subido, "text"),
					   GetSQLValueString($tipo, "text"));

  mysql_select_db($database_conex, $conex);
  $Result1 = mysql_query($insertSQL, $conex) or die(mysql_error());
  $ultimo_id = mysql_insert_id();  
   
    
  if(!empty($_POST['observaciones'])){
	 $ingresa="INSERT INTO observaciones (id_hito, observacion, id_usuario) 
	 VALUES ('$ultimo_id','$_POST[observaciones]',1)";
	 mysql_select_db($database_conex, $conex);
     $registra_observacion = mysql_query($ingresa, $conex) or die(mysql_error()); 
     }
	 
	 if(!empty($ultimo_id)) { 
	 
	 mysql_select_db($database_conex, $conex);
     $busca_destinatario = "SELECT funcionarios.nombre, funcionarios.paterno, funcionarios.materno, funcionarios.email, categorias.nombre_categoria, hitos.asunto, hitos.prioridad
     FROM funcionarios, categorias, hitos
     WHERE categorias.id_categoria = hitos.id_categoria AND 
           funcionarios.id_funcionario = hitos.id_funcionario AND
		   hitos.id_hito='$ultimo_id'";
     $consulta = mysql_query($busca_destinatario, $conex) or die(mysql_error());
     $row_destinatario = mysql_fetch_assoc($consulta); 

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
  <h2><strong>Asignaci&oacuten Hito Serviu Araucan&iacutea</strong></h2>
  <p>Se ha creado un nuevo hito con la siguiente informaci&oacute;n:</p>
  <br />
  <p><strong> CREADOR DEL HITO: </strong>'.strtoupper($fila_usuario['nombre'])." ".strtoupper($fila_usuario['paterno'])." ".strtoupper($fila_usuario['materno']).'</p>
  <p><strong> RESPONSABLE EJECUCI&Oacute;N: </strong>'.strtoupper($row_destinatario['nombre'])." ".strtoupper($row_destinatario['paterno'])." ".strtoupper($row_destinatario['materno']).'</p>
  <p><strong> NOMBRE DEL HITO: </strong>'.strtoupper($row_destinatario['asunto']).'</p>
  <p><strong> OBSERVACIONES: </strong>'.$_POST['observaciones'].'</p>
  <p><strong> CATEGOR&IacuteA: </strong>'.$row_destinatario['nombre_categoria'].'</p>
  <p><strong> PRIORIDAD: </strong>'.$row_destinatario['prioridad'].'</p>
  <p><strong> FECHA DE PLAZO: </strong>'.$_POST['fecha_plazo'].'</p>
  <br />
 
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
    } 
	 
	 $bandera=1;
   }  
}
 
mysql_select_db($database_conex, $conex);
$query_funcionarios = "SELECT * FROM funcionarios ORDER BY nombre ASC";
$funcionarios = mysql_query($query_funcionarios, $conex) or die(mysql_error());
$row_funcionarios = mysql_fetch_assoc($funcionarios);
$totalRows_funcionarios = mysql_num_rows($funcionarios);

mysql_select_db($database_conex, $conex);
$query_categoria = "SELECT * FROM categorias ORDER BY nombre_categoria ASC";
$categoria = mysql_query($query_categoria, $conex) or die(mysql_error());
$row_categoria = mysql_fetch_assoc($categoria);
$totalRows_categoria = mysql_num_rows($categoria);
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
<div align="right"><h4><i class="fas fa-briefcase" title="Has iniciado sesi&oacute;n correctamente"></i> <?php echo $row_usuario['nombre']." ".$row_usuario['paterno']." ".$row_usuario['materno'];?></h4></div>
<?php if($error){ ?>
<div class='alert alert-danger' role='alert'>
<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong><i class="fa fa-times-circle" aria-hidden="true"></i> Se produjeron los siguientes errores :</strong><br>
<?php for($contador=0; $contador < count($error); $contador++ )
     echo $error[$contador];?>
</div>
<?php }?>
<?php if($bandera==1){ ?>
<div class='alert alert-success' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><i class="fa fa-check-circle" aria-hidden="true"></i> El hito ha sido registrado correctamente.</div>
<?php }?>
<div class="panel panel-primary">
<div class="panel-heading">
<h4><i class="fas fa-plus-circle"></i> Crear Hito</h4>
</div>
<div class="panel-body">
<form name="hito" id="hito" method="POST" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
 
 <!-- Fila 1 -->
  <div class="row">
  <div class="col-sm-4">
  <div class="form-group">  
   <label>Fecha de Reuni&oacute;n</label>
*    
<div class="input-group">
     <div class="input-group-addon">
     <i class="fas fa-calendar-alt" aria-hidden="true"></i>
     </div>
     <input type="text" class="form-control" name="fecha" id="fecha" placeholder="Fecha reuni&oacute;n" readonly>
     </div>
    </div>
  </div>
  
  <div class="col-sm-4">
  <div class="form-group">
  <label>Responsable del Hito</label> 
  *
  <div class="input-group">
  <div class="input-group-addon">
     <i class="fas fa-users" aria-hidden="true"></i>
     </div>
     <select class="form-control" name="funcionarios" id="funcionarios">
       <?php
do {  
?>
       <option value="<?php echo $row_funcionarios['id_funcionario']?>">
	   <?php echo strtoupper($row_funcionarios['nombre']." ".$row_funcionarios['paterno']." ". $row_funcionarios['materno'])?></option>
       <?php
} while ($row_funcionarios = mysql_fetch_assoc($funcionarios));
  $rows = mysql_num_rows($funcionarios);
  if($rows > 0) {
      mysql_data_seek($funcionarios, 0);
	  $row_funcionarios = mysql_fetch_assoc($funcionarios);
  }
?>
     </select>     
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
     
     <select class="form-control" name="prioridad" id="prioridad" placeholder="Prioridad">
       <option value="BAJA">PRIORIDAD BAJA</option>
       <option value="MEDIA">PRIORIDAD MEDIA</option>
       <option value="ALTA">PRIORIDAD ALTA</option>
     </select>     
   
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
     <select class="form-control" name="categoria" id="categoria" placeholder="Categoria">
      <?php
do {  
?><option value="<?php echo $row_categoria['id_categoria']?>"><?php echo $row_categoria['nombre_categoria']?></option>
 <?php
} while ($row_categoria = mysql_fetch_assoc($categoria));
  $rows = mysql_num_rows($categoria);
  if($rows > 0) {
      mysql_data_seek($categoria, 0);
	  $row_categoria = mysql_fetch_assoc($categoria);
  }
?>

     </select>
     </div>
    </div>
    </div>
   
  <div class="col-sm-4">
   <div class="form-group">  
   <label>Fecha de Plazo</label>
*   
<div class="input-group">
   <div class="input-group-addon">
     <i class="fas fa-clock" aria-hidden="true"></i>
   </div>
     <input type="text" class="form-control" name="fecha_plazo" id="fecha_plazo" placeholder="Fecha plazo" readonly>
   </div>
   </div>
   </div>

 
  <div class="col-sm-4">
  <div class="form-group">
    <label for="adjunto"><i class="fas fa-file-upload"></i> Adjuntar Acta Reuni&oacute;n (Formato PDF)</label>
    <input name="archivo" id="archivo" type="file">
    <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
    <!--<p class="help-block">Archivo en formato PDF</p>-->
   </div>
  </div>
 
 </div>  
<!-- Fin Fila 2 -->
  
<!-- Fila 3 -->  
  <div class="row">
 
  <div class="col-sm-8"> 
  <div class="form-group">
   <label>Nombre del Hito (máximo 100 caracteres)</label>
*    
<div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-book" aria-hidden="true"></i>
     </div>
     <input type="text" class="form-control" name="hito" id="hito" placeholder="Ingrese nombre del hito" maxlength="100" 
     onKeyDown="cuenta(), mayuscula(this)" onKeyUp="cuenta(), mayuscula(this)">     
     </div> 
    </div>
   </div>  
  
  <!--CONTADOR-->
  <div class="col-sm-3">
  <div class="form-group">
  <label>Nro. Caracteres Hito</label>
   <div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-calculator" aria-hidden="true"></i>
      </div>
     <input type="text" class="form-control" name="texto"  size=4 readonly="readonly" placeholder="#">
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
*    
<div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-binoculars" aria-hidden="true"></i>
     </div>
     <textarea rows="5" class="form-control" name="observaciones" id="observaciones" placeholder="Ingrese observaciones" maxlength="250" 
     onKeyDown="cuenta_obs(),mayuscula(this)" onKeyUp="cuenta_obs(), mayuscula(this)"></textarea>     
     </div> 
    </div>
   </div>
   
   <!--CONTADOR OBS-->
   <div class="col-sm-3">
  <div class="form-group">
  <label>Nro. Caracteres Observaci&oacute;n</label>
   <div class="input-group">
     <div class="input-group-addon">
     <i class="fa fa-calculator" aria-hidden="true"></i>
      </div>
     <input type="text" class="form-control" name="texto_obs"  size=4 readonly="readonly" placeholder="#">
      </div>
     </div>
    </div>   
   <!--FIN CONTADOR OBS-->  
  </div><!-- Fila 4 -->
   
<!--Fila 5 -->
 <div class="row">
 <div class="col-sm-4">
  <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i>&nbsp; Ingresar Hito</button>
  </div>
 
 </div>
 <input type="hidden" name="MM_insert" value="hito">
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
$('#fecha').datepicker({
				dateFormat: 'dd-mm-yy'});				
});</script>

<script>
$(function(){
$('#fecha_plazo').datepicker({
				dateFormat: 'dd-mm-yy'});				
});</script>
<!-- Input File -->
<script>
$('#adjunto').filestyle({
buttonName : 'btn-info',
buttonText : '<i class="fas fa-folder-open"></i> Seleccionar'
});                        
</script>
</body>
</html>
<?php
mysql_free_result($funcionarios);
mysql_free_result($categoria);
mysql_free_result($usuario);
?>