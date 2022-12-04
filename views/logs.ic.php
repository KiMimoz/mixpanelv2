<?php
if(!user::isLogged())
{
    return user::redirect_to("");
}
else
{
    if(user::getUserData()->Admin < 1)
    {
        return user::redirect_to("");
    }
}
?>

<div class="mainArea-content">
    <div class="container">
        <div class="indexRow row" id="index-ongoing-matches">
            <div class="index-title">
                <h1>Panel logs</h1>
                <form id="search_form5">
                    <div id="search-content">
                        <input type="text" autocomplete="off" name="searchuser5" id="searchuser5" placeholder="Search with Nick/Text/Date/Id" required>
                        <div class="search"></div>
                    </div>
                </form>
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
                                    <table id="table-board">
                                        <thead>
                                            <tr>
                                                <th class="score-tab-text">ID</th>
                                                <th class="score-tab-text">Text</th>
                                                <th class="score-tab-text">Tracked by</th>
                                                <th class="score-tab-text">Tracked ip</th>
                                                <th class="score-tab-text">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="search_returned_data5">
                                        <?php
                                            $pl = connect::$g_con->prepare("SELECT * FROM `panel_logs` ORDER BY logID DESC".this::limit());
                                            $pl->execute();
                                            while($panel_logs = $pl->fetch(PDO::FETCH_OBJ))
                                            {
                                        ?>
                                                <tr>
                                                    <td class="score-tab-text">
                                                    <?php
                                                        echo $panel_logs->logID;
                                                    ?>
                                                    </td>

                                                    <td class="score-tab-text">
                                                    <?php
                                                        echo $panel_logs->logText;
                                                    ?>
                                                    </td>

                                                    <td class="score-tab-text">
                                                        <a href="<?php echo user::MakeProfileUrl(this::getSpec('admins', 'id', 'id', $panel_logs->logById)); ?>" target="_blank">
                                                        <?php
                                                            echo this::getSpec('admins', 'name', 'id', $panel_logs->logById);
                                                        ?>
                                                        </a>
                                                    </td>

                                                    <td class="score-tab-text">
                                                    <?php 
                                                        echo $panel_logs->logIP;
                                                    ?>
                                                    </td>

                                                    <td class="score-tab-text">
                                                    <?php 
                                                        echo $panel_logs->logDate;
                                                    ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        echo this::create(connect::rows('panel_logs'));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#search_form5').submit(function (e)
    {
        e.preventDefault();

        var loading_effect=document.getElementById('paginations');
        if(typeof(loading_effect) != 'undefined' && loading_effect != null)
        {
            $(loading_effect).addClass('loader');
        }

        var searchfor5=document.getElementById('searchuser5').value;

        $.ajax({
            type: "post",
            url: "<?php echo this::$_PAGE_URL; ?>search_ajax_callback.php",
            data:
            {
               'searchuser5': searchfor5
            },
            success: function (html)
            {
                if(typeof(loading_effect) != 'undefined' && loading_effect != null)
                {
                    $(loading_effect).removeClass('loader');//da plm
                }
                $('#search_returned_data5').html(html);
            }
        });

        var created_pagination=document.getElementById('pag_created');
        if(typeof(created_pagination) != 'undefined' && created_pagination != null)
        {
            $(created_pagination).html("<button type='button' class='btn btn-outline-success waves-effect waves-dark float-right' data-direct='<?php echo this::$_PAGE_URL; ?>logs' id='target'>back</button>");
        }

        return false;
    });
</script>