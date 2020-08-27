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

	function assertArrayEquals($expected,$value){
		$this->assertEquals($expected,$value);
		$this->assertTrue($expected === $value); // same types required
	}
}
