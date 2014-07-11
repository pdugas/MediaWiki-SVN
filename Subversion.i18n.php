<?php
# =============================================================================
# Subversion - MediaWiki Extension for Integration with Subversion
# =============================================================================
# @file     Subversion.i18n.php
# @brief    Internationalization for the extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# @version  $Id: $
# =============================================================================
 
if (!defined('MEDIAWIKI')) {
    echo("This is an extension to the MediaWiki package and ".
         "cannot be run standalone.\n");
    die(-1);
}

$magicWords = array();

$magicWords['en'] = array(
        'svn' => array( 0, 'svn' ),
);

# =============================================================================
# $LastChangedDate: $
# $LastChangedBy: $
# $URL: $
# $Revision: $
# vim: set et sw=4 ts=4 :
