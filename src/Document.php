<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Document
{
	/**
	 * @var string $hash
	 */
	protected $id;

	/**
	 * @var string $name
	 */
	protected $name;

	/**
	 * @var string $src
	 */
	protected $src;

	/**
	 * @var string $dst
	 */
	protected $dst;

	/**
	 * @var string $link
	 */
	protected $link;

	/**
	 * @var bool $isDir
	 */
	protected $isDir;

	/**
	 * @var string $contents 
	 */
	protected $contents;

	/**
	 * @var boolean $isContentsPage
	 */
	protected $isContentsPage = false;

	/**
	 * @var string $tableOfContents
	 */
	protected $tableOfContents;

	/**
	 * Constructor.
	 *
	 * @param string $id
	 * @param string $src
	 * @param \Smrtr\Bookworm\Config $config
	 * @return void
	 */
	public function __construct($id, $src, Config $config)
	{	
		$this->id = $id;
		$this->src = $src;
		$this->config = $config;
		$this->slug = $this->makeSlug($config);
		$this->name = $this->makeName();
		$this->isDir = (bool)is_dir($this->src);
		$this->dst = $this->makeDestinationDirectory($config);

		if($this->isFrontPage()) {
			$this->dst = $config->rootPath($this->slug);
			$this->isContentsPage = true;
		}
		elseif($this->isDirectory()){
			$this->dst = $this->dst . '/Overview.md';
			$this->src = $this->src . '/Overview.md';
			$this->isContentsPage = true;
		}

		$this->link = $this->makeLink($config);

		if(file_exists($this->src)) {
			$this->contents = file_get_contents($this->src);
		}

		if(! $this->isContentsPage) {
			$this->applyTableOfContents();
		}
	}

	/**
	 * Get contents.
	 *
	 * @return string
	 */
	public function isFrontPage()
	{
		return 
		strtolower(trim($this->slug, '/ ')) === 
		strtolower(trim($this->config->getConfig('front-page'), '/ '));
	}

	/**
	 * Get contents.
	 *
	 * @return string
	 */
	public function contents()
	{
		return $this->contents;
	}

	/**
	 * Apply table of contents
	 *
	 * @return string
	 */
	public function applyTableOfContents()
	{
		$toc = (new TableOfContents($this->contents))->compile();

		$tableOfContents = null;

		foreach($toc->toArray() as $item) {
			$tableOfContents .= sprintf(
				"%s - [%s](%s) \n", 
				$item['padding'], 
				$item['text'], 
				'#' . $item['slug']
			);
		}

		$break = "\n-------------\n\n";

		$this->contents = str_replace("[TOC]", "{$break}$tableOfContents{$break}", $this->contents);
	}

	/**
	 * Get contents.
	 *
	 * @return string
	 */
	public function setContents($contents)
	{
		$this->contents = $contents;
	}

	/**
	 * Get file id.
	 *
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Get link.
	 *
	 * @return string
	 */
	public function link()
	{
		return $this->link;
	}

	/**
	 * Get destination.
	 *
	 * @return string
	 */
	public function getDestinationPath()
	{
		return $this->dst;
	}

	/**
	 * Make destination directory.
	 *
	 * @param \Smrtr\Bookworm\Config $config
	 * @return string
	 */
	protected function makeLink(Config $config)
	{
		return $config->getConfig('base-url', null) 
		. '/' 
		. Str::replaceFirst($config->rootPath(), null, $this->dst);
	}

	/**
	 * Make destination directory.
	 *
	 * @param \Smrtr\Bookworm\Config $config
	 * @return string
	 */
	protected function makeDestinationDirectory(Config $config)
	{
		return $config->getPublishedDirectory() 
		. $this->slug;
	}

	/**
	 * Make destination directory.
	 *
	 * @return string
	 */
	protected function makeSlug(Config $config)
	{
		return Str::replaceFirst(
			$config->getSourceDirectory(), null, $this->src
		);
	}

	/**
	 * Make destination directory.
	 *
	 * @return string
	 */
	protected function makeName()
	{
		$name = str_replace(['-', '_'], ' ', (new File)->name($this->src));

		return $this->dropNumbering($name);
	}

	/**
     * Drop numbering
     *
     * Match a pattern at the begining on a string and only drop if
     * it doesn't cause the resulting string to be empty
     *
     * @param  string  $pattern
     * @param  string  $str
     * @return string
     */
    protected function dropNumbering($str)
    {
        $str = trim($str);

        if(preg_match('/^(\d+\.?)+$/', $str)) {
            return $str;
        }

        return trim(preg_replace('/^(\d+\.?)+/i', '', $str));
    }

	/**
	 * Is directory?
	 *
	 * @return bool
	 */
	public function isDirectory()
	{
		return $this->isDir;
	}

	/**
	 * Is directory?
	 *
	 * @return bool
	 */
	public function isContentsPage()
	{
		return $this->isContentsPage;
	}
}