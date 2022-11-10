<?php

namespace App\Http\Controllers\Helper;

class VPCPaymentCodesHelpers
{
    public function getResultDescription($responseCode)
    {

        switch ($responseCode) {
            case "0" :
                $result = trans('admin.vpc_txn_success');
                break;
            case "?" :
                $result = trans('admin.vpc_txn_success_unknown');
                break;
            case "E" :
                $result = trans('admin.vpc_txn_referred');
                break;
            case "1" :
                $result = trans('admin.vpc_txn_declined');
                break;
            case "2" :
                $result = trans('admin.vpc_txn_bank_declined');
                break;
            case "3" :
                $result = trans('admin.vpc_txn_bank_norply');
                break;
            case "4" :
                $result = trans('admin.vpc_txn_card_expire');
                break;
            case "5" :
                $result = trans('admin.vpc_txn_fund_low');
                break;
            case "6" :
                $result = trans('admin.vpc_txn_bank_connection_error');
                break;
            case "7" :
                $result = trans('admin.vpc_txn_server_error');
                break;
            case "8" :
                $result = trans('admin.vpc_txn_unsupport');
                break;
            case "9" :
                $result = trans('admin.vpc_txn_bank_declined_contact');
                break;
            case "A" :
                $result = trans('admin.vpc_txn_aborted');
                break;
            case "B" :
                $result = trans('admin.vpc_txn_fraud');
                break;
            case "C" :
                $result = trans('admin.vpc_txn_cancelled');
                break;
            case "D" :
                $result = trans('admin.vpc_txn_deferred');
                break;
            case "E" :
                $result = trans('admin.vpc_txn_cancelled_issuer');
                break;
            case "F" :
                $result = trans('admin.vpc_txn_3d_failed');
                break;
            case "I" :
                $result = trans('admin.vpc_txn_csc_verf_failed');
                break;
            case "L" :
                $result = "Shopping Transaction Locked (Please try the transaction again later)";
                break;
            case "M" :
                $result = "Transaction Submitted (No response from acquirer)";
                break;
            case "N" :
                $result = "Cardholder is not enrolled in Authentication scheme";
                break;
            case "P" :
                $result = "Transaction has been received by the Payment Adaptor and is being processed";
                break;
            case "R" :
                $result = "Transaction was not processed - Reached limit of retry attempts allowed";
                break;
            case "S" :
                $result = "Duplicate SessionID (Amex Only)";
                break;
            case "T" :
                $result = "Address Verification Failed";
                break;
            case "U" :
                $result = trans('admin.vpc_txn_csc_failed');
                break;
            case "V" :
                $result = trans('admin.vpc_txn_csc_address_failed');
                break;
            default  :
                $result = trans('admin.vpc_txn_unable_error');
        }
        return $result;
    }

    public function getCSCResultDescription($cscResultCode)
    {

        if ($cscResultCode != "") {
            switch ($cscResultCode) {
                Case "Unsupported" :
                    $result = "CSC not supported or there was no CSC data provided";
                    break;
                Case "M"  :
                    $result = "Exact code match";
                    break;
                Case "S"  :
                    $result = "Merchant has indicated that CSC is not present on the card (MOTO situation)";
                    break;
                Case "P"  :
                    $result = "Code not processed";
                    break;
                Case "U"  :
                    $result = "Card issuer is not registered and/or certified";
                    break;
                Case "N"  :
                    $result = "Code invalid or not matched";
                    break;
                default   :
                    $result = "Unable to be determined";
                    break;
            }
        } else {
            $result = "null response";
        }
        return $result;
    }
}