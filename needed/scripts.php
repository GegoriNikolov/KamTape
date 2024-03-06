<?php
 $jrmaintenance = 0;
ini_set('display_errors', 0); ini_set('display_startup_errors', 0);
require 'C:/xampp/htdocs/vendor/autoload.php';
//Setup site
ob_start(); // Makes redirects easier.
$config = parse_ini_file(__DIR__ . "/config.ini");
try {
	$conn = new PDO("mysql:host=".$config["servername"].";dbname=".$config["dbname"], $config["username"], $config["password"]);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { ?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>KamTape - Your Old School Video Repository</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link href="styles.css" rel="stylesheet" type="text/css">
<link rel="alternate" type="application/rss+xml" title="KamTape " " Recently Added Videos [RSS]" href="https://www.KamTape.com/rss/global/recently_added.rss">
</head>

<body>

<div style="margin: auto; text-align:left; padding: 20px;">
	<div style="font-size:16px;">
	
		<img src="img/logo.gif" height="71" hspace="20" alt="KamTape" align="left">
		Sorry, something went wrong.<br />
		<br />
		Check back later!
	</div>
</div>

</body>
</html>
<?
}

global $conn;
session_set_cookie_params(3600 * 24 * 7); // Sessions last for one week now. So Much Win
session_start(); // This allows sessions to work. If this is removed, you cannot log in.

if($_SESSION['uid']) {
    $session = $conn->prepare("SELECT * FROM users WHERE uid = :uid");
    $session->bindParam(":uid", $_SESSION['uid']);
    $session->execute();
    if(!$session->rowCount()) {
        header("Location: logout.php");
        die();
    } else {
        $session = $session->fetch(PDO::FETCH_ASSOC);
    }
} 

$version_of_tape = "kamtape";
$_KAMTAPECONF = $conn->prepare('SELECT * FROM kamtape_web WHERE version = ? ORDER BY version DESC LIMIT 1');
$_KAMTAPECONF->execute([$version_of_tape]);
if($_KAMTAPECONF->rowCount() == 0) {
	die("<h1>This version of KamTape was not set up correctly</h1>");
} else {
	$_KAMTAPECONF = $_KAMTAPECONF->fetch(PDO::FETCH_ASSOC);
}
if($session['username'] == 'jr' && $jrmaintenance == 1) {
    ini_set('display_errors', 1); ini_set('display_startup_errors', 1);
}
function invokethConfig($where) {
    global $_KAMTAPECONF;
    if (array_key_exists($where, $_KAMTAPECONF)) {
        return htmlspecialchars($_KAMTAPECONF[$where]);
    } else {
        return null;
    }
}

// sets cookie to 4 days -- not a week because i dont got that much trust in people
$maintenance_expires = time() + (4 * 24 * 60 * 60);
if($session['staff'] == 1) {
setcookie("hates__dwntime", "thats_right", $maintenance_expires);
// $_COOKIE['hates__dwntime'] == "thats_right";
}
    if (!isset($_COOKIE['hates__dwntime']) && invokethConfig("maintenance") == 1){
    require_once $_SERVER['DOCUMENT_ROOT'] . '/index_down.php';
    die();
    }
// Set timezone so everything matches up.
date_default_timezone_set('America/Los_Angeles');
 $conn->exec("SET time_zone = '-7:00'");

$current_page = basename($_SERVER['PHP_SELF']);
$current_page = str_replace('.php', '', $current_page);

// ip banning
$stmt = $conn->prepare("SELECT ip FROM ip_bans WHERE ip = :ip");
$stmt->execute([':ip' => $_SERVER['REMOTE_ADDR']]);

if($stmt->rowCount() != 0) {
 die("<h1>404 Not Found</h1>");
}


//Functions
function getStillUrl($vid, $still_id = 2) {
    if($still_id == 2) {
    $url = '//static.kamtape.com/get_still.php?video_id='.$vid;
    } else {
    $url = '//static.kamtape.com/get_still.php?video_id='.$vid.'&still_id='.$still_id;
    }
    return $url;
}
function alert($error, $type = "success") {
    // alert ("Your error here", "error")
    // Success (default) == gray-bluish
    // Error == red
    // process == orange. Only use i've seen is from the video upload success page from 2006
    $error = str_replace(PHP_EOL, '<br><br>', $error);
    if($type == "success") {
    echo '<table width="100%" align="center" bgcolor="#666666" cellpadding="6" cellspacing="3" border="0">
		<tbody><tr>
			<td align="center" bgcolor="#FFFFFF"><span class="success">'.$error.'</span></td>
		</tr>
	</tbody></table></br>';
    } else if($type == "error") {
      echo '<table width="100%" align="center" bgcolor="#FF0000" cellpadding="6" cellspacing="3" border="0">
		<tbody><tr>
			<td align="center" bgcolor="#FFFFFF"><span class="error">'.$error.'</span></td>
		</tr>
	</tbody></table></br>';  
    } else if($type == "process") {
    echo '<table width="100%" align="center" bgcolor="#d86c2f" cellpadding="6" cellspacing="3" border="0">
		<tbody><tr>
			<td align="center" bgcolor="#FFFFFF"><span class="error" style="color: #d86c2f;">'.$error.'</span></td>
		</tr>
	</tbody></table></br>';  
    } else {
        if (substr($type, 0, 1) !== "#") {
    $type = "#" . $type;
    }
        echo '<table width="100%" align="center" bgcolor="#'.$type.'" cellpadding="6" cellspacing="3" border="0">
		<tbody><tr>
			<td align="center" bgcolor="#FFFFFF"><span class="success" style="color:'.$type.';">'.$error.'</span></td>
		</tr>
	</tbody></table></br>';  
    
    }
}
function encrypt($data) {
    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivSize);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $config["aeskey"], OPENSSL_RAW_DATA, $iv);
    $encryptedData = base64_encode($iv . $encrypted);
    return $encryptedData;
}
function unique_word_count($string) {
    $string = explode(' ', strtolower($string));
    $words = array_unique($string);
    return count($words);
}
function decrypt($encryptedData) {
    $encryptedData = base64_decode($encryptedData);
    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($encryptedData, 0, $ivSize);
    $encrypted = substr($encryptedData, $ivSize);
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $config["aeskey"], OPENSSL_RAW_DATA, $iv);
    $decrypted = htmlspecialchars($decrypted);
    $decrypted = nl2br($decrypted);
    return $decrypted;
}
function retroDate($date, $format = "F j, Y") {
    // Short script to make old dates easier.
    // Accommodates for past leap years too! ^_^
    // 08/30/05: This was the first thing ever coded for KamTape. When it begin, it was just me and playing around with this function. Good times. 
    // 10/3/05: First ever actual update to this function. It's so simple, makes sense.
  if ($date === "now") {
    $dateTime = new DateTime();
    } else {
    $dateTime = new DateTime($date);
    }
  $dateTime->modify("-18 years");
  return $dateTime->format($format);
} 

function commentTimeAgo($timestamp) {
    $currentTimestamp = time();
    $timeAgo = $currentTimestamp - $timestamp;

    $days = floor($timeAgo / (60 * 60 * 24));
    $hours = floor(($timeAgo % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($timeAgo % (60 * 60)) / 60);

    $result = '';
    if ($days > 0) {
        $result .= $days . ' day' . ($days != 1 ? 's' : '') . ', ';
    }
    if ($hours > 0) {
        $result .= $hours . ' hour' . ($hours != 1 ? 's' : '') . ', ';
    }
    $result .= $minutes . ' minute' . ($minutes != 1 ? 's' : '') . ' ago';

    return $result;
}

function pluralize($number, $singular, $plural) {
    if ($number === 1) {
        return $number . ' ' . $singular;
    } else {
        return $number . ' ' . $plural;
    }
}

function AutoLinkUrls($str,$popup = FALSE){
    if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $str, $matches)){
        $pop = ($popup == TRUE) ? " target=\"_blank\" " : "";
        for ($i = 0; $i < count($matches['0']); $i++){
            $period = '';
            if (preg_match("|\.$|", $matches['6'][$i])){
                $period = '.';
                $matches['6'][$i] = substr($matches['6'][$i], 0, -1);
            }
            $str = str_replace($matches['0'][$i],
                    $matches['1'][$i].'<a href="http'.
                    $matches['4'][$i].'://'.
                    $matches['5'][$i].
                    $matches['6'][$i].'"'.$pop.'>http'.
                    $matches['4'][$i].'://'.
                    $matches['5'][$i].
                    $matches['6'][$i].'</a>'.
                    $period, $str);
        }//end for
    }//end if
    return $str;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}
function error() {
    header("Location: /error.html");
    exit();
}

function force_login() {
if($_SESSION['uid'] == NULL) {
header("Location: signup_login.php?next=". $_SERVER['REQUEST_URI']);
}
}
$_SESSION['em_confirmation'] = $session['em_confirmation'];
function email_confirm() {
if($_SESSION['em_confirmation'] == "false") {
header("Location: email_confirm.php?next=". $_SERVER['REQUEST_URI']);
}
if($_SESSION['uid'] == NULL) {
header("Location: signup_login.php?next=". $_SERVER['REQUEST_URI']);
}
}

function whos_online() { 
    $lastonline = $GLOBALS['conn']->query("SELECT * FROM users WHERE termination = 0 AND em_confirmation = 'true' ORDER BY users.last_act DESC LIMIT 5");
    ?>
<!--User Highlighted Here If Implemented-->
			<div style="padding-top: 15px;">
						<table class="roundedTable" width="180" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#eeeedd">
			<tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="170">
					<div style="font-size: 14px; font-weight: bold; margin-bottom: 8px; color:#666633;">
						Last 5 users online...
					</div>
									<?php foreach($lastonline as $user) { 
                    /*$user['vids'] = $GLOBALS['conn']->prepare("SELECT COUNT(vid) FROM videos WHERE uid = ? AND converted = 1");
				    $user['vids']->execute([$user['uid']]);
				    $user['vids'] = $user['vids']->fetchColumn();

                    $user['favs'] = $GLOBALS['conn']->prepare("SELECT COUNT(fid) FROM favorites WHERE uid = ?");
				    $user['favs']->execute([$user['uid']]);
				    $user['favs'] = $user['favs']->fetchColumn();

                    $user['friends'] = $GLOBALS['conn']->prepare("SELECT COUNT(relationship) FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
				    $user['friends']->execute([$user['uid'], $user['uid']]);
				    $user['friends'] = $user['friends']->fetchColumn();*/
                    ?>
				
					<div style="font-size: 12px; font-weight: bold; margin-bottom: 5px;"><a href="profile.php?user=<?php echo htmlspecialchars($user['username']); ?>" <? if (strlen($user['username']) > 14) { ?> title="<?= htmlspecialchars($user['username']) ?>" <? } ?>><?php
echo shorten($user['username'], 14);
?></a></div>

					<div style="font-size: 12px; margin-bottom: 8px; padding-bottom: 10px; border-bottom: 1px dashed #CCCC66;"><a href="profile_videos.php?user=<?php echo htmlspecialchars($user['username']); ?>"><img src="img/icon_vid.gif" alt="Videos" width="14" height="14" border="0" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"></a> (<a href="profile_videos.php?user=<?php echo htmlspecialchars($user['username']); ?>"><?php echo $user['pub_vids']; ?></a>)
					 | <a href="profile_favorites.php?user=<?php echo htmlspecialchars($user['username']); ?>"><img src="img/icon_fav.gif" alt="Favorites" width="14" height="14" border="0" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"></a> (<a href="profile_favorites.php?user=<?php echo htmlspecialchars($user['username']); ?>"><?php echo $user['fav_count']; ?></a>)
					 | <a href="profile_friends.php?user=<?php echo htmlspecialchars($user['username']); ?>"><img src="img/icon_friends.gif" alt="Friends" width="14" height="14" border="0" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"></a> (<a href="profile_friends.php?user=<?php echo htmlspecialchars($user['username']); ?>"><?php echo $user['friends_count']; ?></a>)</div><? } ?>

				
				<div style="font-weight: bold; margin-bottom: 5px;">Icon Key:</div>
				<div style="margin-bottom: 4px;"><img src="/img/icon_vid.gif" alt="Videos" width="14" height="14" border="0" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"> - Videos</div>
				<div style="margin-bottom: 4px;"><img src="/img/icon_fav.gif" alt="Favorites" width="14" height="14" border="0" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"> - Favorites</div>
				<img src="/img/icon_friends.gif" alt="Friends" width="14" height="14" border="0" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"> - Friends
				

				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>

			</div>
			
		</td>
	</tr>
</table>

        <?php
}
function shorten($text, $number, $symbols = '...') {
    $text = $text;
    $new = (strlen($text) > $number) ? substr($text, 0, $number) . $symbols : $text;
    return htmlspecialchars($new);
}

function timeAgo($date) { 
/*
  $now = time();
  $time = strtotime($date);
  $diff = $now - $time;
  */
  $dateObj = new DateTime($date);
  $dateObj->modify('-1 hour');

  $now = new DateTime();
  $diff = $now->getTimestamp() - $dateObj->getTimestamp();

  $periods = array(
    "year",
    "month",
    "week",
    "day",
    "hour",
    "minute",
    "second"
  );
  $lengths = array(
    31556926,
    2629743,
    604800,
    86400,
    3600,
    60,
    1
  );

  $difference = "";
  for ($i = 0; $i < count($lengths); $i++) {
    if ($diff >= $lengths[$i]) {
      $number = floor($diff / $lengths[$i]);
      $difference .= $number . " " . $periods[$i];
      if ($number != 1) {
        $difference .= "s";
      }
      $difference .= " ago";
      break;
    }
  }
  if (empty($difference)) {
    $difference = "0 seconds ago";
  }
  return $difference;
}

function base64url_encode($data)
{
  // First of all you should encode $data to Base64 string
  $b64 = base64_encode($data);

  // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
  if ($b64 === false) {
    return false;
  }

  // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
  $url = strtr($b64, '+/', '-_');

  // Remove padding character from the end of line and return the Base64URL result
  return rtrim($url, '=');
}

/**
 * Decode data from Base64URL
 * @param string $data
 * @param boolean $strict
 * @return boolean|string
 */
function base64url_decode($data, $strict = false)
{
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');

  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}

function generateId($length = 11) {
    // Takes 8 (or whatever equivalent for the length entered) randomized bytes and then encodes it in base64url
    // Usage: Most IDs, see generateId2
    $needed_bytes = floor($length * 0.75);
    $needed_bytes = random_bytes($needed_bytes);
    $needed_bytes = base64url_encode($needed_bytes);
    $video = $GLOBALS['conn']->prepare("SELECT vid FROM videos WHERE vid = ?");
    $video->execute([$needed_bytes]);
    if($video->rowCount() > 0) {
        $IdGenned = true;
    }
    $video = $GLOBALS['conn']->prepare("SELECT uid FROM users WHERE uid = ?");
    $video->execute([$needed_bytes]);
    if($video->rowCount() > 0) {
        $IdGenned = true;
    }
    if($IdGenned != true) {
    return $needed_bytes;
    } else {
    generateId($length);
    }
}


function generateId2($length = 11) {
    // Hex representation of a decoded generateId
    // Usage: Email confirmation URLs, playlist IDs, not as used as generateId
	$funny_ids = base64url_decode(generateId($length));
    return strtoupper(bin2hex($funny_ids));
}

$_VCHANE = [
    1 => "Arts & Animation",
    2 => "Autos & Vehicles",
    3 => "Education & Instructional",
    4 => "Events & Weddings",
    5 => "Entertainment",
    6 => "Family",
    7 => "For Sale & Auctions",
    8 => "Hobbies & Interests",
    9 => "Humor",
    10 => "Music",
    11 => "News & Politics",
    12 => "Odd & Outrageous",
    13 => "People",
    14 => "Personals & Dating",
    15 => "Pets & Animals",
    16 => "Science & Technology",
    17 => "Sports",
    18 => "Short Movies",
    19 => "Travel & Places",
    20 => "Video Games",
    21 => "Videoblogging",
    //22 => "Cooking",
];

 // You'd think this took a good while... It didn't. I can finally make an accurate country dropdown without any hassle thanks to this: http://code-cocktail.in/tools/convert-selectbox-to-array/#
      $_COUNTRIES = [
  NULL => '---',
  'US' => 'United States',
  'AF' => 'Afghanistan',
  'AL' => 'Albania',
  'DZ' => 'Algeria',
  'AS' => 'American Samoa',
  'AD' => 'Andorra',
  'AO' => 'Angola',
  'AI' => 'Anguilla',
  'AG' => 'Antigua and Barbuda',
  'AR' => 'Argentina',
  'AM' => 'Armenia',
  'AW' => 'Aruba',
  'AU' => 'Australia',
  'AT' => 'Austria',
  'AZ' => 'Azerbaijan',
  'BS' => 'Bahamas',
  'BH' => 'Bahrain',
  'BD' => 'Bangladesh',
  'BB' => 'Barbados',
  'BY' => 'Belarus',
  'BE' => 'Belgium',
  'BZ' => 'Belize',
  'BJ' => 'Benin',
  'BM' => 'Bermuda',
  'BT' => 'Bhutan',
  'BO' => 'Bolivia',
  'BA' => 'Bosnia and Herzegovina',
  'BW' => 'Botswana',
  'BV' => 'Bouvet Island',
  'BR' => 'Brazil',
  'IO' => 'British Indian Ocean Territory',
  'VG' => 'British Virgin Islands',
  'BN' => 'Brunei',
  'BG' => 'Bulgaria',
  'BF' => 'Burkina Faso',
  'BI' => 'Burundi',
  'KH' => 'Cambodia',
  'CM' => 'Cameroon',
  'CA' => 'Canada',
  'CV' => 'Cape Verde',
  'KY' => 'Cayman Islands',
  'CF' => 'Central African Republic',
  'TD' => 'Chad',
  'CL' => 'Chile',
  'CN' => 'China',
  'CX' => 'Christmas Island',
  'CC' => 'Cocos (Keeling) Islands',
  'CO' => 'Colombia',
  'KM' => 'Comoros',
  'CG' => 'Congo',
  'CD' => 'Congo - Democratic Republic of',
  'CK' => 'Cook Islands',
  'CR' => 'Costa Rica',
  'CI' => 'Cote d\'Ivoire',
  'HR' => 'Croatia',
  'CU' => 'Cuba',
  'CY' => 'Cyprus',
  'CZ' => 'Czech Republic',
  'DK' => 'Denmark',
  'DJ' => 'Djibouti',
  'DM' => 'Dominica',
  'DO' => 'Dominican Republic',
  'TP' => 'East Timor',
  'EC' => 'Ecuador',
  'EG' => 'Egypt',
  'SV' => 'El Salvador',
  'GQ' => 'Equitorial Guinea',
  'ER' => 'Eritrea',
  'EE' => 'Estonia',
  'ET' => 'Ethiopia',
  'FK' => 'Falkland Islands (Islas Malvinas)',
  'FO' => 'Faroe Islands',
  'FJ' => 'Fiji',
  'FI' => 'Finland',
  'FR' => 'France',
  'GF' => 'French Guyana',
  'PF' => 'French Polynesia',
  'TF' => 'French Southern and Antarctic Lands',
  'GA' => 'Gabon',
  'GM' => 'Gambia',
  'GZ' => 'Gaza Strip',
  'GE' => 'Georgia',
  'DE' => 'Germany',
  'GH' => 'Ghana',
  'GI' => 'Gibraltar',
  'GR' => 'Greece',
  'GL' => 'Greenland',
  'GD' => 'Grenada',
  'GP' => 'Guadeloupe',
  'GU' => 'Guam',
  'GT' => 'Guatemala',
  'GN' => 'Guinea',
  'GW' => 'Guinea-Bissau',
  'GY' => 'Guyana',
  'HT' => 'Haiti',
  'HM' => 'Heard Island and McDonald Islands',
  'VA' => 'Holy See (Vatican City)',
  'HN' => 'Honduras',
  'HK' => 'Hong Kong',
  'HU' => 'Hungary',
  'IS' => 'Iceland',
  'IN' => 'India',
  'ID' => 'Indonesia',
  'IR' => 'Iran',
  'IQ' => 'Iraq',
  'IE' => 'Ireland',
  'IL' => 'Israel',
  'IT' => 'Italy',
  'JM' => 'Jamaica',
  'JP' => 'Japan',
  'JO' => 'Jordan',
  'KZ' => 'Kazakhstan',
  'KE' => 'Kenya',
  'KI' => 'Kiribati',
  'KW' => 'Kuwait',
  'KG' => 'Kyrgyzstan',
  'LA' => 'Laos',
  'LV' => 'Latvia',
  'LB' => 'Lebanon',
  'LS' => 'Lesotho',
  'LR' => 'Liberia',
  'LY' => 'Libya',
  'LI' => 'Liechtenstein',
  'LT' => 'Lithuania',
  'LU' => 'Luxembourg',
  'MO' => 'Macau',
  'MK' => 'Macedonia - The Former Yugoslav Republic of',
  'MG' => 'Madagascar',
  'MW' => 'Malawi',
  'MY' => 'Malaysia',
  'MV' => 'Maldives',
  'ML' => 'Mali',
  'MT' => 'Malta',
  'MH' => 'Marshall Islands',
  'MQ' => 'Martinique',
  'MR' => 'Mauritania',
  'MU' => 'Mauritius',
  'YT' => 'Mayotte',
  'MX' => 'Mexico',
  'FM' => 'Micronesia - Federated States of',
  'MD' => 'Moldova',
  'MC' => 'Monaco',
  'MN' => 'Mongolia',
  'MS' => 'Montserrat',
  'MA' => 'Morocco',
  'MZ' => 'Mozambique',
  'MM' => 'Myanmar',
  'NA' => 'Namibia',
  'NR' => 'Naura',
  'NP' => 'Nepal',
  'NL' => 'Netherlands',
  'AN' => 'Netherlands Antilles',
  'NC' => 'New Caledonia',
  'NZ' => 'New Zealand',
  'NI' => 'Nicaragua',
  'NE' => 'Niger',
  'NG' => 'Nigeria',
  'NU' => 'Niue',
  'NF' => 'Norfolk Island',
  'KP' => 'North Korea',
  'MP' => 'Northern Mariana Islands',
  'NO' => 'Norway',
  'OM' => 'Oman',
  'PK' => 'Pakistan',
  'PW' => 'Palau',
  'PA' => 'Panama',
  'PG' => 'Papua New Guinea',
  'PY' => 'Paraguay',
  'PE' => 'Peru',
  'PH' => 'Philippines',
  'PN' => 'Pitcairn Islands',
  'PL' => 'Poland',
  'PT' => 'Portugal',
  'PR' => 'Puerto Rico',
  'QA' => 'Qatar',
  'RE' => 'Reunion',
  'RO' => 'Romania',
  'RU' => 'Russia',
  'RW' => 'Rwanda',
  'KN' => 'Saint Kitts and Nevis',
  'LC' => 'Saint Lucia',
  'VC' => 'Saint Vincent and the Grenadines',
  'WS' => 'Samoa',
  'SM' => 'San Marino',
  'ST' => 'Sao Tome and Principe',
  'SA' => 'Saudi Arabia',
  'SN' => 'Senegal',
  'CS' => 'Serbia and Montenegro',
  'SC' => 'Seychelles',
  'SL' => 'Sierra Leone',
  'SG' => 'Singapore',
  'SK' => 'Slovakia',
  'SI' => 'Slovenia',
  'SB' => 'Solomon Islands',
  'SO' => 'Somalia',
  'ZA' => 'South Africa',
  'GS' => 'South Georgia and the South Sandwich Islands',
  'KR' => 'South Korea',
  'ES' => 'Spain',
  'LK' => 'Sri Lanka',
  'SH' => 'St. Helena',
  'PM' => 'St. Pierre and Miquelon',
  'SD' => 'Sudan',
  'SR' => 'Suriname',
  'SJ' => 'Svalbard',
  'SZ' => 'Swaziland',
  'SE' => 'Sweden',
  'CH' => 'Switzerland',
  'SY' => 'Syria',
  'TW' => 'Taiwan',
  'TJ' => 'Tajikistan',
  'TZ' => 'Tanzania',
  'TH' => 'Thailand',
  'TG' => 'Togo',
  'TK' => 'Tokelau',
  'TO' => 'Tonga',
  'TT' => 'Trinidad and Tobago',
  'TN' => 'Tunisia',
  'TR' => 'Turkey',
  'TM' => 'Turkmenistan',
  'TC' => 'Turks and Caicos Islands',
  'TV' => 'Tuvalu',
  'UG' => 'Uganda',
  'UA' => 'Ukraine',
  'AE' => 'United Arab Emirates',
  'GB' => 'United Kingdom',
  'VI' => 'United States Virgin Islands',
  'UY' => 'Uruguay',
  'UZ' => 'Uzbekistan',
  'VU' => 'Vanuatu',
  'VE' => 'Venezuela',
  'VN' => 'Vietnam',
  'WF' => 'Wallis and Futuna',
  'PS' => 'West Bank',
  'EH' => 'Western Sahara',
  'YE' => 'Yemen',
  'ZM' => 'Zambia',
  'ZW' => 'Zimbabwe',
   ]; function getCountryName($isoCode) {
       // What I did earlier was really fuxxing newby, here's a better version
      global $_COUNTRIES;
    if (isset($_COUNTRIES[$isoCode])) {
        return $_COUNTRIES[$isoCode];
    } else {
        return '???';
    }
}
if ($session['termination'] == 1) {
   session_start();
   session_destroy(); 
   session_error_index("Your user account is suspended.", "error");
}

// Here's a better online detector. Basically I couldn't find any efficent code for making an accurate online detector in this type of codebase, so this is currently the best we got.
if(!empty($session['uid'])){
$lastlogin = $conn->prepare("UPDATE users SET last_act = CURRENT_TIMESTAMP WHERE uid = ?");
$lastlogin->execute([$session['uid']]);
}
function showChannels($vid, $include_char = ',') {
$video2chan= $GLOBALS['conn']->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video2chan->execute([$vid]);

if($video2chan->rowCount() == 0) {
 echo "No channels!";
} else {
    global $_VCHANE;
	$video2chan = $video2chan->fetch(PDO::FETCH_ASSOC);
    $ch1 = $video2chan['ch1'];
    $ch2 = $video2chan['ch2'];
    $ch3 = $video2chan['ch3'];
    if ($ch1 != NULL && $ch2 != NULL) {
     echo '<a href="channels.php?c='.$ch1.'">'.$_VCHANE[$ch1].'</a>'.$include_char.' ';
    } elseif ($ch1 != NULL && $ch2 == NULL && $ch3 == NULL) {
     echo '<a href="channels.php?c='.$ch1.'">'.$_VCHANE[$ch1].'</a>

						';
    }
    
    if ($ch2 != NULL && $ch3 != NULL) {
     echo '<a href="channels.php?c='.$ch2.'">'.$_VCHANE[$ch2].'</a>'.$include_char.' ';
    } elseif ($ch2 != NULL && $ch3 == NULL) {
     echo '<a href="channels.php?c='.$ch2.'">'.$_VCHANE[$ch2].'</a>';
    }

  if ($ch3 != NULL) {
     echo '<a href="channels.php?c='.$ch3.'">'.$_VCHANE[$ch3].'</a>';
    }
  }
} // jr was going insane about php not recognising the drawStars() function; it actually wasn't php's fault
function drawStars($rating, $size = "L", $extras = NULL) {
    if ($size == "L") { // Use == for comparison
        $star_half_icon = "star_half.gif";
        $star_none_icon = "star_bg.gif";
        $star_full_icon = "star.gif";
    }

    if ($size == "SM") { // Use == for comparison
        $star_half_icon = "star_sm_half.gif";
        $star_none_icon = "star_sm_bg.gif";
        $star_full_icon = "star_sm.gif";  
    }

    if(fmod($rating, 1) !== 0.00){
        $rating_half = true;
    } else {
        $rating_half = false;
    }
    
    $star_rating_draw = ''; // Initialize the variable
    
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_full_icon . '">';
        } elseif ($rating_half && $rating > ($i - 1) && $rating < $i) {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_half_icon . '">';
        } else {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_none_icon . '">';
        }
    }

    echo $star_rating_draw;
}

function getRatingCount($vid) {
    $ratingscount = $GLOBALS['conn']->prepare("SELECT COUNT(rating) FROM ratings WHERE video = ?");
    $ratingscount->execute([$vid]);
    $ratingscount = $ratingscount->fetchColumn();
    return $ratingscount;

}
function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}
$enduser_ip = get_ip_address();

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}
function grabRatings($vid, $size = "L", $extras = NULL) {
    
    $avg = $GLOBALS['conn']->prepare("SELECT AVG(rating) FROM ratings WHERE video = ?");
    $avg->execute([$vid]);
    $average = $avg->fetchColumn();
    $rating = $average;
    // fuck php... not only am i disallowed to redeclare it, but it just also randomly fucking broke. this is top retardation  
    if ($size == "L") { // Use == for comparison
        $star_half_icon = "star_half.gif";
        $star_none_icon = "star_bg.gif";
        $star_full_icon = "star.gif";
    }

    if ($size == "SM") { // Use == for comparison
        $star_half_icon = "star_sm_half.gif";
        $star_none_icon = "star_sm_bg.gif";
        $star_full_icon = "star_sm.gif";  
    }

    if(fmod($rating, 1) !== 0.00){
        $rating_half = true;
    } else {
        $rating_half = false;
    }
    
    $star_rating_draw = ''; // Initialize the variable
    
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_full_icon . '">';
        } elseif ($rating_half && $rating > ($i - 1) && $rating < $i) {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_half_icon . '">';
        } else {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_none_icon . '">';
        }
    }

    echo $star_rating_draw;

}

function grabRatingsPage($vid, $size = "SM", $show_count = 1) {
    $avg = $GLOBALS['conn']->prepare("SELECT AVG(rating) FROM ratings WHERE video = ?");
    $avg->execute([$vid]);
    $average = $avg->fetchColumn();
    $rating = $average;
    if($average != 0) {
    $extras = 'style="border:0px; padding:0px; margin:0px; vertical-align:middle;"';
    if ($size == "L") { // Use == for comparison
        $star_half_icon = "star_half.gif";
        $star_none_icon = "star_bg.gif";
        $star_full_icon = "star.gif";
    }

    if ($size == "SM") { // Use == for comparison
        $star_half_icon = "star_sm_half.gif";
        $star_none_icon = "star_sm_bg.gif";
        $star_full_icon = "star_sm.gif";  
    }

    if(fmod($rating, 1) !== 0.00){
        $rating_half = true;
    } else {
        $rating_half = false;
    }
    
    $star_rating_draw = ''; // Initialize the variable
    
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_full_icon . '">';
        } elseif ($rating_half && $rating > ($i - 1) && $rating < $i) {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_half_icon . '">';
        } else {
            $star_rating_draw .= '<img '. $extras. ' src="img/' . $star_none_icon . '">';
        }
    }

    echo $star_rating_draw;
    if($show_count == 1 && $average != 0) { echo '&nbsp;<span style="color:#666666; font-size:smaller; ">('. htmlspecialchars(getRatingCount($vid)) .' ratings)</span>';}
    }
}

function gmailStyleTime($datetime) {

if ((date('d', strtotime($datetime)) != date('d')) && (date('Y', strtotime($datetime)) == date('Y'))) { 
  return date("M d", strtotime($datetime));
}
else if(date('Y', strtotime($datetime)) != date('Y'))
{
  return date("m-d-y h:i", strtotime($datetime. ' - 18 years'));
}
else 
{
  return date("h:i a", strtotime($datetime));
}}

function dec2hex($int) {
  $hex = dechex($int);
  if (strlen($hex)%2 != 0) {
    $hex = str_pad($hex, strlen($hex) + 1, '0', STR_PAD_LEFT);
  }
  return $hex;
}

function session_error_index($message, $type = "error", $location = "index.php") {
	if ($type == "success") {
	$picklestart = hex2bin("80027d710128550c6572726f725f6669656c64737102635f5f6275696c74696e5f5f0a7365740a71035d8552710455066572726f727371055d710655086d6573736167657371075d710855");
	$pickleend = hex2bin("710961752e");
	} elseif ($type == "error") {
	$picklestart = hex2bin("80027d710128550c6572726f725f6669656c64737102635f5f6275696c74696e5f5f0a7365740a71035d8552710455066572726f727371055d710655");
	$pickleend = hex2bin("71076155086d6573736167657371085d7109752e");
	}
	$picklenum = hex2bin(dec2hex(strlen($message)));
    $error_base64 = strtr(base64_encode($picklestart.$picklenum.$message.$pickleend), '+/', '-_');
    redirect($location."?&session=".$error_base64);
}

function htmlspecialsomechars($input, $allowedTags = array()) {
    $allowedTags = array_map('strtolower', $allowedTags);
    $output = preg_replace_callback('/<[^>]*>.*?<\/\s*([^\s>]+)\s*>|<[^>]*>/', function($match) use ($allowedTags) {
        $tag = $match[0];
        preg_match('/<\s*([^\s>\/]+)/', $tag, $tagMatch);
        $tagName = strtolower($tagMatch[1]);
        if (in_array($tagName, $allowedTags) || $tag[strlen($tag) - 2] == '/') {
            return $tag;
        } else {
            return htmlspecialchars($tag);
        }
    }, $input);

    return $output;
}

function getReplies($reply_to = 0, $video_id = 0, $uploaderuid = "blah", $level = 0) {
    $result = $GLOBALS['conn']->prepare("SELECT * FROM comments LEFT JOIN users ON users.uid = comments.uid WHERE is_reply = ? AND reply_to = ? AND users.termination = 0 ORDER BY post_date ASC");
	$result->execute([1, $reply_to]);
    if (!$result || !$result->rowCount()) {
        return;
    }

    while($reply = $result->fetch(PDO::FETCH_ASSOC)) {
		/*$reply_videos = $GLOBALS['conn']->prepare("SELECT vid FROM videos WHERE uid = ? AND converted = 1");
		$reply_videos->execute([$reply['uid']]);
					
		$reply_favorites = $GLOBALS['conn']->prepare("SELECT fid FROM favorites WHERE uid = ?");
		$reply_favorites->execute([$reply['uid']]);

                $reply_friends = $GLOBALS['conn']->prepare("SELECT relationship FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
		$reply_friends->execute([$reply['uid'], $reply['uid']]);*/
        $marginnum = 20;
        for ($i = 0; $i < $level; $i++) {
            $marginnum = $marginnum + 20;
        }
		?>

			<a name="<? echo htmlspecialchars($reply['cid']); ?>"/>
					<table class="childrenSection" id="comment_<? echo htmlspecialchars($reply['cid']); ?>" width="100%" style="margin-left: <? echo htmlspecialchars($marginnum); ?>px">
					<tr valign="top"><? if ($reply['removed'] == 1) { echo '<td>----- Reply deleted by user -----'; } else { ?>
<? if($reply['vid'] != NULL) { ?>
						<td width="60">
							<a href="/watch.php?v=<? echo htmlspecialchars($reply['vid']); ?>"><img src="/get_still.php?video_id=<? echo htmlspecialchars($reply['vid']); ?>" class="commentsThumb" width="60" height="45"></a>
							<div class="commentSpecifics">
								<a href="/watch.php?v=<? echo htmlspecialchars($reply['vid']); ?>">Related Video</a>
							</div>
						</td>

<? } ?>
						<td>
		<?= nl2br(htmlspecialsomechars($reply['body'], ['b', 'i', 'big'])) ?>
			<div class="userStats">
				<? if($reply['termination'] != 1) {?><a href="/profile?user=<? echo htmlspecialchars($reply['username']); ?>"><? echo htmlspecialchars($reply['username']); ?></a> // <a href="/profile_videos.php?user=<? echo htmlspecialchars($reply['username']); ?>">Videos</a> (<?php echo $reply['pub_vids']; ?>) | <a href="/profile_favorites.php?user=<? echo htmlspecialchars($reply['username']); ?>">Favorites</a> (<?php echo $reply['fav_count']; ?>) | <a href="/profile_friends.php?user=<? echo htmlspecialchars($reply['username']); ?>">Friends</a> (<?php echo $reply['friends_count']; ?>)<? } ?>
				 - (<?= timeAgo($reply['post_date']); ?>)
			</div>
				
	<div class="userStats" id="container_comment_form_id_<? echo htmlspecialchars($reply['cid']); ?>" style="display: none"></div>
    <div class="userStats" id="reply_comment_form_id_<? echo htmlspecialchars($reply['cid']); ?>">
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<? echo htmlspecialchars($reply['cid']); ?>', 'current_post');">Reply to this</a>) &nbsp; 
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<? echo htmlspecialchars($reply['cid']); ?>', 'parent_post');">Reply to parent</a>) &nbsp; 
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<? echo htmlspecialchars($reply['cid']); ?>', 'main_thread');">Create new thread</a>) &nbsp; 
				  <?php // $reply['uid'] == $GLOBALS['session']['uid']
				  if ($uploaderuid == $GLOBALS['session']['uid'] || $GLOBALS['session']['staff'] == 1 && $reply['uid'] != NULL) { ?><input type="button" name="remove_comment" id="remove_button_<?php echo htmlspecialchars($reply['cid']); ?>" value="Remove Comment" onclick="removeComment(document.getElementById('remove_comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>'));"> &nbsp; 
	<form name="remove_comment_form" id="remove_comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>">
		<input type="hidden" name="deleter_user_id" value="<?php echo htmlspecialchars($GLOBALS['session']['uid']); ?>">
		<input type="hidden" name="remove_comment" value="">
			<input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($reply['cid']); ?>">
		<input type="hidden" name="comment_type" value="V">
	</form>
	<? } ?>
<? } ?>

	</div>

	<div id="div_comment_form_id_<? echo htmlspecialchars($reply['cid']); ?>" style="display: none">
	<div style="padding-bottom: 5px; font-weight: bold; color: #444; display: none;">Comment on this video:</div>
	<form name="comment_form" id="comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>" method="post" action="#">
		<input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video_id); ?>">
		<input type="hidden" name="add_comment" value="">
		<input type="hidden" name="form_id" value="comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>">
			<input type="hidden" name="comment_parent_id" value="<?php echo htmlspecialchars($reply_to); ?>">
			<input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($reply['cid']); ?>">
		<input type="hidden" name="reply_parent_id" value="">
		<textarea tabindex="2" name="comment" cols="55" rows="3"></textarea>
		<br>
		<input type="button" name="comment_button" id="comment_button_comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>" value="Post Comment" onclick="postThreadedComment('comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>');">
		<input type="button" name="discard_comment" style="" id="discard_button_comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>" value="Discard" onclick="toggleVisibility('div_comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>',false); toggleVisibility('reply_comment_form_id_<?php echo htmlspecialchars($reply['cid']); ?>', true);">
	</form>
	</div>

							</td>
					</tr>
				</table>
<?
        getReplies($reply['cid'], $video_id, $uploaderuid, $level + 1);
    }
}

function handle_fatal_error() {
    $error = error_get_last();
    if (is_array($error)) {
        $errorCode = $error['type'] ?? 0;
        $errorMsg = $error['message'] ?? '';
        $file = $error['file'] ?? '';
        $line = $error['line'] ?? null;

        if ($errorCode == E_ERROR) {
            handle_error();
        }
    }
}
function handle_error() {
    header("Location: /error.html");
}
set_error_handler("handle_error", E_ERROR);
  register_shutdown_function('handle_fatal_error');
?>