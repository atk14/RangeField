<?php
/**
 */
class RangeInput extends Widget
{
	var $input_type = 'number';
	var $disabled = false;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	function __construct($options = array()){
		$options += [
			'min_value' => null,
			'max_value' => null,
			'params' => [],
			'step' => 1,
			'format' => null,			//see javascript NoUISlider.formats
			'format_arguments' => null,
			'unbounded' => false   //returns null for the border values
		];
		$this->range= [
			'min' => $options['min_value'] ?:0,
			'max' => $options['max_value'] ?:1
		];
		parent::__construct($options);
		$this->params = $options['params'];
		$this->unbounded = $options['unbounded'];
		$this->step = $options['step'];
		$this->format = $options['format'];
		$this->format_arguments = $options['format_arguments'];
	}

	function set_range($range) {
		$this->range = $range;
	}

	function value($value, $role) {
		if( $value[$role] && $value[$role] != $this->range[$role] ) {
			return $value[$role];
		}
		return $this->unbounded ? null : $this->range[$role];
	}

	function render($name, $value, $options=array()) {
		$value = (array)$value;
		$value += array(
			"min" => null,
			"max" => null,
		);
		$options = forms_array_merge(array('attrs'=> null), $options);
		$out =
			trim(strip_tags(_('<!-- range --> from'))) .
			" " .
			$this->input($name, $value, 'min', $options) .
			" " .
			trim(strip_tags(_('<!-- range --> to'))) .
			" " .
			$this->input($name, $value, 'max', $options);

		$data = $this->params;
		if( $value['min'] === null ) {
			$value['min'] = $this->range['min'];
		}
		if( $value['max'] === null ) {
			$value['max'] = $this->range['max'];
		}

		$data['start'] = [ $value['min'], $value['max'] ];
		$data['range'] = $this->range;
		$data['step'] = $this->step;
		$data['unbounded'] = $this->unbounded;
		if($this->format) {
			$data['format'] = $this->format;
		}
		if($this->format_arguments) {
			$data['format_arguments'] = $this->format_arguments;
		}

		$data = json_encode($data);

		$class_disabled = $this->disabled ? ' noui-slider--disabled' : '';

		return "
<div class='noui-slider__wrapper'>
  <div class='noui-slider$class_disabled' data-noui-slider='$data'>
    <div class='noui-slider-hide'>
    $out
    </div>
  </div>
</div>";
	}

	function input($name, $value, $role, $options) {

		$final_attrs = $this->build_attrs([
			'type' => $this->input_type,
			'value' => $this->value($value, $role),
			'name' => "{$name}[$role]",
			'class' => "noui-slider-$role" ],
			$options['attrs']
		);
		$final_attrs["id"] = $final_attrs["id"].($role == 'min'?'':'_max'); // "id_name" or "id_name_max"

		if($this->range['min']!==null) {
			$final_attrs['min'] = $this->range['min'];
		}
		if($this->range['max']!==null) {
			$final_attrs['max'] = $this->range['max'];
		}

		return '<input'.flatatt($final_attrs).' />';
	}
}
