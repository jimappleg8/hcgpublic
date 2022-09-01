<?php

/*
   This plugin provides for CSS support in WikiPages. To add a style
   (color, background, font, etc.) simply use the "@@" to initiate
   a CSS definition:

        @@cssparagraph  ... here comes the text
        that is formatted according to the style
        class ".cssparagraph" of out stylesheet

   In the above example the style is applied to the whole paragraph (every
   piece of text, that follows the @@). But you can also assign styles to
   just some parts of the text or even intermix and overlap multiple style
   definitions. To do so, you must however close a begun style allocation:

        @@parastyle  ... some text following
        ... but @@color:red; this part@@ is
        coloured!
        And @@subdef1 ...here... @@more3 ... @@
        a piece@@ of nested CSS-stuff.

   In this example (looks a bit weird) the last two definitions are nested!
   Note also, that you cannot only assign CSS class names to a paragraph or
   piece of text, but also direct format it using all possible CSS
   definitons - but beware that there cannot be any whitespace in the CSS
   instruction that you apply using this syntax.

   This plugin uses regular expressions, but does not slow down the
   rendering process much more than any other plugin!

   See also the 'markup_css_singleat' plugin, which allows to use just
   a single @ instead of two, like with javadoc. Both can be used
   alternative or in conjunction.
*/


define("EWIKI_CSS_BLOCK", "div");
define("EWIKI_CSS_INLINE", "span");

$ewiki_plugins["format_source"][] = "ewiki_format_css";



function ewiki_format_css(&$src) {

   #-- this regex just selects paragraphs with a "@@" inside
   $src = preg_replace(
      '/(\n\s*\n.*?@@.*?)(\n\s*\n)/se',
      'ewiki_format_css_apply("$1") . "$2"',
      $src
   );

}



function ewiki_format_css_apply($para) {

   $stack = array();

   while (preg_match('/^(.*?)@@([^\s]*)(.*)$/s', $para, $uu)) {

      if (!strlen($uu[2])) {
         if ($stack) {
            $repl = "</" . array_pop($stack) . ">";
         }
         else {
            $repl = "@&#x40;";
         }
         $para = $uu[1] . $repl . $uu[3];
      }
      else {
         $span = (trim( $uu[1]) ? EWIKI_CSS_INLINE : EWIKI_CSS_BLOCK);
         $stack[] = $span;

         $para = $uu[1] . "<$span " . (strpos($uu[2], ":") ?"style":"class")
               . '="' . $uu[2] . '">' . $uu[3];
      }

   }

   while ($span = array_pop($stack)) {
      $para .= "</$span>";
   }

   return($para);
}


?>