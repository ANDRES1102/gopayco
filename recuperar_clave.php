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

$colname_pregunta = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_pregunta = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_pregunta = sprintf("SELECT * FROM preguntas_seguridad WHERE estado_preguntas_seguridad = 'activo' AND user_preguntas_seguridad = %s ORDER BY RAND()", GetSQLValueString($colname_pregunta, "text"));
$pregunta = mysql_query($query_pregunta, $gopayco) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$totalRows_pregunta = mysql_num_rows($pregunta);

$colname_preguntas_respondidas = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_preguntas_respondidas = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_preguntas_respondidas = sprintf("SELECT * FROM preguntas_seguridad WHERE estado_preguntas_seguridad = 'inactivo' AND user_preguntas_seguridad = %s", GetSQLValueString($colname_preguntas_respondidas, "text"));
$preguntas_respondidas = mysql_query($query_preguntas_respondidas, $gopayco) or die(mysql_error());
$row_preguntas_respondidas = mysql_fetch_assoc($preguntas_respondidas);
$totalRows_preguntas_respondidas = mysql_num_rows($preguntas_respondidas);
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
<div data-role="page" id="page">
  <div data-role="content">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center"><p>&nbsp;</p>
          <p>&nbsp;</p>
          <p style="font-weight:bold; text-decoration:underline">Proceso de recuperación de clave</p>
        <p>¿<?php echo $row_pregunta['pregunta_preguntas_seguridad']; ?>?</p></td>
      </tr>
      <tr>
        <td align="center"><form name="form1" method="get" action="respuestas_preguntas.php">
          <p>
          <input name="id" type="hidden" value="<?php echo $row_pregunta['id_preguntas_seguridad']; ?>">
            <label for="respuesta"></label>
            <input type="text" name="respuesta" id="respuesta">
          </p>
          <p>
            <input type="submit" name="responder" id="responder" value="Responder">
          </p>
        </form></td>
      </tr>
      <tr>
        <td align="center">Total respuestas correctas <?php echo $totalRows_preguntas_respondidas ?></td>
      </tr>
    </table>
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($pregunta);

mysql_free_result($preguntas_respondidas);
?>
