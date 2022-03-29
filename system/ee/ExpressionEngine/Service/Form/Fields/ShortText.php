<?php

namespace ExpressionEngine\Service\Form\Fields;

class ShortText extends Text
{
	public string $type = 'short-text';
	
	public function label(string $label)
	{
		return $this->setData('label', $label);
	}
}