<?php require_once('Connections/conex.php'); ?>
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

mysql_select_db($database_conex, $conex);
$query_estados = "SELECT * FROM estados";
$estados = mysql_query($query_estados, $conex) or die(mysql_error());
$row_estados = mysql_fetch_assoc($estados);
$totalRows_estados = mysql_num_rows($estados);

$colname_hitos = "-1";
if (isset($_POST['select_estado'])) {
  $colname_hitos = $_POST['select_estado'];
}
mysql_select_db($database_conex, $conex);
$query_hitos = sprintf("SELECT * FROM hitos, funcionarios, categorias, estados WHERE
hitos.id_funcionario=funcionarios.id_funcionario AND
hitos.id_categoria = categorias.id_categoria AND
hitos.id_estado = estados.id_estado AND
hitos.id_estado = %s", GetSQLValueString($colname_hitos, "int"));
$hitos = mysql_query($query_hitos, $conex) or die(mysql_error());
$row_hitos = mysql_fetch_assoc($hitos);
$totalRows_hitos = mysql_num_rows($hitos);
?>
<html>
<head>
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
<!--Panel consultas -->
	
<div class="panel panel-primary">
	
    <div class="panel-heading">
	<h4><i class="fas fa-search"></i> Consultar Estados</h4>
	</div>
    
    	<div class="panel-body">
    		<form action="<?php echo $editFormAction; ?>" method="post">
            
            <div class="row">
    		<div class="col-sm-4">
    		<div class="form-group">  
   			<label>Seleccione el estado del Hito</label>
    		<div class="input-group">
		    <div class="input-group-addon">
    		<i class="fas fa-user-md" aria-hidden="true"></i>
    		</div>    
    		<select name="select_estado" id="select_estado" class="form-control">
             <?php
            do { ?>
            <option value="<?php echo $row_estados['id_estado']?>"><?php echo $row_estados['nombre_estado']?></option>
 			<?php
			} while ($row_estados = mysql_fetch_assoc($estados));
			  $rows = mysql_num_rows($estados);
			  if($rows > 0) {
		      mysql_data_seek($categoria, 0);
	  			$row_estados = mysql_fetch_assoc($estados);
  			}
			?>
            </select>
    		</div>
    		</div>
    		</div>
            <div class="col-sm-8">
            <span class="label label-success" title="VIGENTE (8 o m&aacute;s d&iacute;as de plazo)">VIGENTE (8 o m&aacute;s d&iacute;as de plazo)</span>
            <span class="label label-info" title="ADVERTENCIA (4-7 d&iacute;as de plazo)">ADVERTENCIA (4-7 d&iacute;as de plazo)</span>
            <span class="label label-warning" title="POR VENCER (1-3 d&iacute;as de plazo)">POR VENCER (1-3 d&iacute;as de plazo)</span>
            <span class="label label-danger" title="FUERA DE PLAZO (0 d&iacute;as de plazo)">FUERA DE PLAZO (0 d&iacute;as de plazo)</span>
            </div>
            </div><!--Fin etiqueta ROW-->
            <div class="row">
            <div class="col-sm-12"></div>
            </div>
            <div class="row">
            <div class="col-sm-4">
            <button type="submit" class="btn btn-primary btn-lg " ><i class="fa fa-search"></i>&nbsp; Consultar</button></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4"></div>
            </div>
         </form>
        </div>
    </div>
    
    
    <div class="panel panel-primary">
	<div class="panel-heading">
	<h4><i class="fas fa-binoculars"></i> Resultados de B&uacute;squeda </h4><br>
	<?php 
	if(empty($row_hitos)){
	echo "";}
	
	if($row_hitos['nombre_estado']=="VIGENTE"){
		echo '<div align="center" class="alert alert-success">'."ESTADO: ".'<strong>'.$row_hitos['nombre_estado'].'</strong>'." (8 o m&aacute;s d&iacute;as de plazo)".'</div>';}
				
	if($row_hitos['nombre_estado']=="ADVERTENCIA"){
		echo '<div align="center" class="alert alert-info">'."ESTADO: ".'<strong>'.$row_hitos['nombre_estado'].'</strong>'." (4-7 d&iacute;as de plazo)".'</div>';}	
	
	if($row_hitos['nombre_estado']=="POR VENCER"){
		echo '<div align="center" class="alert alert-warning">'."ESTADO: ".'<strong>'.$row_hitos['nombre_estado'].'</strong>'." (1-3 d&iacute;as de plazo)".'</div>';}
		
	if($row_hitos['nombre_estado']=="FUERA DE PLAZO"){
		echo '<div align="center" class="alert alert-danger">'."ESTADO: ".'<strong>'.$row_hitos['nombre_estado'].'</strong>'." (0 d&iacute;as de plazo)".'</div>';}	
		
		if($row_hitos['nombre_estado']=="HITO TERMINADO"){
		echo '<div align="center">'."ESTADO: ".'<i class="fas fa-minus-circle"></i> <strong>'.$row_hitos['nombre_estado'].'</strong>'." (El hito ha concluido)".'</div>';}	
	
		?>
	   
    </div>
    	<div class="panel-body">
        <table class="table table-bordered table-hover">
 		<thead class="bg-primary">
        <tr>
	    <td>#</td>
    	<td>Nombre Hito</td>        
	    <td>Prioridad</td>
    	<td>Categoria</td>
	    <td>Responsable</td>
	    <td class="label-danger">Fecha Plazo</td>        
  		</tr>
        </thead>
		<tbody>
          <?php
		  $x=0;		  
		   do { ?>
            <tr>
              <td><?php if(!empty($row_hitos)){ echo $x=$x+1;}else {echo "";}?></td>
              <td><?php echo $row_hitos['asunto'];?><?php if(($row_hitos['archivo']!="")&&($row_hitos['tipo_archivo']!="")){
		   echo " "."<a href='$row_hitos[archivo]'><i class='fas fa-download'></i> </a>";}?></td>
              <td><?php echo $row_hitos['prioridad'];?></td>
              <td><?php echo $row_hitos['nombre_categoria'];?></td>
              <td><?php echo strtoupper($row_hitos['nombre']." ".$row_hitos['paterno']." ".$row_hitos['materno']);?></td>
              <td><?php if(!empty($row_hitos)){ echo date("d-m-Y",strtotime($row_hitos['fecha_plazo']));} else {echo " ";}?></td>
            </tr>
            <?php } while ($row_hitos = mysql_fetch_assoc($hitos)); ?>
        </tbody>
		</table>
      </div>
   </div>
</div><!--fin container-->
<!-- Jquery -->
<script type="text/javascript" src="bootstrap/js/jquery-1.11.3.min.js"></script>
<!-- Bootstrap JS -->
<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
<!-- Funciones -->
<script type="text/javascript" src="bootstrap/js/funciones.js"></script>


</body>
</html>
<?php
mysql_free_result($estados);
mysql_free_result($Recordset1);
?>