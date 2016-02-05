<?php

namespace NHL\Parsers;

use DOMDocument;
use DOMElement;
use DOMNode;
use NHL\Contracts\AbstractParser;

/**
 * Class RosterParser
 *
 * Parser for roster (RO) files
 *
 * @package NHL\Parsers
 */
class RosterParser extends AbstractParser
{

    /**
     * @return bool
     * @throws \NHL\Exceptions\ParserException
     */
    public function parse()
    {
        ini_set('memory_limit', -1);

        $this->prepareFiles();
        $files = $this->getAllFileNames();
        foreach ($files as $filename) {
            $this->processFile($filename);
        }

        return true;
    }

    /**
     * Parses the given file and returns a Game object
     *
     * @param string $filename
     */
    protected function processFile($filename)
    {
        $dom = new DOMDocument();
        $dom->loadHTMLFile(__DIR__.'/file.HTM');

        $rows = $dom->getElementsByTagName('tr');

        $lines = [];
        /** @var DOMElement $row */
        foreach($rows as $row) {
            if ($row->childNodes->length == 6) {
                $line = "";
                /** @var DOMNode $node */
                foreach($row->childNodes as $node) {
                    $line .= preg_replace('/[^A-Z\\d\\h\\-\\.]/', '@', $node->textContent);
                }
                if (preg_match("/(\\d+)@@(G|R|D|C|L)@@([A-Z\\h\\.\\-\\']+)/", $line, $matches)) {
                    $lines[] = ['num' => $matches[1], 'pos' => $matches[2], 'name' => $matches[3]];
                }
            }
        }
    }
}