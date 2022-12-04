<?php
//ob_start();

if(this::$_ENABLE_DEBUG!=0)
{
	error_reporting(0);
}

if(!file_exists('views/' . this::$_url[0] . '.ic.php') && strlen(this::$_url[0]))//ehh
{
	return user::redirect_to("");
}

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

/*$playerdata = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `id` = ?');
$playerdata->execute(user::get());
$player = $playerdata->fetch(PDO::FETCH_OBJ);*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="pPCJwmlK4v7SWiyGFbPbHNXr3Sw7uK1cbZyul8Df" name="csrf-token">

	<title>
	<?php
		if(this::getSpec("panel_settings","Maintenance","ID",1))
		{
			echo '(maintenance)';
		}
		echo this::$_SITE_TITLE;
	?>
	</title>
								<!-- png def -->
	<link rel="shortcut icon" type="image/jpg" href="https://icon-library.com/images/counterstrike-icon/counterstrike-icon-17.jpg"/>

	<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link href="https://unpkg.com/tippy.js@6/animations/scale.css" rel="stylesheet">
	<link href="<?php echo this::$_PAGE_URL; ?>resources/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css" integrity="sha512-HcfKB3Y0Dvf+k1XOwAD6d0LXRFpCnwsapllBQIvvLtO2KMTa0nI5MtuTv3DuawpsiA0ztTeu690DnMux/SuXJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-fontpicker/1.4.4/jquery.fontpicker.min.css" integrity="sha512-uJUBCPYgjwO2/2XiWW0UxlqlF7wX3neoE2bf84niljfavkDGHtvkPqBUWIcIFjhHysuEca3Fl9k2C7j1Z45Qrw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="<?php echo this::$_PAGE_URL; ?>resources/css/iconpicker-1.5.0.css" rel="stylesheet">
	<link href="<?php echo this::$_PAGE_URL; ?>resources/css/custom.css" rel="stylesheet">
	
	<style type="text/css">
		.modal
		{
			z-index: 11111;
		}
		.modal-open
		{
		    overflow: hidden;
		}

		.modal-content
		{
			background-color: #000;
			border: 1px solid rgba(255, 255, 255, 0.2);
		}
		.modal-header
		{
			border-bottom: 1px solid #212529;
		}
		.modal-footer
		{
			border-top: 1px solid #212529;
		}
		.modal-backdrop.show
		{
			opacity: 0.75;
		}

		button.close
		{
			background-color: #000;
			color: white;
		}
		@media screen and (max-width: 800px)
		{
			button.close
			{
				border: transparent;
			}
		}

		.popover
		{
			z-index: 11112;
		}

		hr.vertical
		{
		  width: 0px;
		  height: 100%;
		  /* or height in PX */
		}

		.font-picker .fp-modal
		{
			z-index: 99999;
		}
	</style>

	<script src="//cdn.ckeditor.com/4.19.1/full/ckeditor.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<script type="text/javascript">
		toastr.options =
		{
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": true,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": true,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "3500",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
	</script>
</head>

<body class="antialiased" id="page-top">
	<?php
	if(user::isLogged())
	{
	?>
		<div id="logout-up-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="logout-up-modalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title" id="logout-up-modalLabel">Logout</h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                	<span aria-hidden="true">X</span>
		                </button>
		            </div>
					<div class="modal-body">
					    <form method="post">
					        <div class="form-group">
					            <h4 align="center">Are you sure you want to logout?</h4>
					        </div>
					        <hr>
					        <div align="center">
					            <button type="submit" name="amviatasociala" class="btn btn-info btn-block">Yes, i'm sure!</button>
					        </div>
					    </form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
		        </div>
		    </div>
		</div>
	<?php
		$check_notif = connect::$g_con->prepare("SELECT * FROM `panel_notifications` WHERE `UserID` = ? AND `Seen` = ? ORDER BY ID ASC");
		$check_notif->execute(array(user::get(), 0));
		if($check_notif->rowCount())
		{
			$get_notif = $check_notif->fetch(PDO::FETCH_OBJ);

			this::show_toastr('info', 'Notification text: '.$get_notif->Notification.'', 'You have an new notification from '.$get_notif->Date.'', 1);

		    $update_some_user_data3 = connect::$g_con->prepare('UPDATE `panel_notifications` SET `Seen` = ?, `Readed` = ? WHERE `ID` = ?');
		    $update_some_user_data3->execute(array(1, this::getDaT(), $get_notif->ID));
		}
	}
	?>

	<section id="privacy-policy">
		<div class="container">
			<div class="policy-header">
				<h1>Privacy Policy</h1>
				<span id="privacy-close"><i class="fa-solid fa-xmark"></i></span>
			</div>
			<div class="privacy-policy-content">
				<div class="policy-row">
					<h1>General Informations</h1>
					<p>
						Your privacy is important to us.
						It is
						<?php
							echo this::$_SITE_TITLE;
						?>
						's policy to respect your privacy regarding any information we may collect from you across our website and other sites we own and operate.
					</p>
					<p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we're collecting it and how it will be used.</p>
					<p>We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we'll protect within commercially acceptable means to prevent loss and theft, as well as unauthorized access, disclosure, copying, use or modification.</p>
					<p>We don't share any personally identifying information publicly or with third-parties, except when required to by law.</p>
					<p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.</p>
					<p>You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services.</p>
					<p>Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us.</p>
				</div>
				<div class="policy-row">
					<h1>Cookies</h1>
					<p>Our Site may use “cookies” to enhance User experience. User's web browser places cookies on their hard drive for record-keeping purposes and sometimes to track information about them. User may choose to set their web browser to refuse cookies, or to alert you when cookies are being sent. If they do so, note that some parts of the Site may not function properly.</p>
				</div>
				<div class="policy-row">
					<h1>Questions?</h1>
					<p>
						Have a question about our privacy policy?
						<?php
							echo this::$_CONTACT_EMAIL;
						?>
					</p>
				</div>
			</div>
			<div class="policy-row-custom">
				<div id="timezone"></div><span>This policy is effective as of <b>28 April 2022</b>.</span>
			</div>
		</div>
	</section>

	<div id="bladeApp">
		<aside id="sidebar-menu" class="">
			<div class="sidebar-menu-inner">

	      		<span id="sidebarClose">
					<i class="fa-solid fa-xmark"></i>
				</span>

				<a href="<?php echo this::$_PAGE_URL; ?>" id="siteLogo">
					<img alt="logo" class="siteLogo-text" src="https://i.imgur.com/Rmjp1OW.png">
				</a>

				<nav id="sidebar-navbar">
					<div class="navbarItems-group">
						<?php
						if(user::isLogged())
						{
							if(user::getUserData()->Admin >= 5)
							{
						?>
								<li>
									<a class="navbar-item<?php echo this::$_CURRENT_PAGE==3?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>owner">
										<div class="navbar-item-icon">
											<i class="fa-brands fa-superpowers"></i>
										</div>
										<div class="navbar-item-text">Manage Accounts</div>
									</a>
								</li>
								<li>
									<a class="navbar-item<?php echo this::$_CURRENT_PAGE==4?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>panel">
										<div class="navbar-item-icon">
											<i class="fa-solid fa-list-check"></i>
										</div>
										<div class="navbar-item-text">Manage Panel</div>
									</a>
								</li>
								<?php
								if(this::$_ENABLE_RSC==1)
								{
								?>
									<li>
										<a class="navbar-item<?php echo this::$_CURRENT_PAGE==9?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>server">
											<div class="navbar-item-icon">
												<i class="fa-solid fa-server"></i>
											</div>
											<div class="navbar-item-text">Manage Server</div>
										</a>
									</li>
							<?php
								}
							?>
						<hr>
						<?php
							}
						}
						?>
						<li>
							<a class="navbar-item<?php echo this::$_CURRENT_PAGE==-1?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>">
								<div class="navbar-item-icon">
									<i class="fa-brands fa-dashcube"></i>
								</div>
								<div class="navbar-item-text">Dashboard</div>
							</a>
						</li>
						<li>
							<a class="navbar-item<?php echo this::$_CURRENT_PAGE==5?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>staff">
								<div class="navbar-item-icon">
									<i class="fa-solid fa-screwdriver-wrench"></i>
								</div>
								<div class="navbar-item-text">Staff</div>
							</a>
						</li>
						<li>
							<a class="navbar-item<?php echo this::$_CURRENT_PAGE==6?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>matches">
								<div class="navbar-item-icon">
									<i class="fa-history fa"></i>
								</div>
								<div class="navbar-item-text">Matches</div>
							</a>
						</li>
						<li>
							<a class="navbar-item<?php echo this::$_CURRENT_PAGE==7?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>players">
								<div class="navbar-item-icon">
									<i class="fa-solid fa-face-smile-beam"></i>
								</div>
								<div class="navbar-item-text">Players</div>
							</a>
						</li>
						<li>
							<a class="navbar-item<?php echo this::$_CURRENT_PAGE==8?' active':''; ?>" href="<?php echo this::$_PAGE_URL; ?>banlist">
								<div class="navbar-item-icon">
									<i class="fa-lock fa"></i>
								</div>
								<div class="navbar-item-text">Bans</div>
							</a>
						</li>
						<?php
						if(!user::isLogged()&&user::$ENABLE_REGISTRATION==1)
						{
							if(this::$_CURRENT_PAGE!=2)
							{
						?>
								<li>
									<a class="navbar-item" href="<?php echo this::$_PAGE_URL; ?>register">
										<div class="navbar-item-icon">
											<i class="fa-solid fa-user-plus"></i>
										</div>
										<div class="navbar-item-text">Register</div>
									</a>
								</li>
						<?php
							}
						}
						?>
					</div>
					<div class="navbarItems-group">
						<!--<ul>-->
							<li>
								<a class="navbar-item" id="privacy-open">
									<div class="navbar-item-icon">
										<i class="fa-solid fa-exclamation"></i>
									</div>
									<div class="navbar-item-text">
										Privacy Policy
									</div>
								</a>
							</li>
							<!--<li>
								<a class="navbar-item" href="">
								<div class="navbar-item-icon">
									<i class="fa-bug fal"></i>
								</div>
								<div class="navbar-item-text">
									Report a bug
								</div></a>
							</li>
							<li>
								<a class="navbar-item" href="">
								<div class="navbar-item-icon">
									<i class="fa-lightbulb fal"></i>
								</div>
								<div class="navbar-item-text">
									Documentation
								</div></a>
							</li>-->
						<!--</ul>-->
					</div>
				</nav>
			</div>
		</aside>

		<main id="body-content">
			
			<header id="header">
				<div class="container">
					<span id="openSidebar">
						<i class="fa-solid fa-bars"></i>
					</span>
					
					<?php
					if(user::$ENABLE_LOGIN==1)
					{
						if(user::isLogged())
						{
					?>
							<div class="btn-group">
							  <button class="btn btn-secondary btn-sm" type="button" onclick="location.href='<?php echo user::MakeProfileUrl(user::get()); ?>';">
							    <i class="fa-solid fa-user"></i> <?php echo user::getUserData()->name; ?>
							  </button>
							  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    <span class="sr-only">Toggle Dropdown</span>
							  </button>
							  <div class="dropdown-menu">
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Level: <b>".user::getUserData()->Admin."</b>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Flags: <b>".user::getUserData()->access."</b>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Group: <b style='font-family: ".this::getSpec('panel_groups', 'funcFontFamily', 'groupAdmin', user::getUserData()->Admin).";'><span class='badge' style='background-color: ".this::getSpec('panel_groups', 'groupColor', 'groupAdmin', user::getUserData()->Admin).";'><font color='".this::getSpec('panel_groups', 'funcColor', 'groupAdmin', user::getUserData()->Admin)."'><i class='".this::getSpec('panel_groups', 'funcIcon', 'groupAdmin', user::getUserData()->Admin)."'></i> ".this::getSpec('panel_groups', 'groupName', 'groupAdmin', user::getUserData()->Admin)."</font></b></span>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Current IP: <b>".user::GetIp()."</b>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Last IP: <b>".user::getUserData()->LastIP."</b>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Current D&T: <b>".this::getDaT()."</b>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Last Panel-Login: <b>".user::getUserData()->LastPanelLogin."</b>";
							  	?>
							  	</a>
							  	<a class="dropdown-item">
							  	<?php
							  		echo "Warns: <b>".user::getUserData()->warn."</b>";
							  	?>
							  	</a>
							    <div class="dropdown-divider"></div>
					  			<a onclick="$('#logout-up-modal').modal();" href="javascript:void(0)" class="dropdown-item"><!--hmm-->
					  				<i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
					  			</a>
							  </div>
							</div>
			 	 	<?php
			 			}
				  		else
				  		{
				  			if(this::$_CURRENT_PAGE!=1)
				  			{
				  	?>
								<li class="nav-item">
				        		  <a href="<?php echo this::$_PAGE_URL; ?>login" class="nav-link">
				        		  	<i class="fa-solid fa-arrow-right-to-bracket"></i> Login
				        		  </a>
				        		</li>
		        	<?php
		        			}
		        		}
	        		}
	        		?>
				</div>
			</header>

			<nav id="breadcrumb">
				<ul>
					<li>
					</li>
				</ul>
			</nav>

<?php
if(isset($_SESSION['msg']))
{
	if(!empty($_SESSION['msg']))
	{
		echo $_SESSION['msg'];

		$_SESSION['msg'] = '';
	}
}
?>