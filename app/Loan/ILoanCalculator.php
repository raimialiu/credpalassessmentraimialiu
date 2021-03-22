<?php

namespace App\Loan;


interface ILoanCalculator {
    public function GetLoanRepayment($amount, $tenure, $percent, $repaymentDay);
}