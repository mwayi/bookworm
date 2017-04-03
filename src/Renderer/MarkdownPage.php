<?php

namespace Smrtr\Bookworm\Renderer;

use Illuminate\Filesystem\Filesystem;
use Smrtr\Bookworm\DocumentStore\Document;
use Smrtr\Bookworm\TableOfContents;

class MarkdownPage
{
    public function render(Document $document, $destinationPath)
    {
        (new Filesystem)->put($destinationPath, $this->applyTableOfContents($document));
    }

    protected function applyTableOfContents(Document $document)
    {
        $toc = (new TableOfContents($document->getContent()))->compile();
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
        return str_replace("[TOC]", "{$break}$tableOfContents{$break}", $document->getContent());
    }
}