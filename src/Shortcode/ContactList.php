<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class ContactList extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();

    $leagueService = ServiceBroker::getLeagueService();
    $playerService = ServiceBroker::getPlayerService();

    $leagues = $leagueService->getActive();
    $leagueadmins = $playerService->getLeagueAdmins();
    $captains = $playerService->getCaptainsContactData();
    $vicecaptains = $playerService->getViceCaptainsContactData();
    $players = array_merge($leagueadmins, $captains, $vicecaptains);

    $leagueMap = array();
    foreach ($leagues as $league) {
      $leagueMap[$league->getCode()] = $league;
    }

    $contactMap = array();
    foreach ($players as $player) {

      if ($player->league_short) {
        if (!isset($contactMap[$player->league_short])) {
          $contactMap[$player->league_short]['league'] = $leagueMap[$player->league_short];
        }
        $contactMap[$player->league_short]['players'][] = $player;
      } else if ($player->role === 'ligaleitung') {
        if (!isset($contactMap['ligaleitung'])) {
          $contactMap['ligaleitung']['league'] = array(
            'id' => 'ligaleitung',
            'code' => 'ligaleitung',
            'name' => 'Ligaleitung'
          );
        }

        $contactMap['ligaleitung']['players'][] = $player;
      }
    }

    usort($contactMap, array(__CLASS__, "cmp",));

    return $kkl_twig->render(
      self::$TEMPLATE_PATH . '/contact_list.twig',
      array(
        'leagues' => $leagues,
        'contactMap' => $contactMap
      )
    );
  }

  private static function cmp($a, $b) {
    return strcmp($a->team, $b->team);
  }
}