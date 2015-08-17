<?php

class thtml_writer {

	private $function;

	public function __construct($function = 'view') {
		$this->function = $function;
	}

	function draw_input($id = '', $label_text = '', $name = '', $value = '', $placeholder = '', $required = false) {
		?>
		<div class="form-group">
			 <label for='<?php echo $id; ?>' class="control-label col-xs-2"><?php echo $label_text; ?></label>
			 <?php if (($this->function == "add") || ($this->function == "update")) { ?>
				 <div class="col-xs-10">
					  <input type="text" name='<?php echo $name; ?>' class="form-control" value="<?php echo $value; ?>"<?php echo $required ? ' required' : ''; ?> <?php echo ($placeholder != '') ? " placeholder='$placeholder'" : ''; ?>/>
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

	function draw_check($id = '', $label_text = '', $name = '', $value = '', $text = '', $checked = false) {
		?>
		<div class='form-group'>
			 <label for='<?php echo $id; ?>' class="control-label col-xs-2"><?php echo $label_text; ?></label>
			 <div class="col-xs-10">
				  <?php if (($this->function == "add") || ($this->function == "update")) { ?>
					  <div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default<?php echo $checked ? ' active' : ''; ?>">
								 <input type="checkbox" value='<?php echo $value; ?>' name="<?php echo $name; ?>" id="<?php echo $id; ?>"<?php echo $checked ? ' checked="checked"' : ''; ?>/><?php echo $text; ?>
							</label>
					  </div>
				  <?php } else { ?>
					  <div class="input-group">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								 <label class="btn btn-default<?php echo $checked ? ' active' : ''; ?>">
									  <input type="checkbox" value='<?php echo $value; ?>' name="<?php echo $name; ?>" id="<?php echo $id; ?>"<?php echo $checked ? ' checked="checked"' : ''; ?> readonly=""/><?php echo $text; ?>
								 </label>
							</div>
							<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
					  </div>
				  <?php } ?>
			 </div>
		</div>
		<?php
	}

	function draw_date($id = '', $label_text = '', $name = '', $value = '', $required = false) {
		?>
		<div class='form-group'>
			 <label for='<?php echo $id; ?>' class="control-label col-xs-2"><?php echo $label_text; ?></label>
			 <div class="col-xs-10">
				  <?php if (($this->function == "add") || ($this->function == "update")) { ?>
					  <div class="input-group">
							<span class="input-group-addon" onclick='func_<?php echo $id; ?>_datepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
							<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="form-control" value="<?php echo $value; ?>" <?php $required ? ' required' : ''; ?>/>
					  </div>
					  <script type="text/javascript">
						  $(function () {
								$("#<?php echo $id; ?>").datepicker();
						  });
						  function func_<?php echo $id; ?>_datepicker() {
								$("#<?php echo $id; ?>").datepicker("show");
						  }
					  </script>
				  <?php } else { ?>
					  <div class="input-group">
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="form-control" value="<?php echo $value; ?>" readonly/>
							<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
					  </div>
				  <?php } ?>
			 </div>
		</div>
		<?php
	}

	function draw_fileupload($id = '', $label_text = '', $name = '', $filename = '') {
		?>
		<div class='form-group'>
			 <label for='<?php echo $id; ?>' class="control-label col-xs-2"><?php echo $label_text; ?></label>
			 <div class="col-xs-10">
				  <?php if (($this->function == "add") || ($this->function == "update")) { ?>
					  <input type="file" name="<?php echo $name; ?>" id="<?php echo $id; ?>"/>
					  <?php
				  } else {
					  if (!file_exists("../images/$filename")) {
						  echo "<img data-src='holder.js/160x160' class='img-thumbnail' alt='User Photo'>";
					  } else {
						  echo "<img src='../images/$filename' alt='User Photo'>";
					  }
				  }
				  ?>
			 </div>
		</div>
		<?php
	}

}
