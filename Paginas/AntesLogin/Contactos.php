<?php require_once('../../Connections/localhost.php'); ?>
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

if (isset($_POST['Utilizador'])) {
  $loginUsername=$_POST['Utilizador'];
  $password=$_POST['Password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "../DepoisLogin/AntesJogo/BoasVindas.php";
  $MM_redirectLoginFailed = "Falha.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_localhost, $localhost);
  
  $LoginRS__query=sprintf("SELECT Nome, Password FROM utilizador WHERE Nome=%s AND Password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $localhost) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
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
$_SESSION['MM_Password'] = $password;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/T_Inicial.dwt.php" codeOutsideHTMLIsLocked="false" -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<head>
<meta http-equiv="Content-Type: text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SITF</title>
<!-- InstanceEndEditable -->
<style type="text/css">
#Pagina {
	position:absolute;
	width:100%;
	height:100%;
	z-index:1;
	left: 0;
	top: 0;
	background-image:url(../../Imagens/BG1.jpg);
	background-repeat:repeat-x;
}
#Cabecalho {
	position:absolute;
	left:30%;
	top:4%;
	width:40%;
	height:14%;
	z-index:2;
}
#Logo {
	position:absolute;
	left:0;
	top:0;
	width:15%;
	height:15%;	
}
#BarraLat_1 {
	position:absolute;
	left:0px;
	top:30%;
	width:10%;
	height:70%;
	z-index:2;

}
#DivisaoLat1{
	position:absolute;
	left:15%;
	top:22%;
	width:1%;
	height:78%;
	background-color:#006;
	z-index:2;
	background-image: url(../../Imagens/Baee.jpg);
}
table {
	font-family: "Comic Sans MS", cursive;
	font-size:18pt;
	color: #F30;
	text-align: left;
	height: 40%;
	margin-left:40%;
}

#BarraLogin {
	position:absolute;
	left:0%;
	top:18%;
	width:100%;
	height:4%;
	z-index:1;
	background-image: url(../../Imagens/Baeee.jpg);
}
#BarraUsername {
	position:absolute;
	width:20%;
	height:10%;
	z-index:1;
	left: 40%;
	top: 0px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14pt;
}
#BarraPass {
	position:absolute;
	width:22%;
	height:10%;
	z-index:2;
	left: 60%;
	top: 0%;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14pt;
}
#Controlador_Bt {
	position:absolute;
	width:18%;
	height:100%;
	z-index:9;
	left: 82%;
}
.Botao {
	background-color: #999;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	font-family: Georgia, "Times New Roman", Times, serif;
}



#Rodape {
	position:absolute;
	left:30%;
	top:90%;
	width:40%;
	height:10%;
	z-index:1;
	text-align: center;
}
#RodapeBarra1Pequena {
	position:absolute;
	width:100%;
	height:40%;
	z-index:1;
	margin: 0px;
	border-top-width: medium;
	border-bottom-width: medium;
	border-top-style: ridge;
	border-bottom-style: ridge;
	border-top-color: #666;
	border-bottom-color: #666;
	left: 0%;
	top: 40%;
	font-family: Arial, Helvetica, sans-serif;
	font-size: medium;
	color: #000;
}

#Conteudo{
		position:absolute;
		top:23%;
		height:67%;
		left:17%;
		width:78%;
		z-index:2;
}

#TextoCont{
		position:absolute;
		top:10%;
		height:90%;
		left:10%;
		width:75%;
		z-index:1;
		text-align:left;
		font-family:"Times New Roman", Times, serif;
		font-size:20px;
		font-style:normal;
		text-decoration:blink;

}

a:link {
	color:#CCC;
}
a:visited {
	color:#CCC;
}
a:hover {
	color: #F60;
}
a:active {
	color: #C60;
}

</style>
</head>

<body>
<div id="Pagina">
	<div id="Cabecalho"><img src="../../Imagens/Banner1.png" name="SITFcab" width="100%" height="100%" align="absmiddle" id="SITFcab" /img></div>
<div id="Logo">   </div>    
<div id="BarraLat_1">
  <table width="100%" border="0">
    <tr id="B_Home">
      <td scope="col"><a href="Inicio.php" >Home</a></td>
    </tr>
    <tr id="B_SITF">
      <td><a href="SITF.php">SITF</a></td>
    </tr>
    <tr id="B_Servicos">
      <td><a href="Servicos.php">Servi&ccedil;os</a></td>
    </tr>
    <tr id="B_Contactos">
      <td><a href="Contactos.php">Contactos</a></td>
    </tr>
  </table>
</div>  
<div id="DivisaoLat1"> </div>
<div id="BarraLogin">
<!-- InstanceBeginEditable name="FormEdit" -->
  <form action="<?php echo $loginFormAction; ?>" id="FormLogin" name="FormLogin" method="POST">
   <div id="BarraUsername">
<label for="Utilizador">Utilizador:</label>
    <input name="Utilizador" type="text" id="Utilizador" />
  </div>
  <div id="BarraPass">
<label for="Password">Password:</label>
    <input name="Password" type="password" id="Password" maxlength="10" />
  </div>
    <div id="Controlador_Bt">
      <input type="submit" name="Button" id="Button" value="Entrar" style="height:100%; width: 30%"/>
    </div>
  </form>
<!-- InstanceEndEditable -->
</div>
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo"> 
   <p>Em caso de dúvida ou se tiver interessado contacte:</p>
  <p>&nbsp;</p>
  <p>Carlos Rodrigues</p>
  <p> Nº telem: 2112211212 </p>
  <p>Email: caeq@zmail.bip</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
	</div>
<!-- InstanceEndEditable -->
<div id="Rodape">
  <div id="RodapeBarra1Pequena">Design by Carlos Rodrigues 2011</div>
</div>
</div>
</body>
<!-- InstanceEnd --></html>