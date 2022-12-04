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
?>

<div class="mainArea-content">
	<div class="container">
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Players list</h1>
				<form id="search_form">
					<div id="search-content">
						<input type="text" autocomplete="off" name="searchuser" id="searchuser" placeholder="Search with Nick/SteamID" required>
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
												<th class="score-tab-text">#</th>
												<th>Nick</th>
												<th class="score-tab-text top-player-color">Points</th>
												<th class="score-tab-text">Matches</th>
												<th class="score-tab-text">Win</th>
												<th class="score-tab-text">Lose</th>
												<th class="score-tab-text">Kills</th>
												<th class="score-tab-text">Deaths</th>
												<th class="score-tab-text">HS / HS%</th>
												<th class="score-tab-text">% WIN</th>
												<th class="score-tab-text">% LOSS</th>
											</tr>
										</thead>
										<tbody id="search_returned_data">
										<?php
											$q = connect::$g_con->prepare('SELECT * FROM `points_sys` ORDER BY `ID` ASC'.this::limit());
											$q->execute();
											while($row = $q->fetch(PDO::FETCH_OBJ))
											{
												$q2 = connect::$g_con->prepare('SELECT * FROM `mix_player_stats` WHERE `ID` = ?');
												$q2->execute(array($row->ID));
												$row2 = $q2->fetch(PDO::FETCH_OBJ);
												//while($row2 = $q2->fetch(PDO::FETCH_OBJ)) {
										?>
												<tr data-redirect="<?php echo user::MakeProfileUrl($row->ID); ?>" id="player-target">
													<td class="score-tab-text">
													<?php
														echo $row->ID;
													?>
													</td>
													<td class="score-player-name">
	                                                    <?php 
	                                                        echo user::GetSteamAvatar($row->ID);
	                                                    ?>
														<span>
														<?php
															echo $row->Name;
														?>
														</span>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row->Points;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row->Matches;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row2->Wins;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row2->Lose;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row2->Kills;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row2->Deaths;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row2->HS;
													?>
													/
													<?php
														echo ($row2->HS*100)/100;
													?>
													%
													</td>
													<td class="score-tab-text">
														<span class="win-text">
														<?php
															echo ($row2->Wins*100)/100;
														?>
														%
														</span>
													</td>
													<td class="score-tab-text">
														<span class="loss-text">
														<?php
															echo ($row2->Lose*100)/100;
														?>
														%
														</span>
													</td>
												</tr>
										<?php
												//}
												}
										?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div> <!-- pagination MOVED 15.07.22 -->
					<?php
					echo this::create(connect::rows('points_sys'));
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#search_form').submit(function (e)
	{
		e.preventDefault();

		var loading_effect=document.getElementById('paginations');
		if(typeof(loading_effect) != 'undefined' && loading_effect != null)//e cam da csf..
		{
			$(loading_effect).addClass('loader');
		}

	    var searchfor=document.getElementById('searchuser').value;

	    $.ajax({
	        type: "post",//method
	        url: "<?php echo this::$_PAGE_URL; ?>search_ajax_callback.php",
	        data:
	        {
	           'searchuser': searchfor
	        },
	        success: function (html)
	        {
				if(typeof(loading_effect) != 'undefined' && loading_effect != null)
				{
					$(loading_effect).removeClass('loader');
				}
				$('#search_returned_data').html(html);
	        }
	    });

		var created_pagination=document.getElementById('pag_created');
		if(typeof(created_pagination) != 'undefined' && created_pagination != null)
		{
			$(created_pagination).html("<button type='button' class='btn btn-outline-success waves-effect waves-dark float-right' data-direct='<?php echo this::$_PAGE_URL; ?>players' id='target'>back</button>");
		}

	    return false;
	});
</script>