<?php

namespace App\Jobs;

use App\Models\ScorecardStackNodes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScorecardStackNodesQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateName;
    protected $templateData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($templateName, $templateData)
    {
        $this->templateName = $templateName;
        $this->templateData = $templateData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $nodeArray = array();
        // foreach ($this->templateData->nodes as $nodes) {
        //     $nodeType = $nodes->data->type;
        //     if (!empty($nodeType) && $nodeType == 'metricBox') {
        //         $nodeArray[] = $nodes->data->node_id;
        //         ScorecardStackNodes::updateOrCreate(
        //             [
        //                 'node_id' => $nodes->data->node_id,
        //             ],
        //             [
        //                 'scorecard_stack_id'     => $this->templateName,
        //                 'node_data'              => json_encode($nodes),
        //                 'auto_assign_color'      => $nodes->data->auto_assign_color,
        //                 'assigned_color'         => $nodes->data->assigned_color,
        //                 'assigned_to'            => $nodes->data->assigned_to,
        //                 'goal_achieve_in_number' => $nodes->data->goal_achieve_in_number,
        //                 'reminder'               => $nodes->data->reminder,
        //             ]
        //         );
        //     }
        // }
        // ScorecardStackNodes::where('scorecard_stack_id', $this->templateName)->whereNotIn('node_id', $nodeArray)->delete();
    }
}
