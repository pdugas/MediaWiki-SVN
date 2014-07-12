<?php
# =============================================================================
# Subversion - MediaWiki Extension for Integration with Subversion
# =============================================================================
# @file     Subversion.i18n.php
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
        'subversion'      => 'Subversion',
        'subversion-desc' => 'Subversion Integration',
);

$messages['qqq'] = array(
        'subversion' => '{{doc-special|Subversion}}
Subversion is an extension that integrates with Subversion.',
        'subversion-desc' => '{{desc}}',
);


$magicWords = array();

$magicWords['en'] = array(
        'svn' => array( 0, 'svn' ),
);

# =============================================================================
# vim: set et sw=4 ts=4 :
