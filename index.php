<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<div class="form-group container">
<?php 
	// $bizName is obtained from the page itself. This is just a stub.
	$bizName = 'Perperook';
?>
	<table id='open-hours-entry' class="table">
			<caption> Open Hours</caption>
		<tr>
			<td>
				<select class="form-control " name='day' id='day'>
					<?php for($i=0 ; $i<7;$i++){ ?>
					<option class='drop-down' value='<?php echo jddayofweek($i,1);?>'> <?php echo jddayofweek($i,1);?> </option>	
					<?php }?>		
				</select>
			</td>
			<td>
				<select  class="form-control " name='start-shift' id='start-shift'>
					<?php for($i=0 ; $i<24;$i++){ ?>
					<option class='drop-down' value='<?php echo date("h:00 A",mktime($i,0,0,0,0,0));?>'> <?php echo date("h :00 A",mktime($i,0,0,0,0,0));?> </option>	
					<?php }?>		
				</select>
			</td>
			<td>
				<select  class="form-control" name='end-shift' id='end-shift'>
					<?php for($i=0 ; $i<24;$i++){ ?>
					<option class='drop-down' value='<?php echo date("h:00 A",mktime($i,0,0,0,0,0));?>'> <?php echo date("h :00 A",mktime($i,0,0,0,0,0));?> </option>	
					<?php }?>		
				</select>
			</td>
			<td>
				<button  class="form-control" id='add-hours-button' class='button btn btn-default'>Add Hours</button>
			</td>
		</tr>
	</table>

	<div id='open-hours-schedule'></div>

	<script type="text/javascript">

		$(document).ready(function(){
			/*As soon as the page loads, the list of already entered shifts whould be visible*/
			$.post('openHours.php',{bizName:'<?php echo $bizName; ?>',operation:'display'},response);

			function response(result){
				var response = JSON.parse(result);
				if(response.success == 'false'){
					alert(response.message);
				}else{
					var table_data = '<table class="table table-striped">' +
								'<caption> Open Hours Schedule</caption>' +
							    '<thead>' + 
							      '<tr>' + 
							        '<th>Day</th>' + 
							        '<th>Start Shift</th>' + 
							        '<th>End Shift</th>' + 
							        '<th> Remove </th>' + 
							     ' </tr>' +
							    '</thead>' +
							    '<tbody>';
					$.each(response.result,function(index,value){
					table_data += 	'<tr>' +
								'<td class="individual-shift-day">' + response.result[index].day + "</td>" +
								'<td class="individual-shift-start_hour">' + response.result[index].start_hour + "</td>" + 
								'<td class="individual-shift-end_hour">' + response.result[index].end_hour + "</td>"+
								"<td><button class='remove-hours-button btn btn-default min-btn-padding'>remove</button></td>" +
							'</tr>';


					});

					table_data += '</tbody></table>';
					$('#open-hours-schedule').html(table_data);

				}
			}

			/*Add Hours to the database*/
			$('#add-hours-button').on('click',function(){
				var day = $('#day').val();
				var start_shift = $('#start-shift').val();
				var end_shift = $('#end-shift').val();
				var bizName = '<?php echo $bizName;?>';

				$.post('openHours.php',{day:day,
										start_shift:start_shift,
										end_shift:end_shift,
										bizName:bizName,
										operation:'addition'},response);

			});

			/*REFER : http://stackoverflow.com/questions/6537323/jquery-function-not-binding-to-newly-added-dom-elements*/

			/*Remove hourse from Database*/
			$(document).on('click','.remove-hours-button',function(){
				console.log($(this).parent());
				var bizName = '<?php echo $bizName;?>';
				var day = $(this).parent().siblings('.individual-shift-day').text();
				var start_shift = $(this).parent().siblings('.individual-shift-start_hour').text();
				var end_shift = $(this).parent().siblings('.individual-shift-end_hour').text();

				$.post('openHours.php',{day:day,
								start_shift:start_shift,
								end_shift:end_shift,
								bizName:bizName,
								operation:'deletion'},response);
			});

			/*Adjust size of 'Add Hours' button text*/
			$(window).on('resize load',function(){
				if(window.innerWidth < 500){
					$('#add-hours-button').css('font-size','8px');	
				}else{
					$('#add-hours-button').css('font-size','14px');
				}
			});
		});

	</script>
</div>