<?php
// list all user normal
// add project manager to project
// get tables for analizses
/*
 * This is a dummy API. To be superseded by James' API
 */
$act = $_POST['action'];
IF ($act == 'TASK_LIST') { //done
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'id' => 'T-1',
				'priority' => 'Critical',
				'taskTitle' => 'Debug PHP API',
				'projectTitle' => 'CSCI311 project',
				'status' => 'Work In Progress'
			],
			[
				'id' => 'T-2',
				'priority' => 'Critical',
				'taskTitle' => 'Debug PHP API',
				'projectTitle' => 'CSCI311 project',
				'status' => 'Work In Progress'
			],
			[
				'id' => 'T-3',
				'priority' => 'Critical',
				'taskTitle' => 'Debug PHP API',
				'projectTitle' => 'CSCI311 project',
				'status' => 'Work In Progress'
			]
		]
	]);
} else if ($act == 'PROJECT_LIST_I_AM_MANAGING') { //done
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'id' => 'P-1',
				'manager' => 'Hoa Dam',
				'title' => 'Rollout of new NBN scheme',
				'progress' => '50%'
			],
			[
				'id' => 'P-2',
				'manager' => 'Hoa Dam',
				'title' => 'Rollout of new NBN scheme',
				'progress' => '50%'
			],
			[
				'id' => 'P-3',
				'manager' => 'Hoa Dam',
				'title' => 'Rollout of new NBN scheme',
				'progress' => '50%'
			]				
		]
	]);
} else if ($act == 'PROJECT_LIST_ALL') { //done
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'id' => 'P-4',
				'manager' => 'Hoa Dam',
				'title' => 'Rollout of new NBN scheme',
				'progress' => '50%'
			],
			[
				'id' => 'P-5',
				'manager' => 'Hoa Dam',
				'title' => 'Rollout of new NBN scheme',
				'progress' => '50%'
			],
			[
				'id' => 'P-6',
				'manager' => 'Hoa Dam',
				'title' => 'Rollout of new NBN scheme',
				'progress' => '50%'
			]				
		]
	]);
} else if ($act == 'PROJECT_GET') { //done
	echo json_encode([
		'action' => $act,
		'payload' => [
			'title' => 'Rollout new Telstra database system',
			'userId' => 'michaeln',			
			'createdDate' => '18/04/2015',
			'dateStart' => '20/04/2015',
			'dateExpectedFinish' => '20/09/2015',
			'projectManagerUserIds' => ['hans', 'jakem', 'chrissyj'],
			'status' => 'IN PROGRESS',
			'allocatedBudget' => '8400',
			'usedBudget' => '4200',
			'allocatedTime' => '230',
			'usedTime' => '161',
			'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed hendrerit felis vel fringilla consequat. Nunc nec nunc neque. Integer sagittis leo ornare augue finibus scelerisque. Integer ultrices nunc eget odio egestas, quis pellentesque tortor feugiat. 
<br><br>
Nullam velit nulla, ullamcorper sed elit ut, ullamcorper rhoncus justo. Aliquam erat volutpat. Duis vitae ipsum nec lectus aliquet molestie. Aliquam efficitur pharetra sem at lacinia.',
			'attachments' => [
				[
					'url' => 'req-spec.pdf',
					'title' => 'req-spec-functional.pdf',
					'type' => 'Requirements specification'
				],
				[
					'url' => 'req-spec.pdf',
					'title' => 'architecture-spec.docx',
					'type' => 'Requirements specification'
				],
				[
					'url' => 'req-spec.pdf',
					'title' => 'training-plan-notes.txt',
					'type' => 'Requirements specification'
				]								
			],
			'milestones' => [
				[
					'id' => 'M-1',
					'title' => 'Architecture specification',
					'progress' => 'Complete'
				],
				[
					'id' => 'M-2',
					'title' => 'Program specification',
					'progress' => 'In Progress'
				],
				[
					'id' => 'M-3',
					'title' => 'Training plan',
					'progress' => 'Open'
				]								
			],
			'tasks' => [
				[
					'id' => 'T-1',
					'priority' => 'Critical',
					'title' => 'Finish PHP API',
					'status' => 'Closed',
					'assignees' => 'James Wilson'
				],
				[
					'id' => 'T-2',
					'priority' => 'Critical',
					'title' => 'Finish PHP API',
					'status' => 'Closed',
					'assignees' => 'James Wilson'
				],
				[
					'id' => 'T-3',
					'priority' => 'Critical',
					'title' => 'Finish PHP API',
					'status' => 'Closed',
					'assignees' => 'James Wilson'
				]				
			],
			'users' => [
				[
					'username' => 'legendofking1992',
					'displayName' => 'James Wilson',
					'manhours' => '481'							// what is this			
				],
				[
					'username' => 'abg47',
					'displayName' => 'Hans',
					'manhours' => '381'										
				],
				[
					'username' => 'hoaisking1337',
					'displayName' => 'Michael Nguyen',
					'manhours' => '581'										
				]				
			],
			'comments' => [
				[
					'username' => 'michaeln',
					'displayName' => 'Michael Nguyen',
					'datetime' => '18/04/2015 01:04',
					'comment' => 'I feel as if this project is not moving fast enough. Please reply, thanks.'
				],
				[
					'username' => 'michaeln',
					'displayName' => 'Emma Bree',
					'datetime' => '18/04/2015 01:04',
					'comment' => 'I feel as if this project is not moving fast enough. Please reply, thanks.'
				],
				[
					'username' => 'michaeln',
					'displayName' => 'James Wilson',
					'datetime' => '18/04/2015 01:04',
					'comment' => 'I feel as if this project is not moving fast enough. Please reply, thanks.'
				]								
			]
		]
	]);
} else if ($act == 'PROJECT_EDIT') { // done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'PROJECT_ATTACH_DELIVERABLE') { //done
	echo json_encode([
		'action' => 'PROJECT_ATTACH_DELIVERABLE',
		'payload' => 1
	]);
} else if ($act == 'MILESTONE_CREATE') { //done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'PROJECT_ASSIGN_TASK') { //??????
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'PROJECT_COMMENT_ADD') { //done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'MILESTONE_GET') { //done
	echo json_encode([
		'action' => $act,
		'payload' => [
			'title' => 'Finish the requirements & architecture specifications',
			'creatorUserId' => 'michaeln',
			'creatorDisplayName' => 'Michael Nguyen',
			'createdDate' => '18/04/2015',
			'projectId' => 'P-1',
			'managerUserId' => 'cathyb',			 // same as project manager, list them here
			'managerDisplayName' => 'Cathy Bronze',
			'status' => 'Completed',
			'allocatedBudget' => '8400',
			'usedBudget' => '4200',
			'allocatedTime' => '230',
			'usedTime' => '161',
			'description' => 'Lorem ipsum dolor sit amet lorem ipsum. Dolor sit amet. Lorem ipsum dolor sit amet lorem ipsum. Dolor sit amet. 
<br><br>
Ipsum dolor sit amet lorem ipsum. Dolor sit amet lorem. 
<br><br>
Sit amet lorem ipsum. Dolor sit amet.',
			'tasks' => [
				[
					'id' => 'T-1',
					'priority' => 'Critical',
					'taskTitle' => 'Create architecture specification for Telstra modem module',
					'projectId' => 'P-4',
					'status' => 'Complete'
				],
				[
					'id' => 'T-2',
					'priority' => 'Critical',
					'taskTitle' => 'Create architecture specification for Telstra modem module',
					'projectId' => 'P-5',
					'status' => 'Complete'
				],
				[
					'id' => 'T-3',
					'priority' => 'Critical',
					'taskTitle' => 'Create architecture specification for Telstra modem module',
					'projectId' => 'P-6',
					'status' => 'Complete'
				]								
			],
			'users' => [
				[
					'username' => 'legendofking1992',
					'displayName' => 'James Wilson',
					'manhours' => '481'
				],
				[
					'username' => 'abg47',
					'displayName' => 'Hans',
					'manhours' => '381'
				],
				[
					'username' => 'hoaisking1337',
					'displayName' => 'Michael Nguyen',
					'manhours' => '581'
				]								
			]
		]
	]);
} else if ($act == 'MILESTONE_EDIT') { // done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'MILESTONE_ASSIGN_TASK') { // removed
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'TASK_GET') { // done
	echo json_encode([
		'action' => $act,
		'payload' => [
			'title' => 'Finish PHP API',
			'projectId' => 'P-3',
			'projectTitle' => 'Rollout new telstra NBN',
			'assigneeIds' => ['michaeln', 'steveo', 'jakea'], // now is single, or we need another table
			'assigneeDisplayNames' => ['Michael Nguyen', 'Steven Ong', 'Jake Arros'],
			'priority' => 'Critical',
			'status' => 'In Progress',
			'allocatedBudget' => '8400',
			'usedBudget' => '4200',
			'allocatedTime' => '230',
			'usedTime' => '161',
			'dueDate' => '18/04/2015',
			'flags' => ['Release 1.6.0', 'sprint 1', 'UI interface'], // shoudl be a string or we need another table
			'description' => 'Lorem ipsum dolor sit amet. Lorem ipsum dolor. 
<br><br>
Ipsum dolor sit amet lorem.',
			'attachments' => [
				[
					'url' => 'plugin-concept-inspiration.jpg',
					'title' => 'plugin-concept-inspiration.jpg',
					'type' => 'JPG'
				],
				[
					'url' => 'coffee.gif',
					'title' => 'coffee.gif',
					'type' => 'GIF'
				],
				[
					'url' => 'meeting-agenda',
					'title' => 'meeting-agenda.txt',
					'type' => 'TXT'
				]				
			],
			'comments' => [
				[
					'userId' => 'michaeln',
					'userDisplayName' => 'Michael Nguyen',
					'date' => '18/04/2015 01:04',
					'comment' => 'I feel as if this project is not moving fast enough. Please reply, thanks.'
				],
				[
					'userId' => 'michaeln',
					'userDisplayName' => 'Jake Hughes',
					'date' => '18/04/2015 01:04',
					'comment' => 'I feel as if this project is not moving fast enough. Please reply, thanks.'
				],
				[
					'userId' => 'michaeln',
					'userDisplayName' => 'Selena Gomez',
					'date' => '18/04/2015 01:04',
					'comment' => 'I feel as if this project is not moving fast enough. Please reply, thanks.'
				]								
			],
			'subtasks' => [
				[
					'id' => 'T-10',
					'priority' => 'Critical',
					'taskTitle' => 'Fix the infinite loop',
					'assigneeUsernames' => ['michaeln', 'yangb', 'shaoz'],
					'assigneeDisplayNames' => ['Michael Nguyen', 'Yang Biao', 'Shao Zhang'],
					'status' => 'Complete'
				],
				[
					'id' => 'T-11',
					'priority' => 'Critical',
					'taskTitle' => 'Fix the infinite loop',
					'assigneeUsernames' => ['michaeln', 'yangb', 'shaoz'],
					'assigneeDisplayNames' => ['Michael Nguyen', 'Yang Biao', 'Shao Zhang'],
					'status' => 'Complete'
				],
				[
					'id' => 'T-12',
					'priority' => 'Critical',
					'taskTitle' => 'Fix the infinite loop',
					'assigneeUsernames' => ['michaeln', 'yangb', 'shaoz'],
					'assigneeDisplayNames' => ['Michael Nguyen', 'Yang Biao', 'Shao Zhang'],
					'status' => 'Complete'
				]				
			],
			'id' => 'T200',			// need reformat this
			'dependeeIds' => ['T123', 'T124'],
			'dependantIds' => ['T201']
		]
	]);
} else if ($act == 'TASK_EDIT') { // done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'TASK_WATCH') { // ????
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'TASK_ATTACH_FILE') {
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'TASK_ADD_COMMENT') {
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'TASK_ASSIGN_SUBTASK') {
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'USER_GET') {
	echo json_encode([
		'action' => $act,
		'payload' => [
			'displayName' => 'Michael Nguyen',
			'username' => 'michaeln',			
			'expertise' => 'WEB TECHNOLOGIES (INC. C#)', // do not have this arrtribute
			'role' => 'Project Manager',
			'permissions' => [
				'Create project plan, milestone, task & user',
				'Edit project, milestone, task & user',
				'Delete project plan, milestone, task & user'
			],
			'pastProjects' => [
				[
					'id' => 'P-690',
					'title' => 'Re-brand the CCA label',
					'projectManagerUserName' => ['hansa', 'michaeln', 'jakeb'],
					'projectManagerDisplayName' => ['Hans Albert', 'Michael Nguyen', 'Jake Bozina'],
					'rolesServed' => ['Developer', 'Quality Assurance', 'Developer lead']
				],
				[
					'id' => 'P-537',
					'title' => 'Pass TCG inspection',
					'projectManagerUserName' => ['hansa', 'michaeln', 'jakeb'],
					'projectManagerDisplayName' => ['Hans Albert', 'Michael Nguyen', 'Jake Bozina'],
					'rolesServed' => ['Developer', 'Quality Assurance', 'Developer lead']
				],
				[
					'id' => 'P-421',
					'title' => 'Deploy new mobile-friendly website',
					'projectManagerUserName' => ['hansa', 'michaeln', 'jakeb'],
					'projectManagerDisplayName' => ['Hans Albert', 'Michael Nguyen', 'Jake Bozina'],
					'rolesServed' => ['Developer', 'Quality Assurance', 'Developer lead']
				]				
			]
		]
	]);
} else if ($act == 'USER_EDIT') {
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'SEARCH_PROJECTS') { // done id
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'id' => 'P-1',
				'title' => 'Rollout Telstra NBN',
				'projectManagerUserNames' => ['michaeln', 'stevem', 'hoad'],
				'projectManagerDisplayNames' => ['Michael Nguyen', 'Steven Moody', 'Hoa Dam'],
				'status' => 'In Progress'
			],
			[
				'id' => 'P-2',
				'title' => 'Rollout Telstra NBN',
				'projectManagerUserNames' => ['michaeln', 'stevem', 'hoad'],
				'projectManagerDisplayNames' => ['Michael Nguyen', 'Steven Moody', 'Hoa Dam'],
				'status' => 'Failed to finish'
			],
			[
				'id' => 'P-3',
				'title' => 'Rollout Telstra NBN',
				'projectManagerUserNames' => ['michaeln', 'stevem', 'hoad'],
				'projectManagerDisplayNames' => ['Michael Nguyen', 'Steven Moody', 'Hoa Dam'],
				'status' => 'Finished'
			]			
		]
	]);
} else if ($act == 'SEARCH_MILESTONES') {
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'id' => 'M-1',
				'title' => 'Create test plan',
				'status' => 'In Progress'
			],
			[
				'id' => 'M-2',
				'title' => 'Execute test plan',
				'status' => 'In Progress'
			],
			[
				'id' => 'M-3',
				'title' => 'Finish training system end-users',
				'status' => 'In Progress'
			]			
		]
	]);
} else if ($act == 'SEARCH_TASKS') { //done
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'id' => 'T-1',
				'title' => 'Support underground broadband cables',
				'status' => 'In Progress',
				'assigneeIds' => ['michaeln', 'stevem'],
				'assigneeDisplayNames' => ['Michael Nguyen', 'Steve Moody']
			],
			[
				'id' => 'T-2',
				'title' => 'Support underground broadband cables',
				'status' => 'In Progress',
				'assigneeIds' => ['michaeln', 'stevem'],
				'assigneeDisplayNames' => ['Michael Nguyen', 'Steve Moody']
			],
			[
				'id' => 'T-3',
				'title' => 'Support underground broadband cables',
				'status' => 'In Progress',
				'assigneeIds' => ['michaeln', 'stevem'],
				'assigneeDisplayNames' => ['Michael Nguyen', 'Steve Moody']
			]			
		]
	]);
} else if ($act == 'SEARCH_USERS') {
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'username' => ['michaeln'],
				'displayName' => ['Michael Nguyen'],
				'estimatedWorkRemainingForAllProjects' => '951'
			],
			[
				'username' => ['steveb'],
				'displayName' => ['Steve Borne'],
				'estimatedWorkRemainingForAllProjects' => '32'
			],
			[
				'username' => ['jakee'],
				'displayName' => ['Jake Echos'],
				'estimatedWorkRemainingForAllProjects' => '0'
			]						
		]
	]);
} else if ($act == 'LOGIN') {
	$mysqli = new mysqli('localhost', 'vitawebs_csci311', 'hoaisking1337', 'vitawebs_csci311');
	$result = $mysqli->query('SELECT * ' .
		'FROM Users ' .
		'WHERE username = "' . $_POST['username'] . '" AND password = sha1("' . $_POST['password'] . '")');
	$loginSuccess = ($result->num_rows >= 1);
	
	if ($loginSuccess && session_start()) {
		$_SESSION['username'] = $result->fetch_object()->username;
	} else {
		$loginSuccess = 0;
	}
	$mysqli->close();
		
	// Tell client-side if login was successful
	echo json_encode([
		'action' => $act,
		'payload' => $loginSuccess
	]);	
} else if ($act == 'LOGOUT') {
	session_start();
	session_unset();
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'PROJECT_MANAGERS_GET') {
	echo json_encode([
		'action' => $act,
		'payload' => [
			[
				'displayName' => 'Michael Nguyen',
				'username' => 'michaeln'
			],
			[
				'displayName' => 'Chrissy Banks',
				'username' => 'chrissyb'
			],
			[
				'displayName' => 'Jessy Jackson',
				'username' => 'jessyj'
			],
			[
				'displayName' => 'Emma stozer',
				'username' => 'emmas'
			],
			[
				'displayName' => 'Julian Crane',
				'username' => 'julianc'
			]								
		]
	]);
} else if ($act == 'PERMISSIONS_GET') {	
	echo json_encode([
		'action' => $act,
		'payload' => [
			'Create, edit & delete Project plans',
			'Create, edit & delete Milestones',
			'Create, edit & delete Tasks',
			'Create, edit & delete Users',
			'Create, edit & delete user-defined roles',
			'Watch tasks',			
			'Message project managers',
			'Message development managers',
			'Message SMEs',
			'Message FAs',
			'Message SAs',
			'Message dev. leads',
			'Message developers',
			'Message QAs',
			'Message deployment specialists',
			'Message training specialists',
			'Send approval requests',
			'Accept & reject approval requests',
			'View project list page',
			'View projects they are managing'
		]
	]);
} else if ($act == 'ROLES_GET') {
	echo json_encode([
		'action' => $act,
		'payload' => [
			'Project manager',
			'Development manager',
			'Subject Matter Expert',
			'Functional Analyst',
			'Solutions Architect',
			'Developer lead',
			'Developer',
			'Quality Assurance',
			'Deployment specialist',
			'Training specialist',
		]
	]);
} else if ($act == 'PROJECT_CREATE') { //done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'MILESTONE_CREATE') { //done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'TASK_CREATE') { //done
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);
} else if ($act == 'USER_CREATE') {
	echo json_encode([
		'action' => $act,
		'payload' => 1
	]);	
} else {
	echo 'ERROR: unknown action specified';
}