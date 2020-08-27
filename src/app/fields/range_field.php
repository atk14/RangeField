<?php
class RangeField extends Field {

	function __construct($options) {
		$options+= [
			'numeric' => true,
			'min_value' => null,
			'max_value' => null,
			'widget_range' => null,
			'autocorrect' => false,
			'unbounded' => false,
			'error_messages' => [],
			'widget_options' => []
		];

		$this->autocorrect = $options['autocorrect'];
		$options['error_messages'] += [
			'required_min' => _('Minimum of the range is required'),
			'required_max' => _('Maximum of the range is required'),
			'out_of_range' => _('Value is out of range of allowed values'),
			'invalid' => _('Invalid input for range field'),
			'numeric' => _("This range field accepts only numeric values"),
		];

		$this->numeric = $options['numeric'];

		$this->range = [
			'min' => $options['min_value'],
			'max' => $options['max_value']
		];
		$wrange = $options['widget_range']?$wrange:$this->range;

		$options+= [
			'widget' => new RangeInput($options['widget_options'] + [
				'min_value' => $wrange['min'],
				'max_value' => $wrange['max'],
				'unbounded' => $options['unbounded'],
			])
		];

		$this->unbounded = $options['unbounded'];

		parent::__construct($options);
	}

	function set_range($range, $for = ['widget', 'field']) {
		if(!is_array($for)) {
			$for = [ $for ];
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
				$v = $this->unbounded?null : $this->range[$role];
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
				return [null, $this->unbounded ? [] : $this->range ];
			}
			return [$this->messages['invalid'],null];
		}
		$value = array_intersect_key($value, ['min'=>1, 'max' =>1]) +
			['min' => null, 'max' => null];
		$value = array_map( function($v) { $v = trim($v); return $v==='' ? null: $v; }, $value);

		if( $value['min'] !== null && $value['max'] !== null && $value['min'] > $value['max'] ) {
			$value['min']=$value['max'];
		}

		foreach($value as $k => &$v) {
			if($e = $this->checkRange($v, $k)) {
				return [ $e, null ];
			}
		}
		unset($v);
		return [null, $value];
	}

}
