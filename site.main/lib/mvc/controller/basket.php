<?php
/**
 *  module
 *
 * @category	
 * @package		MVC
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */
namespace Site\Main\Mvc\Controller;
use Site\Main as Main;
use Site\Main\Mvc as Mvc;
use Site\Main\Mvc\View;
use \Bitrix\Main\Loader;

Loader::includeModule('sale');
/**
 * Контроллер для Корзины
 *
 * @category	
 * @package		MVC
 */
class Basket extends Prototype
{
    /**
     * Удаляет товар из корзины
     *
     * @return void
     */
    public function removeAction()
    {
        $this->view = new Mvc\View\Json();
        $this->returnAsIs = true;
        $id = $this->getParam("ID");
        \CSaleBasket::Delete($id);
    }
    /**
     * Изменение количества покупаемого товара
     *
     */
    public function updateCountAction() {
        $this->view = new Mvc\View\Json();
        $this->returnAsIs = true;
        $id = $this->getParam("ID");
        $quantity = $this->getParam("QUANTITY");
        \CSaleBasket::Update($id, array("QUANTITY" => $quantity));
    }
}
?>