<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNArticle.php
# @brief    Article class for the SVN extension
# @author   Paul Dugas <paul@dugas.cc>
# =============================================================================

class SVNArticle extends Article
{
  function __construct($title) 
    { parent::__construct($title); }

  function view()
    {
      $title = $this->getTitle()->getPartialURL();
      $output = $this->getContext()->getOutput();
      $output->setHTMLTitle($title);
      $output->setPageTitle($title);
      $output->enableClientCache(false);

      #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, '');
      #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, '');
      svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);

      $ls = SVNHooks::svn_info($title);
      if ($ls['type'] == 'dir') {
        $output->addWikiText("<svn depth=\"0\">$title</svn>");
      } else {
        $content = svn_cat($title);
        $output->disable();
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$file\"");
        header("Content-Length: ".strlen($content));
        echo $content;
      }
    }

} // class SVNArticle

# =============================================================================
# vim: set et sw=4 ts=4 :
