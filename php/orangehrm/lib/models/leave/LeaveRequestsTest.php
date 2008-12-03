<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */


// Call LeaveRequestTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "LeaveRequestTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";

$_SESSION['WPATH'] = WPATH;

require_once 'LeaveRequests.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

/**
 * Test class for LeaveRequest.
 * Generated by PHPUnit_Util_Skeleton on 2006-12-28 at 05:15:40.
 */
class LeaveRequestsTest extends PHPUnit_Framework_TestCase {

	public $classLeaveRequest = null;
    public $connection = null;

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("LeaveRequestsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    	$this->classLeaveRequest = new LeaveRequests();

    	$conf = new Conf();

    	$this->connection = mysql_connect($conf->dbhost.":".$conf->dbport, $conf->dbuser, $conf->dbpass);

        mysql_select_db($conf->dbname);

        $this->_deleteTestData();

		$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, emp_lastname, emp_firstname, emp_nick_name, coun_code) " .
				"VALUES ('011', 'Arnold', 'Subasinghe', 'Arnold', 'AF')");
		$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, emp_lastname, emp_firstname, emp_middle_name, emp_nick_name) " .
				"VALUES ('012', 'Mohanjith', 'Sudirikku', 'Hannadige', 'MOHA')");
		$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, emp_lastname, emp_firstname, emp_middle_name, emp_nick_name) " .
				"VALUES ('013', 'Mohanjithx', 'Sudirikkux', 'Hannadigex', 'MOHAx')");
		$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, emp_lastname, emp_firstname, emp_middle_name, emp_nick_name) " .
				"VALUES ('014', 'Mohanjith1', 'Sudirikku1', 'Hannadige1', 'MOHA1')");
		$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, emp_lastname, emp_firstname, emp_middle_name, emp_nick_name) " .
				"VALUES ('015', 'Jack', 'Bauer', '', 'John')");

		mysql_query("INSERT INTO `hs_hr_emp_reportto` VALUES ('012', '011', 1);");

		mysql_query("INSERT INTO `hs_hr_leavetype` VALUES ('LTY010', 'Medical', 1)");

		mysql_query("TRUNCATE TABLE `hs_hr_weekends`;");
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (1, 0);"), mysql_error());
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (2, 0);"), mysql_error());
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (3, 0);"), mysql_error());
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (4, 0);"), mysql_error());
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (5, 0);"), mysql_error());
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (6, 0);"), mysql_error());
		$this->assertTrue(mysql_query("INSERT INTO `hs_hr_weekends` (day, length) VALUES (7, 0);"), mysql_error());

		//Leave 1
		mysql_query("INSERT INTO `hs_hr_leave_requests` (`leave_request_id`, `leave_type_id`, `leave_type_name`, `date_applied`, `employee_id`) VALUES (10, 'LTY010', 'Medical', '".date('Y-m-d', time()+3600*24)."', '011')");
		mysql_query("INSERT INTO `hs_hr_leave` (`leave_request_id`, `leave_id`, `employee_id`, `leave_type_id`, `leave_date`, `leave_length_days`, `leave_length_hours`, `leave_status`, `leave_comments`) VALUES (10, 10, '011', 'LTY010', '".date('Y-m-d', time()+3600*24)."', 0.12, 1, 1, 'Leave 1')");

		//Leave 2
		mysql_query("INSERT INTO `hs_hr_leave_requests` (`leave_request_id`, `leave_type_id`, `leave_type_name`, `date_applied`, `employee_id`) VALUES (11, 'LTY010', 'Medical', '".date('Y-m-d', time()+3600*24)."', '011')");
		mysql_query("INSERT INTO `hs_hr_leave` (`leave_request_id`, `leave_id`, `employee_id`, `leave_type_id`, `leave_date`, `leave_length_days`, `leave_length_hours`, `leave_status`, `leave_comments`) VALUES (11, 11, '011', 'LTY010', '".date('Y-m-d', time()+3600*24)."', 0.12, 1, 1, 'Leave 2-1')");
		mysql_query("INSERT INTO `hs_hr_leave` (`leave_request_id`, `leave_id`, `employee_id`, `leave_type_id`, `leave_date`, `leave_length_days`, `leave_length_hours`, `leave_status`, `leave_comments`) VALUES (11, 13, '011', 'LTY010', '".date('Y-m-d', time()+3600*24*2)."', 0.12, 1, 1, 'Leave 2-2')");

		mysql_query("INSERT INTO `hs_hr_leave_requests` (`leave_request_id`, `leave_type_id`, `leave_type_name`, `date_applied`, `employee_id`) VALUES (12, 'LTY010', 'Medical', '".date('Y-m-d', time()+3600*24)."', '015')");

        UniqueIDGenerator::getInstance()->initTable();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    	$this->_deleteTestData();
    }

	/**
	 * Deletes test data created during test
	 */
	private function _deleteTestData() {
    	mysql_query("DELETE FROM `hs_hr_emp_reportto` WHERE `erep_sup_emp_number` = '012' AND `erep_sub_emp_number` = '011'", $this->connection);

    	mysql_query("TRUNCATE TABLE `hs_hr_leave`");
    	mysql_query("TRUNCATE TABLE `hs_hr_leave_requests`");
    	mysql_query("TRUNCATE TABLE `hs_hr_weekends`;");

    	mysql_query("DELETE FROM `hs_hr_leavetype` WHERE `Leave_Type_ID` = 'LTY010'");

    	mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_number` = '011'");
    	mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_number` = '012'");
    	mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_number` = '013'");
    	mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_number` = '014'");
    	mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_number` = '015'");
    }

	/**
	 * Run given sql query
	 */
	private function _runQuery($sql) {
	    $this->assertTrue(mysql_query($sql), mysql_error());
	}

    public function testRetriveLeaveRequestsEmployee1() {
    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsEmployee('051');

    	$this->assertNull($res, 'Non exsistent record found');
    }

    public function testRetriveLeaveRequestsEmployee2() {
    	$leaveObj = $this->classLeaveRequest;
    	$employeeId = '011';

    	$res = $leaveObj->retriveLeaveRequestsEmployee($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(2, count($res), 'Wrong number of records found');

    	$expected[0] = array('10', 'Medical', date('Y-m-d', time()+3600*24), null);
    	$expected[1] = array('11', 'Medical', date('Y-m-d', time()+3600*24), date('Y-m-d', time()+3600*24*2));

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), 'Wrong To Date');
    	}
    }

    public function testRetriveLeaveRequestsSupervisor1() {
    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsSupervisor('051');

    	$this->assertNull($res, 'Non exsistent record found');
    }

    public function testRetriveLeaveRequestsSupervisor2() {
    	$leaveObj = $this->classLeaveRequest;
    	$employeeId = '012';

    	$res = $leaveObj->retriveLeaveRequestsSupervisor($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(2, count($res), 'Wrong number of records found');

    	$expected[0] = array('10', 'Medical', date('Y-m-d', time()+3600*24), null);
    	$expected[1] = array('11', 'Medical', date('Y-m-d', time()+3600*24), date('Y-m-d', time()+3600*24*2));

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), 'Wrong To Date');
    	}
    }

    /**
     * Tests that retrieveLeaveRequestsSupervisor only retrieves leave requests with the
     * statuses: Pending approval, approved and rejected
     */
    public function testRetriveLeaveRequestsSupervisorStatuses() {
    	$leaveObj = $this->classLeaveRequest;
    	$supervisorId = '012';

		// Change status to Pending approval
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL." WHERE leave_request_id = 10"), mysql_error());
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL." WHERE leave_request_id = 11"), mysql_error());
    	$res = $leaveObj->retriveLeaveRequestsSupervisor($supervisorId);

    	$this->assertNotNull($res, 'Record not found');
    	$this->assertSame(2, count($res), 'Wrong number of records found');

		// Change status to Rejected
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_REJECTED." WHERE leave_request_id = 10"), mysql_error());
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_REJECTED." WHERE leave_request_id = 11"), mysql_error());
    	$res = $leaveObj->retriveLeaveRequestsSupervisor($supervisorId);

    	$this->assertNotNull($res, 'Record not found');
    	$this->assertSame(2, count($res), 'Wrong number of records found');

		// Change status to Approved
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_APPROVED." WHERE leave_request_id = 10"), mysql_error());
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_APPROVED." WHERE leave_request_id = 11"), mysql_error());
    	$res = $leaveObj->retriveLeaveRequestsSupervisor($supervisorId);

    	$this->assertNotNull($res, 'Record not found');
    	$this->assertSame(2, count($res), 'Wrong number of records found');

		// Change one leave request's status to 'Partly Approved'11, 13
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_APPROVED." WHERE leave_id = 11"), mysql_error());
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_REJECTED." WHERE leave_id = 13"), mysql_error());
    	$res = $leaveObj->retriveLeaveRequestsSupervisor($supervisorId);

    	$this->assertNotNull($res, 'Record not found');
    	$this->assertSame(2, count($res), 'Wrong number of records found');

		// Change status to Cancelled - not shown
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_CANCELLED." WHERE leave_request_id = 10"), mysql_error());
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_CANCELLED." WHERE leave_request_id = 11"), mysql_error());
    	$res = $leaveObj->retriveLeaveRequestsSupervisor($supervisorId);

    	$this->assertNull($res, 'Should not return any results');


		// Change status to Taken - not shown
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_TAKEN." WHERE leave_request_id = 10"), mysql_error());
		$this->assertTrue(mysql_query("UPDATE `hs_hr_leave` SET `leave_status`=". Leave::LEAVE_STATUS_LEAVE_TAKEN." WHERE leave_request_id = 11"), mysql_error());
    	$res = $leaveObj->retriveLeaveRequestsSupervisor($supervisorId);

    	$this->assertNull($res, 'Should not return any results');

    }


    public function testApplyLeave1() {
    	$employeeId = '012';

        $this->classLeaveRequest->setEmployeeId($employeeId);
    	$this->classLeaveRequest->setLeaveTypeId("LTY010");
    	$this->classLeaveRequest->setLeaveFromDate(date('Y-m-d', time()+3600*24));
    	$this->classLeaveRequest->setLeaveToDate(date('Y-m-d', time()+3600*24));
    	$this->classLeaveRequest->setLeaveLengthHours($this->classLeaveRequest->lengthFullDay);
    	$this->classLeaveRequest->setLeaveStatus("1");
    	$this->classLeaveRequest->setLeaveComments("New Leave 1");

    	$this->classLeaveRequest->applyLeaveRequest();
    	$newId = $this->classLeaveRequest->getLeaveRequestId();

    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsEmployee($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(1, count($res), 'Wrong number of records found');

    	$expected[0] = array($newId, 'Medical', date('Y-m-d', time()+3600*24), null, '8.00', '1.00');

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), 'Wrong To Date');
    		$this->assertSame($expected[$i][4], $res[$i]->getLeaveLengthHours(), "Wrong length(hours)");
    		$this->assertEquals($expected[$i][5], $res[$i]->getNoDays(), "Wrong length(days)");
    	}
    }

    public function testApplyLeave2() {
    	$employeeId = '012';

        $this->classLeaveRequest->setEmployeeId($employeeId);
    	$this->classLeaveRequest->setLeaveTypeId("LTY010");
    	$this->classLeaveRequest->setLeaveFromDate(date('Y-m-d', time()+3600*24));
    	$this->classLeaveRequest->setLeaveToDate(date('Y-m-d', time()+3600*24*3));
    	$this->classLeaveRequest->setLeaveLengthHours($this->classLeaveRequest->lengthFullDay);
    	$this->classLeaveRequest->setLeaveStatus("1");
    	$this->classLeaveRequest->setLeaveComments("New Leave 1");

    	$this->classLeaveRequest->applyLeaveRequest();
    	$newId = $this->classLeaveRequest->getLeaveRequestId();

    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsEmployee($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(1, count($res), 'Wrong number of records found');

    	$expected[0] = array($newId, 'Medical', date('Y-m-d', time()+3600*24), date('Y-m-d', time()+3600*24*3), '24.00', '3.00');

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), 'Wrong To Date');
    		$this->assertSame($expected[$i][4], $res[$i]->getLeaveLengthHours(), "Wrong length(hours)");
    		$this->assertSame($expected[$i][5], $res[$i]->getNoDays(), "Wrong length(days)");
    	}
    }

    public function testApplyLeave3() {

		// Mark Sunday as weekend
    	$this->assertTrue(mysql_query("UPDATE `hs_hr_weekends` SET length=8 WHERE day=7"), mysql_error());

    	$employeeId = '012';

    	$this->classLeaveRequest = null;
    	$this->classLeaveRequest = new LeaveRequests();

        $this->classLeaveRequest->setEmployeeId($employeeId);
    	$this->classLeaveRequest->setLeaveTypeId("LTY010");
    	$this->classLeaveRequest->setLeaveFromDate(date('Y-m-d', time()+3600*24));
    	$this->classLeaveRequest->setLeaveToDate(date('Y-m-d', time()+3600*24*7));
    	$this->classLeaveRequest->setLeaveLengthHours($this->classLeaveRequest->lengthFullDay);
    	$this->classLeaveRequest->setLeaveStatus("1");
    	$this->classLeaveRequest->setLeaveComments("New Leave 1");

    	$this->classLeaveRequest->applyLeaveRequest();
    	$newId = $this->classLeaveRequest->getLeaveRequestId();

    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsEmployee($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(1, count($res), 'Wrong number of records found');

    	$expected[0] = array($newId, 'Medical', date('Y-m-d', time()+3600*24), date('Y-m-d', time()+3600*24*7), '48.00', '6.00');

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), "Wrong To Date");
    		$this->assertSame($expected[$i][4], $res[$i]->getLeaveLengthHours(), "Wrong length(hours) {$expected[$i][4]} {$res[$i]->getLeaveLengthHours()}");
    		$this->assertSame($expected[$i][5], $res[$i]->getNoDays(), "Wrong length(days)");
    	}
    }

    public function testApplyLeave4() {

		// Mark Saturday and Sunday as weekend
    	$this->assertTrue(mysql_query("UPDATE `hs_hr_weekends` SET length=8 WHERE day=7 OR day=6"), mysql_error());

    	$employeeId = '012';

    	$this->classLeaveRequest = null;
    	$this->classLeaveRequest = new LeaveRequests();

        $this->classLeaveRequest->setEmployeeId($employeeId);
    	$this->classLeaveRequest->setLeaveTypeId("LTY010");
    	$this->classLeaveRequest->setLeaveFromDate(date('Y-m-d', time()+3600*24));
    	$this->classLeaveRequest->setLeaveToDate(date('Y-m-d', time()+3600*24*7));
    	$this->classLeaveRequest->setLeaveLengthHours($this->classLeaveRequest->lengthFullDay);
    	$this->classLeaveRequest->setLeaveStatus("1");
    	$this->classLeaveRequest->setLeaveComments("New Leave 1");

    	$this->classLeaveRequest->applyLeaveRequest();
    	$newId = $this->classLeaveRequest->getLeaveRequestId();

    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsEmployee($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(1, count($res), 'Wrong number of records found');

    	$expected[0] = array($newId, 'Medical', date('Y-m-d', time()+3600*24), date('Y-m-d', time()+3600*24*7), '40.00', '5.00');

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), 'Wrong To Date');
    		$this->assertSame($expected[$i][4], $res[$i]->getLeaveLengthHours(), "Wrong length(hours) {$expected[$i][4]} {$res[$i]->getLeaveLengthHours()}");
    		$this->assertSame($expected[$i][5], $res[$i]->getNoDays(), "Wrong length(days)");
    	}
    }

    public function testApplyLeave5() {


		// Mark Saturday as half day
    	$this->assertTrue(mysql_query("UPDATE `hs_hr_weekends` SET length=4 WHERE day=6"), mysql_error());
    	// Mark Sunday as weekend
    	$this->assertTrue(mysql_query("UPDATE `hs_hr_weekends` SET length=8 WHERE day=7"), mysql_error());

    	$employeeId = '012';

    	$this->classLeaveRequest = null;
    	$this->classLeaveRequest = new LeaveRequests();

        $this->classLeaveRequest->setEmployeeId($employeeId);
    	$this->classLeaveRequest->setLeaveTypeId("LTY010");
    	$this->classLeaveRequest->setLeaveFromDate(date('Y-m-d', time()+3600*24));
    	$this->classLeaveRequest->setLeaveToDate(date('Y-m-d', time()+3600*24*7));
    	$this->classLeaveRequest->setLeaveLengthHours($this->classLeaveRequest->lengthFullDay);
    	$this->classLeaveRequest->setLeaveStatus("1");
    	$this->classLeaveRequest->setLeaveComments("New Leave 1");

    	$this->classLeaveRequest->applyLeaveRequest();
    	$newId = $this->classLeaveRequest->getLeaveRequestId();

    	$leaveObj = $this->classLeaveRequest;

    	$res = $leaveObj->retriveLeaveRequestsEmployee($employeeId);

    	$this->assertNotNull($res, 'Record not found');

    	$this->assertSame(1, count($res), 'Wrong number of records found');

    	$expected[0] = array($newId, 'Medical', date('Y-m-d', time()+3600*24), date('Y-m-d', time()+3600*24*7), '44.00', '5.50');

    	for ($i=0; $i<count($res); $i++) {
    		$this->assertSame($expected[$i][0], $res[$i]->getLeaveRequestId(), 'Wrong Leave Request Id');
    		$this->assertSame($expected[$i][1], $res[$i]->getLeaveTypeName(), 'Wrong Leave Type Name');
    		$this->assertSame($expected[$i][2], $res[$i]->getLeaveFromDate(), 'Wrong From Date');
    		$this->assertSame($expected[$i][3], $res[$i]->getLeaveToDate(), 'Wrong To Date');
    		$this->assertSame($expected[$i][4], $res[$i]->getLeaveLengthHours(), "Wrong length(hours) {$expected[$i][4]} {$res[$i]->getLeaveLengthHours()}");
    		$this->assertSame($expected[$i][5], $res[$i]->getNoDays(), "Wrong length(days)");
    	}
    }

}
// Call LeaveRequestTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "LeaveRequestsTest::main") {
    LeaveRequestsTest::main();
}
?>
