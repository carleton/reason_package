<?
include_once('reason_header.php');
include_once('/usr/local/webapps/reason/reason_package_local/disco/plasmature/types/datetime.php');
reason_include_once('minisite_templates/modules/form/views/thor/default.php');
//include_once('disco/boxes/boxes.php');
$GLOBALS[ '_form_view_class_names' ][ basename( __FILE__, '.php') ] = 'IndividualVisitForm';

/**
 * IndividualVisitForm adds visit request info to Thor form
 * that gets personal info
 *
 * @author Steve Smith
 */


class IndividualVisitForm extends DefaultThorForm
{
	
	var $elements = array(
/*
	'visit_date_comments' => array(
		'type' => 'comment',
		'text' => '<h3>Please use the calendar to select a date to visit. Available dates are in green. Please include an arrival time so we know when to expect you.</h3>',
		),
	'visit_date_and_time' => array(
		'type' => 'textdatetime_js',
		'script_url' => 'http://www.luther.edu/scripts/datetime.js',
		),

	'first_name' => 'text',
	'last_name' => 'text',
	'gender' => array(
		'type' => 'radio_inline',
		'options' => array('Female'=>'Female','Male'=>'Male',),
		),
	'address' => 'text',
	'city' => 'text',
	'state/province' => 'state_province',
	'zip' => 'text',
	'email' => 'text',
	'home_phone' => 'text',
	'cell_phone' => 'text',
*/
	'high_school' => array(
		'type' => 'text',
		'display_style' => 'normal',
		),
	'graduation_year' => array(
		'type' => 'year',
		'num_years_after_today' => 4,
		'num_years_before_today' => 4,
		),
	'transfer' => array(
		'type' => 'radio_inline_no_sort',
		'display_name' => 'Are you a transfer student?',
		'display_style' => 'normal',
		'options' => array('Yes' => 'Yes', 'No' => 'No',),
		),
	'transfer_college' => array(
		'type' => 'textarea',
		'display_name' => 'If yes, what is the name and address of the school you previously attended?'
		),
	'visit_activities' => array(
		'type' => 'comment',
		'text' => '<h3>Please check any of the following activities 
					that you would like to do as part of your campus visit.
					We will try to accommodate as many of your requests as 
					possible.</h3>',
		),
	'meet_counselor' => array(
		'type' => 'checkboxfirst',
		'colspan' => 1,
		'display_name' => 'Meet with an Admissions Counselor',
		'display_style'=>'normal',
 		'comments' => '<small>  (30 min)</small>', 
		),
	'tour' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Take a campus tour',
		'display_style'=>'normal',
		'comments' => '<small>  (60 min)</small>',
		),
	'meet_faculty' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Meet with a faculty member',
		'display_style' => 'normal',
		'comments' => '<small>  (30 min)</small>',
		),
	'meet_faculty_details' => array(
		'type' => 'select_no_sort',
		'add_null_value_to_top' => true,
		'display_name' =>'Select Department',
		'options' => array(
			'Accounting'=>'Accounting',
			'Africana Studies'=>'Africana Studies',
			'Art'=>'Art',
			'Athletic Training'=>'Athletic Training',
			'Biblical Languages'=>'Biblical Languages',
			'Biology'=>'Biology',
			'Business'=>'Business',
			'Chemistry'=>'Chemistry',
			'Classical Studies'=>'Classical Studies',
			'Classics'=>'Classics',
			'Communication Studies'=>'Communication Studies',
			'Computer Science'=>'Computer Science',
			'Economics'=>'Economics',
			'Education'=>'Education',
			'English'=>'English',
			'Environmental Studies'=>'Environmental Studies',
			'French'=>'French',
			'German'=>'German',
			'Health'=>'Health',
			'History'=>'History',
			'International Studies'=>'International Studies',
			'Management'=>'Management',
			'Management Information Systems'=>'Management Information Systems',
			'Mathematics'=>'Mathematics',
			'Mathematics/Statistics'=>'Mathematics/Statistics',
			'Museum Studies'=>'Museum Studies',
			'Music'=>'Music',
			'Nursing'=>'Nursing',
			'Philosophy'=>'Philosophy',
			'Physical Education'=>'Physical Education',
			'Physics'=>'Physics',
			'Political Science'=>'Political Science',
			'Psychology'=>'Psychology',
			'Religion'=>'Religion',
			'Russian Studies'=>'Russian Studies',
			'Scandinavian Studies'=>'Scandinavian Studies',
			'Social Welfare'=>'Social Welfare',
			'Social Work'=>'Social Work',
			'Sociology'=>'Sociology',
			'Spanish'=>'Spanish',
			'Speech and Theatre'=>'Speech and Theatre',
			'Theatre/Dance'=>'Theatre/Dance',
			'Women\'s and Gender Studies'=>'Women\'s and Gender Studies',
			'--' => '--',
			'PreProfessional Programs' => 'PreProfessional Programs',
			'Arts Management' => '--Arts Management',
			'International Management Studies' => '--International Management Studies',
			'Predentistry' => '--Predentistry',
			'Preengineering' => '--Preengineering',
			'Prelaw' => '--Prelaw',
			'Premedicine' => '--Premedicine',
			'Preoptometry' => '--Preoptometry',
			'Prepharmacy' => '--Prepharmacy',
			'Prephysical Therapy' => '--Prephysical Therapy',
			'Preveterinary Medicine' => '--Preveterinary Medicine',
			'Sports Management' => '--Sports Management'
			),
		),
		'meet_second_faculty' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Meet with a second faculty member',
		'display_style' => 'normal',
		'comments' => '<small>  (30 min)</small>',
		),
	'meet_second_faculty_details' => array(
		'type' => 'select_no_sort',
		'add_null_value_to_top' => true,
		'display_name' =>'Select Department',
		'options' => array(
			'Accounting'=>'Accounting',
			'Africana Studies'=>'Africana Studies',
			'Art'=>'Art',
			'Athletic Training'=>'Athletic Training',
			'Biblical Languages'=>'Biblical Languages',
			'Biology'=>'Biology',
			'Business'=>'Business',
			'Chemistry'=>'Chemistry',
			'Classical Studies'=>'Classical Studies',
			'Classics'=>'Classics',
			'Communication Studies'=>'Communication Studies',
			'Computer Science'=>'Computer Science',
			'Economics'=>'Economics',
			'Education'=>'Education',
			'English'=>'English',
			'Environmental Studies'=>'Environmental Studies',
			'French'=>'French',
			'German'=>'German',
			'Health'=>'Health',
			'History'=>'History',
			'International Studies'=>'International Studies',
			'Management'=>'Management',
			'Management Information Systems'=>'Management Information Systems',
			'Mathematics'=>'Mathematics',
			'Mathematics/Statistics'=>'Mathematics/Statistics',
			'Museum Studies'=>'Museum Studies',
			'Music'=>'Music',
			'Nursing'=>'Nursing',
			'Philosophy'=>'Philosophy',
			'Physical Education'=>'Physical Education',
			'Physics'=>'Physics',
			'Political Science'=>'Political Science',
			'Psychology'=>'Psychology',
			'Religion'=>'Religion',
			'Russian Studies'=>'Russian Studies',
			'Scandinavian Studies'=>'Scandinavian Studies',
			'Social Welfare'=>'Social Welfare',
			'Social Work'=>'Social Work',
			'Sociology'=>'Sociology',
			'Spanish'=>'Spanish',
			'Speech and Theatre'=>'Speech and Theatre',
			'Theatre/Dance'=>'Theatre/Dance',
			'Women\'s and Gender Studies'=>'Women\'s and Gender Studies',
			'--' => '--',
			'PreProfessional Programs' => 'PreProfessional Programs',
			'Arts Management' => '--Arts Management',
			'International Management Studies' => '--International Management Studies',
			'Predentistry' => '--Predentistry',
			'Preengineering' => '--Preengineering',
			'Prelaw' => '--Prelaw',
			'Premedicine' => '--Premedicine',
			'Preoptometry' => '--Preoptometry',
			'Prepharmacy' => '--Prepharmacy',
			'Prephysical Therapy' => '--Prephysical Therapy',
			'Preveterinary Medicine' => '--Preveterinary Medicine',
			'Sports Management' => '--Sports Management'
			),
		),	
	'observe_class' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Sit in on a class',
		'display_style' => 'normal',
		'comments' => '<small>  Seniors Only - MWF (60 min) T Th (90 min)</small>',
		),
	'observe_class_details' => array(
		'type' => 'select_no_sort',
		'add_null_value_to_top' => true,
		'display_name' => 'Select Department',
		'options' => array(
			'Accounting'=>'Accounting',
			'Africana Studies'=>'Africana Studies',
			'Art'=>'Art',
			'Athletic Training'=>'Athletic Training',
			'Biblical Languages'=>'Biblical Languages',
			'Biology'=>'Biology',
			'Business'=>'Business',
			'Chemistry'=>'Chemistry',
			'Classical Studies'=>'Classical Studies',
			'Classics'=>'Classics',
			'Communication Studies'=>'Communication Studies',
			'Computer Science'=>'Computer Science',
			'Economics'=>'Economics',
			'Education'=>'Education',
			'English'=>'English',
			'Environmental Studies'=>'Environmental Studies',
			'French'=>'French',
			'German'=>'German',
			'Health'=>'Health',
			'History'=>'History',
			'International Studies'=>'International Studies',
			'Management'=>'Management',
			'Management Information Systems'=>'Management Information Systems',
			'Mathematics'=>'Mathematics',
			'Mathematics/Statistics'=>'Mathematics/Statistics',
			'Museum Studies'=>'Museum Studies',
			'Music'=>'Music',
			'Nursing'=>'Nursing',
			'Philosophy'=>'Philosophy',
			'Physical Education'=>'Physical Education',
			'Physics'=>'Physics',
			'Political Science'=>'Political Science',
			'Psychology'=>'Psychology',
			'Religion'=>'Religion',
			'Russian Studies'=>'Russian Studies',
			'Scandinavian Studies'=>'Scandinavian Studies',
			'Social Welfare'=>'Social Welfare',
			'Social Work'=>'Social Work',
			'Sociology'=>'Sociology',
			'Spanish'=>'Spanish',
			'Speech and Theatre'=>'Speech and Theatre',
			'Theatre/Dance'=>'Theatre/Dance',
			'Women\'s and Gender Studies'=>'Women\'s and Gender Studies',
			'--' => '--',
			'PreProfessional Programs' => 'PreProfessional Programs',
			'Arts Management' => '--Arts Management',
			'International Management Studies' => '--International Management Studies',
			'Predentistry' => '--Predentistry',
			'Preengineering' => '--Preengineering',
			'Prelaw' => '--Prelaw',
			'Premedicine' => '--Premedicine',
			'Preoptometry' => '--Preoptometry',
			'Prepharmacy' => '--Prepharmacy',
			'Prephysical Therapy' => '--Prephysical Therapy',
			'Preveterinary Medicine' => '--Preveterinary Medicine',
			'Sports Management' => '--Sports Management'
			),		
		),
	'chapel' => array(
		'type' => 'checkboxfirst',
		'colspan' => 2,
		'display_style' => 'normal',
		'comments' => '<small>  (30 min) daily at 10:30</small>',
		),
	'lunch' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Lunch',
		'display_style' => 'normal',
		'comments' => '<small>  (30-60 min)</small>',
		),
	'meet_coach' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Conversation with a coach',
		'display_style' => 'normal',
		'comments' => '<small>  (30 min)</small>',
		),
	'meet_coach_details' => array(
		'type' => 'select',
		'display_name' => 'Select Sport',
		'add_null_value_to_top' => true,
		'options' => array(
			'Baseball'=>'Baseball',
			'Basketball'=>'Basketball',
			'Cross Country'=>'Cross Country',
			'Football'=>'Football',
			'Golf'=>'Golf',
			'Soccer'=>'Soccer',
			'Softball'=>'Softball',
			'Swimming & Diving'=>'Swimming & Diving',
			'Tennis'=>'Tennis',
			'Track & Field'=>'Track & Field',
			'Volleyball'=>'Volleyball',		
			'Wrestling'=>'Wrestling',
			),
		),
	'choir' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Observe a choir rehearsal, if available',
		'display_style' => 'normal',
		'comments' => '<small>  MWF 1:30 (60 min)</small>',
		),
	'band' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Observe a band rehearsal, if available',
		'display_style' => 'normal',
		'comments' => '<small>  MWF 12:15 (60 min)</small>',
		),
	'orchestra' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Observe an orchestra rehearsal, if available',
		'display_style' => 'normal',
		'comments' => '<small>  MTWTHF 4:00 (60 min)</small>',
		),
	'music_audition' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'Perform a music audition for scholarship',
		'display_style' => 'normal',
		'comments' => '<small>  Seniors Only (30 min)</small>',
		),
	'music_audition_details' => array(
		'type' => 'select_no_sort_js',
		'display_name' => 'Select Instrument/Voice',
		'add_null_value_to_top' => true,
		'options' => array(
			'Flute'=>'Flute',
			'Oboe'=>'Oboe',
			'Clarinet'=>'Clarinet',
			'Saxophone'=>'Saxophone',
			'Bassoon'=>'Bassoon',
			'Horn'=>'Horn',
			'Trumpet'=>'Trumpet',
			'Trombone'=>'Trombone',
			'Euphonium'=>'Euphonium',
			'Tuba'=>'Tuba', 
			'Percussion'=>'Percussion',
			'Piano'=>'Piano',
			'Harp'=>'Harp',
			'Voice' => 'Voice',
			'Violin'=>'Violin',
			'Viola'=>'Viola',
			'Cello'=>'Cello',
			'Double Bass'=>'Double Bass',
			),
		), 	
	'meet_music_faculty' => array(
		'type' => 'checkboxfirst',
		'display_style' => 'normal',
		'display_name' => 'Conversation with music faculty',
		'comments' => '<small>  (30 min)</small>',
		),
	'meet_music_faculty_details' => array(
		'type' => 'select',
		'display_name' => 'Select Discipline',
		'display_style' => 'right',
		'add_null_value_to_top' => true,
		'options' => array(
			'Band'=>'Band',
			'Choir'=>'Choir',
			'Composition'=>'Composition',
			'Early Music'=>'Early Music',
			'Jazz'=>'Jazz',
			'Music Education'=>'Music Education',
			'Orchestra'=>'Orchestra',
			'Theory'=>'Theory',
			'Flute'=>'Flute',
			'Oboe'=>'Oboe',
			'Clarinet'=>'Clarinet',
			'Saxophone'=>'Saxophone',
			'Bassoon'=>'Bassoon',
			'Horn'=>'Horn',
			'Trumpet'=>'Trumpet',
			'Trombone'=>'Trombone',
			'Euphonium'=>'Euphonium',
			'Tuba'=>'Tuba', 
			'Percussion'=>'Percussion',
			'Piano'=>'Piano',
			'Harp'=>'Harp',
			'Voice' => 'Voice',
			'Violin'=>'Violin',
			'Viola'=>'Viola',
			'Cello'=>'Cello',
			'Double Bass'=>'Double Bass',
			),
		),
	'additional_request' => array( 	
		'type' => 'textarea',
		'rows' => 2,
		'cols' => 35,
		'display_name' =>'Additional Request',
//		'comments' => '<small>  30 min</small>',
		),
	'housing_note' => array(
		'type' => 'comment',
		'text' => '<h3>Overnight Housing</h3> (Seniors Only - Please provide two weeks notice)',
		),
	'overnight_housing' => array(
		'type' => 'checkboxfirst',
		'display_name' => 'I would like to request overnight housing 
						with a current Luther student',
//		'display_style' => 'normal',
		),
	'overnight_note' => array(
		'type' => 'comment',
		'text' => '<strong>Please indicate whether you\'d like to stay with a student on the day of your visit or on the night prior.</strong>',
		),
	'overnight_day' => array(
		'type' => 'radio_no_sort',
		'display_style' => 'normal',
		'options' => array(
			'Day of visit' => 'Day of visit', 
			'Night prior to visit' => 'Night prior to visit',),
		),
	'overnight_prior_arrival_time' => array(
		'type' => 'select_no_sort',
		'display_name' => 'If arriving the night prior, please indicate arrival time',
		'display_style' => 'normal',
		'add_null_value_to_top' => true,
		'options' => array(
			'5:00' => '5:00 p.m.',
			'5:30' => '5:30 p.m.',
			'6:00' => '6:00 p.m.',
			'6:30' => '6:30 p.m.',
			'7:00' => '7:00 p.m.',
			'7:30' => '7:30 p.m.',
			'8:00' => '8:00 p.m.',
			'8:30' => '8:30 p.m.',
			'9:00' => '9:00 p.m.',
			),
		),
	);
	

	var $required = array(
//		'first_name',
//		'last_name',
//		'gender',
		'high_school',
		'graduation_year',
//		'email',
//		'visit_date_and_time'
//		'arrival_time'
	);


	// if defined none of the default actions will be run (such as email_form_data) and you need to define the custom method and a
	// should_custom_method in the view (if they are not in the model).
	var $process_actions = array('email_form_data_to_submitter',);
	
	function custom_init()
	{
	
	}

	function on_every_time()
	{	
		$visitdatetime_properties = array(
			'datepicker_arg' => 'split-date fill-grid-no-select disable-days-7 statusformat-l-cc-sp-d-sp-F-sp-Y opacity-99 disable-20091126 disable-20091127 disable-20091128 disable-20091212 disable-20091219 disable-xxxx1224 disable-xxxx1225 disable-xxxx1226 disable-xxxx1231 disable-xxxx0101 disable-xxxx0102 disable-20100130 disable-20100320 disable-20100327 disable-20100402 disable-20100403 disable-20100531 disable-20100603 disable-20100515 disable-20100522 disable-20100529 disable-20100605 disable-20100612 disable-20100619 disable-20100626 disable-20100703 disable-20100710 disable-20100717 disable-20100724 disable-20100731 disable-20100807 disable-20100814 disable-20100821 disable-20100828 disable-20100904 disable-20100911 range-low-today range-high-20100912',
			);
		$visitdatetime_field = $this->get_element_name_from_label('Visit Date and Time');
		$this->change_element_type($visitdatetime_field, 'textdatetimepublic');
		$this->set_element_properties($visitdatetime_field, $visitdatetime_properties);
			
		$gender = $this->get_element_name_from_label('Gender');
		$this->change_element_type($gender, 'radio_inline_no_sort');
		
		$state_field = $this->get_element_name_from_label('State/Province');
		$this->change_element_type($state_field, 'state_province');
		
		

		//$this->change_element_type('transfer', 'radio_inline_no_sort');
		//$this->set_element_properties('test_date', $visitdatetime_properties);

		//$this->change_element_type('transfer', 'radio_inline_no_sort');
		//$this->set_element_properties('test_date', $visitdatetime_properties);

		

//		$this->set_element_properties($grad_year, 'num_years_after_today' => 3, 'num_years_before_today' => 4);


		//$gender_field_name = $this->get_element_name_from_label('Gender');
		//$this->change_element_type($gender_field_name, 'radio_inline');	
	}
	
	
	function email_form_data_to_submitter()
	{
		$model =& $this->get_model();
		
		// Figure out who would get an email confirmation (either through a 
		// Your Email field or by knowing the netid of the submitter
		if (!$recipient = $this->get_value_from_label('Email'))
		{
			if ($submitter = $model->get_email_of_submitter())
				$recipient = $submitter.'@luther.edu';
		}
		
		// If we're supposed to send a confirmation and we have an address...
		if ($recipient)
		{
			// Use the (first) form recipient as the return address if available
			if ($senders = $model->get_email_of_recipient())
			{
				list($sender) = explode(',',$senders, 1);
				if (strpos($sender, '@') === FALSE)
					$sender .= '@luther.edu';
			} else {
				$sender = 'auto-form-process@luther.edu';
			}
			
			$thank_you = $model->get_thank_you_message();
			
			$email_values = $model->get_values_for_email_submitter_view();
				
			if (!($subject = $this->get_value_from_label('Confirmation Subject')))
				$subject = 'Thank you for requesting a visit';
			
			$values = "\n";
			if ($model->should_email_data())
			{
				foreach ($email_values as $key => $val)
				{
					$values .= sprintf("\n%s:   %s\n", $val['label'], $val['value']);
				}
				
				$high_school = $this->get_value('high_school');
				if ($high_school)
					$values .= "\n High School: " . $high_school;
					
				$graduation_year = $this->get_value('graduation_year');
				if ($graduation_year) 
					$values .= "\t Graduation Year: " . $graduation_year . "\n";
					
				$transfer = $this->get_value('transfer');
				$transfer_college = $this->get_value('transfer_college');
				if ($transfer_college || $transfer) 
					$values .= "\n Transfer College: " . $transfer_college . "\n";
					
				$meet_counselor = $this->get_value('meet_counselor');
				if ($meet_counselor) 
					$values .= "\n Meet with admissions counselor: Yes \n";
				
				$tour = $this->get_value('tour');
				if ($tour) 
					$values .= "\n Take campus tour: Yes \n";
				
				$meet_faculty = $this->get_value('meet_faculty');
				$meet_faculty_details = $this->get_value('meet_faculty_details');
				if($meet_faculty || $meet_faculty_details) 
					$values .= "\n Meet with faculty from: " .$meet_faculty_details."\n";

				$meet_second_faculty = $this->get_value('meet_second_faculty');
				$meet_second_faculty_details = $this->get_value('meet_second_faculty_details');
				if($meet_second_faculty || $meet_second_faculty_details) 
					$values .= "\n Meet with second faculty from: " . $meet_second_faculty_details ."\n";
					
				$observe_class = $this->get_value('observe_class');
				$observe_class_details = $this->get_value('observe_class_details');
				if($observe_class || $observe_class_details) 
					$values .= "\n Observe class: " .$observe_class_details."\n";
				
				$chapel = $this->get_value('chapel');
				if($chapel) 
					$values .= "\n Attend chapel: Yes \n";
					
				$lunch = $this->get_value('lunch');
				if($lunch) 
					$values .= "\n Eat lunch: Yes \n";
				
				$meet_coach = $this->get_value('meet_coach');
				$meet_coach_details = $this->get_value('meet_coach_details');
				if($meet_coach || $meet_coach_details) 
					$values .= "\n Meet with coach from: " .$meet_coach_details."\n";
								
				$choir = $this->get_value('choir');
				if($choir) 
					$values .= "\n Observe a choir rehearsal: Yes \n";
					
				$band = $this->get_value('band');
				if($band) 
					$values .= "\n Observe a band rehearsal: Yes \n";

				$orchestra = $this->get_value('orchestra');
				if($orchestra) 
					$values .= "\n Observe an orchestra rehearsal: Yes \n";
				
				$music_audition = $this->get_value('music_audition');
				$music_audition_details = $this->get_value('music_audition_details');
				if($music_audition || $music_audition_details) 
					$values .= "\n Perform a music audition for: " .$music_audition_details."\n";
					
				$meet_music_faculty = $this->get_value('meet_music_faculty');
				$meet_music_faculty_details = $this->get_value('meet_music_faculty_details');
				if($meet_music_faculty || $meet_music_faculty_details) 
					$values .= "\n Meet with music faculty from: " .$meet_music_faculty_details."\n";
				
				$additional_request = $this->get_value('additional_request');
				if($additional_request) 
					$values .= "\n Additional request: " .$additional_request. "\n";
					
				$overnight_housing = $this->get_value('overnight_housing');	
				$overnight_day = $this->get_value('overnight_day');
				if ($overnight_housing)
					$values .= "\n Overnight housing arrival information: ".$this->get_value('overnight_day');
					
				if ($overnight_day == "Night prior to visit")
					$values .= "  Arrival time: ".$this->get_value('overnight_prior_arrival_time');	
			}
			
			$html_body = $thank_you . nl2br($values);
			$txt_body = html_entity_decode(strip_tags($html_body));

			// Send thank you message and details of request to the requestor	
			$recipient_mailer = new Email($recipient, $sender, $sender, $subject, $txt_body, $html_body);
			$recipient_mailer->send();

			// Send details of the request to the administrator listed as recipient on the form builder
			$html_body2 = nl2br($values);	
			$txt_body2 = html_entity_decode(strip_tags($html_body2));	
			$subject2 = "IndividualVisitRequest".date(ymd);
			$admin_mailer = new Email($sender, $sender, $sender, $subject2, $txt_body2, $html_body2);
			$admin_mailer->send();
		}
	}		
	
	
	
	function run_error_checks()
	{
		//$val = $this->get_value('extra_field');
		//if (empty($val)) $this->set_error('extra_field', 'The field must have content');
	}
	
	function process()
	{
		// getting value from a disco field
		///$field_value = $this->get_value('extra_field');
//		foreach ($elements as $key => $val)
//		{
//			$extra_values = $this->get_value($elements)
//		}
		// getting disco field name from thor
		///$food_stuff_field_name = $this->get_element_name_from_label('Food Stuff');
		///$food_stuff_value = $this->get_value($food_stuff_field_name);
		///echo $food_stuff_value;
/*
		echo $this->get_value('high_school') . "\n";
		echo $this->get_element_name_from_label('graduation_year') . "\n";
		echo $this->get_value('graduation_year');
		echo $this->get_value('meet_faculty');
		echo $this->get_value('meet_faculty_details');
		echo 'blah';
*/
	}
	

	function should_my_custom_process()
	{
		return true;
	}

	
//	function where_to()
//	{
//		return false;
//	}
	
}
?>
