<?php
namespace Scss\Formatter;

use Scss\Formatter;

class NestedSilex extends Nested
{
    public function __construct()
    {
        parent::__construct();
        $this->indentChar = '   ';
        $this->close = '}';
    }

    protected function block($block)
    {
        if ($block->type == 'root') {
            $this->adjustAllChildren($block);
        }

        $inner = $pre = $this->indentStr($block->depth - 1);
        if (!empty($block->selectors)) {
            echo $pre .
                implode($this->tagSeparator, $block->selectors) .
                $this->open . $this->break;
            $this->indentLevel++;
            $inner = $this->indentStr($block->depth - 1);
        }

        if (!empty($block->lines)) {
            $this->blockLines($inner, $block);
        }

        foreach ($block->children as $i => $child) {
            // echo "*** block: ".$block->depth." child: ".$child->depth."\n";
            $this->block($child);
            if ($i < count($block->children) - 1) {
                echo $this->break;

                if (isset($block->children[$i + 1])) {
                    $next = $block->children[$i + 1];
                    if ($next->depth == max($block->depth, 1) && $child->depth >= $next->depth) {
                        echo $this->break;
                    }
                }
            }
        }

        if (!empty($block->selectors)) {
            $this->indentLevel--;
            echo $this->break;
            for ($i = 0; $i < $block->depth - 1; $i++) {
                echo $this->indentChar;
            }
            echo $this->close;
        }

        if ($block->type == 'root') {
            echo $this->break;
        }
    }
}
