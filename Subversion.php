<?php
# =============================================================================
# Subversion - MediaWiki Extension for Integration with Subversion
# =============================================================================
# @file     Subversion.php
# @brief    Setup for the extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# @version  $Id: $
# =============================================================================
 
if (!defined('MEDIAWIKI')) {
    echo("This is an extension to the MediaWiki package and ".
         "cannot be run standalone.\n");
    die(-1);
}

$wgSubversionURL = '/repos';  // (without trailing slash please)
$wgSubversionUser = NULL;
$wgSubversionPass = NULL;

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
  $id = $args['id'];
  if (!is_numeric($id)) { $id = $input; }
  if (!is_numeric($id)) { return ''; }
  return $parser->recursiveTagParse(SubversionFunc($parser, $id, $input));
}

function SubversionFunc($parser, $id = '', $text = '')
{
  global $wgSubversionURL;
  global $wgSubversionUser;
  global $wgSubversionPass;

  $parser->disableCache();

  if (!is_numeric($id)) { return ''; }
  $id = intval($id);

  $url = sprintf('%s/view.php?id=%d', $wgSubversionURL, $id);

  $issue = null;
  if (!is_null($wgSubversionUser) && !is_null($wgSubversionPass)) {
    try {
      $soap = new SoapClient($wgSubversionURL.'/api/soap/mantisconnect.php?wsdl');
      $issue = $soap->mc_issue_get($wgSubversionUser, $wgSubversionPass, $id);
    } catch (Exception $e) { /* ignored */ }
  }
  
  if (empty($text)) {
    $ret = sprintf('[%s Issue-%d "%s" (%s)]', $url, $id, 
                   $issue->summary, $issue->status->name);
    if ($issue->status->name == 'resolved' || 
        $issue->status->name == 'closed') {
      $ret = "<strike>$ret</strike>";
    }
  } else {
    $ret = "[$url $text]";
  }  
  return $ret;
}

# =============================================================================
# $LastChangedDate: $
# $LastChangedBy: $
# $URL: $
# $Revision: $
# vim: set et sw=2 ts=2 :
