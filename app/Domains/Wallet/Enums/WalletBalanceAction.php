<?php

namespace App\Domains\Wallet\Enums;

enum WalletBalanceAction: string
{
    case CREDIT = 'CREDIT';
    case DEBIT = 'DEBIT';
}
