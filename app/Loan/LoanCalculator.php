<?php

namespace App\Loan;

use Carbon\Carbon;
use DateTime;
use stdClass;

class LoanCalculator implements ILoanCalculator
{

    public function GetLoanRepayment($amount, $tenure, $percent, $repaymentDay)
    {
        // a*(r/n)


        if($amount == null || empty($amount)) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'amount is required';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }

        if($tenure == null || empty($tenure)) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'tenure is required';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }
        if($percent == null || empty($percent)) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'percent is required';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }

        if($repaymentDay == null || empty($repaymentDay)) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'repayment day is required';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }

        if($amount < 1) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'invalid amount';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }

        if($percent < 1) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'invalid percent';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }

        if($tenure < 1) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'invalid tenure';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }

        if($repaymentDay < 1) {
            $std = new stdClass();

       
            $std->status = 'failed';
            $std->message = 'invalid amount';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');
        }
        $n = $amount;
        $r = $percent/100;;
       
        $final = ($n)*($r/$tenure);
        $date = new DateTime();
        $nw = Carbon::now();
        $day = $nw->day;
        $month = $nw->month;
        $year = $nw->year;
        $nwDateString = Carbon::parse($repaymentDay."-".$month."-".$year);
        $repaymenSchedule =[];
        $totalIntrestPerMonth = 0;
        $start = 0;
        $std = new stdClass();

        if($repaymentDay < $day) {
            $std->status = 'failed';
            $std->message = 'repayment day must be equal or greater than current day of the month';
            return response(json_encode($std), 400)->header('Content-Type', 'application/json');;
        }
        while($start != $tenure) {
            $nextPayment = $nwDateString->addMonth();
            $totalIntrestPerMonth = $totalIntrestPerMonth+$final;
            $eachMonthPaymenth =0;
            $paymentSchedule = new stdClass();
            if($start == $tenure) {
                $eachMonthPaymenth = ($totalIntrestPerMonth+$n)/$tenure;
                
            }
            
            $paymentSchedule->paymentDate = $nextPayment->toDateString();
            $paymentSchedule->paymentAmount = ($n/$tenure) + $final;
            array_push($repaymenSchedule, $paymentSchedule);
            $start = $start +1;
            
        }
        //$nw->getDays()
       
        $std->LoanAmount = $n;
        $std->TotalAmount = $totalIntrestPerMonth+$n;
        $std->paymentDetails = $repaymenSchedule;
        $std->loanDate = $nw->toDateTimeString();
        $std->repaymentDay = $repaymentDay;
        
        return response(json_encode($std), 200)->header('Content-Type', 'application/json');
    }

}