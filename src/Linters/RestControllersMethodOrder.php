<?php

namespace Tighten\Linters;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\Parser;
use Tighten\BaseLinter;

class RestControllersMethodOrder extends BaseLinter
{
    private const RESTFUL_METHOD_NAMES = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ];

    protected $description = 'REST methods in controllers should match the ordering here:'
        . ' https://laravel.com/docs/5.4/controllers#restful-partial-resource-routes';

    public function lint(Parser $parser)
    {
        $traverser = new NodeTraverser;

        $visitor = new FindingVisitor(function (Node $node) {
            if ($node instanceof Node\Stmt\Class_) {
                $methodNames = array_map(function ($stmt) {
                    return $stmt->name->name;
                }, array_filter($node->stmts, function ($stmt) {
                    return $stmt instanceof Node\Stmt\ClassMethod;
                }));

                $restfulMethods = array_intersect(self::RESTFUL_METHOD_NAMES, $methodNames);
                $nonRestfulMethods = array_diff($methodNames, self::RESTFUL_METHOD_NAMES);

                if (count($restfulMethods) > 0 && count($nonRestfulMethods) === 0) {
                    $correctlyOrderedPresentMethods = array_filter(
                        self::RESTFUL_METHOD_NAMES,
                        function ($method) use ($restfulMethods) {
                            return in_array($method, $restfulMethods);
                        }
                    );

                    return array_values($correctlyOrderedPresentMethods) !== array_values($methodNames);
                }
            }

            return false;
        });

        $traverser->addVisitor($visitor);

        $traverser->traverse($parser->parse($this->code));

        return $visitor->getFoundNodes();
    }
}
