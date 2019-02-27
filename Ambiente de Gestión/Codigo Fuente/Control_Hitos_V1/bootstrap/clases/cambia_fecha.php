<?php
class clase_orden_fecha 
{

function cambio($fecha_nac)
{
	$dia=substr($fecha_nac,0,2);
    $mes=substr($fecha_nac,3,2);
    $ano=substr($fecha_nac,6,4);
   
    $fecha=$ano."-".$mes."-".$dia;
	
	return $fecha;
} // fin funcion

function cambio_f($fecha_nac)
{
	$ano=substr($fecha_nac,6,4);
	$mes=substr($fecha_nac,3,2);
    $dia=substr($fecha_nac,0,2);
   
    $fecha=$dia."-".$mes."-".$ano;
	
	return $fecha;
} // fin funcion

} // fin clase



?>
