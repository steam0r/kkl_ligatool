<?php

namespace KKL\Ligatool;

use stdClass;

class KoelnClub {
    
    /**
     * render Template /w shortcode "koelncup_table"
     * @param $args
     * @param $content
     * @param $tag
     * @return string
     */
    public static function renderTable($args, $content, $tag) {
        $kkl_twig = Template\Service::getTemplateEngine();
        $context = Plugin::getContext();
        
        return $kkl_twig->render('shortcodes/table_koelncup.twig', array(
            'context' => $context,
            'rankings' => array(
                'ranks' => array() // TODO hier müssen die ranks aus der berechneten Tabelle rein
            )
        ));
    }
    
    
    /**
     * render Template /w shortcode "koelncup_games"
     * @param $args
     * @param $content
     * @param $tag
     * @return string
     */
    public static function renderGames($args, $content, $tag) {
        $kkl_twig = Template\Service::getTemplateEngine();
        $db = new DB\Wordpress();
        
        $sql = "SELECT * FROM matches WHERE game_day_id = '" . esc_sql($args['game_day']) . "'";
        $schedule = new stdClass;
        $schedule->matches = $db->getDb()->get_results($sql);
        
        foreach($schedule->matches as $match) {
            $match->home = $db->getTeam($match->home_team);
            $match->away = $db->getTeam($match->away_team);
        }
        
        $schedules[] = $schedule;
        
        return $kkl_twig->render('shortcodes/games_koelncup.twig', array(
            'schedules' => $schedules
        ));
    }
    
}