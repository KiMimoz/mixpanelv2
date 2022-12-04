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

if(!isset($_POST["searchuser5"]))
{
    $q = connect::$g_con->prepare("SELECT * FROM `mix_sys_match` ORDER BY `MatchID` DESC".this::limit());
    $q->execute();
}
else
{
    if(!is_numeric($_POST["searchuser5"]))
    {
        this::show_toastr('info', 'Match id need to be format only from number!', 'Info');

        return user::redirect_to("matches");
    }
    $q = connect::$g_con->prepare("SELECT * FROM `mix_sys_match` WHERE `MatchID` = ?");
    $q->execute(array($_POST["searchuser5"]));
    if(!$q->rowCount())
    {
        this::show_toastr('info', 'This match id does not exist!', 'Info');

        return user::redirect_to("matches");
    }
}
?>

<div class="mainArea-content">
    <div class="container">
        <div id="mainArea-body">
            <div id="mainArea-content-body">
                <div class="indexRow row"  id="index-ongoing-matches">
                    <div class="index-title">
                        <h1>All Matches</h1>
                        <?php
                        if(isset($_POST["searchuser5"]))
                        {
                        ?>
                            <button type="button" class="btn btn-outline-success waves-effect waves-dark float-right" onclick="window.location.href='<?php echo this::$_PAGE_URL; ?>matches'">
                                back
                            </button>
                        <?php
                        }
                        ?>
                        <form method="post">
                            <div id="search-content">
                                <input type="text" minlength="1" autocomplete="off" name="searchuser5" placeholder="Search with id" required>
                                <div class="search"></div>
                            </div>
                        </form>
                    </div>
                    <div class="indexRow-inner row">
                    <?php
                    while($data = $q->fetch(PDO::FETCH_OBJ))
                    {
                    ?>
                        <a class="col-xl-12 ix-matches succes" href="<?php echo this::$_PAGE_URL; ?>match/<?php echo $data->MatchID; ?>">
                            <div class="ix-match-header">
                                <div class="match-status-type status-type-<?php echo $data->Status==1?'finished':'live'; ?>">
                                    <span></span>
                                    <?php
                                        echo $data->Status==1?'Finished':'Ongoing';
                                    ?>
                                </div>
                                <h1>
                                <?php
                                    echo "#".$data->MatchID." | ".this::$_SERVER_NAME;
                                ?>
                                </h1>
                                <div class="match-game-icon">
                                <?php
                                    echo $data->Map;
                                ?>
                                </div>
                            </div>
                            <div class="match-body">
                                <div class="col-match-body">
                                    <h1 class="ix-match-title match-team-title">TEAM BLUE</h1>
                                    <h1 class="ix-match-title match-team-title-responsive">BLUE</h1>
                                </div>
                                <div class="col-match-body ix-matches-col-center">
                                    <div class="ix-matches-col-inner">
                                        <span class="match-score2 <?php echo $data->CTScore>$data->TScore?'match-winned':''; ?>">
                                        <?php
                                            echo $data->CTScore;
                                        ?>
                                        </span>
                                        <div class="ix-match-sinfo">
                                            <span class="match-status status-ended">
                                                Winners:
                                                <span class="match-winned">
                                                <?php
                                                    echo $data->Winner;
                                                ?>
                                                </span>
                                            </span>
                                            <span class="match-duration">
                                                Ended after:
                                                <?php
                                                    echo $data->Duration;
                                                ?>
                                            </span>
                                        </div>
                                        <span class="match-score2 <?php echo $data->TScore>$data->CTScore?'match-winned':''; ?>">
                                        <?php
                                            echo $data->TScore;
                                        ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-match-body col-right">
                                    <h1 class="ix-match-title match-team-title">TEAM RED</h1>
                                    <h1 class="ix-match-title match-team-title-responsive">RED</h1>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if(!isset($_POST["searchuser5"]))
        {
            echo this::create(connect::rows('mix_sys_match'));
        }
        ?>
    </div>
</div>