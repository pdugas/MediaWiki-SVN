<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNArticle.php
# @brief    Article class for the SVN extension
# @author   Paul Dugas <paul@dugas.cc>
# =============================================================================

/** 
 * Abstract class for SVN articles.
 */
class SVNArticle extends Article
{
  private $url;
  private $rev;
  private $user;
  private $pass;

  protected function getUrl() { return $this->url; }
  protected function getRev() { return $this->rev; }
  protected function getUser() { return $this->user; }
  protected function getPass() { return $this->pass; }

  function __construct($title, $url, $rev = NULL, $user = NULL, $pass = NULL) 
    { 
      parent::__construct($title); 
      $this->url = $url; 
      $this->rev = $rev; 
      $this->user = $user; 
      $this->pass = $pass; 

      if ($user) 
        { svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $user); }
      if ($pass) 
        { svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $pass); }
      svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
    }

  /** 
   * Factory for SVNArticle objects.
   * 
   * Returns an SVNArticle object based on the requested MediaWik Title.
   * Returns NULL if the Title wasn't recognize.
   */
  public static function make($title)
    {
      if ($title->getNamespace() != NS_SVN) { return NULL; }

      $dbKey = $title->getDBKey();

      if (strpos($dbKey, '?') === FALSE) { return NULL; }
      list($action, $querystr) = explode('?', $dbKey, 2);
      $action = strtolower($action);
      parse_str($querystr, $query);

      if (!array_key_exists('URL', $query)) { return NULL; }
      $url = $query['URL'];
      $rev = NULL;
      $user = NULL;
      $pass = NULL;
      if (array_key_exists('REV', $query)) { $rev = $query['REV']; }
      if (array_key_exists('USER', $query)) { $user = $query['USER']; }
      if (array_key_exists('PASS', $query)) { $pass = $query['PASS']; }

      switch ($action) {
        case 'ls':    
          return new SVNArticleLs($title, $url, $rev, $user, $pass);
        case 'get':
          return new SVNArticleGet($title, $url, $rev, $user, $pass);
        #case 'log':
        #  return new SVNArticleLog($title, $url, $rev, $user, $pass);
        #case 'blame':
        #  return new SVNArticleBlame($title, $url, $rev, $user, $pass);
        default: 
          error_log("Unknown ACTION ($action)"); return NULL;
      }
    }

} // class SVNArticle

# =============================================================================
# vim: set et sw=4 ts=4 :
