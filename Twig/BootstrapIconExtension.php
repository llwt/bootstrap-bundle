<?php
/**
 * This file is part of BraincraftedBootstrapBundle.
 * (c) 2012-2013 by Florian Eckerstorfer
 */

namespace Braincrafted\Bundle\BootstrapBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;

/**
 * BootstrapIconExtension
 *
 * @package    BraincraftedBootstrapBundle
 * @subpackage Twig
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2012-2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @link       http://bootstrap.braincrafted.com Bootstrap for Symfony2
 */
class BootstrapIconExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        EngineInterface $templating,
        array $options = array()
    ) {
        $this->templating = $templating;
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            'parse_icons' => new Twig_Filter_Method(
                $this,
                'parseIconsFilter',
                array('pre_escape' => 'html', 'is_safe' => array('html'))
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $functions = array(
            'icon' => new Twig_Function_Method(
                $this,
                'iconFunction',
                array('pre_escape' => 'html', 'is_safe' => array('html'))
            )
        );
        if (true === $this->options['icon_short_methods_enable']) {
            $functions['fa'] = new Twig_Function_Method(
                $this,
                'fontAwesomeIconFunction',
                array('pre_escape' => 'html', 'is_safe' => array('html'))
            );
            $functions['gi'] = new Twig_Function_Method(
                $this,
                'glyphIconFunction',
                array('pre_escape' => 'html', 'is_safe' => array('html'))
            );
        }

        return $functions;
    }

    /**
     * Parses the given string and replaces all occurrences of .icon-[name] with the corresponding icon.
     *
     * @param string $text  The text to parse
     *
     * @return string The HTML code with the icons
     */
    public function parseIconsFilter($text)
    {
        $that = $this;
        return preg_replace_callback(
            '/\.icon-([a-z0-9-]+)/',
            function ($matches) use ($that) {
                return $that->iconFunction($matches[1]);
            },
            $text
        );
    }

    /**
     * Returns the HTML code for the given icon.
     *
     * @param string       $icon The name of the icon
     * @param string|array $addClass
     * @param string       $prefixOverride Overrides the prefix set in the config if present
     *
     * @return string The HTML code for the icon
     */
    public function iconFunction($icon, $addClass = '', $prefixOverride = null)
    {
        $prefix   = (null === $prefixOverride)
            ? $this->options['icon_prefix']
            : $prefixOverride ;
        $addClass = is_array($addClass) ? implode(' ', $addClass) : $addClass;

        return $this->templating->render(
            $this->options['icon_template'],
            array('icon' => $icon, 'prefix' => $prefix, 'add_class' => $addClass)
        );
    }

    /**
     * Shorthand for generating font-awesome icons
     *
     * @param string       $icon
     * @param string|array $addClass
     *
     * @return string The HTML for the icon
     */
    public function fontAwesomeIconFunction($icon, $addClass = '')
    {
        return $this->iconFunction(
            $icon,
            $addClass,
            $this->options['icon_short_methods_fontawesome_prefix']
        );
    }

    /**
     * Shorthand for generating Glyphicon icons
     *
     * @param string       $icon
     * @param string|array $addClass
     *
     * @return string The HTML for the icon
     */
    public function glyphIconFunction($icon, $addClass = '')
    {
        return $this->iconFunction(
            $icon,
            $addClass,
            $this->options['icon_short_methods_glyphicon_prefix']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'braincrafted_bootstrap_icon';
    }

    private function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'icon_prefix',
            'icon_template',
            'icon_short_methods_enable',
            'icon_short_methods_fontawesome_prefix',
            'icon_short_methods_glyphicon_prefix'
        ));
    }
}
