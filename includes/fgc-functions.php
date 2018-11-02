<?php

// List all users
function fgc_list_users() {

	// Check for user capabilities
	if ( !current_user_can('manage_options')) {
		return;
	}

	$cont = 0;
	$users = get_users();


	$table ="<table class='fgc  widefat '>";
	$table .= "<tr>";
		$table .= "<th>#</th>";
		$table .= "<th>Name</th>";
		$table .= "<th>Email</th>";
		$table .= "<th>User Type</th>";
		$table .= "<th>Status</th>";
		$table .= "<th>Actions</th>";
	$table .= "</tr>";

	foreach ( $users as $user ) {
			$cont++;

			$user_data = get_userdata( $user->ID);
			$user_type = $user_data->roles[0];

			$is_activated = get_user_meta($user->ID,'activated',true);

			if($is_activated == '' OR $is_activated == 'true')
				$user_status = 'Activate';
			elseif ($is_activated ==  'false')
				$user_status = 'Deactivate';

			$table .= "<tr>";
				$table .= "<td>$cont</td>";
				$table .= "<td>$user->display_name</td>";
				$table .= "<td>$user->user_email</td>";
				$table .= "<td>$user_type</td>";
				$table .= "<td>$user_status</td>";
				$table .= "<td> <a href='/wp-admin/admin.php?page=fgc-users-management-edit&user-id=$user->ID' class='fgc-button'>Edit</a>
								<a href='/wp-admin/admin.php?page=fgc-users-management-deactivate&user-id=$user->ID' class='fgc-button'>Change status</a>
							</td>";
			$table .= "</tr>";
	}


?>
	<div class="wrap">
		<h1><?php esc_html_e(get_admin_page_title()) ?> </h1>
		<?php echo $table; ?>
	</div>

<?php

}



// Edit User page
function fgc_edit_user(){

	// Check for user capabilities
	if ( !current_user_can('manage_options')) {
		return;
	}


	//Variables
	$user_updated = '';

	if(isset($_GET['user-id']))
		$user_id = $_GET['user-id'];
	else
		$user_id = 1;


	if(isset($_POST['updated']))
		$user_updated = fgc_update_user($_POST);

	echo $user_updated;
	$user = get_user_by('ID', $user_id);



?>
	<div class="wrap">
		<h1><?php esc_html_e(get_admin_page_title()) ?> <small><?php echo $user->display_name; ?></small></h1>
		<form class="fgc-form" action="" method="POST">

			<label for="first_name">First Name</label>
			<input type="text" name="first_name" value="<?php echo $user->first_name ?>" placeholder="First Name">

			<label for="last_name">Last Name</label>
			<input type="text" name="last_name" value="<?php echo $user->last_name ?>" placeholder="Last Name">

			<label for="display_name">Display Name</label>
			<input type="text" name="display_name" value="<?php echo $user->display_name ?>" placeholder="Display Name">



			<label for="email">Email</label>
			<input type="email" name="email" value="<?php echo $user->user_email ?>" disabled>
			<p class='info-text'>You can't change the email</p>

			<input type="hidden" value="<?php echo $user_id ?>" name="user_id" >
			<input type="hidden" name='updated' value="true" >

			<button>Update</button>
		</form>
	</div>


<?php
}


//Update User
function fgc_update_user($data){

	$user_id = $data['user_id'];

	update_user_meta($user_id,'first_name',$data['first_name']);
	update_user_meta($user_id,'last_name',$data['last_name']);
	update_user_meta($user_id,'display_name',$data['display_name']);


	$message = '<div class="fgc-alert success">User updated</div>';
	return $message;
}




// Deactivate  User page
function fgc_deactivate_user(){

	// Check for user capabilities
	if ( !current_user_can('manage_options')) {
		return;
	}


	//Variables
	$user_deactivate = '';

	if(isset($_GET['user-id']))
		$user_id = $_GET['user-id'];
	else
		$user_id = 1;


	if(isset($_POST['updated']))
		$user_deactivate = fgc_change_user_status($_POST);

	echo $user_deactivate;

	$user = get_user_by('ID', $user_id);

	$is_activated = get_user_meta($user_id,'activated',true);

	if($is_activated == '' OR $is_activated == true){
		$user_status = 'Activate';
		$is_activated = true;
	}	else {
		$user_status = 'Deactivate';
	}


?>
	<div class="wrap">
		<h1><?php esc_html_e(get_admin_page_title()) ?> <small><?php echo $user->display_name; ?></small></h1>
		<form class="fgc-form" action="" method="POST">


			<p>Current Status: <?php echo $user_status ?></p>

			<input type="checkbox" name="status" checked=<?php echo $is_activated ?>>


			<input type="hidden" value="<?php echo $user_id ?>" name="user_id" >
			<input type="hidden" name='updated' value="true" >

			<button>Change status</button>
		</form>
	</div>


<?php
}


//Change User
function fgc_change_user_status($data){
	$user_id = $data['user_id'];


	if(isset($data['status']) AND $data['status'] == 'on'){
		update_user_meta($user_id,'activated','true');
		$message = '<div class="fgc-alert success">User activated</div>';
	}	else {
		update_user_meta($user_id,'activated','false');
		$message = '<div class="fgc-alert success">User deactivated</div>';
	}




	return $message;
}


// Redirect to specific page if user is deactivated
function fgc_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?

    $user_id = get_current_user_id();
    $is_activated = get_user_meta($user_id,'activated',true);

    if ($is_activated == 'false')
            $redirect_to =  home_url().'/user-deactivated';


    return $redirect_to;
}

add_filter( 'login_redirect', 'fgc_login_redirect', 10, 3 );

// Load CSS and JS
function fgc_admin_assets() {
	wp_enqueue_style( 'fgc-styles', FGC_URL . 'assets/fgc-style.css', [],'1');
	wp_enqueue_script('fgc-js',	FGC_URL . 'assets/fgc-app.js',[],'1');
}

add_action('admin_enqueue_scripts', 'fgc_admin_assets',100);