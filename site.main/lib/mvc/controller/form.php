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

/**
 * Контроллер для веб-форм
 * 
 * @category	
 * @package		MVC
 */
class Form extends Prototype
{
	/**
	 * Выводит форму обратной связи
	 *
	 * @return string
	 */
	public function feedbackAction()
	{
		return $this->getForm('FEEDBACK');
	}
	
	/**
	 * Выводит форму обратного звонка
	 *
	 * @return string
	 */
	public function callbackAction()
	{
		return $this->getForm('CALLBACK');
	}
	
	/**
	 * Выводит компонент добавления результата формы
	 *
	 * @param integer $sid Символьный код формы
	 * @return string
	 */
	protected function getForm($sid)
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;
		
		$sid = trim($sid);
		if (!$sid) {
			throw new Main\Exception('Form SID is undefined.');
		}
		
		\Bitrix\Main\Loader::includeModule('form');
		$form = \CForm::GetBySID($sid)->Fetch();
		if (!$form) {
			throw new Main\Exception('The form is not found.');
		}
		
		return $this->getComponent(
			'bitrix:form.result.new',
			'.default',
			array(
				'WEB_FORM_ID' => $form['ID'],
				'IGNORE_CUSTOM_TEMPLATE' => 'N',
				'USE_EXTENDED_ERRORS' => 'Y',
				'SEF_MODE' => 'N',
				'SEF_FOLDER' => '/',
				'CACHE_TYPE' => 'A',
				'CACHE_TIME' => '3600',
				'LIST_URL' => '',
				'EDIT_URL' => '',
				'SUCCESS_URL' => '',
				'CHAIN_ITEM_TEXT' => '',
				'CHAIN_ITEM_LINK' => '',
				'HIDE_TITLE' => 'N',
				'POPUP_MODE' => 'Y',
				'VARIABLE_ALIASES' => array(
					'WEB_FORM_ID' => 'WEB_FORM_ID',
					'RESULT_ID' => 'RESULT_ID',
				)
			)
		);
	}
}