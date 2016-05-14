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

$currentPage = $_SERVER["PHP_SELF"];

$colname_saldos_prioridad = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldos_prioridad = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldos_prioridad = sprintf("SELECT * FROM saldos WHERE user_saldos = %s AND prioridad_saldos = 'Primario'", GetSQLValueString($colname_saldos_prioridad, "int"));
$saldos_prioridad = mysql_query($query_saldos_prioridad, $gopayco) or die(mysql_error());
$row_saldos_prioridad = mysql_fetch_assoc($saldos_prioridad);
$totalRows_saldos_prioridad = mysql_num_rows($saldos_prioridad);

$colname_saldos = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldos = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldos = sprintf("SELECT * FROM saldos WHERE user_saldos = %s", GetSQLValueString($colname_saldos, "int"));
$saldos = mysql_query($query_saldos, $gopayco) or die(mysql_error());
$row_saldos = mysql_fetch_assoc($saldos);
$totalRows_saldos = mysql_num_rows($saldos);

$maxRows_movimientos = 10;
$pageNum_movimientos = 0;
if (isset($_POST['pageNum_movimientos'])) {
  $pageNum_movimientos = $_POST['pageNum_movimientos'];
}
$startRow_movimientos = $pageNum_movimientos * $maxRows_movimientos;

$colname_movimientos = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_movimientos = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_movimientos = sprintf("SELECT * FROM movimientos_cuenta WHERE user_movimientos_cuenta = %s ORDER BY id_movimientos_cuenta DESC", GetSQLValueString($colname_movimientos, "int"));
$query_limit_movimientos = sprintf("%s LIMIT %d, %d", $query_movimientos, $startRow_movimientos, $maxRows_movimientos);
$movimientos = mysql_query($query_limit_movimientos, $gopayco) or die(mysql_error());
$row_movimientos = mysql_fetch_assoc($movimientos);

if (isset($_POST['totalRows_movimientos'])) {
  $totalRows_movimientos = $_POST['totalRows_movimientos'];
} else {
  $all_movimientos = mysql_query($query_movimientos);
  $totalRows_movimientos = mysql_num_rows($all_movimientos);
}
$totalPages_movimientos = ceil($totalRows_movimientos/$maxRows_movimientos)-1;
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

<div data-role="page" id="movimientos">
  <form action="gopayco.php" method="get">
          <input name="salir" type="submit" value="Volver al menú principal">
        </form>
  <div data-role="content">
    <div data-role="collapsible-set">
      <div data-role="collapsible">
        <h3>Saldo <?php echo number_format($row_saldos_prioridad['valor_saldos'],2); ?> <?php echo $row_saldos_prioridad['moneda_saldos']; ?></h3>
        
          <table width="auto" border="0" cellspacing="3" cellpadding="3">
            <?php do { ?><tr>
              <td><?php echo number_format($row_saldos['valor_saldos'],2); ?></td>
              <td><?php echo $row_saldos['moneda_saldos']; ?></td>
              <td><?php echo $row_saldos['prioridad_saldos']; ?></td>
            </tr><?php } while ($row_saldos = mysql_fetch_assoc($saldos)); ?>
        </table>
          
      </div>
</div>
    <br>
<?php if ($totalRows_movimientos > 0){ ?>
<?php do { ?>
<?php 
mysql_select_db($database_gopayco, $gopayco);
$query_cliente = "SELECT * FROM cliente WHERE cliente_cliente = '".$row_movimientos['cliente_movimientos_cuenta']."'";
$cliente = mysql_query($query_cliente, $gopayco) or die(mysql_error());
$row_cliente = mysql_fetch_assoc($cliente);
$totalRows_cliente = mysql_num_rows($cliente);

mysql_select_db($database_gopayco, $gopayco);
$query_cliente_user = "SELECT * FROM `user` WHERE user_user = '".$row_movimientos['cliente_movimientos_cuenta']."'";
$cliente_user = mysql_query($query_cliente_user, $gopayco) or die(mysql_error());
$row_cliente_user = mysql_fetch_assoc($cliente_user);
$totalRows_cliente_user = mysql_num_rows($cliente_user);

$colname_user = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_user = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_user = sprintf("SELECT * FROM `user` WHERE user_user = %s", GetSQLValueString($colname_user, "int"));
$user = mysql_query($query_user, $gopayco) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

$queryString_movimientos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_movimientos") == false && 
        stristr($param, "totalRows_movimientos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_movimientos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_movimientos = sprintf("&totalRows_movimientos=%d%s", $totalRows_movimientos, $queryString_movimientos);

?>
<div data-role="collapsible-set">
    <div data-role="collapsible">
      <h3><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="image/anterior.png" width="15" height="15" style="display:<?php if ($row_movimientos['haber_movimientos_cuenta'] == '' || $row_movimientos['haber_movimientos_cuenta'] == '0'){echo 'none';} ?>"><img src="image/siguiente.png" width="15" height="15" style="display:<?php if ($row_movimientos['debe_movimientos_cuenta'] == '' || $row_movimientos['debe_movimientos_cuenta'] == '0'){echo 'none';} ?>">
            <?php if($totalRows_cliente > 0){echo $row_cliente['nombre_cliente'];}else{echo $row_cliente_user['nombres_user'];
			  } ?> <?php echo $row_movimientos['fecha_movimientos_cuenta']; ?></td>
          </tr>
      </table></h3>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-radius:5px; border:solid 1px #666666">
        <tr>
          <td bgcolor="#999999" style="font-weight:bold; border-bottom:solid 1px #666666">Fecha</td>
          <td style="border-bottom:solid 1px #666666"><?php echo $row_movimientos['fecha_movimientos_cuenta']; ?></td>
          </tr>
        <tr>
          <td bgcolor="#999999" style="font-weight:bold; border-bottom:solid 1px #666666">Cliente</td>
          <td style="border-bottom:solid 1px #666666"><?php echo $row_movimientos['cliente_movimientos_cuenta']; ?></td>
          </tr>
        <tr>
          <td bgcolor="#999999" style="font-weight:bold; border-bottom:solid 1px #666666">Nombre</td>
          <td style="border-bottom:solid 1px #666666"><?php if($totalRows_cliente > 0){echo $row_cliente['nombre_cliente'];}else{echo $row_cliente_user['nombres_user'];
			  } ?></td>
          </tr>
        <tr>
          <td bgcolor="#999999" style="font-weight:bold; border-bottom:solid 1px #666666">Concepto</td>
          <td style="border-bottom:solid 1px #666666"><?php echo $row_movimientos['concepto_movimientos_cuenta']; ?></td>
          </tr>
        <tr>
          <td bgcolor="#999999" style="font-weight:bold; border-bottom:solid 1px #666666">Dinero Entrando</td>
          <td style="border-bottom:solid 1px #666666"><?php if($row_movimientos['debe_movimientos_cuenta'] == ''){echo '0.00';}else{echo number_format($row_movimientos['debe_movimientos_cuenta'],2);} ?> </td>
          </tr>
        <tr>
          <td bgcolor="#999999" style="font-weight:bold; border-bottom:solid 1px #666666">Dinero Saliendo</td>
          <td style="border-bottom:solid 1px #666666"><?php if($row_movimientos['haber_movimientos_cuenta'] == ''){echo '0.00';}else{echo number_format($row_movimientos['haber_movimientos_cuenta'],2);} ?></td>
          </tr>
        <tr>
          <td bgcolor="#999999" style="font-weight:bold">Moneda</td>
          <td><?php echo $row_movimientos['moneda_movimientos_cuenta']; ?></td>
          </tr>
      </table>
      <p></div>
    <?php } while ($row_movimientos = mysql_fetch_assoc($movimientos)); ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="33%" align="right"><a href="<?php printf("%s?pageNum_movimientos=%d%s", $currentPage, max(0, $pageNum_movimientos - 1), $queryString_movimientos); ?>" style="text-decoration:none; display:<?php if ($_POST == false or $_POST['pageNum_movimientos'] == '0'){echo 'none';} ?>">Anterior</a></td>
    <td width="32%" align="center">Pag 
      <?php if($_POST == false){echo '1';}else{echo $_POST['pageNum_movimientos']+1;} ?></td>
    <td width="35%"><a href="<?php printf("%s?pageNum_movimientos=%d%s", $currentPage, min($totalPages_movimientos, $pageNum_movimientos + 1), $queryString_movimientos); ?>" style="text-decoration:none; display:<?php $info = number_format($totalRows_movimientos/$maxRows_movimientos-1,0); if ($info == $_POST['pageNum_movimientos'] or $maxRows_movimientos > $_POST['totalRows_movimientos']){echo 'none';} ?>">Siguiente</a></td>
  </tr>
</table>

    <?php }else{?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">No cuenta con información disponible</td>
  </tr>
</table>

    <?php }?>
</div>
<br>

</div>
</body>
</html>
<?php
mysql_free_result($saldos_prioridad);

mysql_free_result($saldos);

mysql_free_result($movimientos);

mysql_free_result($cliente);

mysql_free_result($cliente_user);

mysql_free_result($user);

?>
