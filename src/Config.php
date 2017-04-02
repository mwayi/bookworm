<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Arr;

class Config
{
	/**
	 * @var string $src
	 */
	protected $src;

	/**
	 * @var string $src
	 */
	protected $configPath;

	/**
	 * @var array $configs
	 */
	protected $configs = [];

	/**
	 * @var Illuminate\Filesystem\Filesystem $file 
	 */
	protected $file;

	/**
	 * @var array $defaults
	 */
	protected $defaults = [
		'templates' => 'templates', 
		'src' => 'src',
		'published' => 'published',
		'baseurl' => null,
		'front-page' => 'README.md'
	];

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct($src)
	{
		$this->file = new File;	
		$this->src = $src;
		$this->checkProjectExists();
		$this->checkConfigExists();
		$this->initialiseConfigs();
		$this->checkDirectoriesExists();

	}

	/**
	 * Initialise configs
	 *
	 * @return void
	 */
	protected function initialiseConfigs()
	{
		if(($content = file_get_contents($this->configPath))) {
			$this->config = array_merge($this->defaults, (array)json_decode($content));
		}

		return $this;
	}

	/**
	 * Check project exists.
	 *
	 * @return void
	 *
	 * @throws RuntimeException
	 */
	protected function checkProjectExists()
	{
		if(! is_dir($this->src)) {
			throw new RuntimeException("Project '{$this->src}' does not exist.");
		}

		$this->src = realpath($this->src);
	}

	/**
	 * Check config exists.
	 *
	 * @return void
	 *
	 * @throws RuntimeException
	 */
	protected function checkConfigExists()
	{
		if(! file_exists($this->configPath = $this->src . '/bookworm.json')) {
			throw new RuntimeException("A bookworm config must exist.");
		}
	}

	/**
	 * Check directories exist.
	 *
	 * @return void
	 *
	 * @throws RuntimeException if the source directory does not exist.
	 */
	protected function checkDirectoriesExists()
	{
		$directories = [
			$this->getConfig('src'),
			$this->getConfig('templates')
		];

		foreach($directories as $directory) {

			if(! is_dir($this->rootPath($directory))) {
				throw new RuntimeException("The $directory does not exist");
			}
		}
	}


	/**
	 * Root Path
	 *
	 * @param  string $extra
	 * @return string
	 */
	public function rootPath($extra = null)
	{
		return $this->src . '/' . ($extra? ltrim($extra, '/'): null);
	}

	/**
	 * Get config
	 *
	 * @param  string $config
	 * @param  mixed  $default
	 * @return void
	 */
	public function getConfig($config, $default = null)
	{
		return Arr::get($this->config, $config, $default);
	}

	/**
	 * Get source.
	 *
	 * @return string
	 */
	public function getSourceDirectory()
	{
		return $this->rootPath($this->getConfig('src'));
	}

	/**
	 * Get template directory.
	 *
	 * @return string
	 */
	public function getTemplateDirectory()
	{
		return $this->rootPath($this->getConfig('templates'));
	}

	/**
	 * Get published directory.
	 *
	 * @return string
	 */
	public function getPublishedDirectory()
	{
		return $this->rootPath($this->getConfig('published'));
	}
}
