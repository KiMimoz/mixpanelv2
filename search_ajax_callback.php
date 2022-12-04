<?php

session_start();//levi noob

$servername 	= "db.DEVELAB";
$username	 	= "";
$dbname 		= "";
$password 		= "";
try
{
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
}
catch(PDOException $e)
{
  echo "Connection failed: " . $e->getMessage();
}


$_SITE_NAME = 'DEVELAB - Matchmaking System';
$_PAGE_URL = 'https://develab';
$STEAM_API_KEY = '0D528CA03A875C555CD1A5EBFD3175A7';
$_SERVER_NAME = 'develab';



function is_bot($user_agent)
{
    $botRegexPattern = "(googlebot\/|Googlebot\-Mobile|Googlebot\-Image|Google favicon|Mediapartners\-Google|bingbot|slurp|java|wget|curl|Commons\-HttpClient|Python\-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST\-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub\.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum\.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips\-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail\.RU_Bot|discobot|heritrix|findthatfile|europarchive\.org|NerdByNature\.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb\-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web\-archive\-net\.com\.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks\-robot|it2media\-domain\-crawler|ip\-web\-crawler\.com|siteexplorer\.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki\-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e\.net|GrapeshotCrawler|urlappendbot|brainobot|fr\-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf\.fr_bot|A6\-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive\.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j\-asr|Domain Re\-Animator Bot|AddThis|YisouSpider|BLEXBot|YandexBot|SurdotlyBot|AwarioRssBot|FeedlyBot|Barkrowler|Gluten Free Crawler|Cliqzbot)";
    return preg_match("/{$botRegexPattern}/", $user_agent);
}
function getSteamProfileData($steam,$type=1)
{
	if ( is_bot($_SERVER['HTTP_USER_AGENT']) )
	{
		return;
	}

	$data="";

	switch($type)
	{
		case 1:
		{
			$STEAMXML_URL_MASK = 'https://steamcommunity.com/profiles/%s/?xml=1';
			$url = sprintf($STEAMXML_URL_MASK, $steam);
			if (!$data = simplexml_load_file($url))
			{
				throw new UnexpectedValueException(sprintf('Unable to load XML from "%s"', $url));
			}
			break;
		}
		case 2:
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$STEAM_API_KEY.'&steamids='.$steam.'');
			$result = curl_exec($ch);
			curl_close($ch);
			$obj = json_decode($result);
			foreach($obj->response->players as $player)
			{
				$data=$player;
			}
			break;
		}
		case 3:
		{
			$ch =  curl_init('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$STEAM_API_KEY.'&steamids='.$steam.'');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
			$parsed = curl_exec($ch);
			curl_close($ch);
			$obj = json_decode($parsed);
			foreach($obj->response->players as $player)
			{
				$data=$player;
			}
			break;
		}
		case 4:
		{
			$data2 = file_get_contents('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$STEAM_API_KEY.'&steamids='.$steam.'');
			$users = json_decode($data2);
			foreach($users->response->players as $player)
			{
				$data=$player;
			}
			break;
		}
		case 5:
		{
			$curl = curl_init();

			curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$STEAM_API_KEY.'&steamids='.$steam.'',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
					"accept: application/json",
					"content-type: application/json"
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$obj = json_decode($response);
			foreach($obj->response->players as $player)
			{
				$data=$player;
			}
		}
		break;
	}
	return $data;
}
function IsValidSteamStr($steamid)
{
	if ( is_bot($_SERVER['HTTP_USER_AGENT']) )
	{
		return;
	}
	if(preg_match('/STEAM_0:[0-1]:[0-9]{1,10}/',$steamid)&&!strpos($steamid,'NON-STEAM'))
	{
		return true;//sau fix cu 0-1 + ^ început ți i final xd SAU 1,3
	}
	return false;
}
function SteamStr2SteamId($steamid)
{
	if(!IsValidSteamStr($steamid)||is_bot($_SERVER['HTTP_USER_AGENT']))
	{
		return;
	}
	$parts = explode(':', str_replace('STEAM_', '' ,$steamid));
	return bcadd(bcadd('76561197960265728', $parts['1']), bcmul($parts['2'], '2'));
}

function getSpec($table, $data, $name, $value)
{
	global $conn;
	$q = $conn->prepare('SELECT `'.$data.'` FROM `'.$table.'` WHERE `'.$name.'` = ?');
	$q->execute(array($value));
	$r_data = $q->fetch();
	return $r_data[$data] ?? '-1';
}
function MakeProfileUrl($id)
{
	global $_PAGE_URL;
	$formated_id='';

	if(getSpec('admins', 'steamid64', 'id', $id)!='-1')
	{
		$formated_id=getSpec('admins', 'steamid64', 'id', $id);
	}
	/*else if(this::getSpec('admins', 'id', 'id', $id)!='-1')
	{
		$formated_id=this::getSpec('admins', 'id', 'id', $id);
	}*/
	else if(getSpec('admins', 'name', 'id', $id)!='-1')
	{
		$formated_id=getSpec('admins', 'name', 'id', $id);
	}
	else if(getSpec('admins', 'email', 'id', $id)!='-1')
	{
		$formated_id=getSpec('admins', 'email', 'id', $id);
	}
	else if(getSpec('admins', 'auth', 'id', $id)!='-1')
	{
		$formated_id=getSpec('admins', 'auth', 'id', $id);
	}

	if(empty($formated_id)||$formated_id=='-1')
	{
		$link=$_PAGE_URL."profile/".$id;
	}
	else
	{
		$link=$_PAGE_URL."profile/".$formated_id;
	}

	return $_PAGE_URL."profile/".(empty($formated_id)||$formated_id=='-1'?$id:$formated_id);
}

function GetSteamAvatar($detect,$type=1)
{
	$steam_auth=is_numeric($detect)?getSpec("admins", "auth", "id", $detect):getSpec("admins", "auth", "name", $detect);
	if($type==1)
	{
		$avatar=IsValidSteamStr($steam_auth)?getSteamProfileData(SteamStr2SteamId($steam_auth))->avatarFull:"https://steamuserimages-a.akamaihd.net/ugc/885384897182110030/F095539864AC9E94AE5236E04C8CA7C2725BCEFF/";
		$url='<img alt="" src="%s">';
		return sprintf($url,$avatar);
	}
	else
	{
		$avatar=IsValidSteamStr($steam_auth)?getSteamProfileData(SteamStr2SteamId($steam_auth))->avatarFull:"https://steamuserimages-a.akamaihd.net/ugc/885384897182110030/F095539864AC9E94AE5236E04C8CA7C2725BCEFF/";
		return $avatar;
	}
}



/*$get_user_data = $conn->prepare('SELECT * FROM `admins` WHERE `id` = ?');
$get_user_data->execute($_POST['USER_ID']);
$user = $get_user_data->fetch(PDO::FETCH_OBJ);*/

$user_id=isset($_POST['USER_ID']) ? $_POST['USER_ID'] : -1;
$user_access=isset($_POST['USER_ADMIN_ACCESS']) ? $_POST['USER_ADMIN_ACCESS'] : -1;
$user_have_access=isset($_POST['USER_HAVE_ACCESS']) ? $_POST['USER_HAVE_ACCESS'] : -1;



$searchuser5 = isset($_POST['searchuser5']) ? $_POST['searchuser5'] : '';
if(!empty($searchuser5))
{
    $pl = $conn->prepare("SELECT * FROM `panel_logs` WHERE `logID` LIKE ? OR `logText` LIKE ? OR `logById` LIKE ? OR `logDate` LIKE ?");
    $pl->execute(array('%'.$searchuser5.'%', '%'.$searchuser5.'%', '%'.$searchuser5.'%', '%'.$searchuser5.'%'));
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
                <a href="<?php echo MakeProfileUrl(getSpec('admins', 'id', 'id', $panel_logs->logById)); ?>" target="_blank">
                <?php
                    echo getSpec('admins', 'name', 'id', $panel_logs->logById);
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
}


$searchuser4 = isset($_POST['searchuser4']) ? $_POST['searchuser4'] : '';
if(!empty($searchuser4))
{
	$adm = $conn->prepare('SELECT * FROM `admins` WHERE ( `auth` LIKE ? OR `name` LIKE ? OR `email` LIKE ? OR `id` LIKE ? OR `online` LIKE ? ) AND `Admin` > 0 ORDER BY `Admin` DESC');
	$adm->execute(array('%'.$searchuser4.'%', '%'.$searchuser4.'%', '%'.$searchuser4.'%', '%'.$searchuser4.'%', '%'.$searchuser4.'%'));
	while($row4 = $adm->fetch(PDO::FETCH_OBJ))
	{
	?>
		<tr data-redirect="<?php echo MakeProfileUrl($row4->id); ?>" id="player-target">
			<td class="score-tab-text">
			<?php
				echo $row4->id;
			?>
			</td>
			<td class="score-tab-text">
				<span class="badge" style="background-color: <?php echo $row4->online == 0?'red':'green'; ?>;">
					<strong>
					<?php
						echo "O".($row4->online == 0?'ff':'n')."line";
					?>
					</strong>
				</span>
			</td>
			<td class="score-player-name">
                <?php 
                    echo user::GetSteamAvatar($row4->id);
                ?>
				<span>
				<?php
					echo $row4->name;
				?>
				</span>
			</td>
			<td class="score-tab-text">
			<?php
				$groups = $conn->prepare("SELECT * FROM `panel_groups` WHERE `groupAdmin` = ? ORDER BY `groupAdmin` ASC");
				$groups->execute(array($row4->Admin));
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
				echo $row4->warn;
			?>
			</td>
			<td class="score-tab-text">
			<?php
				echo $row4->LastPanelLogin;
			?>
			</td>
		</tr>
	<?php
	}

	exit();
}


$searchuser3 = isset($_POST['searchuser3']) ? $_POST['searchuser3'] : '';
if(!empty($searchuser3))
{
	$adm = $conn->prepare("SELECT * FROM `admins` WHERE `auth` LIKE ? OR `name` LIKE ? ORDER BY `Admin` DESC");
	$adm->execute(array('%'.$searchuser3.'%', '%'.$searchuser3.'%'));
	if(!$adm->rowCount())
	{
	?>
		I DIDN'T FIND SOMETHING TO MATCH WITH YOUR CRITERIA
	<?php
		exit();
	}
	while($row = $adm->fetch(PDO::FETCH_OBJ))
	{
	?>
		<tr>
			<td>
			<?php
				echo $row->id;
			?>
			</td>
			<td>
				<i class="fa fa-circle text-<?php echo $row->online == 0?'danger':'success'; ?>" data-toggle="tooltip" data-placement="top" title="o<?php echo $row->online == 0?'ff':'n'; ?>line"></i>
				<a href="<?php echo MakeProfileUrl($row->id); ?>" target="_blank">
				<?php
					echo $row->name;
				?>
				</a>
			</td>
			<td>
			<?php
				$groups = $conn->prepare("SELECT * FROM `panel_groups` WHERE `groupAdmin` = ? ORDER BY `groupAdmin` ASC");
				$groups->execute(array($row->Admin));
				$function = $groups->fetch(PDO::FETCH_OBJ);
				echo '
					<span class="badge" style="background-color: '.$function->groupColor.';color: '.$function->funcColor.';">
						<font style="font-family: '.$function->funcFontFamily.';">
							<i class="'.$function->funcIcon.'"></i> <strong>'.$function->groupName.'</strong>
						</font>
					</span>
					';
			?>
			</td>
			<td>
			<?php
				echo $row->auth;
			?>
			</td>
			<td>
			<?php
				echo $row->warn;
			?>
			</td>
			<td>
			<?php
				echo $row->FirstPanelRegister;
			?>
			</td>
			<td>
			<?php
				echo $row->LastPanelLogin;
			?>
			</td>
			<td>
			<?php
				echo $row->IP;
			?>
			</td>
			<td>
			<?php
				echo $row->LastIP;
			?>
			</td>
		</tr>
<?php
	}

	exit();
}


$searchuser2 = isset($_POST['searchuser2']) ? $_POST['searchuser2'] : '';
if(!empty($searchuser2))
{
	$q2 = $conn->prepare('SELECT * FROM `bans` WHERE `victim_name` LIKE ? OR `victim_steamid` LIKE ? OR `victim_ip` LIKE ?');
	$q2->execute(array('%'.$searchuser2.'%', '%'.$searchuser2.'%', '%'.$searchuser2.'%'));
	if(!$q2->rowCount())
	{
	?>
		I DIDN'T FIND SOMETHING TO MATCH WITH YOUR CRITERIA
	<?php
		exit();
	}
	while($row2 = $q2->fetch(PDO::FETCH_OBJ))
	{
	?>
		<tr>
			<td class="score-tab-text">
			<?php
				echo $row2->id;
			?>
			</td>
			<td class="score-player-name" data-redirect="<?php echo MakeProfileUrl($row2->id); ?>" id="player-target">
                <?php 
                    echo user::GetSteamAvatar($row2->victim_id);
                ?>
				<span>
				<?php
					echo $row2->victim_name;
				?>
				</span>
			</td>
			<td class="score-tab-text">
			<?php
				echo $row2->victim_steamid;
			?>
			</td>
			<td class="score-tab-text">
			<?php
				echo $row2->unbantime;
			?>
			</td>
			<td class="score-tab-text">
			<?php
				echo $row2->reason;
			?>
			</td>
			<td class="score-player-name" data-redirect="<?php echo MakeProfileUrl($row2->admin_id); ?>" id="player-target">
                <?php 
                    echo user::GetSteamAvatar($row2->admin_id);
                ?>
				<span>
				<?php
					echo $row2->admin_name;
				?>
				</span>
			</td>
			<td class="score-tab-text">
			<?php
				echo $row2->admin_steamid;
			?>
			</td>
			<td class="score-tab-text">
			<?php
				echo $row2->date;
			?>
			</td>
			<?php
			if($user_have_access==1)
			{
			?>
				<td class="score-tab-text">
					<form method="post">
						<button type="button" class="btn btn-success btn-sm" value="<?php echo $row2->id; ?>" data-toggle="modal" data-target="#editban<?php echo $row2->id; ?>" data-toggle2="tooltip" data-placement="top" title="Edit ban">
							<i class="fa fa-edit"></i>
						</button>

						<button type="button" class="btn btn-danger btn-sm" value="<?php echo $row2->id; ?>" data-toggle="modal" data-target="#deleteban" data-toggle2="tooltip" data-placement="top" title="Delete ban">
							<i class="fa-solid fa-file-circle-xmark"></i>
						</button>
					</form>
				</td>
			<?php
			}
			?>
		</tr>

		<script type="text/javascript">
			$(function ()
			{
  				$('[data-toggle="tooltip"], [data-toggle2="tooltip"]').tooltip();
			});
		</script>

	<?php
	}

	exit();
}


$searchuser = isset($_POST['searchuser']) ? $_POST['searchuser'] : '';
if(!empty($searchuser))
{
	$q = $conn->prepare('SELECT * FROM `points_sys` WHERE `SteamID` LIKE ? OR `Name` LIKE ?');
	$q->execute(array('%'.$searchuser.'%', '%'.$searchuser.'%'));
	while($row = $q->fetch(PDO::FETCH_OBJ))
	{
		$q3 = $conn->prepare('SELECT * FROM `mix_player_stats` WHERE `ID` = ?');
		$q3->execute(array($row->ID));
		$row3 = $q3->fetch(PDO::FETCH_OBJ);
		//while($row3 = $q3->fetch(PDO::FETCH_OBJ)) {
	?>
			<tr data-redirect="<?php echo MakeProfileUrl($row->ID); ?>" id="player-target">
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
					echo $row3->Wins;
				?>
				</td>
				<td class="score-tab-text">
				<?php
					echo $row3->Lose;
				?>
				</td>
				<td class="score-tab-text">
				<?php
					echo $row3->Kills;
				?>
				</td>
				<td class="score-tab-text">
				<?php
					echo $row3->Deaths;
				?>
				</td>
				<td class="score-tab-text">
				<?php
					echo $row3->HS;
				?>
				/
				<?php
					echo ($row3->HS*100)/100;
				?>
				%
				</td>
				<td class="score-tab-text">
					<span class="win-text">
					<?php
						echo ($row3->Wins*100)/100;
					?>
					%
					</span>
				</td>
				<td class="score-tab-text">
					<span class="loss-text">
					<?php
						echo ($row3->Lose*100)/100;
					?>
					%
					</span>
				</td>
			</tr>
	<?php
		//}
	}

	exit();
}

exit();//=))
?>