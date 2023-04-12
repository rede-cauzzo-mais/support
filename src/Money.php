<?php

class Money
{
	public static function format( $value, $currency = 'BRL' ): string
	{
		return number_format( $value, 2, ',', '.' ) . ' ' . $currency;
	}
}
