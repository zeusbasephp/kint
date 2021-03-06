<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 Jonathan Vollebregt (jnvsor@gmail.com), Rokas Šleinius (raveren@gmail.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Kint\Renderer\Rich;

use Kint\Object\Representation\Representation;
use Kint\Object\Representation\SourceRepresentation;

class SourcePlugin extends Plugin implements TabPluginInterface
{
    public function renderTab(Representation $r)
    {
        if (!($r instanceof SourceRepresentation) || empty($r->source)) {
            return false;
        }

        $source = $r->source;

        // Trim empty lines from the start and end of the source
        foreach ($source as $linenum => $line) {
            if (\trim($line) || $linenum === $r->line) {
                break;
            }

            unset($source[$linenum]);
        }

        foreach (\array_reverse($source, true) as $linenum => $line) {
            if (\trim($line) || $linenum === $r->line) {
                break;
            }

            unset($source[$linenum]);
        }

        $start = '';
        $highlight = '';
        $end = '';

        foreach ($source as $linenum => $line) {
            if ($linenum < $r->line) {
                $start .= $line."\n";
            } elseif ($linenum === $r->line) {
                $highlight = '<div class="kint-highlight">'.$this->renderer->escape($line).'</div>';
            } else {
                $end .= $line."\n";
            }
        }

        $output = $this->renderer->escape($start).$highlight.$this->renderer->escape(\substr($end, 0, -1));

        if ($output) {
            \reset($source);

            $marker = '@@ '.((int) \key($source)).','.\count($source).' @@';

            if ($r->showfilename) {
                $marker = $this->renderer->escape($r->filename).'&#13;&#10;'.$marker;
            }

            return '<pre class="kint-source" data-kint-sourcerange="'.$marker.'">'.$output.'</pre>';
        }
    }
}
