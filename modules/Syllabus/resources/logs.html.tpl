<div class="container-fluid pt-3">
	<h1 class="pb-2">Syllabus Access Logs</h1>
	<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
	<p>{$courseSection->getFullSummary()}</p>


	<h2>Users who have viewed the syllabus</h2>
	<table class="table table-sm table-striped mb-3">
		<thead class="thead-dark">
			<tr>
				<th>Last, First Name</th>
				<th>Email</th>
				<th>SF State ID</th>
				<th>Last Access Date(s)</th>
			</tr>
		</thead>
		<tbody>
			{assign var=currentStudent value=null}
			{foreach $logs as $key => $studentLogs}
				{assign var=currentStudent value=$users[$key]}
				<tr>
					<td>
						{$currentStudent->lastName}, {$currentStudent->firstName}
					</td>
					<td>
						<a href="mailto:{$currentStudent->emailAddress}">{$currentStudent->emailAddress}</a>  
					</td>
					<td>
						{$currentStudent->username}
					</td>
					<td>
						{$currentStudent->lastAccessDate->format('Y-m-d h:i a')}
						{if count($studentLogs) > 1}
						<a data-toggle="collapse" href="#statistics{$key}" role="button" aria-expanded="false" aria-controls="statistics{$key}">
							<i id="collapseIcon" class="ml-3 far fa-plus-square"></i>
						</a>
						<div class="collapse pt-0 mt-0" id="statistics{$key}">
							{foreach $studentLogs as $log}
								{if $log->accessDate != $currentStudent->lastAccessDate}
  								{$log->accessDate->format('Y-m-d h:i a')}<br>
  								{/if}
							{/foreach}
						</div>
						{/if}
					</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="4">No users have viewed this syllabus</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

	<hr>

	<h2 class="mt-3">Enrolled users who haven't viewed the syllabus</h2>
	<table class="table table-sm table-striped">
		<thead class="thead-dark">
			<tr>
				<th>Last, First Name</th>
				<th>Email</th>
				<th>SF State ID</th>
			</tr>
		</thead>
		<tbody>
			{foreach $nonViewUsers as $key => $nonViewUser}
				<tr>
					<td>
						{$nonViewUser->lastName}, {$nonViewUser->firstName}
					</td>
					<td>
						<a href="mailto:{$nonViewUser->emailAddress}">{$nonViewUser->emailAddress}</a>  
					</td>
					<td>
						{$nonViewUser->id}
					</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="3">All enrolled users have viewed this syllabus</td>
				</tr>
			{/foreach}
		</tbody>
	</table>


</div>