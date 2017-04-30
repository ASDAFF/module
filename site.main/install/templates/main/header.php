<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (defined('\Site\Main\IS_AJAX') && \Site\Main\IS_AJAX) {
	return;
}

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

$obAsset = Asset::getInstance();
$obAsset->addString('<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>');
$obAsset->addJs(SITE_TEMPLATE_PATH . "/js/jquery.maskedinput.min.js");
$obAsset->addJs(SITE_TEMPLATE_PATH . "/js/site.js");
$obAsset->addJs(SITE_TEMPLATE_PATH . "/js/template.js");


/* Add additional stylesheets */
//$obAsset->addString('<link rel="stylesheet" href="//fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext,cyrillic"/>');

/* Add stylesheets by requested path */
//\Site\Main\Util::addCSSLinksByPath();
	
?><!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
	<head>
		<title><?$APPLICATION->ShowTitle()?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		
		<?$APPLICATION->ShowHead()?>
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries. Selectivizr IE8 support of CSS3-selectors -->
		<!--[if lt IE 9]>
			<script data-skip-moving="true" src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script data-skip-moving="true" src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
			<script data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH?>/js/selectivizr-min.js"></script>
		<![endif]-->
	</head>
	<body role="document">
		<?$APPLICATION->ShowPanel()?>