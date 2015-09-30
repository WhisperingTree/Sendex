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

	// ����������� ���� ���� �� ����������
	$subjectYear = date('Y', $date);
	$subjectMonth = date('m', $date);
	$subjectDay = date('d', $date);
	$subjectDW = date('w', $date); //���� ������

	$weekNum = date('W', $posTime) - date('W', strtotime(date('Y-m-01', $posTime))) + 1;

	//	������� ���������� �� �� � ������ �������� ������ �ELEBRATION - 'CELEBRATIONLIST'
	//	������������ � ����
	$db_name = 'testtask';
	$mysqli = new mysqli('localhost','root','',$db_name);

	//	�������� ��������
	if ($mysqli->connect_errno) {
		printf('Connect failed: %s\n', mysqli_connect_error());
		exit();
	}

	//	���������
	$mysqli->query("SET NAMES 'utf8'");

	//	������ �������
	$result_set = $mysqli->query('SELECT * FROM tt_celebrations_list');

	//	������ �������� Celebration
	$celebrationList = array();

	//	���� ������� �� �����, ��������� ������
	if ($result_set){

		while (($row = $result_set->fetch_assoc()) != false){

			//	�������� ���������� ������� ��������� �� ����� ������ ��
			$parsedCelebration =  new Celebration(

				$row['celeb_dayinmonth_index'],
				$row['celeb_month_index'],
				$row['celeb_weekinmonth_index'],
				$row['celeb_dayinweek_index'],
				$row['celeb_name']);

			//	������ ��� � ������
			$celebrationList[] = $parsedCelebration;

		}
	}
	$mysqli->close();

	$listLength = count($celebrationList);

//	���� ��������� ������� �� �������� ���� � ���������� ������� 'celebrationList'
	for ($count=0; $count<$listLength; $count++){

		$processingCeleb = $celebrationList[$count];

		$processingDay = $processingCeleb->getCelebDay();
		$processingMonth = $processingCeleb->getCelebMonth();
		$processingWeek = $processingCeleb->getCelebWeek();
		$processingDW = $processingCeleb->getCelebDayOfWeek();

		//	��������� �� ��������� -1 ���� DAY_INDEX �������� ��������.
		if($processingDay != -1){

			//	���� ������� �� -1, �� ����� ���� �� ����������� ���������� - ���������� ���� (day_index && month_index) C �����ר����� �� �������� ���� (� ����� ��������).
			//	���� ��� ����� - ���� �������� ����������� ���������� - ������� TRUE �� ������� (��, ��������).
			if(($subjectDay == $processingDay) && ($subjectMonth == $processingMonth)){

				//return TRUE;
				$isCelebration = TRUE;
				break;
			}
			//	����� - ������� � ���� �������� ����� (continue).
			else continue;
		}

		//	���� day_index ����� -1 - ����� ���� �� ��������� ���������� - ��������� ����: month_index, week_index, dayinweek_index.

		//	���������� ���� month_index && dayofweek_index && week_index (��� ������ �������� �� ��������� ���������� ���� $processingMonth) C �����ר����� �� �������� ���� � ���������� ��������� ������ weekNum.
		//	���� ��� ��� ����� - ���� �������� ��������� ���������� - ������� TRUE �� ������� (��, ��������).

		if (($subjectDW == $processingDW) && ($subjectMonth == $processingMonth)){

			//	������� ����� � ���� ������

			if($processingWeek == 6){

				//	���� �������� ������ � ��������� '���������' �����_����_������ � ������
				//	����������, ��������� �� ��� �����_����_������ � ������ �����
				//	���������� 7 ����, ���������, �������� �� �� � ������ ������ �����

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
		//	�� ������� ������������ ���� ������ � ����� - ������� � ���� �������� ����� (continue).
		else continue;
	}
	/*���� ���������, ������ ��������� ��������������� ���� �� ���� �������� celebrationList, � ������ - �� ����� �����������, ���������� � ��.
	��� ��� �� ��������� �� ���� ������, �� ������ ������ � ������� ���� (� ��� ����� �� ����� �� ������� ��������), �� ��������, ��� ��������������� ���� - �� ��� ����������.

	���������� FALSE (�� �������� ����������)/**/
	//return FALSE;
	return $isCelebration;

