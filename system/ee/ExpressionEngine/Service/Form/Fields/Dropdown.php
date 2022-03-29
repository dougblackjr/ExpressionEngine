<?php

namespace ExpressionEngine\Service\Form\Fields;

class Dropdown extends Select
{
	public string $type = 'dropdown';
	
	public function __construct($settings = [])
	{		
		parent::__construct([
			'values' => []
		]);
	}
	
	public function limit(int $limit)
	{
		return $this->setData('limit', $limit);
	}
	
	public function filterUrl(string $url)
	{
		return $this->setData('filter_url', $url);
	}
	
	public function emptyText(string $text)
	{
		return $this->setData('empty_text', $text);
	}
}