<?php require_once('Connections/conex.php'); ?>
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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['rut'])) {
  $loginUsername=$_POST['rut'];
  $password=$_POST['passwd'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_conex, $conex);
  
  $LoginRS__query=sprintf("SELECT funcionarios.rut, usuarios.passwd FROM funcionarios, usuarios
  WHERE rut=%s AND passwd=%s AND
  funcionarios.id_funcionario = usuarios.id_funcionario",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $conex) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
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
<div class="container">
<div class="col-md-4"></div>
<div class="col-md-4"><br /><br />
<div class="well">
<form id="valida_rut" name="valida_rut" method="POST" action="<?php echo $loginFormAction; ?>">
<div align="center">
<img src="bootstrap/img/logo.jpg" width="100" height="100" class="img-thumbnail img-responsive" title="Serviu Región de La Araucanía"/><br />
<h3>Control Hitos Serviu</h3>
</div>
<div class="page-header"></div>
<div class="form-group">
<label for="inputName">Ingrese su RUT </label><span> (Ej: XXXXXXXX-X)</span>
<div class="input-group">
  <span class="input-group-addon"><i class="fas fa-lock"></i></span>
  <input  name="rut" type="text" class="form-control input-lg" id="rut" maxlength="10" placeholder="Ej: xxxxxxxx-x" required autofocus>
</div>
</div>

<div class="form-group">
<label for="inputName">Ingrese su contrase&ntilde;a</label>
<div class="input-group">
  <span class="input-group-addon"><i class="fas fa-key"></i></span>
  <input  name="passwd" type="password" class="form-control input-lg" id="passwd" maxlength="10" placeholder="Ingrese su contrase&ntilde;a" required autofocus>
</div>
</div>





<button class="btn btn-lg btn-primary btn-block" type="submit"><i class="fas fa-sign-in-alt"></i> Acceder</button>
</form>
</div>
</div>
<div class="col-md-4"></div>
</div>
</body>
</html>