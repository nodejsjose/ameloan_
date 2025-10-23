<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MembersImport implements ToCollection, WithStartRow {

    private $data;

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows) {
        $i = 0;
        $j = 1;

        foreach ($rows as $row) {
            if ($row->filter()->isEmpty()) {
                continue;
            }

            // Check First name || Last name || Member no is empty
            if ($row[0] == '' || $row[1] == '' || $row[3] == '') {
                continue;
            }

            // Check Email is a valid email address
            if ($row[2] != '' && !filter_var($row[2], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // Check Email is unique
            if ($row[2] != '') {
                $member = Member::where('email', $row[2])->first();
                if ($member) {
                    continue;
                }
            }

            // Check Member no is unique
            $member = Member::where('member_no', $row[3])->first();
            if ($member) {
                continue;
            }

            /*Validator::make($rows->toArray(), [
            "$i.0" => 'required', //First Name
            "$i.1" => 'required', // Last Name
            "$i.2" => 'nullable|email|unique:members|max:191', // Email
            "$i.3" => 'required|unique:members|max:50', // Member No
            //"$i.4" => 'required', // Country Code
            //"$i.5" => 'required', // Mobile
            ], [
            "$i.0.required" => _lang('Row No') . " $j - " . _lang('First name must required'),
            "$i.1.required" => _lang('Row No') . " $j - " . _lang('Last name must required'),
            "$i.2.email"    => _lang('Row No') . " $j - " . _lang('Email must be a valid email address'),
            "$i.2.unique"   => _lang('Row No') . " $j - " . _lang('Email already exists'),
            "$i.3.required" => _lang('Row No') . " $j - " . _lang('Member no must required'),
            //"$i.4.required" => _lang('Row No') . " $j - " . _lang('Country code must required'),
            //"$i.5.required" => _lang('Row No') . " $j - " . _lang('Mobile number must required'),
            ])->validate();*/

            $i++;
            $j++;

            $member                = new Member();
            $member->first_name    = $row[0];
            $member->last_name     = $row[1];
            $member->email         = $row[2];
            $member->member_no     = $row[3];
            $member->country_code  = $row[4];
            $member->mobile        = $row[5];
            $member->business_name = $row[6];
            $member->gender        = strtolower($row[7]);
            $member->city          = $row[8];
            $member->state         = $row[9];
            $member->zip           = $row[10];
            $member->address       = $row[11];
            $member->credit_source = $row[12];
            $member->status        = 1;

            $member->save();

        }

    }

    /**
     * @return int
     */
    public function startRow(): int {
        return 2;
    }

}
