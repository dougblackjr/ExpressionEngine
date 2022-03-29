<?php

namespace ExpressionEngine\Service\Form\Fields;

use ExpressionEngine\Service\Form\Field;

class Textarea extends Text
{
	public string $type = 'textarea';
	
	public function cols(int $cols)
	{
		return $this->setData('cols', $cols);
	}
	
	public function rows(int $rows)
	{
		return $this->setData('rows', $rows);
	}
	
	public function killPipes(bool $killPipes = TRUE)
	{
		return $this->setData('kill_pipes', $killPipes);
	}
}