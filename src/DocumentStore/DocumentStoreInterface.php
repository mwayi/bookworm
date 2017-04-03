<?php

namespace Smrtr\Bookworm\DocumentStore;

interface DocumentStoreInterface
{
    /**
     * @return Document[]
     */
    public function getDocuments();

    /**
     * @param null|string $index
     *
     * @return Document[]
     */
    public function getDocumentsUnder($index = null);
}