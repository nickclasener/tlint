<?php

namespace Tighten\Linters;

use PhpParser\Parser;
use Tighten\BaseLinter;
use Tighten\CustomNode;

class NewLineAtEndOfFile extends BaseLinter
{
    protected $description = 'File should end with a new line';

    public function lint(Parser $parser)
    {
        $codeLines = $this->getCodeLines();

        if (last($codeLines) !== '') {
            return [new CustomNode(['startLine' => count($codeLines)])];
        }

        return [];
    }
}
