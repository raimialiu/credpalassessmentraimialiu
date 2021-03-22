<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan\ILoanCalculator;
use stdClass;

class LoanController extends Controller
{
    private $loanCalc;
    private $request;

    public function __construct(ILoanCalculator $loanCalc)
    {
        $this->loanCalc = $loanCalc;
    }

    private function getJsonKey($keyName) {
            return $this->request->json()->get($keyName);
    }   
    public function GetRepaymentSchedule(Request $request) 
    {
        $this->request = $request;

        $amount = $this->getJsonKey('amount');
        $tenure = $this->getJsonKey('tenure');
        $percent = $this->getJsonKey('percent');
        $day = $this->getJsonKey('repaymentDay');

       

        $answer = $this->loanCalc->GetLoanRepayment($amount, $tenure, $percent, $day);

        return $answer;

    }
}
