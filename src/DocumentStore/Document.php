<?php

namespace Smrtr\Bookworm\DocumentStore;

class Document
{
    /**
     * @var string
     */
    protected $index;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var bool
     */
    protected $isFrontPage;

    /**
     * @var bool
     */
    protected $isContentsPage;

    /**
     * Document constructor.
     *
     * @param string $index
     * @param string $slug
     * @param string $name
     * @param string $content
     * @param bool   $isFrontPage
     * @param bool   $isContentsPage
     */
    public function __construct($index, $slug, $name, $content = null, $isFrontPage = false, $isContentsPage = false)
    {
        $this->index = $index;
        $this->slug = $slug;
        $this->name = $name;
        $this->content = $content;
        $this->isFrontPage = $isFrontPage;
        $this->isContentsPage = $isContentsPage;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return boolean
     */
    public function isFrontPage()
    {
        return $this->isFrontPage;
    }

    /**
     * @return boolean
     */
    public function isContentsPage()
    {
        return $this->isContentsPage;
    }
}