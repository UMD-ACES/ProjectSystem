<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{

    /** Peer Evaluation Instruction Types */
    public static $overview = 'overview';

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
    */
    public static function getPeerEvaluationInstructions()
    {
        return Instruction::query()->where('assignment', 'peer_evaluation')->get();
    }

    public static function getPeerEvaluationInstruction($type)
    {
        $value = self::getPeerEvaluationInstructions()
            ->where('instruction_type', $type)
            ->first();

        if($value == null)
        {
            return '';
        }

        return $value->instruction;
    }
}
