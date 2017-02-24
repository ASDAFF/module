<?php
/**
 *  module
 * 
 * @category	
 * @package		Sale
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main\Sale;

/**
 * Корзина покупателя
 * 
 * @category	
 * @package		Sale
 */
class Basket
{
	
	/**
	 * Обработчик, выполняемый перед добавлением товара в корзину
	 *
	 * @param array $fields Данные добавленной позиции
	 * @return void|boolean
	 */
	public static function onBeforeBasketAdd(&$fields)
	{
	}
	
	/**
	 * Обработчик, выполняемый после добавления товара в корзину
	 *
	 * @param integer $id Идентификатор добавленной позиции
	 * @param array $fields Данные добавленной позиции
	 * @return void
	 */
	public static function onBasketAdd($id, $fields)
	{
	}
}