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

$q = connect::$g_con->prepare("SELECT * FROM `mix_sys_match` ORDER BY `MatchID` DESC".this::limit());
$q->execute();

$ann = connect::$g_con->prepare("SELECT * FROM `panel_news` ORDER BY `id` DESC".this::limit());//de fkt cu view all announces gen? matches =)
$ann->execute();
$find_ann=$ann->rowCount();
?>

<div class="mainArea-content">
    <div class="container">
        <div id="mainArea-body">
            <div id="mainArea-content-body">
                <div class="indexRow row" id="index-ongoing-matches">
                    <div class="index-title">
                        <h1>Match in progress</h1>
                    </div>
                    <div class="indexRow-inner row">
                    <?php
                        if(this::getSpec('mix_sys_match','Status','Status',0)/*==0*/)
                        {
                            echo 'No live match for now';
                        }
                        else
                        {
                            //while cu getspec ..
                            $qr = connect::$g_con->prepare("SELECT * FROM `mix_sys_match` ORDER BY `MatchID` WHERE `Status` = ?");
                            $qr->execute(array(1));
                            while($onmatch = $qr->fetch(PDO::FETCH_OBJ))
                            {
                    ?>
                               <a class="col-xl-12 ix-matches succes" href="<?php echo this::$_PAGE_URL; ?>match/<?php echo $onmatch->MatchID; ?>">
                                    <div class="ix-match-header">
                                        <div class="match-status-type status-type-live">
                                            <span></span> Ongoing
                                        </div>
                                        <h1>
                                        <?php
                                            echo "#".$onmatch->MatchID." | ".this::$_SERVER_NAME;
                                        ?>
                                         </h1>
                                        <div class="match-game-icon">
                                        <?php
                                            echo $onmatch->Map;
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
                                                <span class="match-score2 match-<?php echo $onmatch->CTScore>$onmatch->TScore?'winned':'lost'; ?>">
                                                <?php
                                                    echo $onmatch->CTScore;
                                                ?>
                                                </span>
                                                <div class="ix-match-sinfo">
                                                    <span class="match-status status-ended">
                                                        Leaders:
                                                        <span class="match-winned">
                                                        <?php
                                                            echo $onmatch->CTScore>$onmatch->TScore?'Blue':'Red';
                                                        ?>
                                                        </span>
                                                    </span>
                                                    <span class="match-duration">
                                                        Started at:
                                                    <?php
                                                        echo $onmatch->Started;
                                                    ?>
                                                    </span>
                                                </div>
                                                <span class="match-score2 match-<?php echo $onmatch->TScore>$onmatch->CTScore?'winned':'lost'; ?>">
                                                <?php
                                                    echo $onmatch->TScore;
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
                        <?php }
                        }
                        ?>
                    </div>
                </div>
                <div class="indexRow row" id="index-matches">
                    <div class="index-title">
                        <h1>
                            Last
                        <?php
                            echo this::$_perPage." Match".(this::$_perPage==1?'':'es');
                        ?>
                        </h1>
                        <a href="<?php echo this::$_PAGE_URL; ?>matches">View All Matches</a>
                    </div>
                    <div class="indexRow-inner row">
                    <?php
                        while($data = $q->fetch(PDO::FETCH_OBJ))
                        {
                    ?>
                            <a class="col-xl-12 ix-matches succes" href="<?php echo this::$_PAGE_URL; ?>match/<?php echo $data->MatchID; ?>">
                                <div class="ix-match-header">
                                    <div class="match-status-type status-type-finished">
                                        <span></span> Finished
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
                                            <span class="match-score2 match-<?php echo $data->CTScore>$data->TScore?'winned':'lost'; ?>">
                                            <?php
                                                echo $data->CTScore;
                                            ?>
                                            </span>
                                            <div class="ix-match-sinfo">
                                                <span class="match-status status-ended">Winners:
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
                                            <span class="match-score2 match-<?php echo $data->TScore>$data->CTScore?'winned':'lost'; ?>">
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
                    <?php
                        }
                    ?>
                    </div>
                </div>
                <div class="indexRow row">
                    <div class="index-title">
                        <h1>
                            Last
                        <?php
                            echo this::$_perPage." Announce".(this::$_perPage==1?'':'s');
                        ?>
                        </h1>
                    </div>
                    <div class="indexRow-inner row">
                    <?php
                        if(!$find_ann)
                        {
                            echo 'No announces for now..';
                        }
                        else
                        {
                    ?>
                            <div class="table-overflow">
                                <table id="table-board">
                                    <thead>
                                        <tr>
                                            <th class="score-tab-text">ID</th>
                                            <th class="score-tab-text">Title</th>
                                            <th class="score-tab-text">Created on</th>
                                            <th class="score-tab-text">By</th>
                                            <th class="score-tab-text">Last edit on</th>
                                            <th class="score-tab-text">Last edit by</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        while($get_ann_data = $ann->fetch(PDO::FETCH_OBJ))
                                        {
                                    ?>
                                            <tr>
                                                <td class="score-tab-text">
                                                <?php
                                                    echo $get_ann_data->id;
                                                ?>
                                                </td>

                                                <td class="score-tab-text">
                                                    <a href="#ann_<?php echo $get_ann_data->id; ?>" data-toggle="modal" data-toggle2="tooltip" data-placement="top" title="Click to view announce '<?php echo $get_ann_data->title; ?>'">
                                                    <?php
                                                        echo $get_ann_data->title;
                                                    ?>
                                                    </a>
                                                </td>

                                                <td class="score-tab-text">
                                                <?php
                                                    echo $get_ann_data->date;
                                                ?>
                                                </td>

                                                <td class="score-player-name">
                                                    <a href="<?php echo user::MakeProfileUrl(this::getSpec('admins', 'id', 'name', $get_ann_data->by)); ?>" target="_blank">
                                                        <?php 
                                                            echo user::GetSteamAvatar($get_ann_data->by);
                                                        ?>
                                                        <span>
                                                        <?php
                                                            echo $get_ann_data->by;
                                                        ?>
                                                        </span>
                                                    </a>
                                                </td>

                                                <td class="score-tab-text">
                                                <?php
                                                    echo $get_ann_data->LastEdit_Date;
                                                ?>
                                                </td>

                                                <td class="score-player-name">
                                                    <a href="<?php echo user::MakeProfileUrl(this::getSpec('admins', 'id', 'name', $get_ann_data->LastEdit_By_Name)); ?>" target="_blank">
                                                        <?php 
                                                            echo user::GetSteamAvatar($get_ann_data->LastEdit_By_Name);
                                                        ?>
                                                        <span>
                                                        <?php
                                                            echo $get_ann_data->LastEdit_By_Name;
                                                        ?>
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>

                                            <div id="ann_<?php echo $get_ann_data->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ann_<?php echo $get_ann_data->id; ?>Label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="ann_<?php echo $get_ann_data->id; ?>Label">
                                                                Announce:
                                                                <?php
                                                                    echo $get_ann_data->title;
                                                                ?>
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">X</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="tab-pane active" id="editann" role="tabpanel">
                                                            <?php
                                                                echo $get_ann_data->text;
                                                            ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                        <?php
                                        }
                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="indexRow row" id="index-matches">
                    <div class="index-title"><h1>Top</h1></div>
                    <div class="indexRow-inner row">
                    <?php
                        echo this::getSpec('panel_settings', 'ServersOfTheWeek', 'ID', 1);
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>