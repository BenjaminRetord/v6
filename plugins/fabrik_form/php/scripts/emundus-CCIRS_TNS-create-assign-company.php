<?php
defined('_JEXEC') or die();
/**
 * @version 6.3.4: emundus-CCIRS-create-assign-comany.php 89 2019-12-26 Hugo Moracchini
 * @package Fabrik
 * @copyright Copyright (C) 2018 eMundus. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * @description Create a company and asssign 
 */

$db = JFactory::getDBO();
$query = $db->getQuery(true);

jimport('joomla.log.log');
JLog::addLogger(array('text_file' => 'com_emundus.createassigncompany.php'), JLog::ALL, array('com_emundus'));


if ($fabrikFormData['vous_etes'] != 3)
	return false;


// Get the siret for the company, this is used as a primary key to find it.
$siret = $fabrikFormData['siret_raw'];

$user_id = $fabrikFormData['user'];

// Using the institution IDs we can get the groups attached to it.
$query->select($db->quoteName('id'))
	->from($db->quoteName('#__emundus_entreprise'))
	->where($db->quoteName('siret').' LIKE '.$db->quote($siret));

try {

    $db->setQuery($query);
    $company_id = $db->loadResult();

} catch (Exception $e) {
    JLog::add('Error in script/CCIRS-create-assign-company getting company by siret at query: '.$query->__toString(), JLog::ERROR, 'com_emundus');
}

// TODO: Check all column names.
// TODO: Add more columns, because there probably are more.
// If the company wasn't found, make a new one.
if (!empty($company_id)) {

	$query->clear()
		->insert($db->quoteName('#__emundus_entreprise'))
		->columns($db->quoteName(['siret', 'raison_sociale']))
		->values($db->quote($siret).', '.$db->quote($fabrikFormData['raison_sociale']));

	try {

		$db->setQuery($query);
		$db->execute();
		$company_id = $db->insertid();

	} catch (Exception $e) {
		JLog::add('Error in script/CCIRS-create-assign-company inserting company at query: '.$query->__toString(), JLog::ERROR, 'com_emundus');
	}

}

// Check if the user is already assigned to the company in question.
$query->clear()
	->select($db->quoteName('id'))
	->from($db->quoteName('#__emundus_user_entreprise'))
	->where($db->quoteName('cid').' = '.$company_id.' AND '.$db->quoteName('uid').' = '.$user_id);

try {

	$db->setQuery($query);
	$link = $db->loadResult();

} catch (Exception $e) {
	JLog::add('Error in script/CCIRS-create-assign-company getting company/user link: '.$query->__toString(), JLog::ERROR, 'com_emundus');
}

// If there is no link between the user and the company, make him DRH.
// If a link already exists, it's up to the DRH to handle it.
if (empty($link)) {

	$query->clear()
		->insert($db->quoteName('#__emundus_user_entreprise'))
		->columns($db->quoteName(['cid', 'uid', 'profile']))
		->values($db->quote($company_id).', '.$db->quote($user_id).', '.$db->quote('1002'));

	try {

		$db->setQuery($query);
		$db->execute();

	} catch (Exception $e) {
		JLog::add('Error in script/CCIRS-create-assign-company inserting company at query: '.$query->__toString(), JLog::ERROR, 'com_emundus');
	}

}
