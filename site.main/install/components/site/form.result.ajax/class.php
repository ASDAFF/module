<?

use Bitrix\Main\Context;
use \Bitrix\Main\Localization\Loc;
use Site\Main\Iblock\Prototype;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class FormResultAjax extends CBitrixComponent
{

	/**
	* подключает языковые файлы
	*/

	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}   

	/**
	* Обработка входных параметров
	* 
	* @param mixed[] $arParams
	* @return mixed[] $arParams
	*/ 

	public function onPrepareComponentParams($arParams)
	{
		// время кэширования

		$arParams["CACHE_TIME"] = (int) $arParams["CACHE_TIME"];

		return $arParams;
	}


	/**
	 * Get form fields
	 * 
	 * @throws Exception
	 * @throws \Exception
	 */
	protected function getResult()
	{
		if( empty($this->arParams['IBLOCK_CODE']) ){
			throw new \Exception('Incorrect Iblock code');
		}
		
		$obIblock = Prototype::getInstance($this->arParams['IBLOCK_CODE']);

		$arResult = array();
		$this->arParams['IBLOCK_ID'] = $arResult['IBLOCK_ID'] = $obIblock->getId();
		foreach(array('LEFT', 'RIGHT') as $col){
			foreach($this->arParams['FIELDS_' . $col] as $fieldCode){
				if( empty($fieldCode) ){
					continue;
				}

				$arResult['FIELDS_' . $col][$fieldCode]['NAME'] = $fieldCode;
				if( in_array($fieldCode, $this->arParams['FIELDS_REQUIRED']) ){
					$arResult['FIELDS_' . $col][$fieldCode]['REQUIRED'] = 'Y';
				}
			}
		}
		
		$arResult['FORM_ACTION'] = parent::GetPath() . '/ajax.php';
		
		$this->arResult = $arResult;
	}

	public function saveElem($arFields)
	{
		if( empty($arFields['FIELDS']) || empty($arFields['IBLOCK_ID']) ){
		    throw new \Exception('Incorrect fields');
		}
		
		$obIblockElem = new \CIBlockElement();
		$arFields = array_merge($arFields['FIELDS'], array('IBLOCK_ID' => $arFields['IBLOCK_ID']));
		foreach($arFields as $fieldCode => $fieldVal){
			if( preg_match('/EMAIL/i', $fieldCode) && !preg_match('/[a-zA-Z0-9_-\.]*@[a-z]{0, 50}\.[a-z]{0, 15}/') ){
				return array('ERROR_FIELDS' => array($fieldCode));
			}

			if( preg_match('/PROPERTY/i', $fieldCode) ){
				$arrFields['PROPERTY_VALUES'][str_replace('PROPERTY_', '', $fieldCode)] = $fieldVal;
			}
			else{
				$arrFields[$fieldCode] = $fieldVal;
			}
		}

		$elemId = $obIblockElem->Add($arrFields);
		return $elemId;
	}

	/**
	* выполняет логику работы компонента
	* 
	* @return void
	*/

	public function executeComponent()
	{
		$arReq = Context::getCurrent()->getRequest()->toArray();

		try
		{
			if( !empty($arReq['FORM_SEND']) ){
				$this->saveElem($arReq);
			}

			if($this->StartResultCache($this->arParams["CACHE_TIME"])){
				$this->getResult();
				$this->includeComponentTemplate($this->page); 
			}

		}
		catch (Exception $e)
		{   
			ShowError($e->getMessage());
		}
	}
}