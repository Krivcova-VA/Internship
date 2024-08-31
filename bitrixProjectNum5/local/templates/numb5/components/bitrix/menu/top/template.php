<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (empty($arResult["ALL_ITEMS"]))
	return;

?>

	<ul>
        <li><a href=""   class="menu-img-fon"  style="background-image: url(images/nv_home.png);" ><span></span></a></li>
		<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>     <!-- first level-->
			<li>
				<a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
					<?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?>
				</a>
                <?if (is_array($arColumns) && count($arColumns) > 0):?>
				<?foreach($arColumns as $key=>$arRow):?>
								<ul>
									<?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>  <!-- second level-->
										<li>
											<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>">
												<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>
											</a>
											<?if (is_array($arLevel_3) && count($arLevel_3) > 0):?>
												<ul>
													<?foreach($arLevel_3 as $itemIdLevel_3):?>	<!-- third level-->
														<li>
															<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>">
                                                                <?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?>
															</a>
														</li>
													<?endforeach;?>
												</ul>
											<?endif?>
										</li>
									<?endforeach;?>
								</ul>
						<?endforeach;?>
				<?endif?>
			</li>
		<?endforeach;?>
	</ul>
