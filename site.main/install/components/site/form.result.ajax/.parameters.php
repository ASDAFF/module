<?
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;
use Site\Main\Iblock\Prototype;

if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true ) {
	die();
}

Loc::loadLanguageFile(__FILE__);

// get info block id
$iblockId = Prototype::getInstance('Forms_Feedback')->getId();
if( empty($iblockId) ){
	throw new \Exception('Invalid iblock id');
}

// merge standard iblock fields with this props 
$arFields = \CIBlockParameters::GetFieldCode('', "DATA_SOURCE");
$obProps = PropertyTable::getList(array('filter' => array('IBLOCK_ID' => $iblockId)));
while($arProp = $obProps->fetch()) {
	$arFields['VALUES']['PROPERTY_' . $arProp['CODE']] = $arProp['NAME'];
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"CACHE_TIME"      => array("DEFAULT" => 3600),
		"FIELDS_LEFT"     => array_merge($arFields, array('NAME' => Loc::getMessage("MSG_FIELDS_LEFT"))),
		"FIELDS_RIGHT"    => array_merge($arFields, array('NAME' => Loc::getMessage("MSG_FIELDS_RIGHT"))),
		"FIELDS_REQUIRED" => array_merge($arFields, array('NAME' => Loc::getMessage("MSG_FIELDS_REQUIRED"))),
		"EVENT_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MSG_EVENT_TYPE"),
			"TYPE" => "STRING",
		)
	)
);