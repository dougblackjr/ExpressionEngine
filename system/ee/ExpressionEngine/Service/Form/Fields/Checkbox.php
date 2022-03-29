<?php

namespace ExpressionEngine\Service\Form\Fields;

class Checkbox extends Select
{
	public string $type = 'checkbox';
	
	public function limit(int $limit)
	{
		return $this->setData('limit', $limit);
	}
	
	public function filterUrl(string $url)
	{
		return $this->setData('filter_url', $url);
	}
}