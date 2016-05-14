<?php require_once('Connections/gopayco.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "activo";
$MM_donotCheckaccess = "false";

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
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?accesscheck=lock";
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

$colname_user = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_user = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_user = sprintf("SELECT * FROM `user` WHERE user_user = %s", GetSQLValueString($colname_user, "int"));
$user = mysql_query($query_user, $gopayco) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

$colname_pregunta = "-1";
if (isset($_POST['id'])) {
  $colname_pregunta = $_POST['id'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_pregunta = sprintf("SELECT * FROM preguntas_seguridad WHERE id_preguntas_seguridad = %s", GetSQLValueString($colname_pregunta, "int"));
$pregunta = mysql_query($query_pregunta, $gopayco) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$totalRows_pregunta = mysql_num_rows($pregunta);

$colname_respuestas = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_respuestas = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_respuestas = sprintf("SELECT * FROM respuesta_seguridad WHERE user_respuesta_seguridad = %s", GetSQLValueString($colname_respuestas, "text"));
$respuestas = mysql_query($query_respuestas, $gopayco) or die(mysql_error());
$row_respuestas = mysql_fetch_assoc($respuestas);
$totalRows_respuestas = mysql_num_rows($respuestas);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php 
$verdaderas = $row_respuestas['verdaderas_respuesta_seguridad']+ 1;
$falsas = $row_respuestas['falsas_respuesta_seguridad'] + 1;
//1 verificar si la respuesta es correcta
if($row_pregunta['respuesta_preguntas_seguridad'] == $_POST['respuesta']){
	//si es correcta
	mysql_select_db($database_gopayco, $gopayco);
$query_respuestas_correcta = "UPDATE respuesta_seguridad SET verdaderas_respuesta_seguridad = '".$verdaderas."' WHERE 	user_respuesta_seguridad = '".$row_user['user_user']."'";
$respuestas_correcta = mysql_query($query_respuestas_correcta, $gopayco) or die(mysql_error());

mysql_select_db($database_gopayco, $gopayco);
$query_respuestas = "UPDATE preguntas_seguridad SET estado_preguntas_seguridad = 'inactivo' WHERE	id_preguntas_seguridad = '".$row_pregunta['id_preguntas_seguridad']."'";
$respuestas = mysql_query($query_respuestas, $gopayco) or die(mysql_error());

if($row_respuestas['verdaderas_respuesta_seguridad'] >= 3){
	
	mysql_select_db($database_gopayco, $gopayco);
$query_userx = "UPDATE user SET intentos_clave_user = '3' WHERE user_user = '".$row_user['user_user']."'";
$userx = mysql_query($query_userx, $gopayco) or die(mysql_error());

mysql_select_db($database_gopayco, $gopayco);
$query_userx_preguntas = "UPDATE preguntas_seguridad SET estado_preguntas_seguridad = 'activo' WHERE user_preguntas_seguridad = '".$row_user['user_user']."'";
$userx_preguntas = mysql_query($query_userx_preguntas, $gopayco) or die(mysql_error());

mysql_select_db($database_gopayco, $gopayco);
$query_userx_respuestas = "UPDATE respuesta_seguridad SET verdaderas_respuesta_seguridad = '0' WHERE user_respuesta_seguridad = '".$row_user['user_user']."'";
$userx_respuestas = mysql_query($query_userx_respuestas, $gopayco) or die(mysql_error());


	header("Location: fin_preguntas.php");
	}
	}else{
		//si es falsa
		mysql_select_db($database_gopayco, $gopayco);
$query_respuestas_falsa = "UPDATE respuesta_seguridad SET verdaderas_respuesta_seguridad = '0', falsas_respuesta_seguridad = '0' WHERE 	user_respuesta_seguridad = '".$row_user['user_user']."'";
$respuestas_falsa = mysql_query($query_respuestas_falsa, $gopayco) or die(mysql_error());


		mysql_select_db($database_gopayco, $gopayco);
$query_respuestas = "UPDATE preguntas_seguridad SET estado_preguntas_seguridad = 'activo' WHERE	user_preguntas_seguridad = '".$row_user['user_user']."'";
$respuestas = mysql_query($query_respuestas, $gopayco) or die(mysql_error());

header("Location: error_preguntas.php");
		}		
		
		
		
		
		?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" style="height:10
0px"><img src="image/loader.gif" alt="" width="128" height="128" /><br />
      Cargando<br />
      Por favor espere</td>
  </tr>
</table>
<meta http-equiv="refresh" content="1;URL=recuperar_clave.php?e=none" />

</body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($pregunta);

mysql_free_result($respuestas);
?>
