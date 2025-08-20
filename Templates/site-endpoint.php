<?php

	const STOIC_CORE_PATH = '{$corePath}';
	require(STOIC_CORE_PATH . 'inc/core.php');

	use League\Plates\Engine;
	use Stoic\Web\PageHelper;
	use Stoic\Web\Stoic;

	$stoic = Stoic::getInstance(STOIC_CORE_PATH);

	$page = PageHelper::getPage('{$page}.php');
	$page->setTitle('{$pageName}');

	$tpl = new Engine(null, 'tpl.php');
	$tpl->addFolder('page', STOIC_CORE_PATH . 'tpl/{$page}');

	echo(
		$tpl->render(
			'page::index',
			[
				'page' => $page
			]
		)
	);
