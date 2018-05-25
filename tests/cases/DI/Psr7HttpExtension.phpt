<?php declare(strict_types = 1);

/**
 * Test: DI\Psr7HttpExtension
 */

use Contributte\Psr7\DI\Psr7HttpExtension;
use Contributte\Psr7\Psr7Request;
use Contributte\Psr7\Psr7Uri;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('psr7', new Psr7HttpExtension());
		$compiler->addExtension('http', new HttpExtension());
		$compiler->loadConfig(FileMock::create('
        services:
            http.request: Nette\Http\Request(Nette\Http\UrlScript(https://github.com))
        ', 'neon'));
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(Psr7Request::class, $container->getService('psr7.request'));

	/** @var Psr7Request $psr7Request */
	$psr7Request = $container->getService('psr7.request');
	Assert::type(Psr7Uri::class, $psr7Request->getUri());
	Assert::equal('https://github.com/', (string) $psr7Request->getUri());
});
