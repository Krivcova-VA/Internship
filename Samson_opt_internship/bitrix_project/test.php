<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");
?><?$APPLICATION->IncludeComponent(
	"bitrix:search.form",
	"head",
	Array(
		"PAGE" => "#SITE_DIR#search/index.php",
		"USE_SUGGEST" => "N"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>