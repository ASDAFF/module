<?
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main;

use Bitrix\Main\Context;


/**
 * Основной класс модуля
 */
class Module
{
	/**
	 * Обработчик начала отображения страницы
	 *
	 * @return void
	 */
	public static function onPageStart()
	{
		self::defineConstants();
		self::setupEventHandlers();
	}
	
	
	/**
	 * Определяет вычисляемые константы модуля
	 *
	 * @return void
	 */
	protected static function defineConstants()
	{
		define(__NAMESPACE__ . '\IS_INDEX', \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPage() == '/index.php');
		define(__NAMESPACE__ . '\IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
		define(DOC_ROOT, Context::getCurrent()->getServer()->getDocumentRoot());

		Iblock\Prototype::defineConstants();
		File::defineConstants();
		//User::defineConstants();
		//Site::defineConstants();
		
		\Bitrix\Main\EventManager::getInstance()->addEventHandler(
			'main',
			'OnProlog',
			function() {
				if (defined('SITE_TEMPLATE_PATH')) {
					define(__NAMESPACE__ . '\TEMPLATE_IMG', SITE_TEMPLATE_PATH . '/images');
				}
			}
		);
	}
	
	
	/**
	 * Добавляет обработчики событий
	 *
	 * @return void
	 */
	protected static function setupEventHandlers()
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		
		$eventManager->addEventHandler('main', 'OnBeforeEventAdd', array('\Site\Main\Form', 'OnBeforeEventAddHandler'));
		$eventManager->AddEventHandler('search', 'OnSearchGetFileContent', array('\Site\Main\Search', 'onBeforeIndex'));
	}
}