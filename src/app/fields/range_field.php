<?php
class RangeField extends Field {

	function __construct($options) {
		$options += array(
			'numeric' => true,
			'min_value' => null,
			'max_value' => null,
			'widget_range' => null,
			'autocorrect' => false,
			'unbounded' => false,
			'error_messages' => array(),
			'widget_options' => array(),
			'both_values_required_at_once' => true,
		);

		$this->autocorrect = $options['autocorrect'];
		$options['error_messages'] += array(
			'required_min' => _('Minimum of the range is required'),
			'required_max' => _('Maximum of the range is required'),
			'out_of_range' => _('Value is out of range of allowed values'),
			'invalid' => _('Invalid input for range field'),
			'numeric' => _("This range field accepts only numeric values"),
		);

		$this->numeric = $options['numeric'];

		$this->range = array(
			'min' => $options['min_value'],
			'max' => $options['max_value']
		);
		$wrange = $options['widget_range'] ? $wrange : $this->range;

		$options+= array(
			'widget' => new RangeInput($options['widget_options'] + array(
				'min_value' => $wrange['min'],
				'max_value' => $wrange['max'],
				'unbounded' => $options['unbounded'],
			))
		);

		$this->unbounded = $options['unbounded'];

		parent::__construct($options);
	}

	function set_range($range, $for = array('widget', 'field')) {
		if(!is_array($for)) {
			$for = array( $for );
		}
		if(in_array('field', $for)) {
			$this->range = $range;
		}
		if(in_array('widget', $for)) {
			$this->widget->range = $range;
		}
	}

	function checkRange(&$v, $role) {
		if($v === null || $v === '') {
			if($this->autocorrect) {
				$v = $this->unbounded ? null : $this->range[$role];
			}
			if($this->required) {
				return $this->messages['required'];
			}
			return false;
		}

		if($this->numeric) {
			if(!is_numeric($v)) {
					return $this->messages['numeric'];
			}
			$v = (float)$v;
		}

		if($this->range['min'] !== null && ($v < $this->range['min'])) {
			if($this->autocorrect) {
				$v = $this->range['min'];
			} else {
				if($v===null) {
					return $this->messages['required_'.$role] ;
				} else {
					return $this->messages['out_of_range'];
				}
			}
		}

		if($this->range['max'] !== null && $v > $this->range['max']) {
				if($this->autocorrect) {
					$v = $this->range['max'];
				} else {
					if($v===null) {
						return $this->messages['required_'.$role] ;
					} else {
						return $this->messages['out_of_range'];
					}
				}
		}

		if( $this->unbounded && $v == $this->range[$role] ) {
			$v = null;
		}
		return false;
	}

	function clean($value) {
		if(!is_array($value)) {
			if($this->autocorrect) {
				return array(null, $this->unbounded ? array() : $this->range);
			}
			return array($this->messages['invalid'], null);
		}
		$value = array_intersect_key($value, array('min' => 1, 'max' => 1)) +
			array('min' => null, 'max' => null);
		$value = array_map( function($v) { $v = trim($v); return $v==='' ? null : $v; }, $value);

		if( $value['min'] !== null && $value['max'] !== null && $value['min'] > $value['max'] ) {
			$value['min']=$value['max'];
		}

		foreach($value as $k => &$v) {
			if($e = $this->checkRange($v, $k)) {
				return array_map($e, null);
			}
		}
		unset($v);
		return array(null, $value);
	}

}
