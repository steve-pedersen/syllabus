<?php

class Syllabus_Admin_Semester extends Bss_ActiveRecord_BaseWithAuthorization
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_semesters',
            '__azidPrefix' => 'at:syllabus:admin/Semester/',
            '__pk' => ['id'],
            
            'id' => 'int',
            'display' => 'string',
            'internal' => 'string',
            'startDate' => ['datetime', 'nativeName' => 'start_date'],
            'endDate' => ['datetime', 'nativeName' => 'end_date'],
            'active' => 'bool',
        ];
    }
    
    public static function GetActiveSemesters ($app)
    {
        $schemaManager = $app->schemaManager;
        $semesters = $schemaManager->getSchema('Syllabus_Admin_Semester');
        $condition = $semesters->active->isTrue()->orIf($semesters->active->isNull());
        $activeSemesters = $semesters->findValues(['internal' => 'internal'], $condition, ['orderBy' => '-startDate']);
        return $activeSemesters;
    }

    public static function GetTerms ()
    {
        return [
            'Fall',
            'Spring',
            'Summer',
            'Winter',
        ];
    }
    
    public static function GetYears ($limit = 5)
    {
        $date = new DateTime();
        $year = $date->format('Y');
		$year -= 2;
        $years = [];
        
        for ($i = 0; $i < $limit; $i++)
        {
            $years[] = $year++;
        }
        
        return $years;
    }

    public static function ConvertToDescription ($code, $onlyTerm=false)
    {
        $term = $code[3];
        $year = $code[0] . '0' . $code[1] . $code[2];

        switch ($term) {
            case 1:
                $term = 'Winter';
                break;
            
            case 3:
                $term = 'Spring';
                break;

            case 5:
                $term = 'Summer';
                break;

            case 7:
                $term = 'Fall';
                break;
        }

        if ($onlyTerm)
        {
            return $term;
        }

        return $term . ' ' . $year;
    }

    public static function ConvertToTerm ($term, $onlyTerm=false)
    {
        $term = (int) $term;

        switch ($term) {
            case 1:
                $term = 'Winter';
                break;
            
            case 3:
                $term = 'Spring';
                break;

            case 5:
                $term = 'Summer';
                break;

            case 7:
                $term = 'Fall';
                break;
        }
        
        return $term;
    }

    public static function ConvertToCode ($display)
    {
        $space = strpos($display, ' ');
        $term = substr($display, 0, $space);
        $year = substr($display, $space + 1);

        switch ($term) {
            case 'Winter':
                $term = 1;
                break;
            
            case 'Spring':
                $term = 3;
                break;

            case 'Summer':
                $term = 5;
                break;

            case 'Fall':
                $term = 7;
                break;
        }

        return $year[0] . $year[2] . $year[3] . $term;
    }

    public static function guessActiveSemester ($returnTermCode = true, $fromDate = null, $endDate = null)
    {
        $earlyWinter = false;
        $m2 = 0;

        if ($fromDate)
        {
            $y = $fromDate->format('Y');
            $m = $fromDate->format('n');
            $d = $fromDate->format('d');
            if ($endDate) 
            {
                $m2 = $endDate->format('n');
                $d2 = $endDate->format('d');
            }
        }
        else
        {
            $y = date('Y');
            $m = date('n');
            $d = date('d');            
        }

        // Winter session ~ Dec 20 to Jan 18?
        // double check that the course end date also ends in Jan
        if (($m == 1 && $d < 20) || ($m == 12 && $d > 20))
        {
            if ($m2 && $m2 != 1) 
            {
                $s = 3; // then Spring
            }
            else
            {
                $s = 1; // Winter
                if ($m == 12)
                {
                    $earlyWinter = true;
                }
            }
        }
        elseif ($m < 6)
        {
            $s = 3; // Spring
        }
        elseif ($m < 8 || ($m == 8 && $d < 20))
        {
            $s = 5; // Summer
        }
        else
        {
            $s = 7; // Fall
        }

        if ($earlyWinter)
        {
            $y = (string) ($y + 1);
        }
        $y = $y[0] . substr($y, 2);

        return ($returnTermCode ? "$y$s" : [$y, $s]);
    }

    public function setDisplay ($display)
    {
        $this->_assign('display', $display);
    }

    public function setInternal ($internal)
    {
        $this->_assign('internal', $internal);
    }

    public function validate ()
    {
        $errors = [];
        
        if (!$this->startDate || !($this->startDate instanceof DateTime))
        {
            $errors['startDate'] = 'You must specify a start date';
        }
        
        if (!$this->endDate || !($this->endDate instanceof DateTime))
        {
            $errors['endDate'] = 'You must specify an end date';
        }
        
        if (!$this->display || !$this->internal)
        {
            $errors['display'] = 'You must specify the semester name and year';
        }
        
        return $errors;
    }
}
