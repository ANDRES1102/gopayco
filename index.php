<?php require_once('Connections/gopayco.php'); ?>
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

mysql_select_db($database_gopayco, $gopayco);
$query_version = "SELECT * FROM version WHERE estado_version = 'activo' ORDER BY id_version DESC";
$version = mysql_query($query_version, $gopayco) or die(mysql_error());
$row_version = mysql_fetch_assoc($version);
$totalRows_version = mysql_num_rows($version);

mysql_select_db($database_gopayco, $gopayco);
$query_estado_app = "SELECT * FROM estado_aplicacion";
$estado_app = mysql_query($query_estado_app, $gopayco) or die(mysql_error());
$row_estado_app = mysql_fetch_assoc($estado_app);
$totalRows_estado_app = mysql_num_rows($estado_app);

?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['usuario'])) {
  $loginUsername=$_POST['usuario'];
  $password=$_POST['contrasena'];
  $MM_fldUserAuthorization = "estado_user";
  $MM_redirectLoginSuccess = "verificacion.php";
  $MM_redirectLoginFailed = "validacion.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_gopayco, $gopayco);
  	
  $LoginRS__query=sprintf("SELECT user_user, password_user, estado_user FROM `user` WHERE user_user=%s AND password_user=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $gopayco) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'estado_user');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
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
<title>Login</title>
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
<link href="jquery-mobile/jquery.mobile-1.0.min.css" rel="stylesheet" type="text/css">
<script src="jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
</head>
<body>
<?php 
$version_app = '1.0';
if($row_estado_app['estadoestado_aplicacion'] == 'activo'){
if ($row_version['numero_version'] == $version_app)
{
 ?>
<div data-role="page" id="login">
<div data-role="content">
    <div data-role="fieldcontain">
    <form name="login" action="<?php echo $loginFormAction; ?>" method="POST">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><img src="image/gopayco.png" width="197" height="205"></td>
  </tr>
</table>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="color:#000; font-weight:bold; font-size:16px; border-radius:5px">
      <tr>
        <td align="center" bgcolor="#999999" style="border-radius:5px">Iniciar Sesión en Gopayco</td>
      </tr>
      <tr>
        <td width="100%" align="center">
          <hr>
          Usuario<br >
          <input type="text" name="usuario" id="usuario" value="" style="text-align:center" />
          </td>
      </tr>
      <tr>
        <td align="center">
          
            Contraseña<br>
            <input type="password" name="contrasena" id="contrasena" value="" style="text-align:center" />
          </td>
      </tr>
      <tr>
        <td align="center"><input type="submit" value="Iniciar Sesión" /></td>
      </tr>
      <tr>
        <td align="center">
        <div id="error" style="display:<?php if ($_POST == false){echo 'none';} ?>">
        <?php if ($_POST['accesscheck'] == 'fin'){ ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FF0000" style="border-radius:5px">
  <tr>
    <td align="center" style="color:#FF0">Usuario o Contraseña Erronea<br>Intente nuevamente</td>
  </tr>
</table>
<?php }elseif ($_POST['accesscheck'] == '/gopayco/gopayco.php'){?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FF0000" style="border-radius:5px">
  <tr>
    <td align="center" style="color:#FF0">Se cerró la sesión correctamente</td>
  </tr>
</table>
<?php }elseif ($_POST['accesscheck'] !== 'fin' || $_POST['accesscheck'] !== '/gopayco/gopayco.php'){?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FF0000" style="border-radius:5px">
  <tr>
    <td align="center" style="color:#FF0">Usuario Bloqueado, por favor comuniquese con nuestros asesores</td>
  </tr>
</table>
<?php }?>
</div>
        </td>
      </tr>
      <tr>
        <td align="center">Versión <?php echo $row_version['numero_version']; ?></td>
      </tr>
    </table>
    </form>
    </div>
  </div>
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

  </div>
</div>
<?php }?>
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
mysql_free_result($version);

mysql_free_result($estado_app);
?>
