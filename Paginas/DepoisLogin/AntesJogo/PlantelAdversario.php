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

//Query para obter lista de todos os clubes menos o do utilizador;
mysql_select_db($database_localhost, $localhost);
$query_TodosClubesAdversario = "SELECT * FROM clube WHERE Id_Clube <> $identificacao";
$TodosClubesAdversario = mysql_query($query_TodosClubesAdversario, $localhost) or die(mysql_error());
$row_TodosClubesAdversario = mysql_fetch_assoc($TodosClubesAdversario);
$totalRows_TodosClubesAdversario = mysql_num_rows($TodosClubesAdversario);

//Query para Listar próximo Jogo
mysql_select_db($database_localhost, $localhost);
$query_ProximoJogo = "SELECT * FROM jogo WHERE `Data` >= now() AND Id_Clube_Visitado = $identificacao OR`Data` >= now() AND Id_Clube_Visitante = $identificacao ORDER BY  jogo.`Data`  LIMIT 1";
$ProximoJogo = mysql_query($query_ProximoJogo, $localhost) or die(mysql_error());
$row_ProximoJogo = mysql_fetch_assoc($ProximoJogo);
$totalRows_ProximoJogo = mysql_num_rows($ProximoJogo);

//Guardar uma variável com o id do próximo Adversário
$Id_ProximoAdversario;
if($identificacao == $row_ProximoJogo['Id_Clube_Visitado']){	
	$Id_ProximoAdversario = $row_ProximoJogo['Id_Clube_Visitante'];
}else if($identificacao == $row_ProximoJogo['Id_Clube_Visitante']){
	$Id_ProximoAdversario =  $row_ProximoJogo['Id_Clube_Visitado'];
}

$IdAdversario;
if(empty($_GET['Id'])&&empty($_POST["SelecClube"])){
	$IdAdversario=$Id_ProximoAdversario ;
	}else if (($_POST["SelecClube"])){
	$IdAdversario = $_POST["SelecClube"];
	}else{
	$IdAdversario = $_GET['Id'];
}

//Query para obter informações sobre o clube Adversario
mysql_select_db($database_localhost, $localhost);
$query_Informacoes_ClubeAdversario = "SELECT * FROM clube WHERE Id_Clube = $IdAdversario";
$Informacoes_ClubeAdversario = mysql_query($query_Informacoes_ClubeAdversario, $localhost) or die(mysql_error());
$row_Informacoes_ClubeAdversario = mysql_fetch_assoc($Informacoes_ClubeAdversario);
$totalRows_Informacoes_ClubeAdversario = mysql_num_rows($Informacoes_ClubeAdversario);

//Query para selecionar todos os guarda-redes do plantel
mysql_select_db($database_localhost, $localhost);
$query_Guarda_Redes = "SELECT * FROM jogador INNER JOIN jogador_posicao ON jogador.Id_jogador = jogador_posicao.Id_jogador WHERE jogador_posicao.Sigla LIKE 'GR' AND Id_Clube = $IdAdversario GROUP BY Nome_Conhecido";
$Guarda_Redes = mysql_query($query_Guarda_Redes, $localhost) or die(mysql_error());
$row_Guarda_Redes = mysql_fetch_assoc($Guarda_Redes);
$totalRows_Guarda_Redes = mysql_num_rows($Guarda_Redes);

//Query para selecionar todos os defesas do plantel
mysql_select_db($database_localhost, $localhost);
$query_Defesas = "SELECT * FROM jogador INNER JOIN jogador_posicao ON jogador.Id_jogador = jogador_posicao.Id_jogador WHERE Id_Clube = $IdAdversario AND (jogador_posicao.Sigla LIKE 'DAE' OR jogador_posicao.Sigla LIKE 'DC' OR jogador_posicao.Sigla LIKE 'LE'OR jogador_posicao.Sigla LIKE 'LCE'OR jogador_posicao.Sigla LIKE 'LC'OR jogador_posicao.Sigla LIKE 'LCD'OR jogador_posicao.Sigla LIKE 'DE'OR jogador_posicao.Sigla LIKE 'DCE' OR jogador_posicao.Sigla LIKE 'DCD'OR jogador_posicao.Sigla LIKE 'DD'OR jogador_posicao.Sigla LIKE 'LAD') GROUP BY Nome_Conhecido ";
$Defesas = mysql_query($query_Defesas, $localhost) or die(mysql_error());
$row_Defesas = mysql_fetch_assoc($Defesas);
$totalRows_Defesas = mysql_num_rows($Defesas);

//Query para selecionar todos os médios do plantel
mysql_select_db($database_localhost, $localhost);
$query_Medios = "SELECT * FROM jogador INNER JOIN jogador_posicao ON jogador.Id_jogador = jogador_posicao.Id_jogador WHERE Id_Clube = $IdAdversario AND (jogador_posicao.Sigla LIKE 'MDCE' OR jogador_posicao.Sigla LIKE 'MDC' OR jogador_posicao.Sigla LIKE 'MDCE'OR jogador_posicao.Sigla LIKE 'ME'OR jogador_posicao.Sigla LIKE 'MCE'OR jogador_posicao.Sigla LIKE 'MCD'OR jogador_posicao.Sigla LIKE 'MC'OR jogador_posicao.Sigla LIKE 'MD'OR jogador_posicao.Sigla LIKE 'MAE'OR jogador_posicao.Sigla LIKE 'MACE'OR jogador_posicao.Sigla LIKE 'MAC'OR jogador_posicao.Sigla LIKE 'MACD'OR jogador_posicao.Sigla LIKE 'MAD') GROUP BY Nome_Conhecido";
$Medios = mysql_query($query_Medios, $localhost) or die(mysql_error());
$row_Medios = mysql_fetch_assoc($Medios);
$totalRows_Medios = mysql_num_rows($Medios);

//Query para selecionar todos os avançados do plantel
mysql_select_db($database_localhost, $localhost);
$query_Avancados = "SELECT *  FROM jogador INNER JOIN jogador_posicao ON jogador.Id_jogador = jogador_posicao.Id_jogador WHERE Id_Clube = $IdAdversario AND(jogador_posicao.Sigla LIKE 'AE' OR jogador_posicao.Sigla LIKE 'ACE' OR jogador_posicao.Sigla LIKE 'AC'OR jogador_posicao.Sigla LIKE 'ACD'OR jogador_posicao.Sigla LIKE 'AD') GROUP BY Nome_Conhecido";
$Avancados = mysql_query($query_Avancados, $localhost) or die(mysql_error());
$row_Avancados = mysql_fetch_assoc($Avancados);
$totalRows_Avancados = mysql_num_rows($Avancados);

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
#ControlTabela {
	position:absolute;
	left:0%;
	top:10%;
	width:100%;
	height:90%;
	z-index:0;
	color: #000;
	border:none;
}
#ControlNomeSimboloAdversario{
	position:absolute;
	left:0%;
	width:100%;
	height:10%;
	top:0%;
	z-index:2;
}
#ControlNomeAdversario{
	position:absolute;
	left:10%;
	width:38%;
	height:100%;
	top:0%;
	font-size:medium;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	color:#FFF;
	text-align:right;
}
#ControlSimboloAdversario{
	position:absolute;
	top:0%;
	left:51%;
	width:5%;
	height:100%;
	z-index:2;
}

#SelecClube {
	 position:absolute;
	 left:8%;
	 top:0;
	 width:20%;
	 height:100%;	
 }
 
 #LabClube {
	position:absolute;
	left:0%;
	top:0;
	width:20%;
	height:100%;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight:bold;
}
  
  #ClubeForm {
	position:absolute;
	left:0%;
	top:2%;
	width:60%;
	height:5%;
	z-index:3;
  }


#TabelaGR{
	position:absolute;
	width:20%;
	left:10%;
	top:0;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	color:#FFF;
}

#TabelaDef {
	position:absolute;
	width:20%;
	left:30%;
	top:0;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	color:#FFF;
}
#TabelaMed{
	position:absolute;
	width:20%;
	left:50%;
	top:0;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	color:#FFF;
}
#TabelaAva{
	position:absolute;
	width:20%;
	left:70%;
	top:0;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	color:#FFF;
	font-size:medium;
}
#Listas {
	font:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:15px;
}
#Selecionavel{
	color:#CCC;
}
#Selecionavel:hover{
	color:#F60;
	}</style>
<!-- InstanceEndEditable -->
<script src="../../../SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
<link href="../../../SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<div id="Pagina">
<!-- InstanceBeginEditable name="CabEdit" -->
  <div id="Cabecalho"> <div id="ControlNomeClubeCabecalho"><?php echo $row_Informacoes_Clube['Nome']; ?></div> 
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
	<div id="DuranteJogo"><a href="BoasVindas.php">Jogo em directo</a></div>
    <div id="Analise"><a href="../Paginas/DepoisLogin/AntesJogo/BoasVindas.php">Análise</a></div>
	<div id="Inserir"><a href="../Inserir/BVInserir.php">Inserir dados</a></div>
</div>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo">
	<form action="" method="post" name="Clube" id="ClubeForm">
      <label for="SelecClube" id="LabJog">Clube:</label>
      <select name="SelecClube" onchange="ClubeForm.submit()"  id="SelecClube">
        <option value="0">Escolher...</option>
        <?php
do {  
?>
        <option value="<?php echo $row_TodosClubesAdversario['Id_Clube']?>"><?php echo $row_TodosClubesAdversario['Nome']?></option>
        <?php
} while ($row_TodosClubesAdversario = mysql_fetch_assoc($TodosClubesAdversario));
  $rows = mysql_num_rows($TodosClubesAdversario);
  if($rows > 0) {
      mysql_data_seek($TodosClubesAdversario, 0);
	  $row_TodosClubesAdversario = mysql_fetch_assoc($TodosClubesAdversario);
  }
?>
      </select>
    </form>
  <div id="ControlNomeSimboloAdversario">
  	<div id="ControlNomeAdversario"><?php echo $row_Informacoes_ClubeAdversario['Nome']; ?></div>
    <div id="ControlSimboloAdversario"><img src="../../<?php echo $row_Informacoes_ClubeAdversario['Simbolo']; ?>" width="100%" height="100%" /> </div>
  </div> 
  <div id="ControlTabela">
    <table width="192" border="0" id="TabelaGR" name="TabelaGR">
      <tr>
        <th scope="col" height="40">Guarda Redes</th>
      </tr>
      <?php
      do {
      	echo "<tr>";
    	echo "<td align=center id=Listas>";
		$valor =strtr($row_Guarda_Redes ['Nome_Conhecido']," ","-");
		$valorid = $Id_ProximoAdversario;
		echo "<a id=Selecionavel href=PerfilJogadorAdversario.php?nome=$valor&Id=$IdAdversario>";
		echo $row_Guarda_Redes ['Nome_Conhecido'];
		echo "</a>";
		echo "</td>";
		echo "</tr>";
	  } while ( $row_Guarda_Redes = mysql_fetch_assoc($Guarda_Redes));
	?>
	</table>
    <table width="150" border="0" id="TabelaDef" name="TabelaDef">
      <tr>
        <th scope="col" height="40">Defesas</th>
      </tr>
       <?php
      do {
		echo "<tr>";
    	echo "<td align=center id=Listas>";
		$valor = strtr($row_Defesas ['Nome_Conhecido']," ","-");
		echo "<a id=Selecionavel href=PerfilJogadorAdversario.php?nome=$valor&Id=$IdAdversario>";
		echo $row_Defesas ['Nome_Conhecido'];
		echo "</a>";
		echo "</td>";
		echo "</tr>";
	  } while ( $row_Defesas = mysql_fetch_assoc($Defesas));
	?>
    </table>
    <table width="149" border="0" id="TabelaMed" name="TabelaMed">
      <tr>
        <th scope="col" height="40">Médios</th>
      </tr>
         <?php
      do {
      	echo "<tr>";
    	echo "<td align=center id=Listas>";
		$valor = strtr($row_Medios ['Nome_Conhecido']," ","-");
		echo "<a id=Selecionavel href=PerfilJogadorAdversario.php?nome=$valor&Id=$IdAdversario>";
		echo $row_Medios ['Nome_Conhecido'];
		echo "</a>";
		echo "</td>";
		echo "</tr>";
	  } while ( $row_Medios = mysql_fetch_assoc($Medios));
	?>
    </table>
    <table width="149" border="0" id="TabelaAva" name="TabelaAva">
      <tr>
        <th scope="col" height="40">Avançados</th>
        </tr>
    	 <?php
      do {
      	echo "<tr>";
    	echo "<td align=center id=Listas>";
		$valor = strtr($row_Avancados ['Nome_Conhecido']," ","-");
		echo "<a id=Selecionavel href=PerfilJogadorAdversario.php?nome=$valor&Id=$IdAdversario>";
		echo $row_Avancados ['Nome_Conhecido'];
		echo "</a>";
		echo "</td>";
		echo "</tr>";
	  } while ( $row_Avancados = mysql_fetch_assoc($Avancados));
	?>
    </table>
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

mysql_free_result($ProximoJogo);

mysql_free_result($Informacoes_ClubeAdversario);

mysql_free_result($Guarda_Redes);

mysql_free_result($Defesas);

mysql_free_result($Medios);

mysql_free_result($Avancados);

mysql_free_result($TodosClubesAdversario);
?>
