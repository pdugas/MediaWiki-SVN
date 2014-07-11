<?php
# =============================================================================
# Subversion - MediaWiki Extension for Integration with Subversion
# =============================================================================
# @file     Subversion.php
# @brief    Setup for the extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# =============================================================================
 
if (!defined('MEDIAWIKI')) {
    echo("This is an extension to the MediaWiki package and ".
         "cannot be run standalone.\n");
    die(-1);
}

$wgExtensionCredits['parserhook'][] = array(
    'path'          => __FILE__,
    'name'          => 'Subversion',
    'author'        => array('[mailto:paul@dugas.cc Paul Dugas]'),
    'url'           => 'https://www.mediawiki.org/wiki/Extension:Subversion',
    'description'   => 'Adds <nowiki><svn/> tag and {{#svn}}</nowiki> '.
                       ' parser function for integration with Subversion.',
    'version'       => 0.1,
    'license-name'  => 'GPL',
);

$wgHooks['ParserFirstCallInit'][] = 'SubversionParserInit';
$wgExtensionMessagesFiles['Subversion'] = __DIR__ . '/Subversion.i18n.php';

function SubversionParserInit(Parser $parser)
{
    $parser->setHook('svn', 'SubversionTag');
    $parser->setFunctionHook('svn', 'SubversionFunc');
    return true;
}

function SubversionTag($input, array $args, Parser $parser, PPFrame $frame)
{
  return $parser->recursiveTagParse(SubversionFunc($parser, $input));
}

function SubversionFunc($parser, $input = '')
{
  $parser->disableCache();

  $ret = "{| class=\"wikitable\" width=\"100%\"\n".
         "! Path\n".
         "! Rev\n".
         "! Author\n".
         "! Size\n".
         "! Timestamp\n";
  SubversionSubFunc($input, $ret);
  $ret .= "|}\n";
  return $ret;
}

function SubversionSubFunc($path, &$out, $maxDepth=-1, $depth=0) 
{
  #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, '');
  #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, '');
  svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
  svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
  svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);

  foreach (svn_ls($path) as $ent) {
    $out .= "|-\n".
            "|".str_repeat('&nbsp;', $depth*2)."[$path/{$ent['name']} {$ent['name']}]\n".
            "|style=\"text-align:center;\"|{$ent['created_rev']}\n".
            "|style=\"text-align:center;\"|{$ent['last_author']}\n".
            "|style=\"text-align:right;\"|{$ent['size']}\n".
            "|style=\"text-align:center;\"|{$ent['time']}\n";
    if ($ent['type'] == 'dir') { 
      SubversionSubFunc("$path/{$ent['name']}", $out , $maxDepth, $depth+1);
    }
  }
}

# =============================================================================
# vim: set et sw=2 ts=2 :
