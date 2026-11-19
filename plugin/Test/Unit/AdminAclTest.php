<?php
declare(strict_types=1);

namespace LiveChat\LiveChat\Test\Unit;

use PHPUnit\Framework\TestCase;

class AdminAclTest extends TestCase
{
	private const MODULE_ROOT = __DIR__ . '/../..';
	private const RESOURCE_DECLARATION = "const ADMIN_RESOURCE = 'LiveChat_LiveChat::mymodule';";

	public function testAclDeclaresConfigResource(): void
	{
		$xml = simplexml_load_file(self::MODULE_ROOT . '/etc/acl.xml');

		$this->assertNotFalse($xml);
		$ids = array_map('strval', $xml->xpath('//resource/@id'));
		$this->assertContains('LiveChat_LiveChat::mymodule', $ids);
	}

	public function testConfigSectionUsesDeclaredResource(): void
	{
		$xml = simplexml_load_file(self::MODULE_ROOT . '/etc/adminhtml/system.xml');

		$this->assertNotFalse($xml);
		$this->assertSame('LiveChat_LiveChat::mymodule', (string)$xml->xpath('//section/resource')[0]);
	}

	public function testEveryAdminControllerDeclaresAdminResource(): void
	{
		$files = glob(self::MODULE_ROOT . '/Controller/Adminhtml/*/*.php');

		$this->assertNotEmpty($files);
		foreach ($files as $file) {
			$contents = file_get_contents($file);
			$this->assertStringContainsString(self::RESOURCE_DECLARATION, $contents, $file);
		}
	}
}
