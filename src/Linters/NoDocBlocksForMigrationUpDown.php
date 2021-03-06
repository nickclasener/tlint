<?php

namespace Tighten\Linters;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\Parser;
use Tighten\BaseLinter;

class NoDocBlocksForMigrationUpDown extends BaseLinter
{
    protected $description = 'Remove doc blocks from the up and down method in migrations.';

    public function lint(Parser $parser)
    {
        $traverser = new NodeTraverser;

        $visitor = new FindingVisitor(function (Node $node) {
            return $node instanceof Node\Stmt\ClassMethod
                && in_array($node->name, ['up', 'down'])
                && (bool) $node->getDocComment();
        });

        $traverser->addVisitor($visitor);

        $traverser->traverse($parser->parse($this->code));

        return $visitor->getFoundNodes();
    }
}
