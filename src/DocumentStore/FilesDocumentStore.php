<?php

namespace Smrtr\Bookworm\DocumentStore;

use Illuminate\Support\Str;

class FilesDocumentStore extends AbstractDocumentStore implements DocumentStoreInterface
{
    const CONFIG_SOURCE_DIRECTORY = 'src';

    /**
     * @var Document[]
     */
    protected $documents;

    /**
     * Get array of documents.
     *
     * @return array
     */
    public function getDocuments()
    {
        if (null === $this->documents) {
            $this->findDocuments(
                $this->getFiles($this->getSourceDirectory()),
                $this->getSourceDirectory()
            );
            ksort($this->documents);
        }

        return $this->documents;
    }

    /**
     * Get array of documents under a particular index.
     *
     * @param null|string $index
     *
     * @return Document[]
     */
    public function getDocumentsUnder($index = null)
    {
        if(is_null($index)) {
            return $this->getDocuments();
        }

        $section = [];
        $open = false;
        foreach ($this->getDocuments() as $key => $item) {
            if ($key == $index) {
                $open = true;
            }
            if ($open && !Str::startsWith($key, $index)) {
                break;
            }
            if ($open) {
                $section[$key] = $item;
            }
        }

        return $section;
    }

    /**
     * Traverse the source directory and find documents.
     *
     * Populates $this->documents.
     *
     * @param array     $files
     * @param string    $basePath
     * @param null|int  $j
     *
     * @return void
     */
    protected function findDocuments(array $files, $basePath, $j = null)
    {
        $i = 1;
        foreach ($files as $file) {
            $index = is_null($j)? "$i": "$j.$i";
            $filePath = "$basePath/$file";
            $slug = Str::replaceFirst(
                $this->config->rootPath($this->getConfig(static::CONFIG_SOURCE_DIRECTORY)),
                '',
                $filePath
            );

            if (is_file($filePath) && '.md' === substr($file, -3)) {
                $hasDirectoryWithSameName = is_dir(substr($filePath, 0, -3));
                $this->documents["$index"] = new Document(
                    $index,
                    $slug,
                    $file,
                    file_exists($filePath) && is_file($filePath) ? file_get_contents($filePath) : null,
                    strtolower(trim($slug, '/ ')) === strtolower(trim($this->config->getConfig('front-page'), '/ ')),
                    $hasDirectoryWithSameName
                );
            }

            // Recurse through the sub folders
            if(is_dir($filePath)) {
                $hasMarkdownFileWithSameName = is_file($filePath.'.md');
                if (!$hasMarkdownFileWithSameName) {
                    $this->documents["$index"] = new Document(
                        $index,
                        $slug.'.md',
                        $file.'.md',
                        null,
                        false,
                        true
                    );
                }

                $this->findDocuments(
                    $this->getFiles($filePath),
                    $filePath,
                    $index
                );
            }

            $i++;
        }
    }

    /**
     * Get non hidden files.
     *
     * @param string $directory
     *
     * @return array
     */
    protected function getFiles($directory)
    {
        return array_filter(scandir($directory), function ($file) {
            return strpos($file, '.') !== 0;
        });
    }

    /**
     * @return string
     */
    protected function getSourceDirectory()
    {
        return $this->config->rootPath($this->getConfig(static::CONFIG_SOURCE_DIRECTORY));
    }
}