// JavaScript Document

/* Funcion para convertir a fecha formato mysql

function FormatoFecha($fecha){ 
@list($anio,$mes,$dia)= @explode("-",$fecha); 
return $dia."-".$mes."-".$anio; 
	}
	
** en esta linea se llama a la funcion:
<?php echo FormatoFecha($row_carrera['nacimiento']); ?>


Script para colocar en el HEADER y exportar a EXCEL

$file_type = "vnd.ms-excel";
$file_ending = "xls";
header("Content-Type: application/$file_type");
header("Content-Disposition: attachment; filename=cumple_egresados.$file_ending");
header("Pragma: no-cache");
header("Expires: 0");

*/
function limpiar(form){
	document.form.reset();
    return false;	
	}


function puntitos(donde,caracter){
	pat = /[\*,\+,\(,\),\?,\,$,\[,\],\^]/
	valor = donde.value
	largo = valor.length
	crtr = true
	if(isNaN(caracter) || pat.test(caracter) == true){
		if (pat.test(caracter)==true){ 
			caracter = "\\" + caracter
		}
		carcter = new RegExp(caracter,"g")
		valor = valor.replace(carcter,"")
		donde.value = valor
		crtr = false
	}
	else{
		var nums = new Array()
		cont = 0
		for(m=0;m<largo;m++){
			if(valor.charAt(m) == "." || valor.charAt(m) == " ")
				{continue;}
			else{
				nums[cont] = valor.charAt(m)
				cont++
			}
		}
	}
	var cad1="",cad2="",tres=0
	if(largo > 3 && crtr == true){
		for (k=nums.length-1;k>=0;k--){
			cad1 = nums[k]
			cad2 = cad1 + cad2
			tres++
			if((tres%3) == 0){
				if(k!=0){
					cad2 = "." + cad2
				}
			}
		}
		donde.value = cad2
	}
}	 




function formato(input)
{
var num = input.value.replace(/\./g,'');
if(!isNaN(num)){
num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
num = num.split('').reverse().join('').replace(/^[\.]/,'');
input.value = num;
}
  
else{ alert('Solo se permiten numeros');
input.value = input.value.replace(/[^\d\.]*/g,'');
}
}


function validamail()
            {
                var s = document.form1.email.value;
                var filter=/^[A-Za-z][.A-Za-z0-9_]*@[A-Za-z0-9_]+.[A-Za-z0-9_.]+[A-za-z]$/;
                if (s.length == 0 ) return true;
                if (filter.test(s))
                    return true;
                else
				{
                    alert("Escriba una direccion de E-mail valida");
					document.form1.email.value="";
					document.form1.email.focus();
                }
                return false;
            }
			
function s_inv(campo,opcion){	
	if(opcion.checked==true){
		campo.value="S/INV";
		campo.readOnly=true;
		}else{campo.value=" ";
		campo.readOnly=false;		
	  }
}			

function habilitar()
            {
               submit.disabled="enabled";
            }

function aviso()
            {
               alert("Los datos fueron modificados correctamente");
            }
			
function eliminar()
            {
               alert("El registro ha sido eliminado de la Base de datos");
            }
			
function confirmar()
{
var seguro=confirm("¿Esta seguro que desea eliminar este registro?\n La informacion no podra ser recuperada.");
if (seguro){
	return eliminar();
	return true;
}
else
return false ;
}			
	
	
function vacio(q) {  
        for ( i = 0; i < q.length; i++ ) {  
                if ( q.charAt(i) != " " ) {  
                        return true  
                }  
        }  
        return false  
}  
  
function valida(F) {  
          
        if( vacio(F.campo.value) == false ) {  
                alert("Este campo no puede quedar vacío!")  
                return false  
        } else {  
                return true
        }            
} 

function obligatorio(campo){
	if(campo.value==""){
		alert("Este campo es obligatorio, porfavor complete la información solicitada");
		campo.focus();
		}
	
	} 			
	
			
function cursor()
            {
              document.login.user.focus();
            }			
function registro()
            {
               alert("Los datos fueron registrados correctamente");
            }


function minuscula(field)
            {
                field.value = field.value.toLowerCase()
            }
			
			
function mayuscula(field)
            {
                field.value = field.value.toUpperCase()
            }

function numerico(field)
            {
                if(isNaN(field.value)){
                    alert("Debe ingresar solo numeros");
					field.value="";
					field.focus();				    
					}
            }

function completo(field,form)
            {
                if(field.value==""){
				    alert("Debe seleccionar un archivo para subir\nEl tamaño maximo del archivo debe ser de 5 MB");
					return false;					
					}
					if(field.value!=""){
                    form.submit();
					}

            }	
			
			
function cuenta(){ 
      document.forms[0].texto.value=document.forms[0].hito.value.length 
}

function cuenta_final(){ 
      document.forms[0].final.value=document.forms[0].finales.value.length 
} 

function cuenta_obs(){ 
      document.forms[0].texto_obs.value=document.forms[0].observaciones.value.length 
} 			
			
function cargar_estado(field,form,fecha)
            {
                if(field.value==""){
				    alert("Debe seleccionar un archivo para subir\nEl tamaño maximo del archivo debe ser de 5 MB");
					return false;					
					}
				if(fecha.value==""){
				    alert("Falta ingresar la fecha de pago del Estado de Pago");
					return false;					
					}	
					if(field.value!="" && fecha.value!=""){
                    form.submit();
					}

            }						
			

function Valida_Rut(rut)
            {
                var tmpstr = "";
                var intlargo = rut.value
                if (intlargo.length> 0)
                {
                    crut = rut.value
                    largo = crut.length;
                    if ( largo <2 )
                    {
                        alert('rut invalido')
                        rut.focus()
                        return false;
                    }
                    for ( i=0; i < crut.length ; i++ ){
                        if ( crut.charAt(i) != ' ' && crut.charAt(i) != '.' && crut.charAt(i) != '-' )
                        {
                            tmpstr = tmpstr + crut.charAt(i);
                        }
                    }
                    rut = tmpstr;
                    crut=tmpstr;
                    largo = crut.length;

                    if ( largo> 2 )
                        rut = crut.substring(0, largo - 1);
                    else
                        rut = crut.charAt(0);

                    dv = crut.charAt(largo-1);

                    if ( rut == null || dv == null )
                        return 0;

                    var dvr = '0';
                    suma = 0;
                    mul  = 2;

                    for (i= rut.length-1 ; i>= 0; i--)
                    {
                        suma = suma + rut.charAt(i) * mul;
                        if (mul == 7)
                            mul = 2;
                        else
                            mul++;
                    }

                    res = suma % 11;
                    if (res==1)
                        dvr = 'k';
                    else if (res==0)
                        dvr = '0';
                    else
                    {
                        dvi = 11-res;
                        dvr = dvi + "";
                    }

                    if ( dvr != dv.toLowerCase() )
                    {
                        alert("El Rut Ingresado es Invalido");
                        document.form1.rut.value="";
						document.form1.rut.focus();
                        return false;}

                    return true;
             }
       }

function puntitos(donde,caracter){
	pat = /[\*,\+,\(,\),\?,\,$,\[,\],\^]/
	valor = donde.value
	largo = valor.length
	crtr = true
	if(isNaN(caracter) || pat.test(caracter) == true){
		if (pat.test(caracter)==true){ 
			caracter = "\\" + caracter
		}
		carcter = new RegExp(caracter,"g")
		valor = valor.replace(carcter,"")
		donde.value = valor
		crtr = false
	}
	else{
		var nums = new Array()
		cont = 0
		for(m=0;m<largo;m++){
			if(valor.charAt(m) == "." || valor.charAt(m) == " ")
				{continue;}
			else{
				nums[cont] = valor.charAt(m)
				cont++
			}
		}
	}
	var cad1="",cad2="",tres=0
	if(largo > 3 && crtr == true){
		for (k=nums.length-1;k>=0;k--){
			cad1 = nums[k]
			cad2 = cad1 + cad2
			tres++
			if((tres%3) == 0){
				if(k!=0){
					cad2 = "." + cad2
				}
			}
		}
		donde.value = cad2
	}
}	

