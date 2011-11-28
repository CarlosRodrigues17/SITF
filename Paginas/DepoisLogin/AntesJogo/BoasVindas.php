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

//variável para guardar o Estadio do Clube para futuros querys
$estadio = $row_Informacoes_Clube ['Id_Estadio'];

//Query para listar jogos do clube para calendário
mysql_select_db($database_localhost, $localhost);
$query_ListaJogos = "SELECT * FROM jogo WHERE Id_Clube_Visitado = $identificacao OR Id_Clube_Visitante = $identificacao ORDER BY jogo.`Data`";
$ListaJogos = mysql_query($query_ListaJogos, $localhost) or die(mysql_error());
$row_ListaJogos = mysql_fetch_assoc($ListaJogos);
$totalRows_ListaJogos = mysql_num_rows($ListaJogos);

//Query para Listar próximo Jogo
mysql_select_db($database_localhost, $localhost);
$query_ProximoJogo = "SELECT * FROM jogo WHERE `Data` >= now() ORDER BY jogo.`Data` LIMIT 1";
$ProximoJogo = mysql_query($query_ProximoJogo, $localhost) or die(mysql_error());
$row_ProximoJogo = mysql_fetch_assoc($ProximoJogo);
$totalRows_ProximoJogo = mysql_num_rows($ProximoJogo);

//Variável para guardar id da equipa visitada
$Id_Equipa_Visitada = $row_ProximoJogo['Id_Clube_Visitado'];

//Query para obter informacoes da Equipa visitada
mysql_select_db($database_localhost, $localhost);
$query_Equipa_Visitada_Proximo_Jogo = "SELECT * FROM clube WHERE Id_Clube = $Id_Equipa_Visitada";
$Equipa_Visitada_Proximo_Jogo = mysql_query($query_Equipa_Visitada_Proximo_Jogo, $localhost) or die(mysql_error());
$row_Equipa_Visitada_Proximo_Jogo = mysql_fetch_assoc($Equipa_Visitada_Proximo_Jogo);
$totalRows_Equipa_Visitada_Proximo_Jogo = mysql_num_rows($Equipa_Visitada_Proximo_Jogo);

//Variável para guardar id da equipa visitante
$Id_Equipa_Visitante = $row_ProximoJogo['Id_Clube_Visitante'];

//Query para obter informacoes da Equipa visitante
mysql_select_db($database_localhost, $localhost);
$query_Equipa_Visitante_Proximo_Jogo = "SELECT * FROM clube WHERE Id_Clube = $Id_Equipa_Visitante";
$Equipa_Visitante_Proximo_Jogo = mysql_query($query_Equipa_Visitante_Proximo_Jogo, $localhost) or die(mysql_error());
$row_Equipa_Visitante_Proximo_Jogo = mysql_fetch_assoc($Equipa_Visitante_Proximo_Jogo);
$totalRows_Equipa_Visitante_Proximo_Jogo = mysql_num_rows($Equipa_Visitante_Proximo_Jogo);

//Variável para guardar id do Estadio
$Id_Estadio = $row_ProximoJogo['Id_Estadio'];

//Query para obter estádio do jogo
mysql_select_db($database_localhost, $localhost);
$query_Estadio_Proximo_Jogo = "SELECT * FROM estadio WHERE Id_Estadio = $Id_Estadio";
$Estadio_Proximo_Jogo = mysql_query($query_Estadio_Proximo_Jogo, $localhost) or die(mysql_error());
$row_Estadio_Proximo_Jogo = mysql_fetch_assoc($Estadio_Proximo_Jogo);
$totalRows_Estadio_Proximo_Jogo = mysql_num_rows($Estadio_Proximo_Jogo);

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
#ControlProximoJogo{
	position:absolute;
	top:0%;
	left:0%;
	width:55%;
	height:65%;
}
#LabProximoJogo{
	position:absolute;
	left:2%;
	top:2%;
	width:25%;
	height:10%;
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight:bold;
	font-size:Large;
	color:#CCC;
}
#ControlDataProximoJogo{
	position:absolute;
	left:25%;
	top:2%;
	width:25%;
	height:10%;
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:large;
	color:#FFF;
}
#SimboloCasa{
	position:absolute;
	left:16%;
	top:15%;
	width:25%;
	height:40%;
}
#NomeEquipaCasa {
	position:absolute;
	left:16%;
	top:57%;
	width:25%;
	height:12%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:small;
	text-align:center;
	color:#FFF;
}
#LabVs{
	position:absolute;
	left:47%;
	top:28%;
	width:5%;
	text-align:center;
	height:5%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight:bold;
	font-size:large;
	color:#CCC;
}
#SimboloFora{
	position:absolute;
	left:58%;
	top:15%;
	width:25%;
	height:40%;
}
#NomeEquipaFora {
	position:absolute;
	left:58%;
	top:57%;
	width:25%;
	height:12%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:small;
	text-align:center;
	color:#FFF;
}
#LabEstadio{
	position:absolute;
	left:2%;
	top:80%;
	height:5%;
	width:8%;
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:large;
	font-weight:bold;
	color:#CCC;
}
#ControlEstadio{
	position:absolute;
	left:15%;
	top:80%;
	width:60%;
	height:5%;
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:large;
	color:#FFF;
}
#ControlCalendario{
	position:absolute;
	top:0%;
	left:57%;
	width:43%;
	height:65%;
}
#LabCalendario{
	position:absolute;
	left:2%;
	top:2%;
	width:25%;
	height:10%;
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight:bold;
	font-size:Large;
	color:#CCC;
}
#ControlTabelaCalendario{
	position:absolute;
	top:10%;
	width:100%;
	height:90%
}
#TituloTabela{
	color:#CCC;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:large;
}

#ColunaTabela{
	color:#FFF;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:medium;
}
#VerMaisProximoJogo{
	position:absolute;
	left:95%;
	width:3%;
	top:1%;
	height:3%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:x-large;
}

#VerMaislink:link {
	color:#CCC;
}
#VerMaislink:visited {
	color:#CCC;
}
#VerMaislink:hover {
	color: #F60;
}
#VerMaislink:active {
	color: #C60;
}

#ControlAlertas{
	position:absolute;
	top:68%;
	left:0%;
	width:100%;
	height:30%;
	border:groove;
}
#LabAlerta{
	position:absolute;
	top:3%;
	left:1%;
	width:10%;
	height:10%;
	color:#CCC;
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:Large;
	font-weight:bold;
}

#VerMaisAlertas{
	position:absolute;
	left:97%;
	width:3%;
	top:1%;
	height:3%;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:x-large;
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
  <div id="Cabecalho"> <div id="ControlNomeClubeCabecalho"> <?php echo $row_Informacoes_Clube['Nome'];?></div> 
  <div id="ControlSimboloCabecalho"><img src="../../<?php echo $row_Informacoes_Clube['Simbolo']; ?>" width="100%" height="100%"> </div> 
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
      <input name="UtilizadorBV" type="text" id="UtilizadorBV" style="background:transparent" value="<?php echo $_SESSION['MM_Username']; ?>" readonly="readonly"/>
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
	<div id="DuranteJogo"><a href="BoasVindas.php">Jogo em directo</a></div>
	 <div id="Analise"><a href="../Paginas/DepoisLogin/AntesJogo/BoasVindas.php">Análise</a></div>
	<div id="Inserir"><a href="../Inserir/BVInserir.php">Inserir dados</a></div>
</div>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo"> 
	<div id="ControlProximoJogo" style="border:groove"> 
   	  <div id="LabProximoJogo">Próximo Jogo: </div>
      <div id="ControlDataProximoJogo"><?php echo $row_ProximoJogo['Data']; ?></div> 
      <div id="VerMaisProximoJogo"><a id="VerMaislink" href="">+</a></div>
      <div id="SimboloCasa"><img src="../../<?php echo $row_Equipa_Visitada_Proximo_Jogo['Simbolo']; ?>" width="100%" height="100%" /> </div>
      <div id="NomeEquipaCasa"><?php echo $row_Equipa_Visitada_Proximo_Jogo['Nome']; ?></div>
      <div id="LabVs">VS </div>  
      <div id="SimboloFora"><img src="../../<?php echo $row_Equipa_Visitante_Proximo_Jogo['Simbolo']; ?>" width="100%" height="100%" /> </div>
      <div id="NomeEquipaFora"><?php echo $row_Equipa_Visitante_Proximo_Jogo['Nome']; ?></div>
      <div id="LabEstadio">Estádio:</div>
      <div id="ControlEstadio"><?php echo $row_Estadio_Proximo_Jogo['Nome'].", ".$row_Estadio_Proximo_Jogo['Localizacao'] ;?></div>
    </div>
    <div id="ControlCalendario" style="border:groove"> 
      <div id="LabCalendario">Calendário:</div>	
      <div id="ControlTabelaCalendario">
	<table width="100%" border="0" cellspacing="3" cellpadding="0">
  <tr id="TituloTabela">
    <th>Data</th>
    <th>C/F</th>
    <th>Adversário</th>
    <th>Resultado</th>
  </tr>
<?php
      do {
		echo "<tr>";
    	echo "<td align=center id=ColunaTabela>";
		echo $row_ListaJogos['Data'];
		echo "</td>";
		echo "<td align=center id=ColunaTabela>";
		$valor = $row_ListaJogos['Id_Clube_Visitado'];
		if($valor == $identificacao){
		echo "C";
		}else{
		echo "F";
		}
		echo "</td>";
		echo "<td align=center id=ColunaTabela>";
		if($valor ==$identificacao){
		echo $row_ListaJogos['Nome_Equipa_Visitante'];
		}else{
		echo $row_ListaJogos['Nome_Equipa_Visitada'];
		}
		echo "</td>";
		echo "</td>";
		echo "<td align=center id=ColunaTabela>";
		$valor = $row_ListaJogos['Id_Clube_Visitado'];
		$data_Jogo = $row_ListaJogos['Data'];
		$data_actual = Date("Y-m-d");
		//$JogoJogado = $row_ListaJogos['Realizado'];
		if($valor == $identificacao && $data_Jogo <= $data_actual){
		echo $row_ListaJogos['Nr_Golos_Equipa_Visitada_Final']." - ".$row_ListaJogos['Nr_Golos_Equipa_Visitante_Final'];
		}else if ($valor != $identificacao && $data_Jogo <= $data_actual){
		echo $row_ListaJogos['Nr_Golos_Equipa_Visitante_Final']." - ".$row_ListaJogos['Nr_Golos_Equipa_Visitada_Final'];
		}else{
		echo "";
		}
		echo "</td>";
		echo "</tr>";
	  } while ( $row_ListaJogos = mysql_fetch_assoc($ListaJogos));
	?>  
</table>
      </div>
    </div>
    <div id="ControlAlertas">
    <div id="VerMaisAlertas"><a id="VerMaislink" href="">+</a></div>
    <div id="LabAlerta">Alertas:</div>
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

mysql_free_result($ListaJogos);

mysql_free_result($ProximoJogo);

mysql_free_result($Equipa_Visitada_Proximo_Jogo);

mysql_free_result($Equipa_Visitante_Proximo_Jogo);

mysql_free_result($Estadio_Proximo_Jogo);
?>
