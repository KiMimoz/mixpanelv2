<?php

$navigateur = $_SERVER["HTTP_USER_AGENT"];
$bannav = Array('HTTrack','httrack','WebCopier','HTTPClient','websitecopier','webcopier');
foreach ($bannav as $banni)
{
	$comparaison = strstr($navigateur, $banni);
	if($comparaison!==false)
	{
		echo '<center>Re valet!<br><br>Idk.';
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		echo '<br>';
		echo $hostname;
		echo '</center>';
		exit;
	}
}

class this
{
	private static $instance;

	public static $pdo;

	public static $htmlpurifier;

	public static $_url = array();

	public static $_CURRENT_PAGE = -1;
	

	public static $_ENABLE_DEBUG = 1;

	public static $_perPage = 10;//hmm

	public static $_ENABLE_RSC = 1;

	public static $_PAGE_URL = 'https://DEVELAB/';

	public static $_SITE_NAME = 'DEVELAB - Matchmaking System';

	public static $_SITE_TITLE = 'DEVELAB - Matchmaking System';

	public static $_SERVER_NAME = "DEVELAB";

	public static $_FORUM_URL = "https://develab/forum";

	public static $_FORUM_DIRECT_LOCATION = "https://DEVELAB";

	public static $_CONTACT_EMAIL = "forum@DEVELAB";

	public function __construct()
	{
		connect::init();
		self::_getUrl();
	}

	public static function getContent()
	{
		require_once 'system/HTMLPurifier.auto.php';

		switch(self::$_url[0])
		{
			case 'login':
			{
				self::$_CURRENT_PAGE=1;
				break;
			}
			case 'register':
			{
				self::$_CURRENT_PAGE=2;
				break;
			}
			case 'panel':
			{
				self::$_CURRENT_PAGE=4;
				break;
			}
			case 'staff':
			{
				self::$_CURRENT_PAGE=5;
				break;
			}
			case 'players':
			{
				self::$_CURRENT_PAGE=7;
				break;
			}
			case 'banlist':
			{
				self::$_CURRENT_PAGE=8;
				break;
			}
			case 'server':
			{
				self::$_CURRENT_PAGE=9;
				break;
			}
		}

		if(strpos(self::$_url[0],'owner')!==FALSE)//hmmm
		{
			self::$_CURRENT_PAGE=3;
		}
		else if(strpos(self::$_url[0],'match')!==FALSE)
		{
			self::$_CURRENT_PAGE=6;
		}
		else if(strpos(self::$_url[0],'profile')!==FALSE)
		{
			self::$_CURRENT_PAGE=7;
		}

		include_once 'views/general/header.ic.php';

		if(self::$_url[0] === 'action')
		{
			include 'actions/' . self::$_url[1] . '.a.php';

			return;
		}

		if(isset(self::$_url[0]) && !strlen(self::$_url[0]))
		{
			include_once 'views/index.ic.php';
		}
		else if(file_exists('views/' . self::$_url[0] . '.ic.php'))
		{
			include 'views/' . self::$_url[0] . '.ic.php';
		}
		else
		{
			include_once 'views/index.ic.php';
		}

		include_once 'views/general/footer.ic.php';
	}

	public static function show_toastr($toastr_type='info',$toastr_text,$toastr_title='Info',$force_show=0,$redirect=0,$redirect_to='',$redirect_time=1)
	{
    	if($force_show!=1)
    	{
    		$_SESSION['msg'] = "<script>toastr.".$toastr_type."('".$toastr_text."', '".$toastr_title."');</script>";
    	}
    	else
    	{
    		echo "<script>toastr.".$toastr_type."('".$toastr_text."', '".$toastr_title."');</script>";
    	}

    	if($redirect==1)
    	{
    		user::redirect_to($redirect_to, $redirect_time);
    	}
	}

	public static function format_number($number)//unused
	{
		return number_format($number,0,'.','.');
	}

	public static function isAjax()//unused
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'?true:false;
	}

	public static function format_ts($data,$reverse = false)//unused
	{
		return (!$reverse ? date('H:i:s | d/m/Y',$data) : date('d/m/Y | H:i:s',$data));
	}

	public static function getDaT($time=0)
	{
		return !$time?date('H:i:s | d/m/Y'):date('H:i:s | d/m/Y',$time);
	}

	public static function getElapsedTime($timestamp,$time = true)//unused
	{
		if(!$timestamp)
		{
			return 1;
		}

		$difference = time() - $timestamp;
		if($difference == 0)
		{
			return 'just now';
		}

		$periods = array("second", "minute", "hour", "day", "week",
		"month", "year", "decade");

		$lengths = array("60","60","24","7","4.35","12","10");

		if ($difference > 0)
		{
			$ending = "ago";
		}
		else
		{
			$difference = -$difference;
			$ending = "to go";
		}

		if(!$difference)
		{
			return 'just now';
		}

		for($j = 0; $difference >= $lengths[$j]; $j++)
		{
			$difference /= $lengths[$j];
			$difference = round($difference);
			if($difference != 1)
			{
				$periods[$j].= "s";
			}
			
			$text = "$difference $periods[$j]".(!$time?" $ending":"");
		}

		return $text;
	}

	public static function timeFuture($time_ago)//unused
	{
		$cur_time   	= time();
		$time_elapsed   = $time_ago - $cur_time;
		$days       	= round($time_elapsed / 86400);
		return ($days > -1?'in ':'$days ')."$days day".$days==1?'':'s';
	}

	public static function timeAgo($time_ago, $icon = true)//unused
	{
		$time_ago 		= strtotime($time_ago);
		$cur_time   	= time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    	= $time_elapsed;
		$minutes    	= round($time_elapsed / 60);
		$hours      	= round($time_elapsed / 3600);
		$days       	= round($time_elapsed / 86400);
		$weeks      	= round($time_elapsed / 604800);
		$months     	= round($time_elapsed / 2600640);
		$years      	= round($time_elapsed / 31207680);

		if($seconds <= 60)
		{
			return "right now";
		}
		else if($minutes <= 60)
		{
			return "with $minutes minute".$minutes==1?' ago':'s ago';
		}
		else if($hours <= 24)
		{
			return "with $hours hour".$hours==1?' ago':'s ago';
		}
		else if($days <= 7)
		{
			if($days==1)
			{
				return "yesterday";
			}
			else
			{
				return "with $days days ago";//aiurea ..
			}
		}
		else if($weeks <= 4.3)
		{
			return "with $weeks week".$weeks==1?' ago':'s ago';
		}
		else if($months <=12)
		{
			return "with $months month".$months==1?' ago':'s ago';
		}
		else
		{
			return "with $years year".$years==1?' ago':'s ago';
		}
	}

	public static function getSpec($table, $data, $name, $value)
	{
		$q = connect::$g_con->prepare('SELECT `'.$data.'` FROM `'.$table.'` WHERE `'.$name.'` = ?');
		$q->execute(array($value));
		$r_data = $q->fetch();
		return $r_data[$data] ?? '-1';
	}

	public static function getSpecificData($data,$data2,$id1,$id)//unused
	{
		if(!is_array($data))
		{
			$q = connect::prepare('SELECT `'.$data.'` FROM `'.$data2.'` WHERE `'.$id1.'` = ?');
			$q->execute(array($id));
			$fdata = $q->fetch();
			return $fdata[$data];
		}
		else
		{
			$q = '';

			foreach($data as $d)
			{
				if(end($data) !== $d)
				{
					$q .= '`'.$d.'`,';
				}
				else
				{
					$q .= '`'.$d.'`';
				}
			}

			$q = connect::prepare('SELECT '.$q.' FROM `'.$data2.'` WHERE `'.$id1.'` = ?');
			$q->execute(array($id));
			return $q->fetch(PDO::FETCH_ASSOC);
		}
	}

	public static function getSpecificDataLike($data,$data2,$id1,$id)//unused
	{
		if(!is_array($data))
		{
			$q = connect::prepare('SELECT `'.$data.'` FROM `'.$data2.'` WHERE `'.$id1.'` LIKE ?');
			$q->execute(array('%'.$id.'%'));
			$fdata = $q->fetch();
			return $fdata[$data];
		}
		else
		{
			$q = '';

			foreach($data as $d)
			{
				if(end($data) !== $d)
				{
					$q .= '`'.$d.'`,';
				}
				else
				{
					$q .= '`'.$d.'`';
				}
			}

			$q = connect::prepare('SELECT '.$q.' FROM `'.$data2.'` WHERE `'.$id1.'` = ?');
			$q->execute(array($id));
			return $q->fetch(PDO::FETCH_ASSOC);
		}
	}

	public static function init()
	{
		$url = isset($_GET['page']) ? $_GET['page'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);

        self::$_url = explode('/', $url);

		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	private static function _getUrl()
	{
		$url = isset($_GET['page']) ? $_GET['page'] : null;
        $url = ltrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);

        self::$_url = explode('/', $url);
	}

	public static function limit()
	{
		if(!isset($_GET['pg']))
		{
			$_GET['pg'] = 1;
		}

		return " LIMIT ".(($_GET['pg'] * self::$_perPage) - self::$_perPage).",".self::$_perPage;
	}

	public static function create($rows)
	{
		if(!isset($_GET['pg']))
		{
			$_GET['pg'] = 1;
		}

		$adjacents = "2";
		$prev = $_GET['pg'] - 1;
		$next = $_GET['pg'] + 1;
		$lastpage = ceil($rows/self::$_perPage);
		$lpm1 = $lastpage - 1;

		$pagination = "<br><div class='d-flex flex-row-reverse' id='pag_created'><ul>";
		if($lastpage > 1)
		{
			if($prev != 0)
			{
				$pagination.= "<a href='?pg=1'><li class='previous_page btn btn-dark flat'>« First</li></a>";  
			}
			else
			{
				$pagination.= "<li class='previous_page disabled btn btn-dark flat'>« First</li>";  
			}

			if ($lastpage < 7 + ($adjacents * 2))
			{   
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $_GET['pg'])
					{
						$pagination.= "<li class='disabled btn btn-dark flat'>$counter</li>";
					}
					else
					{
						$pagination.= "<a href='?pg=$counter'><li class='btn btn-dark flat'>$counter</li></a>";                   
					}
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))
			{
				if($_GET['pg'] < 1 + ($adjacents * 2))       
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $_GET['pg'])
						{
							$pagination.= "<li class='disabled btn btn-dark flat'>$counter</li>";
						}
						else
						{
							$pagination.= "<a href='?pg=$counter'><li class='btn btn-dark flat'>$counter</li></a>";                   
						}
					}
					$pagination.= "<li class='dots disabled btn btn-dark flat'>...</li>";
					$pagination.= "<a href='?pg=$lpm1'><li class='btn btn-dark flat'>$lpm1</li></a>";
					$pagination.= "<a href='?pg=$lastpage'><li class='active btn btn-dark flat'>$lastpage</li></a>";       
				}
				elseif($lastpage - ($adjacents * 2) > $_GET['pg'] && $_GET['pg'] > ($adjacents * 2))
				{
					$pagination.= "<a href='?pg=1'><li class='active btn btn-dark flat'>1</li></a>";
					$pagination.= "<a href='?pg=2'><li class='active btn btn-dark flat'>2</li></a>";
					$pagination.= "<li class='dots disabled btn btn-dark flat'>...</li>";
					for ($counter = $_GET['pg'] - $adjacents; $counter <= $_GET['pg'] + $adjacents; $counter++)
					{
						if ($counter == $_GET['pg'])
						{
							$pagination.= "<li class='disabled btn btn-dark flat'>$counter</li>";
						}
						else
						{
							$pagination.= "<a href='?pg=$counter'><li class='btn btn-dark flat'>$counter</li></a>";                   
						}
					}
					$pagination.= "<li class='dots disabled btn btn-dark flat'>...</li>";
					$pagination.= "<a href='?pg=$lpm1'><li class='btn btn-dark flat'>$lpm1</li></a>";
					$pagination.= "<a href='?pg=$lastpage'><li class='active btn btn-dark flat'>$lastpage</li></a>";      
				}
				else
				{
					$pagination.= "<a href='?pg=1'><li class='active btn btn-dark flat'>1</li></a>";
					$pagination.= "<a href='?pg=2'><li class='active btn btn-dark flat'>2</li></a>";
					$pagination.= "<li class='dots disabled btn btn-dark flat'>...</li>";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $_GET['pg'])
						{
							$pagination.= "<li class='disabled btn btn-dark flat'><a href='#'>$counter</a></li>";
						}
						else
						{
							$pagination.= "<a href='?pg=$counter'><li class='btn btn-dark flat'>$counter</li></a>";                   
						}
					}
				}
			}

			if($lastpage == (isset($_GET['pg']) ? $_GET['pg'] : 1))
			{
				$pagination.= "<li class='next_page disabled btn btn-dark flat'><a>Last »</a></li>";  
			}
			else
			{
				$pagination.= "<a href='?pg=$lastpage'><li class='next_page btn btn-dark flat'>Last »</li></a>";  
			}
		}

		$pagination .= "</ul></div><div class='clearfix'></div>";

		return $pagination;
	}

	public static function getPage()//unused
	{
		return isset(self::$_url[2]) ? self::$_url[2] : 1;//hmmm
	}

	public static function getLinkPath()
	{
		return $_SERVER['REQUEST_URI'];
	}

	public static function register_db_notification($for_id,$for_nick,$text,$by_id,$by_nick,$link)
	{
		$notify = connect::$g_con->prepare('INSERT INTO `panel_notifications` (`UserID`,`UserName`,`Notification`,`FromID`,`FromName`,`Url`,`Date`) VALUES (?, ?, ?, ?, ?, ?, ?)');
		$notify->execute(array($for_id,$for_nick,$text,$by_id,$by_nick,$link,self::getDaT()));
		
		//return 1;
	}

	public static function register_db_log($by_id,$text,$by_ip)
	{
		$notify = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logById`, `logIP`, `logDate`) VALUES (?, ?, ?, ?)');
		$notify->execute(array($text,$by_id,$by_ip,self::getDaT()));
	}

	public static function xss_clean($data)
	{
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
		    $old_data = $data;
		    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		return $data;
	}
	
	public static function clean($text = null)
	{
		if(strpos($text, '<h1') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<h2') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<h3') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<h4') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<h5') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<h6') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<script') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, '<img') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, 'meta') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, 'document.location') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}
		if(strpos($text, 'olteanu') !== false)
		{
			return '<i><small>Unknown</small></i>';
		}

		strtr ($text, array ('olteanuadv' => '<replacement>'));

		$regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';

		return preg_replace_callback($regex, function ($matches)
		{
			return '<a target="_blank" href="'.$matches[0].'">'.$matches[0].'</a>';
		}, $text);
	}

	public static function IsValidMail($mail)
	{
		if ( user::is_bot($_SERVER['HTTP_USER_AGENT']) )
		{
			return;
		}
		if(preg_match('/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/',$mail))
		{
			return true;
		}
		return false;
	}

	public static function IsValidIP($IP)
	{
		if ( user::is_bot($_SERVER['HTTP_USER_AGENT']) )
		{
			return;
		}
		if(preg_match('/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/',$IP)||preg_match('/^$|^[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}$/',$IP))
		{
			return true;
		}
		return false;
	}

	public static function IsValidSteamStr($steamid)
	{
		if ( user::is_bot($_SERVER['HTTP_USER_AGENT']) )
		{
			return;//da
		}
		if(preg_match('/STEAM_0:[0-1]:[0-9]{1,10}/',$steamid)&&!strpos($steamid,'NON-STEAM'))
		{
			return true;//sau fix cu 0-1 + ^ început ți i final xd SAU 1,3
		}
		return false;
	}
	public static function SteamStr2SteamId($steamid)
	{
		if(!self::IsValidSteamStr($steamid)||user::is_bot($_SERVER['HTTP_USER_AGENT']))
		{
			return;
		}
		$parts = explode(':', str_replace('STEAM_', '' ,$steamid));
		return bcadd(bcadd('76561197960265728', $parts['1']), bcmul($parts['2'], '2'));
	}
	public static function SteamId2StrSteam($steamid64,$steamid)
	{
		if(!self::IsValidSteamStr($steamid)||user::is_bot($_SERVER['HTTP_USER_AGENT']))
		{
			return;
		}
	    $accountID = bcsub($steamid64, '76561197960265728');
	    return 'STEAM_0:'.bcmod($accountID, '2').':'.bcdiv($accountID, 2);
	}

	public static function GetPanelStatus($type = 2)
	{
		$data = "";
		switch($type)
		{
			case 1:
			{
				$udata = json_decode(file_get_contents("https://raw.githubusercontent.com/raiz0x/CS1.6/master/web/cs16panels/panels_sts.json"));
				foreach($udata->response->status as $status)
				{
				    $data=$status;
				}
				break;
			}
			case 2:
			{
				$udata = json_decode(file_get_contents("https://raw.githubusercontent.com/raiz0x/CS1.6/master/web/cs16panels/panels_sts.json"), true);
				foreach($udata['response']['license'] as $key => $value)
				{
					$process = explode(";", $value['licensed_sites_mix']);
					foreach($process as $k => $v)
					{
						if(trim($v))
						{
							if(self::IsValidIP($v))
							{
								$data=$v==gethostbyname(gethostname())?true:false;
							}
							else
							{
								$data=strpos($v, this::$_PAGE_URL) !== FALSE?true:false;
							}
						}
					}
				}
				break;
			}
		}
		return $data;
	}
}
