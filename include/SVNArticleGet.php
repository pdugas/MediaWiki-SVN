<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNArticleGet.php
# @brief    Article class for the SVN extension to get a file
# @author   Paul Dugas <paul@dugas.cc>
# =============================================================================

class SVNArticleGet extends SVNArticle
{
  function __construct($title, $url, $rev = NULL, $user = NULL, $pass = NULL)
    { parent::__construct($title, $url, $rev, $user, $pass); }

  function view()
    {
      $output = $this->getContext()->getOutput();

      $output->setHTMLTitle('SVN GET '.$this->getUrl());
      $output->setPageTitle('SVN GET '.$this->getUrl());
      $output->enableClientCache(false);

      $ls = SVNHooks::svn_info($this->getUrl());
      if ($ls['type'] == 'file') {
        $content = svn_cat(str_replace(' ','%20',$this->getUrl()));
        $output->disable();
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"{$ls['name']}\"");
        header("Content-Length: ".strlen($content));
        echo $content;
      } else {
        $output->addWikiText($this->getUrl().' doesn\'t point to a file!');
      }
    }

} // class SVNArticleGet

# =============================================================================
# vim: set et sw=4 ts=4 :
