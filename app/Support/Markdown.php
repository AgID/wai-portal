<?php

namespace App\Support;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\ExternalLink\ExternalLinkExtension;

/**
 * Markdown support class.
 */
class Markdown extends CommonMarkConverter
{
    /**
     * Default constructor.
     */
    public function __construct()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new ExternalLinkExtension());
        $config = [
            'external_link' => [
                'internal_hosts' => parse_url(config('app.url'), PHP_URL_HOST),
                'open_in_new_window' => true,
                'html_class' => 'external-link',
            ],
        ];

        parent::__construct($config, $environment);
    }

    /**
     * Render a text excerpt (before the separator).
     *
     * @param string $text the full text
     * @param string $separator the separator for the excerpt
     *
     * @return string the rendered excerpt (empty if separator is not found)
     */
    public function excerpt(string $text, string $separator = '<!--more-->')
    {
        $excerpt = explode($separator, $text, 2)[0];

        return $this->convertToHtml($excerpt !== $text ? $excerpt : '');
    }

    /**
     * Render a text remainder (after the separator).
     *
     * @param string $text the full text
     * @param string $separator the separator for the remainder
     *
     * @return string the rendered remainder (the full text if separator is not found)
     */
    public function remainder(string $text, string $separator = '<!--more-->')
    {
        $remainderArray = explode($separator, $text, 2);

        return $this->convertToHtml(end($remainderArray));
    }
}
