<?php

namespace ExpressionEngine\Service\Form\Fields;

use ExpressionEngine\Service\Form\Field;

class Select extends Field
{
	public string $type = 'select';
	
	public function __construct($settings = [])
	{		
		parent::__construct([
			'choices' => []
		]);
	}
	
	public function encode(bool $encode = TRUE)
	{
		return $this->setData('encode', $encode);
	}
	
	public function choice(string $value, string $label)
	{
		return $this->options([$value => $label]);
	}
	
	public function options(array $options = [])
	{
		$this->data['choices'] = array_merge($this->data['choices'], $options);
		
		return $this;
	}
	
	public function optionGroup(string $label, array $options = [])
	{
		$this->data['choices'][$label] = $options;
		
		return $this;
	}
	
	public function noResults(string $text, ?string $link = NULL, ?string $linkText = NULL)
	{
		return $this->setData('no_results', [
			'text' => $text,
			'link_href' => $link,
			'link_text' => $linkText,
		]);
	}
}