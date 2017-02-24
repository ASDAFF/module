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
 * Заказ в магазине
 * 
 * @category	
 * @package		Sale
 */
class Order
{
	/**
	 * Обработчик, выполняемый при обновлении заказа
	 *
	 * @param integer $id Идентификатор заказа
	 * @param array $fields Данные заказа
	 * @return void
	 */
	public static function onOrderUpdate($id, $fields)
	{
	}
}