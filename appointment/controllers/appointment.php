<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
--------------------------------------------------------------------------------
HHIMS - Hospital Health Information Management System
Copyright (c) 2011 Information and Communication Technology Agency of Sri Lanka
<http: www.hhims.org/>
----------------------------------------------------------------------------------
This program is free software: you can redistribute it and/or modify it under the
terms of the GNU Affero General Public License as published by the Free Software 
Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,but WITHOUT ANY 
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License along 
with this program. If not, see <http://www.gnu.org/licenses/> 




---------------------------------------------------------------------------------- 
Date : June 2016
Author: Mr. Jayanath Liyanage   jayanathl@icta.lk

Programme Manager: Shriyananda Rathnayake
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/
class Appointment extends MX_Controller {
	 function __construct(){
		parent::__construct();
		$this->checkLogin();
        if(isset($_GET["mid"])){
			$this->session->set_userdata('mid', $_GET["mid"]);
		}
		$this->load->library('session');
		$this->load->helper('text');
	 }

	public function index()
	{
	
		$this->appointment_list(null);
    }

    public function remove_doctor($opd_room_doctor_id,$room_id){
        $this->load->model('mpersistent');
        $this->mpersistent->delete($opd_room_doctor_id,"opd_room_doctor","opd_room_doctor_id");
        header("Location: ".base_url()."index.php/appointment/room_info/".$room_id); 
    }
    
	public function mark_serverd($appid){
		if(!isset($appid) ||(!is_numeric($appid) )){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		$this->load->model('mpersistent');
		$sve_data = array(
			"status"=>"SERVED"
		);
        $res = $this->mpersistent->update("appointment","APPID",$appid,$sve_data );
		$this->process($appid);
	}
	    

	
	public function mark_wait($appid){
		if(!isset($appid) ||(!is_numeric($appid) )){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		$this->load->model('mpersistent');
		$sve_data = array(
			"status"=>NULL,
			"opd_room_id"=>NULL,
			"Consultant"=>NULL
		);
        $res = $this->mpersistent->update("appointment","APPID",$appid,$sve_data );
		$this->process($appid);
	}
	public function call_again($appid){
		if(!isset($appid) ||(!is_numeric($appid) )){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		$this->load->model('mpersistent');
		$sve_data = array(
			"status"=>"CALL"
		);
        $res = $this->mpersistent->update("appointment","APPID",$appid,$sve_data );
		$this->process($appid);
	}
    public function process($appid){
		$data = array();
		if(!isset($appid) ||(!is_numeric($appid) )){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		$this->load->model('mpersistent');
        $data["appointment_info"] = $this->mpersistent->open_id($appid,"appointment", "APPID");
		if (empty($data["appointment_info"])){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		
		 
        $data["patient_info"] = $this->mpersistent->open_id($data["appointment_info"]["PID"],"patient", "PID");
		if (empty($data["patient_info"])){
			$data["error"] ="patient_info  not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');
			return;
		}
		if ($data["appointment_info"]["Consultant"]){
			$data["dr_info"] = $this->mpersistent->open_id($data["appointment_info"]["Consultant"],"user", "UID");
		}
		else{
			$data["dr_info"]  = null;
        }
        if ($data["appointment_info"]["opd_room_id"]){
			$data["room_info"] = $this->mpersistent->open_id($data["appointment_info"]["opd_room_id"],"opd_room", "opd_room_id");
		}
		else{
			$data["room_info"]  = null;
		}
        $this->load->vars($data);
		$this->load->view('appointment_process_view');
    }
    public function room_info($room_id=null){
        $data = array();
		if(!isset($room_id) ||(!is_numeric($room_id) )){
			$data["error"] = "Room not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		$this->load->model('mpersistent');
        $this->load->model('mappointment');
        $data["room_info"] = $this->mpersistent->open_id($room_id,"opd_room", "opd_room_id");
        $data["doctors_list"] = $this->mappointment->get_doctors_list($room_id);
		if (empty($data["room_info"])){
			$data["error"] = "room_info not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}    
         $this->load->vars($data);
		$this->load->view('opd_room_info');
        
    }
    public function load(){
               $path='application/forms/opd_room.php';
        require $path;
        $frm = $form;
        $columns = $frm["LIST"];
        $table = $frm["TABLE"];
        $sql = "SELECT ";

        foreach ($columns as $column) {
            $sql.=$column . ',';
        }
        $sql = substr($sql, 0, -1);
        $sql.=" FROM $table ";
        $this->load->model('mpager');
        $this->mpager->setSql($sql);
        $this->mpager->setDivId('prefCont');
        $this->mpager->setSortorder('asc');
        //set colun headings
        $colNames = array();
        foreach ($frm["DISPLAY_LIST"] as $colName) {
            array_push($colNames, $colName);
        }
        $this->mpager->setColNames($colNames);

        //set captions
        $this->mpager->setCaption($frm["CAPTION"]);
        //set row id
        $this->mpager->setRowid($frm["ROW_ID"]);

        //set column models
        foreach ($frm["COLUMN_MODEL"] as $columnName => $model) {
            if (gettype($model) == "array") {
                $this->mpager->setColOption($columnName, $model);
            }
        }

        //set actions
        $action = $frm["ACTION"];
        $this->mpager->gridComplete_JS = "function() {
            var c = null;
            $('.jqgrow').mouseover(function(e) {
                var rowId = $(this).attr('id');
                c = $(this).css('background');
                $(this).css({'background':'yellow','cursor':'pointer'});
            }).mouseout(function(e){
                $(this).css('background',c);
            }).click(function(e){
                var rowId = $(this).attr('id');
                window.location='".base_url()."index.php/appointment/room_info/'+rowId;
            });
            }";

        //report starts
        if(isset($frm["ORIENT"])){
            $this->mpager->setOrientation_EL($frm["ORIENT"]);
        }
        if(isset($frm["TITLE"])){
            $this->mpager->setTitle_EL($frm["TITLE"]);
        }

//        $pager->setSave_EL($frm["SAVE"]);
        $this->mpager->setColHeaders_EL(isset($frm["COL_HEADERS"])?$frm["COL_HEADERS"]:$frm["DISPLAY_LIST"]);
        //report endss

        $data['pager']=$this->mpager->render(false);
        $data["pre_page"] = 'opd_room';
        $this->load->vars($data);
		$this->load->view('opd_room_list');
//        return "<h1>$sql";
    }
    
        public function opd_queue(){
        if($this->session->userdata("UserGroup")!='Programmer'){
            	$data["error"] = "You Don't have Permission";
		$this->load->vars($data);
		$this->load->view('appointment_error');	
		return;
            
        }
            
        $path='application/forms/opd_queue.php';
        require $path;
        $frm = $form;
        $columns = $frm["LIST"];
        $table = $frm["TABLE"];
        $sql = "SELECT ";

        foreach ($columns as $column) {
            $sql.=$column . ',';
        }
        $sql = substr($sql, 0, -1);
        $sql.=" FROM $table ";
        $this->load->model('mpager');
        $this->mpager->setSql($sql);
        $this->mpager->setDivId('prefCont');
        $this->mpager->setSortorder('asc');
        //set colun headings
        $colNames = array();
        foreach ($frm["DISPLAY_LIST"] as $colName) {
            array_push($colNames, $colName);
        }
        $this->mpager->setColNames($colNames);

        //set captions
        $this->mpager->setCaption($frm["CAPTION"]);
        //set row id
        $this->mpager->setRowid($frm["ROW_ID"]);

        //set column models
        foreach ($frm["COLUMN_MODEL"] as $columnName => $model) {
            if (gettype($model) == "array") {
                $this->mpager->setColOption($columnName, $model);
            }
        }

        //set actions
        $action = $frm["ACTION"];
        $this->mpager->gridComplete_JS = "function() {
            var c = null;
            $('.jqgrow').mouseover(function(e) {
                var rowId = $(this).attr('id');
                c = $(this).css('background');
                $(this).css({'background':'yellow','cursor':'pointer'});
            }).mouseout(function(e){
                $(this).css('background',c);
            }).click(function(e){
                var rowId = $(this).attr('id');
                window.location='".base_url()."index.php/appointment/room_info/'+rowId;
            });
            }";

        //report starts
        if(isset($frm["ORIENT"])){
            $this->mpager->setOrientation_EL($frm["ORIENT"]);
        }
        if(isset($frm["TITLE"])){
            $this->mpager->setTitle_EL($frm["TITLE"]);
        }

//        $pager->setSave_EL($frm["SAVE"]);
        $this->mpager->setColHeaders_EL(isset($frm["COL_HEADERS"])?$frm["COL_HEADERS"]:$frm["DISPLAY_LIST"]);
        //report endss

        $data['pager']=$this->mpager->render(false);
        $data["pre_page"] = 'opd_room';
        $this->load->vars($data);
		$this->load->view('opd_room_list');
//        return "<h1>$sql";
    }
	
	public function appointment_list($dte = null){
       $qry = "SELECT appointment.APPID as APPID, patient.HIN as HIN,CONCAT(patient.Full_Name_Registered,' ', patient.Personal_Used_Name) ,appointment.Type as Type,appointment.Token   , 
			appointment.CreateDate ,appointment.app_type,appointment.status, opd_room.room_number  ,opd_room_doctor.doctor_number
            from appointment 
			LEFT JOIN `patient` ON patient.PID = appointment.PID 
            LEFT JOIN `opd_room` ON opd_room.opd_room_id = appointment.opd_room_id 
            LEFT JOIN `opd_room_doctor` ON opd_room_doctor.doctor_id = appointment.Consultant 
            
            where appointment.CreateDate like '".date("Y-m-d")."%' 
			";
        $this->load->model('mpager',"visit_page");
        $visit_page = $this->visit_page;
        $visit_page->setSql($qry);
        $visit_page->setDivId("patient_list"); //important
        $visit_page->setDivClass('');
        $visit_page->setRowid('APPID');
        $visit_page->setCaption("Previous visits");
        //$visit_page->setShowHeaderRow(false);
       // $visit_page->setShowFilterRow(false);
        //$visit_page->setShowPager(false);
        $visit_page->setColNames(array("","HIN","Patient ", "Type", "Token Number","Date ","Mode","Status","Room","Seat No"));
        $visit_page->setRowNum(25);
        $visit_page->setColOption("APPID", array("search" => false, "hidden" => true));
		$visit_page->setColOption("HIN", array("search" => true, "hidden" => false, "width" => 70));
        //$visit_page->setColOption("patient_name", array("search" => true, "hidden" => false));
        $visit_page->setColOption("Token", array("search" => true, "hidden" => false));
		//GIHAN this is not working?
        //$visit_page->setColOption("CreateDate", $visit_page->getDateSelector(date("Y-m-d")));
        $visit_page->setColOption("Type", array("search" => false, "hidden" => false, "width" => 75));
        $visit_page->gridComplete_JS
            = "function() {
        $('#patient_list .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            window.location='".site_url("/appointment/process")."/'+rowId;
        });
        }";
		//var prn = window.open('".site_url("/appointment/open")."/'+rowId,'prn','width='+$(document).width()+',height=600,top=200,left=200');
			//prn.focus();
        $visit_page->setOrientation_EL("L");
		$data['pager'] = $visit_page->render(false);
		$this->load->vars($data);
		$this->load->view('appointment_search');	
}

	public function patient(){
       $qry = "SELECT clinic_patient.PID as PID, clinic_patient.clinic_patient_id,CONCAT(patient.Full_Name_Registered,' ', patient.Personal_Used_Name)  , clinic.name as clinic_name, 
			next_visit_date from clinic_patient 
			LEFT JOIN `patient` ON patient.PID = clinic_patient.PID 
			LEFT JOIN `clinic` ON clinic.clinic_id = clinic_patient.clinic_id 
			";
        $this->load->model('mpager',"visit_page");
        $visit_page = $this->visit_page;
        $visit_page->setSql($qry);
        $visit_page->setDivId("patient_list"); //important
        $visit_page->setDivClass('');
        $visit_page->setRowid('clinic_patient_id');
        $visit_page->setCaption("Previous visits");
        $visit_page->setShowHeaderRow(false);
        $visit_page->setShowFilterRow(false);
        $visit_page->setShowPager(false);
        $visit_page->setColNames(array("","ID", "Patient", "Next visit date","Clinic "));
        $visit_page->setRowNum(25);
        $visit_page->setColOption("PID", array("search" => false, "hidden" => true));
        $visit_page->setColOption("clinic_patient_id", array("search" => false, "hidden" => true));
        //$visit_page->setColOption("patient_name", array("search" => true, "hidden" => false));
        $visit_page->setColOption("next_visit_date", array("search" => false, "hidden" => false, "width" => 75));
        $visit_page->setColOption("clinic_name", array("search" => false, "hidden" => false, "width" => 75));
        $visit_page->gridComplete_JS
            = "function() {
        $('#patient_list .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            window.location='".site_url("/appointment/open")."/'+rowId;
        });
        }";
        $visit_page->setOrientation_EL("L");
		$data['pager'] = $visit_page->render(false);
		$this->load->vars($data);
        $this->load->view('clinic_search');	
       
	}	
	public function save(){
		$this->load->model('mpersistent');
		$this->load->model('mappointment');
		$this->load->helper('form');
        $this->load->library('form_validation');
		
        $this->form_validation->set_rules("VDate", "VDate", "required|xss_clean");
        $this->form_validation->set_rules("Type", "Type", "required|xss_clean");
        //$this->form_validation->set_rules("Consultant", "Consultant", "required|xss_clean");
        $this->form_validation->set_rules("PID", "PID", "required|xss_clean");
		
        $data = array();
		$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
        if ($this->form_validation->run() == FALSE) {
            $this->load->vars($data);
            echo Modules::run('form/create', 'appointment');
        } else {
			$token = $this->mappointment->get_next_token($this->input->post("VDate"),$this->input->post("Type"));
			$sve_data = array(
					"VDate"=>$this->input->post("VDate"),
					"Type"=>$this->input->post("Type"),
					"app_type"=>$this->input->post("app_type"),
					//"Consultant"=>$this->input->post("Consultant"),
					"PID"=>$this->input->post("PID"),
					"Token"=>$token+1
				);
			$appid = $this->mpersistent->create("appointment", $sve_data);
			if ($appid>0){
				$this->open($appid);
					$new_page   =   base_url()."index.php/appointment/open/".$appid."?CONTINUE=".$this->input->post("CONTINUE")."";
					header("Status: 200");
					header("Location: ".$new_page);
			}
			else{
				$data["error"] ="save error";
				$this->load->vars($data);
				$this->load->view('appointment_error');
				return;
			}
		}
	}

	
	public function open($appid){
		$data = array();
		if(!isset($appid) ||(!is_numeric($appid) )){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		$this->load->model('mpersistent');
        $data["appointment_info"] = $this->mpersistent->open_id($appid,"appointment", "APPID");
		if (empty($data["appointment_info"])){
			$data["error"] = "Appointment not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');	
			return;
		}
		
		 
        $data["patient_info"] = $this->mpersistent->open_id($data["appointment_info"]["PID"],"patient", "PID");
		if (empty($data["patient_info"])){
			$data["error"] ="patient_info  not found";
			$this->load->vars($data);
			$this->load->view('appointment_error');
			return;
		}
		if ($data["appointment_info"]["Consultant"]){
			$data["dr_info"] = $this->mpersistent->open_id($data["appointment_info"]["Consultant"],"user", "UID");
			/*if (empty($data["dr_info"])){
				$data["error"] ="Doctor  not found";
				$this->load->vars($data);
				$this->load->view('appointment_error');
				return;
			}*/
		}
		else{
			$data["dr_info"]  = null;
		}
		$this->load->vars($data);
        $this->load->view('appointment_view');	
	}
	
} 


//////////////////////////////////////////

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */