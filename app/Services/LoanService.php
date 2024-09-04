<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\User;
use App\Models\ScheduledRepayment; 
use Carbon\Carbon;


class LoanService
{
    /**
     * Create a Loan
     *
     * @param  User  $user
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  int  $terms
     * @param  string  $processedAt
     *
     * @return Loan
     */
    public function createLoan(User $user, int $amount, string $currencyCode, int $terms, string $processedAt): Loan
    {
        $loan = Loan::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'terms' => $terms,
            'outstanding_amount' => $amount,
            'currency_code' => $currencyCode,
            'processed_at' => $processedAt,
            'status' => Loan::STATUS_DUE,
        ]);

        $repaymentAmount = intdiv($amount, $terms);
        $remainder = $amount % $terms;

        for ($i = 1; $i <= $terms; $i++) {
            $amountToRepay = $repaymentAmount + ($i === $terms ? $remainder : 0);
            ScheduledRepayment::create([
                'loan_id' => $loan->id,
                'amount' => $amountToRepay,
                'outstanding_amount' => $amountToRepay,
                'currency_code' => $currencyCode,
                'due_date' => Carbon::parse($processedAt)->addMonths($i)->format('Y-m-d'),
                'status' => 'due',
            ]);
        }

        return $loan;
    }

    /**
     * Repay Scheduled Repayments for a Loan
     *
     * @param  Loan  $loan
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  string  $receivedAt
     *
     * @return ReceivedRepayment
     */
    public function repayLoan(Loan $loan, int $amount, string $currencyCode, string $receivedAt): ReceivedRepayment
    {
        
    }
}
