<?php
class TcRangeField extends TcBase {

	function test(){
		$this->field = new RangeField(array("required" => false, "min_value" => -12.5, "max_value" => 99.9));

		$value = $this->assertValid(array("min" => "10", "max" => "20"));
		$this->assertArrayEquals(array("min" => 10.0, "max" => 20.0),$value);

		$value = $this->assertValid(array("min" => " 10.01 ", "max" => " 20.02 "));
		$this->assertArrayEquals(array("min" => 10.01, "max" => 20.02),$value);

		$value = $this->assertValid(array("min" => "+10.1", "max" => "20.2"));
		$this->assertEquals(array("min" => 10.1, "max" => 20.2),$value);

		$value = $this->assertValid(array("min" => "-11.1", "max" => 22.22));
		$this->assertEquals(array("min" => -11.1, "max" => 22.22),$value);

		$value = $this->assertValid(array());
		$this->assertArrayEquals(array("min" => null, "max" => null),$value);

		$value = $this->assertValid(array("min" => "", "max" => ""));
		$this->assertArrayEquals(array("min" => null, "max" => null),$value);
	}

	function test_widget(){
		$form = new Atk14Form();

		$form->add_field("range1", new RangeField([
			"min_value" => 1,
			"max_value" => 10000,
		]));

		$range1 = $form->get_field("range1");
		$this->assertEquals('
<div class=\'noui-slider__wrapper\'>
  <div class=\'noui-slider\' data-noui-slider=\'{"start":[1,10000],"range":{"min":1,"max":10000},"step":1,"unbounded":false}\'>
    <div class=\'noui-slider-hide\'>
    from <input type="number" value="1" name="range1[min]" class="noui-slider-min" id="id_range1" min="1" max="10000" /> to <input type="number" value="10000" name="range1[max]" class="noui-slider-max" id="id_range1_max" min="1" max="10000" />
    </div>
  </div>
</div>',$range1->as_widget());
	}

	function assertArrayEquals($expected,$value){
		$this->assertEquals($expected,$value);
		$this->assertTrue($expected === $value); // same types required
	}
}
