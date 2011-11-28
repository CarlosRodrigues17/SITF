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

//Query para Listar os estÃ¡dios existentes na base de dados
mysql_select_db($database_localhost, $localhost);
$query_Estadio = "SELECT * FROM estadio";
$Estadio = mysql_query($query_Estadio, $localhost) or die(mysql_error());
$row_Estadio = mysql_fetch_assoc($Estadio);
$totalRows_Estadio = mysql_num_rows($Estadio);

//Query para obter Equipas Existentes na base de dados
mysql_select_db($database_localhost, $localhost);
$query_Clube = "SELECT * FROM clube";
$Clube = mysql_query($query_Clube, $localhost) or die(mysql_error());
$row_Clube = mysql_fetch_assoc($Clube);
$totalRows_Clube = mysql_num_rows($Clube);

//Query para obter Ãºltimo id de jogo na Base de dados
mysql_select_db($database_localhost, $localhost);
$query_Ultimo_Id_jogo = "SELECT MAX(Id_Jogo) FROM jogo";
$Ultimo_Id_jogo = mysql_query($query_Ultimo_Id_jogo, $localhost) or die(mysql_error());
$row_Ultimo_Id_jogo = mysql_fetch_assoc($Ultimo_Id_jogo);
$totalRows_Ultimo_Id_jogo = mysql_num_rows($Ultimo_Id_jogo);

//VariÃ¡vel para guardar o Ãºltimo id de jogo
 if($row_Ultimo_Id_jogo['MAX(Id_Jogo)'] =="" ){
	 $Ult_id_jogo = 0;
 }else{
	 $Ult_id_jogo = $row_Ultimo_Id_jogo['MAX(Id_Jogo)']+1; 
 }
 
//Query para nome do visitado
$colname_NomeVisitado = "-1";
if (isset($_POST['EquipaCasa'])) {
  $colname_NomeVisitado = $_POST['EquipaCasa'];
}
mysql_select_db($database_localhost, $localhost);
$query_NomeVisitado = sprintf("SELECT Nome FROM clube WHERE Id_Clube = %s", GetSQLValueString($colname_NomeVisitado, "int"));
$NomeVisitado = mysql_query($query_NomeVisitado, $localhost) or die(mysql_error());
$row_NomeVisitado = mysql_fetch_assoc($NomeVisitado);
$totalRows_NomeVisitado = mysql_num_rows($NomeVisitado);

//Query para nome do visitado
$colname_NomeVisitante = "-1";
if (isset($_POST['EquipaFora'])) {
  $colname_NomeVisitante = $_POST['EquipaFora'];
}
mysql_select_db($database_localhost, $localhost);
$query_NomeVisitante = sprintf("SELECT Nome FROM clube WHERE Id_Clube = %s", GetSQLValueString($colname_NomeVisitante, "int"));
$NomeVisitante = mysql_query($query_NomeVisitante, $localhost) or die(mysql_error());
$row_NomeVisitante = mysql_fetch_assoc($NomeVisitante);
$totalRows_NomeVisitante = mysql_num_rows($NomeVisitante);



//Insere formulÃ¡rio
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormJogo")) {
  $insertSQL = sprintf("INSERT INTO jogo (Id_Jogo, Id_Clube_Visitado, Id_Clube_Visitante, Id_Estadio,Nome_Equipa_Visitada,Nome_Equipa_Visitante,`Data`,Hora) VALUES ($Ult_id_jogo,%s, %s, %s,%s,%s, %s,%s)",
                       GetSQLValueString($_POST['EquipaCasa'], "int"),
                       GetSQLValueString($_POST['EquipaFora'], "int"),
                       GetSQLValueString($_POST['Estadio'], "int"),
					   GetSQLValueString($row_NomeVisitado['Nome'], "text"),
					   GetSQLValueString($row_NomeVisitante['Nome'],"text"),
                       GetSQLValueString($_POST['Data'], "date"),
					   GetSQLValueString($_POST['Hora'], "date"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

  $insertGoTo = "InseridoSucesso.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

#EquipaCasa{
	position:absolute;
	top:15%;
	left:18%;
	width:20%;
	height:5%;	
}
#LabelEquipaCasa{
	position:absolute;
	top:15%;
	left:2%;
	width:15%;
	height:5%;
}

#EquipaFora{
	position:absolute;
	top:15%;
	left:59%;
	width:20%;
	height:5%;	
}
#LabelEquipaFora{
	position:absolute;
	top:15%;
	left:43%;
	width:15%;
	height:5%;
}

#Estadio{
	position:absolute;
	top:25%;
	left:18%;
	width:20%;
	height:5%;	
}
#LabelEstadio{
	position:absolute;
	top:25%;
	left:2%;
	width:15%;
	height:5%;
}


#Data{
	position:absolute;
	top:35%;
	left:18%;
	width:10%;
	height:5%;		
}
#LabelData{
	position:absolute;
	top:35%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoData{
	position:absolute;
	top:37%;
	left:29%;
	width:15%;
	height:5%;
	font-size:13px;
}

#Hora{
	position:absolute;
	top:45%;
	left:18%;
	width:10%;
	height:5%;		
}
#LabelHora{
	position:absolute;
	top:45%;
	left:2%;
	width:15%;
	height:5%;		
}
#LabelAvisoHora{
	position:absolute;
	top:45%;
	left:29%;
	width:15%;
	height:5%;
	font-size:13px;
}

#BTSubmeter{
	position:absolute;
	top:75%;
	left:68%;
	width:10%;
	height:5%;	
}

#BTApagar{
	position:absolute;
	top:75%;
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
  
  <label id="LabelNomeForm">Inserir Jogo</label>
  
  <form action="<?php echo $editFormAction; ?>" method="POST" name="FormJogo">
  
  <label id="LabelEquipaCasa"> Equipa Visitada:</label>
  <Select name="EquipaCasa" id="EquipaCasa"/>
    <option value="">Escolher</option>
        <?php
do {  
?>
        <option value="<?php echo $row_Clube['Id_Clube']?>"><?php echo $row_Clube['Nome']?></option>
        <?php
} while ($row_Clube = mysql_fetch_assoc($Clube));
  $rows = mysql_num_rows($Clube);
  if($rows > 0) {
      mysql_data_seek($Clube, 0);
	  $row_Clube = mysql_fetch_assoc($Clube);
  }
?>  
   </Select>
  
  <label id="LabelEquipaFora"> Equipa Visitante:</label>
  <Select name="EquipaFora" id="EquipaFora"/>
    <option value="">Escolher</option>
        <?php
do {  
?>
        <option value="<?php echo $row_Clube['Id_Clube']?>"><?php echo $row_Clube['Nome']?></option>
        <?php
} while ($row_Clube = mysql_fetch_assoc($Clube));
  $rows = mysql_num_rows($Clube);
  if($rows > 0) {
      mysql_data_seek($Clube, 0);
	  $row_Clube = mysql_fetch_assoc($Clube);
  }
?>  
   </Select>
   
   <label id="LabelEstadio"> Estádio:</label>
  <Select name="Estadio" id="Estadio"/>
    <option value="">Escolher</option>
        <?php
do {  
?>
        <option value="<?php echo $row_Estadio['Id_Estadio']?>"><?php echo $row_Estadio['Nome']?></option>
        <?php
} while ($row_Estadio = mysql_fetch_assoc($Estadio));
  $rows = mysql_num_rows($Estadio);
  if($rows > 0) {
      mysql_data_seek($Estadio, 0);
	  $row_Estadio = mysql_fetch_assoc($Estadio);
  }
?>  
   </Select>
  
  <label id="LabelData">Data do Jogo:</label>
  <input name="Data" type="text" id="Data" />
  <label id="LabelAvisoData">(Formato: AAAA-MM-DD)</label>
	
  <label id="LabelHora">Hora do Jogo:</label>
  <input name="Hora" type="text" id="Hora" />
  <label id="LabelAvisoHora">(Formato: HH:MM)</label> 	
    
   <input name="BTSubmeter" type="button" value="Submeter" id="BTSubmeter" onclick="FormJogo.submit()" /> 
   <input name="BTApagar" type="button" value="Apagar" id="BTApagar" />
   <input type="hidden" name="MM_insert" value="FormJogo" />
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

mysql_free_result($Estadio);

mysql_free_result($Clube);

mysql_free_result($Ultimo_Id_jogo);

mysql_free_result($NomeVisitado);

mysql_free_result($NomeVisitante);
?>
