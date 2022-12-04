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
				<h1>Staff List</h1>
				<form id="search_form4">
					<div id="search-content">
						<input type="text" autocomplete="off" name="searchuser4" id="searchuser4" placeholder="Search with Nick/Steamid/Email/Online=1/Id" required>
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
												<th class="score-tab-text">Status</th>
												<th class="score-tab-text">Nick</th>
												<th class="score-tab-text">Group</th>
												<th class="score-tab-text">Warns</th>
												<th class="score-tab-text">Last login</th>
											</tr>
										</thead>
										<tbody id="search_returned_data4">
										<?php
											$adm = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `Admin` > 0 ORDER BY `Admin` DESC".this::limit());
											$adm->execute();
											while($row = $adm->fetch(PDO::FETCH_OBJ))
											{
										?>
												<tr data-redirect="<?php echo user::MakeProfileUrl($row->id); ?>" id="player-target">
													<td class="score-tab-text">
													<?php
														echo $row->id;
													?>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $row->online == 0?'red':'green'; ?>;">
															<strong>
															<?php
																echo "O".($row->online == 0?'ff':'n')."line";
															?>
															</strong>
														</span>
													</td>
													<td class="score-player-name">
														<img alt="" src="<?php echo this::IsValidSteamStr($row->auth)?user::getSteamProfileData(this::SteamStr2SteamId($row->auth))->avatarFull:'https://steamuserimages-a.akamaihd.net/ugc/885384897182110030/F095539864AC9E94AE5236E04C8CA7C2725BCEFF/'; ?>">
														<span>
														<?php
															echo $row->name;
														?>
														</span>
													</td>
													<td class="score-tab-text">
													<?php
														$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` WHERE `groupAdmin` = ? ORDER BY `groupAdmin` ASC");
														$groups->execute(array($row->Admin));
														$function = $groups->fetch(PDO::FETCH_OBJ);
														echo
														'
															<span class="badge" style="background-color: '.$function->groupColor.';color: '.$function->funcColor.';font-family: '.$function->funcFontFamily.'">
																<strong>
																	<i class="'.$function->funcIcon.'"></i> '.$function->groupName.'
																</strong>
															</span>
														';
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row->warn;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row->LastPanelLogin;
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
						echo this::create(connect::rows2('admins', 'WHERE `Admin` > 0 ORDER BY `Admin` DESC'));
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#search_form4').submit(function (e)
	{
		e.preventDefault();

		var loading_effect=document.getElementById('paginations');
		if(typeof(loading_effect) != 'undefined' && loading_effect != null)
		{
			$(loading_effect).addClass('loader');
		}

	    var searchfor4=document.getElementById('searchuser4').value;

	    $.ajax({
	        type: "post",
	        url: "<?php echo this::$_PAGE_URL; ?>search_ajax_callback.php",
	        data:
	        {
	           'searchuser4': searchfor4
	        },
	        success: function (html)
	        {
				if(typeof(loading_effect) != 'undefined' && loading_effect != null)
				{
					$(loading_effect).removeClass('loader');//da plm
				}
				$('#search_returned_data4').html(html);
	        }
	    });

		var created_pagination=document.getElementById('pag_created');
		if(typeof(created_pagination) != 'undefined' && created_pagination != null)
		{
			$(created_pagination).html("<button type='button' class='btn btn-outline-success waves-effect waves-dark float-right' data-direct='<?php echo this::$_PAGE_URL; ?>staff' id='target'>back</button>");
		}

	    return false;
	});
</script>