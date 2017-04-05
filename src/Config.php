<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Support\Arr;
use Smrtr\Bookworm\DocumentStore\DocumentStoreInterface;

class Config
{
    const CONFIG_TEMPLATES = 'templates';
    const CONFIG_PUBLISHED = 'published';
    const CONFIG_BASE_URL = 'base-url';
    const CONFIG_FRONT_PAGE = 'front-page';
    const CONFIG_DOCUMENTS = 'documents';
    const CONFIG_DOCUMENTS_STORE = 'store';

	/**
	 * @var string $root The root directory containing the bookworm.json
	 */
	protected $root;

    /**
     * @var string The file path of the bookworm.json
     */
    protected $configPath;

    /**
	 * @var array $configs
	 */
	protected $configs = [];

    /**
     * @var DocumentStoreInterface
     */
    protected $documentStore;

    /**
     * @return array
     */
    public static function getDefaultConfigs()
    {
        return [
            static::CONFIG_TEMPLATES => 'templates',
            static::CONFIG_PUBLISHED => 'published',
            static::CONFIG_BASE_URL => null,
            static::CONFIG_FRONT_PAGE => 'README.md',
            static::CONFIG_DOCUMENTS => [
                static::CONFIG_DOCUMENTS_STORE => 'files'
            ],
        ];
    }

	public function __construct($root)
	{
		$this->root = $root;
		$this->checkProjectExists();
		$this->checkConfigExists();
		$this->initialiseConfigs();
        $this->loadDocumentStore();
		$this->checkDirectoriesExists();

	}

	protected function loadDocumentStore()
    {
        $documentStoreClass = sprintf(
            'Smrtr\\Bookworm\\DocumentStore\\%sDocumentStore',
            ucfirst(strtolower($documentStoreName = $this->getConfig('documents.store')))
        );

        if (!is_subclass_of($documentStoreClass, DocumentStoreInterface::class)) {
            throw new RuntimeException(sprintf('Document store \'%s\' is not implemented', $documentStoreName));
        }

        $this->documentStore = new $documentStoreClass($this);
    }

	protected function checkProjectExists()
	{
		if (!is_dir($this->root)) {
			throw new RuntimeException("Project '{$this->root}' does not exist.");
		}

		$this->root = realpath($this->root);
	}

	protected function checkConfigExists()
	{
		if(! file_exists($this->configPath = $this->root . '/bookworm.json')) {
			throw new RuntimeException("A bookworm config must exist.");
		}
	}

    protected function initialiseConfigs()
    {
        if(($content = file_get_contents($this->configPath))) {
            $this->configs = array_merge(static::getDefaultConfigs(), json_decode($content, true));
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
		return $this->root . '/' . ($extra ? ltrim($extra, '/') : null);
	}

	/**
	 * Get config
	 *
	 * @param  string $config
	 * @param  mixed  $default
	 *
     * @return mixed
	 */
	public function getConfig($config, $default = null)
	{
		return Arr::get($this->configs, $config, $default);
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

    /**
     * @return DocumentStoreInterface
     */
    public function getDocumentStore()
    {
        return $this->documentStore;
    }
}
