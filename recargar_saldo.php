<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recargar</title>
<link href="jquery-mobile/jquery.mobile-1.0.min.css" rel="stylesheet" type="text/css" />
<script src="jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
<script src="js/recarga.js" type="text/javascript"></script>
</head>

<body>
<div data-role="page" id="page">
  <div data-role="content">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" style="font-weight:bold">Recargar Saldo de Gopayco</td>
      </tr>
      <tr>
        <td align="center"><form action="" method="get">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="55%" align="center">
          <label for="cantidad"></label>
          <input name="cantidad" type="text" disabled="disabled" id="cantidad" value="20000" readonly="readonly" style="text-align:center" />
        <input id="mult" type="hidden" value="3" />
        </td>
    <td width="0%">&nbsp;</td>
    <td width="45%" align="left">
      <input type="button" name="max" id="max" value="Subir Valor" onclick="subir()" />
      <input type="button" name="min" id="min" value="Bajar Valor" onclick="bajar()" />
    </td>
  </tr>
</table>
</form>
</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
    </table>
  </div>
</div>
</body>
</html>