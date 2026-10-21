<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="article-card">
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<div class="article-card__title"><?=$arResult["NAME"]?></div>
	<?endif;?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<div class="article-card__date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<?endif;?>
	<div class="article-card__content">
		<div class="article-card__image sticky">
			<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
				<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" />
			<?endif?>
		</div>
		<div class="article-card__text">
			<?if (!empty($arResult["DETAIL_TEXT"])){?>
				<div class="block-content" data-anim="anim-3">
					<?echo $arResult["DETAIL_TEXT"];?>
				</div>
			<?}?>
		</div>
	</div>

	
</div>
