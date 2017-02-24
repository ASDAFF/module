<?php
/**
 *  module
 * 
 * @category	
 * @copyright	2014  LTD
 * @link		http://.ru
 */

namespace Site\Main;

/**
 * Работа с гео-данными постетителей
 */
class GeoServices
{
	// ID инфоблока с городами
	const CITIES_IBLOCK_ID = 49;
	
	// ID элемента города по умолчанию
	const DEFAUL_CITY_ID = 6288;
	

	/**
	 * Данные о текущем местоположении из таблицы местоположений модуля интернет-магазина
	 *
	 * @var array|null
	 */
	protected static $currentLocation = null;
	
	/**
	 * Возвращает данные о местоположении из модуля статистики
	 *
	 * @return array
	 */
	public static function getCurrentInfo()
	{
		if (!\Bitrix\Main\Loader::includeModule('statistic')) {
			throw new Main\Exception("Statistic module isn't installed.");
		}
		
		$city = new \CCity();
		return $city->GetFullInfo();
	}
	
	/**
	 * Возвращает данные о местоположении из таблицы местоположений модуля интернет-магазина
	 *
	 * @return array
	 */
	public static function getCurrentLocation()
	{
		if (self::$currentLocation !== null) {
			return self::$currentLocation;
		}
		
		if (!\Bitrix\Main\Loader::includeModule('sale')) {
			throw new Main\Exception("Sale module is't installed.");
		}
		
		self::$currentLocation = array();
		$info = self::getCurrentInfo();
		
		foreach(array(
			'CITY_NAME' => 'CITY_NAME',
			'REGION_NAME' => 'REGION_NAME',
			'COUNTRY_NAME' => 'COUNTRY_NAME',
		) as $infoKey => $locationKey) {
			if (!$info[$infoKey]['VALUE']) {
				continue;
			}
			foreach (array('en', 'ru') as $lid) {
				self::$currentLocation = self::getLocationRecordset(array(
					'LID' => $lid,
					$locationKey => $info[$infoKey]['VALUE']
				))->Fetch();
				
				if (self::$currentLocation) {
					break;
				}
			}
			
			if (self::$currentLocation) {
				break;
			}
		}
		
		if (self::$currentLocation) {
			self::$currentLocation = \CSaleLocation::GetByID(self::$currentLocation['ID']);
		} else {
			self::$currentLocation = array();
		}
		
		return self::$currentLocation;
	}
	
	/**
	 * Возвращает данные о местоположении из таблицы местоположений модуля интернет-магазина
	 *
	 * @return array
	 */
	protected static function getLocationRecordset($filter)
	{
		return \CSaleLocation::GetList(
			array(
				'SORT' => 'ASC',
			),
			$filter,
			false,
			false,
			array()
		);
	}
	
	
	/**
	* Получаем ID города  
	* Сначала из кук и, если не задан, по IP. 
	* Если и такого нет, получаем Москву. 
	* 
	*/
	function GetCityID()
	{
		$arCity = self::GetCity();
			
		return $arCity["ID"];
	}
	
	/**
	* Получаем город 
	* Сначала из кук и, если не задан, по IP. 
	* Если и такого нет, получаем Москву. 
	* 
	*/
	function GetCity()
	{
		$arCity = self::GetCityFromCookies();

		if(!is_array($arCity))
			$arCity = self::GetCityByIP();
			
		if(!is_array($arCity))
			$arCity = self::GetCityElementByID(self::DEFAUL_CITY_ID);
			
		return $arCity;
	}
	
	/**
	* Получаем город из кук
	* 
	*/
	function GetCityFromCookies()
	{
		global $APPLICATION;
		$city_id = $APPLICATION->get_cookie("CITY_ID");
		
		if($city_id > 0)
			$arCity = self::GetCityElementByID($city_id);
		
		if(is_array($arCity))
			return $arCity;
		else
			return false;
	}
	
	/**
	* Получаем город по IP
	* 
	*/
	function GetCityByIP()
	{
		$code = self::GetCityCodeByIP();
		
		if(strlen($code) > 0)
			return self::GetCityElementByCode($code);
		else
			return false;
	}
	
	/**
	* Получает код города по IP
	* 
	*/
	function GetCityCodeByIP()
	{
		$obCity = new \CCity();
		$arCity = $obCity->GetFullInfo();
		
		if(strlen($arCity["CITY_NAME"]["VALUE"]) > 0)
			return $arCity["CITY_NAME"]["VALUE"];
		else
			return false;
		
	}
	
	/**
	* Устанавливает в куки город с указаным ID
	* 
	* @param int $city_id
	* @param int $cookie_time
	*/
	function SetCityToCookie($city_id, $cookie_time)
	{
		$city_id = intval($city_id);
		$cookie_time = intval($cookie_time);
		if(!$city_id > 0)
			return false;
		
		if(!$cookie_time > 0)
			$cookie_time = time() + 3600 * 24 * 7;
			
		global $APPLICATION;
		$APPLICATION->set_cookie("CITY_ID", $city_id, $cookie_time, '/', $_SERVER["HTTP_HOST"]);
	}
	
	/**
	* Получаем элемент города по символьному коду (регистронезависимо)
	* 
	* @param string $code
	* @return {array|mixed[]}
	*/
	function GetCityElementByCode($code)
	{
		$code = trim($code);

		if(!strlen($code) > 0)
			return false;
		
		$CacheId = serialize(array($code, self::CITIES_IBLOCK_ID));
		if(\COption::getOptionString("main", "component_cache_on") == "Y")
			$CacheTime = 60 * 60;
		else
			$CacheTime = 0;
		$CacheFolder = '/Site/Main/GeoServices/GetCityElementByCode';
		$obCache = new \CPHPCache;
		$arResult = false;
		if($obCache->InitCache($CacheTime, $CacheId, $CacheFolder))
		{
			$arResult = $obCache->GetVars();
		}
		elseif($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder))
		{
			$arFilter = array(
			    "IBLOCK_ID" => self::CITIES_IBLOCK_ID, 
			    "ACTIVE" => "Y",
			    "=CODE" => $code	// регистронезависимый поиск по коду
			);
			$arSelect = array(
			    "ID", "IBLOCK_ID", "NAME", "CODE"
			);
			$dbEl = \CIBlockElement::GetList(array(), $arFilter, false, array('nTopCount' => 1), $arSelect);    
			if($arItem = $dbEl->GetNext())    
			{
				$arResult = $arItem;    
			    $obCache->EndDataCache($arResult);
			}
			else
			    $obCache->AbortDataCache();
		}
		return $arResult;
	}
	
	/**
	* Получает элемент города по его ID
	* 
	* @param int $city_id
	* @return {array|mixed[]}
	*/
	function GetCityElementByID($city_id)
	{
		$city_id = intval($city_id);

		if(!strlen($city_id) > 0)
			return false;
		
		$CacheId = serialize(array($city_id, self::CITIES_IBLOCK_ID));
		if(\COption::getOptionString("main", "component_cache_on") == "Y")
			$CacheTime = 60 * 60;
		else
			$CacheTime = 0;
		$CacheFolder = '/Site/Main/GeoServices/GetCityElementByID';
		$obCache = new \CPHPCache;
		$arResult = false;
		if($obCache->InitCache($CacheTime, $CacheId, $CacheFolder))
		{
			$arResult = $obCache->GetVars();
		}
		elseif($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder))
		{
			$arFilter = array(
			    "IBLOCK_ID" => self::CITIES_IBLOCK_ID, 
			    "ACTIVE" => "Y",
			    "ID" => $city_id
			);
			$arSelect = array(
			    "ID", "IBLOCK_ID", "NAME", "CODE"
			);
			$dbEl = \CIBlockElement::GetList(array(), $arFilter, false, array('nTopCount' => 1), $arSelect);    
			if($arItem = $dbEl->GetNext())    
			{
				$arResult = $arItem;    
			    $obCache->EndDataCache($arResult);
			}
			else
			    $obCache->AbortDataCache();
		}
		return $arResult;
	}	
	
	
}