<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNArticleLs.php
# @brief    Article class for the SVN extension to list directory contents
# @author   Paul Dugas <paul@dugas.cc>
# =============================================================================

class SVNArticleLs extends SVNArticle
{
  function __construct($title, $url, $rev = NULL, $user = NULL, $pass = NULL)
    { parent::__construct($title, $url, $rev, $user, $pass); }

  function view()
    {
      $output = $this->getContext()->getOutput();

      $output->setHTMLTitle('SVN Listing '.$this->getUrl());
      $output->setPageTitle('SVN Listing '.$this->getUrl());
      $output->enableClientCache(false);

      $output->addWikiText("<svn depth=\"0\">".$this->getUrl()."</svn>");
    }

} // class SVNArticleLs

# =============================================================================
# vim: set et sw=4 ts=4 :
