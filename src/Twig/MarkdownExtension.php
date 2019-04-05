<?php

namespace App\Twig;

use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MarkdownExtension extends AbstractExtension
{
    /** @var Parsedown  */
    private $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('markdown', array($this, 'convertFromMarkdown')),
        );
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function convertFromMarkdown(string $text)
    {
        return $this->parsedown->text($text);
    }
}
