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

if(isset($_POST['login']) /*&& !user::isLogged()*/)
{
    trim($_POST['your_auth']);
    trim($_POST['your_password']);
    
    if(user::$ENABLE_REGISTRATION_WITH_STEAM)
    {
        $que = connect::$g_con->prepare('SELECT * FROM `admins` WHERE ( `name` = ? OR `auth` = ? OR `email` = ? ) AND `password` = ?');
        $que->execute(array($_POST['your_auth'],$_POST['your_auth'],$_POST['your_auth'],$_POST['your_password']));
    }
    else
    {
        $que = connect::$g_con->prepare('SELECT * FROM `admins` WHERE ( `name` = ? OR `email` = ? ) AND `password` = ?');
        $que->execute(array($_POST['your_auth'],$_POST['your_auth'],$_POST['your_password']));
    }
    if($que->rowCount())
    {
        $inter = $que->fetch(PDO::FETCH_OBJ);
        if(this::getSpec("panel_settings","IPLoginVerify","ID",1))//hmm
        {
            if($inter->LastIP == user::GetIp())
            {
                $_SESSION['user'] = $inter->id;
            } 
            else
            {
                this::show_toastr('error', 'Your IP is not the same as last registered IP.<br>First login into the game, before to login on here.', 'IP mismatch', 1);

                //return user::redirect_to("login");
            }
        }
        else
        {
            $_SESSION['user'] = $inter->id;

            $update_some_user_data = connect::$g_con->prepare('UPDATE `admins` SET `LastIP` = ?, `online` = ? WHERE `id` = ?');
            $update_some_user_data->execute(array(user::GetIp(), 1, $inter->id)); //<button type="button" class="btn btn-success toastrSuccess">

            $update_some_user_data2 = connect::$g_con->prepare('UPDATE `points_sys` SET `LastOnline` = ? WHERE `id` = ?');
            $update_some_user_data2->execute(array(this::getDaT(), $inter->id));

            this::show_toastr('success', 'You logged in successfully!', 'Success');

            user::redirect_to("");
        }
    }
    else
    {
        this::show_toastr('error', '<b>Invalid login credentials.</b>', 'Error!', 1);

        //return user::redirect_to("login");
    }
}
?>

<div class="mainArea-content">
    <div class="container">
        <div class="indexRow row" id="index-ongoing-matches">
            <div class="index-title">
                <h1>Login</h1>
            </div>
            <div class="indexRow-inner row">
                <div id="pagination-loader">
                    <div id="mainArea-body">
                        <div id="mainArea-content-body">
                            <div id="paginations">
                                <div class="table-loader">
                                    <div id="spinner-loader">
                                        <div class="spinner">
                                            <div class="inner one"></div>
                                            <div class="inner two"></div>
                                            <div class="inner three"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-overflow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <form method="post">
                                                <div class="row mb-3">
                                                    <div class="input-group mb-3">
                                                        <div class="col-sm-9">
                                                        <?php 
                                                            if(user::$ENABLE_REGISTRATION_WITH_STEAM)
                                                            {
                                                        ?>
                                                                <input type="text" class="form-control" name="your_auth" placeholder="Nick/SteamID/Email" aria-label="Recipient's auth" aria-describedby="basic-addon1" required>
                                                        <?php 
                                                            }
                                                            else
                                                            {
                                                        ?>
                                                                <input type="text" class="form-control" name="your_auth" placeholder="Nick/Email" aria-label="Recipient's auth" aria-describedby="basic-addon1" required>
                                                        <?php 
                                                            }
                                                        ?>
                                                        </div>
                                                        <div class="input-group-text">
                                                            <span class="fa fa-user-shield" id="basic-addon1"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="input-group mb-3">
                                                        <div class="col-sm-9">
                                                            <input type="password" class="form-control" name="your_password" placeholder="Password" aria-label="Recipient's pass" aria-describedby="basic-addon2" required>
                                                        </div>
                                                        <div class="input-group-text">
                                                            <span class="fa-solid fa-key" id="basic-addon2"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <button type="submit" name="login" class="btn btn-primary btn-block toastrSuccess">Log In</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <br/>
                                        <?php
                                        if(user::$ENABLE_AUTO_PW_RECOVER==1)
                                        {
                                        ?>
                                            <p class="mb-1" align="left">
                                                <a href="<?php echo this::$_PAGE_URL; ?>recover">I forgot my password (RECOVER)</a>
                                            </p>
                                        <?php
                                        }
                                        if(user::$ENABLE_REGISTRATION==1)
                                        {
                                        ?>
                                            <p class="mb-0" align="left">
                                                <a href="<?php echo this::$_PAGE_URL; ?>register" class="text-center">You don't have an account? Register now</a>
                                            </p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>