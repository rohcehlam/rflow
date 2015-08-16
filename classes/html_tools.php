<?php

class thtml_writer {

	private $function;

	public function __construct($function = 'view') {
		$this->function = $function;
	}

	function draw_input($id = '', $label_text = '', $name = '', $value = '', $required = false) {
		?>
		<div class="form-group">
			 <label for='<?php echo $id; ?>' class="control-label col-xs-2"><?php echo $label_text; ?></label>
			 <?php if (($this->function == "add") || ($this->function == "update")) { ?>
				 <div class="col-xs-10">
					  <input type="text" name='<?php echo $name; ?>' class="form-control" value="<?php echo $value; ?>"<?php echo $required ? ' required' : ''; ?>/>
				 </div>
			 <?php } else { ?>
				 <div class="col-xs-10">
					  <div class="input-group">
							<input type="text" name='<?php echo $name; ?>' value="<?php echo $value; ?>" class='form-control' readonly/>
							<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
					  </div>
				 </div>
			 <?php }
			 ?>
		</div>
		<?php
	}

	function draw_select($id = '', $label_text = '', $name = '', $value = '', $elements = [], $selected = '', $required = false) {
		?>
		<div class="form-group">
			 <label for='<?php echo $id; ?>' class="control-label col-xs-2"><?php echo $label_text; ?></label>
		<?php if (($this->function == "add") || ($this->function == "update")) { ?>
				 <div class="col-xs-10">
					  <select name="<?php echo $name ?>" class='form-control'>
							<?php
							foreach ($elements as $data) {
								echo "<option value='{$data['id']}'" . ($data['id'] == $selected ? ' selected' : '') . ">{$data['value']}</option>\n";
							}
							?>
					  </select>
				 </div>
		<?php } else { ?>
				 <div class="col-xs-10">
					  <div class="input-group">
							<input type="text" name='<?php echo $name; ?>' value="<?php echo $value; ?>" class='form-control' readonly/>
							<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
					  </div>
				 </div>
			 <?php }
			 ?>
		</div>
		<?php
	}

}
