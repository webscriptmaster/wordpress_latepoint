<?php 

class OsMoneyHelper {

	/**
	 * @param $booking
	 * @param $apply_coupons
	 * @param $apply_taxes
	 * @return mixed|void
	 *
	 * Returns full amount to charge in database format 1999.0000
	 *
	 */
  public static function calculate_full_amount_to_charge($booking,  $apply_coupons = true, $apply_taxes = true){
		if(!$booking->service_id) return 0;
  	$amount = self::calculate_full_amount_for_service($booking, $apply_coupons, $apply_taxes);
    $amount = apply_filters('latepoint_full_amount', $amount, $booking, $apply_coupons, $apply_taxes);
		$amount = number_format((float)$amount, 4, '.', '');
  	return $amount;
  }

	public static function calculate_full_amount_for_service($booking,  $apply_coupons = true, $apply_taxes = true){
		if(!$booking->service_id) return 0;
		$service = new OsServiceModel($booking->service_id);
    $amount_for_service = $service->get_charge_amount_for_duration($booking->duration);
    $amount_for_service = apply_filters('latepoint_full_amount_for_service', $amount_for_service, $booking, $apply_coupons, $apply_taxes);
    return $amount_for_service;
	}


	/**
	 * @param $booking
	 * @param $apply_coupons
	 * @return mixed|void
	 *
	 * Returns deposit amount to charge in database format 1999.0000
	 *
	 */
  public static function calculate_deposit_amount_to_charge($booking, $apply_coupons = true){
  	$service = new OsServiceModel($booking->service_id);
    $amount_for_service = $service->get_deposit_amount_for_duration($booking->duration);
    $amount_for_service = apply_filters('latepoint_deposit_amount_for_service', $amount_for_service, $booking, $apply_coupons);
    $amount = $amount_for_service;
    $amount = apply_filters('latepoint_deposit_amount', $amount, $booking, $apply_coupons);
		$amount = number_format((float)$amount, 4, '.', '');
  	return $amount;
  }


	/**
	 * @param $amount
	 * @param $include_currency
	 * @param $hide_zero_decimals
	 * @return string
	 *
	 * Formats amount from database format (99999.0000) to requested format, optionally can include currency symbol and strip zero cents
	 *
	 */
  public static function format_price($amount, $include_currency = true, $hide_zero_decimals = true): string{
		$decimal_separator = OsSettingsHelper::get_settings_value('decimal_separator', '.');
		$thousand_separator = OsSettingsHelper::get_settings_value('thousand_separator', ',');
		$decimals = OsSettingsHelper::get_settings_value('number_of_decimals', '2');
  	$amount = number_format($amount, $decimals, $decimal_separator, $thousand_separator);
    if($hide_zero_decimals){
			$zeros = '';
			switch($decimals){
				case '1': $zeros = '0'; break;
				case '2': $zeros = '00'; break;
				case '3': $zeros = '000'; break;
				case '4': $zeros = '0000'; break;
			}
			$amount = str_replace($decimal_separator.$zeros, '', $amount);
    }
  	if($include_currency) $amount = implode('', array(OsSettingsHelper::get_settings_value('currency_symbol_before'), $amount, OsSettingsHelper::get_settings_value('currency_symbol_after')));
		$amount = apply_filters('latepoint_format_price', $amount, $include_currency, $hide_zero_decimals);
		return $amount;
  }

	// formats amount to be used in input money fields
	public static function to_money_field_format($amount){
		return self::format_price((float)$amount, false, false);
	}

	// amount stripped from any formatting like currency symbol, thousand separator, just numbers and decimal separator is left
  public static function convert_amount_from_money_input_to_db_format($amount){
		$decimal_separator = OsSettingsHelper::get_settings_value('decimal_separator', '.');
    $amount = preg_replace('/[^-\\d'.$decimal_separator.']+/', '', $amount);
		// database is using dot as a decimal separator, if latepoint is not using dot for currency input - convert it to dot to store in db
		if($decimal_separator != '.') $amount = str_replace($decimal_separator, '.', $amount);
		$amount = self::pad_to_db_format($amount);
    return $amount;
  }

	public static function convert_value_from_percent_input_to_db_format($value){
		$decimal_separator = OsSettingsHelper::get_settings_value('decimal_separator', '.');
    $value = preg_replace('/[^-\\d'.$decimal_separator.']+/', '', $value);
		// database is using dot as a decimal separator, if latepoint is not using dot for input - convert it to dot to store in db
		if($decimal_separator != '.') $value = str_replace($decimal_separator, '.', $value);
		$value = number_format($value, 4, '.', '');
		return $value;
	}

	public static function pad_to_db_format($amount){
		return number_format((float)$amount, 4, '.', '');
	}

}