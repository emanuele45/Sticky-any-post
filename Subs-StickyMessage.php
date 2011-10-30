<?php
/**
 * Sticky any Post (SaP)
 *
 * @package SaP
 * @author emanuele
 * @copyright 2011 emanuele, Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 0.1.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function stickymessage_add_action(&$actionArray)
{
	$actionArray['stickymessage'] = array('Subs-StickyMessage.php', 'make_message_sticky');
}

function posting_message_sticky($msgID, $topicID)
{
	if(!isset($msgID) || !isset($topicID))
		return false;

	$_REQUEST['msg'] = (int) $msgID;
	$_REQUEST['topic'] = (int) $topicID;

	make_message_sticky(false);
}

function make_message_sticky($redirect = true)
{
	global $smcFunc, $topic, $modSettings;

	if($redirect)
		checkSession('get');
	$_REQUEST['msg'] = (int) $_REQUEST['msg'];

	// Is $topic set?
	if (empty($topic) && isset($_REQUEST['topic']))
		$topic = (int) $_REQUEST['topic'];

	$request = $smcFunc['db_query']('', '
		SELECT sticky_message
		FROM {db_prefix}topics
		WHERE id_topic = {int:topic_id}
		LIMIT 1',
		array(
			'topic_id' => $topic,
	));
	list($stikyed) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	$stikyed = !empty($stikyed) ? unserialize($stikyed) : array('id_msg' => 0, 'id_member' => 0);

	if(!empty($_POST['sticky_message']))
		$msg = $_REQUEST['msg'];
	elseif(empty($_POST['sticky_message']) && !$redirect)
		$msg = $stikyed['id_msg'];
	else
		$msg = ($stikyed['id_msg'] == $_REQUEST['msg']) ? 0 : $_REQUEST['msg'];
	$memberID = 0;
	$sticky_msg = '';

	if(!allowedTo('make_sticky') || empty($modSettings['enable_sticky_message']))
		$msg = $stikyed['id_msg'];

	if(!empty($msg))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}messages
			WHERE id_msg = {int:message_id}
			LIMIT 1',
			array(
				'message_id' =>$msg,
		));
		list($memberID) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		$sticky_msg = serialize(array('id_msg' => $msg, 'id_member' => $memberID));
	}

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}topics
		SET sticky_message = {string:sticky_message}
		WHERE id_topic = {int:id_topic}',
		array(
			'id_topic' => $topic,
			'sticky_message' => $sticky_msg,
	));

	if($redirect)
		redirectexit('topic=' . $topic . '.' . $_REQUEST['start']);
}

?>