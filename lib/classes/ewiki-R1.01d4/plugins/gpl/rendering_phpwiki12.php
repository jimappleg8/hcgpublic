<?php ############################ <license>GPL</license> #####################
// rcs_id('$Id: rendering_phpwiki12.php,v 1.1 2004/03/18 13:33:38 milky Exp $');
// expects $pagehash and $html to be set

/*

This plugin was taken from PhpWiki [http://freshmeat.net/projects/phpwiki],
and is covered by the GNU GPL [http://www.gnu.org/gpl]:

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

-

This file is an (heavily modified/crippled) excerpt from the older
PhpWiki 1.2 releases. Recent versions of PhpWiki use a more advanced
rendering kernel.
This plugin was added just for fun (the ewiki kernel provides almost
equal functionality).

*/



#-- register
$ewiki_plugins["render"][0] = "phpwiki12_transform";



#-- more faking
define("NUM_LINKS", 20);    #-- page appended links [2]
define("ZERO_LEVEL", 0);    #-- ?????????????
define("NESTED_LEVEL", 1);    #-- ?????????????
$FieldSeparator = "\263";   #-- ?????????????
$AllowedProtocols = "http|https|mailto|ftp|news|gopher";
$WikiNameRegexp = "(?<![A-Za-z0-9])([A-Z][a-z]+){2,}(?![A-Za-z0-9])";



#-- main
function phpwiki12_transform($wiki_source, $scan_links=1) {

   global $stack, $AllowedProtocols, $WikiNameRegexp, $FieldSeparator, $ewiki_links;

   $dbi = 0;
   $stack = new Stack;

   #-- fake phpwiki env
   $pagehash = array(
      "content" => explode("\n", $wiki_source),
      "refs" => array(),
   );
   $html = "";

   if ($scan_links) {
      ewiki_scan_wikiwords($wiki_source, $ewiki_links);
   }

/**********
   // Prepare replacements for references [\d+]
   for ($i = 1; $i < (NUM_LINKS + 1); $i++) {
      if (! empty($pagehash['refs'][$i])) {
         if (preg_match("/($InlineImages)$/i", $pagehash['refs'][$i])) {
            // embed images
            $embedded[$i] = LinkImage($pagehash['refs'][$i]);
         } else {
            // ordinary link
            $embedded[$i] = LinkURL($pagehash['refs'][$i], "[$i]");
         }
      }
   }
************/


   // Loop over all lines of the page and apply transformation rules
   $numlines = count($pagehash["content"]);

   for ($index = 0; $index < $numlines; $index++) {
      unset($tokens);
      unset($replacements);
      $ntokens = 0;
      $replacements = array();
      
      $tmpline = $pagehash['content'][$index];

      if (!strlen($tmpline) || $tmpline == "\r") {
         // this is a blank line, send <p>
         $html .= SetHTMLOutputMode('', ZERO_LEVEL, 0);
         continue;
      }

/* If your web server is not accessble to the general public, you may
allow this code below, which allows embedded HTML. If just anyone can reach
your web server it is highly advised that you do not allow this.

      elseif (preg_match("/(^\|)(.*)/", $tmpline, $matches)) {
         // HTML mode
         $html .= SetHTMLOutputMode("", ZERO_LEVEL, 0);
         $html .= $matches[2];
         continue;
      }
*/


      //////////////////////////////////////////////////////////
      // New linking scheme: links are in brackets. This will
      // emulate typical HTML linking as well as Wiki linking.
	
      // First need to protect [[. 
      $oldn = $ntokens;
      $tmpline = tokenize($tmpline, '\[\[', $replacements, $ntokens);
      while ($oldn < $ntokens)
         $replacements[$oldn++] = '[';

      // Now process the [\d+] links which are numeric references	
      $oldn = $ntokens;
      $tmpline = tokenize($tmpline, '\[\s*\d+\s*\]', $replacements, $ntokens);
      while ($oldn < $ntokens) {
	 $num = (int) substr($replacements[$oldn], 1);
         if (! empty($embedded[$num]))
            $replacements[$oldn] = $embedded[$num];
	 $oldn++;
      }

      // match anything else between brackets 
      $oldn = $ntokens;
      $tmpline = tokenize($tmpline, '\[.+?\]', $replacements, $ntokens);
      while ($oldn < $ntokens) {
	$link = ParseAndLink($replacements[$oldn]);	
	$replacements[$oldn] = $link['link'];
	$oldn++;
      }

      //////////////////////////////////////////////////////////
      // replace all URL's with tokens, so we don't confuse them
      // with Wiki words later. Wiki words in URL's break things.
      // URLs preceeded by a '!' are not linked

      $tmpline = tokenize($tmpline, "!?\b($AllowedProtocols):[^\s<>\[\]\"'()]*[^\s<>\[\]\"'(),.?]", $replacements, $ntokens);
      while ($oldn < $ntokens) {
        if($replacements[$oldn][0] == '!')
	   $replacements[$oldn] = substr($replacements[$oldn], 1);
	else
	   $replacements[$oldn] = LinkURL($replacements[$oldn]);
        $oldn++;
      }

      //////////////////////////////////////////////////////////
      // Link Wiki words
      // Wikiwords preceeded by a '!' are not linked

      $oldn = $ntokens;
      $tmpline = tokenize($tmpline, "!?$WikiNameRegexp", $replacements, $ntokens);
      while ($oldn < $ntokens) {
        $old = $replacements[$oldn];
        if ($old[0] == '!') {
	  $replacements[$oldn] = substr($old,1);
	} elseif (IsWikiPage($dbi, $old)) {
	  $replacements[$oldn] = LinkExistingWikiWord($old);
	} else {
	  $replacements[$oldn] = LinkUnknownWikiWord($old);
	}
	$oldn++;
      }


      //////////////////////////////////////////////////////////
      // escape HTML metachars
      $tmpline = str_replace('&', '&amp;', $tmpline);
      $tmpline = str_replace('>', '&gt;', $tmpline);
      $tmpline = str_replace('<', '&lt;', $tmpline);


      // %%% are linebreaks
      $tmpline = str_replace('%%%', '<br>', $tmpline);

      // bold italics (old way)
      $tmpline = preg_replace("|(''''')(.*?)(''''')|",
                              "<strong><em>\\2</em></strong>", $tmpline);

      // bold (old way)
      $tmpline = preg_replace("|(''')(.*?)(''')|",
                              "<strong>\\2</strong>", $tmpline);

      // bold
      $tmpline = preg_replace("|(__)(.*?)(__)|",
                              "<strong>\\2</strong>", $tmpline);

      // italics
      $tmpline = preg_replace("|('')(.*?)('')|",
                              "<em>\\2</em>", $tmpline);


      //////////////////////////////////////////////////////////
      // unordered, ordered, and dictionary list  (using TAB)

      if (preg_match("/(^\t+)(.*?)(:\t)(.*$)/", $tmpline, $matches)) {
         // this is a dictionary list (<dl>) item
         $numtabs = strlen($matches[1]);
         $html .= SetHTMLOutputMode('dl', NESTED_LEVEL, $numtabs);
	 $tmpline = '';
	 if(trim($matches[2]))
            $tmpline = '<dt>' . $matches[2];
	 $tmpline .= '<dd>' . $matches[4];

      } elseif (preg_match("/(^\t+)(\*|\d+|#)/", $tmpline, $matches)) {
         // this is part of a list (<ul>, <ol>)
         $numtabs = strlen($matches[1]);
         if ($matches[2] == '*') {
            $listtag = 'ul';
         } else {
            $listtag = 'ol'; // a rather tacit assumption. oh well.
         }
         $tmpline = preg_replace("/^(\t+)(\*|\d+|#)/", "", $tmpline);
         $html .= SetHTMLOutputMode($listtag, NESTED_LEVEL, $numtabs);
         $html .= '<li>';


      //////////////////////////////////////////////////////////
      // tabless markup for unordered, ordered, and dictionary lists
      // ul/ol list types can be mixed, so we only look at the last
      // character. Changes e.g. from "**#*" to "###*" go unnoticed.
      // and wouldn't make a difference to the HTML layout anyway.

      // unordered lists <UL>: "*"
      } elseif (preg_match("/^([#*]*\*)[^#]/", $tmpline, $matches)) {
         // this is part of an unordered list
         $numtabs = strlen($matches[1]);
         $tmpline = preg_replace("/^([#*]*\*)/", '', $tmpline);
         $html .= SetHTMLOutputMode('ul', NESTED_LEVEL, $numtabs);
         $html .= '<li>';

      // ordered lists <OL>: "#"
      } elseif (preg_match("/^([#*]*\#)/", $tmpline, $matches)) {
         // this is part of an ordered list
         $numtabs = strlen($matches[1]);
         $tmpline = preg_replace("/^([#*]*\#)/", "", $tmpline);
         $html .= SetHTMLOutputMode('ol', NESTED_LEVEL, $numtabs);
         $html .= '<li>';

      // definition lists <DL>: ";text:text"
      } elseif (preg_match("/(^;+)(.*?):(.*$)/", $tmpline, $matches)) {
         // this is a dictionary list item
         $numtabs = strlen($matches[1]);
         $html .= SetHTMLOutputMode('dl', NESTED_LEVEL, $numtabs);
	 $tmpline = '';
	 if(trim($matches[2]))
            $tmpline = '<dt>' . $matches[2];
	 $tmpline .= '<dd>' . $matches[3];


      //////////////////////////////////////////////////////////
      // remaining modes: preformatted text, headings, normal text	

      } elseif (preg_match("/^\s+/", $tmpline)) {
         // this is preformatted text, i.e. <pre>
         $html .= SetHTMLOutputMode('pre', ZERO_LEVEL, 0);

      } elseif (preg_match("/^(!{1,3})[^!]/", $tmpline, $whichheading)) {
	 // lines starting with !,!!,!!! are headings
	 if($whichheading[1] == '!') $heading = 'h3';
	 elseif($whichheading[1] == '!!') $heading = 'h2';
	 elseif($whichheading[1] == '!!!') $heading = 'h1';
	 $tmpline = preg_replace("/^!+/", '', $tmpline);
	 $html .= SetHTMLOutputMode($heading, ZERO_LEVEL, 0);

      } elseif (preg_match('/^-{4,}\s*(.*?)\s*$/', $tmpline, $matches)) {
	 // four or more dashes to <hr>
	 // <hr> can not be contained in a
	 $html .= SetHTMLOutputMode('', ZERO_LEVEL, 0) . "<hr>\n";
	 if ( ($tmpline = $matches[1]) != '' ) {
	    $html .= SetHTMLOutputMode('p', ZERO_LEVEL, 0);
	 }
      } else {
         // it's ordinary output if nothing else
         $html .= SetHTMLOutputMode('p', ZERO_LEVEL, 0);
      }

/*******************
      // These are still problems as far as generating correct HTML is
      // concerned.  Paragraph (<p>) elements are not allowed to contain
      // other block-level elements (like <form>s). 
      if (strstr($tmpline, '%%Search%%'))
         $tmpline = str_replace('%%Search%%', RenderQuickSearch(), $tmpline);
      if (strstr($tmpline, '%%Fullsearch%%'))
         $tmpline = str_replace('%%Fullsearch%%', RenderFullSearch(), $tmpline);
      if (strstr($tmpline, '%%Mostpopular%%'))
         $tmpline = str_replace('%%Mostpopular%%', RenderMostPopular(), $tmpline);
      if(defined('WIKI_ADMIN') && strstr($tmpline, '%%ADMIN-'))
         $tmpline = ParseAdminTokens($tmpline);
********************/


      ///////////////////////////////////////////////////////
      // Replace tokens

      for ($i = 0; $i < $ntokens; $i++)
	  $tmpline = str_replace($FieldSeparator.$FieldSeparator.$i.$FieldSeparator, $replacements[$i], $tmpline);


      $html .= $tmpline . "\n";
   }

   $html .= SetHTMLOutputMode('', ZERO_LEVEL, 0);





   #-- oki, finished!!!
   return($html);

}





#-- var funcs --------------------------------------------------------------

   function tokenize($str, $pattern, &$orig, &$ntokens) {
      global $FieldSeparator;
      // Find any strings in $str that match $pattern and
      // store them in $orig, replacing them with tokens
      // starting at number $ntokens - returns tokenized string
      $new = '';      
      while (preg_match("/^(.*?)($pattern)/", $str, $matches)) {
         $linktoken = $FieldSeparator . $FieldSeparator . ($ntokens++) . $FieldSeparator;
         $new .= $matches[1] . $linktoken;
	 $orig[] = $matches[2];
         $str = substr($str, strlen($matches[0]));
      }
      $new .= $str;
      return $new;
   }


function LinkExistingWikiWord($link, $title="") {
   return(ewiki_link_regex_callback(array(strlen($title) ? "$title|$link" : $link)));
}

function LinkUnknownWikiWord($link, $title="") {
   return(LinkExistingWikiWord($link, $title));
}

function LinkURL($whatever) {
   return(ewiki_link_regex_callback(array($whatever)));
}

function LinkImage($whatever) {
   return(ewiki_link_regex_callback(array($whatever)));
}

function IsWikiPage($dbi, $page) {
   return($GLOBALS["ewiki_links"][$page]);
}

function ParseAndLink($whatever) {
   return(array('link'=>ewiki_link_regex_callback(array($whatever))));
}


   function SetHTMLOutputMode($tag, $tagtype, $level)
   {
      global $stack;
      $retvar = '';

      if ($level > 10) {
	  // arbitrarily limit tag nesting
	  //die ("Nesting depth exceeded in SetHTMLOutputMode");
	  // Now, instead of crapping out when we encounter a deeply
	  // nested list item, we just clamp the the maximum depth.
	  $level = 10;
      }
      
      if ($tagtype == ZERO_LEVEL) {
         // empty the stack until $level == 0;
         if ($tag == $stack->top()) {
            return; // same tag? -> nothing to do
         }
         while ($stack->cnt() > 0) {
            $closetag = $stack->pop();
            $retvar .= "</$closetag>\n";
         }
   
         if ($tag) {
            $retvar .= "<$tag>\n";
            $stack->push($tag);
         }


      } elseif ($tagtype == NESTED_LEVEL) {
         if ($level < $stack->cnt()) {
            // $tag has fewer nestings (old: tabs) than stack,
	    // reduce stack to that tab count
            while ($stack->cnt() > $level) {
               $closetag = $stack->pop();
               if ($closetag == false) {
                  //echo "bounds error in tag stack";
                  break;
               }
               $retvar .= "</$closetag>\n";
            }

	    // if list type isn't the same,
	    // back up one more and push new tag
	    if ($tag != $stack->top()) {
	       $closetag = $stack->pop();
	       $retvar .= "</$closetag><$tag>\n";
	       $stack->push($tag);
	    }
   
         } elseif ($level > $stack->cnt()) {
	    // Test for and close top level elements which are not allowed to contain
	    // other block-level elements.
	    if ($stack->cnt() == 1 and
	        preg_match('/^(p|pre|h\d)$/i', $stack->top()))
	    {
	       $closetag = $stack->pop();
	       $retvar .= "</$closetag>";
	    }

	    // we add the diff to the stack
	    // stack might be zero
	    if ($stack->cnt() < $level) {
	       while ($stack->cnt() < $level - 1) {
		  // This is a bit of a hack:
		  //
		  // We're not nested deep enough, and have to make up
		  // some kind of block element to nest within.
		  //
		  // Currently, this can only happen for nested list
		  // element (either <ul> <ol> or <dl>).  What we used
		  // to do here is to open extra lists of whatever
		  // type was requested.  This would result in invalid
		  // HTML, since and list is not allowed to contain
		  // another list without first containing a list
		  // item.  ("<ul><ul><li>Item</ul></ul>" is invalid.)
		  //
		  // So now, when we need extra list elements, we use
		  // a <dl>, and open it with an empty <dd>.

		  $retvar .= "<dl><dd>";
		  $stack->push('dl');
	       }

	       $retvar .= "<$tag>\n";
	       $stack->push($tag);
            }
   
         } else { // $level == $stack->cnt()
            if ($tag == $stack->top()) {
               return; // same tag? -> nothing to do
            } else {
	       // different tag - close old one, add new one
               $closetag = $stack->pop();
               $retvar .= "</$closetag>\n";
               $retvar .= "<$tag>\n";
               $stack->push($tag);
            }
         }

   
      } else { // unknown $tagtype
         die ("Passed bad tag type value in SetHTMLOutputMode");
      }

      return $retvar;
   }
   // end SetHTMLOutputMode



   class Stack {
      var $items = array();
      var $size = 0;

      function push($item) {
         $this->items[$this->size] = $item;
         $this->size++;
         return true;
      }  
   
      function pop() {
         if ($this->size == 0) {
            return false; // stack is empty
         }  
         $this->size--;
         return $this->items[$this->size];
      }  
   
      function cnt() {
         return $this->size;
      }  

      function top() {
         if($this->size)
            return $this->items[$this->size - 1];
         else
            return '';
      }  

   }  
   // end class definition



?>