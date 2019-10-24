<?php
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
with this program. If not, see <http://www.gnu.org/licenses/> or write to:
Free Software  HHIMS
ICT Agency,
160/24, Kirimandala Mawatha,
Colombo 05, Sri Lanka
---------------------------------------------------------------------------------- 
Author: Author: Mr. Jayanath Liyanage   jayanathl@icta.lk
                 
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/

	include_once("header.php");	///loads the html HEAD section (JS,CSS)

?>
<?php echo Modules::run('menu'); //runs the available menu option to that usergroup ?>

	<div class="container" style="width:95%;">
		<div class="row" style="margin-top:55px;">
		  <div class="col-md-2 ">
			<?php echo Modules::run('leftmenu/appointment'); //runs the available left menu for preferance ?>
		  </div>
		  <div class="col-md-5 ">
			<div class="panel panel-primary"  >
			<div class="panel-heading"><b>Appointment token</b>
			</div>
				<div style="padding:10px;">
				<?php
					echo "Patient : ".$patient_info["Personal_Title"].' '.$patient_info["Full_Name_Registered"].' ' .$patient_info["Personal_Used_Name"]."<br>";
					echo "Appointment date : ".$appointment_info["VDate"]."<br>";
					echo "Token type : ".$appointment_info["Type"]."<br>";
					if ((isset($dr_info))){
						echo "Doctor : ".$dr_info["Title"].' '.$dr_info["FirstName"].' '.$dr_info["OtherName"]. "<br>";
                    }
                    if ((isset($room_info))){
						echo "Room : ".$room_info["room_number"]."<br>";
                    }
                    echo "Status : <b>".$appointment_info["status"]."</b><br>";
					echo "<b>TOKEN NO : ".$appointment_info["Token"];
					if ($appointment_info["app_type"] == "VIP"){
						echo "<br><div class='label label-danger'>".$appointment_info["app_type"]."</div>";
					}
					else{
						echo "<br><div class='label label-info'>".$appointment_info["app_type"]."</div>";
					}
					echo "<hr>";
                    
				?>


<!--                    <button type="button" onclick="window.location=--><?php //echo site_url("report/pdf/patientSlip/print/".$this->uri->segment(3)); ?><!--" class="btn btn-primary">Print token</button>
					<a  class="btn btn-success" href="">Call again</a>-->
					<?php
						if (($appointment_info["status"] == "CALL")){
							echo '<a  class="btn btn-success btn-sm" href="'.site_url('appointment/mark_serverd/'.$appointment_info["APPID"]).'">Mark as served</a>';
							echo '<a  class="btn btn-danger btn-sm" href="'.site_url('qdisplay/mark_skipped/'.$appointment_info["APPID"]).'">Mark as skipped</a>';
                    }
                    elseif($appointment_info["status"] == ""){
                        echo '<a  class="btn btn-success btn-sm" href="'.site_url('appointment/mark_serverd/'.$appointment_info["APPID"]).'">Mark as served</a>';

                    }
                    else{
							echo '<a  class="btn btn-warning btn-sm" href="'.site_url('appointment/mark_wait/'.$appointment_info["APPID"]).'">Mark as waiting</a>';
						}
					?>		
						
                    <a  class="btn btn-default" href="<?php echo site_url('patient/view/'.$patient_info["PID"]);?>">Overview</a>
                    <a  class="btn btn-default" href="<?php echo site_url('appointment/?mid=16');?>">Back to list</a>
				</div>
			</div>
			</div>
		</div>
	</div>