<?php

namespace ExpressionEngine\Service\Form;

use ExpressionEngine\Service\Form\Traits\HasArrayableData;

class FieldSet
{
	use HasArrayableData;
	
	public array $fields = [];
	
	public function __construct($title = NULL)
	{
		$this->data = [
			'title' => $title,
		];
	}
	
	public function toArray()
	{
		return array_merge($this->data, [
			'fields' => array_map(function ($field) {
				return $field->toArray();
			}, $this->fields)
		]);
	}
	
	public function field($type, $key, $settings = [])
	{
		$fieldName = str_replace(' ', '', ucwords(str_replace('-', ' ', $type)));
		$fieldNamespace = 'ExpressionEngine\\Service\\Form\\Fields\\' . $fieldName;

		$field = new $fieldNamespace($settings);
		
		$this->fields[$key] = $field;
	
		return $field;
	}
	
	public function title($title)
	{
		return $this->setData('title', $title);
	}
	
	public function description($desc)
	{
		return $this->setData('desc', $desc);
	}
	
	public function descriptionContinued($desc)
	{
		return $this->setData('desc_cont', $desc);
	}
	
	public function example($example)
	{
		return $this->setData('example', $example);
	}
	
	public function columns($int)
	{
		return $this->setData('columns', $int);
	}
	
	public function hasSecurityLabel()
	{
		return $this->setData('security', TRUE);
	}
	
	public function hasCautionLabel()
	{
		return $this->setData('security', TRUE);
	}
	
	public function isGrid()
	{
		return $this->setData('grid', TRUE);
	}
	
	public function setAttrs($attrs = [])
	{
		return $this->setData('attrs', $attrs);
	}
	
	public function rows($int)
	{
		return $this->setData('rows', $int);
	}
	
	public function cols($int)
	{
		return $this->setData('cols', $int);
	}
	
	public function group($name)
	{
		return $this->setData('group', $name);
	}
}