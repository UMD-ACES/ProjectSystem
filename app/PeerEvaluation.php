<?php

namespace App;

use Faker\Provider\UserAgent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PeerEvaluation extends Model
{
    protected $fillable = ['name'];

    public static function isOneActive()
    {
        return PeerEvaluation::where('active', 1)->first() != null;
    }

    public static function active()
    {
        return PeerEvaluation::where('active', 1)->first();
    }


    /* --------------- Defining Relationships --------------- */

    public function peerEvaluationsTeam()
    {
        return $this->hasMany('App\PeerEvaluationsTeam');
            /*'peer_evaluations_team',
            'peer_evaluation_id')
            ->withPivot('user_id')
            ->withTimestamps();*/
    }


    /* --------------- Filtering Relationships --------------- */

    /**
     * Returns all of the users in the group that have *submitted* the peer evaluation
     */
    public function getTeamMembers(Group $group)
    {
        // Gets all the team members that have submitted the peer evaluation
        $teamMembersIDGroupForm = $this->peerEvaluationsTeam()
            ->where('group_id', $group->id)
            ->get();

        // Convert to User models
        $teamMembers = new Collection();

        foreach ($teamMembersIDGroupForm as $teamMember)
        {
            $teamMembers[] = User::query()->find($teamMember->user_id);
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
        $teamMembersIDGroupForm = $this->peerEvaluationsTeam()
            ->where('group_id', $group->id)
            ->get();

        // Convert to User models
        $teamMembers = new Collection();

        foreach ($teamMembersIDGroupForm as $teamMember)
        {
            $teamMembers[] = User::query()->find($teamMember->user_id);
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


    /**
     * Returns individual score out of 100% based on the scores provided by the team members
     * (including the score provided to him/herself)
     *
     * If the world was perfect, then it is possible to simply add the scores given for an individual
     * but since some students do not fill out their peer evaluations, then I must compute the
     * individual score based on the number who filled out the peer evaluations
     *
     * 100% contribution >= 100 / # team Members who submitted the peer evaluations
     *
     * @param User $user
     * @return null
     */
    function computeTeamMemberScore(User $user)
    {
        /** @var Group $group */
        $group = $user->group;

        if($group == null)
        {
            return null;
        }

        $submittedGroupMembers = $this->getTeamMembers($group);

        // Addition of all the scores
        $userScore = 0;
        /** @var User $groupMember */
        foreach ($submittedGroupMembers as $groupMember)
        {
            if($groupMember->getSubmittedPeerEvaluationTeamMember($this, $user) != null)
            {
                $userScore += $groupMember->getSubmittedPeerEvaluationTeamMember($this, $user)->grade;
                //echo 'Score:'.$groupMember->name.':'.$groupMember->getSubmittedPeerEvaluationTeamMember($this, $user)->grade.'<br/>';
            }
        }

        // Average score
        $userScore = $userScore / $submittedGroupMembers->count();
        //echo 'User Score Total:'.$userScore.'<br/>';

        $allGroupMembers = $this->getAllTeamMembers($group);
        // 100% contribution score
        $fullContributionScore = 100 / $allGroupMembers->count();
        //echo 'Full Contribution Score:'.$fullContributionScore.'<br/>';

        // Actual score
        $score = round(($userScore / $fullContributionScore) * 100, 2);
        //echo 'Actual Score:'.$score.'<br/>';

        if($score > 100)
        {
            $score = 100;
        }

        return $score;
    }


}
