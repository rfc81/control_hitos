<?php require_once('Connections/conex.php'); ?>
<?php
$hoy=date("d/m/Y");
$file_type = "vnd.ms-excel";
$file_ending = "xls";
header("Content-Type: application/$file_type");
header("Content-Disposition: attachment; filename=reporte_hitos_finalizados_al_$hoy.$file_ending");
header("Pragma: no-cache");
header("Expires: 0");

mysql_select_db($database_conex, $conex);
$query_hitos = "SELECT hitos.id_hito, hitos.asunto, hitos.prioridad, hitos.fecha_reunion, hitos.fecha_plazo, 
funcionarios.nombre, funcionarios.paterno, funcionarios.materno, 
categorias.nombre_categoria 
FROM hitos, funcionarios, categorias
WHERE hitos.id_categoria = categorias.id_categoria AND
hitos.id_funcionario = funcionarios.id_funcionario AND
hitos.id_estado=5 ORDER BY fecha_reunion ASC";
$hitos = mysql_query($query_hitos, $conex) or die(mysql_error());
$row_hitos = mysql_fetch_assoc($hitos);
$totalRows_hitos = mysql_num_rows($hitos);
$x=0;
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
<title>Reporte Hitos Finalizados Serviu Araucania</title>
</head>
<body>
<h2><strong>Reporte Hitos Finalizados al <?php echo $hoy; ?>
</strong></h2>
<h3><?php echo "Registros Encontrados: ".$totalRows_hitos;?></h3>
<table width="100%" border="1">
  <tr bgcolor="#CCCCCC">
    <td width="2%">N°</td>
    <td width="7%">FECHA REUNION</td>
    <td width="23%">NOMBRE DEL HITO</td>
    <td width="10%">CREADOR HITO</td>
    <td width="10%">RESPONSABLE DEL HITO</td>
    <td width="15%">CATEGORIA</td>
    <td width="11%">PRIORIDAD</td>
    <td width="21%">OBSERVACIONES AL INICIO DEL HITO</td>
    <td width="11%">FECHA PLAZO</td>
    <td width="11%">OBSERVACIONES FINALES</td>
    <td width="11%">FECHA TERMINO</td>
  </tr>
  <?php if(!empty($row_hitos)){  
   do { ?>
  <tr>
  
    <td><?php echo @$x=$x+1;?></td>
    <td><?php echo date("d-m-Y",strtotime ($row_hitos['fecha_reunion']));?></td>
    <td><?php echo $row_hitos['asunto']; ?></td>
    <td><?php
	mysql_select_db($database_conex, $conex);
    $query_usuario = "SELECT funcionarios.nombre, funcionarios.paterno, funcionarios.materno
    FROM usuarios, funcionarios, hitos
    WHERE usuarios.id_funcionario = funcionarios.id_funcionario AND
    usuarios.id_usuario = hitos.id_usuario AND
	hitos.id_hito='$row_hitos[id_hito]'";
    $usuario = mysql_query($query_usuario, $conex) or die(mysql_error());
    $row_usuario = mysql_fetch_assoc($usuario);
    $totalRows_usuario = mysql_num_rows($usuario);	
    
	echo strtoupper ($row_usuario['nombre']." ".$row_usuario['paterno']." ".$row_usuario['materno']); ?></td>
    <td><?php echo strtoupper ($row_hitos['nombre']." ".$row_hitos['paterno']." ".$row_hitos['materno']); ?></td>
    <td><?php echo $row_hitos['nombre_categoria']; ?></td>
    <td><?php echo $row_hitos['prioridad']; ?></td>
    <td>
    <?php mysql_select_db($database_conex, $conex);
    $query_observacion = "SELECT observacion
    FROM observaciones, hitos
    WHERE observaciones.id_hito=hitos.id_hito AND
    observaciones.id_hito = '$row_hitos[id_hito]'";
    $observacion = mysql_query($query_observacion, $conex) or die(mysql_error());
    $row_observacion = mysql_fetch_assoc($observacion);
    $totalRows_observacion = mysql_num_rows($observacion);
	if(empty($row_observacion)) {
		echo "SIN OBSERVACIONES";}
		   else { echo $row_observacion['observacion'];
		   }
	?>    
    </td>
    <td><?php echo date("d-m-Y",strtotime ($row_hitos['fecha_plazo'])); ?></td>
    <td>
    <?php mysql_select_db($database_conex, $conex);
    $query_final = "SELECT terminados.observaciones_finales, terminados.fecha_termino
    FROM hitos, terminados
    WHERE hitos.id_hito = terminados.id_hito AND
    terminados.id_hito = '$row_hitos[id_hito]'";
    $final = mysql_query($query_final, $conex) or die(mysql_error());
    $row_final = mysql_fetch_assoc($final);
    $totalRows_final = mysql_num_rows($final);
	echo $row_final['observaciones_finales'];
	?>    
    </td>
    <td><?php echo date("d-m-Y",strtotime ($row_final['fecha_termino'])); ?></td>
  </tr>
<?php  } while ($row_hitos = mysql_fetch_assoc($hitos));}?>
</table>
<p>&nbsp;</p>
</body>
</html>