<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * @package		Pago Shipping Plugin
 * @author 		'corePHP' LLC.
 * @copyright 	(C) 2010- 'corePHP' LLC.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Support: http://support.corephp.com/
 */



class plgPago_securityAkismet extends JPlugin
{

	public function __construct($subject, $plugin)
	{
		parent::__construct($subject, $plugin);

		//KDispatcher::add_filter('is_spam', array($this, 'set_options'));
		//KDispatcher::add_filter('generate_link', array($this, 'generate_link'));
	}

	public function is_spam($name, $email, $url, $comment)
	{
		require_once (dirname ( __FILE__ ) . '/akismet.class.php');
		
		$akismet = new Akismet('http://54.232.83.11', $this->params->get('api_key'));
		$akismet->setCommentAuthor($name);
		$akismet->setCommentAuthorEmail($email);
		$akismet->setCommentAuthorURL($url);
		$akismet->setCommentContent($comment);
		$akismet->setPermalink('http://www.example.com/blog/alex/someurl/');
		
		return $akismet->isCommentSpam();
	}
}
