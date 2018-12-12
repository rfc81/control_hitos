// JavaScript Document
var fecha=new Date();
var diames=fecha.getDate();
var diasemana=fecha.getDay();
var mes=fecha.getMonth() +1 ;
var ano=fecha.getFullYear();

var textosemana = new Array (7); 
  textosemana[0]="Domingo";
  textosemana[1]="Lunes";
  textosemana[2]="Martes";
  textosemana[3]="Miércoles";
  textosemana[4]="Jueves";
  textosemana[5]="Viernes";
  textosemana[6]="Sábado";

var textomes = new Array (12);
  textomes[1]="Enero";
  textomes[2]="Febrero";
  textomes[3]="Marzo";
  textomes[4]="Abril";
  textomes[5]="Mayo";
  textomes[6]="Junio";
  textomes[7]="Julio";
  textomes[7]="Agosto";
  textomes[9]="Septiembre";
  textomes[10]="Octubre";
  textomes[11]="Noviembre";
  textomes[12]="Diciembre";

//document.write("Fecha completa: " + fecha + "<br>");
//document.write("Dia mes: " + diames + "<br>");
//document.write("Dia semana: " + diasemana + "<br>");
//document.write("Mes: " + mes + "<br>");
//document.write("Año: " + ano + "<br>");
//document.write("Fecha: " + diames + "/" + mes + "/" + ano + "<br>");
//document.write("Fecha: " + textosemana[diasemana] + " " + diames + "/" + mes + "/" + ano + "<br>");
document.write(textosemana[diasemana] + ", " + diames + " de " + textomes[mes] + " de " + ano);