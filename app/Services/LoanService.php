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
        $remainingAmount = $amount;

        $scheduledRepayments = $loan->scheduledRepayments()
            ->where('status', ScheduledRepayment::STATUS_DUE)
            ->orderBy('due_date', 'asc')
            ->get();

        foreach ($scheduledRepayments as $repayment) {
            if ($remainingAmount >= $repayment->outstanding_amount) {
                $remainingAmount -= $repayment->outstanding_amount;
                $repayment->update([
                    'outstanding_amount' => 0,
                    'status' => ScheduledRepayment::STATUS_REPAID,
                ]);
            } else {
                $repayment->update([
                    'outstanding_amount' => $repayment->outstanding_amount - $remainingAmount,
                ]);
                $remainingAmount = 0;
                break;
            }
        }

        $loan->update([
            'outstanding_amount' => $loan->outstanding_amount - $amount + $remainingAmount,
            'status' => $loan->outstanding_amount - $amount + $remainingAmount === 0
                ? Loan::STATUS_REPAID
                : Loan::STATUS_DUE,
        ]);

        ReceivedRepayment::create([
            'loan_id' => $loan->id,
            'amount' => $amount - $remainingAmount,
            'currency_code' => $currencyCode,
            'received_at' => $receivedAt,
        ]);

        return $loan;
    }
}
