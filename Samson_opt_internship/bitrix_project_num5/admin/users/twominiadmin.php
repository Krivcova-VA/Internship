<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Параметры пользователя");
?><?$APPLICATION->IncludeComponent(
	"bitrix:blog.user",
	"",
	Array(
		"BLOG_VAR" => "",
		"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
		"ID" => 6,
		"PAGE_VAR" => "",
		"PATH_TO_BLOG" => "",
		"PATH_TO_SEARCH" => "",
		"PATH_TO_USER" => "",
		"PATH_TO_USER_EDIT" => "",
		"SET_TITLE" => "Y",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USER_PROPERTY" => array(),
		"USER_VAR" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>