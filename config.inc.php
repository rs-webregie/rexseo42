<?php
/**
 * RexSEO - URLRewriter Addon
 *
 * @link https://github.com/gn2netwerk/rexseo
 *
 * @author dh[at]gn2-netwerk[dot]de Dave Holloway
 * @author code[at]rexdev[dot]de jdlx
 *
 * Based on url_rewrite Addon by
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 1.5.0
 */

// ADDON PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself = 'rexseo42';
$myroot = $REX['INCLUDE_PATH'].'/addons/'.$myself;

$REX['ADDON'][$myself]['VERSION'] = array
(
'VERSION'      => 1,
'MINORVERSION' => 5,
'SUBVERSION'   => 0,
);

$REX['ADDON']['rxid'][$myself]        = '0';
$REX['ADDON']['name'][$myself]        = 'rexseo42';
$REX['ADDON']['version'][$myself]     = '1.0.1';
$REX['ADDON']['author'][$myself]      = 'Markus Staab, Wolfgang Huttegger, Dave Holloway, Jan Kristinus, jdlx, RexDude';
$REX['ADDON']['supportpage'][$myself] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['PERM'][]                        = $myself.'[]';
$REX['ADDON'][$myself]['SUBPAGES']    = array (
  array ('',          'Einstellungen'),
  array ('setup',      'Setup'),
  array ('help',      'Hilfe')
  );
$REX['ADDON'][$myself]['debug_log']   = 0;
$REX['ADDON'][$myself]['settings']['default_redirect_expire'] = 60;
$REX['PROTOCOL'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';


// INCLUDES
////////////////////////////////////////////////////////////////////////////////
require_once($myroot . '/functions/function.rexseo_helpers.inc.php');
require_once($myroot . '/classes/class.rexseo42.inc.php');

// USER SETTINGS
////////////////////////////////////////////////////////////////////////////////
require_once($myroot . '/settings.inc.php');

// RUN ON ADDONS INLCUDED
////////////////////////////////////////////////////////////////////////////////
if(!$REX['SETUP']){
  rex_register_extension('ADDONS_INCLUDED','rexseo_init');
}

if(!function_exists('rexseo_init')){
  function rexseo_init($params)
  {
    global $REX;

    if ($REX['MOD_REWRITE'] !== false)
    {
      // REWRITE
      $levenshtein    = (bool) $REX['ADDON']['rexseo42']['settings']['levenshtein'];
      $rewrite_params = (bool) $REX['ADDON']['rexseo42']['settings']['rewrite_params'];

      require_once $REX['INCLUDE_PATH'].'/addons/rexseo42/classes/class.rexseo_rewrite.inc.php';

      $rewriter = new RexseoRewrite($levenshtein,$rewrite_params);
      $rewriter->resolve();

      rex_register_extension('URL_REWRITE', array ($rewriter, 'rewrite'));
    }

    // CONTROLLER
    include $REX['INCLUDE_PATH'].'/addons/rexseo42/controller.inc.php';

    // REXSEO POST INIT
    rex_register_extension_point('REXSEO_POST_INIT');

  }
}

// SEOPAGE
////////////////////////////////////////////////////////////////////////////////

if ($REX['REDAXO']) {
	// add new menu item
	rex_register_extension('PAGE_CONTENT_MENU', function ($params) {
		$class = "";

		if ($params['mode']  == 'seo') {
			$class = 'class="rex-active"';
		}

		$seoLink = '<a '.$class.' href="index.php?page=content&amp;article_id=' . $params['article_id'] . '&amp;mode=seo&amp;clang=' . $params['clang'] . '&amp;ctype=' . rex_request('ctype') . '">SEO</a>';
		array_splice($params['subject'], '-2', '-2', $seoLink);

		return $params['subject'];
	});

	// include seo page
	rex_register_extension('PAGE_CONTENT_OUTPUT', function ($params) {
		global $REX, $I18N;

		if ($params['mode']  == 'seo') {
			include($REX['INCLUDE_PATH'] . '/addons/rexseo42/pages/seopage.inc.php');
		}
	});
}

?>
