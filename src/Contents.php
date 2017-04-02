<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Str;

class Contents
{
	/**
	 * @var array $file 
	 */
	protected $contents = [];

	/**
	 * Constructor.
	 *
	 * @param string $contents
	 * @return void
	 */
	public function __construct(array $contents)
	{	
		$this->contents = $contents;
		ksort($this->contents);
	}

	/**
	 * Constructor.
	 *
	 * @param string $fileId
	 * @return void
	 */
	public function getMarkdown(array $list)
	{	
		$baseTab = $markdown = null;

		foreach($list as $item) {

			$levels = explode('.', $item->id());

			if(is_null($baseTab)) {
				$baseTab = count($levels);
			}

			$markdown .= str_pad('', 4 * (count($levels) - $baseTab), ' ') . 
			sprintf(
				"- %s [%s](%s) \n", 
				$item->id(), 
				$item->name(), 
				$item->link()
			);
		}

		return $markdown;
	}


	/**
	 * Constructor.
	 *
	 * @param string $fileId
	 * @return void
	 */
	public function getSection($fileId = null)
	{	
		if(is_null($fileId)) {
			return $this->contents;
		}
		
		$section = [];
		$open = false;

		foreach ($this->contents as $key => $item) {

			if($key == $fileId) {
				$open = true;
			}

			if(! Str::startsWith($key, $fileId) && $open) {
				break;
			}

			if($open) {
				$section[$key] = $item;
			}
		}

		return $section;
	}

	/**
	 * Get index.
	 *
	 * @param  string $documentId
	 * @return integer|null
	 */
	public function getItem($documentId)
	{
		if(isset($this->contents[$documentId])) {
			return $this->contents[$documentId];
		}

		return null;
	}

	/**
	 * Get index.
	 *
	 * @param  string $documentId
	 * @return integer|null
	 */
	public function getItemIndex($documentId)
	{
		if(isset($this->contents[$documentId])) {
			return array_flip(array_keys($this->contents))[$documentId];
		}

		return null;
	}

}
