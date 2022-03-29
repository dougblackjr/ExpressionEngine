<?php

namespace ExpressionEngine\Service\Form\Traits;

use ExpressionEngine\Service\Form\Section;

trait HasSections
{
	public ?array $sections = [];
		
	public function section($heading = NULL)
	{
		$section = new Section();
				
		if(isset($heading))
		{
			$this->sections[$heading] = $section;
			
			return $section;
		}
		
		$this->sections[] = $section;
		
		return $section;
	}
}