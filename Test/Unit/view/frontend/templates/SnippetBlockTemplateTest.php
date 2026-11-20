<?php
declare(strict_types=1);

namespace LiveChat\LiveChat\Test\Unit\view\frontend\templates;

use LiveChat\LiveChat\Block\SnippetBlock;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use PHPUnit\Framework\TestCase;

class SnippetBlockTemplateTest extends TestCase
{
    private const TEMPLATE = __DIR__ . '/../../../../../view/frontend/templates/snippetblock.phtml';

    public function testSnippetScriptIsRenderedThroughSecureRenderer(): void
    {
        $block = $this->createStub(SnippetBlock::class);
        $block->method('getLicenseId')->willReturn('12345678');
        $block->method('isCartSet')->willReturn(false);
        $block->method('escapeUrl')->willReturnArgument(0);
        $block->method('getUrl')->willReturnArgument(0);

        $rendered = [];
        $secureRenderer = $this->createStub(SecureHtmlRenderer::class);
        $secureRenderer->method('renderTag')->willReturnCallback(
            function (string $tag, array $attributes, ?string $content) use (&$rendered): string {
                $rendered[] = [$tag, $content];
                return '<' . $tag . ' nonce="test">' . $content . '</' . $tag . '>';
            }
        );

        $output = $this->render($block, $secureRenderer);

        $this->assertCount(1, $rendered);
        $this->assertSame('script', $rendered[0][0]);
        $this->assertStringContainsString('window.__lc.license = 12345678;', $rendered[0][1]);
        $this->assertStringContainsString('<script nonce="test">', $output);
        $this->assertSame(1, preg_match_all('/<script(?![^>]*\ssrc=)[^>]*>/', $output));
        $this->assertSame(0, preg_match('/<script>/', $output));
    }

    public function testNotConfiguredNoticeIsRenderedThroughSecureRenderer(): void
    {
        $block = $this->createStub(SnippetBlock::class);
        $block->method('getLicenseId')->willReturn(null);

        $rendered = [];
        $secureRenderer = $this->createStub(SecureHtmlRenderer::class);
        $secureRenderer->method('renderTag')->willReturnCallback(
            function (string $tag, array $attributes, ?string $content) use (&$rendered): string {
                $rendered[] = [$tag, $content];
                return '<' . $tag . ' nonce="test">' . $content . '</' . $tag . '>';
            }
        );

        $output = $this->render($block, $secureRenderer);

        $this->assertCount(1, $rendered);
        $this->assertStringContainsString('configure your LiveChat account', $rendered[0][1]);
        $this->assertStringNotContainsString('<script>', $output);
    }

    public function testCartBranchIsIncludedInRenderedScript(): void
    {
        $block = $this->createStub(SnippetBlock::class);
        $block->method('getLicenseId')->willReturn('12345678');
        $block->method('isCartSet')->willReturn(true);
        $block->method('isOrderPlacedPage')->willReturn(false);
        $block->method('escapeUrl')->willReturnArgument(0);
        $block->method('getUrl')->willReturnArgument(0);

        $content = null;
        $secureRenderer = $this->createStub(SecureHtmlRenderer::class);
        $secureRenderer->method('renderTag')->willReturnCallback(
            function (string $tag, array $attributes, ?string $body) use (&$content): string {
                $content = $body;
                return '<script nonce="test">' . $body . '</script>';
            }
        );

        $this->render($block, $secureRenderer);

        $this->assertStringContainsString('LC_API.on_after_load', (string)$content);
        $this->assertStringContainsString('livechat/getcart', (string)$content);
    }

    public function testFallsBackToPlainScriptWithoutSecureRenderer(): void
    {
        $block = $this->createStub(SnippetBlock::class);
        $block->method('getLicenseId')->willReturn('12345678');
        $block->method('isCartSet')->willReturn(false);
        $block->method('escapeUrl')->willReturnArgument(0);
        $block->method('getUrl')->willReturnArgument(0);

        $level = ob_get_level();
        $output = $this->render($block, null);

        $this->assertSame($level, ob_get_level());
        $this->assertStringContainsString('<script>', $output);
        $this->assertStringContainsString('window.__lc.license = 12345678;', $output);
        $this->assertStringNotContainsString('nonce', $output);
    }

    public function testNotConfiguredNoticeFallsBackToPlainScriptWithoutSecureRenderer(): void
    {
        $block = $this->createStub(SnippetBlock::class);
        $block->method('getLicenseId')->willReturn(null);

        $output = $this->render($block, null);

        $this->assertStringContainsString('<script>console.log(', $output);
    }

    private function render(SnippetBlock $block, ?SecureHtmlRenderer $secureRenderer): string
    {
        if ($secureRenderer === null) {
            unset($secureRenderer);
        }
        $level = ob_get_level();
        ob_start();
        try {
            include self::TEMPLATE;
            return (string)ob_get_clean();
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }
}
