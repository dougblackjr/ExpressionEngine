<?php

namespace ExpressionEngine\Service\Form\Traits;

trait HasArrayableData
{
	public array $data = [];
	
	public function setData($key, $val)
	{
		$this->data[$key] = $val;
		
		return $this;
	}
	
	public function setKeyValueTagAttribute($key, array|string $data)
	{
		$return = $data;
				
		if(is_array($data))
		{
			$return = '';
			$sep = NULL;
			
			foreach($data as $key => $val)
			{
				$return .= $sep . $key . '="'. $val .'"';
				$sep = ' ';
			}
		}
		
		return $this->setData($key, $return);
	}
}