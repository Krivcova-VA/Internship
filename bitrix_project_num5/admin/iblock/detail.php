<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новость детально");
?><?$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:news.detail", 
	".default", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_ELEMENT_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BROWSER_TITLE" => "-",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_CODE" => "",
		"ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"IBLOCK_ID" => "1",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_URL" => "",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"MESSAGE_404" => "",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Страница",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "Y",
		"SET_CANONICAL_URL" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"STRICT_SECTION_CHECK" => "N",
		"USE_PERMISSIONS" => "N",
		"USE_SHARE" => "N",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>

<?

$res = CIBlockElement::GetByID($ElementID);

        if($ar_res = $res->GetNext())
            $arCurentElement=$ar_res;

        $tmpName=str_replace(
            array(".", ",","?","!","-"),
            "",
            trim($arCurentElement["NAME"])
        );

        if(strlen($tmpName)>0){
            $arLooksLike = array(
                "INCLUDE_SUBSECTIONS" => "Y",
                "!ID"=> $ElementID
            );
            $NameItems=explode(" ",$tmpName);

            $itemsArray=array();
            foreach($NameItems as $item){
                if(strlen($item)>1){
                    $itemsArray[]=array("NAME" => "%".$item."%");
                }
            }
            $tmpArray=array("LOGIC" => "OR");

            $tmpArray=array_merge($tmpArray,$itemsArray);

    $addFArray=array(
        array($tmpArray),
    );
    $GLOBALS["arLooksLike"]=array_merge($arLooksLike,$addFArray);
	
		}
    ?>

        <hr /><h3><?=GetMessage("CATEGORIES")?></h3>

		<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				$arParams["CATEGORY_THEME_"."1"],
				[
					"IBLOCK_ID" => 1,
					"NEWS_COUNT" => 5,
					"SET_TITLE" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_GROUPS" => "Y",
					"FILTER_NAME" => "arLooksLike",
					"CACHE_FILTER" => "Y",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
				],
				$component,
				['HIDE_ICONS' => 'Y']
			);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>