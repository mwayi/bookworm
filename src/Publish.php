<?php

namespace Smrtr\Bookworm;

use RuntimeException;
use Illuminate\Filesystem\Filesystem as File;

class Publish
{
	/**
	 * @var array $file 
	 */
	protected $files = [];

	/**
	 * @var array $file 
	 */
	protected $contents = [];

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(DocumentManager $documentManager)
	{	
		$this->documents = $documentManager->getDocuments();

		$this->contents = new Contents($this->documents);

		$this->makeFiles();
	}


	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function makeFiles()
	{	
		foreach($this->documents as $id => $document) {

			$dst = $document->getDestinationPath();
			if(($dir = dirname($dst)) && !is_dir($dir)) {
				(new File)->makeDirectory($dir, 0755, true);
			}

			if($document->isContentsPage()) {
				$contents = $document->contents(); 
				$title = $document->name(); 

				$section = null;
				if($document->isFrontPage()) {
					$section = $this->contents->getSection();
				}else {
					$section = $this->contents->getSection($document->id());
				}

				$index = $this->contents->getMarkdown($section);

				$contents = "# $title\n" . $contents . "\n--------------\n" . $index;

				$document->setContents($contents);
			}

			(new File)->put($dst, $document->contents());
		}
	}
}
