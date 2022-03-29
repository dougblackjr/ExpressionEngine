<?php
/**
 * This source file is part of the open source project
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2021, Packet Tide, LLC (https://www.packettide.com)
 * @license   https://expressionengine.com/license Licensed under Apache License, Version 2.0
 */

namespace ExpressionEngine\Service\Form;

use ExpressionEngine\Service\Form\Traits\HasArrayableData;

class Field
{
	use HasArrayableData;
	
	public string $type = 'text';
	
	public function toArray()
	{
		return $this->data;
	}
	
	public function __construct($settings = [])
	{		
		$defaultSettings = [
			'type' => $this->type
		];
		
		$this->data = array_merge($defaultSettings, $settings);
	}
	
	public function addTopMargin()
	{
		return $this->setData('margin_top', TRUE);
	}
	
	public function addLeftMargin()
	{
		return $this->setData('margin_left', TRUE);
	}
	
	public function note(string $text)
	{
		return $this->setData('has_note', $text);
	}
	
	public function name(string $name)
	{
		return $this->setData('name', $name);
	}
	
	public function value(string $value)
	{
		return $this->setData('value', $value);
	}
	
	public function attributes(array|string $data)
	{
		return $this->setKeyValueTagAttribute('attrs', $data);
	}
	
	public function class(string $classes)
	{
		return $this->setData('class', $classes);
	}
	
	public function toggleGroup(array $groups)
	{
		return $this->setData('group_toggle', $groups);
	}
	
	public function group(string $group)
	{
		return $this->setData('group', $group);
	}
	
	public function isDisabled(bool $disabled = TRUE)
	{
		return $this->setData('disabled', $disabled);
	}
	
	public function isRequired(bool $required = TRUE)
	{
		return $this->setData('required', $required);
	}
}