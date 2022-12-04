<?php
    if(this::getSpec("panel_settings","Maintenance","ID",1))
    {
        if(user::isLogged())
        {
            if(user::getUserData()->Boss < 1)
            {
                return user::redirect_to("maintenance");
            }
        }
        else
        {
            return user::redirect_to("maintenance");
        }
    }

    if(user::isLogged()||user::$ENABLE_REGISTRATION!=1)//eeeehehehe
    {
        return user::redirect_to("");
    }

	if(isset($_POST['createaccount']))
	{
		$ip = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `IP` = ? OR `LastIP` = ?");
		$ip->execute(array(user::GetIp(), user::GetIp()));
		$find = $ip->rowCount();
		if($find)
        {
			this::show_toastr('error', 'Error: This ip is marked as used already...', 'Error');

            return user::redirect_to("register");//ehh
		}

        trim($_POST['nume']);

		$user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `name` = ?");
		$user->execute(array($_POST['nume']));
		$find = $user->rowCount();
		if($find)
        {
			this::show_toastr('error', 'Error: This nick is already reserved on the server.', 'Error');

            return user::redirect_to("register");
		}

        if(user::$ENABLE_REGISTRATION_WITH_STEAM==1)
        {
            trim($_POST['steam']);
            if(!this::IsValidSteamStr($_POST['steam']))
            {
                this::show_toastr('error', 'This is not a valid steamid.', 'Error');

                return user::redirect_to("register");
            }
            $user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `auth` = ?");
            $user->execute(array($_POST['steam']));
            $find = $user->rowCount();
            if($find)
            {
                this::show_toastr('error', 'This steamid is already reserved on the server.', 'Error');

                return user::redirect_to("register");
            }
        }

        if(this::getSpec('bans', 'victim_ip', 'victim_ip', user::GetIp())||this::getSpec('bans', 'victim_name', 'victim_name', $_POST['nume']))
        {
            return this::show_toastr('error', 'It seems your account is banned..', 'Error', 0, 1, "");
        }

        trim($_POST['email']);

        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            this::show_toastr('error', 'This is not a valid email address.', 'Error');

            return user::redirect_to("register");
        }
        $user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `email` = ?");
        $user->execute(array($_POST['email']));
        $find = $user->rowCount();
        if($find)
        {
            this::show_toastr('error', 'Error: This email is already reserved on the server.', 'Error');

            return user::redirect_to("register");
        }

        trim($_POST['parola']);

        if(!user::$ENABLE_REGISTRATION_WITH_STEAM)
        {
            $q = connect::$g_con->prepare("INSERT INTO `admins` (`name`, `password`, `access`, `flags`, `email`, `IP`) VALUES (?, ?, '', 'z', ?, ?);");
        }
        else
        {
            //$q = connect::$g_con->prepare("INSERT INTO `admins` (`name`, `password`, `access`, `flags`, `email`, `IP`, `LastIP`, `id`, `auth`) VALUES (?, ?, '', 'z', ?, ?, ?, ?, ?);");
            $q = connect::$g_con->prepare("INSERT INTO `admins` (`name`, `password`, `access`, `flags`, `email`, `IP`, `auth`, `steamid64`) VALUES (?, ?, '', 'z', ?, ?, ?, ?);");
        }
        $q->bindParam(1, $purifier->purify(this::xss_clean(this::clean($_POST['nume']))));
        $q->bindParam(2, $purifier->purify(this::xss_clean(this::clean($_POST['parola']))));
        $q->bindParam(3, $purifier->purify(this::xss_clean(this::clean($_POST['email']))));//mdea
        $q->bindParam(4, user::GetIp());
        if(user::$ENABLE_REGISTRATION_WITH_STEAM==1)
        {
            trim($_POST['steam']);
            $q->bindParam(5, $purifier->purify(this::xss_clean(this::clean($_POST['steam']))));
            $q->bindParam(6, this::SteamStr2SteamId($_POST['steam']));
        }
		$q->execute();

        $q3 = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `name` = ? AND `email` = ? AND `password` = ?');
        $q3->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['nume']))),$purifier->purify(this::xss_clean(this::clean($_POST['email']))),$purifier->purify(this::xss_clean(this::clean($_POST['parola'])))));
        $get_user_info = $q3->fetch(PDO::FETCH_OBJ);//la sigur da

        if(user::$ENABLE_REGISTRATION_WITH_STEAM==1)
        {
            $q2 = connect::$g_con->prepare("INSERT INTO `points_sys` (`ID`, `Name`, `FirstJoined`, `SteamID`) VALUES (?, ?, ?, ?);");
        }
        else
        {
            $q2 = connect::$g_con->prepare("INSERT INTO `points_sys` (`ID`, `Name`, `FirstJoined`) VALUES (?, ?, ?);");
        }
        $q2->bindParam(1, $get_user_info->id);
        $q2->bindParam(2, $purifier->purify(this::xss_clean(this::clean($_POST['nume']))));
        $q2->bindParam(3, this::getDaT());
        if(user::$ENABLE_REGISTRATION_WITH_STEAM==1)
        {
            $q2->bindParam(4, $purifier->purify(this::xss_clean(this::clean($_POST['steam']))));
        }
        $q2->execute();

		this::show_toastr('success', 'You successfully reigstred! Before connecting on the server, type in console <b>'.user::$USER_SV_LOGIN_INFO.'</b>.', 'Success');

		return user::$ENABLE_LOGIN==1?user::redirect_to("login"):user::redirect_to("");
	}
?>

<div class="modal fade" id="termsandconditions" tabindex="-1" role="dialog" aria-labelledby="termsandconditionsLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="termsandconditionsLabel">Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
        		<p>
                    Terms
                    "
                    <?php
                        echo this::$_SITE_NAME;
                    ?>
                    "
                    and "service/services" represent services offered through website
                    <b>
                        <font color="gold">
                        <?php
                            echo this::$_SITE_TITLE;
                        ?>
                        </font>
                    </b> 
            		and CS.16 server
                    <b>
                        <font color="gold">
                        <?php
                            echo this::$_SERVER_NAME;
                        ?>
                        </font>
                    </b>
                    .
                </p>
                <ul>
                    <li>All donations are final and refund will be possible only if we cannot deliver the service which you donated for.</li>
            		<li>Please read carefully details about the service you want to donate for and understand what you'll receive.</li>
                    <li>
                        If you donate for one service, this doesn't means that you can break our rules. Rules must be respected by all players/admins. 
            		      You can check our <b><a href="<?php echo this::$_FORUM_DIRECT_LOCATION; ?>" target="_blank"><u style="text-decoration: underline;">rules HERE</u></a></b>
                    </li>
                    <li>
                        If we delivered the service according to the donation and after it is used the option of "<b>refund</b>" on paypal, this will be clearly lead
            		      to a remove of the services and ban on the server/web
                    </li>
                    <li>
                        If a player made a donation and is caught <b><font color="red">cheating</font></b> on server, he/she will receive 
            		      <b><font color="red">permanent ban</font></b>. <b><font color="red">No refund</font></b> will be possible in this case.
                    </li>
                </ul>
        		<hr>
                <h3>Privacy policy</h3>
                Information collected from users are limited to email address and IP. This is ensure the safety of their account.<br/>
                <br><br>
                <b>Cookies</b>
                <br><br>
                Our website use cookies to improve users experience. 
                <br><br>
                <b>Protection of collected informations</b>
                <br><br>
                We ensure you that we do our best to protect your data and we will never give these information to third parties, without your prior consent
                We do not sell, exchange or rent your informations to third parties.
                <br><br>
                <b>Third party websites</b>
                <br><br>
                Users can find advertisements on our website, or content which includes links to other websites. 
        		We try to control as much as possible what contect we promote on our website or server, but we are not responsible about what you'll find there.<br><br>
                <b>Changes and Updates</b>
                <br><br>
                We can modify anytime privacy policy. Anyway, when this will happen, all users will receive a notification email with this change and requested to agree 
        		with. Otherwise, access on our website can be restricted.
                <br><br>
        		Using this site and registering on our database, means your consent and approval for our terms.
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
		</div>
	</div>
</div>

<div class="mainArea-content">
    <div class="container">
        <div class="indexRow row" id="index-ongoing-matches">
            <div class="card-header text-center">
                <center>
                    <h3>
                        <b>
                            REGISTER
                        </b>
                    </h3>
                </center>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <form method="post">
                        <div class="row mb-3">
                            <div class="input-group mb-3">
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nume" placeholder="Nick" minlength="3" maxlength="33" required aria-label="Recipient's nick" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group-text">
                                    <span class="fa fa-user-shield" id="basic-addon1"></span>
                                </div>
                            </div>
                        </div>
                        <?php
                        if(user::$ENABLE_REGISTRATION_WITH_STEAM==1)
                        {
                            //sau cu opt. pt required..
                        ?>
                            <div class="row mb-3">
                                <div class="input-group mb-3">
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="steam" placeholder="SteamID" minlength="8" required aria-label="Recipient's steamid" aria-describedby="basic-addon4">
                                    </div>
                                    <div class="input-group-text">
                                        <span class="fa-brands fa-steam-symbol" id="basic-addon4"></span>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="input-group mb-3">
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="parola" name="parola" minlength="5" maxlength="15" placeholder="Password" required aria-label="Recipient's pass" aria-describedby="basic-addon2">
                                </div>
                                <div class="input-group-text">
                                    <span class="fa-solid fa-key" id="basic-addon2"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="input-group mb-3">
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required aria-label="Recipient's mail" aria-describedby="basic-addon3">
                                 </div>
                                <div class="input-group-text">
                                    <span class="fa-solid fa-envelope-circle-check" id="basic-addon3"></span>
                                </div>
                            </div>
                        </div>
                        <br>
        				<div align="left" class="icheck-primary">
        					<input type="checkbox" id="agreeTerms" required>
        					<label for="agreeTerms">
        					   Please read the <a data-toggle="modal" data-target="#termsandconditions"><u style="text-decoration: underline;">rules here</u></a>, then tick the box.
        					</label>
        				</div>
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <button type="submit" name="createaccount" class="btn btn-primary btn-block toastrSuccess">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
                if(user::$ENABLE_LOGIN==1)
                {
                ?>
                    <br>
                    <p class="mb-1" align="left">
                        <a href="<?php echo this::$_PAGE_URL; ?>login">You already have an account? Log In</a>
                    </p>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>