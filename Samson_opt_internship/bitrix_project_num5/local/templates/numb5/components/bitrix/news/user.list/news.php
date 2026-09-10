<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>
<?
$filter = Array(
"ACTIVE"=>"Y",
"LAST_LOGIN"=>""
);

$rsUsers = CUser::GetList(($by="last_login"), ($order="desc"), $filter, $arParams);

while($res = $rsUsers->GetNext())
{
	$rsUser = CUser::GetByID($res['ID']);
	$arUser = $rsUser->Fetch();
	$cvoistvo = $arUser['UF_DITAIL_URL'];
	
	?>
	<p class="news-item" id="<?=$this->GetEditAreaId($res['ID']);?>">
		<a href="<?echo $arUser['UF_DITAIL_URL'].$res['LOGIN']?>.php"><b><?echo "(".$res['LOGIN'].") ".$res['NAME']." ".$res['LAST_NAME']?></b></a><br />
	</p>
<?
}
?>

