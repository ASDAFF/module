<? use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arReq = Context::getCurrent()->getRequest()->toArray();
Loc::loadLanguageFile(__FILE__);

if( !empty($arReq['FORM_SEND']) && !empty($arReq['IBLOCK_ID']) && !empty($arReq['FIELDS']) && !empty($arReq['EVENT_TYPE']) ){
	\CBitrixComponent::includeComponentClass('site:form.result.ajax');
	$obClass = new FormResultAjax();
	
	// Load current template messages file	
	Loc::loadMessages(dirname(__FILE__) . '/templates/' . $arReq['TEMPLATE'] . '/template.php');

	// Add new iblock elem	
	$elemId = $obClass->saveElem($arReq);
	// Send message	
	$eventId = \CEvent::Send($arReq['EVENT_TYPE'], 's1', $arReq['FIELDS']);

	// Check, are there an error fields in result array	
	if( !empty($elemId['ERROR_FIELDS']) ){
		$arResult = array('ERROR' => true);
		foreach($elemId['ERROR_FIELDS'] as $fieldCode){
			$arResult['ERROR_TEXT'][$fieldCode] = Loc::getMessage('MSG_FIELD_' . $fieldCode . '_ERROR');
		}
		$arResult['ERROR_TEXT'] = implode('.<br>', $arResult['ERROR_TEXT']);
	}
	elseif( !empty($elemId) && !empty($eventId) ){
		$arResult = array('SUCCESS' => true, 'SUCCESS_TEXT' => Loc::getMessage('MSG_SUCCESS'));
	}
	else{
		$arResult = array('ERROR' => true, 'ERROR_TEXT' => Loc::getMessage('MSG_ERROR'));
	}

	echo json_encode($arResult);
}