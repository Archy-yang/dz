<?php

if(!defined('IN_WP')) {
	exit('Access Denied');
}

class template {

	//note var
	var $base;
	var $defaulttpldir;
	var $tpldir;
	var $objdir;
	var $tplfile;
	var $objfile;
	var $tplid;
	var $currdir;
	var $tmptpldir;
	var $vars;//note
	var $removeblanks;
	var $stdout;

	function __construct(&$base, $tplid, $currdir) {
		$this->template($base, $tplid, $currdir);
	}

	function template(&$base, $tplid, $currdir) {
		$this->base = $base;
        ob_start();
		if(file_exists(DIR_TPL.'/'.$currdir)) {
			$this->currdir = $currdir;
			$this->tplid = $tplid;
		} else {
			$this->currdir = 'default';
			$this->tplid = 1;
		}
		$this->defaulttpldir = DIR_TPL.'/default';
		$this->tpldir = DIR_TPL.'/'.$this->currdir;
		$this->objdir = $this->tmptpldir = DIR_DATA.'/cache/tpl';
		if(version_compare(PHP_VERSION, '5') == -1) {
			register_shutdown_function(array(&$this, '__destruct'));
		}
		$this->removeblanks = false;
		$this->stdout = 'display';
	}

	//note  publlic
	function assign($k, $v) {
        WAPCONV && $v = $this->base->convertWapCharset($v, CHARSET, $this->base->config['wap']['charset']);
		$this->vars[$k] = $v;
	}

	//note  publlic
	function display($file) {
		extract($this->vars, EXTR_SKIP);
		include $this->getObj($file);
	}

	function getObj($file, $tpldir = '') {
		$subdir = ($pos = strpos($file, '/')) === false ? '' : substr($file, 0, $pos);
		$file = $subdir ? substr($file, $pos + 1) : $file;
		isset($_REQUEST['inajax']) && ($file == 'header' || $file == 'footer') && $file = 'ajax_'.$file;
		$this->tplfile = ($tpldir ? $tpldir : $this->tpldir).'/'.($subdir ? $subdir.'/' : '').$file.'.htm';
		$this->objfile = $this->objdir.'/'.($tpldir ? '' : $this->tplid.'_').($subdir ? $subdir.'_' : '').$file.'.php';
		ISWAP && $this->objfile = $this->objdir.'/'.($tpldir ? '' : $this->tplid.'_').'wap_'.($subdir ? $subdir.'_' : '').$file.'.php';
		if(@filemtime($this->tplfile) === FALSE) {
			$this->tplfile = $this->defaulttpldir.'/'.($subdir ? $subdir.'/' : '').$file.'.htm';
        }
		//note
		if(!file_exists($this->objfile) || @filemtime($this->objfile) < filemtime($this->tplfile) || DEBUG) {
			$this->compile();
        }
		return $this->objfile;
	}

	function getTpl($file) {
		$subdir = ($pos = strpos($file, '/')) === false ? '' : substr($file, 0, $pos);
		$file = $subdir ? substr($file, $pos + 1) : $file;
		$tplfile = $this->tpldir.'/'.($subdir ? $subdir.'/' : '').$file.'.htm';
		if(@filemtime($tplfile) === FALSE) {
			$tplfile = $this->defaulttpldir.'/'.($subdir ? $subdir.'/' : '').$file.'.htm';
		}
		return $tplfile;
	}

    function compile() { 
		$var_regexp = "\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*";
		$vtag_regexp = "\<\?=(\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)\?\>";
		$const_regexp = "\{([\w]+)\}";
        $template = file_get_contents($this->tplfile);

		for($i = 1; $i <= 3; $i++) {
			if(strpos($template, '{subtpl') !== FALSE) {
				if(DEBUG == 2) {
					$template = str_replace('{subtpl ', '{tpl ', $template);
				} else {
					$template = preg_replace("/[\n\r\t]*\{subtpl\s+([a-z0-9_:\/]+)\}[\n\r\t]*/ies", "file_get_contents(\$this->getTpl('\\1'))", $template);
				}
			}
		}

		$remove = array(
			'/(^|\r|\n)\/\*.+?(\r|\n)\*\/(\r|\n)/is',
			'/\/\/note.+?(\r|\n)/i',
			'/\/\/debug.+?(\r|\n)/i',
			'/(^|\r|\n)(\s|\t)+/',
			'/(\r|\n)/',
		);
		$this->removeblanks && $template = preg_replace($remove, '', $template);

		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace("/\{lang\s+([\w\/]+?)\}/ise", "\$this->lang('\\1')", $template);

		$template = preg_replace("/\{($var_regexp)\}/", "<?=\\1?>", $template);
        $template = preg_replace("/\{($const_regexp)\}/", "<?=\\1?>", $template);

		$template = preg_replace("/(?<!\<\?\=|\\\\)$var_regexp/", "<?=\\0?>", $template);

        $template = preg_replace("/\<\?=(\@?\\\$[a-zA-Z_]\w*)((\[[\\$\[\]\w]+\])+)\?\>/ies", "\$this->arrayindex('\\1', '\\2')", $template);

		$template = preg_replace("/\{\{eval (.*?)\}\}/ies", "\$this->stripvtag('<? \\1?>')", $template);
		$template = preg_replace("/\{eval (.*?)\}/ies", "\$this->stripvtag('<? \\1?>')", $template);
		$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "\$this->stripvtag('<? echo \\1; ?>','')", $template);
		$template = preg_replace("/\{for (.*?)\}/ies", "\$this->stripvtag('<? for(\\1) {?>')", $template);

		$template = preg_replace("/\{elseif\s+(.+?)\}/ies", "\$this->stripvtag('<? } elseif(\\1) { ?>')", $template);

		for($i=0; $i<2; $i++) {
			$template = preg_replace("/\{loop\s+$vtag_regexp\s+$vtag_regexp\s+$vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopsection('\\1', '\\2', '\\3', '\\4')", $template);
			$template = preg_replace("/\{loop\s+$vtag_regexp\s+$vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopsection('\\1', '', '\\2', '\\3')", $template);
		}
        $template = preg_replace("/\{if\s+(.+?)\}/ies", "\$this->stripvtag('<? if(\\1) { ?>')", $template);
        $template = preg_replace("/\{tpl\s+(\w+?)\}/is", "<? include \$this->getObj('\\1'); ?>", $template);
        $template = preg_replace("/\{tpl\s+(.+?)\}/ise", "\$this->stripvtag('<? include \$this->getObj(\"\\1\"); ?>')", $template);
		$template = preg_replace("/\{tmptpl\s+(\w+?)\}/is", "<? include \$this->getObj(\"\\1\", \$this->tmptpldir);?>", $template);
        $template = preg_replace("/\{tmptpl\s+(.+?)\}/ise", "\$this->stripvtag('<? include \$this->getObj(\"\\1\", \$this->tmptpldir); ?>')", $template);


		$template = preg_replace("/\{else\}/is", "<? } else { ?>", $template);
		$template = preg_replace("/\{\/if\}/is", "<? } ?>", $template);
		$template = preg_replace("/\{\/for\}/is", "<? } ?>", $template);

		$template = preg_replace("/$const_regexp/", "<?=\\1?>", $template);

		$template = "<? if(!defined('IN_WP')) exit('Access Denied');?>\r\n$template";
        $template = preg_replace("/(\\\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i", "\\1'\\2']", $template);

        $template = $this->stripvtag($template);

		$fp = fopen($this->objfile, 'w');
		fwrite($fp, $template);
		fclose($fp);
	}

	function arrayindex($name, $items) {
		$items = preg_replace("/\[([a-zA-Z_]\w*)\]/is", "['\\1']", $items);
		return "<?=$name$items?>";
	}

	function stripvtag($s) {
		$vtag_regexp = "\<\?=(\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)\?\>";
		return preg_replace("/$vtag_regexp/is", "\\1", str_replace("\\\"", '"', $s));
	}

	function loopsection($arr, $k, $v, $statement) {
		$arr = $this->stripvtag($arr);
		$k = $this->stripvtag($k);
		$v = $this->stripvtag($v);
		$statement = str_replace("\\\"", '"', $statement);
		return $k ? "<? foreach((array)$arr as $k => $v) {?>$statement<?}?>" : "<? foreach((array)$arr as $v) {?>$statement<? } ?>";
	}

	function lang($k) {
		$subdir = ($pos = strpos($k, '/')) === false ? '' : substr($k, 0, $pos + 1);
		$k = $subdir ? substr($k, $pos + 1) : $k;
		@include DIR_LANG.'/default/'.$subdir.'tpl.php';
		return !empty($languages[$k]) ? $languages[$k] : "{ $k }";
	}

	function __destruct() {
		$content = ob_get_contents();
		if($this->base->config['urlrewrite']) {
			$rewrite_array = array(
				'search' => array(
					'<a href="index.php?m=reg">',
					'<a href="index.php?m=home">',
				),
				'replace' => array(
					'<a href="./reg">',
					'<a href="./home">',
				)
			);
			$content = str_replace($rewrite_array['search'], $rewrite_array['replace'], $content);
		}
		if($this->base->config['userdomain']['open'] && preg_match_all("/\<a href\=\"index\.php\?m\=blog&uid\=(\d+)\"\>/is", $content, $matches)) {
			$domains = $this->base->getDomains($matches[1]);
			$content = preg_replace("/\<a href\=\"index\.php\?m\=blog&uid\=(\d+)\"\>/e", "\$this->base->rewriteBlog('\\1', \$domains)", $content);
		}
		ob_end_clean();
		if(!empty($_REQUEST['inajax'])) {
			$content = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $content);
			$content = str_replace(array(chr(0), ']]>'), array(' ', ']]&gt;'), $content);
			@header("Expires: -1");
			@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
			@header("Pragma: no-cache");
			@header("Content-type: application/xml; charset=".CHARSET);
			echo '<?xml version="1.0" encoding="'.CHARSET.'"?><root><![CDATA['.$content.']]></root>';
        } elseif($this->stdout == 'display') {
			echo $content;
		}
	}

}

?>
