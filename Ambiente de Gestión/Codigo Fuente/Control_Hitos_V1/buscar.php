<?php require_once('Connections/conex.php'); ?>
<?php

 
      $buscar = $_POST['b'];
        
      if(!empty($buscar)) {
            buscar($buscar);
      }
        
      function buscar($b) {
            $con = mysql_connect('localhost','root', '');
            mysql_select_db('w3r_alerta', $con);       
            $sql = mysql_query("SELECT * FROM funcionarios, hitos, categorias, estados 
			WHERE hitos.id_categoria=categorias.id_categoria AND
			      hitos.id_estado=estados.id_estado AND
				  hitos.id_funcionario=funcionarios.id_funcionario AND
				  asunto LIKE '%".$b."%' LIMIT 9" ,$con);
		                        
            $contar = @mysql_num_rows($sql);
             
            if($contar == 0){
                  echo "No se han encontrado resultados para '<b>".$b."</b>'.";
            }else{?>
			
            <table class="table table-bordered table-hover">	
			<thead class="bg-primary">
            <tr>
            <td>#</td>
            <td>Nombre Hito</td>
            <td>Prioridad</td>
            <td>Categoria</td>
            <td>Responsable</td>
            <td class=" label-danger">Fecha Plazo</td> 
            <td>Estado</td>            
            </tr>            
            </thead>
            <tbody>	
			<?php $x=0;
              while($row=mysql_fetch_assoc($sql))
			  {	?>
              <tr>
              <td><?php echo $x=$x+1; ?></td>
              <td><?php echo $asunto = $row['asunto'];?><?php if(($row['archivo']!="")&&($row['tipo_archivo']!="")){
		   echo " "."<a href='$row[archivo]'><i class='fas fa-download'></i> </a>";}?></td>
              <td><?php echo $prioridad = $row['prioridad']; ?></td>
              <td><?php echo $categoria = $row['nombre_categoria']; ?></td>
              <td><?php echo strtoupper($funcionario = $row['nombre']." ".$row['paterno']." ".$row['materno']); ?></td>
              <td><?php echo date("d-m-Y",strtotime($plazo = $row['fecha_plazo']));?></td>
              <td>
			  <?php if($row['nombre_estado']=="VIGENTE"){ ?>
              <div class="alert alert-success">
			  <?php echo $categoria = $row['nombre_estado']." <i class='fas fa-smile'></i>";} ?></div>
              
              <?php if($row['nombre_estado']=="ADVERTENCIA"){ ?>
              <div class="alert alert-info">
			  <?php echo $categoria = $row['nombre_estado']." <i class='fas fa-meh'></i>";} ?></div>
              
              <?php if($row['nombre_estado']=="POR VENCER"){ ?>
              <div class="alert alert-warning">
			  <?php echo $categoria = $row['nombre_estado']." <i class='fas fa-meh'></i>";} ?></div>
              
              <?php if($row['nombre_estado']=="FUERA DE PLAZO"){ ?>
              <div class="alert alert-danger">
			  <?php echo $categoria = $row['nombre_estado']." <i class='fas fa-frown'></i>";} ?></div>
              
              <?php if($row['nombre_estado']=="HITO TERMINADO"){ ?>
              <div class="btn btn-default"><i class="fas fa-minus-circle"></i> HITO TERMINADO </div>
			  <?php } ?></div>             
              
              
              </td>
              </tr>			               		
			<?php	
            }?>
			</tbody>
            </table>
			<?php
        }
  }
       
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
</body>
</html>