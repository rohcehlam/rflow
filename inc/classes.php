<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class tBS_label {

	private $for;
	private $size;
	private $value;

	function __construct($for = '', $size = 0, $value = '') {
		$this->for = $for;
		$this->size = $size;
		$this->value = $value;
	}

	function draw() {
		echo "<label for='{$this->for}' class=\"control-label col-xs-{$this->size}\">{$this->value}</label>\n";
	}

}

class tBS_input {

	private $size;
	private $name;
	private $id;
	private $value;
	private $placeholder;
	private $before;
	private $after;
	private $data_error;
	private $required;

	function __construct($size, $name, $id, $value = '', $placeholder = '', $before = '', $after = '', $data_error = '', $required = false) {
		$this->size = $size;
		$this->name = $name;
		$this->id = $id;
		$this->value = $value;
		$this->placeholder = $placeholder;
		$this->before = $before;
		$this->after = $after;
		$this->data_error = $data_error;
		$this->required = $required;
	}

	function draw($edit = true) {
		echo "<div class=\"col-xs-{$this->size}\">\n";
		echo ($this->before != '' or $this->after != '' or ! $edit) ? "<div class = \"input-group\">\n" : '';
		echo $this->before != '' ? "<span class = \"input-group-addon\">{$this->before}</span>\n" : '';
		echo "<input name=\"{$this->name}\" type=\"text\" id=\"{$this->id}\" value=\"{$this->value}\" class = \"form-control\""
		. (!$edit ? ' readonly' : '') . ($this->required ? ' required' : '') . ($this->data_error != '' ? " data-error='{$this->data_error}'" : '') . "/>";
		echo!$edit ? "<span class = \"input-group-addon\"><span class = \"glyphicon glyphicon-lock\"></span></span>\n" : '';
		echo $this->after != '' ? "<span class = \"input-group-addon\">{$this->after}</span>\n" : '';
		echo $this->before != '' or $this->after != '' or ! $edit ? "</div>\n" : '';
		echo "</div>\n";
	}

}

class tBS_select {

	private $size;
	private $name;
	private $id;
	private $value;
	private $before;
	private $after;
	private $required;

	function __construct($size, $name, $id, $value = '', $before = '', $after = '', $required = false) {
		$this->size = $size;
		$this->name = $name;
		$this->id = $id;
		$this->value = $value;
		$this->before = $before;
		$this->after = $after;
		$this->required = $required;
	}

	function draw($options = array(), $edit = true) {
		echo "<div class=\"col-xs-{$this->size}\">\n";
		echo ($this->before != '' or $this->after != '' or ! $edit) ? "<div class = \"input-group\">\n" : '';
		echo $this->before != '' ? "<span class = \"input-group-addon\">{$this->before}</span>\n" : '';
		echo "<select  name=\"{$this->name}\" id=\"{$this->id}\" class = \"form-control\"" . (!$edit ? ' readonly' : '') . "/>";
		foreach ($options as $option) {
			echo "<option value='{$option['id']}'" . ($option['id'] == $this->value ? ' selected="selected"' : '') . ">{$option['value']}</option>\n";
		}
		echo "</select>\n";
		echo!$edit ? "<span class = \"input-group-addon\"><span class = \"glyphicon glyphicon-lock\"></span></span>\n" : '';
		echo $this->after != '' ? "<span class = \"input-group-addon\">{$this->after}</span>\n" : '';
		echo $this->before != '' or $this->after != '' or ! $edit ? "</div>\n" : '';
		echo "</div>\n";
	}

}

class tBS_input_date {

	private $size;
	private $name;
	private $id;
	private $value;
	private $placeholder;
	private $before;
	private $after;
	private $data_error;
	private $required;

	function __construct($size, $name, $id, $value = '', $placeholder = '', $before = '', $after = '', $data_error = '', $required = false) {
		$this->size = $size;
		$this->name = $name;
		$this->id = $id;
		$this->value = $value;
		$this->placeholder = $placeholder;
		$this->before = $before;
		$this->after = $after;
		$this->data_error = $data_error;
		$this->required = $required;
	}

	function draw($edit = true) {
		echo "<div class=\"col-xs-{$this->size}\">\n";
		echo "<div class = \"input-group\">\n";
		echo $this->before != '' ? "<span class = \"input-group-addon\">{$this->before}</span>\n" : '';
		echo "<span class = \"input-group-addon\" onclick = 'open{$this->id}datepicker();'><span class = \"glyphicon glyphicon-calendar\"></span></span>\n";
		echo "<input name=\"{$this->name}\" type=\"text\" id=\"{$this->id}\" value=\"{$this->value}\" class = \"form-control\""
		. (!$edit ? ' readonly' : '') . ($this->required ? ' required' : '') . ($this->data_error != '' ? " data-error='{$this->data_error}'" : '') . "/>";
		echo!$edit ? "<span class = \"input-group-addon\"><span class = \"glyphicon glyphicon-lock\"></span></span>\n" : '';
		echo $this->after != '' ? "<span class = \"input-group-addon\">{$this->after}</span>\n" : '';
		echo "</div>\n";
		echo "</div>\n";
		echo '<script>$(function () {$("#' . $this->id . '").datepicker();});function open' . $this->id . 'datepicker() {$("#' . $this->id . '").datepicker("show");}</script>' . PHP_EOL;
	}

}

class thtml_builder {
	
}

// thtml_builder

