<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNHook.php
# @brief    Hooks for the SVN extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# =============================================================================

class SVNHooks
{

  public static function onFirstCallInit(Parser $parser)
    {
      $parser->setHook('svn', 'SVNHooks::onTag');
      $parser->setFunctionHook('svn', 'SVNHooks::onFunc');
      return true;
    }

  public static function onArticleFromTitle($title, &$article) 
    {
      if ($title->getNamespace() == NS_SVN) 
        { $article = new SVNPage($title); }
      return true;
    }

  public static function onFunc(Parser $parser, $input=null)
    {
      $parser->disableCache();

      if (empty($input)) { error_log('missing PATH'); return '(missing PATH)'; }

      #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, '');
      #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, '');
      svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);

      $ret = "{| class=\"wikitable\" width=\"100%\"\n".
             "! Path\n".
             "! Rev\n".
             "! Author\n".
             "! Size\n".
             "! Timestamp\n";
      self::_tableRow($input, $ret);
      $ret .= "|}\n";

      return $ret;
    }

  public static function onTag($input, array $args, 
                               Parser $parser, PPFrame $frame)
    {
      return $parser->recursiveTagParse(self::onFunc($parser, $input));
    }

  public static function onLinkBegin($dummy, $target, &$html, &$customAttribs,
                                     &$query, &$options, &$ret) 
    {
      if ($target->getNamespace() == NS_SVN) {
        $options[] = 'known';
        $options[] = 'noclasses';
      }
      return true;
    }

  private static function _tableRow($path, &$out, $maxDepth=-1, $depth=0)
    {
      global $wgExtraNamespaces;
      foreach (svn_ls($path) as $ls) {
        $out .= "|-\n".
                "|".str_repeat('&nbsp;', $depth*2).
                  "[[{$wgExtraNamespaces[NS_SVN]}:$path/{$ls['name']}|{$ls['name']}]]\n".
                "|style=\"text-align:center;\"|{$ls['created_rev']}\n".
                "|style=\"text-align:center;\"|{$ls['last_author']}\n".
                "|style=\"text-align:right;\"|{$ls['size']}\n".
                "|style=\"text-align:center;\"|{$ls['time']}\n";
        if ($ls['type'] == 'dir') { 
          self::_tableRow("$path/{$ls['name']}", $out , $maxDepth, $depth+1);
        }
      }
    }

} // class SVNHooks

# =============================================================================
# vim: set et sw=4 ts=4 :
