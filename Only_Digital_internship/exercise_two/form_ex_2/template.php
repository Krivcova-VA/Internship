<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @var array $arResult
 */

if ($arResult["isFormErrors"] == "Y"):?>
<?//=$arResult["FORM_ERRORS_TEXT"];?><?endif;?>
<?= $arResult["FORM_NOTE"] ?? '' ?>
<?if ($arResult["isFormNote"] != "Y")
{
?>
<div class="contact-form">
    <div class="contact-form__head">
        <?if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y"){?>
            <?if ($arResult["isFormTitle"]) {?>
                <div class="contact-form__head-title">
                    <?=$arResult["FORM_TITLE"]?>
                </div>
            <?}?>
            
            <div class="contact-form__head-text">
                <?=$arResult["FORM_DESCRIPTION"]?>
            </div>
        <?}?>
    </div>
<?=$arResult["FORM_HEADER"]?>
<div class="contact-form__form-inputs">

	<?
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}
		else{
		    if ($arQuestion["STRUCTURE"][0]["FIELD_TYPE"] == 'textarea'){?>
		    </div>
		         <div class="contact-form__form-message">
                    <div class="input">
		    <?}else{?>
		       <div class="input contact-form__input">
		    <?}?>
	
		
		    <label class="input__label">
				
				<div class="input__label-text">
				     <?=$arQuestion["CAPTION"]?>
				
    				<?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
    				<?=$arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />".$arQuestion["IMAGE"]["HTML_CODE"] : ""?>
				</div>
				
			    <?=$arQuestion["HTML_CODE"]?>
			    
			    <?if (isset($arResult["FORM_ERRORS"][$FIELD_SID])):?>
				    <div class="input__notification"><?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?></div>
				<?endif;?>
			</label>
		</div>
	<?
		}
	} //endwhile
	?>
</div>

    
<?
if($arResult["isUseCaptcha"] == "Y")
{
?>
		<tr>
			<th colspan="2"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" /><img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" alt=""/></td>
		</tr>
		<tr>
			<td><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$arResult["REQUIRED_SIGN"];?></td>
			<td><input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" /></td>
		</tr>
<?
} // isUseCaptcha
?>
	<div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что
                ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных
                данных&raquo;.
            </div>
             <button class="form-button contact-form__bottom-button" data-success="Отправлено" data-error="Ошибка отправки">
                <div class="form-button__title">
				<input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
				</div>
            </button>
        </div>
    </form>
				<!-- <?if ($arResult["F_RIGHT"] >= 15):?>
				&nbsp;<input type="hidden" name="web_form_apply" value="Y" /><input type="submit" name="web_form_apply" value="<?=GetMessage("FORM_APPLY")?>" />
				<?endif;?>
				&nbsp;<input type="reset" value="<?=GetMessage("FORM_RESET");?>" /> -->
		
<p>
<?=$arResult["REQUIRED_SIGN"];?> - <?=GetMessage("FORM_REQUIRED_FIELDS")?>
</p>


<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)?>
</div>
