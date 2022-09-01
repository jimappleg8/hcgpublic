<?php
class SyntaxHighlighter_none
{
    //highlight for viewing
    function highlight($text)
    {
        return "<code>".nl2br(htmlentities($text))."</code>";
    }

    //preprocess input before db storage
    function preprocess($text)
    {
        return $text;
    }
}

//php syntax highlighter
class SyntaxHighlighter_php extends SyntaxHighlighter_none
{

    function highlight($php)
    {
        //get php to do the hard work
        ob_start();
        @highlight_string($php);
        $code = ob_get_contents();
        ob_end_clean();

        // Hyperlink keywords - we could have a table or array or
        // interesting keywords, but that would be a bit laborious.
        // Instead, we just for things that look like function calls...
        // this has the downside that it links
        // user defined functions too, but what the hell. It's only
        // a few lines of code....

        $keycol = ini_get("highlight.keyword");
        $manual = "http://www.php.net/manual-lookup.php?lang=en&amp;pattern=";

        $code = preg_replace(
            //match a highlighted keyword
            '{([\w_]+)(\s*</font>)'.
            //followed by a bracket
            '(\s*<font\s+color="'.$keycol.'">\s*\()}m',
            //and replace with manual hyperlink
            '<a class="code" title="View manual page for $1" href="'.$manual.'$1">$1</a>$2$3', $code);

        return $code;
    }

    function preprocess($code)
    {
        //ensure code has begin and end tags somewhere
        $code = trim($code);
        if (strpos($code, '<?') === false)
            $code = "<?php\n".$code;
        if (strpos($code, '?>') === false)
        $code .= "\n?>";

        return $code;
    }
}
?>
