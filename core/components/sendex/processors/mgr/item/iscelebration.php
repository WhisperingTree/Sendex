<?php
/**
 * Created by PhpStorm.
 * User: Parker
 * Date: 30.09.2015
 * Time: 19:04
 */

require_once 'celebration.class.php';

	$isCelebration = FALSE;
	$posTime = 0;
	$date = $posTime;

	// разлаживаем САБЖ дату на переменные
	$subjectYear = date('Y', $date);
	$subjectMonth = date('m', $date);
	$subjectDay = date('d', $date);
	$subjectDW = date('w', $date); //день недели

	$weekNum = date('W', $posTime) - date('W', strtotime(date('Y-m-01', $posTime))) + 1;

	//	ПАРСИНГ ПРАЗДНИКОВ ИЗ БД В МАССИВ ОБЪЕКТОВ КЛАССА СELEBRATION - 'CELEBRATIONLIST'
	//	подключаемся к базе
	$db_name = 'testtask';
	$mysqli = new mysqli('localhost','root','',$db_name);

	//	проверка коннекта
	if ($mysqli->connect_errno) {
		printf('Connect failed: %s\n', mysqli_connect_error());
		exit();
	}

	//	кодировка
	$mysqli->query("SET NAMES 'utf8'");

	//	запись выборки
	$result_set = $mysqli->query('SELECT * FROM tt_celebrations_list');

	//	массив объектов Celebration
	$celebrationList = array();

	//	если выборка не пуста, итерируем строки
	if ($result_set){

		while (($row = $result_set->fetch_assoc()) != false){

			//	создание очередного объекта праздника из полей записи БД
			$parsedCelebration =  new Celebration(

				$row['celeb_dayinmonth_index'],
				$row['celeb_month_index'],
				$row['celeb_weekinmonth_index'],
				$row['celeb_dayinweek_index'],
				$row['celeb_name']);

			//	запись его в массив
			$celebrationList[] = $parsedCelebration;

		}
	}
	$mysqli->close();

	$listLength = count($celebrationList);

//	ЦИКЛ СРАВНЕНИЙ ГЕТЕРОВ ИЗ ЗАДАННОЙ ДАТЫ С ЭЛЕМЕНТАМИ МАССИВА 'celebrationList'
	for ($count=0; $count<$listLength; $count++){

		$processingCeleb = $celebrationList[$count];

		$processingDay = $processingCeleb->getCelebDay();
		$processingMonth = $processingCeleb->getCelebMonth();
		$processingWeek = $processingCeleb->getCelebWeek();
		$processingDW = $processingCeleb->getCelebDayOfWeek();

		//	ПРОВЕРЯЕМ НА РАВЕНСТВО -1 ПОЛЯ DAY_INDEX ТЕКУЩЕГО ЭЛЕМЕНТА.
		if($processingDay != -1){

			//	ЕСЛИ ОТЛИЧЕН ОТ -1, ТО ИМЕЕМ ДЕЛО СО СТАТИЧЕСКИМ ПРАЗДНИКОМ - СРАВНИВАЕМ ПОЛЯ (day_index && month_index) C ИЗВЛЕЧЁННЫМИ ИЗ ЗАДАННОЙ ДАТЫ (С СООТВ ГЕТЕРАМИ).
			//	Если оба равны - дата является статическим праздником - вернуть TRUE из функции (да, праздник).
			if(($subjectDay == $processingDay) && ($subjectMonth == $processingMonth)){

				//return TRUE;
				$isCelebration = TRUE;
				break;
			}
			//	Иначе - перейти к след итерации цикла (continue).
			else continue;
		}

		//	ЕСЛИ day_index РАВЕН -1 - ИМЕЕМ ДЕЛО СО ПЛАВАЮЩИМ ПРАЗДНИКОМ - УЧАСТВУЮТ ПОЛЯ: month_index, week_index, dayinweek_index.

		//	СРАВНИВАЕМ ПОЛЯ month_index && dayofweek_index && week_index (они сейчас записаны во временные переменные вида $processingMonth) C ИЗВЛЕЧЁННЫМИ ИЗ ЗАДАННОЙ ДАТЫ И ПОЛУЧЕННЫМ СЧЕТЧИКОМ НЕДЕЛЬ weekNum.
		//	Если все три равны - дата является плавающим праздником - вернуть TRUE из функции (да, праздник).

		if (($subjectDW == $processingDW) && ($subjectMonth == $processingMonth)){

			//	СОВПАЛИ МЕСЯЦ И ДЕНЬ НЕДЕЛИ

			if($processingWeek == 6){

				//	если праздник описан в контексте 'последний' такой_день_недели в месяце
				//	Определяем, последний ли это такой_день_недели в месяце САБЖа
				//	Прибавляем 7 дней, проверяем, остаемся ли мы в числах месяца САБЖа

				$isDate = checkdate($subjectMonth, $subjectDay+7, $subjectYear);

				if(!$isDate) {

					//return TRUE;
					$isCelebration = TRUE;
					break;
				}
				else continue;
			}
			else if ($weekNum == $processingWeek){

				//return TRUE;
				$isCelebration = TRUE;
				break;
			}
		}
		//	Не совпали одновременно день недели и месяц - перейти к след итерации цикла (continue).
		else continue;
	}
	/*Цикл отработал, прошло сравнение рассматриваемой даты со всем массивом celebrationList, а значит - со всеми праздниками, занесёнными в БД.
	Так как мы добрались до этой строки, не вернув ИСТИНУ в строках выше (и тем самым не выйдя из функции ретурном), то очевидно, что рассматриваемая дата - не явл праздником.

	Возвращаем FALSE (не является праздником)/**/
	//return FALSE;
	return $isCelebration;

