<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PeerEvaluations extends Model
{
    protected $fillable = ['name'];

    public static function isOneActive()
    {
        return PeerEvaluations::where('active', 1)->first() != null;
    }

    public static function active()
    {
        return PeerEvaluations::where('active', 1)->first();
    }


    /* --------------- Defining Relationships --------------- */

    public function groups()
    {
        return $this->belongsToMany('App\Group', 'user_group', 'peer_evaluation_id')
            ->withPivot('user_id')
            ->withTimestamps();
    }


    /* --------------- Filtering Relationships --------------- */

    /**
     * Returns all of the users in the group that have *submitted* the peer evaluation
     */
    public function getTeamMembers(Group $group)
    {
        $teamMembersIDGroupForm = $this->groups()
            ->where('group_id', $group->id)
            ->get();

        // Convert to User models
        $teamMembers = new Collection();

        foreach ($teamMembersIDGroupForm as $teamMember)
        {
            $teamMembers[] = User::query()->find($teamMember->pivot->user_id);
        }

        return $teamMembers;
    }



    /**
     * Returns all of the users in the group
     *
     * @param Group $group
     * @return Collection
     */
    public function getAllTeamMembers(Group $group)
    {
        // Gets all the team members that have submitted the peer evaluation
        $teamMembersIDGroupForm = $this->groups()
            ->where('group_id', $group->id)
            ->get();

        // Convert to User models
        $teamMembers = new Collection();

        foreach ($teamMembersIDGroupForm as $teamMember)
        {
            $teamMembers[] = User::query()->find($teamMember->pivot->user_id);
        }

        // Gets all the team members from submitted peer_evaluation_team_members
        // in case a teammate did not submit the peer evaluation.
        $teamMembersPeerEvaluation = new Collection();

        /** @var User $teamMember */
        foreach ($teamMembers as $teamMember)
        {
            $peerEvaluationTeamMembers = $teamMember->getSubmittedPeerEvaluationTeamMembers($this)->get();

            foreach ($peerEvaluationTeamMembers as $member)
            {
                $teamMembersPeerEvaluation->push(User::query()->find($member->user_to_id));
            }
        }

        // Not required to merge since it should include oneself but still...
        $teamMembers = $teamMembers->merge($teamMembersPeerEvaluation);
        // Remove duplicates and reset the key ordering
        $teamMembers = $teamMembers->unique()->values();

        return $teamMembers;
    }


}
