<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';


$itemSize = count($arResult);
?>
<div class="bc_breadcrumbs">
    <ul>
<?
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);

	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= '
			<li><a href="'.$arResult[$index]["LINK"].'">'.$title.'</a>
			</li>';
	}
	else
	{
		$strReturn .= $title;
	}
}?>
    </ul>
    <div class="clearboth"></div>
</div>
<?php
return $strReturn;
