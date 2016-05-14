<?php require_once('Connections/gopayco.php'); ?>
<?php require_once('Connections/gopayco.php'); ?>
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

mysql_select_db($database_gopayco, $gopayco);
$query_comision = "SELECT * FROM comision_user";
$comision = mysql_query($query_comision, $gopayco) or die(mysql_error());
$row_comision = mysql_fetch_assoc($comision);
$totalRows_comision = mysql_num_rows($comision);

$colname_recibe = "-1";
if (isset($_POST['textinput'])) {
  $colname_recibe = $_POST['textinput'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_recibe = sprintf("SELECT * FROM `user` WHERE user_user = %s", GetSQLValueString($colname_recibe, "int"));
$recibe = mysql_query($query_recibe, $gopayco) or die(mysql_error());
$row_recibe = mysql_fetch_assoc($recibe);
$totalRows_recibe = mysql_num_rows($recibe);

$colname_saldos = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldos = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldos = sprintf("SELECT * FROM saldos WHERE user_saldos = %s AND prioridad_saldos = 'Primario'", GetSQLValueString($colname_saldos, "int"));
$saldos = mysql_query($query_saldos, $gopayco) or die(mysql_error());
$row_saldos = mysql_fetch_assoc($saldos);
$totalRows_saldos = mysql_num_rows($saldos);

mysql_select_db($database_gopayco, $gopayco);
$query_comision_user = "SELECT * FROM comision_user WHERE minimo_valor_comision_user <= ".$_POST['textinput3']." AND maximo_valor_comision_user >= ".$_POST['textinput3']."";
$comision_user = mysql_query($query_comision_user, $gopayco) or die(mysql_error());
$row_comision_user = mysql_fetch_assoc($comision_user);
$totalRows_comision_user = mysql_num_rows($comision_user);

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
<div data-role="page" id="page">
  <div data-role="content">
    <?php if ($totalRows_recibe > 0) { // Show if recordset not empty ?>
  <table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
      <td width="42%" align="right" bgcolor="#999999" style="color: #FFF"> Recibe</td>
      <td width="58%" align="center"> <?php echo $row_recibe['nombres_user']; ?></td>
    </tr>
    <tr>
      <td align="right" bgcolor="#999999" style="color: #FFF">Valor Enviado</td>
      <td align="center"> <?php echo number_format($_POST['textinput3'],2) ?>&nbsp;<?php echo $row_saldos['moneda_saldos']; ?></td>
      </tr>
    <tr>
      <td align="right" bgcolor="#999999" style="color: #FFF">Valor Recibido</td>
      <td align="center"> <?php echo number_format($_POST['textinput3']-$_POST['textinput3']*$row_comision_user['valor_comision_user']/100,2) ?> <?php echo $row_saldos['moneda_saldos']; ?></td>
      </tr>
    <tr>
      <td align="right" bgcolor="#999999" style="color: #FFF">Concepto</td>
      <td align="center"><?php if($_POST['textinput2'] == ''){echo 'Solicitud de pago de '.$row_user['nombres_user'];}else{echo $_POST['textinput2'];} ?></td>
    </tr>
  </table>
  <form name="form1" method="post" action="intermedio_pagar_solicitud.php?valor=<?php echo $_POST['textinput3'] ?>&usuario=<?php echo $row_recibe['user_user']; ?>&concepto=<?php if($_POST['textinput2'] == ''){echo 'Solicitud de pago de '.$row_user['nombres_user'];}else{echo $_POST['textinput2'];} ?>">
    <input type="submit" name="pagar" id="pagar" value="Solicitar Dinero">
  </form>
  
  <br>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_recibe == 0) { // Show if recordset empty ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      
  <td align="center" valign="middle">Usuario no encontrado, por favor corriga y confirme nuevamente</td>
  
    </tr>
  </table>
  <?php } // Show if recordset empty ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><form name="form2" method="post" action="solicitar_pago.php">
      <input type="submit" name="corregir" id="corregir" value="Corregir">
    </form></td>
  </tr>
</table>

  </div>
</div>
</body>
</html>
<?php
mysql_free_result($comision);

mysql_free_result($recibe);

mysql_free_result($saldos);

mysql_free_result($comision_user);

mysql_free_result($user);
?>
