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

	function __construct($conn = null, $id = 0) {
		$this->conn = $conn;
		$this->EmployeeID = $id;
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

		$writer = new thtml_writer($function);
		$writer->draw_input('firstName', 'First Name', 'firstName', $this->firstName);
		$writer->draw_input('lastName', 'Last Name', 'lastName', $this->lastName);
		$writer->draw_input('displayName', 'Display Name', 'displayName', $this->DisplayName, true);
		$writer->draw_input('title', 'Title', 'title', $this->title);
		$writer->draw_input('cellPhone', 'Cell Phone', 'cellPhone', $this->cellPhone);
		$writer->draw_input('homePhone', 'Home Phone', 'homePhone', $this->homePhone);
		$writer->draw_input('workEmail', 'Work Email', 'workEmail', $this->workEmail);
		$writer->draw_select('groupID', 'Group', 'groupID', $this->str_group, $array_groups, $this->groupID);
	}

}
