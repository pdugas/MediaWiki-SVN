<?php
# =============================================================================
# MediaWiki-SVN - MediaWiki Extension for Integration with Subversion
# =============================================================================
# @file     SVN.i18n.php
# @brief    Internationalization for the extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# =============================================================================
 
if (!defined('MEDIAWIKI')) {
    echo("This is an extension to the MediaWiki package and ".
         "cannot be run standalone.\n");
    die(-1);
}

$messages = array();

$messages['en'] = array(
        'svn'      => 'SVN',
        'svn-desc' => 'Subversion Integration',
);

$messages['qqq'] = array(
        'svn' => '{{doc-special|SVN}}
MediaWiki-SVN is an extension that integrates with Subversion.',
        'svn-desc' => '{{desc}}',
);


$magicWords = array();

$magicWords['en'] = array(
        'svn' => array( 0, 'svn' ),
);

# =============================================================================
# vim: set et sw=4 ts=4 :
