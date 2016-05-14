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

$colname_saldos = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_saldos = $_SESSION['MM_Username'];
}
mysql_select_db($database_gopayco, $gopayco);
$query_saldos = sprintf("SELECT * FROM saldos WHERE prioridad_saldos= 'Primario' AND user_saldos = %s", GetSQLValueString($colname_saldos, "int"));
$saldos = mysql_query($query_saldos, $gopayco) or die(mysql_error());
$row_saldos = mysql_fetch_assoc($saldos);
$totalRows_saldos = mysql_num_rows($saldos);

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
<html class="">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<link href="jquery-mobile/jquery.mobile-1.0.min.css" rel="stylesheet" type="text/css">
<script src="jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
<script src="js/teclado_numerico_uno.js" type="text/javascript"></script>
<style>
body{
	font-family:Verdana, Geneva, sans-serif;
	}
</style>
</head>

<body>
<?php if ($row_saldos['estado_saldos'] == 'activo'){ ?>
<input id="uno" type="hidden" value="1">
<input id="dos" type="hidden" value="2">
<input id="tres" type="hidden" value="3">
<input id="cuatro" type="hidden" value="4">
<input id="cinco" type="hidden" value="5">
<input id="seis" type="hidden" value="6">
<input id="siete" type="hidden" value="7">
<input id="ocho" type="hidden" value="8">
<input id="nueve" type="hidden" value="9">
<input id="cero" type="hidden" value="0">
<input id="valor" type="hidden" value="<?php echo $_POST['valor'] ?>">
<input id="usuario" type="hidden" value="<?php echo $_POST['usuario'] ?>">
<div data-role="page" id="solicitud">
  <div data-role="content" style="display:<?php if ($row_user['intentos_clave_user'] == 0){echo 'none';} ?>">
    <table width="100%">
    <tr>
    <td align="center">
    <table width="auto" border="0" cellspacing="0" cellpadding="0" bgcolor="#CCC" style="border-radius:5px; color:#000; font-weight:bold">
      <tr>
        <td align="center" style="font-size:24px"><table width="300px" height="260xpx" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align=""><input type="password" disabled="disabled" id="passwordinput" readonly align="middle" style="text-align:center"></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td width="33%" align="center" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="uno()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">1</td>
                </tr>
              </table>
            </a></td>
            <td width="35%" align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="dos()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">2</td>
                </tr>
              </table>
            </a></td>
            <td width="33%" align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="tres()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">3</td>
                </tr>
              </table>
            </a></td>
          </tr>
          <tr>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="cuatro()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">4</td>
                </tr>
              </table>
            </a></td>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="cinco()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">5</td>
                </tr>
              </table>
            </a></td>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="seis()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">6</td>
                </tr>
              </table>
            </a></td>
          </tr>
          <tr>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="siete()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">7</td>
                </tr>
              </table>
            </a></td>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="ocho()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">8</td>
                </tr>
              </table>
            </a></td>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="nueve()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">9</td>
                </tr>
              </table>
            </a></td>
          </tr>
          <tr>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#000099'" bgcolor="#000099" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#FFF" onClick="reloadx()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <input id="id_url" type="hidden" value="<?php echo $_POST['id'] ?>">
                  <td align="center" valign="middle" width="100%" height="30">Cancel</td>
                </tr>
              </table>
            </a></td>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#CCC'" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#000" onClick="cero()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">0</td>
                </tr>
              </table>
            </a></td>
            <td align="center" onMouseOver="this.bgColor = '#666666'" onMouseOut="this.bgColor = '#FF0000'" bgcolor="#FF0000" style="border:solid 1px #666666"><a href="javascript:void(0)" style="text-decoration:none; color:#FFF" onClick="confirmar_envio()">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" valign="middle" width="100%" height="30">OK</td>
                </tr>
              </table>
            </a></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php }else{?>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr>
        <td align="center" valign="middle">Su saldos se encuentran bloqueados, por favor comuniquese con nosotros gracias</td>
      </tr>
    </table>
    <p>
      <?php }?>
    </p>
  </div>
  <div data-role="content" style="display:<?php if ($row_user['intentos_clave_user'] > 0){echo 'none';} ?>">
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center">Clave bloqueada, no puede realizar este pago</td>
      </tr>
      <tr>
        <td align="center"><form action="gopayco.php" method="get">
          <input name="sali" type="submit" value="Salir">
        </form></td>
      </tr>
    </table>
    <p>&nbsp;</p>
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($saldos);

mysql_free_result($user);
?>
