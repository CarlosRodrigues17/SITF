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
	
  $logoutGoTo = "../../../AntesLogin/Inicio.php";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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

//variÃ¡vel para guardar o Id_clube para futuros querys
$identificacao = $row_Identificar_Clube ['Id_Clube'];

//VariÃ¡vel que guarda data actual
$data_actual = Date("Y-m-d");

//Query para obter informaÃ§oes sobre o clube do utilizador
mysql_select_db($database_localhost, $localhost);
$query_Informacoes_Clube = "SELECT * FROM clube WHERE Id_Clube = $identificacao";
$Informacoes_Clube = mysql_query($query_Informacoes_Clube, $localhost) or die(mysql_error());
$row_Informacoes_Clube = mysql_fetch_assoc($Informacoes_Clube);
$totalRows_Informacoes_Clube = mysql_num_rows($Informacoes_Clube);


//Query para obter Lista de jogos anteriores Ã  data de hoje
mysql_select_db($database_localhost, $localhost);
$query_JogosAnteriores = "SELECT * FROM jogo WHERE `Data` <= now()";
$JogosAnteriores = mysql_query($query_JogosAnteriores, $localhost) or die(mysql_error());
$row_JogosAnteriores = mysql_fetch_assoc($JogosAnteriores);
$totalRows_JogosAnteriores = mysql_num_rows($JogosAnteriores);


//VariÃ¡veis para inserir da BD que nÃ£o sÃ£o directas do formulÃ¡rio
$TempoTotalJogo = $_POST['TempoJogoInter'] + $_POST['TempoJogoFim'];

$EquipaCasaInter =substr(GetSQLValueString($_POST['GolosEquipaCasaInter'], "text"),1,-1);
$EquipaForaInter = substr(GetSQLValueString($_POST['GolosEquipaForaInter'], "text"),1,-1);
$ResultadoInter = $EquipaCasaInter."-".$EquipaForaInter;
$ResultadoIntervalo =GetSQLValueString($ResultadoInter,"text");

$EquipaCasaFim =substr(GetSQLValueString($_POST['GolosEquipaCasaFim'], "text"),1,-1);
$EquipaForaFim = substr(GetSQLValueString($_POST['GolosEquipaForaFim'], "text"),1,-1);
$ResultadoFim = $EquipaCasaFim."-".$EquipaForaFim;
$ResultadoFinal =GetSQLValueString($ResultadoFim,"text");

//Inserir dados formulÃ¡rio
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "FormIncidencias")) {
  $updateSQL = sprintf("UPDATE jogo SET Resultado_intervalo=$ResultadoIntervalo, Resultado_final=$ResultadoFinal, Nr_Golos_Equipa_Visitada_Intervalo=%s, Nr_Golos_Equipa_Visitante_Intervalo=%s, Nr_Golos_Equipa_Visitada_Final=%s, Nr_Golos_Equipa_Visitante_Final=%s, Tempo_jogo_1_Parte=%s, Tempo_jogo_2_Parte=%s, Tempo_jogo_Total=$TempoTotalJogo, Tactica_da_Equipa_Visitada=%s, Tactica_da_Equipa_Visitante=%s WHERE Id_Jogo=%s",
                       GetSQLValueString($_POST['GolosEquipaCasaInter'], "int"),
                       GetSQLValueString($_POST['GolosEquipaForaInter'], "int"),
                       GetSQLValueString($_POST['GolosEquipaCasaFim'], "int"),
                       GetSQLValueString($_POST['GolosEquipaForaFim'], "int"),
                       GetSQLValueString($_POST['TempoJogoInter'], "double"),
                       GetSQLValueString($_POST['TempoJogoFim'], "double"),
                       GetSQLValueString($_POST['TacticaInicialEquipaCasa'], "text"),
                       GetSQLValueString($_POST['TacticaInicialEquipaFora'], "text"),
                       GetSQLValueString($_POST['Jogo'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());

  $updateGoTo = "InseridoSucesso.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/T_DepLoginInserir.dwt.php" codeOutsideHTMLIsLocked="false" -->
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

#LabelNomeForm{
	position:absolute;
	top:2%;
	left:0%;
	width:100%;
	height:6%;
	text-align:center;
	font-size:18px;
	font-family:Tahoma, Geneva, sans-serif;
	text-decoration:inherit;
}

#Jogo{
	position:absolute;
	top:15%;
	left:18%;
	width:70%;
	height:5%;
	text-align:center;		
}
#LabelJogo{
	position:absolute;
	top:15%;
	left:2%;
	width:15%;
	height:5%;		
}

#GolosEquipaCasaInter{
	position:absolute;
	top:25%;
	left:18%;
	width:5%;
	height:5%;
	text-align:center;		
}
#Separador{
	position:absolute;
	top:25%;
	left:24%;
	width:2%;
	height:5%;
	font-size:24px;
	text-align:center;			
}
#GolosEquipaForaInter{
	position:absolute;
	top:25%;
	left:27%;
	width:5%;
	height:5%;
	text-align:center;		
}
#LabelResultadoIntervalo{
	position:absolute;
	top:25%;
	left:2%;
	width:15%;
	height:5%;		
}

#GolosEquipaCasaFim{
	position:absolute;
	top:35%;
	left:18%;
	width:5%;
	height:5%;	
	text-align:center;	
}
#Separador1{
	position:absolute;
	top:35%;
	left:24%;
	width:2%;
	height:5%;
	font-size:24px;
	text-align:center;			
}
#GolosEquipaForaFim{
	position:absolute;
	top:35%;
	left:27%;
	width:5%;
	height:5%;		
	text-align:center;
}
#LabelResultadoFinal{
	position:absolute;
	top:35%;
	left:2%;
	width:15%;
	height:5%;		
}

#TempoJogoInter{
	position:absolute;
	top:45%;
	left:18%;
	width:5%;
	height:5%;
	text-align:center;
}
#LabelTempoJogoInter{
	position:absolute;
	top:45%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoTempoIntervalo{
	position:absolute;
	top:47%;
	left:24%;
	width:15%;
	height:5%;
	font-size:13px;
}

#TempoJogoFim{
	position:absolute;
	top:55%;
	left:18%;
	width:5%;
	height:5%;
	text-align:center;		
}
#LabelTempoJogoFim{
	position:absolute;
	top:55%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoTempoFinal{
	position:absolute;
	top:57%;
	left:24%;
	width:15%;
	height:5%;
	font-size:13px;
}

#TacticaInicialEquipaCasa{
	position:absolute;
	top:65%;
	left:18%;
	width:20%;
	height:5%;	
}
#TacticaInicialEquipaFora{
	position:absolute;
	top:65%;
	left:40%;
	width:20%;
	height:5%;	
}
#LabelTacticaInicial{
	position:absolute;
	top:65%;
	left:2%;
	width:15%;
	height:5%;
}

#TacticaFinalEquipaCasa{
	position:absolute;
	top:75%;
	left:18%;
	width:20%;
	height:5%;	
}
#TacticaFinalEquipaFora{
	position:absolute;
	top:75%;
	left:40%;
	width:20%;
	height:5%;	
}
#LabelTacticaFinal{
	position:absolute;
	top:75%;
	left:2%;
	width:15%;
	height:5%;
}


#BTSubmeter{
	position:absolute;
	top:95%;
	left:68%;
	width:10%;
	height:5%;	
}

#BTApagar{
	position:absolute;
	top:95%;
	left:2%;
	width:10%;
	height:5%;	
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
    	<form action="" method="post" id="FomLogout" >
    	<input type="submit" id="BtLogout" value="Sair"/>
    </form>
    </div>
    <!-- InstanceEndEditable -->
  </div>
		<div id="ControladorMenuLat">
<ul id="MenuBar1" class="MenuBarVertical">
<li><a href="BVInserir.php">Inicio</a></li>
<li><a href="Insere_Equipa.php">Equipa Advers&aacute;ria</a></li>
<li><a href="Inserir_Estadio.php">Est&aacute;dio</a></li>
<li><a href="#" class="MenuBarItemSubmenu">Jogador</a>
  <ul>
    <li><a href="InsereJogadorCasa.php">Equipa</a></li>
    <li><a href="InsereJogadorAdversario.php">Advers&aacute;rio</a></li>
  </ul>
</li>
<li><a href="#" class="MenuBarItemSubmenu">Jogo</a>
  <ul>
    <li><a href="Insere_JogoGeral.php">Jogo </a></li>
    <li><a href="IncidenciasJogo.php">Incid&ecirc;ncias Gerais</a></li>
    <li><a href="#">Estat&iacute;sticas Colectivas</a></li>
    <li><a href="#">Estat&iacute;sticas Individuais</a></li>
  </ul>
</li>
<li><a href="#" class="MenuBarItemSubmenu">Alertas</a>
  <ul>
    <li><a href="#">Minha Equipa</a></li>
    <li><a href="#">Meus Jogadores</a></li>
    <li><a href="#">Pr&oacute;ximo Advers&aacute;rio</a></li>
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
	<div id="AntesJogo"><a href="../AntesJogo/BoasVindas.php">Preparação Antes do Jogo</a></div>
	<div id="DuranteJogo"><a href="../AntesJogo/Boas_Vindas.php">Jogo em directo</a></div>
    <div id="Analise"><a href="../AntesJogo/BoasVindas.php">Análise</a></div>
	<div id="Inserir"><a href="BVInserir.php">Inserir dados</a></div>
</div>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo"> 
	
    <div id="ControlForm">
  
  <label id="LabelNomeForm">Inserir Incidencias do Jogo</label>
  
  <form action="<?php echo $editFormAction; ?>" method="POST" name="FormIncidencias">
  
  <label id="LabelJogo"> Jogos Realizados:</label>
  <Select name="Jogo" id="Jogo"/>
    <option value="">Escolher</option>
        <?php
do {  
?>
        <option value="<?php echo $row_JogosAnteriores['Id_Jogo']?>"><?php echo $row_JogosAnteriores['Nome_Equipa_Visitada']."  -  ".$row_JogosAnteriores['Nome_Equipa_Visitante']." ----------------------------- Data: ". $row_JogosAnteriores['Data']?></option>
        <?php
} while ($row_JogosAnteriores = mysql_fetch_assoc($JogosAnteriores));
  $rows = mysql_num_rows($JogosAnteriores);
  if($rows > 0) {
      mysql_data_seek($JogosAnteriores, 0);
	  $row_JogosAnteriores = mysql_fetch_assoc($JogosAnteriores);
  }
?>  
   </Select>
  
  <label id="LabelResultadoIntervalo">Resultado ao Intervalo:</label>
  <input name="GolosEquipaCasaInter" type="text" id="GolosEquipaCasaInter"/>
  <label id="Separador">-</label>
  <input name="GolosEquipaForaInter" type="text" id="GolosEquipaForaInter"/>
  
  <label id="LabelResultadoFinal">Resultado no final:</label>
  <input name="GolosEquipaCasaFim" type="text" id="GolosEquipaCasaFim"/>
  <label id="Separador1">-</label>
  <input name="GolosEquipaForaFim" type="text" id="GolosEquipaForaFim"/>
  
  <label id="LabelTempoJogoInter">Minutos da 1ªParte:</label>
  <input name="TempoJogoInter" type="text" id="TempoJogoInter"/>
  <label id="LabelAvisoTempoIntervalo">Min (Ex: 47)</label>
  
  <label id="LabelTempoJogoFim">Minutos da 2ªParte:</label>
  <input name="TempoJogoFim" type="text" id="TempoJogoFim"/>
  <label id="LabelAvisoTempoFinal">Min (Ex: 49)</label>
  
  <label id="LabelTacticaInicial"> Tácticas Iniciais:</label>
  <Select name="TacticaInicialEquipaCasa" id="TacticaInicialEquipaCasa"/>
    <option value="">Equipa Visitada</option>
    <option value="5-3-2">5-3-2</option>
    <option value="5-4-1">5-4-1</option>
    <option value="4-5-1">4-5-1</option>
    <option value="4-4-2">4-4-2</option>
    <option value="4-3-3">4-3-3</option>
    <option value="4-2-4">4-2-4</option>
    <option value="3-3-4">3-3-4</option>
    <option value="3-4-3">3-4-3</option>
    <option value="3-5-2">3-5-2</option>
    <option value="4-1-4-1">4-1-4-1</option>
    <option value="4-2-3-1">4-2-3-1</option>
    <option value="4-3-2-1">4-3-2-1</option>   
   </Select>
   <Select name="TacticaInicialEquipaFora" id="TacticaInicialEquipaFora"/>
    <option value="">Equipa Visitante</option>
    <option value="5-3-2">5-3-2</option>
    <option value="5-4-1">5-4-1</option>
    <option value="4-5-1">4-5-1</option>
    <option value="4-4-2">4-4-2</option>
    <option value="4-3-3">4-3-3</option>
    <option value="4-2-4">4-2-4</option>
    <option value="3-3-4">3-3-4</option>
    <option value="3-4-3">3-4-3</option>
    <option value="3-5-2">3-5-2</option>
    <option value="4-1-4-1">4-1-4-1</option>
    <option value="4-2-3-1">4-2-3-1</option>
    <option value="4-3-2-1">4-3-2-1</option>  
   </Select>  
   
   <label id="LabelTacticaFinal"> Tácticas Finais:</label>
  <Select name="TacticaFinalEquipaCasa"/ id="TacticaFinalEquipaCasa"/>
    <option value="">Equipa Visitada</option>
    <option value="5-3-2">5-3-2</option>
    <option value="5-4-1">5-4-1</option>
    <option value="4-5-1">4-5-1</option>
    <option value="4-4-2">4-4-2</option>
    <option value="4-3-3">4-3-3</option>
    <option value="4-2-4">4-2-4</option>
    <option value="3-3-4">3-3-4</option>
    <option value="3-4-3">3-4-3</option>
    <option value="3-5-2">3-5-2</option>
    <option value="4-1-4-1">4-1-4-1</option>
    <option value="4-2-3-1">4-2-3-1</option>
    <option value="4-3-2-1">4-3-2-1</option>  
   </Select>
   <Select name="TacticaFinalEquipaFora"/ id="TacticaFinalEquipaFora"//>
    <option value="">Equipa Visitante</option>
    <option value="5-3-2">5-3-2</option>
    <option value="5-4-1">5-4-1</option>
    <option value="4-5-1">4-5-1</option>
    <option value="4-4-2">4-4-2</option>
    <option value="4-3-3">4-3-3</option>
    <option value="4-2-4">4-2-4</option>
    <option value="3-3-4">3-3-4</option>
    <option value="3-4-3">3-4-3</option>
    <option value="3-5-2">3-5-2</option>
    <option value="4-1-4-1">4-1-4-1</option>
    <option value="4-2-3-1">4-2-3-1</option>
    <option value="4-3-2-1">4-3-2-1</option>   
   </Select>  
   
    <input name="BTSubmeter" type="button" value="Submeter" id="BTSubmeter" onclick="FormIncidencias.submit()" /> 
   <input name="BTApagar" type="button" value="Apagar" id="BTApagar" />
   <input type="hidden" name="MM_insert" value="FormJogo" />
   <input type="hidden" name="MM_update" value="FormIncidencias" />
  </form>
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

mysql_free_result($JogosAnteriores);

mysql_free_result($Identificar_Clube);

mysql_free_result($Informacoes_Clube);
?>
