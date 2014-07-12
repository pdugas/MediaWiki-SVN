<?php
# =============================================================================
# SVN - MediaWiki Extension for Subversion Integration
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/SVNHook.php
# @brief    Hooks for the SVN extension
# @author   Paul Dugas <paul@dugas.cc>
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
        { $article = new SVNArticle($title); }
      return true;
    }

  public static function onFunc(Parser $parser, $url=null, $rev=null)
    {
      global $wgExtraNamespaces;
      $parser->disableCache();

      if (empty($url)) { error_log('missing URL'); return '(missing URL)'; }

      #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, '');
      #svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, '');
      svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
      svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);

      $info = self::svn_info($url, $rev, $parent);
      $dotdot = self::svn_info($parent, $rev);
      if (!$info) { return $url; }

      if ($info['type'] == 'dir') {
        $ret = "{| class=\"wikitable\" width=\"100%\"\n".
               "! Path\n".
               "! Rev\n".
               "! Author\n".
               "! Size\n".
               "! Timestamp\n".
               "|-\n".
               "|[[{$wgExtraNamespaces[NS_SVN]}:$url|.]]\n".
               "|style=\"text-align:center;\"|{$info['created_rev']}\n".
               "|style=\"text-align:center;\"|{$info['last_author']}\n".
               "|style=\"text-align:right;\"|{$info['size']}\n".
               "|style=\"text-align:center;\"|{$info['time']}\n".
               "|-\n".
               "|[[{$wgExtraNamespaces[NS_SVN]}:$parent|..]]\n".
               "|style=\"text-align:center;\"|{$dotdot['created_rev']}\n".
               "|style=\"text-align:center;\"|{$dotdot['last_author']}\n".
               "|style=\"text-align:right;\"|{$dotdot['size']}\n".
               "|style=\"text-align:center;\"|{$dotdot['time']}\n";
        foreach (svn_ls($url, $rev) as $ls) {
          $ret .= "|-\n".
                  "|[[{$wgExtraNamespaces[NS_SVN]}:$url/{$ls['name']}|{$ls['name']}]]\n".
                  "|style=\"text-align:center;\"|{$ls['created_rev']}\n".
                  "|style=\"text-align:center;\"|{$ls['last_author']}\n".
                  "|style=\"text-align:right;\"|{$ls['size']}\n".
                  "|style=\"text-align:center;\"|{$ls['time']}\n";
        }
        $ret .= "|}\n";
      } else {
        $ret = "(file block for $url goes here...)";
      }

      return $ret;
    }

  public static function onTag($input, array $args, 
                               Parser $parser, PPFrame $frame)
    {
      $rev = array_key_exists('rev', $args) ? $args['rev'] : NULL;
      return $parser->recursiveTagParse(self::onFunc($parser, $input, $rev));
    }

  public static function onLinkBegin($dummy, $target, &$html, &$customAttribs,
                                     &$query, &$options, &$ret) 
    {
      if ($target->getNamespace() == NS_SVN) {
        if (self::svn_info($target->getPartialURL())) {
          $options[] = 'known';
          $options[] = 'noclasses';
        }
      }
      return true;
    }

  public static function svn_info($title, $rev=null, &$parent=null)
    {
      $url = parse_url($title);
      if ($url === FALSE) { return NULL; }
      $file = basename($url['path']);
      $parent = $url; $parent['path'] = dirname($url['path']);
      $parent = self::unparse_url($parent);
      $ls = svn_ls($parent); 
      return (array_key_exists($file, $ls) ? $ls[$file] : NULL);
    }

  public static function unparse_url($parsed_url)
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

} // class SVNHooks

# =============================================================================
# vim: set et sw=4 ts=4 :
