<?php
/**
* @package JooAg Countdown
* @version 3.x.0 Beta
* @for Joomla 3.3+
* @author Joomla Agentur - http://www.joomla-agentur.de
* @copyright Copyright (c) 2009 - 2015 Joomla-Agentur All rights reserved.
* @license GNU General Public License version 2 or later;
* @description A small Plugin to Calculate the Days for a specific Date
* @thanksto Thanks to Guido De Gobbis from http://joomtools.de for his great contributions!
*/
defined('_JEXEC') or die;

class plgContentJooagCountDays extends JPlugin
{
	public function __construct(&$subject, $config)	{
		$app = JFactory::getApplication();
	        if($app->isAdmin()){
	            return;
	        }
		parent::__construct($subject, $config);
	}
	
	public function onContentPrepare($context, &$article, &$params, $page=0 )
	{

		if ( JString::strpos( $article->text, 'dateToNow' ) === false ) {
			return true;
		}

		$regexDTN = "#{dateToNow}(.*?){/dateToNow}#s";

		$article->text = preg_replace_callback( $regexDTN, array(&$this,'plgCountDaysOutput'), $article->text );
		return true;
	}
	
	protected function plgCountDaysOutput ( &$matches) 
	{
		$date = $matches[1];
		$datetime1 = new DateTime($date);
		$datetime2 = new DateTime('now');
		$interval = $datetime1->diff($datetime2);
		$days = $interval->format('%a');
		if($days == 1){
		$suffix = ( $interval->invert ? ' Tag noch' : ' Tag alt' );
		}
		else{
		$suffix = ( $interval->invert ? ' Tage noch' : ' Tage alt' );
		}
		$btn = '';
		
		if($this->params->get('class_red') >= $days or $this->params->get('class_red') < $days){$btn='btn-danger';}
		if($this->params->get('class_orange') >= $days ){$btn='btn-warning';}
		if($this->params->get('class_green') >= $days){$btn='btn-success';}
		
		
		if($interval->invert == 1){$btn='';}
		
		$years = floor($days / 365);
		if($years != 0 and $this->params->get('show_years') == 1){
			$days = $days - ($years * 365);
			if($years == 1){$years .= ' Jahr';}
			else{$years .= ' Jahre';}
		}
		else{$years ='';}
		
		$months = floor($days / 30);
		if($months != 0  and $this->params->get('show_months') == 1){
			$days = $days - ($months * 30);
			if($months == 1){$months .= ' Monat';}
			else{$months .= ' Monate';}
		}
		else{$months ='';}
		if($days == 0){$days = '';}
		if($days != 0){
			if($days == 1){$days .= ' Tag';}
			else{$days .= ' Tage';}
		}
		
		if($this->params->get('show_date') == 0){$date = '';}else{$date = $date; }
		$suffix = ( $interval->invert ? ' noch' : ' alt' );
		
		$days = '<span class="btn btn-xs '.$btn.'">'.$date.' - '.$years.' '.$months.' '.$days.' '.$suffix.' </span>';
		return $days;
	}
}
