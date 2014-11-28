<?php

namespace phpDocumentor\Parser\Backend;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Parser\Backend;

final class Document implements Handler
{
    /** @var string[] */
    private $extensions = array('md', 'rst');

    /** @var Analyzer */
    private $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function boot(\phpDocumentor\Configuration $configuration)
    {
    }

    public function matches(\SplFileInfo $file)
    {
        return in_array($file->getExtension(), $this->extensions);
    }

    public function parse(\SplFileObject $file)
    {
        $document = $this->createDocumentForFile($file);
        $document->loadFile($file->getPath());

        try {
            $docBook = $document->getAsDocbook();
            var_dump($docBook->__toString());
        } catch (\Exception $e) {
            var_dump('fail: ' . $e->getMessage());
        }

        $fileDescriptor = $this->analyzer->analyze($docBook);
        // TODO: RegisterDocbook
    }

    /**
     * @param \SplFileObject $file
     * @return \ezcDocumentRst
     */
    private function createDocumentForFile(\SplFileObject $file)
    {
        switch (strtolower($file->getExtension())) {
            case 'rst':
                $document = new \ezcDocumentRst();
                $document->options->xhtmlVisitor
                    = 'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors\Discover';
                break;
            default:
                throw new \RuntimeException(
                    'The file extension ' . $file->getExtension() . ' is currently not supported by the Document Parser'
                    . ' Handler'
                );
        }
        return $document;
    }
}
