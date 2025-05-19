<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Tests;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ExtraOptionTest extends TestCase
{
    /**
     * @return iterable<array{ExtraOption, non-empty-array<string>}>
     */
    public static function repeatableProvider(): iterable
    {
        yield [
            new ExtraOption\Allow('/the/path'),
            ['--allow', '/the/path'],
        ];

        yield [
            new ExtraOption\BypassProxyFor('<the-value>'),
            ['--bypass-proxy-for', '<the-value>'],
        ];

        yield [
            new ExtraOption\Cookie('the-name', 'the-value'),
            ['--cookie', 'the-name', 'the-value'],
        ];

        yield [
            new ExtraOption\Cookie('the-name', ''),
            ['--cookie', 'the-name', ''],
        ];

        yield [
            new ExtraOption\CustomHeader('Content-Type', 'text/html'),
            ['--custom-header', 'Content-Type', 'text/html'],
        ];

        yield [
            new ExtraOption\CustomHeader('Content-Type', ''),
            ['--custom-header', 'Content-Type', ''],
        ];

        yield [
            new ExtraOption\Post('the-name', 'the-value'),
            ['--post', 'the-name', 'the-value'],
        ];

        yield [
            new ExtraOption\Post('the-name', ''),
            ['--post', 'the-name', ''],
        ];

        yield [
            new ExtraOption\PostFile('the-name', '/the/path'),
            ['--post-file', 'the-name', '/the/path'],
        ];

        yield [
            new ExtraOption\RunScript('console.log("js")'),
            ['--run-script', 'console.log("js")'],
        ];

        yield [
            new ExtraOption\Replace('[name]', '<the-value>'),
            ['--replace', '[name]', '<the-value>'],
        ];
    }

    /**
     * @return iterable<array{ExtraOption, non-empty-array<string>}>
     */
    public static function nonRepeatableProvider(): iterable
    {
        yield [
            new ExtraOption\NoCollate(),
            ['--no-collate'],
        ];

        yield [
            new ExtraOption\CookieJar('/the/path'),
            ['--cookie-jar', '/the/path'],
        ];

        yield [
            new ExtraOption\Copies(1),
            ['--copies', '1'],
        ];

        yield [
            new ExtraOption\Dpi(96),
            ['--dpi', '96'],
        ];

        yield [
            new ExtraOption\Grayscale(),
            ['--grayscale'],
        ];

        yield [
            new ExtraOption\ImageDpi(600),
            ['--image-dpi', '600'],
        ];

        yield [
            new ExtraOption\ImageQuality(94),
            ['--image-quality', '94'],
        ];

        yield [
            new ExtraOption\LowQuality(),
            ['--lowquality'],
        ];

        yield [
            new ExtraOption\MarginBottom('10mm'),
            ['--margin-bottom', '10mm'],
        ];

        yield [
            new ExtraOption\MarginLeft('10mm'),
            ['--margin-left', '10mm'],
        ];

        yield [
            new ExtraOption\MarginRight('10mm'),
            ['--margin-right', '10mm'],
        ];

        yield [
            new ExtraOption\MarginTop('10mm'),
            ['--margin-top', '10mm'],
        ];

        yield [
            new ExtraOption\Orientation(PageOrientation::LANDSCAPE),
            ['--orientation', 'Landscape'],
        ];

        yield [
            new ExtraOption\Orientation(PageOrientation::PORTRAIT),
            ['--orientation', 'Portrait'],
        ];

        yield [
            new ExtraOption\PageHeight('297mm'),
            ['--page-height', '297mm'],
        ];

        yield [
            new ExtraOption\PageSize('A4'),
            ['--page-size', 'A4'],
        ];

        yield [
            new ExtraOption\PageSize('Letter'),
            ['--page-size', 'Letter'],
        ];

        yield [
            new ExtraOption\PageWidth('210mm'),
            ['--page-width', '210mm'],
        ];

        yield [
            new ExtraOption\NoPdfCompression(),
            ['--no-pdf-compression'],
        ];

        yield [
            new ExtraOption\Title('<the-title>'),
            ['--title', '<the-title>'],
        ];

        yield [
            new ExtraOption\UseXserver(),
            ['--use-xserver'],
        ];

        yield [
            new ExtraOption\NoOutline(),
            ['--no-outline'],
        ];

        yield [
            new ExtraOption\OutlineDepth(4),
            ['--outline-depth', '4'],
        ];

        yield [
            new ExtraOption\NoBackground(),
            ['--no-background'],
        ];

        yield [
            new ExtraOption\CacheDir('/the/path'),
            ['--cache-dir', '/the/path'],
        ];

        yield [
            new ExtraOption\CheckboxCheckedSvg('/the/path'),
            ['--checkbox-checked-svg', '/the/path'],
        ];

        yield [
            new ExtraOption\CheckBoxSvg('/the/path'),
            ['--checkbox-svg', '/the/path'],
        ];

        yield [
            new ExtraOption\CustomHeaderPropagation(),
            ['--custom-header-propagation'],
        ];

        yield [
            new ExtraOption\DefaultHeader(),
            ['--default-header'],
        ];

        yield [
            new ExtraOption\Encoding('<the-encoding>'),
            ['--encoding', '<the-encoding>'],
        ];

        yield [
            new ExtraOption\DisableExternalLinks(),
            ['--disable-external-links'],
        ];

        yield [
            new ExtraOption\EnableForms(),
            ['--enable-forms'],
        ];

        yield [
            new ExtraOption\NoImages(),
            ['--no-images'],
        ];

        yield [
            new ExtraOption\DisableInternalLinks(),
            ['--disable-internal-links'],
        ];

        yield [
            new ExtraOption\DisableJavascript(),
            ['--disable-javascript'],
        ];

        yield [
            new ExtraOption\JavascriptDelay(200),
            ['--javascript-delay', '200'],
        ];

        yield [
            new ExtraOption\KeepRelativeLinks(),
            ['--keep-relative-links'],
        ];

        yield [
            new ExtraOption\EnableLocalFileAccess(),
            ['--enable-local-file-access'],
        ];

        yield [
            new ExtraOption\MinimumFontSize(42),
            ['--minimum-font-size', '42'],
        ];

        yield [
            new ExtraOption\ExcludeFromOutline(),
            ['--exclude-from-outline'],
        ];

        yield [
            new ExtraOption\PageOffset(0),
            ['--page-offset', '0'],
        ];

        yield [
            new ExtraOption\Password('<the-password>'),
            ['--password', '<the-password>'],
        ];

        yield [
            new ExtraOption\EnablePlugins(),
            ['--enable-plugins'],
        ];

        yield [
            new ExtraOption\PrintMediaType(),
            ['--print-media-type'],
        ];

        yield [
            new ExtraOption\Proxy('://the-proxy'),
            ['--proxy', '://the-proxy'],
        ];

        yield [
            new ExtraOption\ProxyHostnameLookup(),
            ['--proxy-hostname-lookup'],
        ];

        yield [
            new ExtraOption\RadioButtonCheckedSvg('/the/path'),
            ['--radiobutton-checked-svg', '/the/path'],
        ];

        yield [
            new ExtraOption\RadioButtonSvg('/the/path'),
            ['--radiobutton-svg', '/the/path'],
        ];

        yield [
            new ExtraOption\DisableSmartShrinking(),
            ['--disable-smart-shrinking'],
        ];

        yield [
            new ExtraOption\SslCrtPath('/the/path'),
            ['--ssl-crt-path', '/the/path'],
        ];

        yield [
            new ExtraOption\SslKeyPassword('<the-password>'),
            ['--ssl-key-password', '<the-password>'],
        ];

        yield [
            new ExtraOption\SslKeyPath('/the/path'),
            ['--ssl-key-path', '/the/path'],
        ];

        yield [
            new ExtraOption\NoStopSlowScripts(),
            ['--no-stop-slow-scripts'],
        ];

        yield [
            new ExtraOption\EnableTocBackLinks(),
            ['--enable-toc-back-links'],
        ];

        yield [
            new ExtraOption\UserStyleSheets('/the/path'),
            ['--user-style-sheet', '/the/path'],
        ];

        yield [
            new ExtraOption\Username('<the-username>'),
            ['--username', '<the-username>'],
        ];

        yield [
            new ExtraOption\ViewportSize('width=device-width, initial-scale=1'),
            ['--viewport-size', 'width=device-width, initial-scale=1'],
        ];

        yield [
            new ExtraOption\WindowStatus('<the-status>'),
            ['--window-status', '<the-status>'],
        ];

        yield [
            new ExtraOption\Zoom(1),
            ['--zoom', '1'],
        ];

        yield [
            new ExtraOption\FooterCenter('<the-footer>'),
            ['--footer-center', '<the-footer>'],
        ];

        yield [
            new ExtraOption\FooterFontName('Arial'),
            ['--footer-font-name', 'Arial'],
        ];

        yield [
            new ExtraOption\FooterFontSize(12),
            ['--footer-font-size', '12'],
        ];

        yield [
            new ExtraOption\FooterHtml('http://url'),
            ['--footer-html', 'http://url'],
        ];

        yield [
            new ExtraOption\FooterLeft('<the-text>'),
            ['--footer-left', '<the-text>'],
        ];

        yield [
            new ExtraOption\FooterLine(),
            ['--footer-line'],
        ];

        yield [
            new ExtraOption\FooterRight('<the-text>'),
            ['--footer-right', '<the-text>'],
        ];

        yield [
            new ExtraOption\FooterSpacing(0),
            ['--footer-spacing', '0'],
        ];

        yield [
            new ExtraOption\HeaderCenter('<the-header>'),
            ['--header-center', '<the-header>'],
        ];

        yield [
            new ExtraOption\HeaderFontName('Arial'),
            ['--header-font-name', 'Arial'],
        ];

        yield [
            new ExtraOption\HeaderFontSize(12),
            ['--header-font-size', '12'],
        ];

        yield [
            new ExtraOption\HeaderHtml('http://url'),
            ['--header-html', 'http://url'],
        ];

        yield [
            new ExtraOption\HeaderLeft('<the-text>'),
            ['--header-left', '<the-text>'],
        ];

        yield [
            new ExtraOption\HeaderLine(),
            ['--header-line'],
        ];

        yield [
            new ExtraOption\HeaderRight('<the-text>'),
            ['--header-right', '<the-text>'],
        ];

        yield [
            new ExtraOption\HeaderSpacing(0),
            ['--header-spacing', '0'],
        ];

        yield [
            new ExtraOption\DisableDottedLines(),
            ['--disable-dotted-lines'],
        ];

        yield [
            new ExtraOption\TocHeaderText('<toc-header-text>'),
            ['--toc-header-text', '<toc-header-text>'],
        ];

        yield [
            new ExtraOption\TocLevelIndentation('1em'),
            ['--toc-level-indentation', '1em'],
        ];

        yield [
            new ExtraOption\DisableTocLinks(),
            ['--disable-toc-links'],
        ];

        yield [
            new ExtraOption\TocTextSizeShrink(0.8),
            ['--toc-text-size-shrink', '0.8'],
        ];

        yield [
            new ExtraOption\XslStyleSheet('/the/path'),
            ['--xsl-style-sheet', '/the/path'],
        ];
    }

    /**
     * @param non-empty-array<string> $command
     */
    #[DataProvider('repeatableProvider')]
    public function testRepeatableOption(ExtraOption $option, array $command): void
    {
        self::assertTrue($option->isRepeatable());
        self::assertSame($option->getCommand(), $command);
    }

    /**
     * @param non-empty-array<string> $command
     */
    #[DataProvider('nonRepeatableProvider')]
    public function testNonRepeatableOption(ExtraOption $option, array $command): void
    {
        self::assertFalse($option->isRepeatable());
        self::assertSame($option->getCommand(), $command);
    }
}
