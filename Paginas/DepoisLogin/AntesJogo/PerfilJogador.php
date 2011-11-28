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

//Query para obter uma lista de todos os jogadores do plantel
mysql_select_db($database_localhost, $localhost);
$query_ListaJogadores = "SELECT * FROM jogador WHERE Id_Clube = $identificacao ORDER BY jogador.Numero_da_camisola";
$ListaJogadores = mysql_query($query_ListaJogadores, $localhost) or die(mysql_error());
$row_ListaJogadores = mysql_fetch_assoc($ListaJogadores);
$totalRows_ListaJogadores = mysql_num_rows($ListaJogadores);

//Guardar numa variável o jogador selecionado na Lista ou enviado por Url
$JogadorId;
if(empty($_POST["SelecJogador"])&&empty($_GET['id'])){
	$JogadorId = $row_ListaJogadores ['Id_Jogador'];
	}else if(empty($_POST["SelecJogador"])&&$_GET['id']){
		$JogadorId = $_GET['id']; 					
		}else{
			$JogadorId = $_POST['SelecJogador'];
		}	

//Query para obter as informações do Jogador selecionado 
mysql_select_db($database_localhost, $localhost);
$query_Jogador = "SELECT * FROM jogador WHERE jogador.Id_Jogador = $JogadorId  AND jogador.Id_Clube = $identificacao";
$Jogador = mysql_query($query_Jogador, $localhost) or die(mysql_error());
$row_Jogador = mysql_fetch_assoc($Jogador);
$totalRows_Jogador = mysql_num_rows($Jogador);

//Query para a posição dos jogadores
mysql_select_db($database_localhost, $localhost);
$query_PosicaoJogador = "SELECT Sigla FROM jogador_posicao WHERE Id_Jogador = $JogadorId";
$PosicaoJogador = mysql_query($query_PosicaoJogador, $localhost) or die(mysql_error());
$row_PosicaoJogador = mysql_fetch_assoc($PosicaoJogador);
$totalRows_PosicaoJogador = mysql_num_rows($PosicaoJogador);

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

#ControlConteudo {
	position:absolute;
	left:0;
	top:0;
	width:100%;
	height:100%;
	z-index:2;
}

 #SelecJogador {
	 position:absolute;
	 left:22%;
	 top:0;
	 width:20%;
	 height:100%;	
 }
 
 #LabJog {
	position:absolute;
	left:5%;
	top:0;
	width:20%;
	height:100%;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight:bold;
}
  
  #JogadorForm {
	position:absolute;
	left:40%;
	top:2%;
	width:60%;
	height:7%;
  }

#FotoControl {
	position:absolute;
	left:75%;
	top:12%;
	width:15%;
	height:35%;
	z-index:2;
}

#ControlDados {
	position:absolute;
	left:5%;
	top:13%;
	width:68%;
	height:87%;
	z-index:2;
}

#TabelaDados{
	width:100%;
	height:100%;
	color:#FFF;
	font-size:medium;
}
#LabelNomeCompleto {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelNumeroCamisola {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelIdade {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelPosicao {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelPePreferido {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelNacionalidade {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelPeso {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelAltura {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
}

#LabelContracto{
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: medium;
	text-align: left;
	font-weight: bold;
	color:#CCC;
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
	<div id="DuranteJogo"><a href="Boas_Vindas.php">Jogo em directo</a></div>
    <div id="Analise"><a href="../Paginas/DepoisLogin/AntesJogo/BoasVindas.php">Análise</a></div>
	<div id="Inserir"><a href="../Inserir/BVInserir.php">Inserir dados</a></div>
</div>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="ConteudoEdit" -->
<div id="Conteudo"> 
<div id="ControlConteudo">
    <form action="" method="post" name="Jogador" id="JogadorForm">
      <label for="SelecJogador" id="LabJog">Jogador:</label>
      <select name="SelecJogador" onchange="JogadorForm.submit()"  id="SelecJogador">
        <option value="0">Escolher</option>
        <?php
do {  
?>
        <option value="<?php echo $row_ListaJogadores['Id_Jogador']?>"><?php echo $row_ListaJogadores['Nome_Conhecido']?></option>
        <?php
} while ($row_ListaJogadores = mysql_fetch_assoc($ListaJogadores));
  $rows = mysql_num_rows($ListaJogadores);
  if($rows > 0) {
      mysql_data_seek($ListaJogadores, 0);
	  $row_ListaJogadores = mysql_fetch_assoc($ListaJogadores);
  }
?>
      </select>
    </form>

    <div id="FotoControl"><img src="../../<?php echo $row_Jogador['Foto']; ?>" width="100%" height="100%"/></div>
  	<div id="ControlDados">
  	  <table width="100%" border="0" id="TabelaDados">
  	    <tr>
  	      <td width="30%" height="12%" id="LabelNomeCompleto" scope="col">Nome Completo:</td>
  	      <td width="70%" scope="col"><?php echo $row_Jogador['Nome_Completo']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelNumeroCamisola">Número:</td>
  	      <td width="70%"><?php echo $row_Jogador['Numero_da_camisola']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelIdade">Idade:</td>
  	      <td width="70%"><?php echo $row_Jogador['Idade']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelPosicao">Posição:</td>
  	      <td width="70%"><?php 
		   do {
			 if($totalRows_PosicaoJogador==1)  
			 	echo $row_PosicaoJogador['Sigla'];
			else{
				echo $row_PosicaoJogador['Sigla'].";  ";
			}
						
			} while ($row_PosicaoJogador = mysql_fetch_assoc($PosicaoJogador));	    
	
	
	?> 
		 </td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelPePreferido">Pé Preferido:</td>
  	      <td width="70%"><?php echo $row_Jogador['Pe_Preferido']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelNacionalidade">Nacionalidade:</td>
  	      <td width="70%"><?php echo $row_Jogador['Nacionalidade']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelPeso">Peso(Kg):</td>
  	      <td width="70%"><?php echo $row_Jogador['Peso']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelAltura">Altura(m):</td>
  	      <td width="70%"><?php echo $row_Jogador['Altura']; ?></td>
	      </tr>
  	    <tr>
  	      <td width="30%" height="11%" id="LabelContracto">Contracto:</td>
  	      <td width="70%"><?php echo $row_Jogador['Contracto'];?></td>
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

mysql_free_result($ListaJogadores);

mysql_free_result($Jogador);

mysql_free_result($PosicaoJogador);
?>
