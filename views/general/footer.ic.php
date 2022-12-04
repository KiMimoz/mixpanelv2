				<footer id="footer">
					<div class="container">
						<div class="footer-inner">
							<div class="footer-author">
								<div>
									<span></span><a href="develab.uk" target="_blank">DEVELAB</a> edits | All rights reserved © <a href="https://develab.uk/">DEVELAB</a><span></span>
								</div>
							</div>
							<span data-toggle="modal" data-target="#exampleModal" data-toggle2="tooltip" data-placement="top" title="Click to view info">
								v
								<?php
								echo this::GetPanelStatus(1)->current_version[0]->mxp;
								?>
								-
								<b>
								<?php
									echo this::GetPanelStatus(1)->version_stage[0]->mxp;
								?>
								</b>
							</span>
						</div>
					</div>
				</footer>

			</main>

		</div>

		<!-- Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Current panel version informations</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">X</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        Last official edits made on:
		        <b>
		        <?php
		        	echo this::GetPanelStatus(1)->last_official_edit[0]->mxp;
			        if(this::GetPanelStatus(1)->last_official_edit_critic[0]->mxp==1)
			        {
			        	echo
			        	"
			        		(critical edits)
			        	";
			        }
		        ?>
		        </b>
		        <br>
		        Current version released on:
		        <b>
		        <?php
		        	echo this::GetPanelStatus(1)->cv_release[0]->mxp;
		        ?>
		        </b>
		        <br>
		        Next version update will be:
		        <b>
		        <?php
		        	echo this::GetPanelStatus(1)->next_update[0]->mxp;
			        if(this::GetPanelStatus(1)->critic_update[0]->mxp==1)
			        {
			        	echo
			        	"
			        		(critical update)
			        	";
			        }
		        ?>
		        </b>
		        <br>
		        Next update planned for:
		        <b>
		        <?php
		        	echo this::GetPanelStatus(1)->nu_release[0]->mxp;
		        ?>
		        </b>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>

<?php
if(user::isLogged())
{
	if(isset($_POST['amviatasociala'])||$_SESSION['auto_unlog']==1)
	{
	    $update_some_user_data = connect::$g_con->prepare('UPDATE `admins` SET `LastIP` = ?, `online` = ? WHERE `id` = ?');
	    $update_some_user_data->execute(array(user::GetIp(), 0, user::get()));

	    $update_some_user_data2 = connect::$g_con->prepare('UPDATE `points_sys` SET `LastOnline` = ? WHERE `id` = ?');
	    $update_some_user_data2->execute(array(this::getDaT(), user::get()));

	    this::show_toastr('success', 'You logged out successfully! Wait 3 seconds for redirect', 'Success', 1);

	    unset($_SESSION['auto_unlog']);

	    unset($_SESSION['time']);

	    unset($_SESSION['user']);

	    session_destroy();//hmm

	    user::redirect_to("", 3);
	}

	if(isset($_SESSION['time']))
	{
		if (isset($_SESSION['time']) && (time() - $_SESSION['time']) >= user::$AUTO_UNLOG_AFTER && $_SESSION['auto_unlog']!=1)//>= / +-
		{
			this::show_toastr('warning', 'You will be automatically unlogged from panel in 10 seconds due security', 'Warning', 1);

			$_SESSION['auto_unlog']=1;

			user::redirect_to("", 10);
		}
		/*else
		{
		  $_SESSION['time'] = time();//hmm
		}*/
	}
	else
	{
		$_SESSION['time'] = time();

		$_SESSION['auto_unlog']=0;
	}
}

if(!user::isLogged())
{
	$check_ban = connect::$g_con->prepare("SELECT * FROM `bans` WHERE `victim_ip` = ? AND `unbantime` != 'NEVER' AND `banlength` > 0 ORDER BY id ASC");
	$check_ban->execute(array(user::GetIp()));
}
else
{
	$check_ban = connect::$g_con->prepare("SELECT * FROM `bans` WHERE (`victim_id` = ? OR `victim_steamid` = ? OR `victim_ip` = ? OR `victim_ip` = ?) AND `unbantime` != 'NEVER' AND `banlength` > 0 ORDER BY id ASC");
	$check_ban->execute(array(user::get(),user::getUserData()->auth,user::getUserData()->IP,user::getUserData()->LastIP));
}
if($check_ban->rowCount())
{
	this::show_toastr('error', 'You are banned', 'Error!', 0, 1, 'banned');
}
?>

		<script src="https://unpkg.com/@popperjs/core@2"></script>
		<script src="https://unpkg.com/tippy.js@6"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.0-alpha3/js/bootstrap.bundle.min.js" integrity="sha512-4mKgMjjiLqHCkqTX9YRfreevkJHBzF1d5GT7HKaFr/dLQqTKNvFl3m6fwTedsbc039MVQPAFIA1s8hP64H3LFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="<?php echo this::$_PAGE_URL; ?>resources/js/api.min.js"></script>
		<script src="<?php echo this::$_PAGE_URL; ?>resources/js/custom.min.js"></script>
		<!--<script src="<?php echo this::$_PAGE_URL; ?>resources/assets/js/adminlte.min.js"></script>
		<script src="<?php echo this::$_PAGE_URL; ?>resources/js/jquery.easing.min.js"></script>
		<script src="<?php echo this::$_PAGE_URL; ?>resources/js/sb-admin-2.min.js"></script>-->
		
		<script type="text/javascript">
			/*jQuery(function($)
			{
				var val = [ "owner" ]
				$('nav li a').each(function()
				{
					if (this.href === window.location.href || (window.location.href.includes(this.href) && location.href.includes('pg') && this.href.includes(val)))
					{
						$(this).addClass('active');
					}
				});
			});*/

			$(function ()
			{
  				$('[data-toggle="tooltip"], [data-toggle2="tooltip"]').tooltip();
			});
		</script>

		<!--<script type="text/javascript">
		    document.addEventListener('contextmenu', event=> event.preventDefault());

		    document.onkeydown = function(e)
		    {
		        if(event.keyCode == 123)
		        {
		            return false;
		        }
		        if(e.ctrlKey && e.shiftKey && ( e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'J'.charCodeAt(0) ))
		        {
		            return false;
		        }
		        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0))
		        {
		            return false;
		        }
		    }
		</script>-->

	</body>
</html>

<?php
ob_flush();
?>