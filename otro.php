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
$query_comision = "SELECT * FROM comision_user WHERE minimo_valor_comision_user <= 100000 AND maximo_valor_comision_user >= 100000";
$comision = mysql_query($query_comision, $gopayco) or die(mysql_error());
$row_comision = mysql_fetch_assoc($comision);
$totalRows_comision = mysql_num_rows($comision);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php do { ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="33%"><?php echo number_format($row_comision['minimo_valor_comision_user'],0); ?></td>
    <td width="33%"><?php echo number_format($row_comision['maximo_valor_comision_user'],0); ?></td>
    <td width="33%"><?php echo $row_comision['valor_comision_user']; ?></td>
  </tr>
</table>

  <?php } while ($row_comision = mysql_fetch_assoc($comision)); ?>
</body>
</html>
<?php
mysql_free_result($comision);
?>
