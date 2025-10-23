<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Member {

    public static function bootMember() {
        static::addGlobalScope('member_id', function (Builder $builder) {
            if (auth()->user()->user_type == 'user') {
                $assigned = [];
                if (method_exists(auth()->user(), 'assignedBranchIds')) {
                    $assigned = auth()->user()->assignedBranchIds();
                } else {
                    $assigned = [auth()->user()->branch_id];
                }
                return $builder->whereHas('member', function (Builder $query) use ($assigned) {
                    if(!empty($assigned)){
                        $query->whereIn('branch_id', $assigned);
                    }
                });
            }else {
                if (session('branch_id') != '') {
                    $branch_id = session('branch_id') == 'default' ? null : session('branch_id');
                    return $builder->whereHas('member', function (Builder $query) use($branch_id) {
                        $query->where('branch_id', $branch_id);
                    });
                }
            }
        });
    }

}