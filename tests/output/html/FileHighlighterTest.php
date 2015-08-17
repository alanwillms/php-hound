<?php
namespace tests\output;

use phphound\output\html\FileHighlighter;

class FileHighlighterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_generates_html_without_issues()
    {
        $filePath = realpath(__DIR__ . '/../../data/File.php');
        $issues = [];
        $highlighter = new FileHighlighter($filePath, $issues);
        $expectedHtml = <<<'HTML'
<code><span style="color: #000000"><div class="no-issues" id="line1"><span class="line-number">01</span><span style="color: #0000BB">&lt;?php</div><div class="no-issues" id="line2"><span class="line-number">02</span>&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</div><div class="no-issues" id="line3"><span class="line-number">03</span>&nbsp;&nbsp;&nbsp;*&nbsp;</div><div class="no-issues" id="line4"><span class="line-number">04</span>&nbsp;&nbsp;&nbsp;*/</div><div class="no-issues" id="line5"><span class="line-number">05</span>&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">class&nbsp;</span><span style="color: #0000BB">SomeFakeUglyClass&nbsp;</span><span style="color: #007700">{</div><div class="no-issues" id="line6"><span class="line-number">06</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class="no-issues" id="line7"><span class="line-number">07</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;function&nbsp;</span><span style="color: #0000BB">__construct</span><span style="color: #007700">(</span><span style="color: #0000BB">$argument</span><span style="color: #007700">)&nbsp;{</div><div class="no-issues" id="line8"><span class="line-number">08</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$argument&nbsp;</span><span style="color: #007700">+&nbsp;</span><span style="color: #0000BB">1234</span><span style="color: #007700">;</div><div class="no-issues" id="line9"><span class="line-number">09</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}</div><div class="no-issues" id="line10"><span class="line-number">10</span>&nbsp;&nbsp;&nbsp;}</span>
</div></span></code>
HTML;
        $this->assertEquals($expectedHtml, $highlighter->getHtml());
    }

    /** @test */
    public function it_generates_html_with_issues()
    {
        $filePath = realpath(__DIR__ . '/../../data/File.php');
        $issues = [
            2 => [
                ['message' => 'Some error on this line!']
            ]
        ];
        $highlighter = new FileHighlighter($filePath, $issues);

        $expectedHtml = <<<'HTML'
<code><span style="color: #000000"><div class="no-issues" id="line1"><span class="line-number">01</span><span style="color: #0000BB">&lt;?php</div><div class="has-issues" id="line2"><span class="line-number">02</span><div class="mdl-tooltip mdl-tooltip--large" for="line2"><ul><li>Some error on this line!</ul></ul></div>&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</div><div class="no-issues" id="line3"><span class="line-number">03</span>&nbsp;&nbsp;&nbsp;*&nbsp;</div><div class="no-issues" id="line4"><span class="line-number">04</span>&nbsp;&nbsp;&nbsp;*/</div><div class="no-issues" id="line5"><span class="line-number">05</span>&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">class&nbsp;</span><span style="color: #0000BB">SomeFakeUglyClass&nbsp;</span><span style="color: #007700">{</div><div class="no-issues" id="line6"><span class="line-number">06</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class="no-issues" id="line7"><span class="line-number">07</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;function&nbsp;</span><span style="color: #0000BB">__construct</span><span style="color: #007700">(</span><span style="color: #0000BB">$argument</span><span style="color: #007700">)&nbsp;{</div><div class="no-issues" id="line8"><span class="line-number">08</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$argument&nbsp;</span><span style="color: #007700">+&nbsp;</span><span style="color: #0000BB">1234</span><span style="color: #007700">;</div><div class="no-issues" id="line9"><span class="line-number">09</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}</div><div class="no-issues" id="line10"><span class="line-number">10</span>&nbsp;&nbsp;&nbsp;}</span>
</div></span></code>
HTML;
        $this->assertEquals($expectedHtml, $highlighter->getHtml());
    }
}