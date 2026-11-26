<?php
declare(strict_types=1);

namespace LiveChat\LiveChat\Test\Unit;

use PHPUnit\Framework\TestCase;

class ModuleDeclarationTest extends TestCase
{
	private const MODULE_ROOT = __DIR__ . '/../..';

	public function testModuleXmlDeclaresModule(): void
	{
		$xml = simplexml_load_file(self::MODULE_ROOT . '/etc/module.xml');

		$this->assertNotFalse($xml);
		$this->assertSame('LiveChat_LiveChat', (string)$xml->module['name']);
	}

	public function testRegistrationRegistersModule(): void
	{
		$registration = file_get_contents(self::MODULE_ROOT . '/registration.php');

		$this->assertStringContainsString('LiveChat_LiveChat', $registration);
	}

	public function testComposerAutoloadsModuleNamespaceFromRoot(): void
	{
		$composer = json_decode(file_get_contents(self::MODULE_ROOT . '/composer.json'), true);

		$this->assertSame('', $composer['autoload']['psr-4']['LiveChat\\LiveChat\\']);
	}
}
