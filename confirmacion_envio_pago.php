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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
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
  </table>
  <form name="form1" method="post" action="intermedio_envio_pagos.php?valor=<?php echo $_POST['textinput3'] ?>&usuario=<?php echo $row_recibe['user_user']; ?>">
    <input type="submit" name="pagar" id="pagar" value="Enviar Dinero">
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
    <td><form name="form2" method="post" action="realizar_pagos.php">
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
?>
