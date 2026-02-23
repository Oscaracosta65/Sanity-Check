<?php
/**
 * @version    CVS: 1.0.3
 * @package    Com_Lotterydb
 * @author     FULLSTACK DEV <admin@fullstackdev.us>
 * @copyright  2022 FULLSTACK DEV
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
/** *********************************** MAIN DB IMPORT/UDPATE CONTROLLER ************************************** **/

// No direct access bigsky ky
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$bDir = Uri::root();
$ftpDir = 'ftpUpload';
$fileName = 'lottery2.json';
//$filePath = $bDir.$ftpDir.'/'.$fileName;
$filePath = 'http://www.lotterynumbersxml.com/lotterydata/oscar@dt2k.com/gahuse3y4/lottery.json';

// Prepare DB connection for later use (no behavior change, just ready early)
$db = Factory::getDbo();

// Build a simple HTTP context with timeout to avoid hanging if the feed is slow
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
    ],
]);

// Fetch remote JSON safely
$JSON = @file_get_contents($filePath, false, $context);

if ($JSON === false) {
    // Fail fast if the feed cannot be reached
    echo 'Error: Unable to fetch lottery feed from remote source.' . "<br />\n";
    return;
}

$myData = json_decode($JSON, true);

// Ensure we have a valid array before processing
if (!is_array($myData) || empty($myData)) {
    echo 'Error: Lottery feed returned invalid or empty JSON.' . "<br />\n";
    return;
}

if(!empty($myData)){

    
    foreach($myData as $md){
    $myResults = $md['results'];
    foreach($myResults as $mr){
        
        $country = 'USA';
        $stateprov_name = $mr['stateprov_name'];
        $stateprov_id = $mr['stateprov_id'];
        $game_id = $mr['game_id'];
        $gId = $mr['game_id'];
        $game_name = $mr['game_name'];
        $draw_date = date('Y-m-d',strtotime($mr['draw_date']));
        $draw_results = $mr['draw_results'];
        $next_draw_date = date('Y-m-d',strtotime($mr['next_draw_date']));
        $next_jackpot = $mr['next_jackpot'];

        // Initialize position variables to avoid stale values if a parsing rule does not run
        $pPosOne = $pPosTwo = $pPosThree = $pPosFour = $pPosFive = $pPosSix = null;
        $pPosSeventh = $pPosEighth = $pPosNineth = $pPosTenth = $pPosEleventh = $pPosTwelveth = null;
        $pPosThirtheenth = $pPosFourteenth = $pPosFifteenth = $pPosSixteenth = $pPosSeventeenth = $pPosEighteenth = null;
        $pPosNineteenth = $pPosTwentieth = $pPosTwenty_first = $pPosTwenty_second = null;
        $pPostSeven = null;

        // Basic escaping for feed values used in SQL to guard against broken queries
        // (Only draw_results and next_jackpot are escaped here to avoid impacting older rows)
        $draw_results = $db->escape($draw_results);
        $next_jackpot = $db->escape($next_jackpot);
        
        /** BREAK-DOWN POWERBALL RESULTS **/
        if($game_name === 'Powerball'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dPp = $rawResults[2];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Powerball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Power Play:","",$dPp);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
        /** BREAK-DOWN POWERBALL DOUBLE PLAY RESULTS **/
        if($game_name === 'Powerball Double Play'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Powerball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }

         /** BREAK-DOWN POWERBALL Puerto Rico RESULTS **/
        if($game_name === 'Powerball' && $stateprov_name === 'Puerto Rico'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dPp = $rawResults[2];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Powerball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Power Play:","",$dPp);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
          /** BREAK-DOWN Health Lottery UK National RESULTS **/
        if($game_name === 'Health Lottery' && $stateprov_name === 'UK National'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dPp = $rawResults[2];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Bonus:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
            
        }
          /** BREAK-DOWN Revancha, Loto Plus Puerto Rico RESULTS **/
        if(($game_name === 'Revancha X2' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Loto Plus' && $stateprov_name === 'Puerto Rico')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dPp = $rawResults[2];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Bonus:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
            
        }

         /** BREAK-DOWN  Thunderball Lottery UK National RESULTS **/
        if($game_name === 'Thunderball' && $stateprov_name === 'UK National'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dPp = $rawResults[2];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Thunderball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
            
        }
        
         /** BREAK-DOWN NEW JERSEY CASH 5 RESULTS **/
        if($game_name === 'Cash 5' && $stateprov_id === 'NJ'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Xtra:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN SuperLotto Plus RESULTS **/
        if($game_name === 'SuperLotto Plus'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Mega Ball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN DELAWARE, IOWA, IDAHO, Maine, Minnesota, Montana, North Dakota, New Mexico, Oklahoma, South Dakota, Tennessee, West-Virginia Lotto America RESULTS **/
        if(($game_name === 'Lotto America' && $stateprov_id === 'DE') || ($game_name === 'Lotto America' && $stateprov_id === 'IA') || ($game_name === 'Lotto America' && $stateprov_id === 'ID') || ($game_name === 'Lotto America' && $stateprov_id === 'ME') || ($game_name === 'Lotto America' && $stateprov_id === 'MN') || ($game_name === 'Lotto America' && $stateprov_id === 'MT') || ($game_name === 'Lotto America' && $stateprov_id === 'ND') || ($game_name === 'Lotto America' && $stateprov_id === 'NM') || ($game_name === 'Lotto America' && $stateprov_id === 'OK') || ($game_name === 'Lotto America' && $stateprov_id === 'SD') || ($game_name === 'Lotto America' && $stateprov_id === 'TN') || ($game_name === 'Lotto America' && $stateprov_id === 'WV') || ($game_name === 'Lotto America' && $stateprov_id === 'KS')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Star Ball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN Super Cash RESULTS **/
        if($game_name === 'Super Cash' && $stateprov_id === 'KS'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Cash Ball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN Cash Ball RESULTS **/
        if ($game_name === 'Cash Ball' && $stateprov_id === 'KY'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = str_replace("Cash Ball:","",$dPb);
            $pPosFive = str_replace(" ","",$pPosFive);
        }
        /** BREAK-DOWN Big Sky Bonus RESULTS **/
        if(($game_name === 'Big Sky Bonus' && $stateprov_id === 'MT') || ($game_name === 'Texas Two Step' && $stateprov_id === 'TX')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = str_replace("Bonus:","",$dPb);
            $pPosFive = str_replace(" ","",$pPosFive);
        }
        /** BREAK-DOWN MEGA MILLIONS RESULTS **/
        if($game_name === 'Mega Millions'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dPp = $rawResults[2];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Mega Ball:","",$dPb);
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Megaplier:","",$dPp);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
 
          /** BREAK-DOWN EuroMillions, UK , Ireland IE RESULTS **/
        if($game_name === 'EuroMillions' && $game_id === '801'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            
                $preSixth = str_replace("Lucky Stars:","",$dPb);
                $preSixth = str_replace(" ","",$preSixth);
                $preSixth = explode("-", $preSixth);
                
            $pPosSix = $preSixth[0];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = $preSixth[1];
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
        

        
                /** BREAK-DOWN Millionaire for Life RESULTS **/
        if($game_name === 'Cash4Life'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Cash Ball:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        
        
        /** BREAK-DOWN Lucky For Life RESULTS **/
        if($game_name === 'Lucky For Life'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Lucky Ball:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN Millionaire For Life RESULTS **/
        if($game_name === 'Millionaire For Life'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);

            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Bonus Ball:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN Lotto, Hoosier Lotto RESULTS **/
        if(($game_name === 'Lotto' && $stateprov_name != 'Illinois' && $stateprov_name != 'New York') || ($game_name === 'Hoosier Lotto' && $stateprov_name === 'Indiana')){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN Lotto Double Play RESULTS **/
        if($game_name === 'Double Play' && $stateprov_name != 'Illinois' && $stateprov_name != 'New York'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        
        /** BREAK-DOWN Lotto HotPicks RESULTS **/
        if(($game_name === 'Lotto HotPicks' && $stateprov_name === 'UK National')){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
        }

        /** BREAK-DOWN Illinois Lotto RESULTS **/
        if($game_name === 'Lotto' && $stateprov_name === 'Illinois'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Extra Shot:","",$dCb);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
 
 
        /** BREAK-DOWN Bank a Million Lotto RESULTS **/
        if(($game_name === 'Bank a Million' && $stateprov_name === 'Virginia') || ($game_name === 'LOTTO' && $stateprov_name === 'Arkansas')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Bonus:","",$dCb);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
 
 
       /** BREAK-DOWN UK Lotto RESULTS, Teatime 49s UK, Lunchtime 49s UK, Lotto 6/49 Ontario  **/
        if(($game_name === 'LOTTO' && $stateprov_name === 'UK National') || ($game_name === 'IrishLotto' && $stateprov_name === 'Ireland') || ($game_name === 'IrishLotto' && $stateprov_name === 'Ireland') || ($game_name === 'Lotto Plus 1' && $stateprov_name === 'Ireland') || ($game_name === 'Lotto Plus 2' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million 9PM' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million Plus 9PM' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million 2PM' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million Plus 2PM' && $stateprov_name === 'Ireland') || ($game_name === 'Teatime 49s' && $stateprov_name === 'UK National') || ($game_name === 'Lunchtime 49s' && $stateprov_name === 'UK National') || ($game_name === 'Lotto 6/49' && $stateprov_name === 'Ontario')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Bonus:","",$dCb);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
 
        /** BREAK-DOWN Wild Money RESULTS **/
        if($game_name === 'Wild Money' && $stateprov_name === 'Rhode Island'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Extra:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN Palmetto Cash 5 RESULTS **/
        if($game_name === 'Palmetto Cash 5' && $stateprov_name === 'South Carolina'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Power-Up:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN New York Lotto RESULTS **/
        if(($game_name === 'Lotto' && $stateprov_name === 'New York')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPostSeven = str_replace("Bonus:","",$dCb);
            $pPostSeven = str_replace(" ","",$pPostSeven);
        }
        /** BREAK-DOWN Bonus Match 5, Tennessee Cash RESULTS **/
        if(($game_name === 'Bonus Match 5' && $stateprov_name === 'Maryland') || ($game_name === 'Tennessee Cash' && $stateprov_name === 'Tennessee')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Bonus:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** BREAK-DOWN TX Two Step RESULTS **/
    /**    if($game_name === 'Texas Two Step' && $stateprov_name === 'Texas'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = str_replace("Bonus:","",$dCb);
            $pPosFive = str_replace(" ","",$pPosFive);
        } **/
        /** BREAK-DOWN Bank a Million RESULTS **/
      /** if(($game_name === 'Bank a Million' && $stateprov_name === 'Virginia')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = str_replace("Bonus:","",$dCb);
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
        } **/
        
        
        /** BREAK-DOWN Megabucks Plus RESULTS **/
        if(($game_name === 'Megabucks Plus' && $stateprov_name === 'Maine') || ($game_name === 'Megabucks Plus' && $stateprov_name === 'New Hampshire') || ($game_name === 'Megabucks Plus' && $stateprov_name === 'Vermont')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dCb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = str_replace("Megaball:","",$dCb);
            $pPosSix = str_replace(" ","",$pPosSix);
        }

             /** (Millionaire Raffle) Millionaire Raffle UK Nastional RESULTS **/
        if(($game_name === 'Millionaire Raffle') || ($game_name === 'Pega 2 Noche Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Multiplicador' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 3 Noche Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 4 Noche Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 2 Day Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 3 Day Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 4 Day Wild' && $stateprov_name === 'Puerto Rico') || $game_name === 'Cash Pop Early Bird' || $game_name === 'Cash Pop Late Morning' || $game_name === 'Cash Pop Matinee' || $game_name === 'Cash Pop Prime Time' || $game_name === 'Cash Pop Night Owl' || $game_name === 'Pick 3 Evening Wild' || $game_name === 'Pick 3 Midday Wild' || $game_name === 'Pick 4 Evening Wild' || $game_name === 'Pick 4 Midday Wild'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
        }


     /** Oscar T Single Fireball or Bonus Ball  **/
            
        if($gId === 'FLEF' || $gId === 'FLBF' || $gId === 'FLFF' || $gId === 'FLCF' || $gId === 'FLM' || $gId === 'FLDF' || $gId === 'FLGF' || $gId === 'FLO' || $gId === 'FLP' || $gId === 'FLAF' || $gId === 'FLHF' || $gId === 'PABW' || $gId === 'PADW' || $gId === 'PAEW' || $gId === 'PAGW' || $gId === 'PAFW' || $gId === 'PAHW' || $gId === 'TXLF' || $gId === 'TXBF' || $gId === 'TXMF' || $gId === 'TXDF' || $gId === 'CTCW' || $gId === 'CTAW' || $gId === 'CTDW' || $gId === 'CTBW' || $gId === 'CTB' || $gId === 'FLA' || $gId === 'FLC' || $gId === 'ILG' || $gId === 'ILH' || $gId === '120' || $gId === '121' || $gId === 'INA' || $gId === 'INAF' || $gId === 'INB' || $gId === 'INBF' || $gId === 'MSA' || $gId === 'MSAF' || $gId === 'MSB' || $gId === 'MSBF' || $gId === 'NCA' || $gId === 'NCAF' || $gId === 'NCB' || $gId === 'NCBF' || $gId === 'NJA' || $gId === 'NJAF' || $gId === 'NJB' || $gId === 'NJBF' || $gId === 'PAA' || $gId === 'PAAW' || $gId === 'PAB' || $gId === 'PABW' || $gId === 'SCA' || $gId === 'SCAF' || $gId === 'SCB' || $gId === 'SCBF' || $gId === 'TNA' || $gId === 'TNAW' || $gId === 'TNC' || $gId === 'TNCW' || $gId === 'TNE' || $gId === 'TNEW' || $gId === 'TXA' || $gId === 'TXAF' || $gId === 'TXC' || $gId === 'TXCF' || $gId === 'TXJ' || $gId === 'TXJF' || $gId === 'TXK' || $gId === 'TXKF' || $gId === 'VAA' || $gId === 'VAAF' || $gId === 'VAB' || $gId === 'VABF' || $gId === '136' || $gId === 'MDM' || $gId === 'MDN' || $gId === 'MDO' || $gId === 'MDP' || $gId === 'MES' || $gId === 'MEB' || $gId === 'VAFF' || $gId === 'WIZ'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
        }


/** Oscar T Modified added Florida and Pennsylvania Pick 2 **/ 
        /** (STRAIGHT 2) BREAK-DOWN DC 2 Midday, DC 2 Evening RESULTS **/
        if($gId === 'FLE' || $gId === 'FLF' || ($game_name === 'Pick 2 Day' && $gId === 'PAG') || ($game_name === 'Pick 2 Evening' && $gId === 'PAH' || $gId === 'DCG' || $gId === 'PRA' || $gId === 'PRD' || $gId === 'DCH')){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);



        }
        /** (STRAIGHT 3) BREAK-DOWN Cash 3 Midday, Cash 3 Evening, Play3 Day, Play3 Night, DC 3 Midday, DC 3 Evening, Play 3 Day, Play 3 Night, Cash 3 Night, Evening 3 Double, Cash 3 Morning, Cash 3 Midday, Cash 3 Evening MyDay, Daily Game, Daily 3 RESULTS **/
        if($game_name === 'Cash 3 Midday' || $game_name === 'Cash 3 Evening' || $game_name === 'Pega 3 Day' || $game_name === 'Pega 3 Noche' || $game_name === 'Play3 Day' || $game_name === 'Play3 Night' || $gId === 'DCA' || $gId === 'DCB' || $game_name === 'Play 3 Day' || $game_name === 'Play 3 Night' || $game_name === 'Cash 3 Night' || $game_name === 'Evening 3 Double' || $game_name === 'Pick 3' || $game_name === 'MyDay' || $game_name === 'Cash 3 Morning' || $game_name === 'Daily Game' || $game_name === 'Daily 3' || $game_name === 'Pick 3 Day' || $game_name === 'Pick 3 Midday' || $game_name === 'Pick 3 Night' || $game_name === 'Pick 3 Evening' || $game_name === 'Pick 3 Morning' || $game_name === 'Daily 3 Evening' || ($gId === 'TXJ' && $game_name === 'Pick 3 Morning') || ($game_name === 'Daily 3 Midday' && $stateprov_name === 'California') || $gId === 'DCI'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
        }
        /** (STRAIGHT 4) BREAK-DOWN Cash 4 Midday, Cash 4 Evening, Daily 4, Play4 Day, Play4 Night, DC 4 Midday, DC 4 Evening, Play 4 Day, Play 4 Night, Cash 4 Night, Daily 4 Midday, Daily 4 Evening, 2 By 2, Numbers Midday, Numbers Evening, Win 4 Midday, Win 4 Evening, Win for Life, Cash 4 Morning, Cash 4 Midday, Cash 4 Evening, Match 4 RESULTS **/
        if ($gId === 'NYC' || $gId === 'NYD' || $gId === 'ARD' || $gId === 'ARC' || $gId === 'CAB' || $gId === 'GAC' || $gId === 'GAD' || $gId === 'GAH' || $gId === 'MIC' || $gId === 'MID' || $gId === '116' || $gId === '110' || $gId === 'DEC' || $gId === 'DED' || $gId === 'IAC' || $gId === 'IAD' || $gId === 'IDC' || $gId === 'IDD' || $gId === 'KYC' || $gId === 'KYD' || $gId === 'LAB' || $gId === 'MAA' || $gId === 'MAC' || $gId === 'MDC' || $gId === 'MDD' || $gId === 'MOC' || $gId === 'MOD' || $gId === 'NME' || $gId === 'NMF' || $gId === 'OHC' || $gId === 'OHD' || $gId === 'ORD' || $gId === 'ORE' || $gId === 'ORF' || $gId === 'ORG' || $gId === 'RIC' || $gId === 'RID' || $gId === 'WIC' || $gId === 'WID' || $gId === 'WVC' || $game_name === 'Cash 4 Midday' || $game_name === 'Cash 4 Evening' || $game_name === 'Daily 4' || $game_name === 'Play4 Day' || $game_name === 'Play4 Night' || $game_name === 'Play 4 Day' || $game_name === 'Play 4 Night' || $game_name === 'Cash 4 Night' || $game_name === 'Daily 4 Midday' || $game_name === 'Daily 4 Evening' || $game_name === '2 By 2' || $game_name === 'Numbers Midday' || $game_name === 'Numbers Evening' || $game_name === 'Win 4 Midday' || $game_name === 'Win 4 Evening' || $game_name === 'Win for Life' || $game_name === 'Cash 4 Morning' || $game_name === 'Cash 4 Midday' || $game_name === 'Cash 4 Evening' || $game_name === 'Match 4' || $game_id === 'TXL' || $game_id === 'TXB' || $game_id === 'TXM' || $game_id === 'TXD' || $game_name === 'Pick 4' || $game_name === 'Pick 4 Day' || $game_name === 'Pick 4 Evening' || $game_name === 'Pick 4 Midday' || $game_name === 'Pega 4 Noche' || $game_name === 'Pega 4 Day' || $game_name === 'Pick 4 Night' || $gId === 'DCD' || $gId === 'DCC' || $gId === 'DCJ'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
        }
        /** (STRAIGHT 5) BREAK-DOWN Fantasy 5, Natural State Jackpot, Cash 5, DC 5 Midday, DC 5 Evening, Georgia FIVE Midday, Georgia FIVE Evening,match Idaho Cash, 5 Star Draw, Weekly Grand, LuckyDay Lotto Midday, LuckyDay Lotto Evening, Easy 5, MassCash, Gimme 5, World Poker Tour, Cash Five , Poker Lotto, Gopher 5, NORTH5, Show Me Cash, Montana Cash, Roadrunner Cash, Take 5 Midday, Take 5 Evening, Treasure Hunt, Dakota Cash, Hit 5, Badger 5, Cowboy Draw RESULTS lac **/
        if(($game_name === 'Fantasy 5' || $game_name === 'Fantasy 5 Midday' || $game_name === 'Fantasy 5 Evening' || $game_name === 'Natural State Jackpot' || ($game_name === 'Cash 5' && $stateprov_id != 'NJ') || $game_name === 'Georgia FIVE Midday' || $game_name === 'Georgia FIVE Evening' || $game_name === 'Idaho Cash' || $game_name === '5 Star Draw' || $game_name === 'Weekly Grand' || $game_name === 'LuckyDay Lotto Midday' || $game_name === 'LuckyDay Lotto Evening' || $game_name === 'Easy 5' || $game_name === 'MassCash' || $game_name === 'Gimme 5' || $game_name === 'World Poker Tour' || $game_name === 'Poker Lotto' || $game_name === 'Gopher 5' || $game_name === 'NORTH5' || $game_name === 'Show Me Cash' || $game_name === 'Montana Cash' || $game_name === 'Roadrunner Cash' || $game_name === 'Take 5 Midday' || $game_name === 'Take 5 Evening' || $game_name === 'Treasure Hunt' || $game_name === 'Dakota Cash' || $game_name === 'Hit 5' || $game_name === 'Pick 5' || $game_name === 'Badger 5' || $game_name === 'Cowboy Draw' || $game_name === 'Kentucky 5' || $game_name === 'Fantasy 5 Evening' || $game_name === 'Fantasy 5 Midday' || $game_name === 'Daily Tennessee Jackpot' || $game_name === 'Pick 5 Evening' || $game_name === 'Cash Five' || $game_name === 'Pick 5 Day' || $game_name === 'Pick 5 Midday' || $game_name === 'Pick 5 Night' ) || $gId === 'DCF' || $gId === 'DCE' || $gId === 'LAC' || $gId === 'MIN' || $gId === 'MS5'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
        }
        /** (STRAIGHT 6) BREAK-DOWN Jackpot Triple, Triple Twist Play, Lotto Plus, Multi-Win Lotto, Jumbo Bucks Lotto, Megabucks Doubler, MultiMatch, Classic Lotto 47,  Classic Lotto, Rolling Cash 5, Megabucks, Match 6 Lotto, pick 6 lotto, Super Cash (Wisconsin), Cash 25 RESULTS **/
        if($game_name === 'Jackpot Triple Play' || $game_name === 'Triple Twist' || $game_name === 'Lotto Plus' || $game_name === 'Multi-Win Lotto' || $game_name === 'Megabucks Doubler' || $game_name === 'MultiMatch' || $game_name === 'Classic Lotto 47' || $game_name === 'Classic Lotto' || $game_name === 'Rolling Cash 5' || $game_name === 'Megabucks' || $game_name === '' || $game_name === 'Match 6 Lotto' ||  $game_name === 'Cash 25' ||  $game_name === 'The Pick' || $game_name === 'Lotto Texas' || ($game_name === 'Pick 6 Lotto' && $stateprov_name === 'New Jersey') || $gId === 'MI6' || ($game_name === 'Super Cash' && $stateprov_name === 'Wisconsin')){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
        }
        /** (STRAIGHT 8) BREAK-DOWN Lucky Lines RESULTS **/
        if($game_name === 'Lucky Lines'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = $rawResults[6];
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
            $pPosEighth = $rawResults[7];
            $pPosEighth = str_replace(" ","",$pPosEighth);
        }
        /** (STRAIGHT 11) BREAK-DOWN All or Nothing Midday, All or Nothing Evening RESULTS **/
        if($game_id === 'WI8' || $game_id === 'WI7'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = $rawResults[6];
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
            $pPosEighth = $rawResults[7];
            $pPosEighth = str_replace(" ","",$pPosEighth);
            $pPosNineth = $rawResults[8];
            $pPosNineth = str_replace(" ","",$pPosNineth);
            $pPosTenth = $rawResults[9];
            $pPosTenth = str_replace(" ","",$pPosTenth);
            $pPosEleventh = $rawResults[10];
            $pPosEleventh = str_replace(" ","",$pPosEleventh);
        }
        /** (STRAIGHT 12) BREAK-DOWN TX All or Nothing RESULTS **/
        if($game_id === 'TXF' || $game_id === 'TXG' || $game_id === 'TXH' || $game_id === 'TXI'){
            $rawResults = explode("-", $draw_results);
            
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = $rawResults[6];
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
            $pPosEighth = $rawResults[7];
            $pPosEighth = str_replace(" ","",$pPosEighth);
            $pPosNineth = $rawResults[8];
            $pPosNineth = str_replace(" ","",$pPosNineth);
            $pPosTenth = $rawResults[9];
            $pPosTenth = str_replace(" ","",$pPosTenth);
            $pPosEleventh = $rawResults[10];
            $pPosEleventh = str_replace(" ","",$pPosEleventh);
            $pPosTwelveth = $rawResults[11];
            $pPosTwelveth = str_replace(" ","",$pPosTwelveth);
        }
 
                /** Oscar Added Pick 10 NY 20 numbers Results **/
        if(($game_name === 'Pick 10' && $stateprov_name === 'New York') || $game_id === 'WA4'){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = $dNumbers[6];
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
            $pPosEighth = $dNumbers[7];
            $pPosEighth = str_replace(" ","",$pPosEighth);
            $pPosNineth = $dNumbers[8];
            $pPosNineth = str_replace(" ","",$pPosNineth);
            $pPosTenth = $dNumbers[9];
            $pPosTenth = str_replace(" ","",$pPosTenth);
            $pPosEleventh = $dNumbers[10];
            $pPosEleventh = str_replace(" ","",$pPosEleventh);
            $pPosTwelveth = $dNumbers[11];
            $pPosTwelveth = str_replace(" ","",$pPosTwelveth);
            $pPosThirtheenth = $dNumbers[12];
            $pPosThirtheenth = str_replace(" ","",$pPosThirtheenth);
            $pPosFourteenth = $dNumbers[13];
            $pPosFourteenth = str_replace(" ","",$pPosFourteenth);
            $pPosFifteenth = $dNumbers[14];
            $pPosFifteenth = str_replace(" ","",$pPosFifteenth);
            $pPosSixteenth = $dNumbers[15];
            $pPosSixteenth = str_replace(" ","",$pPosSixteenth);
            $pPosSeventeenth = $dNumbers[16];
            $pPosSeventeenth = str_replace(" ","",$pPosSeventeenth);
            $pPosEighteenth = $dNumbers[17];
            $pPosEighteenth = str_replace(" ","",$pPosEighteenth);
            $pPosNineteenth = $dNumbers[18];
            $pPosNineteenth = str_replace(" ","",$pPosNineteenth);
            $pPosTwentieth = $dNumbers[19];
            $pPosTwentieth = str_replace(" ","",$pPosTwentieth);
         }
       
        
                       /** Michigan KENO (MI3) RESULTS **/
            if($game_name === 'Keno' && $stateprov_name === 'Michigan'){
            $rawResults = explode("-", $draw_results);
            /** $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1]; **/
            /** $dNumbers = explode("-",$rdNumbers); **/ 
                        
            $pPosOne = $rawResults[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $rawResults[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $rawResults[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $rawResults[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $rawResults[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $rawResults[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = $rawResults[6];
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
            $pPosEighth = $rawResults[7];
            $pPosEighth = str_replace(" ","",$pPosEighth);
            $pPosNineth = $rawResults[8];
            $pPosNineth = str_replace(" ","",$pPosNineth);
            $pPosTenth = $rawResults[9];
            $pPosTenth = str_replace(" ","",$pPosTenth);
            $pPosEleventh = $rawResults[10];
            $pPosEleventh = str_replace(" ","",$pPosEleventh);
            $pPosTwelveth = $rawResults[11];
            $pPosTwelveth = str_replace(" ","",$pPosTwelveth);
            $pPosThirtheenth = $rawResults[12];
            $pPosThirtheenth = str_replace(" ","",$pPosThirtheenth);
            $pPosFourteenth = $rawResults[13];
            $pPosFourteenth = str_replace(" ","",$pPosFourteenth);
            $pPosFifteenth = $rawResults[14];
            $pPosFifteenth = str_replace(" ","",$pPosFifteenth);
            $pPosSixteenth = $rawResults[15];
            $pPosSixteenth = str_replace(" ","",$pPosSixteenth);
            $pPosSeventeenth = $rawResults[16];
            $pPosSeventeenth = str_replace(" ","",$pPosSeventeenth);
            $pPosEighteenth = $rawResults[17];
            $pPosEighteenth = str_replace(" ","",$pPosEighteenth);
            $pPosNineteenth = $rawResults[18];
            $pPosNineteenth = str_replace(" ","",$pPosNineteenth);
            $pPosTwentieth = $rawResults[19];
            $pPosTwentieth = str_replace(" ","",$pPosTwentieth);
            $pPosTwenty_first = $rawResults[20];
            $pPosTwenty_first = str_replace(" ","",$pPosTwenty_first);
            $pPosTwenty_second = $rawResults[21];
            $pPosTwenty_second = str_replace(" ","",$pPosTwenty_second);
        }
            
        
                
        /** Indiana Quick Draw Midday, Quick Draw Evening RESULTS **/
        if(($game_name === 'Quick Draw Midday' && $stateprov_name === 'Indiana') || ($game_name === 'Quick Draw Evening' && $stateprov_name === 'Indiana')){
            $rawResults = explode(",", $draw_results);
            $rdNumbers = $rawResults[0];
            $dPb = $rawResults[1];
            $dNumbers = explode("-",$rdNumbers);
            
            $pPosOne = $dNumbers[0];
            $pPosOne = str_replace(" ","",$pPosOne);
            $pPosTwo = $dNumbers[1];
            $pPosTwo = str_replace(" ","",$pPosTwo);
            $pPosThree = $dNumbers[2];
            $pPosThree = str_replace(" ","",$pPosThree);
            $pPosFour = $dNumbers[3];
            $pPosFour = str_replace(" ","",$pPosFour);
            $pPosFive = $dNumbers[4];
            $pPosFive = str_replace(" ","",$pPosFive);
            $pPosSix = $dNumbers[5];
            $pPosSix = str_replace(" ","",$pPosSix);
            $pPosSeventh = $dNumbers[6];
            $pPosSeventh = str_replace(" ","",$pPosSeventh);
            $pPosEighth = $dNumbers[7];
            $pPosEighth = str_replace(" ","",$pPosEighth);
            $pPosNineth = $dNumbers[8];
            $pPosNineth = str_replace(" ","",$pPosNineth);
            $pPosTenth = $dNumbers[9];
            $pPosTenth = str_replace(" ","",$pPosTenth);
            $pPosEleventh = $dNumbers[10];
            $pPosEleventh = str_replace(" ","",$pPosEleventh);
            $pPosTwelveth = $dNumbers[11];
            $pPosTwelveth = str_replace(" ","",$pPosTwelveth);
            $pPosThirtheenth = $dNumbers[12];
            $pPosThirtheenth = str_replace(" ","",$pPosThirtheenth);
            $pPosFourteenth = $dNumbers[13];
            $pPosFourteenth = str_replace(" ","",$pPosFourteenth);
            $pPosFifteenth = $dNumbers[14];
            $pPosFifteenth = str_replace(" ","",$pPosFifteenth);
            $pPosSixteenth = $dNumbers[15];
            $pPosSixteenth = str_replace(" ","",$pPosSixteenth);
            $pPosSeventeenth = $dNumbers[16];
            $pPosSeventeenth = str_replace(" ","",$pPosSeventeenth);
            $pPosEighteenth = $dNumbers[17];
            $pPosEighteenth = str_replace(" ","",$pPosEighteenth);
            $pPosNineteenth = $dNumbers[18];
            $pPosNineteenth = str_replace(" ","",$pPosNineteenth);
            $pPosTwentieth = $dNumbers[19];
            $pPosTwentieth = str_replace(" ","",$pPosTwentieth);
            
            $pPosTwenty_first = str_replace("BE:","",$dPb);
            $pPosTwenty_first = str_replace(" ","",$pPosTwenty_first);
        }
        
        /** GENERATE DB FIELD NAME **/
        $dbCol = '#__lotterydb_'.strtolower($stateprov_id);
        
        /** FILTER ONLY STATES THAT WE WANT **/
        if($stateprov_id == 'AR' || 
            $stateprov_id == 'AZ' || 
            $stateprov_id == 'CA' || 
            $stateprov_id == 'CO' || 
            $stateprov_id == 'CT' || 
            $stateprov_id == 'DC' || 
            $stateprov_id == 'DE' || 
            $stateprov_id == 'FL' || 
            $stateprov_id == 'GA' || 
            $stateprov_id == 'IA' || 
            $stateprov_id == 'ID' || 
            $stateprov_id == 'IL' || 
            $stateprov_id == 'IN' || 
            $stateprov_id == 'KS' || 
            $stateprov_id == 'KY' || 
            $stateprov_id == 'LA' || 
            $stateprov_id == 'MA' || 
            $stateprov_id == 'MD' || 
            $stateprov_id == 'ME' || 
            $stateprov_id == 'MI' || 
            $stateprov_id == 'MN' || 
            $stateprov_id == 'MO' ||  
            $stateprov_id == 'MS' ||
            $stateprov_id == 'MT' || 
            $stateprov_id == 'NC' || 
            $stateprov_id == 'ND' || 
            $stateprov_id == 'NE' || 
            $stateprov_id == 'NH' || 
            $stateprov_id == 'NJ' || 
            $stateprov_id == 'NM' || 
            $stateprov_id == 'NY' || 
            $stateprov_id == 'OH' || 
            $stateprov_id == 'OK' || 
            $stateprov_id == 'OR' || 
            $stateprov_id == 'PA' || 
            $stateprov_id == 'RI' || 
            $stateprov_id == 'SC' || 
            $stateprov_id == 'SD' || 
            $stateprov_id == 'TN' || 
            $stateprov_id == 'TX' || 
            $stateprov_id == 'VA' || 
            $stateprov_id == 'VI' || 
            $stateprov_id == 'VT' || 
            $stateprov_id == 'WA' || 
            $stateprov_id == 'WI' || 
            $stateprov_id == 'WV' || 
            $stateprov_id == 'WY' || 
            $stateprov_id == 'IE' || 
            $stateprov_id == 'PR' || 
            $stateprov_id == 'ON' || 
            $stateprov_id == 'QC' || 
            $stateprov_id == 'BC' || 
            $stateprov_id == 'WC' || 
            $stateprov_id == 'AC' ||
            $stateprov_id == 'IE' ||
            $stateprov_id == 'UK'
            ){
            
            /** CHECK TO ENSURE DATA ISN'T ALREADY IN THE DB (PREVENT DUPLICATES) **/
            $queryCheck = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName($dbCol))
                ->where($db->quoteName('stateprov_id') . ' = ' . $db->quote($stateprov_id))
                ->where($db->quoteName('game_id') . ' = ' . $db->quote($game_id))
                ->where($db->quoteName('draw_date') . ' = ' . $db->quote($draw_date))
                ->where($db->quoteName('draw_results') . ' = ' . $db->quote($draw_results));
            $db->setQuery($queryCheck);
            
            $posDuplicate = $db->loadResult();
            
            if(empty($posDuplicate)){ /** DATA DOESN'T EXIST **/
                /** INSERT DATA TO DB **/
                /** INSERT POWERBALL DATA **/
                if($game_name === 'Powerball'){
                    // Use Joomla query builder for safer inserting of Powerball rows
                    $query = $db->getQuery(true);

                    $columns = [
                        'country',
                        'stateprov_name',
                        'stateprov_id',
                        'game_id',
                        'game_name',
                        'draw_date',
                        'draw_results',
                        'next_draw_date',
                        'next_jackpot',
                        'first',
                        'second',
                        'third',
                        'fourth',
                        'fifth',
                        'sixth',
                        'seventh',
                    ];

                    $values = [
                        $db->quote($country),
                        $db->quote($stateprov_name),
                        $db->quote($stateprov_id),
                        $db->quote($game_id),
                        $db->quote($game_name),
                        $db->quote($draw_date),
                        $db->quote($draw_results),
                        $db->quote($next_draw_date),
                        $db->quote($next_jackpot),
                        $db->quote($pPosOne),
                        $db->quote($pPosTwo),
                        $db->quote($pPosThree),
                        $db->quote($pPosFour),
                        $db->quote($pPosFive),
                        $db->quote($pPosSix),
                        $db->quote($pPostSeven),
                    ];

                    $query
                        ->insert($db->quoteName($dbCol))
                        ->columns(array_map([$db, 'quoteName'], $columns))
                        ->values(implode(',', $values));

                    $db->setQuery($query);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT DELAWARE, IOWA, IDAHO, MAINE, Minnesota, Montana, North Dakota, New Mexico, Oklahoma, South Dakota, Tennessee, West-Virginia Lotto America & KS Super Cash DATA **/
                }else if(($game_name === 'Lotto America' && $stateprov_id === 'MS' )|| ($game_name === 'Lotto America' && $stateprov_id === 'DE') || ($game_name === 'Lotto America' && $stateprov_id === 'IA') || ($game_name === 'Lotto America' && $stateprov_id === 'ID') || ($game_name === 'Super Cash' && $stateprov_id === 'KS') || ($game_name === 'Lotto America' && $stateprov_id === 'ME') || ($game_name === 'Lotto America' && $stateprov_id === 'MN') || ($game_name === 'Lotto America' && $stateprov_id === 'MT') || ($game_name === 'Lotto America' && $stateprov_id === 'ND') || ($game_name === 'Lotto America' && $stateprov_id === 'NM') || ($game_name === 'Lotto America' && $stateprov_id === 'OK') || ($game_name === 'Lotto America' && $stateprov_id === 'SD') || ($game_name === 'Lotto America' && $stateprov_id === 'TN') || ($game_name === 'Lotto America' && $stateprov_id === 'WV') || ($game_name === 'Lotto America' && $stateprov_id === 'KS')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT NEW JERSEY CASH 5 DATA **/
                }else if($game_name === 'Cash 5' && $stateprov_id === 'NJ'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT POWERBALL DOUBLE PLAY DATA **/
                }else if($game_name === 'Powerball Double Play'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT SuperLotto Plus DATA **/
                }else if($game_name === 'SuperLotto Plus'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT MEGA MILLIONS DATA **/
                }else if($game_name === 'Mega Millions'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`, 
                    `seventh`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPostSeven'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';

               /** INSERT EuroMillions DATA **/
                }else if(($game_name === 'EuroMillions' && $game_id === '801') || ($game_name === 'EuroMillions' && $stateprov_name === 'Ireland')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`, 
                    `seventh`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPostSeven' 
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';


                /** INSERT Cash4Life DATA **/
                }else if($game_name === 'Cash4Life'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT Lucky For Life DATA **/
                }else if($game_name === 'Lucky For Life'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                    
                                    /** INSERT Millionaire For Life DATA **/
                }else if($game_name === 'Millionaire For Life'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                    
                    /** INSERT Lotto, Hoosier Lotto, UK National, Lotto HotPicks DATA **/
                }else if(($game_name === 'Lotto' && $stateprov_name != 'Illinois' && $stateprov_name != 'New York') || ($game_name === 'Hoosier Lotto' && $stateprov_name === 'Indiana') || ($game_name === 'Lotto HotPicks' && $stateprov_name == 'UK National')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** INSERT Lotto Double Play DATA **/
                }else if($game_name === 'Double Play'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';


                /** INSERT Health Lottery UK, Lotto 6/49 Ontario DATA **/
                 }else if($game_name === 'Health Lottery' && $stateprov_name === 'UK National'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                    
                    /** INSERT Thunderball Lottery UK DATA **/
                 }else if(($game_name === 'Thunderball' && $stateprov_name === 'UK National') || ($game_name === 'Loto Plus' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Revancha X2' && $stateprov_name === 'Puerto Rico')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';

 
                /** INSERT Illinois + NY Lotto DATA, UK Lotto, UK Lunchtime 49s, UK Teatime 49s **/
                }else if($game_name === 'Lotto' && ($stateprov_name === 'Illinois' || $stateprov_name === 'New York') || $game_name === 'Bank a Million' || ($game_name === 'LOTTO' && $stateprov_name === 'Arkansas') || ($game_name === 'Lunchtime 49s' && $stateprov_name === 'UK National')  || ($game_name === 'Teatime 49s' && $stateprov_name === 'UK National') || ($game_name === 'LOTTO' && $stateprov_name === 'UK National') || ($game_name === 'Lotto Plus 1' && $stateprov_name === 'Ireland') || ($game_name === 'Lotto Plus 2' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million 9PM' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million Plus 9PM' && $stateprov_name === 'Ireland') || ($game_name === 'Daily Million 2PM' && $stateprov_name === 'Ireland') || ($game_name === 'Bank a Million' && $stateprov_name === 'Virginia') || ($game_name === 'Daily Million Plus 2PM' && $stateprov_name === 'Ireland') || ($game_name === 'IrishLotto' && $stateprov_name === 'Ireland')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`, 
                    `seventh`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPostSeven'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';

    
                    
                /** (Millionaire Raffle) INSERT Millionaire Raffle UK National, Guaranteed Million Draw Ontario DATA **/
                }else if(($game_name === 'Millionaire Raffle') || ($game_name === 'Pega 2 Noche Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 3 Noche Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 4 Noche Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 2 Day Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 3 Day Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Pega 4 Day Wild' && $stateprov_name === 'Puerto Rico') || ($game_name === 'Guaranteed Million Draw' && $stateprov_name === 'Ontario') || $game_name === 'Cash Pop Early Bird' || $game_name === 'Cash Pop Late Morning' || $game_name === 'Cash Pop Matinee' || $game_name === 'Cash Pop Prime Time' || $game_name === 'Cash Pop Night Owl' || $game_name === 'Pick 3 Evening Wild' || $game_name === 'Pick 3 Midday Wild' || $game_name === 'Pick 4 Evening Wild' || $game_name === 'Pick 4 Midday Wild'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
              

                /** Oscar T (1)ball (Fireballs and Bonus Balls single 1 )Insert Pick 4 Midday FB **/
                }else if(
    $gId === 'FLDF' || $gId === 'FLBF' || $gId === 'FLEF' || $gId === 'FLFF' || $gId === 'FLCF' || $gId === 'FLM' || $gId === 'FLN' || $gId === 'FLO' || $gId === 'FLP' || $gId === 'FLQ' || $gId === 'FLAF' || $gId === 'FLHF' || $gId === 'FLGF' || $gId === 'FLHF' || $gId === 'PABW' || $gId === 'PADW' || $gId === 'PAEW' || $gId === 'PAGW' || $gId === 'PAFW' || $gId === 'PAHW' || $gId === 'TXKF' || $gId === 'TXDF' || $gId === 'TXAF' || $gId === 'TXLF' || $gId === 'TXJF' || $gId === 'TXBF' || $gId === 'TXCF' || $gId === 'TXMF' || $gId === 'CTAW' || $gId === 'CTBW' || $gId === 'ILG' || $gId === 'ILH' || $gId === 'INAF' || $gId === 'INBF' || $gId === 'INM' || $gId === 'INN' || $gId === 'INO' || $gId === 'INQ' || $gId === 'INK' || $gId === 'INCF' || $gId === 'INDF' || $gId === 'MSAF' || $gId === 'MSBF' || $gId === 'MSM' || $gId === 'MSP' || $gId === 'MSCF' || $gId === 'MSDF' || $gId === 'NCAF' || $gId === 'NCBF' || $gId === 'NJAF' || $gId === 'NJBF' || $gId === 'PAAW' || $gId === 'SCAF' || $gId === 'SCBF' || $gId === 'SCP' || $gId === 'TNAW' || $gId === 'TNCW' || $gId === 'TNEW' || $gId === 'VAAF' || $gId === 'VABF' || $gId === 'VAM' || $gId === 'VAN' || $gId === 'VAO' || $gId === 'VAP' || $gId === 'VAQ' || $gId === 'WAM' || $gId === 'CTCW' || $gId === 'CTDW' || $gId === 'ILI' || $gId === 'ILJ' || $gId === 'MDM' || $gId === 'MDN' || $gId === 'MDO' || $gId === 'MDP' || $gId === 'MEE' || $gId === 'MEB' || $gId === 'MEM' || $gId === 'MES' || $gId === 'MEN' || $gId === 'MOM' || $gId === 'MON' || $gId === 'MOO' || $gId === 'MOP' || $gId === 'MOQ' || $gId === 'PACW' || $gId === 'SCCF' || $gId === 'SCDF' || $gId === 'TNBW' || $gId === 'TNDW' || $gId === 'TNFW' || $gId === 'VACF' || $gId === 'VADF' || $gId === 'PAFW' || $gId === 'PAEW' || $gId === 'GAM' || $gId === 'GAN' || $gId === 'GAO' || $gId === 'GAP' || $gId === 'GAQ' || $gId === 'NCCF' || $gId === 'NCDF' || $gId === 'NJCF' || $gId === 'NJDF' || $gId === 'MSCF' || $gId === 'MSDF' || $gId === '136' || $gId === 'VAFF' || $gId === 'PRDW' || $gId === 'PREW' || $gId === 'PRFW' || $gId === 'PRCW' || $gId === 'PRBW' || $gId === 'WIZ'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';



                /** (STRAIGHT 2) INSERT DC 2 Midday, DC 2 Evening DATA **/
                }else if($game_name === 'DC 2 1:50PM' || $game_name === 'DC 2 7:50PM' || ($game_name === 'Pick 2 Midday' && $gId === 'FLE') ||($game_name === 'Pick 2 Evening' && $gId === 'FLF') || ($game_name === 'Pega 2 Day' && $gId === 'PRD') || ($game_name === 'Pega 2 Noche' && $gId === 'PRA') || ($game_name === 'Pick 2 Day' && $gId === 'PAG') || ($game_name === 'Pick 2 Evening' && $gId === 'PAH')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** (STRAIGHT 3) INSERT Cash 3 Midday, Cash 3 Evening, Daily 3 Midday, Daily 3 Evening, Play3 Day, Play3 Night, DC 3 Midday, DC 3 Evening, Play 3 Day, Play 3 Night, Cash 3 Night, Evening 3 Double, MyDay, Cash 3 Morning, Cash 3 Midday, Cash 3 Evening, Daily Game, Daily 3 DATA **/
                }else if($game_name === 'Cash 3 Midday' || $game_name === 'Cash 3 Evening' || $game_name === 'Daily 3 Midday' || $game_name === 'Daily 3 Evening' || $game_name === 'Play3 Day' || $game_name === 'Play3 Night' || $game_name === 'DC 3 1:50PM' || $game_name === 'DC 3 7:50PM' || $game_name === 'DC 3 11:30PM' || $game_name === 'Play 3 Day' || $game_name === 'Play 3 Night' || $game_name === 'Cash 3 Night' || $game_name === 'Evening 3 Double' || $game_name === 'MyDay' || $game_name === 'Cash 3 Morning' || $game_name === 'Cash 3 Midday' || $game_name === 'Pick 3 Evening' || $game_name === 'Pick 3 Day' || $game_name === 'Pick 3 Midday' || $game_name === 'Pick 3 Morning' || $game_name === 'Pick 3 Night' || $game_name === 'Pega 3 Day' || $game_name === 'Pega 3 Noche' || $game_name === 'Daily Game' || $game_name === 'Daily 3' || $game_name === 'Pick 3'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                /** (STRAIGHT 4) INSERT Cash 4 Midday, Cash 4 Evening, Daily 4, Play4 Day, Play4 Night, DC 4 Midday, DC 4 Evening, Play 4 Day, Play 4 Night, Cash 4 Night, Daily 4 Midday, Daily 4 Evening, 2 By 2, Numbers Midday, Numbers Evening, Win 4 Midday, Win 4 Evening, Win for Life, Cash 4 Morning, Cash 4 Midday, Cash 4 Evening, Match 4 DATA **/
                }else if($game_name === 'Cash 4 Midday' || $game_name === 'Pick 4 Evening' || $game_name === 'Pick 4 Day' || $game_name === 'Pick 4 Midday' || $game_name === 'Pick 4 Night' || $game_name === 'Cash 4 Evening' || $game_name === 'Daily 4' || $game_name === 'Play4 Day' || $game_name === 'Play4 Night' || $game_name === 'Play 4 Day' || $game_name === 'Play 4 Night' || $game_name === 'Cash 4 Night' || $game_name === 'Daily 4 Midday' || $game_name === 'Daily 4 Evening' || $game_name === '2 By 2' || $game_name === 'Numbers Midday' || $game_name === 'Numbers Evening' || $game_name === 'Win 4 Midday' || $game_name === 'Win 4 Evening' || $game_name === 'Win for Life' || $game_name === 'Cash 4 Morning' || $game_name === 'Cash 4 Midday' || $game_name === 'Cash 4 Evening' || $game_name === 'Match 4' || $game_id === 'TXL' || $game_id === 'TXB' || $game_id === 'TXM' || $game_id === 'TXD' || $game_name === 'Pick 4'  || $gId === 'NYC' || $gId === 'NYD' || $gId === 'ARD' || $gId === 'ARC' || $gId === 'CAB' || $gId === 'GAC' || $gId === 'GAD' || $gId === 'GAH' || $gId === 'MIC' || $gId === 'MID' || $gId === '116' || $gId === 'DCC' || $gId === 'DCD' || $gId === 'DCJ' || $gId === 'DEC' || $gId === 'DED' || $gId === 'IAC' || $gId === 'IAD' || $gId === 'KYC' || $gId === 'KYD' || $gId === 'LAB' || $gId === 'MAA' || $gId === 'MAC' || $gId === 'MDC' || $gId === 'MDD' || $gId === 'MOC' || $gId === 'MOD' || $gId === 'NME' || $gId === 'NMF' || $gId === 'OHC' || $gId === 'OHD' || $gId === 'ORD' || $gId === 'ORE' || $gId === 'ORF' || $gId === 'ORG' || $gId === 'RIC' || $gId === 'RID' || $gId === 'WIC' || $gId === 'WID' || $gId === 'WVC' || $gId === 'PRF' || $gId === 'PRC' || $gId === '110'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                /** (STRAIGHT 5) INSERT Fantasy 5, Natural State Jackpot, Cash 5, DC 5 Midday, DC 5 Evening, Georgia FIVE Midday, Match Georgia FIVE Evening, Idaho Cash, 5 Star Draw, Weekly Grand, LuckyDay Lotto Midday, LuckyDay Lotto Evening, Cash Ball, Easy 5, MassCash, Gimme 5, World Poker Tour, Poker Lotto, Gopher 5, NORTH5, Show Me Cash, Montana Cash, Big Sky Bonus, Roadrunner Cash, Take 5 Midday, Take 5 Evening, Rolling Cash 5, Treasure Hunt, Dakota Cash, Hit 5, Badger 5, Cowboy Draw DATA **/
                }else if($game_name === 'Fantasy 5' ||  $game_name === 'Fantasy 5 Evening' ||  $game_name === 'Fantasy 5 Midday' || $game_name === 'Natural State Jackpot' || ($game_name === 'Cash 5' && $stateprov_id != 'NJ') || $gId === 'DCE' || $gId === 'DCF' || $gId === 'DEE' || $gId === 'DEF' || $gId === 'MIN' || $game_name === 'Georgia FIVE Midday' || $game_name === 'Georgia FIVE Evening' || $game_name === 'Pick 5 Evening' || $game_name === 'Pick 5 Day' || $game_name === 'Pick 5 Midday' || $game_name === 'Pick 5 Night'  || $game_name === 'Idaho Cash' || $game_name === '5 Star Draw' || $game_name === 'Weekly Grand' || $game_name === 'LuckyDay Lotto Midday' || $game_name === 'LuckyDay Lotto Evening' || $game_name === 'Cash Ball' || $game_name === 'Easy 5' || $game_name === 'MassCash' || $game_name === 'Gimme 5' || $game_name === 'World Poker Tour' || $game_name === 'Poker Lotto' || $game_name === 'Gopher 5' || $game_name === 'NORTH5' || $game_name === 'Show Me Cash' || $game_name === 'Montana Cash' || $game_name === 'Big Sky Bonus' || $game_name === 'Roadrunner Cash' || $game_name === 'Take 5 Midday' || $game_name === 'Take 5 Evening' || $game_name === 'Rolling Cash 5' || $game_name === 'Treasure Hunt' || $game_name === 'Dakota Cash' || $game_name === 'Hit 5' || $game_name === 'Badger 5' || $game_name === 'Cowboy Draw' || $gId === 'MS5' || ($game_name === 'Kentucky 5' && $stateprov_id === 'KY') || ($game_name === 'Pick 5' && $stateprov_id === 'NE') || ($game_name === 'Daily Tennessee Jackpot' && $stateprov_id === 'TN') || ($game_name === 'Texas Two Step' && $stateprov_id === 'TX') || ($game_name === 'Cash Five' && $stateprov_id === 'TX') || ($game_name === 'Pick 5' && $stateprov_id === 'LA') || ($game_name === 'EuroMillion Plus' && $stateprov_id === 'IE') ){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** (STRAIGHT 6) INSERT Jackpot Triple Play, Triple Twist, Lotto Plus, Multi-Win Lotto America Lotto, Jumbo Bucks Lotto, Megabucks Doubler, MultiMatch, Bonus Match 5, Megabucks Plus, Classic Lotto 47, Classic Lotto, Megabucks, Match 6 Lotto, Wild Money, Palmetto Cash 5, Tennessee Cash, Cash 25, Two Step (TX) DATA **/
                }else if($game_name === 'Jackpot Triple Play' || $game_name === 'Triple Twist' || $game_name === 'Lotto Plus' ||  $game_name === 'The Pick' || $game_name === 'Multi-Win Lotto' || $game_name === 'Megabucks Doubler' || $game_name === 'MultiMatch' || $gId === 'MD3' || $game_name === 'Megabucks Plus' || $game_name === 'Classic Lotto 47' || $game_name === 'Classic Lotto' || $game_name === 'Megabucks' || $game_name === 'Match 6 Lotto' || $game_name === 'Wild Money' || $game_name === 'Palmetto Cash 5' || $game_name === 'Tennessee Cash' || $game_name === 'Cash 25' || ($game_name === 'Super Cash' && $stateprov_name === 'Wisconsin') || ($game_name === 'Lotto Plus' && $stateprov_name === 'Texas') || ($game_name === 'Pick 6 Lotto' && $stateprov_name === 'New Jersey') || $gId === 'MI6' || ($game_name === 'Double Play' && $stateprov_name === 'New Jersey') || ($game_name === 'Lotto Texas' && $stateprov_name === 'Texas')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** (STRAIGHT 8) INSERT Lucky Lines DATA **/
                }else if($game_name === 'Lucky Lines'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`,
                    `seventh`,
                    `eighth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPosSeventh', 
                    '$pPosEighth'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** (STRAIGH 11) INSERT All or Nothing Midday, All or Nothing Evening DATA **/
                }else if($game_id === 'WI8' || $game_id === 'WI7'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`,
                    `seventh`,
                    `eighth`,
                    `nineth`,
                    `tenth`,
                    `eleventh`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPosSeventh', 
                    '$pPosEighth', 
                    '$pPosNineth', 
                    '$pPosTenth', 
                    '$pPosEleventh'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                /** (STRAIGH 12) INSERT (TX) All or Nothing Midday, All or Nothing Evening DATA **/
                }else if($game_id === 'TXF' || $game_id === 'TXG' || $game_id === 'TXH' || $game_id === 'TXI'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`,
                    `seventh`,
                    `eighth`,
                    `nineth`,
                    `tenth`,
                    `eleventh`,
                    `twelveth`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPosSeventh', 
                    '$pPosEighth', 
                    '$pPosNineth', 
                    '$pPosTenth', 
                    '$pPosEleventh', 
                    '$pPosTwelveth'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                    
                            /** INSERT New York Pick 10 DATA **/
                }else if(($game_name === 'Pick 10' && $stateprov_name === 'New York') || $game_id === 'WA4'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`,
                    `seventh`,
                    `eighth`,
                    `nineth`,
                    `tenth`,
                    `eleventh`,
                    `twelveth`,
                    `thirtheenth`,
                    `fourteenth`,
                    `fifteenth`,
                    `sixteenth`,
                    `seventeenth`,
                    `eighteenth`,
                    `nineteenth`,
                    `twentieth`  
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPosSeventh', 
                    '$pPosEighth', 
                    '$pPosNineth', 
                    '$pPosTenth', 
                    '$pPosEleventh', 
                    '$pPosTwelveth', 
                    '$pPosThirtheenth', 
                    '$pPosFourteenth', 
                    '$pPosFifteenth', 
                    '$pPosSixteenth', 
                    '$pPosSeventeenth', 
                    '$pPosEighteenth', 
                    '$pPosNineteenth', 
                    '$pPosTwentieth'                    
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                /** INSERT Indiana Quick Draw Midday, Quick Draw Evening DATA **/
                }else if(($game_name === 'Quick Draw Midday' && $stateprov_name === 'Indiana') || ($game_name === 'Quick Draw Evening' && $stateprov_name === 'Indiana')){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`,
                    `seventh`,
                    `eighth`,
                    `nineth`,
                    `tenth`,
                    `eleventh`,
                    `twelveth`,
                    `thirtheenth`,
                    `fourteenth`,
                    `fifteenth`,
                    `sixteenth`,
                    `seventeenth`,
                    `eighteenth`,
                    `nineteenth`,
                    `twentieth`,
                    `twenty_first`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPosSeventh', 
                    '$pPosEighth', 
                    '$pPosNineth', 
                    '$pPosTenth', 
                    '$pPosEleventh', 
                    '$pPosTwelveth', 
                    '$pPosThirtheenth', 
                    '$pPosFourteenth', 
                    '$pPosFifteenth', 
                    '$pPosSixteenth', 
                    '$pPosSeventeenth', 
                    '$pPosEighteenth', 
                    '$pPosNineteenth', 
                    '$pPosTwentieth', 
                    '$pPosTwenty_first'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                    
                    
                /** INSERT Michigan Keno WA4 DATA **/
                }else if($game_name === 'Keno' && $stateprov_name === 'Michigan'){
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`, 
                    `first`, 
                    `second`, 
                    `third`, 
                    `fourth`, 
                    `fifth`, 
                    `sixth`,
                    `seventh`,
                    `eighth`,
                    `nineth`,
                    `tenth`,
                    `eleventh`,
                    `twelveth`,
                    `thirtheenth`,
                    `fourteenth`,
                    `fifteenth`,
                    `sixteenth`,
                    `seventeenth`,
                    `eighteenth`,
                    `nineteenth`,
                    `twentieth`,
                    `twenty_first`,
                    `twenty_second`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot', 
                    '$pPosOne', 
                    '$pPosTwo', 
                    '$pPosThree', 
                    '$pPosFour', 
                    '$pPosFive', 
                    '$pPosSix', 
                    '$pPosSeventh', 
                    '$pPosEighth', 
                    '$pPosNineth', 
                    '$pPosTenth', 
                    '$pPosEleventh', 
                    '$pPosTwelveth', 
                    '$pPosThirtheenth', 
                    '$pPosFourteenth', 
                    '$pPosFifteenth', 
                    '$pPosSixteenth', 
                    '$pPosSeventeenth', 
                    '$pPosEighteenth', 
                    '$pPosNineteenth', 
                    '$pPosTwentieth', 
                    '$pPosTwenty_first',
                    '$pPosTwenty_second'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
             

                /** INSERT ALL OTHERS DATA **/
                }else{
                    $db = JFactory::getDBO();
                    $sql = "INSERT INTO `$dbCol` (
                    `country`, 
                    `stateprov_name`, 
                    `stateprov_id`, 
                    `game_id`, 
                    `game_name`, 
                    `draw_date`, 
                    `draw_results`, 
                    `next_draw_date`, 
                    `next_jackpot`
                    )VALUES(
                    '$country', 
                    '$stateprov_name', 
                    '$stateprov_id', 
                    '$game_id', 
                    '$game_name', 
                    '$draw_date', 
                    '$draw_results', 
                    '$next_draw_date', 
                    '$next_jackpot'
                    )";
                    $db->setQuery($sql);
                    $db->execute();
                    
                    echo $stateprov_name.' '.$game_name.' updated successfully<br />';
                }
            }else if(!empty($posDuplicate) && !empty($next_jackpot)){ /** DATA ALREADY IN THE DB NEED JACKPOT UPDATE **/
                
                $queryUpdate = $db->getQuery(true)
                    ->update($db->quoteName($dbCol))
                    ->set($db->quoteName('next_jackpot') . ' = ' . $db->quote($next_jackpot))
                    ->where($db->quoteName('game_name') . ' = ' . $db->quote($game_name))
                    ->where($db->quoteName('stateprov_name') . ' = ' . $db->quote($stateprov_name))
                    ->where($db->quoteName('game_id') . ' = ' . $db->quote($game_id));
                $db->setQuery($queryUpdate);
                $db->execute();
                
                echo 'DUPLICATE FOUND FOR <strong>'.$stateprov_name.' '.$game_name.'</strong> Date:'.$draw_date.' (Jackpot Updated)<br />';
            }else{ /** DATA ALREADY IN THE DB **/
                echo 'DUPLICATE FOUND FOR <strong>'.$stateprov_name.' '.$game_name.'</strong> Date:'.$draw_date.'<br />';
            }
        }
	} /** EO FOREACH $myResults **/
    } /** EO FOREACH $myData **/
    
 } /** EO IF NOT EMPTY $myData **/




?>