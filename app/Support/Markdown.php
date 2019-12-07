<?php

namespace App\Support;

use Parsedown;

/**
 * Markdown support class.
 */
class Markdown extends Parsedown
{
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

        return $this->text($excerpt !== $text ? $excerpt : '');
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

        return $this->text(end($remainderArray));
    }

    /**
     * Render inline links.
     *
     * @param string $excerpt the text excerpt
     *
     * @return array|null the link structure or null if parent returns null
     */
    protected function inlineLink($excerpt): ?array
    {
        $data = parent::inlineLink($excerpt);

        return $this->renderLink($data ?? []);
    }

    /**
     * Render inline urls.
     *
     * @param string $excerpt the text excerpt
     *
     * @return array|null the link structure or null if parent returns null
     */
    protected function inlineUrl($excerpt): ?array
    {
        $data = parent::inlineUrl($excerpt);

        return $this->renderLink($data ?? []);
    }

    /**
     * Render inline url tags.
     *
     * @param string $excerpt the text excerpt
     *
     * @return array|null the link structure or null if parent returns null
     */
    protected function inlineUrlTag($excerpt): ?array
    {
        $data = parent::inlineUrlTag($excerpt);

        return $this->renderLink($data ?? []);
    }

    /**
     * Customize an html rendered link.
     *
     * @param array $link the link structure
     *
     * @return array|null the customized link structure or null $link is empty
     */
    protected function renderLink(array $link): ?array
    {
        if (empty($link)) {
            return null;
        }

        $url = $link['element']['attributes']['href'];
        if (!$this->isLocal($url)) {
            $link['element']['attributes'] = array_replace($link['element']['attributes'], [
                'class' => 'external-link',
                'rel' => 'nofollow noopener',
                'target' => '_blank',
            ]);
        }

        return $link;
    }

    /**
     * Check whether the specified url is considered local.
     * Taken from https://github.com/tovic/parsedown-extra-plugin.
     *
     * @param string $url the url to check
     *
     * @return bool true if the specified url can be considered local, false otherwise
     */
    protected function isLocal($url): bool
    {
        if (empty($url) || (false !== strpos('./?&#', $url[0]) && 0 !== strpos($url, '//')) || 0 === strpos($url, 'data:') || 0 === strpos($url, 'javascript:')) {
            return true;
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else {
            $host = '';
        }

        if (0 === strpos($url, '//') && 0 !== strpos($url, '//' . $host)) {
            return false;
        }

        if (0 === strpos($url, 'https://' . $host) || 0 === strpos($url, 'http://' . $host)) {
            return true;
        }

        return false === strpos($url, '://');
    }
}
