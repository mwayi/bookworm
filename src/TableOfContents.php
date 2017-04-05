<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TableOfContents
{
	/**
	 * @var string $content
	 */
	protected $content;

	/**
	 * @var integer $minOffset
	 */
	protected $minOffset;

    /**
     * @var array
     */
    protected $headers;

    /**
     * TableOfContents constructor.
     *
     * @param $content
     */
	public function __construct($content)
	{
		$this->setContent($content);
	}

	/**
	 * Process the script
	 *
	 * @return $this
	 */
	public function compile()
	{	
		$this->headers = [];
		
		foreach($this->getLines() as $line) {
			$this->setTableOfContents($line);
		}

		foreach($this->headers as &$header) {
			$header['level'] = ($header['level'] - $this->minOffset);
		}

		return $this;
	}

	/**
	 * Get contents.
	 *
	 * @param string $content
	 * @return void
	 */
	public function setTableOfContents($line)
	{
		preg_match('/^#{1,}+/', $line, $matches);
		$hashes = array_shift($matches);
		$count = substr_count($hashes, '#');


		if($count > 1) {
			$padding = $this->getPadding($level = ($count - 2));

			if(is_null($this->minOffset)) {
				$this->minOffset = $level;
			}

			$this->minOffset = min($this->minOffset, $level);

			$text = $this->getHeading($line);
			$slug = $this->getSlug($text);
			$this->headers[$slug] = compact('level', 'padding', 'text', 'slug');
		}
	}

	/**
	 * Make heading
	 *
	 * @param integer $level
	 * @return string
	 */
	protected function getHeading($str)
	{
		return ltrim($str, '# ');
	}

    /**
     * @param $str
     *
     * @return mixed|string
     */
	public function getSlug($str)
	{
		$slug = Str::slug($str);

		$i = 0;

		while(isset($this->headers[$slug])) {
			preg_match('/\-([\d]+)$/', $slug, $matches);
			$index = (int)Arr::get($matches, 1) + 1;
			$slug = preg_replace('/\-[\d]+$/', '', $slug);
			$slug = $slug . '-' . $index;
		}

		return $slug;
	}

	/**
	 * Get padding
	 *
	 * @param integer $level
	 * @return string
	 */
	public function getPadding($level)
	{
		return str_pad('', 4 * $level, ' ');
	}

	/**
	 * Get contents.
	 *
     * @return array
	 */
	public function getLines()
	{
		return array_filter(preg_split('/\n/', $this->content));
	}

	/**
	 * Get contents.
	 *
	 * @param string $content
     *
	 * @return void
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Get table of contents.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->headers;
	}
}