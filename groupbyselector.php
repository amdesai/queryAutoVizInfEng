<div id="groupBy">
<!-- <link rel="stylesheet" type="text/css" href="css/mhs.css" /> -->
<br/>
<p>Change Auto-selected Group By Field:</p>
<table class="group_by_table">
	<tr>
		<td><select id="chooseGroupByField" name="GroupByField" onchange="updateGroupByField(this)">
				<option>Select group by field</option>
				<?php foreach ($groupByCandidateFields as $i => $gbcf):?>
				<option value=<?php echo $gbcf;?>>
					<?php echo $gbcf;?>
				</option>
				<?php endforeach;?>
		</select></td>
	</tr>
</table>
</div>