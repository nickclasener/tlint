<?php

namespace Tighten\Linters;

use PhpParser\Parser;
use Tighten\BaseLinter;
use Tighten\CustomNode;

class NoSpaceAfterBladeDirectives extends BaseLinter
{
    private const NO_SPACE_AFTER = [
        'endif',
        'else',
        'section',
        'show',
        'yield',
        'extends',
        'parent',
        'verbatim',
        'empty',
        'continue',
        'break',
        'php',
        'include',
        'includeIf',
        'each',
        'push',
        'stack',
    ];

    protected $description = 'No space between blade template directive names and the opening paren:'
        . '`@section (` -> `@section(`';

    public function lint(Parser $parser)
    {
        $foundNodes = [];

        foreach ($this->getCodeLines() as $line => $codeLine) {
            $matches = [];

            // https://github.com/illuminate/view/blob/master/Compilers/BladeCompiler.php#L271
            preg_match(
                '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
                $codeLine,
                $matches
            );

            if (in_array($matches[1] ?? null, self::NO_SPACE_AFTER) && ($matches[2] ?? null) !== '') {
                $foundNodes[] = new CustomNode(['startLine' => $line + 1]);
            }
        }

        return $foundNodes;
    }
}
