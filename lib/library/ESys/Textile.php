<?php
/*

This is a slightly modified version of the textile parser supplied with
the textpattern framework. See notes below...

_____________
T E X T I L E

A Humane Web Text Generator

Version 2.0

Copyright (c) 2003-2004, Dean Allen <dean@textism.com>
All rights reserved.

Thanks to Carlo Zottmann <carlo@g-blog.net> for refactoring
Textile's procedural code into a class framework

Additions and fixes Copyright (c) 2006 Alex Shiels http://thresholdstate.com/

_____________
L I C E N S E

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice,
  this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name Textile nor the names of its contributors may be used to
  endorse or promote products derived from this software without specific
  prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

_________
U S A G E

Example: get XHTML from a given Textile-markup string ($string)
    
    $textile = new ESys_Textile();
    echo $textile->parse($string);

*/

// define these before including this file to override the standard glyphs
@define('txt_quote_single_open',  '&#8216;');
@define('txt_quote_single_close', '&#8217;');
@define('txt_quote_double_open',  '&#8220;');
@define('txt_quote_double_close', '&#8221;');
@define('txt_apostrophe',		  '&#8217;');
@define('txt_prime',			  '&#8242;');
@define('txt_prime_double', 	  '&#8243;');
@define('txt_ellipsis', 		  '&#8230;');
@define('txt_emdash',			  '&#8212;');
@define('txt_endash',			  '&#8211;');
@define('txt_dimension',		  '&#215;');
@define('txt_trademark',		  '&#8482;');
@define('txt_registered',		  '&#174;');
@define('txt_copyright',		  '&#169;');

/**
 * @package ESys
 */
class ESys_Textile
{
	private $hlgn;
	private $vlgn;
	private $clas;
	private $lnge;
	private $styl;
	private $cspn;
	private $rspn;
	private $a;
	private $s;
	private $c;
	private $pnct;
	private $rel;
	private $fn;
	
	private $shelf = array();
	private $restricted = false;
	private $noimage = false;
	private $lite = false;
	private $url_schemes = array();
	private $glyph = array();
	private $hu = '';
	
	private $ver = '2.0.0';
	private $rev = '$Rev: 2462 $';
	
	private $doc_root;


	public function __construct ()
	{
		$this->hlgn = "(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+(?! ))";
		$this->vlgn = "[\-^~]";
		$this->clas = "(?:\([^)]+\))";
		$this->lnge = "(?:\[[^]]+\])";
		$this->styl = "(?:\{[^}]+\})";
		$this->cspn = "(?:\\\\\d+)";
		$this->rspn = "(?:\/\d+)";
		$this->a = "(?:{$this->hlgn}|{$this->vlgn})*";
		$this->s = "(?:{$this->cspn}|{$this->rspn})*";
		$this->c = "(?:{$this->clas}|{$this->styl}|{$this->lnge}|{$this->hlgn})*";

		$this->pnct = '[\!"#\$%&\'()\*\+,\-\./:;<=>\?@\[\\\]\^_`{\|}\~]';
		$this->urlch = '[\w"$\-_.+!*\'(),";\/?:@=&%#{}|\\^~\[\]`]';

		$this->url_schemes = array('http','https','ftp','mailto');

		$this->btag = array('bq', 'bc', 'notextile', 'pre', 'h[1-6]', 'fn\d+', 'p');

		$this->glyph = array(
		   'quote_single_open'	=> txt_quote_single_open,
		   'quote_single_close' => txt_quote_single_close,
		   'quote_double_open'	=> txt_quote_double_open,
		   'quote_double_close' => txt_quote_double_close,
		   'apostrophe' 		=> txt_apostrophe,
		   'prime'				=> txt_prime,
		   'prime_double'		=> txt_prime_double,
		   'ellipsis'			=> txt_ellipsis,
		   'emdash' 			=> txt_emdash,
		   'endash' 			=> txt_endash,
		   'dimension'			=> txt_dimension,
		   'trademark'			=> txt_trademark,
		   'registered' 		=> txt_registered,
		   'copyright'			=> txt_copyright,
		);

		if (defined('hu'))
			$this->hu = hu;

		if (defined('DIRECTORY_SEPARATOR'))
			$this->ds = constant('DIRECTORY_SEPARATOR');
		else
			$this->ds = '/';

		$this->doc_root = @$_SERVER['DOCUMENT_ROOT'];
		if (!$this->doc_root)
			$this->doc_root = @$_SERVER['PATH_TRANSLATED']; // IIS
			
		$this->doc_root = rtrim($this->doc_root, $this->ds).$this->ds;

	}



	/**
	 * @param string $text
	 * @param string $lite
	 * @param string $encode
	 * @param string $noimage
	 * @param string $strict
	 * @param string $rel
	 * @return string
	 */
	public function parse ($text, $lite = '', $encode = '', $noimage = '', $strict = '', $rel = '')
	{
		$this->rel = ($rel) ? ' rel="'.$rel.'"' : '';

		$this->lite = $lite;
		$this->noimage = $noimage;

		if ($encode) {
		 $text = $this->incomingEntities($text);
			$text = str_replace("x%x%", "&#38;", $text);
			return $text;
		} else {

			if(!$strict) {
				$text = $this->cleanWhiteSpace($text);
			}

			if (!$lite) {
				$text = $this->block($text);
			}

			$text = $this->retrieve($text);
			$text = $this->retrieveURLs($text);

				// just to be tidy
			$text = str_replace("<br />", "<br />\n", $text);

			return $text;
		}
	}



	/**
	 * @param string $text
	 * @param int $lite
	 * @param int $noimage
	 * @param string $rel
	 * @return string
	 */
	public function parseRestricted ($text, $lite = 1, $noimage = 1, $rel = 'nofollow')
	{
		$this->restricted = true;
		$this->lite = $lite;
		$this->noimage = $noimage;

		$this->rel = ($rel) ? ' rel="'.$rel.'"' : '';

			// escape any raw html
			$text = $this->encode_html($text, 0);

			$text = $this->cleanWhiteSpace($text);

			if ($lite) {
				$text = $this->blockLite($text);
			}
			else {
				$text = $this->block($text);
			}

			$text = $this->retrieve($text);
			$text = $this->retrieveURLs($text);

				// just to be tidy
			$text = str_replace("<br />", "<br />\n", $text);

			return $text;
	}


	private function pba ($in, $element = "", $include_id = 1) // "parse block attributes"
	{
		$style = '';
		$class = '';
		$lang = '';
		$colspan = '';
		$rowspan = '';
		$id = '';
		$atts = '';

		if (!empty($in)) {
			$matched = $in;
			if ($element == 'td') {
				if (preg_match("/\\\\(\d+)/", $matched, $csp)) $colspan = $csp[1];
				if (preg_match("/\/(\d+)/", $matched, $rsp)) $rowspan = $rsp[1];
			}

			if ($element == 'td' or $element == 'tr') {
				if (preg_match("/($this->vlgn)/", $matched, $vert))
					$style[] = "vertical-align:" . $this->vAlign($vert[1]) . ";";
			}

			if (preg_match("/\{([^}]*)\}/", $matched, $sty)) {
				$style[] = rtrim($sty[1], ';') . ';';
				$matched = str_replace($sty[0], '', $matched);
			}

			if (preg_match("/\[([^]]+)\]/U", $matched, $lng)) {
				$lang = $lng[1];
				$matched = str_replace($lng[0], '', $matched);
			}

			if (preg_match("/\(([^()]+)\)/U", $matched, $cls)) {
				$class = $cls[1];
				$matched = str_replace($cls[0], '', $matched);
			}

			if (preg_match("/([(]+)/", $matched, $pl)) {
				$style[] = "padding-left:" . strlen($pl[1]) . "em;";
				$matched = str_replace($pl[0], '', $matched);
			}

			if (preg_match("/([)]+)/", $matched, $pr)) {
				// $this->dump($pr);
				$style[] = "padding-right:" . strlen($pr[1]) . "em;";
				$matched = str_replace($pr[0], '', $matched);
			}

			if (preg_match("/($this->hlgn)/", $matched, $horiz))
				$style[] = "text-align:" . $this->hAlign($horiz[1]) . ";";

			if (preg_match("/^(.*)#(.*)$/", $class, $ids)) {
				$id = $ids[2];
				$class = $ids[1];
			}

			if ($this->restricted)
				return ($lang)	  ? ' lang="'	 . $lang			.'"':'';

			return join('',array(
				($style)   ? ' style="'   . join("", $style) .'"':'',
				($class)   ? ' class="'   . $class			 .'"':'',
				($lang)    ? ' lang="'	  . $lang			 .'"':'',
				($id and $include_id) ? ' id="' 	 . $id				.'"':'',
				($colspan) ? ' colspan="' . $colspan		 .'"':'',
				($rowspan) ? ' rowspan="' . $rowspan		 .'"':''
			));
		}
		return '';
	}


	private function hasRawText ($text)
	{
		// checks whether the text has text not already enclosed by a block tag
		$r = trim(preg_replace('@<(p|blockquote|div|form|table|ul|ol|pre|h\d)[^>]*?'.'>.*</\1>@s', '', trim($text)));
		$r = trim(preg_replace('@<(hr|br)[^>]*?/>@', '', $r));
		return '' != $r;
	}


	private function table ($text)
	{
		$text = $text . "\n\n";
		return preg_replace_callback("/^(?:table(_?{$this->s}{$this->a}{$this->c})\. ?\n)?^({$this->a}{$this->c}\.? ?\|.*\|)\n\n/smU",
		   array($this, "fTable"), $text);
	}


	private function fTable ($matches)
	{
		$tatts = $this->pba($matches[1], 'table');

		foreach(preg_split("/\|$/m", $matches[2], -1, PREG_SPLIT_NO_EMPTY) as $row) {
			if (preg_match("/^($this->a$this->c\. )(.*)/m", ltrim($row), $rmtch)) {
				$ratts = $this->pba($rmtch[1], 'tr');
				$row = $rmtch[2];
			} else $ratts = '';

				$cells = array();
			foreach(explode("|", $row) as $cell) {
				$ctyp = "d";
				if (preg_match("/^_/", $cell)) $ctyp = "h";
				if (preg_match("/^(_?$this->s$this->a$this->c\. )(.*)/", $cell, $cmtch)) {
					$catts = $this->pba($cmtch[1], 'td');
					$cell = $cmtch[2];
				} else $catts = '';

				$cell = $this->graf($cell);

				if (trim($cell) != '')
					$cells[] = $this->doTagBr("t$ctyp", "\t\t\t<t$ctyp$catts>$cell</t$ctyp>");
			}
			$rows[] = "\t\t<tr$ratts>\n" . join("\n", $cells) . ($cells ? "\n" : "") . "\t\t</tr>";
			unset($cells, $catts);
		}
		return "\t<table$tatts>\n" . join("\n", $rows) . "\n\t</table>\n\n";
	}


	private function lists ($text)
	{
		return preg_replace_callback("/^([#*]+$this->c .*)$(?![^#*])/smU", array($this, "fList"), $text);
	}


	private function fList ($m)
	{
		$text = preg_split('/\n(?=[*#])/m', $m[0]);
		foreach($text as $line) {
			$nextline = next($text);
			if (preg_match("/^([#*]+)($this->a$this->c) (.*)$/s", $line, $m)) {
				list(, $tl, $atts, $content) = $m;
				$nl = '';
				if (preg_match("/^([#*]+)\s.*/", $nextline, $nm))
					$nl = $nm[1];
				if (!isset($lists[$tl])) {
					$lists[$tl] = true;
					$atts = $this->pba($atts);
					$line = "\t<" . $this->lT($tl) . "l$atts>\n\t\t<li>" . rtrim($content);
				} else {
					$line = "\t\t<li>" . rtrim($content);
				}

				if(strlen($nl) <= strlen($tl)) $line .= "</li>";
				foreach(array_reverse($lists) as $k => $v) {
					if(strlen($k) > strlen($nl)) {
						$line .= "\n\t</" . $this->lT($k) . "l>";
						if(strlen($k) > 1)
							$line .= "</li>";
						unset($lists[$k]);
					}
				}
			}
			else {
				$line .= n;
			}
			$out[] = $line;
		}
		return $this->doTagBr('li', join("\n", $out));
	}


	private function lT ($in)
	{
		return preg_match("/^#+/", $in) ? 'o' : 'u';
	}


	private function doTagBr ($tag, $in)
	{
		return preg_replace_callback('@<('.preg_quote($tag).')([^>]*?)>(.*)(</\1>)@s', array($this, 'doBr'), $in);
	}



	private function doPBr ($in)
	{
		return $this->doTagBr('p', $in);
	}


	private function doBr ($m)
	{
		$content = preg_replace("@(.+)(?<!<br>|<br />)\n(?![#*\s|])@", '$1<br />', $m[3]);
		return '<'.$m[1].$m[2].'>'.$content.$m[4];
	}


	private function block ($text)
	{
		$find = $this->btag;
		$tre = join('|', $find);

		$text = explode("\n\n", $text);

		$tag = 'p';
		$atts = $cite = $graf = $ext  = '';

		foreach($text as $line) {
			$anon = 0;
			if (preg_match("/^($tre)($this->a$this->c)\.(\.?)(?::(\S+))? (.*)$/s", $line, $m)) {
				// last block was extended, so close it
				if ($ext)
					$out[count($out)-1] .= $c1;
				// new block
				list(,$tag,$atts,$ext,$cite,$graf) = $m;
				list($o1, $o2, $content, $c2, $c1) = $this->fBlock(array(0,$tag,$atts,$ext,$cite,$graf));

				// leave off c1 if this block is extended, we'll close it at the start of the next block
				if ($ext)
					$line = $o1.$o2.$content.$c2;
				else
					$line = $o1.$o2.$content.$c2.$c1;
			}
			else {
				// anonymous block
				$anon = 1;
				if ($ext or !preg_match('/^ /', $line)) {
					list($o1, $o2, $content, $c2, $c1) = $this->fBlock(array(0,$tag,$atts,$ext,$cite,$line));
					// skip $o1/$c1 because this is part of a continuing extended block
					if ($tag == 'p' and !$this->hasRawText($content)) {
						$line = $content;
					}
					else {
						$line = $o2.$content.$c2;
					}
				}
				else {
				   $line = $this->graf($line);
				}
			}

			$line = $this->doPBr($line);
			$line = preg_replace('/<br>/', '<br />', $line);

			if ($ext and $anon)
				$out[count($out)-1] .= "\n".$line;
			else
				$out[] = $line;

			if (!$ext) {
				$tag = 'p';
				$atts = '';
				$cite = '';
				$graf = '';
			}
		}
		if ($ext) $out[count($out)-1] .= $c1;
		return join("\n\n", $out);
	}




	private function fBlock ($m)
	{
		// $this->dump($m);
		list(, $tag, $att, $ext, $cite, $content) = $m;
		$atts = $this->pba($att);

		$o1 = $o2 = $c2 = $c1 = '';

		if (preg_match("/fn(\d+)/", $tag, $fns)) {
			$tag = 'p';
			$fnid = empty($this->fn[$fns[1]]) ? $fns[1] : $this->fn[$fns[1]];
			$atts .= ' id="fn' . $fnid . '"';
			if (strpos($atts, 'class=') === false)
				$atts .= ' class="footnote"';
			$content = '<sup>' . $fns[1] . '</sup> ' . $content;
		}

		if ($tag == "bq") {
			$cite = $this->shelveURL($cite);
			$cite = ($cite != '') ? ' cite="' . $cite . '"' : '';
			$o1 = "\t<blockquote$cite$atts>\n";
			$o2 = "\t\t<p".$this->pba($att, '', 0).">";
			$c2 = "</p>";
			$c1 = "\n\t</blockquote>";
		}
		elseif ($tag == 'bc') {
			$o1 = "<pre$atts>";
			$o2 = "<code".$this->pba($att, '', 0).">";
			$c2 = "</code>";
			$c1 = "</pre>";
			$content = $this->shelve($this->r_encode_html(rtrim($content, "\n")."\n"));
		}
		elseif ($tag == 'notextile') {
			$content = $this->shelve($content);
			$o1 = $o2 = '';
			$c1 = $c2 = '';
		}
		elseif ($tag == 'pre') {
			$content = $this->shelve($this->r_encode_html(rtrim($content, "\n")."\n"));
			$o1 = "<pre$atts>";
			$o2 = $c2 = '';
			$c1 = "</pre>";
		}
		else {
			$o2 = "\t<$tag$atts>";
			$c2 = "</$tag>";
		  }

		$content = $this->graf($content);

		return array($o1, $o2, $content, $c2, $c1);
	}


	private function graf ($text)
	{
		// handle normal paragraph text
		if (!$this->lite) {
			$text = $this->noTextile($text);
			$text = $this->code($text);
		}

		$text = $this->getRefs($text);
		$text = $this->links($text);
		if (!$this->noimage)
			$text = $this->image($text);

		if (!$this->lite) {
			$text = $this->table($text);
			$text = $this->lists($text);
		}

		$text = $this->span($text);
		$text = $this->footnoteRef($text);
		$text = $this->glyphs($text);
		return rtrim($text, "\n");
	}


	private function span ($text)
	{
		$qtags = array('\*\*','\*','\?\?','-','__','_','%','\+','~','\^');
		$pnct = ".,\"'?!;:";

		foreach($qtags as $f) {
			$text = preg_replace_callback("/
				(^|(?<=[\s>$pnct\(])|[{[])
				($f)(?!$f)
				({$this->c})
				(?::(\S+))?
				([^\s$f]+|\S.*?[^\s$f\n])
				([$pnct]*)
				$f
				($|[\]}]|(?=[[:punct:]]{1,2}|\s|\)))
			/x", array($this, "fSpan"), $text);
		}
		return $text;
	}


	private function fSpan ($m)
	{
		$qtags = array(
			'*'  => 'strong',
			'**' => 'b',
			'??' => 'cite',
			'_'  => 'em',
			'__' => 'i',
			'-'  => 'del',
			'%'  => 'span',
			'+'  => 'ins',
			'~'  => 'sub',
			'^'  => 'sup',
		);

		list(, $pre, $tag, $atts, $cite, $content, $end, $tail) = $m;
		$tag = $qtags[$tag];
		$atts = $this->pba($atts);
		$atts .= ($cite != '') ? 'cite="' . $cite . '"' : '';

		$out = "<$tag$atts>$content$end</$tag>";

		if (($pre and !$tail) or ($tail and !$pre))
			$out = $pre.$out.$tail;

//		$this->dump($out);

		return $out;

	}


	private function links ($text)
	{
		return preg_replace_callback('/
			(^|(?<=[\s>.$pnct\(])|[{[]) # $pre
			"							 # start
			(' . $this->c . ')			 # $atts
			([^"]+?)					 # $text
			(?:\(([^)]+?)\)(?="))?		 # $title
			":
			('.$this->urlch.'+?)		 # $url
			(\/)?						 # $slash
			([^\w\/;]*?)				 # $post
			([\]}]|(?=\s|$|\)))
		/x', array($this, "fLink"), $text);
	}


	private function fLink ($m)
	{
		list(, $pre, $atts, $text, $title, $url, $slash, $post, $tail) = $m;

		$atts = $this->pba($atts);
		$atts .= ($title != '') ? ' title="' . $this->encode_html($title) . '"' : '';

		if (!$this->noimage)
			$text = $this->image($text);

		$text = $this->span($text);
		$text = $this->glyphs($text);

		$url = $this->shelveURL($url.$slash);

		$out = '<a href="' . $url . '"' . $atts . $this->rel . '>' . trim($text) . '</a>' . $post;
		
		if (($pre and !$tail) or ($tail and !$pre))
			$out = $pre.$out.$tail;

		// $this->dump($out);
		return $this->shelve($out);

	}


	private function getRefs ($text)
	{
		return preg_replace_callback("/^\[(.+)\]((?:http:\/\/|\/)\S+)(?=\s|$)/Um",
			array($this, "refs"), $text);
	}


	private function refs ($m)
	{
		list(, $flag, $url) = $m;
		$this->urlrefs[$flag] = $url;
		return '';
	}


	private function shelveURL ($text)
	{
		if (!$text) return '';
		$ref = md5($text);
		$this->urlshelf[$ref] = $text;
		return 'urlref:'.$ref;
	}


	private function retrieveURLs ($text)
	{
		return preg_replace_callback('/urlref:(\w{32})/',
			array($this, "retrieveURL"), $text);
	}


	/**
	 * @param mixed
	 * @return void
	 */
	function retrieveURL ($m)
	{
		$ref = $m[1];
		if (!isset($this->urlshelf[$ref]))
			return $ref;
		$url = $this->urlshelf[$ref];
		if (isset($this->urlrefs[$url]))
			$url = $this->urlrefs[$url];
		return $this->r_encode_html($this->relURL($url));
	}


	private function relURL ($url)
	{
		$parts = @parse_url(urldecode($url));
		if ((empty($parts['scheme']) or @$parts['scheme'] == 'http') and
			 empty($parts['host']) and
			 preg_match('/^\w/', @$parts['path']))
			$url = $this->hu.$url;
		if ($this->restricted and !empty($parts['scheme']) and
			  !in_array($parts['scheme'], $this->url_schemes))
			return '#';
		return $url;
	}


	private function isRelURL ($url)
	{
		$parts = @parse_url($url);
		return (empty($parts['scheme']) and empty($parts['host']));
	}


	private function image ($text)
	{
		return preg_replace_callback("/
			(?:[[{])?		   # pre
			\!				   # opening !
			(\<|\=|\>)? 	   # optional alignment atts
			($this->c)		   # optional style,class atts
			(?:\. )?		   # optional dot-space
			([^\s(!]+)		   # presume this is the src
			\s? 			   # optional space
			(?:\(([^\)]+)\))?  # optional title
			\!				   # closing
			(?::(\S+))? 	   # optional href
			(?:[\]}]|(?=\s|$|\))) # lookahead: space or end of string
		/x", array($this, "fImage"), $text);
	}


	private function fImage ($m)
	{
		list(, $algn, $atts, $url) = $m;
		$atts  = $this->pba($atts);
		$atts .= ($algn != '')	? ' align="' . $this->iAlign($algn) . '"' : '';
		$atts .= (isset($m[4])) ? ' title="' . $m[4] . '"' : '';
		$atts .= (isset($m[4])) ? ' alt="'	 . $m[4] . '"' : ' alt=""';
		$size = false;
		if ($this->isRelUrl($url))
			$size = @getimagesize(realpath($this->doc_root.ltrim($url, $this->ds)));
		if ($size) $atts .= " $size[3]";

		$href = (isset($m[5])) ? $this->shelveURL($m[5]) : '';
		$url = $this->shelveURL($url);

		$out = array(
			($href) ? '<a href="' . $href . '">' : '',
			'<img src="' . $url . '"' . $atts . ' />',
			($href) ? '</a>' : ''
		);

		return $this->shelve(join('',$out));
	}


	private function code ($text)
	{
		$text = $this->doSpecial($text, '<code>', '</code>', 'fCode');
		$text = $this->doSpecial($text, '@', '@', 'fCode');
		$text = $this->doSpecial($text, '<pre>', '</pre>', 'fPre');
		return $text;
	}


	private function fCode ($m)
	{
	  @list(, $before, $text, $after) = $m;
	  return $before.$this->shelve('<code>'.$this->r_encode_html($text).'</code>').$after;
	}


	private function fPre ($m)
	{
	  @list(, $before, $text, $after) = $m;
	  return $before.'<pre>'.$this->shelve($this->r_encode_html($text)).'</pre>'.$after;
	}

	private function shelve ($val)
	{
		$i = uniqid(rand());
		$this->shelf[$i] = $val;
		return $i;
	}


	private function retrieve ($text)
	{
		if (is_array($this->shelf))
			do {
				$old = $text;
				$text = strtr($text, $this->shelf);
			 } while ($text != $old);

		return $text;
	}


// NOTE: deprecated
	private function incomingEntities ($text)
	{
		return preg_replace("/&(?![#a-z0-9]+;)/i", "x%x%", $text);
	}


// NOTE: deprecated
    private function encodeEntities ($text)
	{
		return (function_exists('mb_encode_numericentity'))
		?	 $this->encode_high($text)
		:	 htmlentities($text, ENT_NOQUOTES, "utf-8");
	}


// NOTE: deprecated
	private function fixEntities ($text)
	{
		/*	de-entify any remaining angle brackets or ampersands */
		return str_replace(array("&gt;", "&lt;", "&amp;"),
			array(">", "<", "&"), $text);
	}


	private function cleanWhiteSpace ($text)
	{
		$out = str_replace("\r\n", "\n", $text);		# DOS line endings
		$out = preg_replace("/^[ \t]*\n/m", "\n", $out);	# lines containing only whitespace
		$out = preg_replace("/\n{3,}/", "\n\n", $out);	# 3 or more line ends
		$out = preg_replace("/^\n*/", "", $out);		# leading blank lines
		return $out;
	}


	private function doSpecial ($text, $start, $end, $method='fSpecial')
	{
	  return preg_replace_callback('/(^|\s|[[({>])'.preg_quote($start, '/').'(.*?)'.preg_quote($end, '/').'(\s|$|[\])}])?/ms',
			array($this, $method), $text);
	}


	private function fSpecial ($m)
	{
		// A special block like notextile or code
	  @list(, $before, $text, $after) = $m;
		return $before.$this->shelve($this->encode_html($text)).$after;
	}


	private function noTextile ($text)
	{
		 $text = $this->doSpecial($text, '<notextile>', '</notextile>', 'fTextile');
		 return $this->doSpecial($text, '==', '==', 'fTextile');

	}


	private function fTextile ($m)
	{
		@list(, $before, $notextile, $after) = $m;
		#$notextile = str_replace(array_keys($modifiers), array_values($modifiers), $notextile);
		return $before.$this->shelve($notextile).$after;
	}


	private function footnoteRef ($text)
	{
		return preg_replace('/(?<=\S)\[([0-9]+)\](\s)?/Ue',
			'$this->footnoteID(\'\1\',\'\2\')', $text);
	}


	private function footnoteID ($id, $t)
	{
		if (empty($this->fn[$id]))
			$this->fn[$id] = uniqid(rand());
		$fnid = $this->fn[$id];
		return '<sup class="footnote"><a href="#fn'.$fnid.'">'.$id.'</a></sup>'.$t;
	}


	private function glyphs ($text)
	{

		// fix: hackish
		$text = preg_replace('/"\z/', "\" ", $text);
		$pnc = '[[:punct:]]';

		$glyph_search = array(
			'/(\w)\'(\w)/', 									 // apostrophe's
			'/(\s)\'(\d+\w?)\b(?!\')/', 						 // back in '88
			'/(\S)\'(?=\s|'.$pnc.'|<|$)/',						 //  single closing
			'/\'/', 											 //  single opening
			'/(\S)\"(?=\s|'.$pnc.'|<|$)/',						 //  double closing
			'/"/',												 //  double opening
			'/\b([A-Z][A-Z0-9]{2,})\b(?:[(]([^)]*)[)])/',		 //  3+ uppercase acronym
			'/(?<=\s|^|[>(;-])([A-Z]{3,})([a-z]*)(?=\s|'.$pnc.'|<|$)/',  //  3+ uppercase
			'/([^.]?)\.{3}/',									 //  ellipsis
			'/(\s?)--(\s?)/',									 //  em dash
			'/\s-(?:\s|$)/',									 //  en dash
			'/(\d+)( ?)x( ?)(?=\d+)/',							 //  dimension sign
			'/(\b ?|\s|^)[([]TM[])]/i', 						 //  trademark
			'/(\b ?|\s|^)[([]R[])]/i',							 //  registered
			'/(\b ?|\s|^)[([]C[])]/i',							 //  copyright
		 );

		extract($this->glyph, EXTR_PREFIX_ALL, 'txt');

		$glyph_replace = array(
			'$1'.$txt_apostrophe.'$2',			 // apostrophe's
			'$1'.$txt_apostrophe.'$2',			 // back in '88
			'$1'.$txt_quote_single_close,		 //  single closing
			$txt_quote_single_open, 			 //  single opening
			'$1'.$txt_quote_double_close,		 //  double closing
			$txt_quote_double_open, 			 //  double opening
			'<acronym title="$2">$1</acronym>',  //  3+ uppercase acronym
			'<span class="caps">$1</span>$2',	 //  3+ uppercase
			'$1'.$txt_ellipsis, 				 //  ellipsis
			'$1'.$txt_emdash.'$2',				 //  em dash
			' '.$txt_endash.' ',				 //  en dash
			'$1$2'.$txt_dimension.'$3', 		 //  dimension sign
			'$1'.$txt_trademark,				 //  trademark
			'$1'.$txt_registered,				 //  registered
			'$1'.$txt_copyright,				 //  copyright
		 );

		 $text = preg_split("@(<[\w/!?].*>)@Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		 $i = 0;
		 foreach($text as $line) {
			 // text tag text tag text ...
			 if (++$i % 2) {
				 // raw < > & chars are already entity encoded in restricted mode
				 if (!$this->restricted) {
					 $line = $this->encode_raw_amp($line);
					 $line = $this->encode_lt_gt($line);
				 }
				 $line = preg_replace($glyph_search, $glyph_replace, $line);
			 }
			  $glyph_out[] = $line;
		 }
		 return join('', $glyph_out);
	}


	private function iAlign ($in)
	{
		$vals = array(
			'<' => 'left',
			'=' => 'center',
			'>' => 'right');
		return (isset($vals[$in])) ? $vals[$in] : '';
	}


	private function hAlign ($in)
	{
		$vals = array(
			'<'  => 'left',
			'='  => 'center',
			'>'  => 'right',
			'<>' => 'justify');
		return (isset($vals[$in])) ? $vals[$in] : '';
	}


	private function vAlign ($in)
	{
		$vals = array(
			'^' => 'top',
			'-' => 'middle',
			'~' => 'bottom');
		return (isset($vals[$in])) ? $vals[$in] : '';
	}


// NOTE: deprecated
	private function encode_high ($text, $charset = "UTF-8")
	{
		return mb_encode_numericentity($text, $this->cmap(), $charset);
	}


// NOTE: deprecated
	private function decode_high ($text, $charset = "UTF-8")
	{
		return mb_decode_numericentity($text, $this->cmap(), $charset);
	}


// NOTE: deprecated
	private function cmap ()
	{
		$f = 0xffff;
		$cmap = array(
			0x0080, 0xffff, 0, $f);
		return $cmap;
	}


	private function encode_raw_amp ($text)
	 {
		return preg_replace('/&(?!#?[a-z0-9]+;)/i', '&#38;', $text);
	}


	private function encode_lt_gt ($text)
	 {
		return strtr($text, array('<' => '&#60;', '>' => '&#62;'));
	}


	private function encode_html ($str, $quotes=1)
	{
		$a = array(
			'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}


	private function r_encode_html ($str, $quotes=1)
	{
		// in restricted mode, input has already been escaped
		if ($this->restricted)
			return $str;
		return $this->encode_html($str, $quotes);
	}


	private function textile_popup_help ($name, $helpvar, $windowW, $windowH)
	{
		return ' <a target="_blank" href="http://www.textpattern.com/help/?item=' . $helpvar . '" onclick="window.open(this.href, \'popupwindow\', \'width=' . $windowW . ',height=' . $windowH . ',scrollbars,resizable\'); return false;">' . $name . '</a><br />';

		return $out;
	}


// NOTE: deprecated
	private function txtgps ($thing)
	{
		if (isset($_POST[$thing])) {
			if (get_magic_quotes_gpc()) {
				return stripslashes($_POST[$thing]);
			}
			else {
				return $_POST[$thing];
			}
		}
		else {
			return '';
		}
	}


// NOTE: deprecated
	private function dump ()
	{
		foreach (func_get_args() as $a)
			echo "\n<pre>",(is_array($a)) ? print_r($a) : $a, "</pre>\n";
	}



	private function blockLite ($text)
	{
		$this->btag = array('bq', 'p');
		return $this->block($text."\n\n");
	}


}

