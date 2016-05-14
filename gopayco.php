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

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php?accesscheck=lock";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
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

$colname_solicitud = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_solicitud = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_solicitud = sprintf("SELECT * FROM solicitud_pago WHERE user_solicitud_pago = %s AND estado_solicitud_pago = 'true' AND informado_solicitud_pago = 'true'", GetSQLValueString($colname_solicitud, "int"));
$solicitud = mysql_query($query_solicitud, $gopayco) or die(mysql_error());
$row_solicitud = mysql_fetch_assoc($solicitud);
$totalRows_solicitud = mysql_num_rows($solicitud);

mysql_select_db($database_gopayco, $gopayco);
$query_version = "SELECT * FROM version WHERE estado_version = 'activo'";
$version = mysql_query($query_version, $gopayco) or die(mysql_error());
$row_version = mysql_fetch_assoc($version);
$totalRows_version = mysql_num_rows($version);

$colname_preguntas = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_preguntas = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_preguntas = sprintf("SELECT * FROM preguntas_seguridad WHERE user_preguntas_seguridad = %s", GetSQLValueString($colname_preguntas, "text"));
$preguntas = mysql_query($query_preguntas, $gopayco) or die(mysql_error());
$row_preguntas = mysql_fetch_assoc($preguntas);
$totalRows_preguntas = mysql_num_rows($preguntas);

mysql_select_db($database_gopayco, $gopayco);
$query_estado_app = "SELECT * FROM estado_aplicacion";
$estado_app = mysql_query($query_estado_app, $gopayco) or die(mysql_error());
$row_estado_app = mysql_fetch_assoc($estado_app);
$totalRows_estado_app = mysql_num_rows($estado_app);
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
<script src="jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
</head>
<body>
<?php if ($row_estado_app['estadoestado_aplicacion'] == 'activo'){ ?>
<?php if ($row_user['estado_user'] !== 'activo'){
	//header("Location: index.php?accesscheck=lock");
	} ?>
<?php if ($totalRows_preguntas < 3){header("Location: preguntas.php");} ?>
<?php 
$version_app = '1.0';
if ($row_version['numero_version'] == $version_app)
{
 ?>
<input id="user" type="hidden" value="<?php echo $row_user['user_user']; ?>">
<div data-role="page" id="page">
<div id="principal" data-role="content" style="display:<?php if ($totalRows_solicitud > 0){echo 'none';} ?>">
   <strong>Bienvenido,</strong> <?php echo $row_user['nombres_user']; ?></p>
    <p>
  <div data-role="collapsible-set">
      <div data-role="collapsible">
        <h3>Saldos <?php echo number_format($row_saldo['valor_saldos'],2); ?> <?php echo $row_saldo['moneda_saldos']; ?></h3>
        <table width="auto" border="0" cellspacing="0" cellpadding="0">
          <?php do { ?><tr>
            
              <td width="40%" align="right"><?php echo number_format($row_todos_saldos['valor_saldos'],2); ?></td>
              <td width="20%" align="center"><?php echo $row_todos_saldos['moneda_saldos']; ?></td>
              <td width="33%"><?php echo $row_todos_saldos['prioridad_saldos']; ?></td>
              
          </tr>
            <?php } while ($row_todos_saldos = mysql_fetch_assoc($todos_saldos)); ?>
        </table>
      </div>
</div>
    </p>
<ul data-role="listview" data-inset="true">
      <li><a href="javascript:void(0)" onClick="window.location = 'gopayco.php'">Refrescar Página</a></li>
      <li><a href="movimientos_cuenta.php">Movimientos Cuenta</a></li>
      <li><a href="realizar_pagos.php">Realizar Pago</a></li>
      <li><a href="solicitar_pago.php">Solicitar Pago</a></li>
      <?php if($row_saldo['moneda_saldos'] == $row_retiro_minimo['moneda_retiro_minimo'] && $row_saldo['valor_saldos'] >= $row_retiro_minimo['valor_retiro_minimo'] && $row_saldo['estado_saldos'] == 'activo') { ?>
      <li><a href="retirar_saldo.php">Retirar Saldo</a></li>
      <?php }?>
      <li><a href="cambiar_clave.php?e=none">Cambiar Clave</a></li>
      <?php if ($row_user['intentos_clave_user'] == 0){ ?>
      <li><a href="recuperar_clave.php?e=none">Recuperar Clave</a></li>
      <?php }?>
    <li><a href="<?php echo $logoutAction ?>">Desconectar</a></li>
      
  </ul>

</div>
<div id="solicitud_pago" data-role="content" style="display:<?php if ($totalRows_solicitud == 0){echo 'none';} ?>"><?php include('operaciones/carga.php'); ?></div>
</div>
<?php }else{?>
<div data-role="page" id="no">
  <div data-role="content">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><br><br><br><img src="image/gopayco.png" alt="" width="197" height="205"></td>
  </tr>
  <tr>
    <td align="center">
    <br><br><br>
    Esta versión ya no esta disponible y no puede utilizarse más,<br>por favor descargue la versión más reciente.</td>
  </tr>
</table>
<?php }?>

  </div>
</div>
<?php }else {?><br>
<br>
<br>
<br>
<br>
<br>

<table width="50%" border="0" cellspacing="0" cellpadding="0" align="center" style="font-weight:bold">
  <tr>
    <td align="center" style="font-size:22px">Aplicación en Mantenimiento<br>
<br>
</td>
  </tr>
  <tr>
    <td align="center" style="font-weight:normal">Disculpa por las molestias pero en el momento estamos haciendo mantenimiento en nuestros servidores, este proceso no va ser demorado.</td>
  </tr>
</table>

<?php }?>
</body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($saldo);

mysql_free_result($todos_saldos);

mysql_free_result($retiro_minimo);

mysql_free_result($solicitud);

mysql_free_result($version);

mysql_free_result($preguntas);

mysql_free_result($estado_app);
?>
