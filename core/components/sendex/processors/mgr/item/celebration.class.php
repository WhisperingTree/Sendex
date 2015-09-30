<?php
/**
 * Created by PhpStorm.
 * User: Parker
 * Date: 30.09.2015
 * Time: 16:37
 */
// ОПИСАНИЕ КЛАССА "ПРАЗДНИК" СELEBRATION.
class Celebration {


	private $day_index = 0;
	private $month_index = 0;
	private $week_index = 0;
	private $dayofweek_index = 0;

	public $about = "";

	// Объявляем индекс последнего дня недели endOfWeekDayIndex ( от 0 (воскресенье) до 6 (суббота)).
	//Т.е. в привычном нам понимании - последний день воскресенье: endOfWeekDayIndex = 0.
	//По стандартам запада - суббота: endOfWeekDayIndex = 6.
	private static $endOfWeekDayIndex = 6;

	// гетеры
	public static function getEndOfWeekDayIndex() {return self::$endOfWeekDayIndex;}

	public function getCelebDay() {return $this->day_index;}
	public function getCelebMonth() {return $this->month_index;}
	public function getCelebWeek() {return $this->week_index;}
	public function getCelebDayOfWeek() {return $this->dayofweek_index;}

	// распечатка
	public function outputCeleb(){

		echo "<p>$this->day_index"." | ";
		echo "$this->month_index"." | ";
		echo "$this->week_index"." | ";
		echo "$this->dayofweek_index"." | ";
		echo "$this->about</p><br>";
	}

	//конструктор
	public function __construct($d,$m,$w,$dw,$cn){

		$this->day_index = $d;
		$this->month_index = $m;
		$this->week_index = $w;
		$this->dayofweek_index = $dw;

		$this->about = $cn;
	}

// КОНЕЦ ОПИСАНИЯ КЛАССА CELEBRATION
}