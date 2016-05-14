<?php require_once('Connections/gopayco.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST['doLogout'])) &&($_POST['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "validacion.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
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

$colname_saldo = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldo = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldo = sprintf("SELECT * FROM saldos WHERE user_saldos = %s AND prioridad_saldos = 'Primario'", GetSQLValueString($colname_saldo, "int"));
$saldo = mysql_query($query_saldo, $gopayco) or die(mysql_error());
$row_saldo = mysql_fetch_assoc($saldo);
$totalRows_saldo = mysql_num_rows($saldo);

$colname_todos_saldos = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_todos_saldos = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_todos_saldos = sprintf("SELECT * FROM saldos WHERE user_saldos = %s ORDER BY prioridad_saldos ASC", GetSQLValueString($colname_todos_saldos, "int"));
$todos_saldos = mysql_query($query_todos_saldos, $gopayco) or die(mysql_error());
$row_todos_saldos = mysql_fetch_assoc($todos_saldos);
$totalRows_todos_saldos = mysql_num_rows($todos_saldos);

mysql_select_db($database_gopayco, $gopayco);
$query_retiro_minimo = "SELECT * FROM retiro_minimo";
$retiro_minimo = mysql_query($query_retiro_minimo, $gopayco) or die(mysql_error());
$row_retiro_minimo = mysql_fetch_assoc($retiro_minimo);
$totalRows_retiro_minimo = mysql_num_rows($retiro_minimo);

$colname_saldo_retiro = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldo_retiro = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldo_retiro = sprintf("SELECT * FROM saldos WHERE user_saldos = %s AND moneda_saldos = 'COP'", GetSQLValueString($colname_saldo_retiro, "int"));
$saldo_retiro = mysql_query($query_saldo_retiro, $gopayco) or die(mysql_error());
$row_saldo_retiro = mysql_fetch_assoc($saldo_retiro);
$totalRows_saldo_retiro = mysql_num_rows($saldo_retiro);

mysql_select_db($database_gopayco, $gopayco);
$query_banco = "SELECT * FROM bancos";
$banco = mysql_query($query_banco, $gopayco) or die(mysql_error());
$row_banco = mysql_fetch_assoc($banco);
$totalRows_banco = mysql_num_rows($banco);
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
<?php if ($row_user['estado_user'] !== 'activo'){
	header("Location: index.php?accesscheck=lock");
	} ?>
<div data-role="page" id="solicitud">
  <div data-role="content">
    <div data-role="collapsible-set">
      <div data-role="collapsible">
        <h3>Saldo en COP <?php echo number_format($row_saldo_retiro['valor_saldos'],2); ?></h3>

      </div>
</div>
<form action="intermedio_confirmar_retiro.php?id_saldos=<?php echo $row_saldo['id_saldos']; ?>" method="get" name="retiro">
  <div data-role="fieldcontain">
  <input id="saldo" type="hidden" value="<?php echo $row_saldo['valor_saldos']; ?>">
  <input id="minimo" type="hidden" value="<?php echo $row_retiro_minimo['valor_retiro_minimo']; ?>">
    <label for="textinput">Monto a Retirar</label>
    <input type="text" name="textinput" id="textinput" value="" /><br>
    <div id="monto_alto" style="background-color:#F00; color:#FF0; display:none">Este monto supera a su saldo actual</div>
    
    <div id="monto_bajo" style="background-color:#F00; color:#FF0; display:none">El Retiro mínimo permitido para usted es de <?php echo number_format($row_retiro_minimo['valor_retiro_minimo'],2); ?> <?php echo $row_retiro_minimo['moneda_retiro_minimo']; ?></div>
    
    <div data-role="fieldcontain">
      <label for="selectmenu" class="select">Banco</label>
      <select name="selectmenu" id="selectmenu">
        <option value="">Seleccione</option>
        <?php
do {  
?>
        <option value="<?php echo $row_banco['nit_bancos']?>"><?php echo $row_banco['nombre_bancos']?></option>
<?php
} while ($row_banco = mysql_fetch_assoc($banco));
  $rows = mysql_num_rows($banco);
  if($rows > 0) {
      mysql_data_seek($banco, 0);
	  $row_banco = mysql_fetch_assoc($banco);
  }
?>
      </select>
      <div data-role="fieldcontain">
        <label for="textinput2">Número de Cuenta</label>
        <input type="text" name="textinput2" id="textinput2" value=""  />
      </div>
      <input type="submit" value="Siguiente" onClick="solicitud_pago()" id="enviar" />
      
      
    </div>
    
  </div>

</form>
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($saldo);

mysql_free_result($todos_saldos);

mysql_free_result($retiro_minimo);

mysql_free_result($saldo_retiro);

mysql_free_result($banco);
?>
