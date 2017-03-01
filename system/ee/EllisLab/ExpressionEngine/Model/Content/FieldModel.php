<?php

namespace EllisLab\ExpressionEngine\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;
use EllisLab\ExpressionEngine\Service\Validation\Result as ValidationResult;

abstract class FieldModel extends Model {

	protected static $_events = array(
		'afterInsert',
		'afterUpdate',
		'afterDelete'
	);


	protected $_field_facade;

	/**
	 * Return the storing table
	 */
	abstract public function getDataTable();

	/**
	 *
	 */
	abstract public function getStructure();

	/**
	 *
	 */
	public function getField($override = array())
	{
		$field_type = $this->getFieldType();

		if (empty($field_type))
		{
			throw new \Exception('Cannot get field of unknown type "' . $field_type . '".');
		}

		if ( ! isset($this->_field_facade) ||
			$this->_field_facade->getType() != $this->getFieldType() ||
			$this->_field_facade->getId() != $this->getId())
		{
			$values = array_merge($this->getValues(), $override);

			$this->_field_facade = new FieldFacade($this->getId(), $values);
			$this->_field_facade->setContentType($this->getContentType());
		}

		if (isset($this->field_fmt))
		{
			$this->_field_facade->setFormat($this->field_fmt);
		}

		return $this->_field_facade;
	}

	public function getSettingsForm()
	{
		return $this->getField($this->getSettingsValues())->getSettingsForm();
	}

	public function getSettingsValues()
	{
		return $this->getValues();
	}

	protected function getContentType()
	{
		return $this->getStructure()->getContentType();
	}

	public function set(array $data = array())
	{
		// getField() requires that we have a field type, but we might be trying
		// to set it! So, if we are, we'll do that first.
		if (isset($data['field_type']))
		{
			$this->setProperty('field_type', $data['field_type']);
		}

		$field = $this->getField($this->getSettingsValues());
		$data = array_merge($field->saveSettingsForm($data), $data);

		return parent::set($data);
	}

	public function validate()
	{
		$result = parent::validate();

		$settings = $this->getSettingsValues();

		if (isset($settings['field_settings']))
		{
			$field = $this->getField($this->getSettingsValues());
			$settings_result = $field->validateSettingsForm($settings['field_settings']);

			if ($settings_result instanceOf ValidationResult && $settings_result->failed())
			{
				foreach ($settings_result->getFailed() as $name => $rules)
				{
					foreach ($rules as $rule)
					{
						$result->addFailed($name, $rule);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Calling the Post Save Settings after every save. Grid (and others?)
	 * saves its settings in the post_save_settings call.
	 */
	public function save()
	{
		parent::save();
	}

	/**
	 * After inserting, add the columns to the data table
	 */
	public function onAfterInsert()
	{
		$this->createTable();
		$this->callPostSaveSettings();
	}

	/**
	 * After deleting, drop the columns
	 */
	public function onAfterDelete()
	{
		if ($this->hasProperty('legacy_field_data')
			&& $this->getProperty('legacy_field_data') == FALSE)
		{
			$this->dropTable();
		}
		else
		{
			$ft = $this->getFieldtypeInstance();

			$data = $this->getValues();
			$data['ee_action'] = 'delete';

			$columns = $ft->settings_modify_column($data);
			$columns = $this->ensureDefaultColumns($columns);

			$this->dropColumns($columns);
		}
	}

	/**
	 * If the update changes the field_type, we need to sync the columns
	 * on the data table
	 */
	public function onAfterUpdate($changed)
	{
		$this->callPostSaveSettings();

		$old_type = (isset($changed['field_type'])) ? $changed['field_type'] : $this->field_type;
		$old_action = (isset($changed['field_type'])) ? 'delete' : 'get_info';

		$old_ft = $this->getFieldtypeInstance($old_type, $changed);
		$old_columns = $this->callSettingsModify($old_ft, $old_action, $changed);

		$new_ft = $this->getFieldtypeInstance();
		$new_columns = $this->callSettingsModify($new_ft, 'get_info');

		if ( ! empty($old_columns) || ! empty($new_columns))
		{
			$this->diffColumns($old_columns, $new_columns);
		}
	}

	protected function callSettingsModify($ft, $action, $changed = array())
	{
		$data = $this->getValues();
		$data = array_merge($data, $changed);

		if ( ! isset($data['field_settings']))
		{
			$data['field_settings'] = array();
		}

		$data['ee_action'] = $action;

		return $ft->settings_modify_column($data);
	}

	/**
	 * Calls post_save_settings on the fieldtype
	 */
	protected function callPostSaveSettings()
	{
		$data = $this->getValues();
		$field = $this->getField($this->getSettingsValues());
		$field->postSaveSettings($data);
	}

	/**
	 * Get the instance of the current fieldtype
	 */
	protected function getFieldtypeInstance($field_type = NULL, $changed = array())
	{
		$field_type = $field_type ?: $this->getFieldType();
		$values = array_merge($this->getValues(), $changed);

		$facade = new FieldFacade($this->getId(), $values);
		$facade->setContentType($this->getContentType());
		return $facade->getNativeField();
	}

	/**
	 * Simple getter for field type, override if your field type property has a
	 * different name.
	 *
	 * @access protected
	 * @return string The field type.
	 */
	protected function getFieldType()
	{
		return $this->field_type;
	}

	/**
	 *
	 */
	private function diffColumns($old, $new)
	{
		$old = $this->ensureDefaultColumns($old);
		$new = $this->ensureDefaultColumns($new);

		$drop = array();
		$change = array();

		foreach ($old as $name => $prefs)
		{
			if ( ! isset($new[$name]))
			{
				$drop[$name] = $old[$name];
			}
			elseif ($prefs != $new[$name])
			{
				$change[$name] = $new[$name];
				unset($new[$name]);
			}
			else
			{
				unset($new[$name]);
			}
		}

		$this->dropColumns($drop);
		$this->modifyColumns($change);
	}

	/**
	 * Modify columns that were changed
	 *
	 * @param Array $columns List of [column name => column definition]
	 */
	private function modifyColumns($columns)
	{
		if (empty($columns))
		{
			return;
		}

		$data_table = $this->getDataTable();

		foreach ($columns as $name => &$column)
		{
			if ( ! isset($column['name']))
			{
				$column['name'] = $name;
			}
		}

		ee()->load->dbforge();
		ee()->dbforge->modify_column($data_table, $columns);
	}

	/**
	 * Drop columns, including the defaults
	 *
	 * @param Array $columns List of column definitions as in createColumns, but
	 *						 only the keys are actually used
	 */
	private function dropColumns($columns)
	{
		if (empty($columns))
		{
			return;
		}

		$columns = array_keys($columns);

		$data_table = $this->getDataTable();

		ee()->load->dbforge();

		foreach ($columns as $column)
		{
			ee()->dbforge->drop_column($data_table, $column);
		}
	}

	/**
	 * Add the default columns if they don't exist
	 *
	 * @param Array $columns Column definitions
	 * @return Array Updated column definitions
	 */
	private function ensureDefaultColumns($columns)
	{
		$id_field_name = $this->getColumnPrefix().'field_id_'.$this->getId();
		$ft_field_name = $this->getColumnPrefix().'field_ft_'.$this->getId();

		if ( ! isset($columns[$id_field_name]))
		{
			$columns[$id_field_name] = array(
				'type' => 'text',
				'null' => TRUE
			);
		}

		if ( ! isset($columns[$ft_field_name]))
		{
			$columns[$ft_field_name] = array(
				'type' => 'tinytext',
				'null' => TRUE
			);
		}

		return $columns;
	}

	/**
	 * Set a prefix on the default columns we manage for fields
	 *
	 * @return	String	Prefix string to use
	 */
	public function getColumnPrefix()
	{
		return '';
	}

	public function getTableName()
	{
		return $this->getDataTable() . '_field_' . $this->getId();
	}

	protected function getForeignKey()
	{
		return 'entry_id';
	}

	/**
	 * Create the table for the field
	 */
	private function createTable()
	{
		ee()->load->dbforge();
		ee()->load->library('smartforge');
		ee()->dbforge->add_field(
			array(
				'id' => array(
					'type'           => 'int',
					'constraint'     => 10,
					'null'           => FALSE,
					'unsigned'       => TRUE,
					'auto_increment' => TRUE
				),
				$this->getForeignKey() => array(
					'type'           => 'int',
					'constraint'     => 10,
					'null'           => FALSE,
					'unsigned'       => TRUE,
				),
				'language' => array(
					'type'       => 'varchar',
					'constraint' => '5',
					'null'       => FALSE,
					'default'    => 'en-US' // @TODO Have this match the default language of the site
				),
				'data' => array(
					'type' => 'text',
					'null' => TRUE
				),
				'metadata' => array(
					'type' => 'tinytext',
					'null' => TRUE
				)
			)
		);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key($this->getForeignKey());
		ee()->smartforge->create_table($this->getTableName());
	}

	/**
	 * Drops the table for the field
	 */
	private function dropTable()
	{
		ee()->load->library('smartforge');
		ee()->smartforge->drop_table($this->getTableName());
	}

	/**
	 * TEMPORARY, VOLATILE, DO NOT USE
	 *
	 * @param	mixed	$data			Data for this field
	 * @param	int		$content_id		Content ID to pass to the fieldtype
	 * @param	string	$content_type	Content type to pass to the fieldtype
	 * @param	string	$modifier		Variable modifier, if present
	 * @param	string	$tagdata		Tagdata to perform the replacement in
	 * @param	string	$row			Row array to set on the fieldtype
	 * @return	string	String with variable parsed
	 */
	public function parse($data, $content_id, $content_type, $modifier, $tagdata, $row)
	{
		$fieldtype = $this->getFieldtypeInstance();
		$settings = $this->getSettingsValues();
		$field_fmt = isset($this->field_fmt) ? $this->field_fmt : $this->field_default_fmt;
		$settings['field_settings'] = array_merge($settings['field_settings'], array('field_fmt' =>$field_fmt));

		$fieldtype->_init(array(
			'row'			=> $row,
			'content_id'	=> $content_id,
			'content_type'	=> $content_type,
			'field_fmt'		=> $field_fmt,
			'settings'		=> $settings['field_settings']
		));

		$parse_fnc = ($modifier) ? 'replace_'.$modifier : 'replace_tag';

		if (method_exists($fieldtype, $parse_fnc))
		{
			$data = ee()->api_channel_fields->apply($parse_fnc, array(
				$data,
				array(),
				FALSE
			));
		}

		$tag = $this->field_name;
		if ($modifier)
		{
			$tag = $tag.':'.$modifier;
		}

		return str_replace(LD.$tag.RD, $data, $tagdata);
	}
}

// EOF
