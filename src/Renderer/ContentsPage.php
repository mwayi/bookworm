<?php

namespace Smrtr\Bookworm\Renderer;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Smrtr\Bookworm\Config;
use Smrtr\Bookworm\DocumentStore\Document;

class ContentsPage
{
    /**
     * @param Config    $config
     * @param Document  $document
     * @param string    $destinationPath
     *
     * @return string
     */
    public function render(Config $config, Document $document, $destinationPath)
    {
        $content = $document->getContent();
        $title = $document->getName();

        $sectionDocuments = $config->getDocumentStore()->getDocumentsUnder(
            $document->isFrontPage() ? null : $document->getIndex()
        );

        $sectionContents = $this->getMarkdown($sectionDocuments, $config, $destinationPath);

        $content = "# $title\n" . $content . "\n\n--------------\n" . $sectionContents;

        (new Filesystem)->put($destinationPath, $content);
    }

    /**
     * @param array $sectionDocuments
     * @param Config $config
     * @param string $destinationPath
     *
     * @return string
     */
    protected function getMarkdown(array $sectionDocuments, Config $config, $destinationPath)
    {
        $baseTab = $markdown = null;

        foreach($sectionDocuments as $document) {
            $levels = explode('.', $document->getIndex());

            if(is_null($baseTab)) {
                $baseTab = count($levels);
            }

            $markdown .= str_pad('', 4 * (count($levels) - $baseTab), ' ') .
                sprintf(
                    "- %s [%s](%s) \n",
                    $document->getIndex(),
                    $document->getName(),
                    $this->getLink($config, $document)
                );
        }

        return $markdown;
    }

    protected function getLink(Config $config, Document $document)
    {
        return sprintf(
            '%s/%s',
            $config->getConfig(Config::CONFIG_BASE_URL),
            ltrim($document->getSlug(), '/')
        );
    }
}