<?php
namespace tests\output\filter;

use phphound\output\filter\DiffOutputFilter;
use SebastianBergmann\Diff\Parser;

class DiffOutputFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    function it_filter_array_of_issues()
    {
        $data = [
            '/path/file_a.php' => [
                1 => 'issues for a.1',
                2 => 'issues for a.2',
                3 => 'issues for a.2',
            ],
            '/path/file_b.php' => [
                3 => 'issues for b.3',
                4 => 'issues for b.4',
                10 => 'issues for b.10',
            ],
        ];

        $diff = <<<EOT
diff --git a/file_a.php b/file_a.php
index 00935f1..e767aff 100644
--- a/file_a.php
+++ b/file_a.php
@@ -1,5 +1,5 @@
 Line 1
-Line 2
+Line two
 Line 3
 Line 4
 Line 5
diff --git a/file_b.php b/file_b.php
index 00935f1..61d520b 100644
--- a/file_b.php
+++ b/file_b.php
@@ -1,4 +1,4 @@
-Line 1
+Line one
 Line 2
 Line 3
 Line 4
@@ -7,4 +7,4 @@ Line 6
 Line 7
 Line 8
 Line 9
-Line 10
+Line ten
diff --git a/file_c.php b/file_c.php
new file mode 100644
index 0000000..c82de6a
--- /dev/null
+++ b/file_c.php
@@ -0,0 +1,2 @@
+Line 1
+Line 2
EOT;

        $parser = new Parser;
        $filter = new DiffOutputFilter('/path', $parser->parse($diff));

        $this->assertEquals(
            [
                '/path/file_a.php' => [
                    2 => 'issues for a.2',
                ],
                '/path/file_b.php' => [
                    10 => 'issues for b.10',
                ],
            ],
            $filter->filter($data)
        );
    }
}
