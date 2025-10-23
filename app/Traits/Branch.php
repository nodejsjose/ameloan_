<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Branch {

    public static function bootBranch() {
        static::addGlobalScope('branch_id', function (Builder $builder) {
            if (auth()->user()->user_type == 'user') {
                if (method_exists(auth()->user(), 'assignedBranchIds')) {
                    $assigned = auth()->user()->assignedBranchIds();
                    if(!empty($assigned)){
                        return $builder->whereIn('branch_id', $assigned);
                    }
                    // If assigned is empty, don't restrict
                    return;
                }
                return $builder->where('branch_id', auth()->user()->branch_id);
            }else {
                if (session('branch_id') != '') {
                    $branch_id = session('branch_id') == 'default' ? null : session('branch_id');
                    return $builder->where('branch_id', $branch_id);
                }
            }
        });

    }

}