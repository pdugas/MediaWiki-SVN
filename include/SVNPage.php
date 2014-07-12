<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNPage.php
# @brief    Article/Page class for the SVN extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# =============================================================================

class SVNPage extends Article
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

      // Get the "svn ls" info for the given URL, not it's children
      $url = parse_url($title);
      if ($url === FALSE) {
        $output->addWikiText('Failed to parse URL.');
        return;
      }
      $file = basename($url['path']);
      $parent = $url; $parent['path'] = dirname($url['path']);
      $parent = self::_unparse_url($parent);
      $ls = svn_ls($parent); $ls = $ls[$file];

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

  private static function _unparse_url($parsed_url) 
    { 
      $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
      $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
      $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
      $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
      $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
      $pass     = ($user || $pass) ? "$pass@" : ''; 
      $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
      $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
      $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
      return "$scheme$user$pass$host$port$path$query$fragment"; 
    } 

} // class SVNPage

# =============================================================================
# vim: set et sw=4 ts=4 :
