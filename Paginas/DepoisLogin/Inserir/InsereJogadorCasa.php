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

//Query para obter nacionalidades na lista
mysql_select_db($database_localhost, $localhost);
$query_Nacionalidade = "SELECT * FROM nacionalidade ORDER BY Nome ASC";
$Nacionalidade = mysql_query($query_Nacionalidade, $localhost) or die(mysql_error());
$row_Nacionalidade = mysql_fetch_assoc($Nacionalidade);
$totalRows_Nacionalidade = mysql_num_rows($Nacionalidade);

//Query para obter último id de jogador na Base de dados
mysql_select_db($database_localhost, $localhost);
$query_Ultimo_Id_jogador = "SELECT MAX(Id_Jogador) FROM jogador";
$Ultimo_Id_jogador = mysql_query($query_Ultimo_Id_jogador, $localhost) or die(mysql_error());
$row_Ultimo_Id_jogador = mysql_fetch_assoc($Ultimo_Id_jogador);
$totalRows_Ultimo_Id_jogador = mysql_num_rows($Ultimo_Id_jogador);

//calcular idade
$data_nascimento = $_POST['DataNascimento'];

$data = explode("-", $data_actual);
$data_nascimento = explode("-", $data_nascimento);

$anos = $data[0] - $data_nascimento[0];
$idade = $anos;

if($data_nascimento[1] > $data[1]) //verifica se o mÃªs de nascimento Ã© maior que o mÃªs atual
{
$idade = $anos - 1; //tira um ano, jÃ¡ que ele nÃ£o fez aniversÃ¡rio ainda
}
elseif($data_nascimento[1] == $data[1] && $data_nascimento[2] > $data[2]) //verifica se o dia de hoje Ã© maior que o dia do aniversÃ¡rio
{
$idade = $anos - 1; //tira um ano se nÃ£o fez aniversÃ¡rio ainda
}


//Variável para guardar o último id de jogador
 if($row_Ultimo_Id_jogador['MAX(Id_Jogador)'] =="" ){
	 $Ult_id_jogador = 0;
 }else{
	 $Ult_id_jogador = $row_Ultimo_Id_jogador['MAX(Id_Jogador)']+1; 
 }

//Insere formulário
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormJogador")) {
  $insertSQL = sprintf("INSERT INTO jogador (Id_Jogador, Id_Clube,Nome_Completo, Nome_Conhecido, Data_de_Nascimento,Idade, Naturalidade, Nacionalidade,Posicao_Natural, Peso, Altura, Pe_Preferido, Numero_da_camisola, Contracto, Foto) VALUES ($Ult_id_jogador,$identificacao,%s, %s, %s,$idade, %s, %s,%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['NomeCompleto'], "text"),
                       GetSQLValueString($_POST['NomeConhecido'], "text"),
                       GetSQLValueString($_POST['DataNascimento'], "date"),
                       GetSQLValueString($_POST['Naturalidade'], "text"),
                       GetSQLValueString($_POST['Nacionalidade'], "text"),
					   GetSQLValueString($_POST['Posicao'], "text"),
                       GetSQLValueString($_POST['Peso'], "double"),
                       GetSQLValueString($_POST['Altura'], "double"),
                       GetSQLValueString($_POST['PePreferido'], "text"),
                       GetSQLValueString($_POST['NumeroDaCamisola'], "int"),
                       GetSQLValueString($_POST['FimContracto'], "date"),
                       GetSQLValueString($_POST['Foto'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
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

#ControlForm{
	position:absolute;
	top:0%;
	left:0%;
	width:100%;
	height:100%;	
}

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

#NomeCompleto{
	position:absolute;
	top:11%;
	left:18%;
	width:60%;
	height:5%;
}
#LabelNomeCompleto{
	position:absolute;
	top:11%;
	left:1%;
	width:11%;
	height:5%;
	text-align:left;
}

#NomeConhecido{
	position:absolute;
	top:19%;
	left:18%;
	width:30%;
	height:5%;		
}
#LabelNomeConhecido{
	position:absolute;
	top:19%;
	left:2%;
	width:13%;
	height:5%;		
}

#DataNascimento{
	position:absolute;
	top:27%;
	left:18%;
	width:20%;
	height:5%;		
}
#LabelDataNascimento{
	position:absolute;
	top:27%;
	left:2%;
	width:13%;
	height:5%;		
}

#Naturalidade{
	position:absolute;
	top:35%;
	left:18%;
	width:20%;
	height:5%;		
}
#LabelNaturalidade{
	position:absolute;
	top:35%;
	left:2%;
	width:13%;
	height:5%;		
}

#Nacionalidade{
	position:absolute;
	top:43%;
	left:18%;
	width:20%;
	height:5%;		
}

#LabelNacionalidade{
	position:absolute;
	top:43%;
	left:2%;
	width:15%;
	height:5%;		
}

#Posicao{
	position:absolute;
	top:51%;
	left:18%;
	width:20%;
	height:5%;		
}
#LabelPosicao{
	position:absolute;
	top:51%;
	left:2%;
	width:15%;
	height:5%;		
}

#Peso{
	position:absolute;
	top:59%;
	left:18%;
	width:10%;
	height:5%;		
}
#LabelPeso{
	position:absolute;
	top:59%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoPeso{
	position:absolute;
	top:60%;
	left:29%;
	width:10%;
	height:5%;
	font-size:13px;
}


#Altura{
	position:absolute;
	top:59%;
	left:48%;
	width:10%;
	height:5%;		
}
#LabelAltura{
	position:absolute;
	top:59%;
	left:42%;
	width:15%;
	height:5%;		
}
#LabelAvisoAltura{
	position:absolute;
	top:60%;
	left:59%;
	width:10%;
	height:5%;
	font-size:13px;
}

#PePreferido{
	position:absolute;
	top:67%;
	left:18%;
	width:20%;
	height:5%;		
}
#LabelPePreferido{
	position:absolute;
	top:67%;
	left:2%;
	width:15%;
	height:5%;		
}

#NumeroDaCamisola{
	position:absolute;
	top:75%;
	left:18%;
	width:10%;
	height:5%;		
}

#LabelNumeroDaCamisola{
	position:absolute;
	top:75%;
	left:2%;
	width:15%;
	height:5%;		
}

#FimContracto{
	position:absolute;
	top:83%;
	left:18%;
	width:10%;
	height:5%;		
}
#LabelFimContracto{
	position:absolute;
	top:83%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoFimContracto{
	position:absolute;
	top:85%;
	left:29%;
	width:31%;
	height:5%;
	font-size:13px;		
}
#Foto{
	position:absolute;
	top:91%;
	left:18%;
	width:50%;
	height:5%;		
}

#LabelFoto{
	position:absolute;
	top:91%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoFoto{
	position:absolute;
	top:92%;
	left:69%;
	width:31%;
	height:5%;
	font-size:13px;		
}

#BTSubmeter{
	position:absolute;
	top:98%;
	left:68%;
	width:10%;
	height:5%;	
}

#BTApagar{
	position:absolute;
	top:98%;
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
	<div id="AntesJogo"><a href="../AntesJogo/BoasVindas.php">Prepara&ccedil;&atilde;o Antes do Jogo</a></div>
	<div id="DuranteJogo"><a href="../AntesJogo/Boas_Vindas.php">Jogo em directo</a></div>
    <div id="Analise"><a href="../AntesJogo/BoasVindas.php">An&aacute;lise</a></div>
	<div id="Inserir"><a href="BVInserir.php">Inserir dados</a></div>
</div>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo"> 
	
	<div id="ControlForm">
  
  <label id="LabelNomeForm">Inserir Jogador da Sua Equipa</label>
  
  <form action="<?php echo $editFormAction; ?>" method="POST" name="FormJogador">
  
  <label id="LabelNomeCompleto">Nome Completo:</label>
  <input name="NomeCompleto" type="text" id="NomeCompleto" />
  
  <label id="LabelNomeConhecido"> Nome Conhecido:</label>
  <input name="NomeConhecido" type="text" id="NomeConhecido" />
  
  <label id="LabelDataNascimento"> Data de Nascimento:</label>
  <input name="DataNascimento" type="text" id="DataNascimento" />
	
  <label id="LabelNaturalidade">Naturalidade:</label>
  <input name="Naturalidade" type="text" id="Naturalidade"/> 
  
  <label id="LabelNacionalidade">Nacionalidade:</label>
  <Select name="Nacionalidade" id="Nacionalidade"/>
    <option value="">Escolher</option>
        <?php
do {  
?>
        <option value="<?php echo $row_Nacionalidade['Nome']?>"><?php echo $row_Nacionalidade['Nome']?></option>
        <?php
} while ($row_Nacionalidade = mysql_fetch_assoc($Nacionalidade));
  $rows = mysql_num_rows($Nacionalidade);
  if($rows > 0) {
      mysql_data_seek($Nacionalidade, 0);
	  $row_Nacionalidade = mysql_fetch_assoc($Nacionalidade);
  }
?>  
   </Select>
   
    <label id="LabelPosicao">Posi&ccedil;&atilde;o:</label>
  <Select name="Posicao" id="Posicao"/>
    <option value="GR - Guarda Redes">Guarda-Redes</option>
    <option value="DE - Defesa Esquerdo">Defesa Esquerdo</option>
    <option value="DC - Defesa Central">Defesa Central</option>
    <option value="DD - Defesa Direito">Defesa Direito</option>
    <option value="MDC - Médio Defensivo Centro">Médio Defensivo Centro</option>
    <option value="MC - Médio Centro">Médio Centro</option>
    <option value="ME - Médio Esquerdo">Médio Esquerdo</option>
    <option value="MD - Médio Direito">Médio Direito</option>
    <option value="MAC - Médio Ataque Centro">Médio Ataque Centro</option>
    <option value="EE - Extremo Esquerdo">Extremo Esquerdo</option>
    <option value="ED - Extremo Direito ">Extremo Direito</option>
    <option value="AC - Avançado Centro">Avançado Centro </option>  
    <option value="">Escolher</option>
   </Select>         
  
  <label id="LabelPeso">Peso:</label>
  <input name="Peso" type="text" id="Peso" /> 
  <label id="LabelAvisoPeso">KG   (EX: 86.2)</label>
  
  <label id="LabelAltura">Altura:</label>
  <input name="Altura" type="text" id="Altura"/> 
  <label id="LabelAvisoAltura">Metros   (EX: 1.62)</label>
  
  <label id="LabelPePreferido">Pe Preferido:</label>
  <Select name="PePreferido" id="PePreferido" />
   <option value="Ambos">Ambos</option>
	<option value="Esquerdo">Esquerdo</option>
	<option value="Direito">Direito</option>
  </select>
   
  <label id="LabelNumeroDaCamisola">N&uacute;mero da Camisola:</label>
  <input name="NumeroDaCamisola" type="text" id="NumeroDaCamisola" maxlength="2"/>
  
  <label id="LabelFimContracto">Fim de Contracto:</label>
  <input name="FimContracto" type="text" id="FimContracto" maxlength="4" /> 
  <label id="LabelAvisoFimContracto"> Ano de fim de contracto (Ex: 2014)</label>
  
   <label id="LabelFoto">Foto:</label>
  <input name="Foto" type="text" id="Foto" /> 
  <label id="LabelAvisoFoto"> Caminho do ficheiro (EX:../Imagens/Jogadores/ABC.png)</label>
   
     <input name="BTSubmeter" type="button" value="Submeter" id="BTSubmeter" onclick="FormJogador.submit()" /> 
     <input name="BTApagar" type="button" value="Apagar" id="BTApagar" />
     <input type="hidden" name="MM_insert" value="FormJogador" />
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

mysql_free_result($Informacoes_Clube);

mysql_free_result($Nacionalidade);

mysql_free_result($Posicao);

mysql_free_result($Ultimo_Id_jogador);
?>
