<?php

namespace ExpressionEngine\Service\Form;

class Section
{
	public $fieldsets = [];
	
	public function fieldset($title)
	{
		$fieldset = new FieldSet($title);
				
		$this->fieldsets[] = $fieldset;
	
		return $fieldset;
	}
	
	public function toArray()
	{
		return array_map(function($fieldset) {
			return $fieldset->toArray();
		}, $this->fieldsets);
	}
}