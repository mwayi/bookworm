<?php

namespace Smrtr\Bookworm\DocumentStore;

use Illuminate\Support\Arr;
use Smrtr\Bookworm\Config;

abstract class AbstractDocumentStore
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * AbstractDocumentStore constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get config
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return $this->config->getConfig(sprintf('%s.%s', Config::CONFIG_DOCUMENTS, $key), $default);
    }
}