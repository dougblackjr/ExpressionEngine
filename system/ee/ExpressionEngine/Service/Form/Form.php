<?php

namespace ExpressionEngine\Service\Form;

use ExpressionEngine\Service\Form\Traits\HasArrayableData;
use ExpressionEngine\Service\Form\Traits\HasSections;

class Form
{
	use HasArrayableData;
	use HasSections;
	
	public ?array $tabs = NULL;
	
	public function __construct($config = [])
	{
		$defaultConfig = [
			'action_button' => NULL, //but is actually an array if used
			'active_tab' => NULL,
			'ajax_validate' => FALSE,
			'alerts_name' => NULL,
			'base_url' => NULL,
			'buttons' => NULL, //but is actually an array if used
			'cp_page_title' => NULL,
			'cp_page_title_alt' => NULL,
			'extra_alerts' => NULL, //but is actually an array
			'form_hidden' => NULL, //but is actually an array
			'has_file_input' => FALSE,
			'hide_top_buttons' => FALSE,
			'save_btn_text' => 'Save', //make default a lang function?
			'save_btn_text_working' => 'Saving', //make default lang function?
		];
		
		$this->data = array_merge($defaultConfig, $config);
	}
	
	public static function make($config = [])
	{
		return new static($config);
	}
	
	public function tab($key)
	{
		$tab = new Tab($key);
		
		$this->tabs[$key] = $tab;
		
		return $tab;
	}
	
	public function addHiddenFields($data = [])
	{
		if(count($data) > 0)
		{
			foreach($data as $name => $value)
			{
				$this->addHiddenField($name, $value);
			}
		}
		
		return $this;
	}
	
	public function addHiddenField($name, $value = '')
	{
		$this->data['form_hidden'][$name] = $value;
		
		return $this;
	}
	
	public function hideTopButtons()
	{
		return $this->setData('hide_top_buttons', TRUE);
	}
	
	public function setActionButton($href, $text, $rel = NULL)
	{
		return $this->setData('action_button', [
			'href' => $href,
			'text' => $text,
			'rel' => $rel
		]);
	}
	
	public function setAlertName($name)
	{
		return $this->setData('alert_name', $name);
	}
	
	public function setBaseUrl($url)
	{
		return $this->setData('base_url', $url);
	}
	
	public function setPageTitle($text)
	{
		return $this->setData('cp_page_title', $text);
	}
	
	public function setPageTitleAlt($text)
	{
		return $this->setData('cp_page_title_alt', $text);
	}
	
	public function setSaveButtonLabels($awaitClick, $onClick)
	{
		$this->setData('save_btn_text', $awaitClick);
		$this->setData('save_btn_text_working', $onClick);
		
		return $this;
	}
	
	public function hasAjaxValidation()
	{
		return $this->setData('ajax_validate', TRUE);
	}
	
	public function hasFileUpload()
	{
		return $this->setData('has_file_input', TRUE);
	}
	
	public function toArray()
	{
		//$this->data['tabs'] = $this->tabs;
		//$this->data['sections'] = $this->sections;
		
		if(isset($this->tabs) && count($this->tabs) > 0)
		{
			$secureFormCtrls = (isset($this->sections['secure_form_ctrls'])) ? $this->sections['secure_form_ctrls'] : NULL;
			
			$this->sections = NULL;
			
			if($secureFormCtrls)
			{
				$this->sections = ['secure_form_ctrls' => $secureFormCtrls];
			}
		}
		
		if(isset($this->sections) && count($this->sections) > 0)
		{
			$this->data = array_merge($this->data, [
				'sections' => array_map(function ($section) {
				     return $section->toArray();
				}, $this->sections)
				/*'sections' => array_reduce(array_map(function ($fieldset) {
					return [$fieldset->title => $fieldset->toArray()];
				}, $this->sections), 'array_merge', [])*/
			]);
		}
		
		/* If no base_url was set, default to current url */
		if(!isset($this->data['base_url']))
		{
			$this->data['base_url'] = ee('CP/URL')->getCurrentUrl();
		}
		
		/* PLACEHOLDER ACTIVE_TAB CONDITIONAL - NUMERIC / FIND INDEX */
		
		return $this->data;
	}
}