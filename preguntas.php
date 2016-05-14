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

$colname_preguntas = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_preguntas = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_preguntas = sprintf("SELECT * FROM preguntas_seguridad WHERE user_preguntas_seguridad = %s", GetSQLValueString($colname_preguntas, "text"));
$preguntas = mysql_query($query_preguntas, $gopayco) or die(mysql_error());
$row_preguntas = mysql_fetch_assoc($preguntas);
$totalRows_preguntas = mysql_num_rows($preguntas);
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
<?php 
$total = 5-$totalRows_preguntas;
?>
<div data-role="page" id="page">
  <div data-role="content">
  <div id="preguntar" style="display:<?php if ($total <= 0){echo 'none';} ?>">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center"><span style="font-weight: bold">Es necesario que formule preguntas de seguridad en caso de bloqueo de clave</span><br>(Procure que las preguntas sean distintas para mayor seguridad de su dinero)</td>
      </tr>
      <tr>
        <td align="center"><form name="form1" method="get" action="crear_pregunta.php">
          <div data-role="fieldcontain">
            <label for="pregunta">Formule una pregunta:</label>
            <textarea name="pregunta" id="pregunta"></textarea>
            <div data-role="fieldcontain">
              <label for="respuesta">Responda esta pregunta:</label>
              <textarea name="respuesta" id="respuesta"></textarea>
              <br>
              <input type="submit" value="Guardar" />
            </div>
          </div>
        </form></td>
      </tr>
      <tr>
        <td align="center"><?php if ($totalRows_preguntas < 5){ ?>Es necesario formular aún <?php 
		echo 5-$totalRows_preguntas ?> pregunta(s) más.
        <?php }else{?>
        Tiene <?php echo $totalRows_preguntas ?> preguntas en total
        <?php }?>
        </td>
      </tr>
    </table>
  </div>
<div id="maspreguntas" style="display:<?php if ($total > 0){echo 'none';} ?>">
<br><br><br><br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">Ya se han creado <?php echo $totalRows_preguntas ?> preguntas, te recomendamos agregar más para tener mejor seguridad con su dinero de lo contrario concluir con este proceso</td>
  </tr>
</table>
<br><br>
  <div data-role="collapsible-set">
    <div data-role="collapsible">
    
      <h3 style="text-align:center" onClick="
      document.getElementById('preguntar').style.display = '';
      document.getElementById('maspreguntas').style.display = 'none';
      ">Agregar más preguntas</h3>
    </div>
    <input type="submit" value="Terminar" onClick="window.location = 'gopayco.php'" />
  </div>
</div>
</div>
</div>


</body>
</html>
<?php
mysql_free_result($preguntas);
?>
