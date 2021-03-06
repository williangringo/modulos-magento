<?
ob_start();

/*=========================================================================================
Desenvolvimento:
Luciano Lima
www.netdinamica.com.br/boleto
Suporte: boleto@netdinamica.com.br

Licen�a de uso:
A licen�a de uso deste script � para uso pr�prio em seu site.
N�o � permitida a venda do mesmo, estando sujeito �s penas prevista em lei.
A netdinamica, n�o se responsabiliza por preju�zos causados pelo mau uso deste script.
Bem como imper�cia ou altera��es indevidas no c�digo fonte do mesmo.
Caso necessite da licen�a para revenda entre em contato.

Instru��es de Uso:
Sistema para gera��o de boletos
Os dados abaixo s�o apenas um exemplo, estes devem ser substitu�dos pelos seus dados.
Estes dados podem vir de um Banco de Dados ou Mesmo de um Formul�rio WEB
Caso ainda tenha alguma duvida acesse o nosso site, nele voc� encontra maiores detalhes.
DAC = Digito de Auto Confer�ncia - Digito de verifica��o ex: Conta 1018-9  o DAC (digito) � 9
Lembre-se antes de emitir e pagar os boletos, � nescessario que sua conta corrente esteja habilitada junto ao banco para receb�-los. (Caso contrario o valor pago n�o ser� creditado em sua conta corrente)
=============================================================================================*/
//converte string UTF8 para ISO-8859-1
foreach ($_POST as $key => $value) {
    $_POST[$key] = utf8_decode($value);
}

//  pegando os dados via post
$caminho = $_POST['base_url'];
if (strrpos($caminho, '/') != strlen($caminho) - 1) {
	$caminho .= '/'; 
}

$logo_url = $_POST["logo_url"]; 
$caminhoPortal = $_POST["store_url"]; 

$taxa_boleto = $_POST["taxa_boleto"];
$taxa_boleto = str_replace(",", ".",$taxa_boleto);

$dias_de_prazo_para_pagamento = $_POST["prazo_pagamento"];
$dtvencimento = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";

$dtcadastro = date("d/m/Y");
$valor = $_POST["total_pedido"];
$valor = str_replace(",", ".",$valor);

$documento = $_POST["ref_transacao"]; 

$endereco_sacado = $_POST["cliente_end"] . ' - ' . $_POST["cliente_cidade"] . ' ' . $_POST["cliente_uf"] . ' - ' . $_POST["cliente_cep"];
$sacado = $_POST["cliente_nome"]; 

/*
$sacado = explode(" ", $sacado);
$sacado1 = $sacado[0];
$sacado2 = $sacado[1];
$sacado3 = $sacado[2];
$sacado4 = $sacado[3];
$sacado5 = $sacado[4];

$sacado = $sacado1." ".$sacado2." ".$sacado3." ".$sacado4." ".$sacado5;
*/

$valor_boleto = number_format($valor+$taxa_boleto, 2, ',', '');
 
//=========Dados Obrigat�rios para gerar o Boleto
$entra["data_vencimento"] 	=  $dtvencimento; // Data de Vencimento do Boleto (DD/MM/AAAA)
$entra["data_documento"] 	=  $dtcadastro; // Data de emiss�o do Boleto (DD/MM/AAAA)
$entra["valor"] 		=  $valor_boleto; 	// Valor do Boleto (Utilizar virgula como separador decimal, n�o use pontos)
$entra["nosso_numero"]	 	=  $documento; 	// Nosso Numero S/ DAC
$entra["numero_documento"]	=  "1";	// Numero do Pedido ou Nosso Numero
$entra["carteira"]	=  "01";  // C�digo da Carteira (00, 01, 8, 9, 80, 81, 82)

//============Dados da Ag�ncia e Conta =========
$entra["agencia"]		=  "3127"; 		// Numero da Ag�ncia 4 Digitos s/DAC
$entra["conta"]		=  "043969";	// Numero da Conta
$entra["dac_conta"]		=  "5";		// Digito da Conta		
$entra["cn_pj"]			=  "000";		// CNPJ PV da (3 Digitos)

//=============Dados do Cedente ===============
$entra["cpf_cnpj_cedente"] 	= "08.418.926/0001-03"; // CPF OU CNPJ Cedente
$entra["endereco"] 		= "Rua Rui Barbosa, 1690"; 								// Ender�o do cedente
$entra["cidade"] 		= "Presidente Prudente - SP";										 // Cidade do cedente
$entra["cedente"] 		= "Comercial Shopping Livros LTDA"; 									// Nome do cedente

//===Dados do seu Cliente (Opcional)===============
$entra["sacado"]		= $sacado; // nome do Sacado (Seu cliente)
$entra["endereco1"] 		= $endereco_sacado;// Endere�o linha 1
$entra["endereco2"] 		= '';
//$entra["endereco2"] 		= $dados[customers_street_address]." <br> ".$dados[customers_suburb]." - ".$dados[customers_city]." - ".$dados[customers_state]." - ".$dados[customers_postcode]; // Endere�o linha 2

//==Os Campos Abaixo s�o Opcionais=================
$entra["instrucoes"] = "Pagamento referente a compras efetuadas no site www.ShoppingLivros.com.br"; //Instru�oes para o Cliente
$entra["instrucoes1"] = "- ATEN��O: Os produtos s� ser�o enviados ap�s o pagamento ter sido efetuado";
$entra["instrucoes2"] = $_POST["instrucoes1"];
$entra["instrucoes3"] = $_POST["instrucoes2"];
$entra["instrucoes4"] = $_POST["instrucoes3"];
$entra["instrucoes5"] = $_POST["instrucoes4"];

$entra["data_processamento"]	= $dtcadastro;
$entra["quantidade"]		= "";
$entra["valor_unitario"] 	= "";
$entra["especie"] = "DM"; 
$entra["aceite"] = "N";

//==================================================================
//============================N�o mude o valores abaixo=============

include("funcoes/funcoes-caixa.php"); 
$b = new boleto();
$entra["uso_banco"] = ""; 	
$b->banco_caixa($entra);

?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
<HEAD>
<TITLE>Boleto Banc�rio Caixa Ec&ocirc;nomica Federal</TITLE><META http-equiv=Content-Type content=text/html; charset=windows-1252><style type=text/css>
<!--.cp {  font: bold 10px Arial; color: black}
<!--.ti {  font: 9px Arial, Helvetica, sans-serif}
<!--.ld { font: bold 15px Arial; color: #000000}
<!--.ct { FONT: 9px "Arial Narrow"; COLOR: #000033}
<!--.cn { FONT: 9px Arial; COLOR: black }
<!--.bc { font: bold 22px Arial; color: #000000 }
--></style> 

<script language="Javascript1.2">
  <!--
  function printpage() {
	alert("ATEN��O: N�o imprima este boleto em modo econ�mico.");
	window.print();
  }
  //-->
</script>

</HEAD>
<BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0 onload="printpage()">
<table width=666 cellspacing=0 cellpadding=0 border=0><tr><td valign=top class=cp><DIV ALIGN="CENTER">Instru��es 
de Impress�o</DIV></TD></TR><TR><TD valign=top class=ti><DIV ALIGN="CENTER"><span STYLE="font-size: 12px; font-weight: bold">Imprimir 
em impressora jato de tinta ou laser em qualidade normal. <b>(N�o use modo 
econ�mico)</b>.</span> <BR>Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) - Corte 
na linha indicada<BR>
</div>
<br />	
<div align="center" style="font-size: 12px; text-alignment: center">
	<br />
	<b>Obrigado por comprar conosco.</b><br />
	O n�mero do seu pedido �: <strong><?php echo $_POST["ref_transacao"] ?></strong>.<br/>
	<a href="javascript:printpage();"><img border="0" src="<?php echo $caminho; ?>imagens/printer.gif" title="Imprimir"/></a>
	&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $caminhoPortal ?>">Ap�s imprimir, clique aqui para voltar � loja.</a>
	
</DIV></td></tr></table><br><table cellspacing=0 cellpadding=0 width=666 border=0><TBODY><TR><TD class=ct width=666><img height=1 src="<?php echo $caminho; ?>/imagens/6.gif" width=665 border=0></TD></TR><TR><TD class=ct width=666><div align=right><b class=cp>Recibo 
do Sacado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></div></TD></tr></tbody></table><table width=666 cellspacing=5 cellpadding=0 border=0><tr><td width=41></TD></tr></table><table width=666 cellspacing=5 cellpadding=0 border=0 align=Default><tr> 
    <td width=41> <a href="<?php echo $caminhoPortal; ?>" target="_blank"><IMG SRC="<?php echo $logo_url; ?>" WIDTH="150" HEIGHT="60" border="0"></a></td>
<td class=ti width=455> 
<? echo $entra["cedente"]; ?> - <? echo $documento;?><br> <? echo $entra["endereco"]; ?><br> <? echo $entra["cidade"]; ?><br> 
<br> </td>
<td align=RIGHT width=150 class=ti> 
</td></tr></table>

<BR><table cellspacing=0 cellpadding=0 width=661 border=0><tbody><tr><td class=cp width=146>
      <div align=left><IMG SRC="<?php echo $caminho; ?>/imagens/logo-caixa.jpg" WIDTH="145" HEIGHT="36"></div></td><td width=4 valign=bottom><img height=22 src="<?php echo $caminho; ?>/imagens/3.gif" width=2 border=0></td><td class=cpt  width=61 valign=bottom> 
      <div align=center><font class=bc>104-0</font></div></td><td width=4 valign=bottom><img height=22 src="<?php echo $caminho; ?>/imagens/3.gif" width=2 border=0></td><td class=ld align=right width=451 valign=bottom><span class=ld> 
<?=$entra["linha_digitavel"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></td>
</tr><tr><td colspan=5><img height=2 src="<?php echo $caminho; ?>/imagens/2.gif" width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0>
  <tbody><tr><td class=ct valign=top height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top height=13>Cedente</td>
  <td class=ct valign=top height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td>
  <td class=ct valign=top height=13>Ag�ncia/C�digo 
do Cedente</td>
  <td class=ct valign=top height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td>
  <td class=ct valign=top height=13>Esp�cie</td>
  <td class=ct valign=top height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td>
  <td height=13 colspan="3" valign=top class=ct>Nosso 
n�mero</td>
  </tr><tr><td class=cp valign=top height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top height=12> 
<?=$entra["cedente"]?> </td>
  <td class=cp valign=top height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td>
  <td class=cp valign=top height=12> 
<?=$entra["agencia_codigo"]?> </td>
  <td class=cp valign=top height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td>
  <td class=cp valign=top height=12> 
<?=$entra["especie"]?> </td>
  <td class=cp valign=top height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td>
  <td height=12 colspan="3" valign=top class=cp> <div align="right"><?=$entra["nosso_numero"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div></td>
  </tr><tr><td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=298 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=126 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=34 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=53 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td>
<td valign=top height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=120 border=0></td>
</tr></tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top colspan=3 height=13>N�mero 
do documento</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=132 height=13>CPF/CNPJ</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=134 height=13>Vencimento</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>Valor 
documento</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top colspan=3 height=12> 
<?=$entra["numero_documento"]?> </td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=132 height=12> 
<?=$entra["cpf_cnpj_cedente"]?> </td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=134 height=12> 
<?=$entra["data_vencimento"]?> </td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
<?=$entra["valor"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=113 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=113 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=72 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=72 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=132 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=132 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=134 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=134 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=113 height=13>(-) 
Desconto / Abatimentos</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=112 height=13>(-) 
Outras dedu��es</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=113 height=13>(+) 
Mora / Multa</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=113 height=13>(+) 
Outros acr�scimos</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(=) 
Valor cobrado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=112 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=113 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=113 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=112 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=112 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=113 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=113 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=113 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=113 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=659 height=13>Sacado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=659 height=12> 
<?=$entra["sacado"]?> </td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=659 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=659 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct  width=7 height=12></td><td class=ct  width=482 >Instru��es</td><td class=ct  width=29 height=12></td><td class=ct  width=148 >Autentica��o 
mec�nica</td></tr><tr><td  width=7 ></td><td  width=482 ></td><td  width=29 ></td><td  width=148 ></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td width=7></td><td  width=500 class=cp> 
<?=$entra["instrucoes"]?> </td><td width=159></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tr><td class=ct width=666></td></tr><tbody><tr><td class=ct width=666> 
<div align=right>Corte na linha pontilhada&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr><tr><td class=ct width=666><img height=1 src="<?php echo $caminho; ?>/imagens/6.gif" width=665 border=0></td></tr></tbody></table><br><br><table cellspacing=0 cellpadding=0 width=664 border=0><tbody><tr><td class=cp width=146>
      <div align=left><IMG SRC="<?php echo $caminho; ?>/imagens/logo-caixa.jpg" WIDTH="145" HEIGHT="36"></div></td><td width=4 valign=bottom><img height=22 src="<?php echo $caminho; ?>/imagens/3.gif" width=2 border=0></td><td class=cpt  width=59 valign=bottom><div align=center><font class=bc>104-0</font></div></td><td width=4 valign=bottom><img height=22 src="<?php echo $caminho; ?>/imagens/3.gif" width=2 border=0></td><td class=ld align=right width=453 valign=bottom><span class=ld> 
<?=$entra["linha_digitavel"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></td>
</tr><tr><td colspan=5><img height=2 src="<?php echo $caminho; ?>/imagens/2.gif" width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=472 height=13>Local 
de pagamento</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>Vencimento</td></tr><tr>
    <td class=cp valign=top width=7 height=12><img height=15 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=472 height=12>Pag&aacute;vel em qualquer banco 
      at&eacute; o vencimento</td><td class=cp valign=top width=7 height=12><img height=15 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
<?=$entra["data_vencimento"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=472 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=472 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=472 height=13>Cedente</td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>Ag�ncia/C�digo 
cedente</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=472 height=12> 
<?=$entra["cedente"]?> </td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
<?=$entra["agencia_codigo"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=472 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=472 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13> 
<img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=113 height=13>Data 
do documento</td><td class=ct valign=top width=7 height=13> <img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=163 height=13>N<u>o</u> 
documento</td><td class=ct valign=top width=7 height=13> <img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=62 height=13>Esp�cie 
doc.</td><td class=ct valign=top width=7 height=13> <img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=34 height=13>Aceite</td><td class=ct valign=top width=7 height=13> 
<img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=72 height=13>Data 
processamento</td><td class=ct valign=top width=7 height=13> <img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>Nosso 
n�mero</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=113 height=12><div align=left> 
<?=$entra["data_documento"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=163 height=12> 
<?=$entra["numero_documento"]?> </td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=62 height=12><div align=left> 
<?=$entra["especie"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=34 height=12><div align=left> 
<?=$entra["aceite"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=72 height=12><div align=left> 
<?=$entra["data_processamento"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
<?=$entra["nosso_numero"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=113 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=113 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=163 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=163 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=62 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=62 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=34 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=34 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=72 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=72 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr> 
<td class=ct valign=top width=7 height=13> <img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top COLSPAN="3" height=13>Uso 
do banco </td><td class=ct valign=top height=13 width=7> <img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=83 height=13>Carteira</td><td class=ct valign=top height=13 width=7> 
<img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=53 height=13>Esp�cie</td><td class=ct valign=top height=13 width=7> 
<img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=123 height=13>Quantidade</td><td class=ct valign=top height=13 width=7> 
<img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=72 height=13> 
Valor </td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(=) 
Valor documento</td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td valign=top class=cp height=12 COLSPAN="3"><div align=left> 
<?=$entra["uso_banco"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=83> 
<div align=left> <?=$entra["carteira"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=53><div align=left> 
<?=$entra["especie"]?> </div></td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=123> 
<?=$entra["quantidade"]?> </td><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top  width=72> 
<?=$entra["valor_unitario"]?> </td><td class=cp valign=top width=7 height=12> 
<img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
<?=$entra["valor"]?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1> <img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=75 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=31 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=31 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=83 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=83 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=53 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=53 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=123 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=123 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=72 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=72 border=0></td><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody> 
</table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody> 
<tr> <td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr> 
<td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=1 border=0></td></tr></tbody></table></td><td valign=top width=468 rowspan=5><font class=ct>Instru��es 
(Texto de responsabilidade do cedente)</font><br><span class=cp> <? echo $entra["instrucoes1"]; ?><br> 
<? echo $entra["instrucoes2"]; ?><br> <? echo $entra["instrucoes3"]; ?><br> <? echo $entra["instrucoes4"]; ?><br> 
<? echo $entra["instrucoes5"]; ?><br> </span></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(-) 
Desconto / Abatimentos</td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10> 
<table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td valign=top width=7 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=1 border=0></td></tr></tbody></table></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(-) 
Outras dedu��es</td></tr><tr><td class=cp valign=top width=7 height=12> <img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10> 
<table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13> 
<img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=1 border=0></td></tr></tbody></table></td><td align=right width=188> 
<table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(+) 
Mora / Multa</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr> 
<td valign=top width=7 height=1> <img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1> 
<img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr> 
<td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=1 border=0></td></tr></tbody></table></td><td align=right width=188> 
<table cellspacing=0 cellpadding=0 border=0><tbody><tr> <td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(+) 
Outros acr�scimos</td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td></tr></tbody></table></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>(=) 
Valor cobrado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr></tbody> 
</table></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td valign=top width=666 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=659 height=13>Sacado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=659 height=12> 
<?=$entra["sacado"]?> </td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=cp valign=top width=7 height=12><img height=12 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=659 height=12> 
<?=$entra["endereco1"]?> </td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=cp valign=top width=472 height=13> 
<?=$entra["endereco2"]?> </td><td class=ct valign=top width=7 height=13><img height=13 src="<?php echo $caminho; ?>/imagens/1.gif" width=1 border=0></td><td class=ct valign=top width=180 height=13>C�d. 
baixa</td></tr><tr><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=472 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=472 border=0></td><td valign=top width=7 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=7 border=0></td><td valign=top width=180 height=1><img height=1 src="<?php echo $caminho; ?>/imagens/2.gif" width=180 border=0></td></tr></tbody></table><TABLE cellSpacing=0 cellPadding=0 border=0 width=666><TBODY><TR><TD class=ct  width=7 height=12></TD><TD class=ct  width=409 >Sacador/Avalista</TD><TD class=ct  width=250 ><div align=right>Autentica��o 
mec�nica - <b class=cp>Ficha de Compensa��o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></div></TD></TR><TR><TD class=ct  colspan=3 ></TD></tr></tbody></table><TABLE cellSpacing=0 cellPadding=0 width=666 border=0><TBODY><TR><TD vAlign=bottom align=left height=50> 
<? fbarcode($entra["codigo_barras"], $caminho); ?> </TD></tr></tbody></table><TABLE cellSpacing=0 cellPadding=0 width=666 border=0><TR><TD class=ct width=666></TD></TR><TBODY><TR><TD class=ct width=666><div align=right>Corte 
na linha pontilhada&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></TD></TR><TR><TD class=ct width=666><img height=1 src="<?php echo $caminho; ?>/imagens/6.gif" width=665 border=0></TD></tr></tbody></table>
</BODY></HTML>