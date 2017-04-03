<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;
use Smrtr\Bookworm\DocumentStore\DocumentStoreInterface;
use Smrtr\Bookworm\Renderer\ContentsPage;
use Smrtr\Bookworm\Renderer\MarkdownPage;

class Publish
{
	/**
	 * @var DocumentStoreInterface $documentStore
	 */
	protected $documentStore;

	/**
	 * @var array $file 
	 */
	protected $contents = [];

    /**
     * Publish constructor.
     *
     * @param Config $config
     */
	public function __construct(Config $config)
	{	
		$this->config = $config;

		$this->makeFiles();
	}

    /**
     * @return void
     */
	public function makeFiles()
	{
		foreach($this->config->getDocumentStore()->getDocuments() as $id => $document) {

			$destinationPath = $this->config->getPublishedDirectory().$document->getSlug();

			if (($destinationDirectory = dirname($destinationPath)) && !is_dir($destinationDirectory)) {
				(new File)->makeDirectory($destinationDirectory, 0755, true);
			}

			if ($document->isContentsPage()) {
			    $contentsPageRenderer = new ContentsPage();
                $contentsPageRenderer->render($this->config, $document, $destinationPath);
			} else {
			    $markdownPageRenderer = new MarkdownPage();
                $markdownPageRenderer->render($document, $destinationPath);
//                (new File)->put($destinationPath, $document->getContent());
            }
		}
	}
}
