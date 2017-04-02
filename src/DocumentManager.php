<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;

class DocumentManager
{
	/**
	 * @var string $dest
	 */
	protected $dest;

	/**
	 * @var string $src
	 */
	protected $src;

	/**
	 * @var array $contents
	 */
	protected $documents = [];

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(Config $config)
	{	
		$this->config = $config;
		$this->compile();
	}


	/**
	 * Compile files
	 *
	 * @return void
	 */
	protected function compile()
	{
		$src = $this->config->getSourceDirectory();
	
		$this->traverse($this->getFiles($src), $src);

		new Publish($this);
	}

	/**
	 * Recurse
	 *
	 * @param  array  $files
	 * @return void
	 */
	protected function traverse(array $files, $basePath, $folder = './', $j = null)
	{
		$i = 1;

		foreach ($files as $file) {


			$fileName = strtolower((new File)->name($file));

			if($fileName === 'overview') {
				continue;
			}

			$fileId   = is_null($j)? $i: "$j.$i"; 
			$filePath = "$basePath/$file";
			$document = new Document($fileId, $filePath, $this->config);
			$this->documents[$fileId] = $document;

			// Recurse through the sub folders
			if(is_dir($filePath)) {
				$this->traverse(
					$this->getFiles($filePath), 
					$filePath, 
					$folder . '/' . dirname($filePath), 
					$fileId
				);
			}

			$i++;
		}
	}

	/**
	 * Get non hidden files.
	 *
	 * @param  string $directory
	 * @return array
	 */
	protected function getFiles($directory)
	{
		return array_filter(scandir($directory), function ($file) {
		    return strpos($file, '.') !== 0;
		});
	}

	/**
	 * Get contents.
	 *
	 * @return array
	 */
	public function getDocuments()
	{
		return $this->documents;
	}
}
