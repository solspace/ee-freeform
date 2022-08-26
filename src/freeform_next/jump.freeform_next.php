<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

use ExpressionEngine\Service\JumpMenu\AbstractJumpMenu;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Model\FormModel;

class Freeform_next_jump extends AbstractJumpMenu
{
	protected static $items = [
		'forms' => [
			'icon' => 'fa-eye',
			'command' => 'view forms',
			'command_title' => 'view_forms',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'forms',
		],
		'form' => [
			'icon' => 'fa-search',
			'command' => 'view form',
			'command_title' => 'view_form',
			'dynamic' => true,
			'requires_keyword' => false,
			'target' => 'searchForms',
		],
		'submissions' => [
			'icon' => 'fa-search',
			'command' => 'view submission',
			'command_title' => 'view_submissions_in',
			'dynamic' => true,
			'requires_keyword' => false,
			'target' => 'submissionsByForm',
		],
		'fields' => [
			'icon' => 'fa-eye',
			'command' => 'view fields',
			'command_title' => 'view_fields',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'fields'
		],
		'field' => [
			'icon' => 'fa-search',
			'command' => 'view fields',
			'command_title' => 'view_field',
			'dynamic' => true,
			'requires_keyword' => false,
			'target' => 'searchFields'
		],
		'notifications' => [
			'icon' => 'fa-bullhorn',
			'command' => 'view notifications',
			'command_title' => 'view_notifications',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'notifications'
		],
		'general' => [
			'icon' => 'fa-wrench',
			'command' => 'general settings',
			'command_title' => 'general',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/general'
		],
		'spam_protection' => [
			'icon' => 'fa-wrench',
			'command' => 'spam protection',
			'command_title' => 'spam_protection',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/spam_protection'
		],
		'permissions' => [
			'icon' => 'fa-wrench',
			'command' => 'permissions',
			'command_title' => 'permissions',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/permissions'
		],
		'formatting_templates' => [
			'icon' => 'fa-wrench',
			'command' => 'formatting templates',
			'command_title' => 'formatting_templates',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/formatting_templates'
		],
		'email_templates' => [
			'icon' => 'fa-wrench',
			'command' => 'email templates',
			'command_title' => 'email_templates',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/email_templates'
		],
		'statuses' => [
			'icon' => 'fa-wrench',
			'command' => 'statuses',
			'command_title' => 'statuses',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/statuses'
		],
		'recaptcha' => [
			'icon' => 'fa-wrench',
			'command' => 'recaptcha',
			'command_title' => 'recaptcha',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings/recaptcha'
		],
		'mailing_lists' => [
			'icon' => 'fa-cogs',
			'command' => 'mailing lists',
			'command_title' => 'mailing_lists',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'integrations/mailing_lists'
		],
		'crm' => [
			'icon' => 'fa-cogs',
			'command' => 'crm',
			'command_title' => 'crm',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'integrations/crm'
		],
	];

	/**
	 * @param array $searchKeywords
	 *
	 * @return array
	 */
	public function searchForms($searchKeywords = [])
	{
		$items = [];

		$spacedKeywordsString = ee()->db->escape_str(ee()->security->xss_clean(implode(' ', $searchKeywords)));
		$underscoredKeywordsString = ee()->db->escape_str(ee()->security->xss_clean(implode(' ', $searchKeywords)));

		$forms = ee('Model')
			->get(FormModel::MODEL)
			->filter('handle', 'LIKE', '%' . $spacedKeywordsString . '%')
			->orFilter('handle', 'LIKE', '%' . $underscoredKeywordsString . '%')
			->orFilter('name', 'LIKE', '%' . $spacedKeywordsString . '%')
			->orFilter('name', 'LIKE', '%' . $underscoredKeywordsString . '%')
			->all();

		foreach($forms as $form)
		{
			$items['form_' . $form->id] = array(
				'icon' => 'fa-pencil-alt',
				'command' => $form->name,
				'command_title' => $form->name,
				'command_context' => '',
				'dynamic' => false,
				'requires_keyword' => false,
				'target' => 'forms/'.$form->id
			);
		}

		return $items;
	}

	/**
	 * @param array $searchKeywords
	 *
	 * @return array
	 */
	public function searchFields($searchKeywords = [])
	{
		$items = [];

		$spacedKeywordsString = ee()->db->escape_str(ee()->security->xss_clean(implode(' ', $searchKeywords)));
		$underscoredKeywordsString = ee()->db->escape_str(ee()->security->xss_clean(implode(' ', $searchKeywords)));

		$fields = ee('Model')
			->get(FieldModel::MODEL)
			->filter('handle', 'LIKE', '%' . $spacedKeywordsString . '%')
			->orFilter('handle', 'LIKE', '%' . $underscoredKeywordsString . '%')
			->orFilter('label', 'LIKE', '%' . $spacedKeywordsString . '%')
			->orFilter('label', 'LIKE', '%' . $underscoredKeywordsString . '%')
			->all();

		foreach($fields as $field)
		{
			$items['form_' . $field->id] = array(
				'icon' => 'fa-eye',
				'command' => $field->label,
				'command_title' => $field->label,
				'command_context' => $field->handle,
				'dynamic' => false,
				'requires_keyword' => false,
				'target' => 'fields/'.$field->id
			);
		}

		return $items;
	}

	/**
	 * @param array $searchKeywords
	 *
	 * @return array
	 */
	public function submissionsByForm($searchKeywords = [])
	{
		$items = [];

		$spacedKeywordsString = ee()->db->escape_str(ee()->security->xss_clean(implode(' ', $searchKeywords)));
		$underscoredKeywordsString = ee()->db->escape_str(ee()->security->xss_clean(implode(' ', $searchKeywords)));

		$forms = ee('Model')
			->get(FormModel::MODEL)
			->filter('handle', 'LIKE', '%' . $spacedKeywordsString . '%')
			->orFilter('handle', 'LIKE', '%' . $underscoredKeywordsString . '%')
			->all();

		foreach($forms as $form)
		{
			$items['form_' . $form->id] = array(
				'icon' => 'fa-pencil-alt',
				'command' => $form->name,
				'command_title' => $form->name,
				'command_context' => '',
				'dynamic' => false,
				'requires_keyword' => false,
				'target' => 'submissions/'.$form->handle
			);
		}

		return $items;
	}
}