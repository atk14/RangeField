RangeField
==========

RangeField is a field for entering two numeric values within specified minimum and maximum.

In eshops, this is an useful field, for example, for setting the price range for product selection.

Usage
-----

    <?php
    // file: app/forms/products/index_form.php
    class IndexForm extends ApplicatonForm {

      function set_up(){
        $this->add_field("search", new CharField([
          "label" => "Search",
          "required" => false,
        ]));

        $this->add_field("price", new RangeField([
          "label" => "Price range",
          "min_value" => 1,
          "max_value" => 100000,
          "required" => false,
        ]));
      }
    }

    <?php
    // file: app/controllers/products_controller.php
    class ProductsController extends ApplicationController {

      function index(){
        $d = $this->form->validate($this->params);

        $min = $d["price"]["min"];
        $max = $d["price"]["max"];

        // ..
      }
    }

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/range-field

Optionally you can symlink RangeField files into your project:

    ln -s ../../vendor/atk14/range-field/src/app/fields/range_field.php app/fields/range_field.php
    ln -s ../../vendor/atk14/range-field/src/app/widgets/range_input.php app/widgets/range_input.php

Testing
-------

    composer update --dev
    cd test
    ../vendor/bin/run_unit_tests

License
-------

RangeField is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
