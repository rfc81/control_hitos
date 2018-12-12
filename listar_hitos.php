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

$currentPage = $_SERVER["PHP_SELF"];

$colname_usuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_usuario = $_SESSION['MM_Username'];
}
mysql_select_db($database_conex, $conex);
$query_usuario = sprintf("SELECT * FROM funcionarios WHERE rut = %s", GetSQLValueString($colname_usuario, "text"));
$usuario = mysql_query($query_usuario, $conex) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

$maxRows_consulta_hitos = 10;
$pageNum_consulta_hitos = 0;
if (isset($_GET['pageNum_consulta_hitos'])) {
  $pageNum_consulta_hitos = $_GET['pageNum_consulta_hitos'];
}
$startRow_consulta_hitos = $pageNum_consulta_hitos * $maxRows_consulta_hitos;

mysql_select_db($database_conex, $conex);
$query_consulta_hitos = "SELECT  hitos.id_hito, hitos.archivo, hitos.tipo_archivo, categorias.nombre_categoria,  estados.nombre_estado,  funcionarios.nombre, funcionarios.paterno, funcionarios.materno, prioridad, asunto,  fecha_reunion,  fecha_plazo  
FROM  hitos, categorias, estados, funcionarios, usuarios 
WHERE categorias.id_categoria=hitos.id_categoria AND
estados.id_estado=hitos.id_estado AND
funcionarios.id_funcionario=hitos.id_funcionario AND
usuarios.id_usuario = hitos.id_usuario AND
usuarios.id_funcionario = '$row_usuario[id_funcionario]' AND
hitos.id_estado<5
ORDER BY fecha_plazo DESC";
$query_limit_consulta_hitos = sprintf("%s LIMIT %d, %d", $query_consulta_hitos, $startRow_consulta_hitos, $maxRows_consulta_hitos);
$consulta_hitos = mysql_query($query_limit_consulta_hitos, $conex) or die(mysql_error());
$row_consulta_hitos = mysql_fetch_assoc($consulta_hitos);

if (isset($_GET['totalRows_consulta_hitos'])) {
  $totalRows_consulta_hitos = $_GET['totalRows_consulta_hitos'];
} else {
  $all_consulta_hitos = mysql_query($query_consulta_hitos);
  $totalRows_consulta_hitos = mysql_num_rows($all_consulta_hitos);
}
$totalPages_consulta_hitos = ceil($totalRows_consulta_hitos/$maxRows_consulta_hitos)-1;

$queryString_consulta_hitos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_consulta_hitos") == false && 
        stristr($param, "totalRows_consulta_hitos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_consulta_hitos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_consulta_hitos = sprintf("&totalRows_consulta_hitos=%d%s", $totalRows_consulta_hitos, $queryString_consulta_hitos);
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
<div class="panel panel-primary">
<div class="panel-heading">
<h4><i class="fas fa-hands-helping"></i> Listado de Hitos </h4>
</div>
<div class="panel-body">
<div class="alert alert-info"><i class="fas fa-info-circle"></i> Solo se muestran los hitos que han sido creados por el usuario <strong> <?php echo $row_usuario['nombre']." ".$row_usuario['paterno']." ".$row_usuario['materno'];?></strong></div>
<table class="table table-bordered table-hover table-responsive">
 <thead class=" bg-primary">
    <td>#</td>
    <td>Fecha reuni&oacute;n</td>
    <td>Nombre Hito</td>
    <td>Observaciones</td>
    <td>Prioridad</td>
    <td>Categor&iacute;a</td>
    <td>Responsable</td>
    <td class=" label-danger">Fecha plazo</td>
    <td>Estado</td>
 </thead>
 <tbody>
   <?php 
   $x=0;
   $estado=0;
   ?>
   
   <?php if(!empty($row_consulta_hitos)){  
   do { 
   $fechaHoy = date('Y-m-d');
   $fechaPlazo = $row_consulta_hitos['fecha_plazo'];
   $intervalo = date_diff(date_create($fechaHoy), date_create($fechaPlazo));
   $diferencia = $intervalo->format('%r%a'); //el atributo %r% permite mostrar numeros negativos con a
      
   
   mysql_select_db($database_conex, $conex);
   $query_observaciones = "SELECT observaciones.observacion, observaciones.fecha_observacion  FROM observaciones, hitos 
   WHERE observaciones.id_hito = hitos.id_hito AND observaciones.id_hito='$row_consulta_hitos[id_hito]'";
   $observaciones = mysql_query($query_observaciones, $conex) or die(mysql_error());
   $row_observaciones = mysql_fetch_assoc($observaciones);
   $totalRows_observaciones = mysql_num_rows($observaciones);
   
   ?>
     <tr>       		  
      <td>	 
	   <!-- HTML to write 
      <a href="#" data-toggle="tooltip" title="-->
      <?php echo @$x=$x+1;?>

     <!-- Generated markup by the plugin 
     <div class="tooltip bs-tooltip-top" role="tooltip">
     <div class="arrow"></div>
     <div class="tooltip-inner">    
     </div>
     </div>-->
	 </td>
       <td><?php echo date("d-m-Y",strtotime ($row_consulta_hitos['fecha_reunion']));?></td>
       <td><?php echo $row_consulta_hitos['asunto']; ?><?php if(($row_consulta_hitos['archivo']!="")&&($row_consulta_hitos['tipo_archivo']!="")){
		   echo " "."<a href='$row_consulta_hitos[archivo]'><i class='fas fa-download' title='Ver archivo adjunto'></i> </a>";}?> 
	   </td>
       <td><?php if(empty($row_observaciones)){echo "SIN OBSERVACIONES";}
      else{ echo $row_observaciones['observacion'];}?></td>
       <td><?php echo $row_consulta_hitos['prioridad']; ?></td>
       <td><?php echo $row_consulta_hitos['nombre_categoria']; ?></td>
       <td><?php echo strtoupper($row_consulta_hitos['nombre']);?><?php echo " ".strtoupper($row_consulta_hitos['paterno']); ?><?php echo " ".strtoupper($row_consulta_hitos['materno']); ?></td>
       <td><?php echo  date("d-m-Y",strtotime ($row_consulta_hitos['fecha_plazo'])); ?></td>
       <td><a href="finalizar_hito.php?id=<?php echo $row_consulta_hitos['id_hito'] ?>" class="btn btn-danger" title="Finalizar hito"><i class="fas fa-minus-circle"></i> Finalizar hito</a></td>
     </tr>
     <?php } while ($row_consulta_hitos = mysql_fetch_assoc($consulta_hitos));} ?>
 </tbody>
</table>
<div align="center"> 
<!--Total de registros-->
Registros <?php echo ($startRow_consulta_hitos + 1) ?> a <?php echo min($startRow_consulta_hitos + $maxRows_consulta_hitos, $totalRows_consulta_hitos) ?> de <?php echo $totalRows_consulta_hitos ?><br><br>
<!--Paginacion registros primero-->
<?php if ($pageNum_consulta_hitos > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_consulta_hitos=%d%s", $currentPage, 0, $queryString_consulta_hitos); ?>" title="Primer registro"><i class="fas fa-angle-double-left"></i></a>
  <?php } // Show if not first page ?>
<!--Paginacion registro anterior-->
<?php if ($pageNum_consulta_hitos > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_consulta_hitos=%d%s", $currentPage, max(0, $pageNum_consulta_hitos - 1), $queryString_consulta_hitos); ?>" title="Anterior"><i class="fas fa-angle-left"></i></a>
  <?php } // Show if not first page ?>
<!--Paginacion registro siguiente-->
<?php if ($pageNum_consulta_hitos < $totalPages_consulta_hitos) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_consulta_hitos=%d%s", $currentPage, min($totalPages_consulta_hitos, $pageNum_consulta_hitos + 1), $queryString_consulta_hitos); ?>" title="Siguiente"><i class="fas fa-angle-right"></i></a>
  <?php } // Show if not last page ?>
<!--Paginacion ultimo registro-->
<?php if ($pageNum_consulta_hitos < $totalPages_consulta_hitos) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_consulta_hitos=%d%s", $currentPage, $totalPages_consulta_hitos, $queryString_consulta_hitos); ?>" title="&Uacute;ltimo registro">
  <i class="fas fa-angle-double-right"></i></a>
  <?php } // Show if not last page ?>
</div>
<!--Fin paginacion-->
</div>
</div>
</div>
</div>
<!-- Jquery -->
<script type="text/javascript" src="bootstrap/js/jquery-1.11.3.min.js"></script>
<!-- Bootstrap JS -->
<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
<?php
mysql_free_result($usuario);
mysql_free_result(@$observaciones);
mysql_free_result(@$consulta_hitos);
?>