<? use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

Loc::loadLanguageFile(__FILE__);

?><section class="write-us">
	<div class="container sec-pad write-us_pad">
		<h2 class="mainH2 mainH2_write-us"><?=Loc::getMessage('MSG_TITLE')?></h2>
		<p class="write-us__please"><?=Loc::getMessage('MSG_FORM_DESC')?></p>
		<form action="<?=$arResult['FORM_ACTION']?>">
			<input type="hidden" name="FORM_SEND" value="Y">
			<input type="hidden" name="IBLOCK_ID" value="<?=$arResult['IBLOCK_ID']?>">
			<input type="hidden" name="EVENT_TYPE" value="<?=$arParams['EVENT_TYPE']?>">
			<input type="hidden" name="TEMPLATE" value="feedback">
			<div class="row forms"><?
				foreach(array('LEFT', 'RIGHT') as $col){
					if( empty($arResult['FIELDS_' . $col]) ){
						continue;
					}

					?><div class="col-sm-<?=( empty($arResult['FIELDS_RIGHT']) && $col == 'LEFT' ) ? 12 : 6 ?>"><?
						foreach($arResult['FIELDS_' . $col] as $arField){
							if( $arField['NAME'] == 'PREVIEW_TEXT' ){
								?><textarea
									class="forms__i-text forms__area"
									placeholder="<?=Loc::getMessage('MSG_FIELD_' . $arField['NAME'])?>"
									name="FIELDS[<?=$arField['NAME']?>]"
									<?=( $arField['REQUIRED'] == 'Y' ) ? 'required' : ''?>
								></textarea><?
							}
							else{
								?><input
									class="forms__i-text <?=( preg_match('/PHONE/ui', $arField['NAME']) ) ? 'js-phone-mask' : ''?>"
									placeholder="<?=Loc::getMessage('MSG_FIELD_' . $arField['NAME'])?>"
									name="FIELDS[<?=$arField['NAME']?>]?>"
									<?=( preg_match('/EMAIL/ui', $arField['NAME']) ) ? 'type="email"' : ''?>
									<?=( $arField['REQUIRED'] == 'Y' ) ? 'required' : ''?>
								><?
							}
						}
					?></div><?
				}
			?></div>
			<div class="row forms">
				<div class="col-sm-6">
					<button class="btn forms__send-btn js-form__send-btn" type="submit"><?=Loc::getMessage('MSG_SEND_BTN')?></button>
				</div>
			</div>
		</form>

	</div>
</section>