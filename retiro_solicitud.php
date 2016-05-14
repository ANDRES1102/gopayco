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

$colname_retiro = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_retiro = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_retiro = sprintf("SELECT * FROM saldos WHERE prioridad_saldos = 'Primario' AND  user_saldos = %s", GetSQLValueString($colname_retiro, "int"));
$retiro = mysql_query($query_retiro, $gopayco) or die(mysql_error());
$row_retiro = mysql_fetch_assoc($retiro);
$totalRows_retiro = mysql_num_rows($retiro);

mysql_select_db($database_gopayco, $gopayco);
$query_retiro_minimo = "SELECT * FROM retiro_minimo";
$retiro_minimo = mysql_query($query_retiro_minimo, $gopayco) or die(mysql_error());
$row_retiro_minimo = mysql_fetch_assoc($retiro_minimo);
$totalRows_retiro_minimo = mysql_num_rows($retiro_minimo);

mysql_select_db($database_gopayco, $gopayco);
$query_cliente = "SELECT * FROM cliente WHERE cliente_cliente = '".$_POST['selectmenu']."'";
$cliente = mysql_query($query_cliente, $gopayco) or die(mysql_error());
$row_cliente = mysql_fetch_assoc($cliente);
$totalRows_cliente = mysql_num_rows($cliente);

$colname_user = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_user = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_user = sprintf("SELECT * FROM `user` WHERE user_user = %s", GetSQLValueString($colname_user, "int"));
$user = mysql_query($query_user, $gopayco) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);
?>
<doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gopayco</title>
<!-- 
Para obtener más información sobre los comentarios condicionales situados alrededor de las etiquetas html en la parte superior del archivo:
paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/
  
Haga lo siguiente si usa su compilación personalizada de modernizr (http://www.modernizr.com/):
* inserte el vínculo del código js aquí
* elimine el vínculo situado debajo para html5shiv
* añada la clase "no-js" a las etiquetas html en la parte superior
* también puede eliminar el vínculo con respond.min.js si ha incluido MQ Polyfill en su compilación de modernizr 
-->
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</script>
<style>
body{
	background-color:#CCC}
</style>
<link href="jquery-mobile/jquery.mobile-1.0.min.css" rel="stylesheet" type="text/css">
<script src="js/ajax.js" type="text/javascript"></script>
<script src="jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
</head>
<body>
<?php

if($_POST['clave'] === $row_user['clave_user']){
$resta = $row_retiro['valor_saldos'] - $_POST['textinput'];
mysql_select_db($database_gopayco, $gopayco);
$query_retirox = "UPDATE saldos SET valor_saldos = '".$resta."' WHERE id_saldos = '".$row_retiro['id_saldos']."'";
$retirox = mysql_query($query_retirox, $gopayco) or die(mysql_error());

$concepto = 'Solicitud de Retiro de Fondos al Banco '.$row_cliente['nombre_cliente'].' numero de cuenta '.$_POST['textinput2'];
mysql_select_db($database_gopayco, $gopayco);
$query_movimientos = "INSERT INTO movimientos_cuenta (user_movimientos_cuenta, cliente_movimientos_cuenta, concepto_movimientos_cuenta, haber_movimientos_cuenta, moneda_movimientos_cuenta, fecha_movimientos_cuenta)VALUES(
'".$row_user['user_user']."', 
'".$row_cliente['cliente_cliente']."', 
'".$concepto."', 
'".$_POST['textinput']."', 
'".$row_retiro_minimo['moneda_retiro_minimo']."', 
'".date('Y-m-d')."'
)";
$movimientos = mysql_query($query_movimientos, $gopayco) or die(mysql_error());


mysql_select_db($database_gopayco, $gopayco);
$query_solicituds = "INSERT INTO solicitud_retiro (user_solicitud_retiro, banco_solicitud_retiro, valor_solicitud_retiro, fecha_solicitud_retiro) VALUES ('".$row_user['user_user']."', '".$row_cliente['cliente_cliente']."', '".$_POST['textinput']."', '".date('Y-m-d')."')";
$solicituds = mysql_query($query_solicituds, $gopayco) or die(mysql_error());

mysql_select_db($database_gopayco, $gopayco);
$query_clave = "UPDATE user SET intentos_clave_user = '3' WHERE id_user = '".$row_user['id_user']."'";
$clave = mysql_query($query_clave, $gopayco) or die(mysql_error());


header("Location: fin_retiro.php");

}else{
	
	$resta = $row_user['intentos_clave_user'] - 1;
		mysql_select_db($database_gopayco, $gopayco);
$query_clave = "UPDATE user SET intentos_clave_user = '".$resta."' WHERE id_user = '".$row_user['id_user']."'";
$clave = mysql_query($query_clave, $gopayco) or die(mysql_error());


	header("Location: operaciones/error_clave.php");
	}
?>

</div>
</body>
</html>
<?php
mysql_free_result($retiro);

mysql_free_result($retiro_minimo);

mysql_free_result($cliente);

mysql_free_result($user);
?>
