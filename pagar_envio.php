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

$colname_saldo_user = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldo_user = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldo_user = sprintf("SELECT * FROM saldos WHERE user_saldos = %s AND prioridad_saldos = 'Primario'", GetSQLValueString($colname_saldo_user, "int"));
$saldo_user = mysql_query($query_saldo_user, $gopayco) or die(mysql_error());
$row_saldo_user = mysql_fetch_assoc($saldo_user);
$totalRows_saldo_user = mysql_num_rows($saldo_user);

$colname_saldo_recibe = "-1";
if (isset($_POST['usuario'])) {
  $colname_saldo_recibe = $_POST['usuario'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldo_recibe = sprintf("SELECT * FROM saldos WHERE user_saldos = %s AND prioridad_saldos = 'Primario'", GetSQLValueString($colname_saldo_recibe, "int"));
$saldo_recibe = mysql_query($query_saldo_recibe, $gopayco) or die(mysql_error());
$row_saldo_recibe = mysql_fetch_assoc($saldo_recibe);
$totalRows_saldo_recibe = mysql_num_rows($saldo_recibe);

mysql_select_db($database_gopayco, $gopayco);
$query_comision_user = "SELECT * FROM comision_user WHERE minimo_valor_comision_user <= ".$_POST['valor']." AND maximo_valor_comision_user >= ".$_POST['valor']."";
$comision_user = mysql_query($query_comision_user, $gopayco) or die(mysql_error());
$row_comision_user = mysql_fetch_assoc($comision_user);
$totalRows_comision_user = mysql_num_rows($comision_user);

mysql_select_db($database_gopayco, $gopayco);
$query_user = "SELECT * FROM `user` WHERE user_user = '".$row_saldo_user['user_saldos']."'";
$user = mysql_query($query_user, $gopayco) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

mysql_select_db($database_gopayco, $gopayco);
$query_user_recibe = "SELECT * FROM `user` WHERE user_user = '".$row_saldo_recibe['user_saldos']."'";
$user_recibe = mysql_query($query_user_recibe, $gopayco) or die(mysql_error());
$row_user_recibe = mysql_fetch_assoc($user_recibe);
$totalRows_user_recibe = mysql_num_rows($user_recibe);

mysql_select_db($database_gopayco, $gopayco);
$query_saldo_gopayco = "SELECT * FROM saldo_gopayco";
$saldo_gopayco = mysql_query($query_saldo_gopayco, $gopayco) or die(mysql_error());
$row_saldo_gopayco = mysql_fetch_assoc($saldo_gopayco);
$totalRows_saldo_gopayco = mysql_num_rows($saldo_gopayco);

$colname_saldo_envia = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldo_envia = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldo_envia = sprintf("SELECT * FROM saldos WHERE prioridad_saldos = 'Primario' AND user_saldos = '".$row_saldo_user['user_saldos']."'", GetSQLValueString($colname_saldo_envia, "int"));
$saldo_envia = mysql_query($query_saldo_envia, $gopayco) or die(mysql_error());
$row_saldo_envia = mysql_fetch_assoc($saldo_envia);
$totalRows_saldo_envia = mysql_num_rows($saldo_envia);

$colname_user_yo = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_user_yo = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_user_yo = sprintf("SELECT * FROM `user` WHERE user_user = %s", GetSQLValueString($colname_user_yo, "int"));
$user_yo = mysql_query($query_user_yo, $gopayco) or die(mysql_error());
$row_user_yo = mysql_fetch_assoc($user_yo);
$totalRows_user_yo = mysql_num_rows($user_yo);

$colname_mi_saldo = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_mi_saldo = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_mi_saldo = sprintf("SELECT * FROM saldos WHERE prioridad_saldos = 'Primario' AND user_saldos = %s", GetSQLValueString($colname_mi_saldo, "int"));
$mi_saldo = mysql_query($query_mi_saldo, $gopayco) or die(mysql_error());
$row_mi_saldo = mysql_fetch_assoc($mi_saldo);
$totalRows_mi_saldo = mysql_num_rows($mi_saldo);
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
<script src="js/ajax.js" type="text/javascript"></script>
</head>
<body>
<?php 
if($_POST['clave'] === $row_user['clave_user']){
	
if($row_mi_saldo['valor_saldos'] >= $_POST['valor']){
	
$suma_recibe = $row_saldo_recibe['valor_saldos']+$_POST['valor']-($_POST['valor']*$row_comision_user['valor_comision_user']/100);
mysql_select_db($database_gopayco, $gopayco);
$query_dinero_recibe = "UPDATE saldos SET valor_saldos = '".$suma_recibe."' WHERE id_saldos = '".$row_saldo_recibe['id_saldos']."'";
$dinero_recibe = mysql_query($query_dinero_recibe, $gopayco) or die(mysql_error());


$suma_envia =  $row_saldo_envia['valor_saldos']- $_POST['valor'];
mysql_select_db($database_gopayco, $gopayco);
$query_dinero_envia = "UPDATE saldos SET valor_saldos = '".$suma_envia."' WHERE id_saldos = '".$row_saldo_envia['id_saldos']."'";
$dinero_envia = mysql_query($query_dinero_envia, $gopayco) or die(mysql_error());

$suma_empresa = $row_saldo_gopayco['valor_saldo_gopayco'] +$_POST['valor']*$row_comision_user['valor_comision_user']/100;
mysql_select_db($database_gopayco, $gopayco);
$query_dinero_empresa = "UPDATE saldo_gopayco SET valor_saldo_gopayco = '".$suma_empresa."'";
$dinero_empresa = mysql_query($query_dinero_empresa, $gopayco) or die(mysql_error());

$suma_recibex = $_POST['valor']-($_POST['valor']*$row_comision_user['valor_comision_user']/100);


mysql_select_db($database_gopayco, $gopayco);
$query_movimiento_cuenta = "INSERT INTO movimientos_cuenta (user_movimientos_cuenta, cliente_movimientos_cuenta, concepto_movimientos_cuenta, haber_movimientos_cuenta, 	moneda_movimientos_cuenta, fecha_movimientos_cuenta)VALUES('".$row_saldo_user['user_saldos']."', '".$_POST['usuario']."', 'Pago enviado a ".$row_user_recibe['nombres_user']."', '".$_POST['valor']."', '".$row_saldo_user['moneda_saldos']."', '".date('Y-m-d')."')";
$movimiento_cuenta = mysql_query($query_movimiento_cuenta, $gopayco) or die(mysql_error());

mysql_select_db($database_gopayco, $gopayco);
$query_movimiento_cuenta_recibe = "INSERT INTO movimientos_cuenta (user_movimientos_cuenta, cliente_movimientos_cuenta, concepto_movimientos_cuenta, debe_movimientos_cuenta, 	moneda_movimientos_cuenta, fecha_movimientos_cuenta)VALUES('".$_POST['usuario']."', '".$row_saldo_user['user_saldos']."', 'Pago recibido de ".$row_user['nombres_user']."', '".$suma_recibex."', '".$row_saldo_user['moneda_saldos']."', '".date('Y-m-d')."')";
$movimiento_cuenta_recibe = mysql_query($query_movimiento_cuenta_recibe, $gopayco) or die(mysql_error());

mysql_select_db($database_gopayco, $gopayco);
$query_clave = "UPDATE user SET intentos_clave_user = '3' WHERE id_user = '".$row_user_yo['id_user']."'";
$clave = mysql_query($query_clave, $gopayco) or die(mysql_error());

header("Location: pago_enviado.php");
}else{
	header("Location: operaciones/error_saldo.php");
	}
	}else{
		
		$resta = $row_user_yo['intentos_clave_user'] - 1;
		mysql_select_db($database_gopayco, $gopayco);
$query_clave = "UPDATE user SET intentos_clave_user = '".$resta."' WHERE id_user = '".$row_user_yo['id_user']."'";
$clave = mysql_query($query_clave, $gopayco) or die(mysql_error());

		
	header("Location: operaciones/error_clave.php");
	}
?>
</body>
</html>
<?php
mysql_free_result($saldo_user);

mysql_free_result($saldo_recibe);

mysql_free_result($comision_user);

mysql_free_result($user);

mysql_free_result($user_recibe);

mysql_free_result($saldo_gopayco);

mysql_free_result($saldo_envia);

mysql_free_result($user_yo);

mysql_free_result($mi_saldo);
?>
