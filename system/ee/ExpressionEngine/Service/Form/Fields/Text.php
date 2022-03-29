<?php

namespace ExpressionEngine\Service\Form\Fields;

use ExpressionEngine\Service\Form\Field;

class Text extends Field
{
	public string $type = 'text';
	
	public function placeholder(string $placeholder)
	{
		return $this->setData('placeholder', $placeholder);
	}
	
	public function maxlength(int $maxlength)
	{
		return $this->setData('maxlength', $maxlength);
	}
}