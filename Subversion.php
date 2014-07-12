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

define("NS_SVN", 488);
define("NS_SVN_TALK", NS_SVN+1);
$wgExtraNamespaces[NS_SVN] = "SVN";
$wgExtraNamespaces[NS_SVN_TALK] = "SVN_talk";
$wgNamespaceProtection[NS_SVN] = array('editsvn');
$wgNamespaceProtection[NS_SVN_TALK] = array('editsvn');
//$wgGroupPermissions['sysop']['editsvn'] = true; // no edits


$wgExtensionCredits['parserhook'][] = array(
    'path'          => __FILE__,
    'name'          => 'Subversion',
    'author'        => array('[mailto:paul@dugas.cc Paul Dugas]'),
    'url'           => 'https://github.com/pdugas/MediaWiki-Subversion',
    'description'   => 'Adds <nowiki><svn/> tag and {{#svn}}</nowiki> '.
                       ' parser function for integration with Subversion.',
    'version'       => 0.1,
    'license-name'  => 'GPL',
);

$dir = dirname(__FILE__);
$inc = $dir.'/include';

$wgAutoloadClasses['SVNHooks'] = $inc.'/SVNHooks.php';
$wgAutoloadClasses['SVNPage'] = $inc.'/SVNPage.php';

$wgExtensionMessagesFiles['Subversion'] = $dir.'/Subversion.i18n.php';

$wgHooks['ArticleFromTitle'][] = 'SVNHooks::onArticleFromTitle';
$wgHooks['ParserFirstCallInit'][] = 'SVNHooks::onFirstCallInit';
$wgHooks['LinkBegin'][] = 'SVNHooks::onLinkBegin';

# =============================================================================
# vim: set et sw=2 ts=2 :
