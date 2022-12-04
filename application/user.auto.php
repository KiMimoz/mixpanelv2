<?php
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    protected $table = 'admins';

    protected $primaryKey = 'id';

    protected $STEAM_API_KEY = '0D528CA03A875C555CD1A5EBFD3175A7';

	protected $hidden = ['password'];

	protected static $__user = [];

	public static $permissions = [];

	private static $_instance = null;

	public static $ENABLE_LOGIN = 1;
	public static $ENABLE_REGISTRATION = 1;
	public static $ENABLE_REGISTRATION_WITH_STEAM = 1;

	public static $ENABLE_AUTO_PW_RECOVER = 0;

	public static $AUTO_UNLOG_AFTER = 3600;//in seconds

	public static $USER_SV_LOGIN_INFO = 'setinfo _pw "your password"';

	public static function init()
	{
		if(self::isLogged())
		{
			$u = user::find(user::get());
			if(!$u)
			{
				return self::redirect_to('');
			}

			self::$__user = $u;
		}
	}

	public static function isLogged()//as bool..
	{
		return isset($_SESSION['user']) ? true : false;
	}

	public static function get()
	{
		return isset($_SESSION['user']) ? $_SESSION['user'] : false;
	}

	public static function getUserData()
	{
		if(!self::isLogged())
		{
			return false;
		}

		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __get($var)
	{
		self::getUserData();
		
		return self::getData(self::get(),$var);
	}

	public static function redirect_to($page='',$delay = false)
	{
		if($delay != false)
		{
			echo '<meta http-equiv="refresh" content="' . $delay . ';' . this::$_PAGE_URL . $page  . '">';

			return;
		}

		header('Location: ' . this::$_PAGE_URL . $page);
	}

    public static function getData($id,$data)
    {
        if(!is_array($data))
        {
            $q = connect::prepare('SELECT `'.$data.'` FROM `admins` WHERE `id` = ?');
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

            $q = connect::prepare('SELECT '.$q.' FROM `admins` WHERE `id` = ?');
            $q->execute(array($id));
            return $q->fetch(PDO::FETCH_ASSOC);
        }
    }

	public static function format($id, $name = null, $faction = null,$hover = true)
	{
		return self::formatName($id, $name, $faction, $hover);
	}
    public static function formatName($id,$redirect=0)
    {
        $link = '<a href="'.this::$_PAGE_URL.'profile/'.$id.'"'.($redirect==1?' target="_blank"':'').'>'.self::getData($id,'auth').'</a>';
        return $link;
    }

	public static function getLocation($ip,$type)
	{
		$data = json_decode(file_get_contents('https://reallyfreegeoip.org/json/'.$ip),true);
		switch ($type)
		{
			case 1:
			{
				return $data['country_name'];
				break;
			}
			case 2:
			{
				return $data['region_name'];
				break;
			}
			
			default:
			{
				return $data['country_name'];
				break;
			}
		}
		return $data['country_name'];
	}


	public static function is_bot($user_agent)
	{
	    $botRegexPattern = "(googlebot\/|Googlebot\-Mobile|Googlebot\-Image|Google favicon|Mediapartners\-Google|bingbot|slurp|java|wget|curl|Commons\-HttpClient|Python\-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST\-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub\.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum\.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips\-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail\.RU_Bot|discobot|heritrix|findthatfile|europarchive\.org|NerdByNature\.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb\-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web\-archive\-net\.com\.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks\-robot|it2media\-domain\-crawler|ip\-web\-crawler\.com|siteexplorer\.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki\-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e\.net|GrapeshotCrawler|urlappendbot|brainobot|fr\-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf\.fr_bot|A6\-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive\.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j\-asr|Domain Re\-Animator Bot|AddThis|YisouSpider|BLEXBot|YandexBot|SurdotlyBot|AwarioRssBot|FeedlyBot|Barkrowler|Gluten Free Crawler|Cliqzbot)";
	    return preg_match("/{$botRegexPattern}/", $user_agent);
	}
	public static function getSteamProfileData($steam,$type=1)
	{
		if ( self::is_bot($_SERVER['HTTP_USER_AGENT']) )
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
				curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.self::$STEAM_API_KEY.'&steamids='.$steam.'');
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
				$ch =  curl_init('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.self::$STEAM_API_KEY.'&steamids='.$steam.'');
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
				$data2 = file_get_contents('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.self::$STEAM_API_KEY.'&steamids='.$steam.'');
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
						CURLOPT_URL => 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.self::$STEAM_API_KEY.'&steamids='.$steam.'',
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

	public static function GetIp()
	{
		if ( self::is_bot($_SERVER['HTTP_USER_AGENT']) || !this::IsValidIP($_SERVER['REMOTE_ADDR']) )
		{
			return;
		}

		return $_SERVER['REMOTE_ADDR'];
	}

	public static function MakeProfileUrl($id)
	{
		$formated_id='';

		if(this::getSpec('admins', 'steamid64', 'id', $id)!='-1')
		{
			$formated_id=this::getSpec('admins', 'steamid64', 'id', $id);
		}
		/*else if(this::getSpec('admins', 'id', 'id', $id)!='-1')
		{
			$formated_id=this::getSpec('admins', 'id', 'id', $id);
		}*/
		else if(this::getSpec('admins', 'name', 'id', $id)!='-1')
		{
			$formated_id=this::getSpec('admins', 'name', 'id', $id);
		}
		else if(this::getSpec('admins', 'email', 'id', $id)!='-1')
		{
			$formated_id=this::getSpec('admins', 'email', 'id', $id);
		}
		else if(this::getSpec('admins', 'auth', 'id', $id)!='-1')
		{
			$formated_id=this::getSpec('admins', 'auth', 'id', $id);
		}

		return this::$_PAGE_URL."profile/".(empty($formated_id)||$formated_id=='-1'?$id:$formated_id);
	}

	public static function GetSteamAvatar($detect,$type=1)
	{
		$steam_auth=is_numeric($detect)?this::getSpec("admins", "auth", "id", $detect):this::getSpec("admins", "auth", "name", $detect);
		if($type==1)
		{
			$avatar=this::IsValidSteamStr($steam_auth)?user::getSteamProfileData(this::SteamStr2SteamId($steam_auth))->avatarFull:"https://steamuserimages-a.akamaihd.net/ugc/885384897182110030/F095539864AC9E94AE5236E04C8CA7C2725BCEFF/";
			$url='<img alt="" src="%s">';
			return sprintf($url,$avatar);
		}
		else
		{
			$avatar=this::IsValidSteamStr($steam_auth)?user::getSteamProfileData(this::SteamStr2SteamId($steam_auth))->avatarFull:"https://steamuserimages-a.akamaihd.net/ugc/885384897182110030/F095539864AC9E94AE5236E04C8CA7C2725BCEFF/";
			return $avatar;
		}
	}
}