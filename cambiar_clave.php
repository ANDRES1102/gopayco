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
	background-color: #CCC;
	font-weight: bold;
}
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
<div data-role="page" id="page">
  <div data-role="content">
  <div id="cambiando" style="display:<?php if ($row_user['intentos_clave_user'] == '0'){echo 'none';} ?>">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" style="font-weight:bold">Cambiar Clave de autorizacion para salidas de dinero</td>
      </tr>

      <tr>
        <td align="center"><form name="form1" method="get" action="cambiar_claves.php">
          <div data-role="fieldcontain">
            <label for="passwordinput">Ingrese clave actual:</label>
            <input name="passwordinput" type="password" id="passwordinput" value="" maxlength="4"  />
            <div data-role="fieldcontain">
            <label for="passwordinput2">Ingrese clave actual:</label>
            <input name="passwordinput2" type="password" id="passwordinput2" value="" maxlength="4"  />
              <div data-role="fieldcontain">
                <label for="passwordinput3">Repita la nueva clave:</label>
                <input name="passwordinput3" type="password" id="passwordinput3" value="" maxlength="4"  /><br>
                <input type="submit" value="Guardar" />
              </div>
            </div>
          </div>
        </form></td>
      </tr>
    </table>
    <div id="razon" style="display:<?php if ($_POST['e'] == 'none'){echo 'none';} ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FF0000"; style="border-radius:5px">
  <tr>
    <td align="center" style="color:#FF0"><?php 
	if ($_POST['e'] == 'act'){echo 'Error con la clave actual, verifíque nuevamente<br>Intentos restantes '.$row_user['intentos_clave_user'];}
	elseif($_POST['e'] = 'cla'){echo 'Las nuevas claves no coinciden, verifíque nuevamente';}
?>
</td>
  </tr>
</table>
</div>
</div>
<div id="bloqueado" style="display:<?php if ($row_user['intentos_clave_user'] !== '0'){echo 'none';} ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">Has bloqueado su clave<br>
    <form action="gopayco.php" method="get">
    <input name="salir" type="submit" value="Salir">
    </form>
    </td>
  </tr>
</table>

</div>

  </div>
</div>
</body>
</html>
<?php
mysql_free_result($user);
?>
