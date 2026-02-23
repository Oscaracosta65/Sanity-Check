<?php
/**
 * @version    CVS: 1.0.3
 * @package    Com_Lotterydb
 * @author     FULLSTACK DEV <admin@fullstackdev.us>
 * @copyright  2022 FULLSTACK DEV default as of 04 23 2025  0314 pm
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 **/

/** *********************************** MAIN PAGE LOTTERY LIST ********************************************** **/

/** Output main state lotteries page  h1wrapper  **/

// No direct access txj
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/** GRAB THE CURRENT URL (safe string) **/
$c_Url = Uri::getInstance();
$currentUrl = $c_Url->toString();

/** Current state slug from path **/
$path = trim($c_Url->getPath(), '/');
$parts = $path ? explode('/', $path) : [];
$c_state = $parts ? end($parts) : '';




/** CONVERTING STATE NAME AND ABREVIATION FROM THE URL PATH **/
/**
 * Phase 2 – Step 1: Whitelist state slug ? {name, abbr}
 * Security/maintainability: only recognized slugs are allowed to set state values.
 */
$stateMap = [
    // US states / territories / DC
    'arkansas'       => ['name' => 'Arkansas',       'abbr' => 'ar'],
    'arizona'        => ['name' => 'Arizona',        'abbr' => 'az'],
    'california'     => ['name' => 'California',     'abbr' => 'ca'],
    'colorado'       => ['name' => 'Colorado',       'abbr' => 'co'],
    'connecticut'    => ['name' => 'Connecticut',    'abbr' => 'ct'],
    'dc'             => ['name' => 'DC',             'abbr' => 'dc'],
    'delaware'       => ['name' => 'Delaware',       'abbr' => 'de'],
    'florida'        => ['name' => 'Florida',        'abbr' => 'fl'],
    'georgia'        => ['name' => 'Georgia',        'abbr' => 'ga'],
    'iowa'           => ['name' => 'Iowa',           'abbr' => 'ia'],
    'idaho'          => ['name' => 'Idaho',          'abbr' => 'id'],
    'illinois'       => ['name' => 'Illinois',       'abbr' => 'il'],
    'indiana'        => ['name' => 'Indiana',        'abbr' => 'in'],
    'kansas'         => ['name' => 'Kansas',         'abbr' => 'ks'],
    'kentucky'       => ['name' => 'Kentucky',       'abbr' => 'ky'],
    'louisiana'      => ['name' => 'Louisiana',      'abbr' => 'la'],
    'massachusetts'  => ['name' => 'Massachusetts',  'abbr' => 'ma'],
    'maryland'       => ['name' => 'Maryland',       'abbr' => 'md'],
    'maine'          => ['name' => 'Maine',          'abbr' => 'me'],
    'michigan'       => ['name' => 'Michigan',       'abbr' => 'mi'],
    'minnesota'      => ['name' => 'Minnesota',      'abbr' => 'mn'],
    'mississippi'    => ['name' => 'Mississippi',    'abbr' => 'ms'],
    'missouri'       => ['name' => 'Missouri',       'abbr' => 'mo'],
    'montana'        => ['name' => 'Montana',        'abbr' => 'mt'],
    'north-carolina' => ['name' => 'North Carolina', 'abbr' => 'nc'],
    'north-dakota'   => ['name' => 'North Dakota',   'abbr' => 'nd'],
    'nebraska'       => ['name' => 'Nebraska',       'abbr' => 'ne'],
    'new-hampshire'  => ['name' => 'New Hampshire',  'abbr' => 'nh'],
    'new-jersey'     => ['name' => 'New Jersey',     'abbr' => 'nj'],
    'new-mexico'     => ['name' => 'New Mexico',     'abbr' => 'nm'],
    'new-york'       => ['name' => 'New York',       'abbr' => 'ny'],
    'ohio'           => ['name' => 'Ohio',           'abbr' => 'oh'],
    'oklahoma'       => ['name' => 'Oklahoma',       'abbr' => 'ok'],
    'oregon'         => ['name' => 'Oregon',         'abbr' => 'or'],
    'pennsylvania'   => ['name' => 'Pennsylvania',   'abbr' => 'pa'],
    'puerto-rico'    => ['name' => 'Puerto Rico',    'abbr' => 'pr'],
    'rhode-island'   => ['name' => 'Rhode Island',   'abbr' => 'ri'],
    'south-carolina' => ['name' => 'South Carolina', 'abbr' => 'sc'],
    'south-dakota'   => ['name' => 'South Dakota',   'abbr' => 'sd'],
    'tennessee'      => ['name' => 'Tennessee',      'abbr' => 'tn'],
    'texas'          => ['name' => 'Texas',          'abbr' => 'tx'],
    'virginia'       => ['name' => 'Virginia',       'abbr' => 'va'],
    'vermont'        => ['name' => 'Vermont',        'abbr' => 'vt'],
    'washington'     => ['name' => 'Washington',     'abbr' => 'wa'],
    'wisconsin'      => ['name' => 'Wisconsin',      'abbr' => 'wi'],
    'west-virginia'  => ['name' => 'West Virginia',  'abbr' => 'wv'],
    'wyoming'        => ['name' => 'Wyoming',        'abbr' => 'wy'],

    // International (your file already supports these)
    'uk-national'    => ['name' => 'UK National',    'abbr' => 'uk'],
    'ireland'        => ['name' => 'Ireland',        'abbr' => 'ie'],
];

// Normalize slug to a strict format (defensive)
$slug = strtolower(trim((string) $c_state));
$slug = preg_replace('/[^a-z\-]/', '', $slug);

// Apply whitelist mapping
if (isset($stateMap[$slug])) {
    $stName  = $stateMap[$slug]['name'];
    $stAbrev = $stateMap[$slug]['abbr'];
}
/** SET DEFAULT STATE **/
if(empty($stName)){
    $stName = 'Florida';
    $stAbrev = 'fl';
}
$doc = Factory::getDocument();

// PHASE 1 – STEP 7A: keep RAW state values for SQL/URLs; escape only on output
$stNameRaw  = (string) $stName;
$stAbrevRaw = (string) $stAbrev;

// SQL/URL-safe raw values used for DB lookups and query builder binds
// (Never use HTML-escaped strings for SQL comparisons.)
$stNameSql  = $stNameRaw;
$stAbrevSql = $stAbrevRaw;

// Safe, display-ready values (use these only when echoing text)
$stNameEsc  = htmlspecialchars($stNameRaw, ENT_QUOTES, 'UTF-8');
$stAbrevEsc = htmlspecialchars($stAbrevRaw, ENT_QUOTES, 'UTF-8');

// Ensure URL is always a string before validating (prevents null/object edge cases)
$c_UrlStr = (string) ($c_Url ?? '');
$c_Url = filter_var($c_UrlStr, FILTER_VALIDATE_URL) ? $c_UrlStr : Uri::base();
$imageUrl = 'https://lottoexpert.net/images/lottoexpert_logo-stacked.jpg';

// Escaped copies for meta tag attribute contexts
$stNameMeta  = htmlspecialchars((string) $stName, ENT_QUOTES, 'UTF-8');
$stAbrevMeta = htmlspecialchars(strtoupper((string) $stAbrev), ENT_QUOTES, 'UTF-8');

// Twitter Meta Tags
$doc->addCustomTag('<meta name="twitter:title" content="'.$stNameMeta.' AI Lottery Prediction and Analysis - LottoExpert.net">');
$doc->addCustomTag('<meta name="twitter:description" content="All '.$stNameMeta.' AI lottery predictions, AI analysis, Skip and Hit, Heatmaps, archives and free Lotto Wheel Generators">');
$doc->addCustomTag('<meta name="twitter:image" content="'.$imageUrl.'">');

// Open Graph Meta Tags
$doc->addCustomTag('<meta property="og:site_name" content="LottoExpert.net">');
$doc->addCustomTag('<meta property="og:title" content="'.$stNameMeta.' AI lottery prediction, AI analysis, Skip and Hit, Heatmaps, archives and free Lotto Wheel Generators">');
$doc->addCustomTag('<meta property="og:description" content="'.$stNameMeta.' '.$stAbrevMeta.' AI lottery prediction, AI analysis, Skip and Hit, Heatmaps, archives and free Lotto Wheel Generators for '.$stName.' lottery games.">');
$doc->addCustomTag('<meta property="og:url" content="'.htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8').'">');
$doc->addCustomTag('<meta property="og:type" content="article">');
$doc->addCustomTag('<meta property="og:image" content="'.$imageUrl.'">');
$doc->addCustomTag('<meta property="og:image:width" content="312">');
$doc->addCustomTag('<meta property="og:image:height" content="96">');



/** DECLARE DB TABLE TO QUERY descriptiontext**/
/**
 * PHASE 1 – STEP 5A: Safe DB table name
 * Use a strict 2-letter state code for DB identifiers (NOT the HTML-escaped version).
 */
$stAbrevDb = strtolower(preg_replace('/[^a-z]/i', '', (string) $stAbrev));
if ($stAbrevDb === '') { $stAbrevDb = 'fl'; } // safety fallback
$dbCol = '#__lotterydb_' . $stAbrevDb;

//set page and browser title
// --- SEO: page title + meta description (safe production change; meta only) ---
$document = Factory::getDocument();

// Title: lead with "Results & Winning Numbers" (primary search intent)
$browserbar = $stName . ' Lottery Results & Winning Numbers | Tools, Archives, Analysis';
$document->setTitle($browserbar);

// Description: concise, intent-aligned, includes tools/archives
$m_description = 'Latest ' . $stName . ' lottery results and winning numbers, next draw dates, jackpots, archives, and analysis tools for every game in ' . $stName . '.';
$document->setDescription(strip_tags($m_description));
?>

<!-- Phase 5: Sticky quick-link (mobile) -->
<button type="button" id="leQuickIndexBtn" aria-label="Jump to game index">Games</button>

<script>
(function () {
  'use strict';

  // Filter the Game Index (no DB calls)
  var input = document.getElementById('leGameFilter');
  var list  = document.getElementById('leGameIndex');

  if (input && list) {
    input.addEventListener('input', function () {
      var q = (input.value || '').toLowerCase().trim();
      var items = list.querySelectorAll('li');
      for (var i = 0; i < items.length; i++) {
        var t = (items[i].textContent || '').toLowerCase();
        items[i].style.display = (q === '' || t.indexOf(q) !== -1) ? '' : 'none';
      }
    });
  }

  // Sticky "Games" button (mobile)
  var btn = document.getElementById('leQuickIndexBtn');
  if (btn && list) {
    btn.addEventListener('click', function () {
      list.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (input) { input.focus({ preventScroll: true }); }
    });
  } else if (btn) {
    btn.style.display = 'none';
  }
})();
</script>

<style type="text/css">
#sp-main-body {padding: 50px 0 100px;}
.leftSidebarInner {padding: 15px;}
.ftextwrapper {margin: 50px auto 0;}

/* Legacy tweak for older layouts (kept for safety) */
span.pplay span.circlesPb {
    display: inline;
    padding: 5px 10px;
}

/* SKAI – Power Play / Megaplier / Cash Ball line inside state cards */
.lotResultWrap span.pplay {
    display: block;          /* own line under the balls */
    text-align: center;
    margin-top: 18px;        /* space below red/green bonus ball row */
    margin-bottom: 12px;     /* space above "Next Draw" */
    line-height: 1.3;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Keep the little multiplier ball nicely aligned next to the label */
.lotResultWrap span.pplay .circlesFb,
.lotResultWrap span.pplay .circlesPb {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 6px;
}

/* ============================================
   SKAI Phase 1 – State Lottery List Visual Skin
   Scope: Only within .lotResultWrap on this page
   ============================================ */

/* Main container of all game cards */
.lotResultWrap {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

/* Individual game card – SKAI tile look
   NOTE: height:auto explicitly overrides legacy 505px from master CSS
   and extra bottom padding keeps the CTA button clear. */
.lotResultWrap .resultWrap {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 270px;
    max-width: 100%;
    margin: 20px 10px;
    padding: 20px 18px 64px;   /* extra bottom space for the button */
    box-sizing: border-box;
    text-align: center;
    height: auto !important;   /* kill the old fixed 505px height */
    overflow: visible;

    background: #ffffff;
    color: #0e1c2b;
    border-radius: 12px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.18);

    cursor: pointer;
    transition:
        transform 0.18s ease-out,
        box-shadow 0.18s ease-out,
        border-color 0.18s ease-out,
        background-color 0.18s ease-out;
}

.lotResultWrap .resultWrap:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.25);
    border-color: rgba(37, 99, 235, 0.35);
    background-color: #f9fbff;
}

/* Game title – aligned with homepage tile h2 */
.lotResultWrap .resultWrap h2 {
    margin: 0 0 10px;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: 0.01em;
    color: #0f172a;
}

/* Game logo inside card (if present) – CLS safe */
.lotResultWrap .resultWrap img {
    display: block;
    width: 180px;            /* stable width to prevent layout shift */
    max-width: 100%;
    height: 60px;            /* stable height to prevent layout shift */
    margin: 0 auto 12px;
    object-fit: contain;
}

/* Subtle placeholder feel if an image fails */
.lotResultWrap .resultWrap .lotto-logo {
    background: #f8fafc;
    border-radius: 10px;
}


/* "Last Result" line + generic meta text */
.lotResultWrap .lstResult {
    font-size: 0.9rem;
    line-height: 1.5;
    color: #424866;
    margin: 6px 0;
}

/* When two lstResult blocks are stacked (numbers + Fireball/Wild Ball),
   keep them visually connected but not cramped. */
.lotResultWrap .lstResult + .lstResult {
    margin-top: 4px; /* small, even gap between Last Result and Fireball line */
}

/* IMPORTANT:
   Do NOT hide br:last-of-type here.
   Some game templates only have ONE <br /> before the number balls.
   If we hide it, the first ball renders inline with the date. */
/* .lotResultWrap .lstResult br:last-of-type { display: none; } */

/* Next draw / jackpot labels */
.lotResultWrap .nDraw {
    font-size: 0.82rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #6b7280;
    margin: 10px 0 2px;
}

/* When NEXT DRAW comes right after lstResult,
   add breathing room so it never feels covered by the balls or Fireball. */
.lotResultWrap .lstResult + .nDraw {
    margin-top: 22px;
}

/* Override legacy Helix tile rules that were forcing a big fixed block
   for lstResult and squeezing NEXT DRAW. */
.lotResultWrap .resultWrap p.lstResult {
    height: auto !important;    /* remove old 115px fixed height */
    margin-bottom: 6px;         /* clean, compact */
}

.lotResultWrap .resultWrap p.nDraw {
    margin-top: 18px !important;  /* clear space below Fireball / pplay line */
    margin-bottom: 4px;
}

/* Next draw date */
.lotResultWrap .nDrawDate {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 4px;
}

/* Next jackpot amount */
.lotResultWrap .nJackpot {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1a1a72;
    margin: 2px 0 4px;
}
/* Number pills – scoped to state tiles.
   Extra bottom margin prevents wrapped rows (6 balls) from sitting on top
   of each other or on top of the extra ball row. */
.lotResultWrap .circles,
.lotResultWrap .circlesPb,
.lotResultWrap .circlesFb {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;

    /* left/right spacing + more bottom breathing room when balls wrap */
    margin: 0 3px 10px;

    border-radius: 50%;
    /* All visual styling (background, color, shadows) comes from master CSS:
       span.circles, .circlesPb, .circlesFb */
}

/* Ball color/gradients are now defined in master CSS (span.circles, .circlesPb, .circlesFb)
   This file no longer overrides ball colors or shadows. */
.lotResultWrap .circles { /* inherits full visual theme from master CSS */ }
.lotResultWrap .circlesPb,
.lotResultWrap .circlesFb { /* inherit from master CSS (bonus / extra ball styles) */ }
/* No local ::after shadow override here – use the global definition if present */

/* CTA Button – match homepage pbHistoryBtn look */
.lotResultWrap .rnaBtn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 8px;
    padding: 8px 16px;
    min-width: 190px;
    box-sizing: border-box;

    font-size: 0.84rem;
    font-weight: 700;
    line-height: 1.35;
    text-align: center;
    text-decoration: none;

    color: #ffffff;
    background: linear-gradient(135deg, #1d4ed8, #0f172a);
    border-radius: 999px;
    border: none;

    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.35);
    cursor: pointer;
    transition:
        background 0.18s ease-out,
        box-shadow 0.18s ease-out,
        transform 0.18s ease-out,
        opacity 0.18s ease-out;
}

.lotResultWrap .rnaBtn:hover,
.lotResultWrap .rnaBtn:focus-visible {
    background: linear-gradient(135deg, #2563eb, #020617);
    /* Vertical “lift” only by default */
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.5);
    opacity: 0.98;
    outline: 2px solid #fbbf24;
    outline-offset: 2px;
}

/* SKAI: keep AI Lottery Predictions CTA anchored inside the card
   and prevent any global pbHistoryBtn rules from pushing it sideways. */
.lotResultWrap .lotto-actions {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 12px;           /* consistent breathing room above button */
}

/* Hard override any old pbHistoryBtn positioning so it stays in the card */
.lotResultWrap .rnaBtn.pbHistoryBtn {
    position: static;           /* cancel absolute/relative offsets from globals */
    margin: 0;                  /* no side-push from margins */
    max-width: 100%;            /* never extend beyond card width */
    transform: none;            /* base state: perfectly centered, no shift */
}

/* Tiny, controlled “smudge” on hover – stays inside the card */
.lotResultWrap .rnaBtn.pbHistoryBtn:hover,
.lotResultWrap .rnaBtn.pbHistoryBtn:focus-visible {
    transform: translateX(1px) translateY(-1px);  /* 1px right, 1px up */
    background: linear-gradient(135deg, #2563eb, #020617);
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.5);
}

/* Responsive tweaks */
@media only screen and (min-width: 320px) and (max-width: 1024px) {
    .lotResultWrap {
        gap: 16px;
    }

    .lotResultWrap .resultWrap {
        width: 100% !important;
        margin: 14px 0;
        padding: 18px 14px 16px;
    }

    img.lottoMan {max-width: 32px;}
    h1.lotteryHeading {color: #fff; font-size: 18px;}
    .border {height: 69px;}
}



.state-hub-header {max-width: 980px; margin: 0 auto 18px;}
.state-hub-h1 {font-size: 2rem; line-height: 1.15; margin: 0 0 10px; font-weight: 900;}
.state-hub-intro {margin: 0; color: #334155; font-size: 1.02rem; line-height: 1.55;}
.state-hub-h2{margin:22px 0 10px; font-size:1.25rem; font-weight:800;}
.state-hub-game-ul{columns:2; column-gap:24px; padding-left:18px; margin:0 0 18px;}
.state-hub-game-ul li{break-inside:avoid; margin:0 0 6px;}
@media (max-width: 768px){ .state-hub-game-ul{columns:1;} }


/* ============================================
   SKAI Breadcrumb (Visible)
   ============================================ */
.skai-breadcrumb{
    max-width:1100px;
    margin:6px auto 14px;
    padding:0 4px;
}

.skai-breadcrumb__list{
    list-style:none;
    padding:0;
    margin:0;
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:8px;
    color:#64748b;
    font-weight:700;
    font-size:0.92rem;
}

.skai-breadcrumb__item{margin:0;}

.skai-breadcrumb__sep{
    opacity:.75;
    font-weight:900;
}

.skai-breadcrumb__link{
    text-decoration:none;
    color:#1d4ed8;
    padding:6px 10px;
    border-radius:999px;
    transition:background .18s ease, transform .18s ease;
}

.skai-breadcrumb__link:hover,
.skai-breadcrumb__link:focus-visible{
    background:#eef2ff;
    transform:translateY(-1px);
    outline:2px solid #fbbf24;
    outline-offset:2px;
}

.skai-breadcrumb__item--current{
    color:#0f172a;
    font-weight:900;
}

/* ============================================
   SKAI FAQ (Visible + Accessible)
   ============================================ */
.skai-faq{
    max-width:1100px;
    margin: 14px auto 18px;
    padding: 14px 14px 10px;
    background:#ffffff;
    border-radius:14px;
    border:1px solid rgba(15,23,42,0.08);
    box-shadow:0 10px 22px rgba(15,23,42,0.12);
}

.skai-faq__head{ margin-bottom:10px; }

.skai-faq__title{
    margin:0 0 6px;
    font-size:1.25rem;
    font-weight:900;
    color:#0f172a;
    letter-spacing:0.01em;
}

.skai-faq__sub{
    margin:0;
    color:#475569;
    font-size:0.98rem;
    line-height:1.45;
}

.skai-faq__items{
    margin-top:12px;
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:10px 12px;
}

.skai-faq__item{
    background:#f8fafc;
    border:1px solid rgba(37,99,235,0.18);
    border-radius:12px;
    padding:10px 12px;
}

.skai-faq__q{
    cursor:pointer;
    font-weight:900;
    color:#0f172a;
    list-style:none;
    outline:none;
}

.skai-faq__q::-webkit-details-marker{ display:none; }

.skai-faq__a{
    margin-top:8px;
    color:#334155;
    line-height:1.55;
    font-size:0.95rem;
}

.skai-faq__item[open]{
    background:#ffffff;
    border-color: rgba(37,99,235,0.35);
    box-shadow:0 8px 18px rgba(15,23,42,0.10);
}

.skai-faq__item:focus-within{
    outline:2px solid #fbbf24;
    outline-offset:2px;
}

@media (max-width:768px){
    .skai-faq__items{ grid-template-columns:1fr; }
}

/* ============================================
   SKAI Game Index (SEO + UX) – Scoped, safe
   ============================================ */
.skai-gameindex{
    max-width: 1100px;
    margin: 18px auto 22px;
    padding: 14px 14px 16px;
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
}

.skai-gameindex__head{ margin-bottom: 10px; }

.skai-gameindex__title{
    margin: 0 0 6px;
    font-size: 1.25rem;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: 0.01em;
}

.skai-gameindex__sub{
    margin: 0;
    color: #475569;
    font-size: 0.98rem;
    line-height: 1.45;
}

.skai-gameindex__list{
    list-style: none;
    padding: 0;
    margin: 12px 0 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 12px;
}

.skai-gameindex__item{ margin: 0; }

.skai-gameindex__link{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 10px 12px;
    text-decoration: none;
    font-weight: 800;
    font-size: 0.9rem;
    color: #0f172a;
    background: #f8fafc;
    border: 1px solid rgba(37, 99, 235, 0.18);
    border-radius: 999px;
    transition: transform 0.18s ease-out, box-shadow 0.18s ease-out, background 0.18s ease-out, border-color 0.18s ease-out;
}

.skai-gameindex__link:hover,
.skai-gameindex__link:focus-visible{
    background: linear-gradient(135deg, #1d4ed8, #0f172a);
    color: #ffffff;
    border-color: rgba(37, 99, 235, 0.35);
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.25);
    outline: 2px solid #fbbf24;
    outline-offset: 2px;
}

@media (max-width: 768px){
    .skai-gameindex__list{ grid-template-columns: 1fr; }
}


/* Phase 1 (deferred): accessibility + micro-UX polish (scoped) */
.le-skiplink{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;}
.le-skiplink:focus{left:12px;top:12px;width:auto;height:auto;z-index:9999;padding:10px 12px;background:#111827;color:#fff;border-radius:10px;}
.skai-gameindex__filter{margin:10px 0 0 0;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.skai-gameindex__filterlabel{font-weight:800;font-size:.92rem;color:#111827;}
.skai-gameindex__filterinput{flex:1;min-width:220px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;}
.skai-gameindex__filterinput:focus{outline:3px solid rgba(59,130,246,.35);outline-offset:2px;}
#leQuickIndexBtn{position:fixed;right:14px;bottom:14px;z-index:9998;border:none;border-radius:999px;padding:12px 14px;background:#111827;color:#fff;font-weight:800;box-shadow:0 10px 30px rgba(0,0,0,.18);cursor:pointer;}
#leQuickIndexBtn:focus{outline:3px solid rgba(59,130,246,.45);outline-offset:2px;}
@media (min-width: 960px){#leQuickIndexBtn{display:none;}}

</style>

<?php
/** DISPLAY HEADER **/



/** DISPLAY TOP BUTTONS **/
echo JHtml::_('content.prepare', '{loadposition topLEHeader}');

/** INJECT DESCRIPTION TEXT (query builder + prepared output) **/
if (!empty($stAbrevSql)) {
    $qState = strtoupper($stAbrevSql);

    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select($db->quoteName('descriptiontext'))
        ->from($db->quoteName('#__lottostates_words'))
        ->where($db->quoteName('statename') . ' = :statename')
        ->where($db->quoteName('state') . ' = 1')
        ->bind(':statename', $qState);

    $db->setQuery($query);
    $dtext = (string) $db->loadResult();

    if ($dtext !== '') {
        echo JHtml::_('content.prepare', $dtext);
    }
}
/** EO INJECT DESCRIPTION TEXT **/


/** START THE COLUMN SYSTEM **/
echo '<div class="section group">';

/** LEFT SIDEBAR COLUMN — fully commented out for now to avoid mismatched tags **/
// echo '<div class="col span_1_of_4">';
// echo '<div class="leftSidebarInner">';
// echo JHtml::_('content.prepare', '{loadposition usstates}');
// echo JHtml::_('content.prepare', '{loadposition STATELOTTERYLIST}');
// echo '</div>';  // .leftSidebarInner
// echo '</div>';  // .col span_1_of_4

/** MAIN RIGHT COLUMN **/
echo '<div class="col span_4_of_4">';

/** SEO-safe: H1 + intro for State Hub (additive only, no logic changes) **/
echo '<header class="state-hub-header">';
echo '<h1 class="state-hub-h1">'.htmlspecialchars($stName, ENT_QUOTES, 'UTF-8').' Lottery Results & Winning Numbers</h1>';

// A11y: skip link to Game Index
 echo '<a class="le-skiplink" href="#leGameIndex">Skip to game index</a>';

echo '<p class="state-hub-intro">Browse the latest '.htmlspecialchars($stName, ENT_QUOTES, 'UTF-8').' lottery results, next draw dates, jackpots, and game-by-game analysis tools. Select a game below to view results, archives, and number insights.</p>';
echo '</header>';

/* =========================================================
   PHASE 1 – STEP 2: Breadcrumbs (Visible + JSON-LD)
   Production-safe: additive only, no routing changes
   ========================================================= */

// --- Build breadcrumb URLs ---
$homeUrl  = '/';
$hubUrl   = '/all-us-lotteries';
$stateUrl = htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8');

// --- Visible breadcrumb ---
echo '<nav class="skai-breadcrumb" aria-label="Breadcrumb">';
echo '<ol class="skai-breadcrumb__list">';
echo '<li class="skai-breadcrumb__item"><a class="skai-breadcrumb__link" href="'.$homeUrl.'">Home</a></li>';
echo '<li class="skai-breadcrumb__sep" aria-hidden="true">›</li>';
echo '<li class="skai-breadcrumb__item"><a class="skai-breadcrumb__link" href="'.$hubUrl.'">All US Lotteries</a></li>';
echo '<li class="skai-breadcrumb__sep" aria-hidden="true">›</li>';
echo '<li class="skai-breadcrumb__item skai-breadcrumb__item--current" aria-current="page">'
    . htmlspecialchars($stName, ENT_QUOTES, 'UTF-8') . ' Results</li>';
echo '</ol>';
echo '</nav>';

// --- JSON-LD BreadcrumbList ---
$breadcrumbJson = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => 'Home',
            'item'     => rtrim(Uri::base(), '/') . '/'
        ],
        [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => 'All US Lotteries',
            'item'     => rtrim(Uri::base(), '/') . $hubUrl
        ],
        [
            '@type'    => 'ListItem',
            'position' => 3,
            'name'     => $stName . ' Results',
            'item'     => $stateUrl
        ],
    ],
];

Factory::getDocument()->addCustomTag(
    '<script type="application/ld+json">'
    . json_encode($breadcrumbJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    . '</script>'
);



/** GET LIST OF AVAILABLE LOTTERIES FOR THIS STATE **/
$db = Factory::getDbo();
$sqlCheck = "SELECT DISTINCT `game_id` FROM `$dbCol`";
$db->setQuery($sqlCheck);
$db->execute();

$resultList = $db->loadObjectList();

if (!empty($resultList)) {

    // ============================
    // Phase 3: Performance batching + caching (safe, no HTML changes)
    // ============================
    $latestByGameId = [];

    try {
        // Joomla 5+ cache controller (callback)
        $cacheFactory = Factory::getContainer()->get(\Joomla\CMS\Cache\CacheControllerFactoryInterface::class);
        $cache        = $cacheFactory->createCacheController('callback', ['defaultgroup' => 'lottoexpert_statehub']);
        $cache->setLifeTime(1800); // 30 min

        $cacheId = 'latestRows_' . strtolower($stAbrevSql);

        $latestRows = $cache->get($cacheId, function () use ($stNameSql, $dbCol) {
            $db = Factory::getDbo();

            // Latest draw per game_id for this state (single query)
            $sub = $db->getQuery(true)
                ->select([
                    $db->quoteName('game_id'),
                    'MAX(' . $db->quoteName('draw_date') . ') AS ' . $db->quoteName('max_draw')
                ])
                ->from($db->quoteName($dbCol))
                ->where($db->quoteName('stateprov_name') . ' = ' . $db->quote($stNameSql))
                ->group($db->quoteName('game_id'));

            $query = $db->getQuery(true)
                ->select('t.*')
                ->from($db->quoteName($dbCol, 't'))
                ->join('INNER', '(' . $sub . ') AS m ON m.game_id = t.game_id AND m.max_draw = t.draw_date')
                ->where($db->quoteName('t.stateprov_name') . ' = :state2')
                ->bind(':state2', $stNameSql);

            $db->setQuery($query);

            return (array) $db->loadObjectList();
        });

    } catch (\Throwable $e) {
        $latestRows = null;
    }

    if (!is_array($latestRows) || empty($latestRows)) {
        // Fallback (no cache): run the same batched query directly
        $db = Factory::getDbo();

        $sub = $db->getQuery(true)
            ->select([
                $db->quoteName('game_id'),
                'MAX(' . $db->quoteName('draw_date') . ') AS ' . $db->quoteName('max_draw')
            ])
            ->from($db->quoteName($dbCol))
            ->where($db->quoteName('stateprov_name') . ' = ' . $db->quote($stNameSql))
            ->group($db->quoteName('game_id'));

        $query = $db->getQuery(true)
            ->select('t.*')
            ->from($db->quoteName($dbCol, 't'))
            ->join('INNER', '(' . $sub . ') AS m ON m.game_id = t.game_id AND m.max_draw = t.draw_date')
            ->where($db->quoteName('t.stateprov_name') . ' = :state2')
            ->bind(':state2', $stNameSql);

        $db->setQuery($query);
        $latestRows = (array) $db->loadObjectList();
    }

    foreach ($latestRows as $row) {
        if (!empty($row->game_id)) {
            $latestByGameId[(string) $row->game_id] = $row;
        }
    }


    /**
     * SKAI / LottoExpert: Crawl-friendly game index (above cards)
     * Production-safe: single query, additive HTML only.
     */
    $qStateName = $db->quote($stName);

$sqlGames = "SELECT DISTINCT `game_id`, `game_name`
             FROM `$dbCol`
             WHERE `stateprov_name` = $qStateName
               AND `game_name` <> ''
             ORDER BY
               CASE
                 WHEN `game_id` = '101' THEN 0
                 WHEN `game_id` = '113' THEN 1
                 ELSE 2
               END,
               `game_name` ASC";
$db->setQuery($sqlGames);
$gameList = (array) $db->loadObjectList();

    if (!empty($gameList)) {
        echo '<nav class="skai-gameindex" aria-label="Games in '.htmlspecialchars($stName, ENT_QUOTES, 'UTF-8').'">';
        echo '<div class="skai-gameindex__head">';
        echo '<h2 class="skai-gameindex__title">Games in '.htmlspecialchars($stName, ENT_QUOTES, 'UTF-8').'</h2>';
        echo '<p class="skai-gameindex__sub">Quick links to results and AI analysis tools.</p>';
        echo '</div>';

        // Phase 5: client-side filter (no DB calls)
        echo '<div class="skai-gameindex__filter">';
        echo '  <label class="skai-gameindex__filterlabel" for="leGameFilter">Filter games</label>';
        echo '  <input id="leGameFilter" class="skai-gameindex__filterinput" type="search" inputmode="search" autocomplete="off" placeholder="Type a game name (e.g., Powerball)">';
        echo '</div>';

        echo '<ul id="leGameIndex" class="skai-gameindex__list">';

foreach ($gameList as $g) {
    $gameId2   = (string) $g->game_id;
    $gameName2 = (string) $g->game_name;

    if ($gameName2 === '') {
        continue;
    }

    /**
     * Match the tile exclusions exactly (so nav links never point to games
     * that won’t render as tiles).
     * Production-safe: only affects nav output.
     */
    if (
        $gameId2 === '101D' || $gameId2 === 'FLDF' || $gameId2 === 'FLBF' || $gameId2 === 'ILI' || $gameId2 === 'ILJ' ||
        $gameId2 === 'CTCW' || $gameId2 === 'CTDW' || $gameId2 === 'INDF' || $gameId2 === 'INCF' || $gameId2 === 'MSCF' ||
        $gameId2 === 'MSDF' || $gameId2 === 'NJDF' || $gameId2 === 'NJCF' || $gameId2 === 'NCCF' || $gameId2 === 'NCDF' ||
        $gameId2 === 'PADW' || $gameId2 === 'PACW' || $gameId2 === 'SCDF' || $gameId2 === 'SCCF' || $gameId2 === 'TNDW' ||
        $gameId2 === 'TNBW' || $gameId2 === 'TNFW' || $gameId2 === 'TXBF' || $gameId2 === 'TXMF' || $gameId2 === 'TXLF' ||
        $gameId2 === 'TXDF' || $gameId2 === 'VACF' || $gameId2 === 'VADF' || $gameId2 === 'CTAW' || $gameId2 === 'CTBW' ||
        $gameId2 === 'FLAF' || $gameId2 === 'FLCF' || $gameId2 === 'ILH'  || $gameId2 === 'ILG'  || $gameId2 === 'INBF' ||
        $gameId2 === 'INAF' || $gameId2 === 'MSAF' || $gameId2 === 'MSBF' || $gameId2 === 'NCBF' || $gameId2 === 'NCAF' ||
        $gameId2 === 'NJBF' || $gameId2 === 'NJAF' || $gameId2 === 'PABW' || $gameId2 === 'PAAW' || $gameId2 === 'SCBF' ||
        $gameId2 === 'SCAF' || $gameId2 === 'TNCW' || $gameId2 === 'TNAW' || $gameId2 === 'TNEW' || $gameId2 === 'TXCF' ||
        $gameId2 === 'TXKF' || $gameId2 === 'TXJF' || $gameId2 === 'TXAF' || $gameId2 === 'VAAF' || $gameId2 === 'VABF' ||
        $gameId2 === 'PAFW' || $gameId2 === 'PAEW' || $gameId2 === 'FLGF' || $gameId2 === 'FLHF' || $gameId2 === 'FLFF' ||
        $gameId2 === 'FLEF' || $gameId2 === 'PAGW' || $gameId2 === 'PAHW' || $gameId2 === 'NJG'  ||
        $gameName2 === 'Evening 3 Double' || $gameName2 === 'Pick 4 Day Wild'
    ) {
        continue;
    }

    // Same destinations as your tiles/cards
    if ($gameId2 === '101') {
            $href  = '/powerball-winning-numbers-analysis-tools?stn=' . rawurlencode($stName);
                $label = 'Powerball';
            } elseif ($gameId2 === '113') {
                $href  = '/megamillions-winning-numbers-analysis-tools?stn=' . rawurlencode($stName);
                $label = 'Mega Millions';
            } else {
    $href  = '/all-us-lotteries/results-analysis?st=' . rawurlencode($stAbrev)
           . '&stn=' . rawurlencode($stName)
           . '&gm=' . rawurlencode($gameName2)
           . '&gmCode=' . rawurlencode($gameId2);                $label = $gameName2;
            }

            $anchorText = $stName . ' ' . $label . ' Results';

            echo '<li class="skai-gameindex__item">';
            echo '<a class="skai-gameindex__link" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">'
               . htmlspecialchars($anchorText, ENT_QUOTES, 'UTF-8')
               . '</a>';
            echo '</li>';
        }

        echo '</ul>';
echo '</nav>';

/* =========================================================
   PHASE 1 – STEP 3: Quick FAQ (Visible + JSON-LD FAQPage)
   Production-safe: additive only
   ========================================================= */

$faqStateName = htmlspecialchars($stName, ENT_QUOTES, 'UTF-8');
$faqStateAbbr = htmlspecialchars(strtoupper($stAbrev), ENT_QUOTES, 'UTF-8');

// Visible FAQ block (above tiles)
echo '<section class="skai-faq" aria-label="Quick FAQ">';
echo '<div class="skai-faq__head">';
echo '<h2 class="skai-faq__title">'.$faqStateName.' Lottery FAQ</h2>';
echo '<p class="skai-faq__sub">Fast answers about results, jackpots, and how to use the AI tools on LottoExpert.</p>';
echo '</div>';

echo '<div class="skai-faq__items">';

// Q1
echo '<details class="skai-faq__item">';
echo '<summary class="skai-faq__q">Where can I find the latest '.$faqStateName.' lottery results?</summary>';
echo '<div class="skai-faq__a"><p>Use the game cards below to view the most recent winning numbers, the next draw date, and any posted jackpot. Tap <strong>AI Lottery Predictions</strong> for a game-by-game results archive and analysis tools.</p></div>';
echo '</details>';

// Q2
echo '<details class="skai-faq__item">';
echo '<summary class="skai-faq__q">Are the jackpots and next draw dates updated automatically?</summary>';
echo '<div class="skai-faq__a"><p>Yes—each game tile shows the latest stored draw results, plus the next draw date and jackpot when available. If a lottery does not publish a jackpot for a game, the jackpot field may be blank.</p></div>';
echo '</details>';

// Q3
echo '<details class="skai-faq__item">';
echo '<summary class="skai-faq__q">What do the “AI Lottery Predictions” tools include?</summary>';
echo '<div class="skai-faq__a"><p>Tools typically include results archives, number frequency insights, skip/hit patterns, heatmaps, and wheel generators. Availability can vary by game.</p></div>';
echo '</details>';

// Q4
echo '<details class="skai-faq__item">';
echo '<summary class="skai-faq__q">Why do some games show an extra ball (Power Play, Megaplier, Fireball, Wild Ball)?</summary>';
echo '<div class="skai-faq__a"><p>Some lotteries add a multiplier or bonus feature. When that data is provided, LottoExpert displays it beneath the winning numbers for quick reference.</p></div>';
echo '</details>';

echo '</div>'; // .skai-faq__items
echo '</section>';

// JSON-LD FAQPage
$faqJson = [
  '@context' => 'https://schema.org',
  '@type'    => 'FAQPage',
  'mainEntity' => [
    [
      '@type' => 'Question',
      'name'  => 'Where can I find the latest ' . $stName . ' lottery results?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text'  => 'Use the game cards on this page to view the latest winning numbers, next draw date, and jackpot when available. Select “AI Lottery Predictions” to open results archives and analysis tools for each game.'
      ]
    ],
    [
      '@type' => 'Question',
      'name'  => 'Are the jackpots and next draw dates updated automatically?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text'  => 'Yes. Each game tile displays the latest stored draw results along with the next draw date and jackpot when available. Some games may not publish jackpot values, so that field can be blank.'
      ]
    ],
    [
      '@type' => 'Question',
      'name'  => 'What do the AI Lottery Predictions tools include?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text'  => 'Tools commonly include results archives, number frequency insights, skip/hit patterns, heatmaps, and wheel generators. Features can vary by game.'
      ]
    ],
    [
      '@type' => 'Question',
      'name'  => 'Why do some games show an extra ball (Power Play, Megaplier, Fireball, Wild Ball)?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text'  => 'Some lotteries add a multiplier or bonus feature. When the data is available, LottoExpert shows it under the winning numbers for that game.'
      ]
    ],
  ]
];

Factory::getDocument()->addCustomTag(
  '<script type="application/ld+json">'
  . json_encode($faqJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
  . '</script>'
);

}
echo '<div class="lotResultWrap">';
    
    foreach($resultList as $rl){
        
        /** GET RESULT LIST **/
        $gameId = $rl->game_id;

        // Phase 3: use batched latest rows (avoids per-game queries)
        if (!isset($latestByGameId[(string) $gameId])) {
            continue;
        }

        $drawResult = array($latestByGameId[(string) $gameId]);

foreach($drawResult as $dr){
            $gState = $dr->stateprov_name;
            $gId = $dr->game_id;
            $gName = $dr->game_name;
            $dDate = $dr->draw_date;
            $dResult = $dr->draw_results;
            $nDraw = $dr->next_draw_date;
            $nJackpot = $dr->next_jackpot;
            $gPhoto = '/images/lottodb/us/'.$stAbrev.'/'.str_replace(" ","-",strtolower($gName)).'.png';

            /** NUMBER POSITIONS **/
            $posOne = $dr->first;
            $posTwo = $dr->second;
            $posThree = $dr->third;
            $posFour = $dr->fourth;
            $posFive = $dr->fifth;
            $posSix = $dr->sixth;
            $posSeven = $dr->seventh;
            $posEight = $dr->eighth;
            $posNine = $dr->nineth;
            $posTen = $dr->tenth;
            $posEleven = $dr->eleventh;
            $posTwelve = $dr->twelveth;
            $posThirteen = $dr->thirtheenth;
            $posFourteen = $dr->fourteenth;
            $posFifteen = $dr->fifteenth;
            $posSixteen = $dr->sixteenth;
            $posSeventeen = $dr->seventeenth;
            $posEighteen = $dr->eighteenth;
            $posNineteen = $dr->nineteenth;
            $posTwenty = $dr->twentieth;
            $posTwentyOne = $dr->twenty_first;
            $posTwentyTwo = $dr->twenty_second;
            $posTwentyThree = $dr->twenty_third;
            $posTwentyFour = $dr->twenty_fourth;
            $posTwentyFive = $dr->twenty_fifth;

            // Daily-picks exclusions (shared by tiles + nav)
            $skaiDailyPickExcludeGameIds = [
                '101D','FLDF','FLBF','ILI','ILJ','CTCW','CTDW','INDF','INCF','MSCF','MSDF','NJDF','NJCF','NCCF','NCDF',
                'PADW','PACW','SCDF','SCCF','TNDW','TNBW','TNFW','TXBF','TXMF','TXLF','TXDF','VACF','VADF','CTAW','CTBW',
                'FLAF','FLCF','ILH','ILG','INBF','INAF','MSAF','MSBF','NCBF','NCAF','NJBF','NJAF','PABW','PAAW','SCBF',
                'SCAF','TNCW','TNAW','TNEW','TXCF','TXKF','TXJF','TXAF','VAAF','VABF','PAFW','PAEW','FLGF','FLHF','FLFF',
                'FLEF','PAGW','PAHW','NJG'
            ];

            $skaiDailyPickExcludeGameNames = [
                'Evening 3 Double',
                'Pick 4 Day Wild'
            ];
            
               /** EXCLUDE DAILY PICKS **/
            if ($gId != '101D' && $gId != 'FLDF' && $gId != 'FLBF' && $gId != 'ILI' && $gId != 'ILJ' && $gId != 'CTCW' && $gId != 'CTDW' && $gId != 'INDF' && $gId != 'INCF' && $gId != 'MSCF' && $gId != 'MSDF' && $gId != 'NJDF' && $gId != 'NJCF' && $gId != 'NCCF' && $gId != 'NCDF' && $gId != 'PADW' && $gId != 'PACW' && $gId != 'SCDF' && $gId != 'SCCF' && $gId != 'TNDW' && $gId != 'TNBW' && $gId != 'TNFW' && $gId != 'TXBF' && $gId != 'TXMF' && $gId != 'TXLF' && $gId != 'TXDF' && $gId != 'VACF' && $gId != 'VADF' && $gId != 'CTAW' && $gId != 'CTBW' && $gId != 'FLAF' && $gId != 'FLCF' && $gId != 'ILH' && $gId != 'ILG' && $gId != 'INBF' && $gId != 'INAF' && $gId != 'MSAF' && $gId != 'MSBF' && $gId != 'NCBF' && $gId != 'NCAF' && $gId != 'NJBF' && $gId != 'NJAF' && $gId != 'PABW' && $gId != 'PAAW' && $gId != 'SCBF' && $gId != 'SCAF' && $gId != 'TNCW' && $gId != 'TNAW' && $gId != 'TNEW' && $gId != 'TXCF' && $gId != 'TXKF' && $gId != 'TXJF' && $gId != 'TXAF' && $gId != 'VAAF' && $gId != 'VABF'  && $gId != 'PAFW' && $gId != 'PAEW' && $gId != 'FLGF' && $gId != 'FLHF' && $gId != 'FLFF' && $gId != 'FLEF' && $gId != 'PAGW' && $gId != 'PAHW' && $gId != 'NJG' && $gName != 'Evening 3 Double' && $gName != 'Pick 4 Day Wild') {
                
echo '<div class="resultWrap lottery-tile">'; // added lottery-tile for unified SKAI card styling
echo '<h2>'.$gName.'</h2>';
/** SET IMAGE CLICKABLE LINK **/
if($gId === '101'){
echo '<a title="View '.$stName.' '.$gName.' Results & Analysis" href="/powerball-winning-numbers-analysis-tools?stn='.rawurlencode($stName).'">';
}else if($gId === '113'){
echo '<a title="View '.$stName.' '.$gName.' Results & Analysis" href="/megamillions-winning-numbers-analysis-tools?stn='.rawurlencode($stName).'">';
}else{
echo '<a title="View '.$stName.' '.$gName.' Results & Analysis" href="/all-us-lotteries/results-analysis?st='.rawurlencode($stAbrev).'&stn='.rawurlencode($stName).'&gm='.rawurlencode($gName).'&gmCode='.rawurlencode($gId).'">';
}
echo '<img class="lotto-logo" src="'.$gPhoto.'" alt="'.$stName.' '.$gName.'" loading="lazy" decoding="async" width="180" height="60">';
echo '</a>';
                
                /** POWERBALL RESULTS **/
                if($gName === 'Powerball'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span>&nbsp;&nbsp;<span class="circlesPb">'.$posSix.'</span><br /><span class="pplay">Power Play: <span class="circlesFb">'.$posSeven.'</span></span></p>';
  
  
  
  
  
                /** UK National, Lunchtime 49s, Teatime 49s IE Daily Million 2pm 9pm Plus 2pm 9pm**/
               }else if(($gName === 'Lunchtime 49s' && $stAbrev === 'uk') || ($gName === 'Teatime 49s' && $stAbrev === 'uk') || ($gName === 'LOTTO' && $stAbrev === 'uk') || ($gName === 'Daily Million 2PM' && $stAbrev === 'ie') || ($gName === 'Daily Million 9PM' && $stAbrev === 'ie') || ($gName === 'Daily Million Plus 2PM' && $stAbrev === 'ie') || ($gName === 'Daily Million Plus 9PM' && $stAbrev === 'ie') || ($gName === 'IrishLotto' && $stAbrev === 'ie') || ($gName === 'Lotto Plus 1' && $stAbrev === 'ie') || ($gName === 'Lotto Plus 2' && $stAbrev === 'ie')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span>&nbsp;&nbsp;<span class="circlesPb">'.$posSeven.'</span></span></p>';
  

                /** EuroMillions **/
                }else if($gName === 'EuroMillions' && $stAbrev === 'uk'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br />&nbsp;&nbsp;<span class="pplay">Lucky Stars: <span class="circlesPb">'.$posSix.'</span><span class="circlesPb">'.$posSeven.'</span></span><br /></p>';
  
  
                /** THUNDERBALL RESULTS **/
                }else if($gName === 'Thunderball' && $stAbrev === 'uk'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br />&nbsp;&nbsp;<span class="pplay">Thunderball: <span class="circlesPb">'.$posSix.'</span><br /></span></p>';
    
  

                /** Health Lottery RESULTS **/
                }else if($gName === 'Health Lottery' && $stAbrev === 'uk'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br />&nbsp;&nbsp;<span class="pplay">Bonus: <span class="circlesPb">'.$posSix.'</span><br /></span></p>';
    
  
  
                   /** Millionaire Rafffle RESULTS **/            
                  }else if($gName === 'Millionaire Raffle' && $stAbrev === 'uk'){
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span style="font-weight:bold; font-size:1.2em;">'.$posOne.'</span></p>';
                  

 
  
  
  
                /** Delaware, IOWA, IDAHO, MAINE, Minnesota, Montana, North Dakota, New Mexico,cash 5 Oklahoma, South Dakota, Tennessee, West-Virginia Lotto America RESULTS**/
                  }else if(($gName === 'Lotto America' && $stAbrev === 'de') || ($gName === 'Lotto America' && $stAbrev === 'ia') || ($gName === 'Lotto America' && $stAbrev === 'id') || ($gName === 'Lotto America' && $stAbrev === 'me') || ($gName === 'Lotto America' && $stAbrev === 'mn') || ($gName === 'Lotto America' && $stAbrev === 'mt') || ($gName === 'Lotto America' && $stAbrev === 'nd') || ($gName === 'Lotto America' && $stAbrev === 'nm') || ($gName === 'Lotto America' && $stAbrev === 'ok') || ($gName === 'Lotto America' && $stAbrev === 'sd') || ($gName === 'Lotto America' && $stAbrev === 'tn') || ($gName === 'Lotto America' && $stAbrev === 'wv') || ($gName === 'Lotto America' && $stAbrev === 'ks')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Star Ball:  <span class="circlesFb">'.$posSix.'</span></span></p>';
               
               
                
                /** Cash Ball RESULTS **/
                }else if($gName === 'Super Cash' && $stAbrev === 'ks'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Cash Ball: <span class="circlesFb">'.$posSix.'</span></span></p>';
                
                /** Cash Ball RESULTS **/
                }else if($gName === 'Cash Ball' && $stAbrev === 'ky'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /><span class="pplay">Cash Ball: <span class="circlesFb">'.$posFive.'</span></span></p>';
                
                /** Big Sky Bonus RESULTS **/
                }else if($gName === 'Big Sky Bonus' && $stAbrev === 'mt'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /><span class="pplay">Bonus: <span class="circlesFb">'.$posFive.'</span></span></p>';
                
                /** New Jersey Cash 5 RESULTS **/
                }else if($gName === 'Cash 5' && $stAbrev === 'nj'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Xtra: <span class="circlesFb">'.$posSix.'</span></p>';
                
                /** SuperLotto Plus RESULTS **/
                }else if($gName === 'SuperLotto Plus'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Mega Ball: <span class="circlesFb">'.$posSix.'</span></span></p>';
                
                /** MEGA MILLIONS RESULTS **/
              //  }else if($gName === 'Mega Millions'){
                }else if($gId === '113'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span>&nbsp;&nbsp;<span class="circlesPb">'.$posSix.'</span><br /><span class="pplay">Megaplier: <span class="circlesFb">'.$posSeven.'</span></span></p>';
                
                /** Bank a Million GAMES **/
                }else if($gName === 'Bank a Million'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><br /><span class="pplay">Bonus: <span class="circlesFb">'.$posSeven.'</span></span></p>';
                
                /** Cash4Life GAMES **/
                }else if($gName === 'Cash4Life'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Cash Ball: <span class="circlesFb">'.$posSix.'</span></span></p>';
                
                /** Lucky For Life GAMES **/
                }else if($gName === 'Lucky For Life'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Cash Ball: <span class="circlesFb">'.$posSix.'</span></span></p>';

           

                /** Arkansas LOTTO GAME - OSCAR ADDED 06/13/23 **/
                }else if($gName === 'LOTTO' && $stName === 'Arkansas'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><br /><span class="pplay">Bonus:  <span class="circlesFb">'.$posSeven.'</span></span></p>';
                
                
                /** LOTTO GAMES **/
                }else if(($gName === 'Lotto' && $stName != 'Illinois' && $stName != 'New York') || ($gName === 'Hoosier Lotto' && $stName === 'Indiana')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span></p>';
                
                                              
                /** LOTTO Double Play GAMES Pick 5 North Carolina Cash 5 Double play Modified and added by Oscar uk **/
                }else if($gName === 'Double Play' && $stName === 'North Carolina'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span></p>';
                


                /** LOTTO Double Play GAMES Pick 6 **/
                }else if($gName === 'Double Play' && $stName != 'Illinois' && $stName != 'New York'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span></p>';
                
                
                /** Illinois LOTTO GAMES **/
                }else if($gName === 'Lotto' && $stName === 'Illinois'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><br /><span class="pplay">Extra Shot: <span class="circlesFb">'.$posSeven.'</span></span></p>';



                /** Wild Money GAMES **/
                }else if($gName === 'Wild Money' && $stName === 'Rhode Island'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Extra: <span class="circlesFb">'.$posSix.'</span></span></p>';
                
                /** Palmetto Cash 5 GAMES **/
                }else if($gName === 'Palmetto Cash 5' && $stName === 'South Carolina'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /></p>';
                
                /** New York LOTTO GAMES **/
                }else if($gName === 'Lotto' && $stName === 'New York'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><br /><span class="pplay">Bonus: <span class="circlesFb">'.$posSeven.'</span></span></p>';
                    
                
                /** Bonus Match 5, Tennessee Cash GAMES **/
                }else if(($gName === 'Bonus Match 5' && $stName === 'Maryland') || ($gName === 'Tennessee Cash' && $stName === 'Tennessee') || ($gName === 'Loto Plus' && $stName === 'Puerto Rico')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Bonus: <span class="circlesFb">'.$posSix.'</span></span></p>';
    
                    
                    /** Bonus Texas Two Step GAMES **/
                }else if($gName === 'Texas Two Step' && $stName === 'Texas'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /><span class="pplay">Bonus: <span class="circlesFb">'.$posFive.'</span></span></p>';
                    
    
                /** Megabucks Plus GAMES **/
                }else if(($gName === 'Megabucks Plus' && $stName === 'Maine') || ($gName === 'Megabucks Plus' && $stName === 'New Hampshire') || ($gName === 'Megabucks Plus' && $stName === 'Vermont')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Megaball: <span class="circlesFb">'.$posSix.'</span></span></p>';
                    
                
                /** (STRAIGHT 2) DC 2 Midday, DC 2 Evening GAMES, Puerto Rico Pega 2 Games **/
                }else if($gName === 'DC 2 1:50PM' || $gName === 'DC 2 7:50PM' || $gName === 'Pega 2 Day' || $gName === 'Pega 2 Noche'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span></p>';
                    
                
                /** (STRAIGHT 3) Cash 3 Midday, Cash 3 Evening, Daily 3 Midday, Daily 3 Evening, Play3 Day, Play3 Night, DC 3 Midday, DC 3 Evening, Play 3 Day, Play 3 Night, Cash 3 Night, Evening 3 Double, MyDay, Cash 3 Morning, Daily Game, Daily 3 GAMES **/
                }else if($gName === 'Daily Game' || $gName === 'DC 3 Midday' || $gName === 'DC 3 Evening' || $gName === 'Pega 3 Day' || $gName === 'Pega 3 Noche' || $gName === 'Evening 3 Double' || $gName === 'MyDay' || ($gName === 'Numbers Midday' && $stName === 'New York') || ($gName === 'Numbers Evening' && $stName === 'New York') || $gName === 'DC 3 7:50PM' || $gName === 'DC 3 1:50PM' || $gName === 'DC 3 11:30PM'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span></p>';
                    
                
                /** (STRAIGHT 4) Cash 4 Midday, Cash 4 Evening, Daily 4 GAMES, Play4 Day, DC 4 Midday, DC 4 Evening, Play 4 Day, Play 4 Night, Cash 4 Night, Daily 4 Midday, Daily 4 Evening, 2 By 2, Numbers Midday, Numbers Evening, Win 4 Midday, Win 4 Evening, Win for Life, Cash 4 Morning, Cash 4 Midday, Cash 4 Evening, Match 4 **/
                }else if($gName === 'DC 4 Midday' || $gName === 'DC 4 Evening' || $gName === 'Pega 4 Day' || $gName === 'Pega 4 Noche' || $gName === 'Play 4 Day' || $gName === 'Play 4 Night' || $gName === 'Cash 4 Night' || $gName === '2 By 2' || ($gName === 'Numbers Midday' && $stName != 'New York') || ($gName === 'Numbers Evening' && $stName != 'New York') || $gName === 'Win 4 Midday' || $gName === 'Win 4 Evening' || $gName === 'Win for Life'|| $gName === 'Match 4' || $gName === 'DC 4 7:50PM' || $gName === 'DC 4 1:50PM' || $gName === 'DC 4 11:30PM'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span></p>';
                    
                
                /** (STRAIGHT 5) Fantasy 5, Natural State Jackpot, Cash 5, DC 5 Midday, DC 5 Evening, Georgia FIVE Midday, Georgia FIVE Evening, Idaho Cash, 5 Star Draw, Weekly Grand, LuckyDay Lotto Midday, LuckyDay Lotto Evening, Easy 5, MassCash, Gimme 5, World Poker Tour, Poker Lotto, Gopher 5, NORTH5, Show Me Cash, Montana Cash, Roadrunner Cash, Take 5 Midday, Take 5 Evening, Rolling Cash 5, Treasure Hunt, Dakota Cash, Hit 5, Badger 5 Cowboy Draw GAMES **/
                }else if($gName === 'Fantasy 5' || $gName === 'Fantasy 5 Evening' || $gName === 'Fantasy 5 Midday' || $gName === 'Natural State Jackpot' || ($gName === 'Cash 5' && $stAbrev != 'nj') || ($gName === 'Cash 5' && $stAbrev != 'nc') || ($gId === 'TX2' && $stAbrev != 'tx') || $gName === 'DC 5 Midday' || $gName === 'DC 5 Evening' || $gName === 'Georgia FIVE Midday' || $gName === 'Georgia FIVE Evening' || $gName === 'Idaho Cash' || $gName === '5 Star Draw' || $gName === 'Weekly Grand' || $gName === 'LuckyDay Lotto Midday' || $gName === 'LuckyDay Lotto Evening' || $gName === 'Easy 5' || $gName === 'MassCash' || $gName === 'Gimme 5' || $gName === 'World Poker Tour' || $gName === 'Poker Lotto' || $gName === 'Gopher 5' || $gName === 'NORTH5' || $gName === 'Show Me Cash' || $gName === 'Montana Cash' || $gName === 'Roadrunner Cash' || $gName === 'Take 5 Midday' || $gName === 'Take 5 Evening' || $gName === 'Rolling Cash 5' || $gName === 'Treasure Hunt' || $gName === 'Dakota Cash' || $gName === 'Hit 5' || $gName === 'Badger 5' || $gName === 'Cowboy Draw' || $gName === 'DC 5 7:50PM' || $gName === 'DC 5 1:50PM'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span></p>';
                    
                
                /** (STRAIGHT 6) Jackpot Triple Play, Triple Twist, Lotto Plus, Multi-Win Lotto, Jumbo Bucks Lotto, Megabucks Doubler, MultiMatch, Classic Lotto 47, Classic Lotto, Megabucks, Match 6 Lotto, Super Cash, Cash 25, GAMES **/
                }else if($gName === 'Jackpot Triple Play' || $gName === 'Triple Twist' || $gName === 'Lotto Plus' || $gName === 'Multi-Win Lotto' || $gName === 'Megabucks Doubler' || $gName === 'MultiMatch' || $gName === 'Classic Lotto 47' || $gName === 'Classic Lotto' || $gName === 'Megabucks' || $gName === 'Match 6 Lotto' || $gName === 'Super Cash' || $gName === 'Cash 25'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span></p>';
                    
                
                /** (STRAIGHT 8) Lucky Lines GAMES **/
                }else if($gName === 'Lucky Lines'){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><span class="circles">'.$posSeven.'</span><span class="circles">'.$posEight.'</span></p>';
                    
                
                /** ALL All or Nothing Midday, All or Nothing Evening GAMES **/
                }else if(($gName === 'All or Nothing Evening' && $stName === 'Wisconsin') || ($gName === 'All or Nothing Midday' && $stName === 'Wisconsin')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><span class="circles">'.$posSeven.'</span><span class="circles">'.$posEight.'</span><span class="circles">'.$posNine.'</span><span class="circles">'.$posTen.'</span><span class="circles">'.$posEleven.'</span></p>';
                    
                
                /** ALL (TX) All or Nothing Midday, All or Nothing Evening GAMES **/
                }else if(($gName === 'All or Nothing Morning' && $stName === 'Texas') || ($gName === 'All or Nothing Day' && $stName === 'Texas') || ($gName === 'All or Nothing Evening' && $stName === 'Texas') || ($gName === 'All or Nothing Night' && $stName === 'Texas')){
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><span class="circles">'.$posSeven.'</span><span class="circles">'.$posEight.'</span><span class="circles">'.$posNine.'</span><span class="circles">'.$posTen.'</span><span class="circles">'.$posEleven.'</span><span class="circles">'.$posTwelve.'</span></p>';
                    
                
                /** ALL Indiana Quick Draw Midday, Quick Draw Evening GAMES **/
                }else if(($gName === 'Quick Draw Midday' && $stName === 'Indiana') || ($gName === 'Quick Draw Evening' && $stName === 'Indiana')){
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><span class="circles">'.$posSeven.'</span><span class="circles">'.$posEight.'</span><span class="circles">'.$posNine.'</span><span class="circles">'.$posTen.'</span><span class="circles">'.$posEleven.'</span><span class="circles">'.$posTwelve.'</span><span class="circles">'.$posThirteen.'</span><span class="circles">'.$posFourteen.'</span><span class="circles">'.$posFifteen.'</span><span class="circles">'.$posSixteen.'</span><span class="circles">'.$posSeventeen.'</span><span class="circles">'.$posEighteen.'</span><span class="circles">'.$posNineteen.'</span><span class="circles">'.$posTwenty.'</span><span class="circles">'.$posTwentyOne.'</span><br /></p>';
                
                
                /** Michigan Keno GAME **/
                }else if($gId === 'MI3'){
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><span class="circles">'.$posSix.'</span><span class="circles">'.$posSeven.'</span><span class="circles">'.$posEight.'</span><span class="circles">'.$posNine.'</span><span class="circles">'.$posTen.'</span><span class="circles">'.$posEleven.'</span><span class="circles">'.$posTwelve.'</span><span class="circles">'.$posThirteen.'</span><span class="circles">'.$posFourteen.'</span><span class="circles">'.$posFifteen.'</span><span class="circles">'.$posSixteen.'</span><span class="circles">'.$posSeventeen.'</span><span class="circles">'.$posEighteen.'</span><span class="circles">'.$posNineteen.'</span><span class="circles">'.$posTwenty.'</span><span class="circles">'.$posTwentyOne.'</span><span class="circles">'.$posTwentyTwo.'</span><br /></p>';
   
   
      
      
    /** Oscar Tx3 pick 2 merge, Pick 2 With FB **/
                }else if($gId === 'FLF'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLFF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><br /></p>';
                    echo '<p class="lstResult"> Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
               }else if($gId === 'FLE'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLEF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><br /></p>';
                    echo '<p class="lstResult"> Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
               }else if($gId === 'PAG'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PAGW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><br /></p>';
                    echo '<p class="lstResult"> Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
               }else if($gId === 'PAH'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PAHW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><br /></p>';
                    echo '<p class="lstResult"> Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

   
      
   
    /** Oscar T pick 3 merge, Pick 3 With FB **/
                }else if($gId === 'CTA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'CTAW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                 }else if($gId === 'CTB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'CTBW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                  }else if($gId === 'FLA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
         
         
                
                  }else if($gId === 'FLC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === '121'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'ILH' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === '120'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'ILG' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'INB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'INBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'INA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'INAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'MSA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'MSAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'MSB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'MSBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'NCB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NCBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'NCA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NCAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'NJB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NJBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'NJA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NJAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'PAB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PABW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'PAA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PAAW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'SCB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'SCBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'SCA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'SCAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TNC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TNCW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TNA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TNAW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TNE'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TNEW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TXC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TXK'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXKF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TXJ'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXJF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'TXA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'VAA'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'VAAF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

         
                
                  }else if($gId === 'VAB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'VABF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
                    echo '<p class="lstResult"> Wild Ball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

   
   
        /** Oscar T pick 4 merge, Pick 4 Midday / Evening With FB **/
                }else if($gId === 'FLD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLDF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                }else if($gId === 'FLB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';


                }else if($gId === '122'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'ILI' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                }else if($gId === '123'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'ILJ' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
                
                 }else if($gId === 'CTC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'CTCW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Play4 Day Wild: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

               }else if($gId === 'CTD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'CTDW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
                
}else if($gId === 'IND'){

    $db = Factory::getDbo();
    // Match the same draw date as the base game + use draw_results (keeps leading zeros)
    $qDate  = $db->quote(date('Y-m-d', strtotime($dDate)));
    $qState = $db->quote($stName);
    $sqlfb = "SELECT `draw_results`
              FROM `$dbCol`
              WHERE `stateprov_name` = $qState
                AND `game_id` = 'INDF'
                AND DATE(`draw_date`) = $qDate
              ORDER BY `id` DESC
              LIMIT 1";
    $db->setQuery($sqlfb);
    $db->execute();

    $fbRaw    = (string) $db->loadResult();
    $fbResult = preg_replace('/\D+/', '', $fbRaw);

    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.htmlspecialchars($fbResult, ENT_QUOTES, 'UTF-8').'</span><br /></p>';
      

}else if($gId === 'INB'){

    $db = Factory::getDbo();
    $qDate  = $db->quote(date('Y-m-d', strtotime($dDate)));
    $qState = $db->quote($stName);
    $sqlfb = "SELECT `draw_results`
              FROM `$dbCol`
              WHERE `stateprov_name` = $qState
                AND `game_id` = 'INBF'
                AND DATE(`draw_date`) = $qDate
              ORDER BY `id` DESC
              LIMIT 1";
    $db->setQuery($sqlfb);
    $db->execute();

    $fbRaw    = (string) $db->loadResult();
    $fbResult = preg_replace('/\D+/', '', $fbRaw);

    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.htmlspecialchars($fbResult, ENT_QUOTES, 'UTF-8').'</span><br /></p>';


               
                }else if($gId === 'INC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'INCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
               
                }else if($gId === 'MSC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'MSCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
               
                }else if($gId === 'MSD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'MSDF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                }else if($gId === 'NJD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NJDF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
                   }else if($gId === 'NJC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NJCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'NCC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NCCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'NCD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'NCDF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'PAD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PADW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
                   }else if($gId === 'PAC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PACW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'SCD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'SCDF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                
                   }else if($gId === 'SCC'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'SCCF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'TND'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TNDW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'TNB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TNBW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'TNF'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TNFW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                


                   }else if($gId === 'TXB'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXBF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'TXM'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXMF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'TXL'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXLF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'TXD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'TXDF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

}else if($gId === 'VAC'){

    $db = Factory::getDbo();
    $qDate  = $db->quote(date('Y-m-d', strtotime($dDate)));
    $qState = $db->quote($stName);
    $sqlfb = "SELECT `draw_results`
              FROM `$dbCol`
              WHERE `stateprov_name` = $qState
                AND `game_id` = 'VACF'
                AND DATE(`draw_date`) = $qDate
              ORDER BY `id` DESC
              LIMIT 1";
    $db->setQuery($sqlfb);
    $db->execute();

    $fbRaw    = (string) $db->loadResult();
    $fbResult = preg_replace('/\D+/', '', $fbRaw);

    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.htmlspecialchars($fbResult, ENT_QUOTES, 'UTF-8').'</span><br /></p>';


}else if($gId === 'NCA'){ // NC Pick 3 (Day) -> NCAF
    $db = Factory::getDbo();
    $qDate  = $db->quote(date('Y-m-d', strtotime($dDate)));
    $qState = $db->quote($stName);
    $sqlfb = "SELECT `draw_results`
              FROM `$dbCol`
              WHERE `stateprov_name` = $qState
                AND `game_id` = 'NCAF'
                AND DATE(`draw_date`) = $qDate
              ORDER BY `id` DESC
              LIMIT 1";
    $db->setQuery($sqlfb);
    $db->execute();
    $fbRaw    = (string) $db->loadResult();
    $fbResult = preg_replace('/\D+/', '', $fbRaw);
    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.htmlspecialchars($fbResult, ENT_QUOTES, 'UTF-8').'</span><br /></p>';

}else if($gId === 'NCB'){ // NC Pick 3 (Eve) -> NCBF
    $db = Factory::getDbo();
    $qDate  = $db->quote(date('Y-m-d', strtotime($dDate)));
    $qState = $db->quote($stName);
    $sqlfb = "SELECT `draw_results`
              FROM `$dbCol`
              WHERE `stateprov_name` = $qState
                AND `game_id` = 'NCBF'
                AND DATE(`draw_date`) = $qDate
              ORDER BY `id` DESC
              LIMIT 1";
    $db->setQuery($sqlfb);
    $db->execute();
    $fbRaw    = (string) $db->loadResult();
    $fbResult = preg_replace('/\D+/', '', $fbRaw);
    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><br /></p>';
    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.htmlspecialchars($fbResult, ENT_QUOTES, 'UTF-8').'</span><br /></p>';

}else if($gId === 'NCD'){ // NC Pick 4 (Eve) -> NCDF
    $db = Factory::getDbo();
    $qDate  = $db->quote(date('Y-m-d', strtotime($dDate)));
    $qState = $db->quote($stName);
    $sqlfb = "SELECT `draw_results`
              FROM `$dbCol`
              WHERE `stateprov_name` = $qState
                AND `game_id` = 'NCDF'
                AND DATE(`draw_date`) = $qDate
              ORDER BY `id` DESC
              LIMIT 1";
    $db->setQuery($sqlfb);
    $db->execute();
    $fbRaw    = (string) $db->loadResult();
    $fbResult = preg_replace('/\D+/', '', $fbRaw);
    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.htmlspecialchars($fbResult, ENT_QUOTES, 'UTF-8').'</span><br /></p>';








                   }else if($gId === 'VAD'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'VADF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                


                   }else if($gId === 'FLH'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLHF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                   }else if($gId === 'FLG'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'FLGF' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /></p>';
                    echo '<p class="lstResult">Fireball: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                



                   }else if($gId === 'PAE'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PAEW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /></p>';
                    echo '<p class="lstResult">Wild : <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                



                   }else if($gId === 'PAF'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = 'PAFW' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /></p>';
                    echo '<p class="lstResult">Wild: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                

                  /** Oscar T Lotto America joining with All Star Bonus  **/
                /**   }else if($gName === 'Lotto America' && $gId === '135'){
                    
                    $db = Factory::getDbo();
                    $sqlfb = "SELECT `draw_results` FROM `$dbCol` WHERE `stateprov_name` = '$stName' AND `game_id` = '136' ORDER BY `draw_date` DESC LIMIT 1";
                    $db->setQuery($sqlfb);
                    $db->execute();
                    
                    $fbResult = $db->loadResult();
                    
                    echo '<p class="lstResult">Last Result: '.date('m-d-Y',strtotime($dDate)).'<br /><br /><span class="circles">'.$posOne.'</span><span class="circles">'.$posTwo.'</span><span class="circles">'.$posThree.'</span><span class="circles">'.$posFour.'</span><span class="circles">'.$posFive.'</span><br /><span class="pplay">Star Ball:  <span class="circlesFb">'.$posSix.'</span></span></p>';
                    echo '<p class="lstResult">All Star Bonus: <span class="circlesPb">'.$fbResult.'</span><br /></p>';
                **/

 
                
                                /** ALL OTHER GAMES **/
               }else if (preg_match('/^(Cash\s*Pop|Pop|Pick\s*1)/i', $gName)) {

                    // Single-ball: render strictly from draw_results to keep any leading zero
                    $num = trim((string)$dResult);
                    $num = preg_replace('/\D+/', '', $num); // normalize (e.g., "03 " -> "03")
                    echo '<p class="lstResult">Last Result: ' . date('m-d-Y', strtotime($dDate)) . '<br /><span class="circles">' . htmlspecialchars($num, ENT_QUOTES, 'UTF-8') . '</span></p>';
                }else{
                    if($gId != 'AR2'){ /** EXCLUDE AR LOTTO **/
                        // Robust split for -, space, comma, or dot
                        $parts = preg_split('/\s*[-,\s\.]\s*/', trim((string)$dResult));
                        $parts = array_filter($parts, 'strlen');
                        $out = '<span class="circles">' . implode('</span><span class="circles">', array_map(function($p){return htmlspecialchars($p, ENT_QUOTES, 'UTF-8');}, $parts)) . '</span>';
                        echo '<p class="lstResult">Last Result: ' . date('m-d-Y', strtotime($dDate)) . '<br />' . $out . '</p>';
                    }else{
                        echo '<p class="lstResult">Last Result: ' . date('m-d-Y', strtotime($dDate)) . '<br /><span>' . htmlspecialchars($dResult, ENT_QUOTES, 'UTF-8') . '</span></p>';
                    }
               }

if($gId === 'MI3' || $gId === 'IN9' || $gId === 'IN7' || $gId === 'WA4' || $gId === 'NY3'){ /** ADD SOME GAP FOR KENO GAMES **/
    echo '<p class="nDraw" style="margin-top:60px">Next Draw</p>';
}else{
    // Added inline top margin so "Next Draw" sits clearly below Power Play / Megaplier / Cash Ball
    echo '<p class="nDraw" style="margin-top:35px">Next Draw</p>';
}
echo '<h3 class="nDrawDate">'.date('m-d-Y',strtotime($nDraw)).'</h3>';

/** IF NO JACKPOT INFO THEN DISPLAY NOTHING **/
if($nJackpot != '' && $nJackpot > '0' && $nJackpot != 'n/a'){
    echo '<p class="nDraw">Next Jackpot</p>';
    echo '<h3 class="nJackpot">$'.number_format((float)$nJackpot, 0, '.', ',').'</h3>';
} /** EO IF NO JACKPOT INFO **/

/** SET RESULTS AND ANALYSIS LINK – wrapped in lotto-actions for SKAI tile layout **/
echo '<div class="lotto-actions">';
if($gId === '101'){
echo '<a title="View '.$stName.' '.$gName.' Results & Analysis" class="rnaBtn pbHistoryBtn" href="/powerball-winning-numbers-analysis-tools?stn='.rawurlencode($stName).'">';
}else if($gId === '113'){
echo '<a title="View '.$stName.' '.$gName.' Results & Analysis" class="rnaBtn pbHistoryBtn" href="/megamillions-winning-numbers-analysis-tools?stn='.rawurlencode($stName).'">';
}else{
echo '<a title="View '.$stName.' '.$gName.' Results & Analysis" class="rnaBtn pbHistoryBtn" href="/all-us-lotteries/results-analysis?st='.rawurlencode($stAbrev).'&stn='.rawurlencode($stName).'&gm='.rawurlencode($gName).'&gmCode='.rawurlencode($gId).'">';
}

echo 'AI Lottery Predictions';
echo '</a>';
echo '</div>'; // .lotto-actions
echo '</div>';
            } /** EO EXCLUDE DAILY PICKS **/
        } /** EO FOREACH **/
    }
    
/** INJECT FOOTER DESCRIPTION TEXT (query builder + prepared output) **/
if (!empty($stAbrevSql)) {
    $qState = strtoupper($stAbrevSql);

    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select($db->quoteName('footertext'))
        ->from($db->quoteName('#__lottostates_words'))
        ->where($db->quoteName('statename') . ' = :statename')
        ->where($db->quoteName('state') . ' = 1')
        ->bind(':statename', $qState);

    $db->setQuery($query);
    $fottext = (string) $db->loadResult();

    if ($fottext !== '') {
        echo '<div class="ftextwrapper">';
        echo JHtml::_('content.prepare', $fottext);
        echo '</div>';
    }
}

/** Close tiles wrapper **/

echo '</div>'; // .lotResultWrap

} else {
    echo 'No lottery found for this State';
}

echo '</div>'; /** EO MAIN RIGHT COLUMN sidebar adjustment**/
/** EO THE COLUMN SYSTEM **/
echo '</div>';
