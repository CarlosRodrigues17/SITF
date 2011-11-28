<?php require_once('../../../Connections/localhost.php'); ?>
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

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../../AntesLogin/Inicio.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
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

//Query para saber o ID do clube correspondente ao utilizador
$colname_Identificar_Clube = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Identificar_Clube = $_SESSION['MM_Username'];
}
$colpass_Identificar_Clube = "-1";
if (isset($_SESSION['MM_Password'])) {
  $colpass_Identificar_Clube = $_SESSION['MM_Password'];
}
mysql_select_db($database_localhost, $localhost);
$query_Identificar_Clube = sprintf("SELECT Id_Clube FROM utilizador WHERE Nome = %s AND utilizador.Password = %s", GetSQLValueString($colname_Identificar_Clube, "text"),GetSQLValueString($colpass_Identificar_Clube, "text"));
$Identificar_Clube = mysql_query($query_Identificar_Clube, $localhost) or die(mysql_error());
$row_Identificar_Clube = mysql_fetch_assoc($Identificar_Clube);
$totalRows_Identificar_Clube = mysql_num_rows($Identificar_Clube);

//variável para guardar o Id_clube para futuros querys
$identificacao = $row_Identificar_Clube ['Id_Clube'];

//Variável que guarda data actual
$data_actual = Date("Y-m-d");

//Query para obter informações sobre o clube do utilizador
mysql_select_db($database_localhost, $localhost);
$query_Informacoes_Clube = "SELECT * FROM clube WHERE Id_Clube = $identificacao";
$Informacoes_Clube = mysql_query($query_Informacoes_Clube, $localhost) or die(mysql_error());
$row_Informacoes_Clube = mysql_fetch_assoc($Informacoes_Clube);
$totalRows_Informacoes_Clube = mysql_num_rows($Informacoes_Clube);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/T_DepLoginAntesJogo.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type: text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SITF</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<style type="text/css">
#Pagina {
	position:absolute;
	width:100%;
	height:100%;
	z-index:1;
	left: 0;
	top: 0;
	background-image:url(../../../Imagens/BG1.jpg);
	background-repeat:repeat-x;
}
#Cabecalho {
	position:absolute;
	left:30%;
	top:4%;
	width:40%;
	height:10%;
	z-index:2;
	color:#CCC;
}
#ControlNomeClubeCabecalho{
	position:absolute;
	left:17%;
	top:35%;
	width:39%;
	height:65%;
	color:#CCC;
	font:"Courier New", Courier, monospace;
	font-size: larger;
	text-align:center;
}

#ControlSimboloCabecalho{
	position:absolute;
	left:60%;
	width:25%;
	height:100%;
}

#ControlDataActual{
	position:absolute;
	top:14%;
	left:45%;
	width:10%;
	height:3%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:medium;
	color:#FFF;
	text-align:center;
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
	top:22%;
	width:18%;
	height:78%;
	z-index:2;
}

#BoasVindasControl {
	position:absolute;
	left:0;
	top:0;
	width:100%;
	height:20%;
	z-index:2;
}
#BemVindo {
	position:absolute;
	top:10%;
	width:46%;
	height:40%;
	z-index:2;
	font-size:medium;
	font-family:"Arial Black", Gadget, sans-serif;
	left: 0;
	padding-left: 10px;
	color:#CCC;
}
#ControlUtilizBV {
	position:absolute;
	left:21%;
	top:34%;
	width:77%;
	height:30%;
	z-index:2;
}
#UtilizadorBV {
	width:100%;
	height:80%;
	font-size:large;
	left: 5%;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	color:#FFF;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}
#ControlLogout{
	position:absolute;
	left:60%;
	top:65%;
	width:40%;
	height:25%;
}

#BtLogout{
	position:absolute;
	width:100%;
	height:100%;
}

#ControladorMenuLat {
	position:absolute;
	width:100%;
	height:47%;
	z-index:1;
	left: 0%;
	top: 22%;
}

#DivisaoLat1{
	position:absolute;
	left:18%;
	top:22%;
	width:1%;
	height:78%;
	background-color:#006;
	z-index:0;
	background-image:url(../../../Imagens/Baee.jpg);
}

#BarraDivisaoHoriz {
	position:absolute;
	left:0%;
	top:18%;
	width:100%;
	height:4%;
	z-index:1;
	background-image:url(../../../Imagens/Baeee.jpg);
}
#AntesJogo {
	position:absolute;
	left:1%;
	top:0%;
	width:15%;
	height:80%;
	z-index:2;
	background-color:transparent;
	border-top-style:outset;
	border-right-style:outset;
	border-bottom-style:outset;
	border-left-style:outset;
	font-family: Tahoma, Geneva, sans-serif;
	font-size: medium;
	text-align: center;
	color:#333;
}
#DuranteJogo{
position:absolute;
	left:17%;
	top:0%;
	width:10%;
	height:80%;
	z-index:2;
	background-color:transparent;
	border-top-style:outset;
	border-right-style:outset;
	border-bottom-style:outset;
	border-left-style:outset;
	font-family: Tahoma, Geneva, sans-serif;
	font-size: medium;
	text-align: center;
	color:#333;
}

#Inserir {
	position:absolute;
	right:5%;
	top:0%;
	width:15%;
	height:80%;
	z-index:2;
	background-color:transparent;
	border-top-style:outset;
	border-right-style:outset;
	border-bottom-style:outset;
	border-left-style:outset;
	font-family: Tahoma, Geneva, sans-serif;
	font-size: medium;
	text-align: center;
	color:#333;
}
#Analise{
position:absolute;
	right:21%;
	top:0%;
	width:10%;
	height:80%;
	z-index:2;
	background-color:transparent;
	border-top-style:outset;
	border-right-style:outset;
	border-bottom-style:outset;
	border-left-style:outset;
	font-family: Tahoma, Geneva, sans-serif;
	font-size: medium;
	text-align: center;
	color:#333;
}

a:link {
	color:#300;
}
a:visited {
	color:#300;
}
a:hover {
	color: #F60;
}
a:active {
	color: #C60;
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
	left:20%;
	width:78%;
	z-index:0;
}
</style>
<!-- InstanceBeginEditable name="StyleEdit" -->
<style type="text/css">
<style type="text/css">
#ControlEstatisticas {
	position:absolute;
	left:0;
	top:0;
	width:100%;
	height:100%;
	z-index:2;
}
#ControlTabelaTitulo{
	position:absolute;
	left:2%;
	top:1%;
	right:2%;
	width:96%;
	height:3%;
	z-index:2;
}
#TabelaTitulo{
	border:double;
	border-color:#CCC;
	color:#FFF;
}

#ControlFiltros{
	position:absolute;
	top:8%;
	left:0;
	width:100%;
	height:8%;
}

  #JogoForm {
	position:absolute;
	left:0%;
	top:2%;
	width:20%;
	height:100%;
  }
  
#LabJog {
	position:absolute;
	left:0%;
	top:0;
	width:20%;
	height:100%;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#CCC;
}

#SelectJogo{
	 position:absolute;
	 left:25%;
	 top:0;
	 width:75%;
	 height:60%;	
}

#ControlTabelaEstatistica1 {
	position:absolute;
	left:2%;
	top:17%;
	width:45%;
	height:40%;
	z-index:2;
}

#ControlTabelaEstatistica2 {
	position:absolute;
	left:53%;
	top:17%;
	width:45%;
	height:40%;
	z-index:2;
}


#CelTituloEstatistica{
	text-align:left;
	padding-left:3%;
	color:#CCC;
	border:groove;
}

#CelTituloEspe{
	text-align:left;
	padding-left:10%;
	font-size:medium;
	color:#CCC;
	border:groove;
}

#CelTituloEspe1{
	text-align:left;
	padding-left:6%;
	font-size:medium;
	color:#CCC;
	border:groove;
}

#CelTituloTotalMedia{
	text-align:center;
	color:#CCC;
	border:groove;
}

#CelEstatistica{
	color:#FFF;
	border:groove;
	font-size:medium;
}


#VerMenosEstatistica{
	position:absolute;
	left:80%;
	width:15%;
	top:8%;
	height:3%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:small;
	z-index:2;
}

#Verlink:link {
	color:#CCC;
}
#Verlink:visited {
	color:#CCC;
}
#Verlink:hover {
	color: #F60;
}
#Verlink:active {
	color: #030;
}

</style>
<!-- InstanceEndEditable -->
<script src="../../../SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
<link href="../../../SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<div id="Pagina">
<!-- InstanceBeginEditable name="CabEdit" -->
  <div id="Cabecalho"> 
    <div id="ControlNomeClubeCabecalho"><?php echo $row_Informacoes_Clube['Nome']; ?></div> 
    <div id="ControlSimboloCabecalho"><img src="../../<?php echo $row_Informacoes_Clube['Simbolo']; ?>" width="100%" height="100%" /> </div>
  </div>
<!-- InstanceEndEditable -->
  <div id="ControlDataActual"> <?php echo $data_actual ?> </div>
  <div id="Logo"> </div>    
  <div id="BarraLat_1"> 
   <div id="BoasVindasControl">
    <label></label>
    <div id="BemVindo"> Bem-vindo: </div>
    <!-- InstanceBeginEditable name="BVEdit" -->
    <div id="ControlUtilizBV">
      <input name="UtilizadorBV" type="text" id="UtilizadorBV" value="<?php echo $_SESSION['MM_Username']; ?>" readonly="readonly" style="background:transparent"/>
    </div>
     <div id ="ControlLogout">
    	<form action="<?php echo $logoutAction ?>" method="post" id="FomLogout" >
    	<input type="submit" id="BtLogout" value="Sair"/>
    </form>
    </div>
    <!-- InstanceEndEditable -->
  </div>
		<div id="ControladorMenuLat">
<ul id="MenuBar1" class="MenuBarVertical">
              <li><a href="BoasVindas.php">Inicio</a></li>
<li><a class="MenuBarItemSubmenu" href="#">Minha Equipa</a>
  <ul>
    <li><a href="Plantel.php">Plantel</a></li>
    <li><a href="EstatisticasColectivasAJ.php">An&aacute;lise Colectiva</a></li>
    <li><a href="#">T&aacute;cticas</a></li>
  </ul>
</li>
      <li><a href="#" class="MenuBarItemSubmenu">Jogador</a>
        <ul>
          <li><a href="PerfilJogador.php">Perfil</a></li>
          <li><a href="../EstatisticasIndividuaisAntesJogoJogadorCampo.php" class="MenuBarItemSubmenu">An&aacute;lise Individual </a>
            <ul>
              <li><a href="EstatisticaIndividualGR_AJ.php">Guarda-Redes</a></li>
              <li><a href="EstatisticaIndividualJC_AJ.php">Jogadores de Campo</a></li>
            </ul>
          </li>
</ul>
  </li>
      <li><a class="MenuBarItemSubmenu" href="#">Competi&ccedil;&atilde;o</a>
        <ul>
          <li><a href="#">Calend&aacute;rio</a></li>
          <li><a href="#">Pr&oacute;ximo Jogo</a></li>
          <li><a href="#">Classifica&ccedil;&atilde;o</a></li>
          <li><a href="UltimoJogo.php">&Uacute;ltimo Jogo</a></li>
          <li><a href="#" class="MenuBarItemSubmenu">Clubes</a>
            <ul>
<li><a href="#">Info</a></li>
<li><a href="PlantelAdversario.php">Plantel</a></li>
<li><a href="#">Estat&iacute;stica Colectiva</a></li>
            </ul>
          </li>
          <li><a href="#" class="MenuBarItemSubmenu">Jogadores</a>
            <ul>
              <li><a href="#">Perfil</a></li>
              <li><a href="#">Estat&iacute;stica Individual</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li><a href="#" class="MenuBarItemSubmenu">Pr&oacute;ximo Advers&aacute;rio</a>
        <ul>
          <li><a href="PlantelAdversario.php">Plantel</a></li>
          <li><a href="EstatisticasColectivasAdversario_AJ.php">An&aacute;lise Colectiva</a></li>
          <li><a href="#" class="MenuBarItemSubmenu">Jogador</a>
            <ul>
<li><a href="PerfilJogadorAdversario.php">Perfil</a></li>
<li><a href="EstatisticaIndividualAdversarioGR_AJ.php">An&aacute;lise Individual </a></li>
</ul>
          </li>
          <li><a href="#">Alertas</a></li>
          <li><a href="#">Ultimo Jogo</a></li>
        </ul>
      </li>
      <li><a href="#" class="MenuBarItemSubmenu">Alertas</a>
        <ul>
    <li><a href="#">Minha Equipa</a></li>
    <li><a href="#">Meus Jogadores</a></li>
    <li><a href="#">Pr&oacute;ximo AdversÃ¡rio</a></li>
    <li><a href="#">Outros</a></li>
        </ul>
</li>
    </ul>
	</div>
</div>
<div id="DivisaoLat1">
</div>
<!-- InstanceBeginEditable name="EditBarraHorizont" -->
<div id="BarraDivisaoHoriz">
	<div id="AntesJogo"><a href="BoasVindas.php">Preparação Antes do Jogo</a></div>
	<div id="DuranteJogo"><a href="Boas_Vindas.php">Jogo em directo</a></div>
    <div id="Analise"><a href="../Paginas/DepoisLogin/AntesJogo/BoasVindas.php">Análise</a></div>
	<div id="Inserir"><a href="../Inserir/BVInserir.php">Inserir dados</a></div>
</div>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo"> 
 <div id="VerMenosEstatistica"><a id="Verlink" href="EstatisticasColectivasAdversario_AJ2.php">«« (-)Voltar Atrás</a></div>
  <div id="ControlEstatisticas">
  	 <div id="ControlTabelaTitulo">
   	   <table width="100%" border="0" cellspacing="0" style="font-family:'Trebuchet MS', Arial, Helvetica, sans-serif">
        <tr>
         <th colspan="5" scope="col" id="TabelaTitulo">Estatísticas do <?php echo $row_Informacoes_Clube['Nome']; ?> na Liga</th>
        </tr>
       </table>
  	 </div> 
   <div id="ControlFiltros"> 
   	<form action="" method="post" name="JogoForm" id="JogoForm">
      <label for="SelectJogo" id="LabJog">Jogo:</label>
   	  <select name="SelectJogo" id="SelectJogo">
   	     <option value="Value">Escolher...</option>
       </select>
    </form>
   </div>
     <div id="ControlTabelaEstatistica1">
   		<table id="Tabela1" width="100%" border="0" cellspacing="0" cellpadding="0" >
    <tr>
      <th width="83" style="border:none"></th>
      <th width="63" id="CelTituloTotalMedia">Total</th>
      <th width="63" id="CelTituloTotalMedia">Média/Jogo</th>
    </tr>
    <tr>
     <th width="83" id="CelTituloEstatistica" scope="row">Cruzamentos efectuados</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <th width="83" id="CelTituloEspe1" scope="row">Direita</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Certos</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Errados</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <th width="83" id="CelTituloEspe1" scope="row">Esquerda</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Certos</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Errados</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <th width="83" id="CelTituloEstatistica" scope="row">Cruzamentos Consentidos</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <th width="83" id="CelTituloEspe1" scope="row">Direita</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <th width="83" id="CelTituloEspe1" scope="row">Esquerda</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    </table>
    </div>
    <div id="ControlTabelaEstatistica2">
   		<table id="Tabela1" width="100%" border="0" cellspacing="0" cellpadding="0" >
    <tr>
      <th width="83" style="border:none"></th>
      <th width="63" id="CelTituloTotalMedia">Total</th>
      <th width="63" id="CelTituloTotalMedia">Média/Jogo</th>
    </tr>
    <tr>
     <th width="83" id="CelTituloEstatistica" scope="row">Ataques efectuados</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Direita</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Centro</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Esquerda</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <th width="83" id="CelTituloEstatistica" scope="row">Ataques Consentidos</th>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
     <tr>
     <td width="83" id="CelTituloEspe" scope="row">Direita</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Centro</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    <tr>
     <td width="83" id="CelTituloEspe" scope="row">Esquerda</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
     <td width="63" id="CelEstatistica">&nbsp;</td>
    </tr>
    </table>
    </div>
   </div>
  </div>
<!-- InstanceEndEditable -->
<div id="Rodape">
  <div id="RodapeBarra1Pequena">Design by Carlos Rodrigues 2011</div>
</div>
</div>
<script type="text/javascript">
var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgRight:"../SpryAssets/SpryMenuBarRightHover.gif"});
</script>

</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($Identificar_Clube);

mysql_free_result($Informacoes_Clube);
?>
