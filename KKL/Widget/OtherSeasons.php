<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "KKL_Widget_OtherSeasons" );' ) );

class KKL_Widget_OtherSeasons extends WP_Widget {

	private $tpl;

	public function __construct() {
		
		global $kkl_twig;

		parent::__construct(
	 		'KKL_Widget_OtherSeasons', // Base ID
			'KKL Other Seasons', // Name
			array( 'description' => __( 'Shows the other seasons games for the defined League', 'kkl-ligatool' ), ) // Args
		);

		$this->tpl = $kkl_twig;
	}

	public function widget( $args, $instance ) {

		extract( $args );


		$db = new KKL_DB();

		$league_id = $instance['league'];
		if(!$league_id) {
			$context = KKL::getContext();
			$league = $context['league'];
		}else{
			$league = $db->getLeague($league_id);
		}

		$seasons = $db->getSeasonsByLeague($league->id);
		foreach($seasons as $season) {
			$season->link = KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date))));
		}
		if(!empty($seasons)) {
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;	
			echo $this->tpl->render('widgets/other_seasons.tpl', array('seasons' => $seasons));
			echo $after_widget;	
		}

	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['league'] = strip_tags( $new_instance['league'] );

		return $instance;
	}

	public function form( $instance ) {
		$db = new KKL_DB();
		$leagues = $db->getLeagues();

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		if ( isset( $instance[ 'league' ] ) ) {
			$league = $instance[ 'league' ];
		}else {
			$league = __( 'Set League', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<br/><br/>
		<label for="<?php echo $this->get_field_id( 'league' ); ?>"><?php _e( 'League:' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'league' ); ?>" name="<?php echo $this->get_field_name( 'league' ); ?>">
			<option value=""><?php echo __('get from context', 'kkl-ligatool'); ?></option>
			<?php
				foreach ($leagues as $l) {
                                        $selected = false;
                                        if($l->id == $league) $selected = true;
                                        echo "<option value=\"$l->id\"";
                                        if($selected) echo ' selected="selected"';
                                        echo ">";
                                        echo $l->name;
                                        echo "</option>";
                                }
			?>
		</select>
		</p>
		<?php 
	}
	
}
