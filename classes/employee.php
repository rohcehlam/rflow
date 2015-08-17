<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'html_tools.php';

class tEmployee {

	private $conn;
	private $EmployeeID;
	private $firstName;
	private $lastName;
	private $DisplayName;
	private $title;
	private $cellPhone;
	private $homePhone;
	private $workEmail;
	private $departmentID;
	private $groupID;
	private $str_department;
	private $str_group;
	private $engineer;
	private $manager;
	private $hireDate;
	private $active;
	private $hash_pass;
	public $error;

	function __construct($conn = null, $id = 0, $firstName = '', $lastName = '', $displayName = '', $title = '', $cellPhone = '', $homePhone = '', $workEmail = ''
	, $groupID = 0, $departmentID = 0, $engineer = 'n', $manager = 'f', $hireDate = '0000-00-00', $active = 'f') {
		$this->conn = $conn;
		$this->EmployeeID = $id;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->DisplayName = $displayName;
		$this->title = $title;
		$this->cellPhone = $cellPhone;
		$this->homePhone = $homePhone;
		$this->workEmail = $workEmail;
		$this->groupID = $groupID;
		$this->departmentID = $departmentID;
		$this->engineer = $engineer;
		$this->manager = $manager;
		$this->hireDate = $hireDate;
		$this->active = $active;
		$this->error = '';
	}

	function load() {
		$query = <<< EOD
SELECT firstName, lastName, displayName, title, cellPhone, homePhone, workEmail, e.groupID, g.group AS `group`, e.departmentID, d.department AS `department`, engineer, manager
	, hireDate, active, hash_pass
	FROM employees AS e
	LEFT JOIN(groups AS g) ON g.groupID=e.groupID
	LEFT JOIN(departments AS d) ON d.departmentID=e.departmentID
	WHERE employeeID=%%id%%
EOD;
		$result = $this->conn->query(str_replace('%%id%%', $this->EmployeeID, $query));
		while ($row = $result->fetch_assoc()) {
			$this->firstName = $row['firstName'];
			$this->lastName = $row['lastName'];
			$this->DisplayName = $row['displayName'];
			$this->title = $row['title'];
			$this->cellPhone = $row['cellPhone'];
			$this->homePhone = $row['homePhone'];
			$this->workEmail = $row['workEmail'];
			$this->groupID = $row['groupID'];
			$this->departmentID = $row['departmentID'];
			$this->str_group = $row['group'];
			$this->str_department = $row['department'];
			$this->engineer = $row['engineer'];
			$this->manager = $row['manager'];
			$this->hireDate = $row['hireDate'];
			$this->active = $row['active'];
			//$this->hash_pass = $row['hash_pass'];
		}
	}

	function form($function) {

		$rs_groups = $this->conn->query("SELECT groupID, `group` as description FROM groups") or die($this->conn->error);
		while ($row_groups = $rs_groups->fetch_assoc()) {
			$array_groups[] = ['id' => $row_groups['groupID'], 'value' => $row_groups['description']];
		}
		$rs_departments = $this->conn->query("SELECT departmentID, department AS description FROM departments") or die($this->conn->error);
		while ($row_departments = $rs_departments->fetch_assoc()) {
			$array_departments[] = ['id' => $row_departments['departmentID'], 'value' => $row_departments['description']];
		}

		$writer = new thtml_writer($function);
		if ($function != 'update') {
			$writer->draw_input('displayName', 'Display Name', 'displayName', $this->DisplayName, 'User Name', true);
		}
		$writer->draw_input('firstName', 'First Name', 'firstName', $this->firstName, 'First Name');
		$writer->draw_input('lastName', 'Last Name', 'lastName', $this->lastName, 'Last Name');
		$writer->draw_input('title', 'Title', 'title', $this->title, 'Title');
		$writer->draw_input('cellPhone', 'Cell Phone', 'cellPhone', $this->cellPhone, 'Cell Phone');
		$writer->draw_input('homePhone', 'Home Phone', 'homePhone', $this->homePhone, 'Work Phone');
		$writer->draw_input('workEmail', 'Work Email', 'workEmail', $this->workEmail, 'Work Email');
		$writer->draw_select('groupID', 'Group', 'groupID', $this->str_group, $array_groups, $this->groupID);
		$writer->draw_select('departmentID', 'Department', 'departmentID', $this->str_department, $array_departments, $this->departmentID);
		$writer->draw_check('engineer', 'Engineer', 'engineer', 'y', 'Engineer', ($this->engineer == 'y'));
		$writer->draw_check('manager', 'Manager', 'manager', 't', 'Manager', ($this->manager == 't'));
		$writer->draw_date('hireDate', 'Hire Date', 'hireDate', $function == 'add' ? date('Y-m-d') : $this->hireDate);
		$writer->draw_check('active', 'Active', 'active', 't', 'Active', ($this->active == 't'));
		$writer->draw_fileupload('user_photo', 'Employee Portrait', 'user_photo', "user_photo{$this->EmployeeID}.jpg");
	}

	function get_employeeID() {
		return $this->EmployeeID;
	}

	function chet() {
		return $this->DisplayName;
	}

	function add() {
		$errors = array();
		$temp = false;
		$result = $this->conn->query("SELECT count(*) FROM employees WHERE displayName='{$this->DisplayName}'");
		$row = $result->fetch_row();
		if ($row[0] > 0) {
			$errors[] = "There's already one user with the specified username. please return and specify another.";
		} else {
			$this->conn->query("INSERT INTO employees (firstName, lastName, displayName, title, cellPhone, homePhone, workEmail, groupID, departmentID, engineer, manager"
				. ", hireDate, active) VALUES ('{$this->firstName}', '{$this->lastName}', '{$this->DisplayName}', '{$this->title}', '{$this->cellPhone}', '{$this->homePhone}'"
				. ", '{$this->workEmail}', {$this->groupID}, {$this->departmentID}, '{$this->engineer}', '{$this->manager}', '{$this->hireDate}', '{$this->active}')");
			$rs_last_employeeID = $this->conn->query("SELECT MAX(employeeID) FROM employees");
			$row = $rs_last_employeeID->fetch_row();
			$this->EmployeeID = $row[0];
			$check = getimagesize($_FILES['user_photo']['tmp_name']);
			if ($check !== false) {
				move_uploaded_file($_FILES['user_photo']['tmp_name'], "../images/user_photo{$this->EmployeeID}.jpg");
			}
			$temp = true;
		}
		$this->error = implode('\n', $errors);
		return $temp;
	}

	function update() {
		$this->conn->query("UPDATE employees SET firstName='{$this->firstName}', lastName='{$this->lastName}', title='{$this->title}'"
			. ", cellPhone='{$this->cellPhone}', homePhone='{$this->homePhone}', workEmail='{$this->workEmail}', groupID={$this->groupID}, departmentID={$this->departmentID}"
			. ", engineer='{$this->engineer}', manager='{$this->manager}', hireDate='{$this->hireDate}', active='{$this->active}' WHERE employeeID='{$this->EmployeeID}'");
		$check = getimagesize($_FILES['user_photo']['tmp_name']);
		if ($check !== false) {
			move_uploaded_file($_FILES['user_photo']['tmp_name'], "../images/user_photo{$this->EmployeeID}.jpg");
		}
	}

}
