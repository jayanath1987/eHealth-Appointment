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
		  <div class="col-md-10 ">
          <div class="panel panel-primary"  >
					<div class="panel-heading"><b>Room info</b>
					<input type='button' class='btn btn-xs btn-warning pull-right' onclick=self.document.location='<?php echo site_url('form/edit/opd_room/'.$room_info['opd_room_id'].'/?CONTINUE=appointment/room_info/'.$room_info['opd_room_id'].''); ?>' value='Edit'>&nbsp;
					 </div>
                     <?php //var_dump($room_info); ?>
               <table class='table  table-striped table-condensed table-bordered' width=20%>
						
                          <tr>	
							<td width="50%"><b>
								Room number:
							</b></td>
							<td><?php echo $room_info['room_number']; ?>
                            </td>      
                          </tr>
                          <tr>	
							<td width="50%"><b>
								Type:
							</b></td>
							<td><?php echo $room_info['room_type']; ?>
                            </td>      
                          </tr>
                          <tr>	
							<td width="50%"><b>
								Description:
							</b></td>
							<td><?php echo $room_info['name']; ?>
                            </td>      
                          </tr>
               </table>   <br>
               <div class="panel panel-success"  >
					<div class="panel-heading"><b>Serving consultants/doctors information in this room  (<?php echo $room_info['room_number']; ?>)</b>
					 </div>
                     <?php 
                     //var_dump($doctors_list); 
                     ?>
                     <?php 
                        if(empty($doctors_list)){
                            echo "There is no Doctor/Consultant assigned to the room";
                        }
                        else{
                             echo '<table class="table  table-striped table-condensed table-bordered" width=20%>';
                              echo '<tr><th>Doctor</th><th>Serving #</th><th>Serving type</th>	</tr>';
                             for ($i=0; $i<count($doctors_list);++$i){
                                echo '<tr>	';
                                    echo '<td width="50%"><b>';
                                        echo $doctors_list[$i]["doctor"];
                                    echo '</b></td>';
                                     echo '<td width="10%"><b>';
                                        echo '<span class="label label-success">'.$doctors_list[$i]["doctor_number"].'</span>';
                                    echo '</b></td>';   
                                     echo '<td width="10%"><b>';
                                        echo '<span class="label label-info">'.$doctors_list[$i]["serving_type"].'</span>';
                                    echo '</b></td>';  
                                    echo '<td width="10%"><b>';
                                         echo '<input type="button" class="btn btn-xs btn-warning" onclick=self.document.location="'.site_url('form/edit/opd_room_doctor/'.$doctors_list[$i]['opd_room_doctor_id'].'/?CONTINUE=appointment/room_info/'.$room_info['opd_room_id']).'" value="Edit">&nbsp;';
                                         echo '<input type="button" class="btn btn-xs btn-danger" onclick=self.document.location="'.site_url('appointment/remove_doctor/'.$doctors_list[$i]['opd_room_doctor_id'].'/'.$room_info['opd_room_id']).'" value="Remove">&nbsp;';

                                    echo '</b></td>';
                                  echo '</tr>';
                             }
                            echo ' </table>  ';
                     }
                     ?>   
                     <br>
                 <input type='button' class='btn btn-xs btn-success ' onclick=self.document.location='<?php echo site_url('form/create/opd_room_doctor/'.$room_info['opd_room_id'].'/?CONTINUE=appointment/room_info/'.$room_info['opd_room_id'].''); ?>' value='Add doctor to this room'>&nbsp;
                    <br>
               </div>      
           </div>           
			<?php
            
            ?>
			</div>
		</div>
	</div>