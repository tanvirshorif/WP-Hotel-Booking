<?php

class HB_SW_Curreny_Switcher {

	/**
	 * shortcode name
	 * @var string
	 */
	protected $_shortcode_name;

	/**
	 * template file name
	 * @var string
	 */
	protected $_template;

	public function __construct() {
		$this->_shortcode_name = 'hotel_booking_curreny_switcher';

		$this->_template = 'shortcodes/switcher.php';

		add_shortcode( $this->_shortcode_name, array( $this, 'render' ) );
	}

	/**
	 * render shortcode
	 * @param  [type] $att     [description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function render( $att, $content = null )
	{

		$settings = hb_settings();

		if( ! $settings->get( 'currencies_enable' ) || ! $settings->get( 'currencies_multiple_allowed' ) )
			return;

		$html = array();

		$html[] = $this->before();

		if( $this->_template )
		{
			ob_start();
			hb_get_template( $this->_template, $this->parse_attr( $att ) );
			$html[] = ob_get_clean();
		}

		$html[]	= $this->after();

		return implode('', $html);

	}

	/**
	 * parse attr
	 *
	 * @param  array $atts array
	 *
	 * @return array       array
	 */
	public function parse_attr( $atts = array() ) {
		if ( isset( $atts['currencies'] ) ) {
			$atts['currencies'] = explode( ',', $atts['currencies'] );
		}

		return $atts;
	}

	/**
	 * before shortcode render
	 * @return html
	 */
	public function before()
	{
		return '<div class="hb_currency_switcher_wrap '.$this->_shortcode_name.'">';
	}

	/**
	 * after shortcode render
	 * @return html
	 */
	public function after()
	{
		return '</div>';
	}

}

new HB_SW_Curreny_Switcher();