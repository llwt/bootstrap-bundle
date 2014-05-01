<?php
/**
 * This file is part of BraincraftedBootstrapBundle.
 *
 * (c) 2012-2013 by Florian Eckerstorfer
 */

namespace Braincrafted\Bundle\BootstrapBundle\Tests\Twig;

use Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension;

/**
 * BootstrapIconExtensionTest
 *
 * This test is only useful if you consider that it will be run by Travis on every supported PHP
 * configuration. We live in a world where should not have too manually test every commit with every
 * version of PHP. And I know exactly that I will commit short array syntax all the time and break
 * compatibility with PHP 5.3
 *
 * @category   Test
 * @package    BraincraftedBootstrapBundle
 * @subpackage Twig
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2012-2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @link       http://bootstrap.braincrafted.com Bootstrap for Symfony2
 * @group      unit
 */
class BootstrapIconExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $expectedResponse;
    /** @var array */
    private $extensionOptions;

    public function setUp()
    {
        $this->expectedResponse = 'the-rendered-response-' . rand();
        $this->extensionOptions = array(
            'icon_prefix'                           => null,
            'icon_template'                         => null,
            'icon_short_methods_enable'             => null,
            'icon_short_methods_fontawesome_prefix' => null,
            'icon_short_methods_glyphicon_prefix'   => null
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::getFilters()
     */
    public function testGetFilters()
    {
        $this->assertCount(1, $this->createIconExtension()->getFilters());
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::getFunctions()
     */
    public function testGetFunctionsWithoutShortMethods()
    {
        $this->extensionOptions['icon_short_methods_enable'] = false;
        $extension = $this->createIconExtension();

        $this->assertCount(
            1,
            $extension->getFunctions(),
            '->getFunctions() returns only the default "iconFunction" when ' .
            '$iconShortMethods is false'
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::getFunctions()
     */
    public function testGetFunctionsWithShortMethods()
    {
        $this->extensionOptions['icon_short_methods_enable'] = true;
        $extension = $this->createIconExtension();

        $this->assertCount(
            3,
            $extension->getFunctions(),
            '->getFunctions() returns the short-methods in addition to the ' .
            'default "iconFunction" when $iconShortMethods is true'
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::iconFunction
     */
    public function testIconFilterUsingDefaults()
    {
        $extension = $this->createIconExtension($expectedIcon = 'some-icon');

        $this->assertEquals(
            $this->expectedResponse,
            $extension->iconFunction($expectedIcon)
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::iconFunction
     */
    public function testIconFilterWithIconPrefixSetInConstructor()
    {
        $expectedIconPrefix = 'icon-prefix-passed-to-constructor';
        $this->extensionOptions['icon_prefix'] = $expectedIconPrefix;

        $extension = $this->createIconExtension(
            $expectedIcon = 'bar',
            '',
            $expectedIconPrefix
        );

        $this->assertEquals(
            $this->expectedResponse,
            $extension->iconFunction($expectedIcon)
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::iconFunction
     */
    public function testIconFilterWithIconPrefixOverriddenInMethodCall()
    {
        $expectedIconPrefix = 'icon-prefix-passed-to-method';
        $extension          = $this->createIconExtension(
            $expectedIcon = 'foo',
            '',
            $expectedIconPrefix
        );

        $this->assertEquals(
            $this->expectedResponse,
            $extension->iconFunction($expectedIcon, '', $expectedIconPrefix)
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::iconFunction
     */
    public function testIconFilterWithAdditionalClassesPassedInAsString()
    {
        $extension = $this->createIconExtension(
            $expectedIcon           = 'baz',
            $expectedAddClassString = 'should add this class string'
        );

        $this->assertEquals(
            $this->expectedResponse,
            $extension->iconFunction($expectedIcon, $expectedAddClassString)
        );

    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::iconFunction
     */
    public function testIconFilterWithAdditionalClassesPassedInAsArray()
    {
        $addClassArray = array('should', 'add', 'this', 'class', 'string');
        $extension     = $this->createIconExtension(
            $expectedIcon           = 'baz',
            $expectedAddClassString = implode(' ', $addClassArray)
        );

        $this->assertEquals(
            $this->expectedResponse,
            $extension->iconFunction($expectedIcon, $addClassArray)
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::parseIconsFilter
     */
    public function testParseIconsFilter()
    {
        $this->expectedResponse = 'was replaced';
        $extension              = $this->createIconExtension('foo');
        $this->assertEquals(
            'this -> was replaced <- should be replaced',
            $extension->parseIconsFilter(
                'this -> .icon-foo <- should be replaced'
            ),
            '->parseIconsFilter() returns the HTML code with the replaced icons.'
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::fontAwesomeIconFunction()
     */
    public function testFontAwesomeIconFunction()
    {
        $this->extensionOptions['icon_short_methods_fontawesome_prefix'] = 'fa';
        $extension = $this->createIconExtension(
            $icon     = 'some-icon',
            $addClass = 'should add these classes',
            $expectedIconPrefix    = 'fa'
        );
        $this->assertEquals(
            $this->expectedResponse,
            $extension->fontAwesomeIconFunction($icon, $addClass)
        );
    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::glyphIconFunction()
     */
    public function testGlyphIconFunction()
    {
        $this->extensionOptions['icon_short_methods_glyphicon_prefix'] = 'gi';
        $extension = $this->createIconExtension(
            $icon     = 'bar-icon',
            $addClass = 'some extra classes',
            $expectedIconPrefix = 'gi'
        );
        $this->assertEquals(
            $this->expectedResponse,
            $extension->glyphIconFunction($icon, $addClass)
        );

    }

    /**
     * @covers Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension::getName()
     */
    public function testGetName()
    {
        $this->assertEquals('braincrafted_bootstrap_icon', $this->createIconExtension()->getName());
    }

    /**
     * Everything defaulted so we are not testing things we don't care about
     * @param string $expectedResponse
     * @param string $icon
     * @param string $prefix
     * @param string $addClass
     *
     * @return BootstrapIconExtension
     */
    private function createIconExtension(
        $icon = '',
        $addClass = '',
        $expectedIconPrefix = null
    ) {
        $templatingMock     = $this->getTemplatingMock();
        $expectedIconPrefix = (null === $expectedIconPrefix)
            ? $this->extensionOptions['icon_prefix']
            : $expectedIconPrefix;

        $expectedParams     = array(
            'icon'      => $icon,
            'prefix'    => $expectedIconPrefix,
            'add_class' => $addClass
        );

        if (!empty($icon)) {
            $templatingMock
                ->expects($this->once())
                ->method('render')
                ->with($this->extensionOptions['icon_template'], $expectedParams)
                ->will($this->returnValue($this->expectedResponse))
            ;
        }

        return new BootstrapIconExtension(
            $templatingMock,
            $this->extensionOptions
        );
    }

    private function getTemplatingMock()
    {
        return $this->getMock('\Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
    }
}
