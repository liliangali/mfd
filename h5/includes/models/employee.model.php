<?php
class EmployeeModel extends BaseModel
{
	var $table  = 'employee';
	var $prikey = 'idemployee';
	var $_name  = 'employee';

	
	function check_job_number($job_number,$idserve){
        return count($this->find(array('conditions' => 'idserve='.$idserve.' and job_number =\''.$job_number.'\''))) == 0;
    }
    
	function check_employee_name($employee_name,$idserve){
        return count($this->find(array('conditions' => 'idserve='.$idserve.' and employee_name =\''.$employee_name.'\''))) == 0;
    	
    }
}



